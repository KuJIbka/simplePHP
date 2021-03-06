<?php

namespace Main\Factory;

use Main\Exception\BaseException;
use Main\Service\TranslationsService;
use Main\Struct\DefaultResponseData;
use Sabre\HTTP\Response;

abstract class ResponseFactory
{
    const RESP_TYPE_ERROR = 'error';
    const RESP_TYPE_SUCCESS = 'success';

    public static function getSimpleResponse($body = null, $statusCode = 200, array $headers = []): Response
    {
        if (!isset($headers['Content-type'])) {
            $headers['Content-type'] = 'text/html; charset=utf-8';
        }
        return new Response($statusCode, $headers, $body);
    }

    public static function getJsonResponse($body = null, $statusCode = 200, array $headers = []): Response
    {
        if (!isset($headers['Content-type'])) {
            $headers['Content-type'] = 'application/json; charset=utf-8';
        }
        $body = json_encode($body, JSON_UNESCAPED_UNICODE);
        return new Response($statusCode, $headers, $body);
    }

    public static function getCommonSuccessResponse(array $data = [], $text = null)
    {
        $textString = !is_null($text) ? $text : 'L_OPERATION_SUCCESS';
        $body = [
            'type' => self::RESP_TYPE_SUCCESS,
            'text' => TranslationsService::get()->getTranslator()->trans($textString),
            'data' => $data,
        ];
        return self::getJsonResponse($body);
    }

    public static function exceptionToResponse(BaseException $e, bool $isAjax = false)
    {
        $response = $isAjax ? self::getJsonResponse() : self::getSimpleResponse();
        $response->setStatus($e->getStatusCode());
        $response->setBody(json_encode(DefaultResponseData::getFromException($e), JSON_UNESCAPED_UNICODE));
        return $response;
    }
}
