<?php

abstract class BaseService
{
    protected ?BaseService $successor = null;

    public function setSuccessor(BaseService $nextService): void
    {
        $this->successor = $nextService;
    }

    public abstract function handleRequest(Request $request): void;
}

class JsonService extends BaseService
{
    public function handleRequest(Request $request): void
    {
        if ($request->getService() === 'JSON') {
            echo 'Soy un parser de JSON';
        } elseif ($this->successor !== null) {
            $this->successor->handleRequest($request);
        }
    }
}

class XMLService extends BaseService
{
    public function handleRequest(Request $request): void
    {
        if ($request->getService() === 'XML') {
            echo 'Soy un parser de XML';
        } elseif ($this->successor !== null) {
            $this->successor->handleRequest($request);
        }
    }
}

class Request
{
    public function __construct(
        private string $service
    ) {}

    public function getService(): string
    {
        return $this->service;
    }
}

$json = new JsonService();
$xml = new XMLService();
$json->setSuccessor($xml);

$json->handleRequest(new Request('JSON'));
