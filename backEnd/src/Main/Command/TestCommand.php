<?php

namespace Main\Command;

use Main\Service\DB;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
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
        var_dump($input->getArgument('sleep'));
        DB::get()->getEm()->beginTransaction();
        $user = DB::get()->getUserRepository()
            ->find(1);
        $sql_q = 'SELECT * FROM users WHERE id = 1  LOCK IN SHARE MODE';
        $st = DB::get()->getEm()->getConnection()->prepare($sql_q);
        $st->execute();
        $udata = $st->fetch(\PDO::FETCH_ASSOC);
        $sleep = $input->getArgument('sleep');
        if ($udata['balance'] >= 1) {
            sleep((int) $sleep);
            var_dump('here1');
            DB::get()->getEm()->getConnection()->query(
                'UPDATE users SET balance = balance - 1 WHERE id = 1'
            );
            var_dump('here2');
        }
        DB::get()->getEm()->commit();
        $output->writeln([ '', 'End of test script ']);
    }
}
