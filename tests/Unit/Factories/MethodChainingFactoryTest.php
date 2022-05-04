<?php

namespace Liyuze\MethodChainingProxy\Tests\Unit\Factories;

use Liyuze\MethodChainingProxy\Factories\MethodChainingFactory;
use Liyuze\MethodChainingProxy\Proxies\MethodChainingProxy;
use Liyuze\MethodChainingProxy\Tests\Stubs\Cat;
use Liyuze\MethodChainingProxy\Tests\TestCase;

class MethodChainingFactoryTest extends TestCase
{
    public function test_default(): void
    {
        $cat = new Cat('a', 1);
        $proxy = MethodChainingFactory::create($cat);
        $proxy2 = new MethodChainingProxy($cat, MethodChainingProxy::CALL_MODE_MIXED);
        self::assertEquals($proxy, $proxy2);
    }

    public function test_mixed(): void
    {
        $cat = new Cat('a', 1);
        $proxy = MethodChainingFactory::mixed($cat);
        $proxy2 = new MethodChainingProxy($cat, MethodChainingProxy::CALL_MODE_MIXED);
        self::assertEquals($proxy, $proxy2);
    }

    public function test_pipe(): void
    {
        $cat = new Cat('a', 1);
        $proxy = MethodChainingFactory::pipe($cat);
        $proxy2 = new MethodChainingProxy($cat, MethodChainingProxy::CALL_MODE_PIPE);
        self::assertEquals($proxy, $proxy2);
    }

    public function test_tap(): void
    {
        $cat = new Cat('a', 1);
        $proxy = MethodChainingFactory::tap($cat);
        $proxy2 = new MethodChainingProxy($cat, MethodChainingProxy::CALL_MODE_TAP);
        self::assertEquals($proxy, $proxy2);
    }
}