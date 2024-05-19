<?php

namespace App\ServicePath;

use App\Manager\RequestManager;
use Symfony\Component\HttpFoundation\Request;

class ImageRequest
{

    const LOGO_SIZE = 'w92';
    const POSTER_SIZE = 'w500';



    const GET_IMAGE = [
        RequestManager::METHOD => Request::METHOD_GET,
        RequestManager::PATH => '/t/p',

    ];
}
