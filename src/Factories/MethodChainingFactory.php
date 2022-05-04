<?php

namespace Liyuze\MethodChainingProxy\Factories;

use Liyuze\MethodChainingProxy\Proxies\MethodChainingProxy;

class MethodChainingFactory
{
    /**
     * @template T
     * @param  T  $value
     * @return MethodChainingProxy<T>
     */
    public static function create(mixed $value): MethodChainingProxy
    {
        return self::mixed($value);
    }

    /**
     * @template T
     * @param  T  $value
     * @return MethodChainingProxy<T>
     */
    public static function mixed(mixed $value): MethodChainingProxy
    {
        return new MethodChainingProxy($value, MethodChainingProxy::CALL_MODE_MIXED);
    }

    /**
     * @template T
     * @param  T  $value
     * @return MethodChainingProxy<T>
     */
    public static function pipe(mixed $value): MethodChainingProxy
    {
        return new MethodChainingProxy($value, MethodChainingProxy::CALL_MODE_PIPE);
    }

    /**
     * @template T
     * @param  T  $value
     * @return MethodChainingProxy<T>
     * @phpstan-return MethodChainingProxy<T>
     */
    public static function tap(mixed $value): MethodChainingProxy
    {
        return new MethodChainingProxy($value, MethodChainingProxy::CALL_MODE_TAP);
    }
}