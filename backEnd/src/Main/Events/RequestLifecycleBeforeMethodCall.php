<?php

namespace Main\Events;

use Main\Struct\AppRequest;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;

class RequestLifecycleBeforeMethodCall extends Event
{
    /** @var AppRequest */
    protected $appRequest;
    /** @var Response */
    protected $response;

    public function __construct(AppRequest $appRequest)
    {
        $this->appRequest = $appRequest;
    }

    public function getAppRequest(): AppRequest
    {
        return $this->appRequest;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setResponse(Response $response): self
    {
        $this->response = $response;
        return $this;
    }
}
