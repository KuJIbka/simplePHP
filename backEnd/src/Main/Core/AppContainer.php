<?php

namespace Main\Core;

use Doctrine\ORM\EntityManager;
use Main\Factory\ResponseFactory;
use Main\Service\Config;
use Main\Service\DB;
use Main\Service\Router;
use Main\Service\Session\SessionManager;
use Main\Service\TranslationService;
use Main\Service\Utils;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class AppContainer
{
    const TAG_APP_EVENT_LISTENER = 'app.event_listener';
    const TAG_APP_EVENT_SUBSCRIBER = 'app.event_subscriber';

    /**
     * @var ContainerBuilder
     */
    protected $rawContainer;

    public function __construct()
    {
        $this->rawContainer = new ContainerBuilder(new ParameterBag());
    }

    public function getRawContainer(): ContainerBuilder
    {
        return $this->rawContainer;
    }

    /**
     * @return Config
     * @throws \Exception
     */
    public function getConfig(): Config
    {
        /** @var Config $config */
        $config = $this->get(Config::class);
        return $config;
    }

    /**
     * @return Router
     * @throws \Exception
     */
    public function getRouter(): Router
    {
        /** @var Router $router */
        $router = $this->get(Router::class);
        return $router;
    }

    /**
     * @return SessionManager
     * @throws \Exception
     */
    public function getSessionManager(): SessionManager
    {
        /** @var SessionManager $sessManager */
        $sessManager = $this->get(SessionManager::class);
        return $sessManager;
    }

    /**
     * @return Utils
     * @throws \Exception
     */
    public function getUtils(): Utils
    {
        /** @var Utils $utils */
        $utils = $this->get(Utils::class);
        return $utils;
    }

    /**
     * @return TranslationService
     * @throws \Exception
     */
    public function getTranslationService(): TranslationService
    {
        /** @var TranslationService $transService */
        $transService = $this->get(TranslationService::class);
        return $transService;
    }

    /**
     * @return EntityManager
     * @throws \Exception
     */
    public function getEm(): EntityManager
    {
        /** @var EntityManager $em */
        $em = $this->get(EntityManager::class);
        return $em;
    }

    /**
     * @return ResponseFactory
     * @throws \Exception
     */
    public function getResponseFactory(): ResponseFactory
    {
        /** @var ResponseFactory $responseFactory */
        $responseFactory = $this->get(ResponseFactory::class);
        return $responseFactory;
    }

    /**
     * Gets a service.
     *
     * @param string $id              The service identifier
     * @param int    $invalidBehavior The behavior when the service does not exist
     *
     * @return object The associated service
     *
     * @throws InvalidArgumentException          when no definitions are available
     * @throws ServiceCircularReferenceException When a circular reference is detected
     * @throws ServiceNotFoundException          When the service is not defined
     * @throws \Exception
     *
     * @see Reference
     */
    public function get($id, $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE)
    {
        return $this->rawContainer->get($id, $invalidBehavior);
    }
}
