<?php

namespace Main\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Setup;
use Main\Repository\UserRepository;
use Main\Utils\AbstractSingleton;
use Main\Repository\UserLimitRepository;

/**
 * @method static DB get()
 */
class DB extends AbstractSingleton
{
    protected static $inst;

    private $em = null;

    protected function init()
    {
        $isDebug = Config::get()->getParam('debug');
        $dbConf = Config::get()->getParam('db')[0];
        $doctrineParams = [
            'driver' => 'pdo_mysql',
            'host' => $dbConf['host'],
            'port' => $dbConf['port'],
            'dbname' => $dbConf['dbName'],
            'charset' => 'UTF8',
            'user' => $dbConf['user'],
            'pass' => $dbConf['pass'],
        ];
        $doctrinePaths= [
            PATH_ROOT
        ];
        $doctrineConfig = Setup::createAnnotationMetadataConfiguration(
            $doctrinePaths,
            $isDebug,
            PATH_ROOT.DIRECTORY_SEPARATOR.'Core'.DIRECTORY_SEPARATOR.'proxies',
            CacheDriver::get()->getCacheDriver()
        );

        if ($isDebug) {
            $logger = new \Doctrine\DBAL\Logging\DebugStack();
            $doctrineConfig->setSQLLogger($logger);
        }
        $this->em = EntityManager::create($doctrineParams, $doctrineConfig);
    }

    public function getEm(): EntityManager
    {
        return $this->em;
    }

    /**
     * @return UserRepository|EntityRepository
     */
    public function getUserRepository(): UserRepository
    {
        return $this->getEm()->getRepository('Main\Entity\User');
    }

    /**
     * @return UserRepository|EntityRepository
     */
    public function getUserLimitRepository(): UserLimitRepository
    {
        return $this->getEm()->getRepository('Main\Entity\UserLimit');
    }
}
