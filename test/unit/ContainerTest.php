<?php

use ReactDi\Container;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testContainer()
    {
        $loop = React\EventLoop\Factory::create();
        $c = new Container;

        $wg = 2;

        $c->setShared('foo', function () {
            return new React\Promise\FulfilledPromise('bar');
        });

        $c->get('foo')->then(function ($res) use ($loop, &$wg) {
            $this->assertEquals('bar', $res);
            $wg--;
            if ($wg < 1) {
                $loop->stop();
            }
        });

        $c->setShared('baz', function () use ($loop) {
            $d = new React\Promise\Deferred;
            $loop->addTimer(2, function () use ($loop, $d) {
                $d->resolve('bax');
            });
            return $d->promise();
        });

        $c->getShared('baz')->then(function ($res) use ($loop, &$wg, $c) {
            $this->assertEquals('bax', $res);
            $c->getShared('baz')->then(function ($res) use (&$wg, $loop) {
                $this->assertEquals('bax', $res);
                $wg--;
                if ($wg < 1) {
                    $loop->stop();
                }
            });
        });

        $loop->run();
    }
}
