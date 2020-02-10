<?php

use ReactDi\Container;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testDefault()
    {
        $c1 = new Container;
        $d = Container::getDefault();
        $this->assertSame($c1, $d);

        $c2 = new Container;
        $this->assertNotSame($c2, $d);

        Container::setDefault($c2);
        $d = Container::getDefault();
        $this->assertNotSame($c1, $d);
        $this->assertSame($c2, $d);

        Container::reset();
        $this->assertNull(Container::getDefault());
    }

    public function testContainer()
    {
        $c = new Container;

        $wg = 2;

        $def = function () {
            return new React\Promise\FulfilledPromise('bar');
        };

        $this->assertEquals(0, count($c->getServices()));

        $this->assertFalse($c->has('attempt'));
        $svc = $c->attempt('attempt', $def, true);
        $this->assertNotNull($svc);
        $this->assertEquals(1, count($c->getServices()));
        $this->assertSame($svc, $c->getService('attempt'));
        $this->assertSame($def, $c->getRaw('attempt'));

        $this->assertTrue($c->has('attempt'));
        $svc = $c->attempt('attempt', $def, true);
        $this->assertNull($svc);

        $c->remove('attempt');
        $this->assertFalse($c->has('attempt'));
        $this->assertEquals(0, count($c->getServices()));

        $svc = new ReactDi\Service($def);
        $c->setService('svc', $svc);
        $this->assertSame($svc, $c->getService('svc'));

        $c->setShared('foo', $def);

        $c->get('foo')->then(function ($res) use ($c, &$wg) {
            $this->assertEquals('bar', $res);
            $wg--;
            if ($wg < 1) {
                $c->getLoop()->stop();
            }
        });

        $c->setShared('baz', function () use ($c) {
            $d = new React\Promise\Deferred;
            $c->getLoop()->addTimer(2, function () use ($d) {
                $d->resolve('bax');
            });
            return $d->promise();
        });

        $c->getShared('baz')->then(function ($res) use ($c, &$wg) {
            $this->assertEquals('bax', $res);
            $c->getShared('baz')->then(function ($res) use (&$wg, $c) {
                $this->assertEquals('bax', $res);
                $wg--;
                if ($wg < 1) {
                    $c->getLoop()->stop();
                }
            });
        });

        $c->getLoop()->run();
    }
}
