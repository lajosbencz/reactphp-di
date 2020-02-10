<?php

namespace ReactDi;


trait InjectionAwareTrait
{
    protected $_dependencyContainer;

    public function setDependencyContainer(ContainerInterface $container): void
    {
        $this->_dependencyContainer = $container;
    }

    public function getDependencyContainer(): ContainerInterface
    {
        if (!$this->_dependencyContainer) {
            $this->_dependencyContainer = Container::getDefault();
        }
        return $this->_dependencyContainer;
    }

    /**
     * @param string $name
     * @return \React\Promise\PromiseInterface|null
     */
    public function __get(string $name)
    {
        $dc = $this->getDependencyContainer();
        if($dc->has($name)) {
            return $dc->getShared($name);
        }
        return null;
    }
}
