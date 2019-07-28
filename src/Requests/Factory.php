<?php

namespace Api\Requests;

use Api\Config\Config;
use Psr\Http\Message\ServerRequestInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

class Factory
{
    protected $config;

    protected $request;

    /**
     * Factory constructor.
     * @param Config $config
     * @param ServerRequestInterface $request
     */
    public function __construct(Config $config, ServerRequestInterface $request)
    {
        $this->config = $config;
        $this->request = $request;
    }

    /**
     * @param Config $config
     * @param null|ServerRequestInterface $request
     * @return static
     */
    public static function instance(Config $config, ?ServerRequestInterface $request = null)
    {
        return new static($config, $request ?: static::request());
    }

    /**
     * @return Config
     */
    public static function config(): Config
    {
        return (new Config())->accepts(
            'relationsKey',
            'filtersKey',
            'sortKey'
        )
            ->relationsKey('include')
            ->filtersKey('filter')
            ->sortKey('sort');
    }

    /**
     * @return \Psr\Http\Message\ServerRequestInterface
     * @throws \Oilstone\RsqlParser\Exceptions\InvalidQueryStringException
     */
    public function resource()
    {
        $queryParams = $this->request->getQueryParams();

        return $this->request->withAttribute(
            'segments',
            Parser::segments($this->request->getUri()->getPath())
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

    /**
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public static function request()
    {
        $psr17Factory = new Psr17Factory();

        return (new ServerRequestCreator(
            $psr17Factory,
            $psr17Factory,
            $psr17Factory,
            $psr17Factory
        ))->fromGlobals();
    }
}
