<?php

namespace Main\Struct;

use Main\Service\Session\SessionManager;
use Main\Service\traits\SessionManagerTrait;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AppRequest
 * @method static AppRequest createFromGlobals()
 */
class AppRequest extends Request
{
    use SessionManagerTrait;

    /**  @var \Symfony\Component\HttpFoundation\ParameterBag */
    public $routerParameters;

    /**
     * @deprecated - use setSessionManager instead
     * @param callable $factory
     */
    public function setSessionFactory(callable $factory)
    {
        parent::setSessionFactory($factory);
    }

    public function getSession(): SessionManager
    {
        return$this->sessionManager;
    }

    public function initialize(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null)
    {
        parent::initialize($query, $request, $attributes, $cookies, $files, $server, $content);
        $this->routerParameters = new ParameterBag([]);
    }

    public function __clone()
    {
        parent::__clone();
        $this->routerParameters = clone $this->routerParameters;
    }

    public function duplicate(array $query = null, array $request = null, array $attributes = null, array $cookies = null, array $files = null, array $server = null)
    {
        /** @var AppRequest $appRequest */
        $appRequest = parent::duplicate($query, $request, $attributes, $cookies, $files, $server);
        $appRequest->routerParameters = new ParameterBag([]);
        return $appRequest;
    }

    public function getRouterParameters(): ParameterBag
    {
        return $this->routerParameters;
    }

    public function setRouterParameters(ParameterBag $routerParameters): self
    {
        $this->routerParameters = $routerParameters;
        return $this;
    }
}
