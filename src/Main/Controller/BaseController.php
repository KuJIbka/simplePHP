<?php

namespace Main\Controller;

use Main\Service\Templater;
use Sabre\HTTP\Response;

class BaseController
{
    public function render($string, array $array = array()): string
    {
        return Templater::get()->getTemplater()->render($string, $array);
    }

    public function getJsonResponse($data = null)
    {
        return new Response(
            null,
            ['Content-type' => 'application/json'],
            json_encode($data, JSON_UNESCAPED_UNICODE)
        );
    }
}
