<?php

namespace Main\Struct;

use Main\Exception\BaseException;
use Main\Exception\BaseFormDataException;
use Main\Service\TranslationsService;

class DefaultResponseData implements \JsonSerializable
{
    protected $type = '';
    protected $text = '';
    protected $moveTo = '';
    protected $data = [];

    public function __construct(string $type = '', string $text = '', string $moveTo = '', array $data = [])
    {
        $this->setType($type)
            ->setText($text)
            ->setMoveTo($moveTo)
            ->setData($data);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function getMoveTo(): string
    {
        return $this->moveTo;
    }

    public function setMoveTo(string $moveTo): self
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

    public function addData($key, $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => $this->getType(),
            'text' => TranslationsService::get()->getTranslator()->trans($this->getText()),
            'moveTo' => $this->getMoveTo(),
            'data' => $this->getData(),
        ];
    }

    public static function getFromException(BaseException $e): DefaultResponseData
    {
        $data = new self();
        $data->setType($e->getType())
            ->setText(TranslationsService::get()->getTranslator()->transLocalisationString($e->getMessage()))
            ->setMoveTo($e->getMoveTo())
            ->setData($e->getData());
        if ($e instanceof BaseFormDataException) {
            $formsDataErrors = $e->getFormDataErrors();
            if (!empty($formsDataErrors)) {
                $data->addData('formsDataErrors', $formsDataErrors);
            }
        }
        return $data;
    }
}
