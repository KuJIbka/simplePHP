<?php

namespace Main\Factory;

use Main\Exception\BaseException;
use Main\Struct\DefaultResponseData;
use Sabre\HTTP\Response;

abstract class ResponseFactory
{
    const RESP_TYPE_ERROR = 'error';
    const RESP_TYPE_SUCCESS = 'success';

    public static function getSimpleResponse($body = null, $statusCode = null, array $headers = []): Response
    {
        return new Response($statusCode, $headers, $body);
    }

    public static function getJsonResponse($body = null, $statusCode = null, array $headers = []): Response
    {
        $headers['Content-type'] = 'application/json';
        $body = json_encode($body, JSON_UNESCAPED_UNICODE);
        return new Response($statusCode, $headers, $body);
    }

    public static function exceptionToResponse(BaseException $e, bool $isAjax = false)
    {
        $response = $isAjax ? self::getJsonResponse() : self::getSimpleResponse();
        $response->setStatus($e->getStatusCode());
        $response->setBody(json_encode(DefaultResponseData::getFromException($e), JSON_UNESCAPED_UNICODE));
        return $response;
    }
}
