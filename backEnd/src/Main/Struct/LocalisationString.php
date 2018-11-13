<?php

namespace Main\Struct;

class LocalisationString
{
    protected $key = '';
    protected $data = [];
    protected $locale;
    protected $domain;

    public function __construct(string $key = '', array $data = [], string $locale = null, string $domain = null)
    {
        $this->setKey($key)
            ->setData($data)
            ->setLocale($locale)
            ->setDomain($domain);
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(?string $key): self
    {
        $this->key = $key;
        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function addData(string $key, $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function addDataArr(array $data): self
    {
        $this->$data = array_merge($this->data, $data);
        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    public function __toString()
    {
        return $this->key;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(?string $domain): self
    {
        $this->domain = $domain;
        return $this;
    }
}
