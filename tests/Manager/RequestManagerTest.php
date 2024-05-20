<?php

namespace App\Tests\Manager;

use App\Manager\RequestManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RequestManagerTest extends TestCase
{
    public function testPathResovler()
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $serializer = $this->createMock(SerializerInterface::class);
        $cache = $this->createMock(CacheInterface::class);

        $requestManager = new RequestManager($httpClient, $serializer, $cache);

        $path = $requestManager->resolvePath(
            [
                RequestManager::METHOD => Request::METHOD_GET,
                RequestManager::PATH => '/api/search/{uid}'
            ],
            [
                '{uid}' => 'je-suis-un-uid'
            ]
        );

        $this->assertStringNotContainsString('{uid}', $path);

    }
}
