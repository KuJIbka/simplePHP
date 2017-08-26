<?php

namespace Main\Service;

use Main\Utils\AbstractSingleton;
use Symfony\Component\Translation\Loader\PhpFileLoader;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Translator;

/**
* @method static TranslationsService get()
*/
class TranslationsService extends AbstractSingleton
{
    const LANG_RU = 'ru_RU';
    const LANG_EN = 'en_EN';

    protected static $inst;

    /**
     * @var Translator
     */
    protected $translator;

    protected function init()
    {
        $this->translator = new Translator('ru_RU', new MessageSelector());
        $this->translator->addLoader('php', new PhpFileLoader());
        $this->loadFromPath(PATH_LANGS.DIRECTORY_SEPARATOR);
    }

    public function loadFromPath(string $path)
    {
        if (is_dir($path)) {
            $d = dir($path);
            while (false !== ($entry = $d->read())) {
                if ($entry !== '.' && $entry !== '..') {
                    $this->loadFromFile($path.$entry);
                }
            }
        } elseif (is_file($path)) {
            $this->loadFromFile($path);
        } else {
            throw new \Exception('Wrong config path '.$path);
        }
        return $this;
    }

    public function loadFromFile(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new \Exception('File '.$filePath.' is not found');
        }
        $locale = explode('.', basename($filePath))[0];
        $this->translator->addResource('php', $filePath, $locale);
    }

    public function getAvailableLangs()
    {
        return [ self::LANG_RU, self::LANG_EN ];
    }

    public function isAvailableLang(string $lang)
    {
        return in_array($lang, $this->getAvailableLangs(), true);
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}
