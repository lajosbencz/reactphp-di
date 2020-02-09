<?php

namespace ReactDi;


interface InjectionAwareInterface
{
    public function setDI(ContainerInterface $container): void;

    public function getDI(): ContainerInterface;
}
