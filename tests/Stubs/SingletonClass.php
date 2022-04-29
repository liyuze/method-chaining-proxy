<?php

namespace Liyuze\MethodChainingProxy\Tests\Stubs;

class SingletonClass
{
    protected function __construct()
    {
    }

    public static function getInstance()
    {
        return new self();
    }

    protected function __clone()
    {

    }
}