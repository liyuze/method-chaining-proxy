<?php

namespace Liyuze\MethodChainingProxy\Tests\Unit\Factories;

use Liyuze\MethodChainingProxy\Factories\ControlChainingFactory;
use Liyuze\MethodChainingProxy\Proxies\IfChainingProxy;
use Liyuze\MethodChainingProxy\Proxies\SwitchChainingProxy;
use Liyuze\MethodChainingProxy\Tests\Stubs\Cat;
use Liyuze\MethodChainingProxy\Tests\TestCase;

class ControlChainingFactoryTest extends TestCase
{
    public function test_if(): void
    {
        $value = new Cat('a', 1);
        self::assertEquals(ControlChainingFactory::if($value, true), new IfChainingProxy($value, true));
    }

    public function test_unless(): void
    {
        $value = new Cat('a', 1);
        self::assertEquals(ControlChainingFactory::unless($value, true), new IfChainingProxy($value, false));
    }

    public function test_switch(): void
    {
        $value = new Cat('a', 1);
        self::assertEquals(ControlChainingFactory::switch($value, true), new SwitchChainingProxy($value, true));
    }
}