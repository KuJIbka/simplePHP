<?php

namespace Main\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EchoCommand extends Command
{
    protected function configure()
    {
        parent::configure();
        $this->setName('app:echo')
            ->setDescription('Testing echo command')
            ->setHelp('Some help for test echo command');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Some',
            'echo',
            'data'
        ]);
    }
}
