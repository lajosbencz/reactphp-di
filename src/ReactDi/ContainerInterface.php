<?php

namespace ReactDi;

use React\EventLoop\LoopInterface;
use React\Promise\PromiseInterface;

interface ContainerInterface
{
    static function getDefault(): ?ContainerInterface;

    static function reset(): void;

    static function setDefault(ContainerInterface $container): void;

    function setLoop(LoopInterface $loop): void;

    function getLoop(): LoopInterface;

    function attempt(string $name, $definition, bool $shared = false): ?ServiceInterface;

    function get(string $name, array $args = []): PromiseInterface;

    function getRaw(string $name): callable;

    function getService(string $name): ServiceInterface;

    /**
     * @return ServiceInterface[]
     */
    function getServices(): array;

    function getShared(string $name, array $args = []): PromiseInterface;

    function has(string $name): bool;

    function remove(string $name): void;

    function set(string $name, callable $definition, bool $shared = false): ServiceInterface;

    function setService(string $name, ServiceInterface $rawDefinition): void;

    function setShared(string $name, callable $definition): ServiceInterface;
}
