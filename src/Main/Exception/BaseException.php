<?php

namespace Main\Exception;

use Main\Factory\ResponseFactory;

class BaseException extends \Exception
{
    protected $statusCode = 200;
    protected $type = ResponseFactory::RESP_TYPE_ERROR;
    protected $moveTo = '';
    protected $data = [];
    protected $langData = [];

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;
        return $this;
    }

    public function getMoveTo(): string
    {
        return $this->moveTo;
    }

    public function setMoveTo(string $moveTo)
    {
        $this->moveTo = $moveTo;
        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    public function getLangData(): array
    {
        return $this->langData;
    }

    public function setLangData(array $langData)
    {
        $this->langData = $langData;
        return $this;
    }
}
