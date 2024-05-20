<?php

namespace App\API\Action\Image;

use App\Manager\RequestManager;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class ImageAction
 * @package App\API\Action\Image
 * @author Nicolas SABAS <nicolassabas.freelance@gmail.com>
 */
class ImageAction
{

    /**
     * @var RequestManager
     */
    private RequestManager $requestManager;

    /**
     * @param RequestManager $requestManager
     */
    public function __construct(
        RequestManager $requestManager
    )
    {
        $this->requestManager = $requestManager;
    }


    #[Route('/api/image/{name}/{size}', name: 'image.render', methods: ['GET'])]
    public function __invoke(string $name, string $size)
    {
        try {
            return new Response($this->requestManager->getImage($name, $size)->getContent(), Response::HTTP_OK, [ 'Content-Type' => 'image/jpeg' ]);
        } catch (\Exception $exception) {
            return new JsonResponse([ 'message' => 'error image not found' ], Response::HTTP_NOT_FOUND);
        }
    }


}
