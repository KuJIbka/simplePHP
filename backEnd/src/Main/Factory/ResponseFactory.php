<?php

namespace Main\Factory;

use Main\Exception\BaseException;
use Main\Exception\BaseFormDataException;
use Main\Service\TranslationService;
use Main\Struct\DefaultResponseData;
use Main\Struct\LocalisationChoiceString;
use Symfony\Component\HttpFoundation\Response;

class ResponseFactory
{
    const RESP_TYPE_ERROR = 'error';
    const RESP_TYPE_SUCCESS = 'success';

    /** @var TranslationService */
    protected $translationService;
    
    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

    public function getDefaultResponseData(
        string $type = '',
        string $text = '',
        string $moveTo = '',
        array $data = []
    ): DefaultResponseData {
        $responseData = new DefaultResponseData($type, $text, $moveTo, $data);
        $responseData->setTranslationService($this->translationService);
        return $responseData;
    }

    public function getFromException(BaseException $e): DefaultResponseData
    {
        $translator = $this->translationService->getTranslator();
        $responseData = new DefaultResponseData();
        $responseData->setTranslationService($this->translationService);
        $responseData->setType($e->getType())
            ->setText($translator->transLocalisationString($e->getMessage()))
            ->setMoveTo($e->getMoveTo())
            ->setData($e->getData());
        if ($e instanceof BaseFormDataException) {
            $formsDataErrors = $e->getFormDataErrors();
            if (!empty($formsDataErrors)) {
                $translatedErrors = [];
                foreach ($formsDataErrors as $fieldName => $error) {
                    if ($error instanceof LocalisationChoiceString) {
                        $translatedErrors[$fieldName] = $translator->transChoice($error);
                    } else {
                        $translatedErrors[$fieldName] = $translator->trans($error);
                    }
                }
                $responseData->addData('formsDataErrors', $translatedErrors);
            }
        }
        return $responseData;
    }

    public function getSimpleResponse($body = null, $statusCode = 200, array $headers = []): Response
    {
        if (!isset($headers['Content-type'])) {
            $headers['Content-type'] = 'text/html; charset=utf-8';
        }
        return new Response($body, $statusCode, $headers);
    }

    public function getJsonResponse($body = null, $statusCode = 200, array $headers = []): Response
    {
        if (!isset($headers['Content-type'])) {
            $headers['Content-type'] = 'application/json; charset=utf-8';
        }
        $body = json_encode($body, JSON_UNESCAPED_UNICODE);
        return new Response($body, $statusCode, $headers);
    }

    public function getCommonSuccessResponse(array $data = [], $text = null)
    {
        $textString = !is_null($text) ? $text : 'L_OPERATION_SUCCESS';
        $body = [
            'type' => self::RESP_TYPE_SUCCESS,
            'text' => $this->translationService->getTranslator()->trans($textString),
            'data' => $data,
        ];
        return self::getJsonResponse($body);
    }

    public function exceptionToResponse(BaseException $e, bool $isAjax = false)
    {
        $response = $isAjax ? self::getJsonResponse() : self::getSimpleResponse();
        $response->setStatusCode($e->getStatusCode());
        $response->setContent(json_encode(self::getFromException($e), JSON_UNESCAPED_UNICODE));
        return $response;
    }
}
