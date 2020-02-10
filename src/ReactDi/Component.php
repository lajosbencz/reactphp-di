<?php

namespace ReactDi;


abstract class Component implements InjectionAwareInterface
{
    use InjectionAwareTrait;

    public function __construct(?ContainerInterface $container = null)
    {
        $this->setDependencyContainer($container ?? Container::getDefault());
    }
}
