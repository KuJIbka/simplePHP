<?php

namespace Main\Service;

class Config
{
    private static $inst = null;
    private $data = [];

    private function __construct()
    {
    }

    public static function get(): self
    {
        if (is_null(self::$inst)) {
            self::$inst = new self();
        }
        return self::$inst;
    }

    /**
     * @param string $path
     * @return $this
     * @throws \Exception
     */
    public function loadFromPath(string $path)
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
     * @throws \Exception
     */
    public function loadFromFile($filePath)
    {
        if (!file_exists($filePath)) {
            throw new \Exception('File '.$filePath.' is not found');
        }
        $this->merge(require_once $filePath);
    }

    public function load(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function merge(array $data): self
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    public function getParam($param)
    {
        return isset($this->data[$param]) ? $this->data[$param] : null;
    }
}
