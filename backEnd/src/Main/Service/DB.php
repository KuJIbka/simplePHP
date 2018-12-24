<?php

namespace Main\Service;

use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Setup;

class DB
{
    /** @var Config */
    protected $config;
    /** @var CacheDriver */
    protected $cacheDriver;
    
    /** @var EntityManager */
    protected $em;

    /**
     * @param Config $config
     * @param CacheDriver $cacheDriver
     * @throws ORMException
     */
    public function __construct(Config $config, CacheDriver $cacheDriver)
    {
        $this->config = $config;
        $this->cacheDriver = $cacheDriver;
        
        $isDebug = $this->config->getParam('debug');
        $doctrineParams = [
            'driver' => $this->config->getParam('db_driver'),
            'host' => $this->config->getParam('db_host'),
            'port' => $this->config->getParam('db_port'),
            'dbname' => $this->config->getParam('db_dbname'),
            'charset' => $this->config->getParam('db_charset'),
            'user' => $this->config->getParam('db_user'),
            'password' => $this->config->getParam('db_password'),
        ];
        $doctrinePaths= [
            PATH_ROOT.DS.'Entity'
        ];
        $doctrineConfig = Setup::createAnnotationMetadataConfiguration(
            $doctrinePaths,
            $isDebug,
            PATH_ROOT.DS.'Core'.DS.'proxies',
            $this->cacheDriver->getCacheDriver()
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
}
