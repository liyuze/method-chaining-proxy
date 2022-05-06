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

    public function test_dynamic_property(): void
    {
        $proxy = new SwitchChainingProxy(new Cat('a', 1), 2);

        $proxy->caseChaining(1)->setName('b')->breakChaining()
            ->caseChaining(2)->setName('c')->breakChaining()
            ->caseChaining(3)->setName('d')->breakChaining();

        $cat = $proxy->endSwitchChaining();
        self::assertEquals('c', $cat->getName());
    }
}