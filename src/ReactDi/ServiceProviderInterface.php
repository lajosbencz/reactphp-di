<?php

namespace ReactDi;


interface ServiceProviderInterface
{
    function register(ContainerInterface $di): void;
}
