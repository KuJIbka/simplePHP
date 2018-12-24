<?php

namespace Main\Command;

use Main\Service\TranslationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LangToJsonCommand extends Command
{
    /** @var TranslationService */
    protected $transactionService;

    public function setTranslationService(TranslationService $translationsService): void
    {
        $this->transactionService = $translationsService;
    }

    protected function configure()
    {
        parent::configure();
        $this->setName('app:lang-to-json')
            ->setDescription('Convert langs to public json');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Start converting...',
            ''
        ]);

        $translationService = $this->transactionService;
        foreach ($translationService->getAvailableLangs() as $lang) {
            $langJsonName = $lang.'.json';
            $langPublicPath = PATH_PUBLIC.DS.'lang';
            if (!is_dir($langPublicPath)) {
                mkdir($langPublicPath);
            }
            $langPublicPath .= DS;
            $fd = fopen($langPublicPath.$langJsonName, 'w') or die('не удалось создать файл '.$langJsonName);
            $defaultDomain = 'messages';
            $forBazingaJs = [
                'locale' => $lang,
                'defaultDomain' => $defaultDomain,
                'translations' => [
                    $lang => []
                ],
            ];
            $forBazingaJs['translations'][$lang] = $translationService->getTranslator()
                ->getCatalogue($lang)
                ->all();
            fwrite($fd, json_encode($forBazingaJs, JSON_UNESCAPED_UNICODE));
            fclose($fd);
            $output->writeln([ $langJsonName.' - OK' ]);
        }

        $output->writeln([ '', 'End of converting ']);
    }
}
