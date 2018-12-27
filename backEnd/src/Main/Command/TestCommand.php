<?php

namespace Main\Command;

use Main\Core\traits\AppContainerTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    use AppContainerTrait;

    protected function configure()
    {
        parent::configure();
        $this->setName('app:test')
            ->addArgument('sleep', InputArgument::OPTIONAL, 'Sleep time', 0)
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
