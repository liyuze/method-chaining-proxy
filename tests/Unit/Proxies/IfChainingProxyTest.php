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
        $this->assertEquals('b', (new IfChainingProxy((new Cat('a', 1)), true))->setName('b')->elseChaining()->setName('c')->popValue()->getName());
        $this->assertEquals('c', (new IfChainingProxy((new Cat('a', 1)), false))->setName('b')->elseChaining()->setName('c')->popValue()->getName());
    }

    public function test_end_xxx(): void
    {
        $this->assertEquals('a', (new IfChainingProxy((new Cat('a', 1)), true))->getName()->endIFChaining());
        $this->assertEquals('a', (new IfChainingProxy((new Cat('a', 1)), true))->getName()->endUnlessChaining());
    }

    public function test_if_nested(): void
    {
        $proxy = new IfChainingProxy((new Cat('a', 1)), true);
        $name = $proxy->setName('b')
            ->ifChaining(false)->setName('c')->elseChaining()->setName('d')->endIfChaining()
            ->getName()
            ->popValue();

        $this->assertEquals('d', $name);
        $proxy = new IfChainingProxy((new Cat('a', 1)), true);
        $name = $proxy->setName('b')
            ->ifChaining(true)->setName('c')->elseChaining()->setName('d')->endIfChaining()
            ->getName()
            ->popValue();

        $this->assertEquals('c', $name);
    }
}