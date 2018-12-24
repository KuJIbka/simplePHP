<?php

namespace Main\Factory\traits;

use Main\Factory\ResponseFactory;

trait ResponseFactoryTrait
{
    /** @var ResponseFactory */
    protected $responseFactory;

    public function setResponseFactory(ResponseFactory $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }
}
