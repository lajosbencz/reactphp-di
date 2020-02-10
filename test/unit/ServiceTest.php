<?php


use ReactDi\Service;
use PHPUnit\Framework\TestCase;


class svcProvider implements \ReactDi\ServiceProviderInterface
{
    public $di;

    function register(\ReactDi\ContainerInterface $di): void
    {
        $this->di = $di;
    }
}

class ServiceTest extends TestCase
{
    public function testService()
    {

        $def = function($val='svc') {
            $payload = new stdClass();
            $payload->foo = $val;
            return new React\Promise\FulfilledPromise($payload);
        };

        $s = new Service($def, false);

        $this->assertFalse($s->isShared());
        $this->assertFalse($s->isResolved());
        $this->assertEquals($def, $s->getDefinition());

        $res = $s->resolve(['arg1']);
        $this->assertTrue($s->isResolved());

        $res->then(function ($r) {
            $this->assertEquals('arg1', $r->foo);
        });

        $res->then(function ($r2) use($s) {
            $s->resolve(['arg1'])->then(function ($r3) use($r2) {
                $this->assertNotNull($r2);
                $this->assertNotNull($r3);
                $this->assertNotSame($r2, $r3);
            });
        });

        $s->setShared(true);
        $this->assertTrue($s->isShared());


        $res->then(function ($r2) use($s) {
            $s->resolve(['arg1'])->then(function ($r3) use($r2) {
                $this->assertNotNull($r2);
                $this->assertNotNull($r3);
                $this->assertSame($r2, $r3);
            });
        });
    }
}
