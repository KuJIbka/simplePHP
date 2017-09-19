<?php

namespace Main\Service;

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

    protected function init()
    {
        $isDebug = Config::get()->getParam('debug');
        $dbConf = Config::get()->getParam('db')[0];
        $doctrineParams = [
            'driver' => $dbConf['driver'],
            'host' => $dbConf['host'],
            'port' => $dbConf['port'],
            'dbname' => $dbConf['dbname'],
            'charset' => $dbConf['charset'],
            'user' => $dbConf['user'],
            'password' => $dbConf['password'],
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
        $doctrineConfig->setNamingStrategy(new UnderscoreNamingStrategy());

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
