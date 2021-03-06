<?php

namespace Liyuze\MethodChainingProxy\Tests\Unit\Proxies;

use Liyuze\MethodChainingProxy\Factories\MethodChainingFactory;
use Liyuze\MethodChainingProxy\Proxies\MethodChainingProxy;
use Liyuze\MethodChainingProxy\Tests\Stubs\Cat;
use Liyuze\MethodChainingProxy\Tests\TestCase;

class MethodChainingProxyTest extends TestCase
{
    //mixed_mode
    public function test_default(): void
    {
        $cat = new Cat('a', 1);
        $proxy = new MethodChainingProxy($cat);
        $proxy = $proxy->setName('c')->setName('b');
        $this->assertsame($cat, $proxy->popValue());
        $this->assertEquals('b', $proxy->popValue()->getName());
        $this->assertEquals('b', $proxy->getName()->popValue());
    }

    public function test_pipe_mode(): void
    {
        $cat = new Cat('a', 1);
        $proxy = new MethodChainingProxy($cat, MethodChainingProxy::CALL_MODE_PIPE);
        $proxy = $proxy->setName('c')->setName('b');
        $this->assertsame($cat, $proxy->popValue());
        $this->assertEquals('b', $proxy->popValue()->getName());
        $this->assertEquals('b', $proxy->getName()->popValue());
    }

    public function test_tap_mode(): void
    {
        $cat = new Cat('a', 1);
        $proxy = new MethodChainingProxy($cat, MethodChainingProxy::CALL_MODE_TAP);
        $proxy->setAge(2)->getName();
        $this->assertsame($cat, $proxy->popValue());
        $this->assertEquals(2, $proxy->popValue()->getAge());
    }

    public function test_switch_mode_for_manually(): void
    {
        $value1 = (new MethodChainingProxy(new Cat('a', 1)))->switchToTapMode()->getName()->popValue();
        $this->assertEquals('a', (new MethodChainingProxy(new Cat('a', 1)))->getName()->popValue());
        $this->assertInstanceOf(Cat::class, (new MethodChainingProxy(new Cat('a', 1)))->switchToTapMode()->getName()->popValue());


        $this->assertInstanceOf(Cat::class, (new MethodChainingProxy(new Cat('a', 1)))->setAge(2)->popValue());
        $this->assertNull((new MethodChainingProxy(new Cat('a', 1)))->switchToPipeMode()->setAge(2)->popValue());

        $this->assertEquals(2, (new MethodChainingProxy(new Cat('a', 1)))->switchToTapMode()->setAge(2)->switchToPipeMode()->getAge()->popValue());
        $this->assertEquals(2, (new MethodChainingProxy(new Cat('a', 1), MethodChainingProxy::CALL_MODE_PIPE))->switchToMixedMode()->setAge(2)->getAge()->popValue());
    }

    public function test_after(): void
    {
        $cat = new Cat('a', 1);
        $proxy = new MethodChainingProxy($cat);
        $name = $proxy->setAge(2)->after(function (Cat $cat) {
            $cat->setName('b');
        })->getName()->popValue();

        $this->assertEquals('b', $name);

        //return value
        $proxy = new MethodChainingProxy($cat);
        $number = $proxy->after(function (Cat $cat) {
            return 2;
        })->popValue();

        $this->assertEquals(2, $number);
    }

    public function test_tap_once_call_mode(): void
    {
        $cat = new Cat('a', 1);
        $proxy = MethodChainingFactory::pipe($cat);
        $proxy->setAge(2);
        self::assertNull($proxy->popValue());
        $proxy2 = MethodChainingFactory::pipe($cat);
        $proxy2->tapOnce()->setAge(2);
        self::assertSame($cat, $proxy2->popValue());
    }

    public function test_pipe_once_call_mode(): void
    {
        $cat = new Cat('a', 1);
        $proxy = MethodChainingFactory::tap($cat);
        $proxy->getAge();
        self::assertEquals($cat, $proxy->popValue());
        $proxy2 = MethodChainingFactory::tap($cat);
        $proxy2->pipeOnce()->getAge();
        self::assertSame(1, $proxy2->popValue());
    }

    public function test_pick(): void
    {
        $cat = new Cat('a', 1);
        $proxy = MethodChainingFactory::tap($cat);
        $name = null;

        self::assertSame($proxy, $proxy->pick('name', $name));
        self::assertEquals('a', $name);
    }

    public function test_method_pick(): void
    {
        $cat = new Cat('a', 1);
        $proxy = MethodChainingFactory::tap($cat);
        $name = null;

        self::assertSame($proxy, $proxy->methodPick($name, 'setName', 'b'));
        self::assertEquals('b', $proxy->popValue()->getName());
    }
}