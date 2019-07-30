<?php

namespace Api\Http\Requests;

use Api\Config\Manager;
use Psr\Http\Message\ServerRequestInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

class Factory
{
    protected $config;

    protected $baseRequest;

    /**
     * Factory constructor.
     * @param Manager $config
     */
    public function __construct(Manager $config)
    {
        $this->config = $config;
    }

    /**
     * @param ServerRequestInterface $request
     * @return $this
     */
    public function setBaseRequest(ServerRequestInterface $request)
    {
        $this->baseRequest = $request;

        return $this;
    }

    /**
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public function base()
    {
        if (!$this->baseRequest) {
            $psr17Factory = new Psr17Factory();

            $this->baseRequest = (new ServerRequestCreator(
                $psr17Factory,
                $psr17Factory,
                $psr17Factory,
                $psr17Factory
            ))->fromGlobals();
        }

        return $this->baseRequest;
    }

    /**
     * @return \Psr\Http\Message\ServerRequestInterface
     * @throws \Oilstone\RsqlParser\Exceptions\InvalidQueryStringException
     */
    public function query()
    {
        $request = $this->base();
        $queryParams = $request->getQueryParams();

        return $request->withAttribute(
            'segments',
            Parser::segments($request->getUri()->getPath())
        )->withAttribute(
            'relations',
            Parser::relations($queryParams[$this->config->get('relationsKey')] ?? '')
        )->withAttribute(
            'filters',
            Parser::filters($queryParams[$this->config->get('filtersKey')] ?? '')
        )->withAttribute(
            'sort',
            Parser::sort($queryParams[$this->config->get('sortKey')] ?? '')
        );
    }
}
