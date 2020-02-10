<?php

use ReactDi\Component;
use PHPUnit\Framework\TestCase;

class myComponent extends Component
{

}

class ComponentTest extends TestCase
{
    public function testContainerShared()
    {
        $instance = null;
        $container = new ReactDi\Container;
        $container->setShared('svc', function () use(&$instance) {
            $instance = new stdClass;
            return new React\Promise\FulfilledPromise($instance);
        });
        $component = new myComponent($container);
        $component->svc->then(function ($svc) use($instance) {
            $this->assertSame($instance, $svc);
        });
        $component->svc->then(function ($svc) use($instance) {
            $this->assertSame($instance, $svc);
        });
    }

    public function testContainerNotShared()
    {
        $container = new ReactDi\Container;
        $container->set('svc', function () {
            return new React\Promise\FulfilledPromise(new stdClass);
        });
        $component = new myComponent($container);
        $component->svc->then(function ($svc1) use($component) {
            $component->svc->then(function ($svc2) use($svc1) {
                $this->assertNotSame($svc1, $svc2);
            });
        });
    }
}
