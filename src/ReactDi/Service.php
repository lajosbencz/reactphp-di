<?php

namespace ReactDi;


use React\Promise\PromiseInterface;

class Service implements ServiceInterface
{
    protected $_definition;
    protected $_shared;
    protected $_resolved;

    public function __construct(callable $definition, bool $shared = false)
    {
        $this->setDefinition($definition);
        $this->setShared($shared);
    }

    public function setDefinition(callable $definition): ServiceInterface
    {
        $this->_definition = $definition;
        return $this;
    }

    public function getDefinition(): callable
    {
        return $this->_definition;
    }

    public function setShared(bool $shared = true): void
    {
        $this->_shared = $shared;
    }

    public function isShared(): bool
    {
        return $this->_shared;
    }

    public function isResolved(): bool
    {
        return isset($this->_resolved);
    }

    public function resolve(array $args = [], ?ContainerInterface $di = null): PromiseInterface
    {
        if(!$this->isResolved()) {
            $def = \Closure::bind($this->_definition, $di);
            $res = $def(...$args);
            if($res instanceof ServiceProviderInterface) {
                $res->register($di);
            }
            $this->_resolved = $res;
        }
        return $this->_resolved;
    }

}
