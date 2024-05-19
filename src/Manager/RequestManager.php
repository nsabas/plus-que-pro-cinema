<?php

namespace App\Manager;

use App\Cache\CacheTrait;
use App\ServicePath\ImageRequest;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RequestManager
{

    use CacheTrait;

    const METHOD = 'method';
    const PATH = 'path';
    const MODEL = 'model';
    const PATH_PARAMS = 'path_params';

    /**
     * @var HttpClientInterface
     */
    private HttpClientInterface $client;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @var CacheInterface
     */
    protected CacheInterface $cache;

    /**
     * @param HttpClientInterface $client
     */
    public function __construct(
        HttpClientInterface $client,
        SerializerInterface $serializer,
        CacheInterface $cache
    )
    {
        $this->client = $client;
        $this->serializer = $serializer;
        $this->cache = $cache;
    }

    public function getResource(
        array $requestPath,
        array $parameters = [],
        array $urlParam = [],
        bool $toCache = false
    )
    {
        if ($toCache){
            return $this->cache->get(
                $this->generateCacheKey($requestPath[self::PATH], $parameters),
                function (ItemInterface $item) use ($requestPath, $parameters, $urlParam) {
                    $item->expiresAfter(new \DateInterval('P2D'));
                    return $this->request($requestPath, $parameters, $urlParam);
                }
            );
        }
        return $this->request($requestPath, $parameters, $urlParam);
    }

    private function request(array $requestPath, array $parameters = [], array $pathParameters = [])
    {
        try {
            return $this->extractDataFromResponse(
                $this->client->request(
                    $requestPath[self::METHOD],
                    $this->resolvePath($requestPath, $pathParameters),
                    $parameters
                )->getContent(),
                $requestPath[self::MODEL]
            );
        } catch (\Exception $exception){
            // log execption
            throw $exception;
        }
    }

    private function extractDataFromResponse(string $dataResponse, string $model)
    {
        $decodeData = (new JsonDecode())->decode($dataResponse, true, ['json_decode_associative' => true]);

        return $this->serializer->denormalize($decodeData, $model);
    }

    public function getImage(string $path, string $size = 'w300')
    {
        return $this->client->request(
            ImageRequest::GET_IMAGE[self::METHOD],
            ImageRequest::GET_IMAGE[self::PATH] . "/$size/$path"
        );
    }

    /**
     * @param string $servicePath
     * @param array $urlParams
     * @return string
     */
    public function resolvePath(array $requestPath, array $urlParams = []): string
    {
        if (empty($urlParams)) {
            return $requestPath[self::PATH];
        } else {
            return str_replace(
                array_keys($urlParams),
                array_values($urlParams),
                $requestPath[self::PATH]
            );
        }
    }
}
