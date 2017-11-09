<?php

namespace Main\Command;

use Main\Service\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddNewUserCommand extends Command
{
    protected function configure()
    {
        parent::configure();
        $this->setName('app:add-new-user')
            ->setDescription('Add new user')
            ->addArgument('login', InputArgument::REQUIRED, 'The login of user')
            ->addArgument('password', InputArgument::REQUIRED, 'The password of user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $login = $input->getArgument('login');
        $password = UserService::get()->encryptPassword($input->getArgument('password'));

        UserService::get()->addNewUser($login, $password);

        $output->writeln([
            '==================================',
            'User '.$login.' successfully added',
            '==================================',
        ]);
    }
}
