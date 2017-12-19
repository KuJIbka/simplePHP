<?php

namespace Main\Service;

use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
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

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    protected function init()
    {
        $isDebug = Config::get()->getParam('debug');
        $doctrineParams = [
            'driver' => Config::get()->getParam('db_driver'),
            'host' => Config::get()->getParam('db_host'),
            'port' => Config::get()->getParam('db_port'),
            'dbname' => Config::get()->getParam('db_dbname'),
            'charset' => Config::get()->getParam('db_charset'),
            'user' => Config::get()->getParam('db_user'),
            'password' => Config::get()->getParam('db_password'),
        ];
        $doctrinePaths= [
            PATH_ROOT
        ];
        $doctrineConfig = Setup::createAnnotationMetadataConfiguration(
            $doctrinePaths,
            $isDebug,
            PATH_ROOT.DS.'Core'.DS.'proxies',
            CacheDriver::get()->getCacheDriver()
        );
        $doctrineConfig->setNamingStrategy(new UnderscoreNamingStrategy());

        if ($isDebug) {
            $logger = new DebugStack();
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
     * @return UserLimitRepository|EntityRepository
     */
    public function getUserLimitRepository(): UserLimitRepository
    {
        return $this->getEm()->getRepository('Main\Entity\UserLimit');
    }
}
