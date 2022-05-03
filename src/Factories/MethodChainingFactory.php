<?php

namespace Liyuze\MethodChainingProxy\Factories;

use Liyuze\MethodChainingProxy\Proxies\MethodChainingProxy;

/**
 * @template T
 */
class MethodChainingFactory
{
    /**
     * @param  T  $value
     * @param  bool  $isClone
     * @return MethodChainingProxy<T>
     */
    public static function create(mixed $value, bool $isClone = true): MethodChainingProxy
    {
        return self::mixedMode($value, $isClone);
    }

    /**
     * @param  T  $value
     * @param  bool  $isClone
     * @return MethodChainingProxy<T>
     */
    public static function mixedMode(mixed $value, bool $isClone = true): MethodChainingProxy
    {
        return new MethodChainingProxy($value, MethodChainingProxy::CALL_MODE_MIXED, $isClone);
    }

    /**
     * @param  T  $value
     * @param  bool  $isClone
     * @return MethodChainingProxy<T>
     */
    public static function pipeMode(mixed $value, bool $isClone = true): MethodChainingProxy
    {
        return new MethodChainingProxy($value, MethodChainingProxy::CALL_MODE_PIPE, $isClone);
    }

    /**
     * @param  T  $value
     * @param  bool  $isClone
     * @return MethodChainingProxy<T>
     */
    public static function tapMode(mixed $value, bool $isClone = true): MethodChainingProxy
    {
        return new MethodChainingProxy($value, MethodChainingProxy::CALL_MODE_TAP, $isClone);
    }
}