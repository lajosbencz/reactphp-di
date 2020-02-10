<?php

namespace ReactDi;


interface InjectionAwareInterface
{
    public function setDependencyContainer(ContainerInterface $container): void;

    public function getDependencyContainer(): ContainerInterface;
}
