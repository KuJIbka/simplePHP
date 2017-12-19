<?php

namespace Main\Core;

use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Helper\ConfigurationHelper;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Command\ClearCache\MetadataCommand;
use Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand;
use Doctrine\ORM\Tools\Console\Command\ClearCache\ResultCommand;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Main\Command\AddNewUserCommand;
use Main\Command\LangToJsonCommand;
use Main\Command\TestCommand;
use Main\Service\Config;
use Main\Service\DB;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;

class AppConsole extends App
{
    /** @var Application */
    protected $symfonyApp;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->symfonyApp = new Application();
        $em = DB::get()->getEm();
        $entityManagerHelper = new EntityManagerHelper($em);
        $dbConnection = $em->getConnection();
        $migrationConf = new Configuration($dbConnection);
        $migrationConfig = Config::get()->getParam('migrations');
        $migrationConf->setMigrationsDirectory($migrationConfig['dir']);
        $migrationConf->setMigrationsNamespace($migrationConfig['namespace']);
        $configurationHelper = new ConfigurationHelper($dbConnection, $migrationConf);
        $helperSet = new HelperSet([
            'em' => $entityManagerHelper,
            'db' => new ConnectionHelper($dbConnection),
            'dialog' => new QuestionHelper(),
            'configuration' => $configurationHelper,
            'entityManager' => $entityManagerHelper,
        ]);
        $this->symfonyApp->setHelperSet($helperSet);

        $this->symfonyApp->addCommands([
            new LangToJsonCommand(),
            new AddNewUserCommand(),
            new TestCommand(),

            new DiffCommand(),
            new ExecuteCommand(),
            new GenerateCommand(),
            new MigrateCommand(),
            new StatusCommand(),
            new VersionCommand(),

            new QueryCommand(),
            new MetadataCommand(),
            new ResultCommand(),
        ]);
    }

    public function run()
    {
        try {
            $this->symfonyApp->run();
        } catch (\Exception $e) {
            echo "Error found ".$e->getMessage();
            if (Config::get()->getParam('debug')) {
                echo "\n";
                echo "File: ".$e->getFile()."\n";
                echo "Line: ".$e->getLine();
            }
        }
    }
}
