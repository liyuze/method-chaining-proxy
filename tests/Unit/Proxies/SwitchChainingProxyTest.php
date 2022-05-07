<?php

namespace Liyuze\MethodChainingProxy\Tests\Unit\Proxies;

use Liyuze\MethodChainingProxy\Proxies\SwitchChainingProxy;
use Liyuze\MethodChainingProxy\Tests\Stubs\Cat;
use Liyuze\MethodChainingProxy\Tests\TestCase;

class SwitchChainingProxyTest extends TestCase
{
    public function test_switch(): void
    {
        $value = 1;

        $proxy = new SwitchChainingProxy(new Cat('a', 1), $value);
        $proxy->caseChaining(2);
        $proxy->caseChaining(1)->setName('b')
            ->caseChaining(1)->setAge(2)
            ->caseChaining(2)->setName('c')
            ->caseChaining(2)->setAge(3);

        $cat2 = $proxy->endSwitchChaining();
        self::assertEquals('b', $cat2->getName());
        self::assertEquals(2, $cat2->getAge());
    }

    public function test_strict_mode(): void
    {
        $value = '1';

        $proxy = new SwitchChainingProxy(new Cat('a', 1), $value, true);
        $proxy->caseChaining(2);
        $proxy->caseChaining(1)->setName('b')
            ->caseChaining(1)->setAge(2);

        $cat2 = $proxy->endSwitchChaining();
        self::assertEquals('a', $cat2->getName());
        self::assertEquals(1, $cat2->getAge());
    }

    public function test_switch_default(): void
    {
        $cat = new Cat('a', 1);
        $proxy = new SwitchChainingProxy($cat, 2);
        $proxy->caseChaining(1)->setName('b')->breakChaining()
            ->caseChaining(2)->setName('c')
            ->defaultChaining(2)->setName('d');

        self::assertEquals('d', $cat->getName());

        $cat = new Cat('a', 1);
        $proxy = new SwitchChainingProxy($cat, 2);
        $proxy->caseChaining(1)->setName('b')
            ->caseChaining(2)->setName('c')->breakChaining()
            ->defaultChaining(2)->setName('d');

        self::assertEquals('c', $cat->getName());
    }

    public function test_break_multilayer(): void
    {
        $cat = new Cat('a', 1);
        $proxy = new SwitchChainingProxy($cat, 2);
        $proxy->caseChaining(1)->setName('b')
            ->caseChaining(2)->setName('c')
            ->switchChaining(3)->caseChaining(3)->setAge(5)->breakChaining()->endSwitchChaining()
            ->caseChaining(2)->setAge(10);

        self::assertEquals(10, $cat->getAge());

        $cat = new Cat('a', 1);
        $proxy = new SwitchChainingProxy($cat, 2);
        $proxy->caseChaining(1)->setName('b')
            ->caseChaining(2)->setName('c')
            ->switchChaining(3)->caseChaining(3)->setAge(5)->breakChaining(2)->endSwitchChaining()
            ->caseChaining(2)->setAge(10);

        self::assertEquals(5, $cat->getAge());
    }

    public function test_break_chaining(): void
    {
        $value = 1;

        $proxy = new SwitchChainingProxy(new Cat('a', 1), $value);
        $proxy->caseChaining(1)->setName('b')->breakChaining()
            ->caseChaining(1)->setAge(2)
            ->caseChaining(2)->setName('c')
            ->caseChaining(2)->setAge(3);

        $cat2 = $proxy->endSwitchChaining();
        self::assertEquals('b', $cat2->getName());
        self::assertEquals(1, $cat2->getAge());
    }
}