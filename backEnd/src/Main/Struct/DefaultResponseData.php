<?php

namespace Main\Struct;

use Main\Service\TranslationService;

class DefaultResponseData implements \JsonSerializable
{
    protected $type = '';
    protected $text = '';
    protected $moveTo = '';
    protected $data = [];

    /** @var TranslationService */
    protected $translationService;

    public function __construct(string $type = '', string $text = '', string $moveTo = '', array $data = [])
    {
        $this->setType($type)
            ->setText($text)
            ->setMoveTo($moveTo)
            ->setData($data);
    }

    public function setTranslationService(TranslationService $translationService): void
    {
        $this->translationService = $translationService;
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
        $text = $this->translationService
            ? $this->translationService->getTranslator()->trans($this->getText())
            : $this->getText();
        return [
            'type' => $this->getType(),
            'text' => $text,
            'moveTo' => $this->getMoveTo(),
            'data' => $this->getData(),
        ];
    }
}
