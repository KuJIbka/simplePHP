<?php

namespace Main\Service;

use Main\Utils\AbstractSingleton;
use Symfony\Component\Translation\Formatter\MessageFormatter;
use Symfony\Component\Translation\Loader\PhpFileLoader;

/**
* @method static TranslationsService get()
*/
class TranslationsService extends AbstractSingleton
{
    const LANG_RU = 'ru';
    const LANG_EN = 'en';

    protected static $inst;

    /**
     * @var ExpandTranslator
     */
    protected $translator;

    /**
     * @throws \Exception
     */
    protected function init()
    {
        $defaultLang = Config::get()->getParam('language_default_lang');
        $this->translator = new ExpandTranslator($defaultLang, new MessageFormatter());
        $this->translator->addLoader('php', new PhpFileLoader());
        $this->loadFromPath(PATH_LANGS.DS);
    }

    public function seLocale(string $locale)
    {
        if (in_array($locale, Config::get()->getParam('language_available_langs'))) {
            $this->translator->setLocale($locale);
        }
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

    /**
     * @param string $filePath
     * @throws \Exception
     */
    public function loadFromFile(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new \Exception('File '.$filePath.' is not found');
        }
        $locale = explode('.', basename($filePath))[0];
        $this->translator->addResource('php', $filePath, $locale);
    }

    public function getAvailableLangs(): array
    {
        return [ self::LANG_RU, self::LANG_EN ];
    }

    public function isAvailableLang(string $lang)
    {
        return in_array($lang, $this->getAvailableLangs(), true);
    }

    public function getTranslator(): ExpandTranslator
    {
        return $this->translator;
    }
}
