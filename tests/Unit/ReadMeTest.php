<?php

namespace Liyuze\MethodChainingProxy\Tests\Unit;

use Liyuze\MethodChainingProxy\Factories\ControlChainingFactory;
use Liyuze\MethodChainingProxy\Factories\MethodChainingFactory;
use Liyuze\MethodChainingProxy\Tests\Stubs\Cat;
use Liyuze\MethodChainingProxy\Tests\TestCase;

class ReadMeTest extends TestCase
{
    public function test_mixed_mode(): void
    {
        $cat = new Cat('Tom', 5);
        $proxy = MethodChainingFactory::create($cat);
        //或
        $proxy = MethodChainingFactory::mixed($cat);
        $proxy = $proxy->setAge(9)->setName('Tony')->getName();
        self::assertEquals('Tony', $proxy->popValue());
        self::assertEquals('Tony', $cat->getName());
    }

    public function test_tap_mode(): void
    {
        $cat = new Cat('Tom', 5);
        $proxy = MethodChainingFactory::tap($cat);
        $proxy = $proxy->setAge(9)->setName('Tony')->getName();
        self::assertEquals($cat, $proxy->popValue());
        self::assertEquals(9, $cat->getAge());
    }

    public function test_pipe_mode(): void
    {
        $cat = new Cat('Tom', 5);
        $proxy = MethodChainingFactory::pipe($cat);
        $proxy = $proxy->setAge(9);
        self::assertNull($proxy->popValue());
        self::assertEquals(9, $cat->getAge());
    }

    public function test_switch_mode(): void
    {
        $cat = new Cat('Tom', 5);
        $proxy = MethodChainingFactory::tap($cat);
        $proxy = $proxy->switchToPipeMode()->getName();
        self::assertEquals('Tom', $proxy->popValue());
        self::assertEquals('Tom', $cat->getName());
    }

    public function test_once_mode(): void
    {
        $cat = new Cat('Tom', 5);
        $proxy = MethodChainingFactory::tap($cat);
        $proxy = $proxy->pipeOnce()->getName();
        self::assertEquals('Tom', $proxy->popValue());
        self::assertEquals('Tom', $cat->getName());
    }

    public function test_pick(): void
    {
        $cat = new Cat('Tom', 5);
        $name = null;
        $proxy = MethodChainingFactory::create($cat);
        $proxy->setAge(9)->pick('name', $name)->setName('Tony');
        self::assertEquals('Tom', $name);
        self::assertEquals('Tony', $cat->getName());
    }

    public function test_method_pick(): void
    {
        $cat = new Cat('Tom', 5);
        $name = null;
        $proxy = MethodChainingFactory::create($cat);
        $proxy = $proxy->setAge(9)->methodPick($name, 'getName')->setName('Tony');
        self::assertEquals('Tom', $name);
        self::assertEquals('Tony', $cat->getName());
    }

    public function test_after(): void
    {
        $cat = new Cat('Tom', 5);
        $birthMonth = 3;
        $proxy = MethodChainingFactory::create($cat);
        $proxy = $proxy->setAge(9)->setName('Tony')->after(
            function ($proxyValue) use ($birthMonth) {
                //6月前出生的加1岁
                /** @phpstan-ignore-next-line */
                if ($birthMonth < 6) {
                    $proxyValue->setAge($proxyValue->getAge() + 1);
                }
            }
        );

        self::assertEquals(10, $proxy->popValue()->getAge());


        $number = MethodChainingFactory::create($cat)->after(fn () => 3)->popValue();
        self::assertEquals(3, $number);
    }

    public function test_if(): void
    {
        $ifProxy = ControlChainingFactory::if(new Cat('Tom', 5), false);
        // 或 $ifProxy = new IfChainingProxy(new Cat('Tom', 5), false);
        $ifProxy->setName('Tony')
            ->elseChaining()
            ->setName('Alan');

        self::assertEquals('Alan', $ifProxy->endIfChaining()->getName());
    }

    public function test_switch(): void
    {
        $switchProxy = ControlChainingFactory::switch(new Cat('Tom', 5), 2);
        // 或 $switchProxy = new SwitchChainingProxy(new Cat('Tom', 5), 2);
        $cat = $switchProxy
            ->caseChaining(1)->setName('Tony')
            ->caseChaining(2)->setName('Alan')
            ->endSwitchChaining();

        self::assertEquals('Alan', $cat->getName());
    }

    public function test_switch_break(): void
    {
        $switchProxy = ControlChainingFactory::switch(new Cat('Tom', 5), 2);
        // 或 $switchProxy = new SwitchChainingProxy(new Cat('Tom', 5), 2);
        $cat = $switchProxy
            ->caseChaining(1)->setName('Tony')->breakChaining()
            ->caseChaining(2)->setName('Alan')
            ->caseChaining(2)->setAge(10)->breakChaining()
            ->caseChaining(2)->setName('Andy')
            ->endSwitchChaining();

        self::assertEquals('Alan', $cat->getName());
        self::assertEquals(10, $cat->getAge());
    }

    public function test_switch_default(): void
    {
        $switchProxy = ControlChainingFactory::switch(new Cat('Tom', 5), 2);
        // 或 $switchProxy = new SwitchChainingProxy(new Cat('Tom', 5), 2);
        $cat = $switchProxy
            ->caseChaining(1)->setName('Tony')->breakChaining()
            ->defaultChaining()->setName('Alan')->setAge(10)
            ->endSwitchChaining();

        self::assertEquals('Alan', $cat->getName());
        self::assertEquals(10, $cat->getAge());
    }
}