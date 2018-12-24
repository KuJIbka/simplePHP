<?php

namespace Main\Command;

use Main\Service\CacheDriver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCacheTagsCommand extends Command
{
    /** @var CacheDriver */
    protected $cacheDriver;

    public function setCacheDriver(CacheDriver $cacheDriver):void
    {
        $this->cacheDriver = $cacheDriver;
    }

    protected function configure()
    {
        parent::configure();
        $this->setName('app:init-cache-tags')
            ->setDescription('Initialize cache tags on start');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $initTags = [
            CacheDriver::TAG_PERMISSIONS,
            CacheDriver::TAG_ROLES,
        ];
        $tagsString = implode(', ', $initTags);
        $output->writeln([
            'Initializing tags '.$tagsString,
            ''
        ]);

        $this->cacheDriver->setTagsTimestamp($initTags);

        $output->writeln([
            'SUCCESS',
        ]);
    }
}
