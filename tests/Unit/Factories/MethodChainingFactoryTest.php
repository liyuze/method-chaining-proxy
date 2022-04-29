<?php

namespace Liyuze\MethodChainingProxy\Tests\Unit\Factories;

use Liyuze\MethodChainingProxy\Factories\MethodChainingFactory;
use Liyuze\MethodChainingProxy\Proxies\MethodChainingProxy;
use Liyuze\MethodChainingProxy\Tests\Stubs\Cat;
use Liyuze\MethodChainingProxy\Tests\TestCase;

class MethodChainingFactoryTest extends TestCase
{
    public function test_mixed_mode()
    {
        $cat = new Cat('a', 1);
        $proxy = MethodChainingFactory::mixedMode($cat);
        $proxy2 = new MethodChainingProxy($cat, MethodChainingProxy::CALL_MODE_MIXED);
        self::assertEquals($proxy, $proxy2);


        $proxy3 = MethodChainingFactory::mixedMode($cat, false);
        $proxy4 = new MethodChainingProxy($cat, MethodChainingProxy::CALL_MODE_MIXED, false);
        self::assertEquals($proxy3, $proxy4);
    }

    public function test_pipe_mode()
    {
        $cat = new Cat('a', 1);
        $proxy = MethodChainingFactory::pipeMode($cat);
        $proxy2 = new MethodChainingProxy($cat, MethodChainingProxy::CALL_MODE_PIPE);
        self::assertEquals($proxy, $proxy2);


        $proxy3 = MethodChainingFactory::pipeMode($cat, false);
        $proxy4 = new MethodChainingProxy($cat, MethodChainingProxy::CALL_MODE_PIPE, false);
        self::assertEquals($proxy3, $proxy4);
    }

    public function test_tap_mode()
    {
        $cat = new Cat('a', 1);
        $proxy = MethodChainingFactory::tapMode($cat);
        $proxy2 = new MethodChainingProxy($cat, MethodChainingProxy::CALL_MODE_TAP);
        self::assertEquals($proxy, $proxy2);


        $proxy3 = MethodChainingFactory::tapMode($cat, false);
        $proxy4 = new MethodChainingProxy($cat, MethodChainingProxy::CALL_MODE_TAP, false);
        self::assertEquals($proxy3, $proxy4);
    }
}