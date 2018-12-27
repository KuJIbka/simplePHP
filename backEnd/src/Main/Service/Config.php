<?php

namespace Main\Service;

class Config
{
    private $data = [];

    /**
     * @param string $path
     * @return $this
     * @throws \Exception
     */
    public function loadFromPath(string $path): self
    {
        if (is_dir($path)) {
            $d = dir($path);
            while (false !== ($entry = $d->read())) {
                if ($entry !== '.' && $entry !== '..' && $entry[0] !== '.') {
                    $this->loadFromFile($path.DS.$entry);
                }
            }
        } elseif (is_file($path)) {
            $this->loadFromFile($path);
        } else {
            throw new \Exception('Wrong config path '.$path);
        }
        return $this;
    }

    /**
     * @param $filePath
     * @return $this
     * @throws \Exception
     */
    public function loadFromFile($filePath): self
    {
        if (!file_exists($filePath)) {
            throw new \Exception('File '.$filePath.' is not found');
        }
        $this->merge(require $filePath);
        return $this;
    }

    public function load(array $data): self
    {
        $this->data = $this->parseConfigData($data);
        return $this;
    }

    public function merge(array $data): self
    {
        $this->data = array_merge($this->data, $this->parseConfigData($data));
        return $this;
    }

    public function setParam(string $param, $value): self
    {
        $this->data[$param] = $value;
        return $this;
    }

    public function getParam($param)
    {
        return isset($this->data[$param]['__val']) ? $this->data[$param]['__val'] : null;
    }

    public function getPublicSettings(): array
    {
        $settings = [];
        foreach ($this->data as $key => $param) {
            if ($param['__public']) {
                $settings[$key] = $param['__val'];
            }
        }
        return $settings;
    }

    private function parseConfigData(array $data): array
    {
        $parsedData = [];
        foreach ($data as $key => $param) {
            if (is_array($param) && isset($param['__val'])) {
                $parsedData[$key] = array_merge($this->getDefaultParam(), $param);
            } else {
                $parsedData[$key] = array_merge($this->getDefaultParam(), ['__val' => $param]);
            }
        }
        return $parsedData;
    }

    private function getDefaultParam()
    {
        return [
            '__val' => null,
            '__public' => false
        ];
    }
}
