<?php

namespace Main\Core;

use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Tools\Console\Helper\ConfigurationHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Main\Command\LangToJsonCommand;
use Main\Service\Config;
use Main\Service\DB;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;

class AppConsole extends App
{
    /** @var Application */
    protected $symfonyApp;

    public function __construct()
    {
        parent::__construct();
        $this->symfonyApp = new Application();
        $em = DB::get()->getEm();
        $dbConnection = $em->getConnection();
        $migrationConf = new Configuration($dbConnection);
        $migrationConfig = Config::get()->getParam('migrations');
        $migrationConf->setMigrationsDirectory($migrationConfig['dir']);
        $migrationConf->setMigrationsNamespace($migrationConfig['namespace']);
        $configurationHelper = new ConfigurationHelper($dbConnection, $migrationConf);
        $helperSet = new HelperSet([
            'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($dbConnection),
            'dialog' => new \Symfony\Component\Console\Helper\QuestionHelper(),
            'configuration' => $configurationHelper,
            'entityManager' => new EntityManagerHelper($em),
        ]);
        $this->symfonyApp->setHelperSet($helperSet);

        $this->symfonyApp->addCommands([
            new LangToJsonCommand(),
            new \Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand(),
            new \Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand(),
            new \Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand(),
            new \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand(),
            new \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand(),
            new \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand(),
        ]);
    }

    public function run()
    {
        $this->symfonyApp->run();
    }
}
