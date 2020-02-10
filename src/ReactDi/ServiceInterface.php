<?php

namespace ReactDi;


use React\Promise\PromiseInterface;

interface ServiceInterface
{
    function setDefinition(callable $definition): self;

    function getDefinition(): callable;

    function setShared(bool $shared = true): void;

    function isShared(): bool;

    function isResolved(): bool;

    function resolve(array $args=[], ?ContainerInterface $di=null): PromiseInterface;
}
