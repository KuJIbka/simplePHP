<?php

namespace Main\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    protected function configure()
    {
        parent::configure();
        $this->setName('app:test')
            ->setDescription('Any test');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Start Test Script...',
            ''
        ]);

        $output->writeln([ '', 'End of test script ']);
    }
}
