<?php

namespace Liyuze\MethodChainingProxy\Tests\Unit\Proxies;

use Liyuze\MethodChainingProxy\Proxies\IfChainingProxy;
use Liyuze\MethodChainingProxy\Tests\Stubs\Cat;
use Liyuze\MethodChainingProxy\Tests\TestCase;

class IfChainingProxyTest extends TestCase
{
    public function test_default(): void
    {
        $this->assertInstanceOf(Cat::class, (new IfChainingProxy((new Cat('a', 1)), false))->getName()->popValue());
        $this->assertEquals('a', (new IfChainingProxy((new Cat('a', 1)), true))->getName()->popValue());
    }

    public function test_unless(): void
    {
        $this->assertInstanceOf(Cat::class, (new IfChainingProxy((new Cat('a', 1)), false))->getName()->popValue());
        $this->assertEquals('a', (new IfChainingProxy((new Cat('a', 1)), true))->getName()->popValue());
    }

    public function test_else(): void
    {
        $this->assertEquals('b', (new IfChainingProxy((new Cat('a', 1)), true))->setName('b')->else()->setName('c')->popValue()->getName());
        $this->assertEquals('c', (new IfChainingProxy((new Cat('a', 1)), false))->setName('b')->else()->setName('c')->popValue()->getName());
    }

    public function test_end_if(): void
    {
        $this->assertEquals('a', (new IfChainingProxy((new Cat('a', 1)), true))->getName()->endIF());
    }

    public function test_if_nested(): void
    {
        $proxy = new IfChainingProxy((new Cat('a', 1)), true);
        $name = $proxy->setName('b')
            ->if(false)->setName('c')->else()->setName('d')->endIf()
            ->getName()
            ->popValue();

        $this->assertEquals('d', $name);
        $proxy = new IfChainingProxy((new Cat('a', 1)), true);
        $name = $proxy->setName('b')
            ->if(true)->setName('c')->else()->setName('d')->endIf()
            ->getName()
            ->popValue();

        $this->assertEquals('c', $name);
    }

    public function test_dynamic_property(): void
    {
        $this->assertEquals('b', (new IfChainingProxy((new Cat('a', 1)), true))->setName('b')->else->setName('c')->popValue()->getName());
        $this->assertEquals('c', (new IfChainingProxy((new Cat('a', 1)), false))->setName('b')->else->setName('c')->popValue()->getName());
    }
}