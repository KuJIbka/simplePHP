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

    protected $cyr = [
        'а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п',
        'р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',
        'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П',
        'Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я',
    ];

    protected $lat = [
        'a','b','v','g','d','e','io','zh','z','i','y','k','l','m','n','o','p',
        'r','s','t','u','f','h','ts','ch','sh','sht','a','i','y','e','yu','ya',
        'A','B','V','G','D','E','Io','Zh','Z','I','Y','K','L','M','N','O','P',
        'R','S','T','U','F','H','Ts','Ch','Sh','Sht','A','I','Y','e','Yu','Ya',
    ];

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

    public function toTranslit(string $toTranslit): string
    {
        return str_replace($this->cyr, $this->lat, $toTranslit);
    }
}
