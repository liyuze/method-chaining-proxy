<?php

namespace Liyuze\MethodChainingProxy\Tests\Unit\Proxies;

use Liyuze\MethodChainingProxy\Factories\MethodChainingFactory;
use Liyuze\MethodChainingProxy\Proxies\MethodChainingProxy;
use Liyuze\MethodChainingProxy\Tests\Stubs\Cat;
use Liyuze\MethodChainingProxy\Tests\Stubs\SingletonClass;
use Liyuze\MethodChainingProxy\Tests\TestCase;

class MethodChainingProxyTest extends TestCase
{
    public function test_default_for_mixed_mode()
    {
        $cat = new Cat('a', 1);
        $proxy = new MethodChainingProxy($cat);
        $proxy = $proxy->setName('c')->setName('b');
        $this->assertsame($cat, $proxy->popValue());
        $this->assertEquals('b', $proxy->popValue()->getName());
        $this->assertEquals('b', $proxy->getName()->popValue());
    }

    public function test_pipe_mode()
    {
        $cat = new Cat('a', 1);
        $proxy = new MethodChainingProxy($cat, MethodChainingProxy::CALL_MODE_PIPE);
        $proxy = $proxy->setName('c')->setName('b');
        $this->assertsame($cat, $proxy->popValue());
        $this->assertEquals('b', $proxy->popValue()->getName());
        $this->assertEquals('b', $proxy->getName()->popValue());
    }

    public function test_tap_mode()
    {
        $cat = new Cat('a', 1);
        $proxy = new MethodChainingProxy($cat, MethodChainingProxy::CALL_MODE_TAP);
        $proxy->setAge(2)->getName();
        $this->assertsame($cat, $proxy->popValue());
        $this->assertEquals(2, $proxy->popValue()->getAge());
    }

    public function test_switch_mode_for_manually()
    {

        $value1 = (new MethodChainingProxy(new Cat('a', 1)))->tap->getName()->popValue();
        $this->assertEquals('a', (new MethodChainingProxy(new Cat('a', 1)))->getName()->popValue());
        $this->assertInstanceOf(Cat::class, (new MethodChainingProxy(new Cat('a', 1)))->tap->getName()->popValue());


        $this->assertInstanceOf(Cat::class, (new MethodChainingProxy(new Cat('a', 1)))->setAge(2)->popValue());
        $this->assertNull((new MethodChainingProxy(new Cat('a', 1)))->pipe->setAge(2)->popValue());

        $this->assertEquals(2, (new MethodChainingProxy(new Cat('a', 1)))->tap->setAge(2)->pipe->getAge()->popValue());
        $this->assertEquals(2, (new MethodChainingProxy(new Cat('a', 1), MethodChainingProxy::CALL_MODE_PIPE))->mixed->setAge(2)->getAge()->popValue());
    }

    public function test_clone()
    {
        $cat = new Cat('a', 1);
        $proxy = new MethodChainingProxy($cat);
        $this->assertSame($cat, $proxy->popValue());
        $this->assertNotSame($cat, $proxy->popCloneValue());

        $proxy2 = new MethodChainingProxy($cat, MethodChainingProxy::CALL_MODE_MIXED, false);
        $this->assertSame($cat, $proxy2->popValue());
        $this->assertSame($cat, $proxy2->popCloneValue());


        $singleton = SingletonClass::getInstance();
        $singletonProxy = new MethodChainingProxy($singleton);
        $this->assertSame($singleton, $singletonProxy->popValue());
        $this->assertSame($singleton, $singletonProxy->popCloneValue());
    }

    public function test_after()
    {
        $cat = new Cat('a', 1);
        $proxy = new MethodChainingProxy($cat);
        $name = $proxy->setAge(2)->after(function (Cat $cat) {
            $cat->setName('b');
        })->getName()->popValue();

        $this->assertEquals('b', $name);
    }

    public function test_tap_once_call_mode()
    {
        $cat = new Cat('a', 1);
        $proxy = MethodChainingFactory::pipeMode($cat);
        $proxy->setAge(2);
        self::assertNull($proxy->popValue());
        $proxy2 = MethodChainingFactory::pipeMode($cat);
        $proxy2->tapOnce()->setAge(2);
        self::assertSame($cat, $proxy2->popValue());
    }

    public function test_pipe_once_call_mode()
    {
        $cat = new Cat('a', 1);
        $proxy = MethodChainingFactory::tapMode($cat);
        $proxy->getAge();
        self::assertEquals($cat, $proxy->popValue());
        $proxy2 = MethodChainingFactory::tapMode($cat);
        $proxy2->pipeOnce()->getAge();
        self::assertSame(1, $proxy2->popValue());
    }

    public function test_pick()
    {
        $cat = new Cat('a', 1);
        $proxy = MethodChainingFactory::tapMode($cat);
        $name = null;

        self::assertSame($proxy, $proxy->pick('name', $name));
        self::assertEquals('a', $name);
    }
    public function test_method_pick()
    {
        $cat = new Cat('a', 1);
        $proxy = MethodChainingFactory::tapMode($cat);
        $name = null;

        self::assertSame($proxy, $proxy->methodPick($name, 'setName', 'b'));
        self::assertEquals('b', $proxy->popValue()->getName());
    }
}