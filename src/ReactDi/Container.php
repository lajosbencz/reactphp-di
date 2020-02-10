<?php

namespace ReactDi;


use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Promise\PromiseInterface;

class Container implements ContainerInterface
{
    protected static $_default = null;

    /** @var LoopInterface */
    protected $_loop;
    /** @var ServiceInterface[] */
    protected $_services = [];

    public function __construct(?LoopInterface $loop = null)
    {
        if (!isset(static::$_default)) {
            static::$_default = $this;
        }
        $this->setLoop($loop ?? Factory::create());
    }

    public static function getDefault(): ContainerInterface
    {
        if(!isset(static::$_default)) {
            $c = new static();
            static::setDefault($c);
        }
        return static::$_default;
    }

    public static function reset(): void
    {
        static::$_default = null;
    }

    public static function setDefault(ContainerInterface $container): void
    {
        static::$_default = $container;
    }

    public function getLoop(): LoopInterface
    {
        return $this->_loop;
    }

    public function setLoop(LoopInterface $loop): void
    {
        $this->_loop = $loop;
    }

    public function attempt(string $name, $definition, bool $shared = false): ?ServiceInterface
    {
        if ($this->has($name)) {
            return null;
        }
        $this->set($name, $definition, $shared);
        return $this->_services[$name];
    }

    public function get(string $name, array $args = []): PromiseInterface
    {
        $def = \Closure::bind($this->_services[$name]->getDefinition(), $this);
        return $def($args);
    }

    public function getRaw(string $name): callable
    {
        return $this->_services[$name]->getDefinition();
    }

    public function getService(string $name): ServiceInterface
    {
        return $this->_services[$name];
    }

    public function getServices(): array
    {
        return $this->_services;
    }

    public function getShared(string $name, array $args = []): PromiseInterface
    {
        return $this->_services[$name]->resolve($args, $this);
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->_services);
    }

    public function remove(string $name): void
    {
        if ($this->has($name)) {
            unset($this->_services[$name]);
        }
    }

    public function set(string $name, callable $definition, bool $shared = false): ServiceInterface
    {
        $svc = new Service($definition, $shared);
        $this->_services[$name] = $svc;
        return $svc;
    }

    public function setService(string $name, ServiceInterface $rawDefinition): void
    {
        $this->_services[$name] = $rawDefinition;
    }

    public function setShared(string $name, callable $definition): ServiceInterface
    {
        return $this->set($name, $definition, true);
    }

}
