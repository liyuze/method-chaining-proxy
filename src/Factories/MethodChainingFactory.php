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
     * @return MethodChainingProxy<T>|T
     */
    public static function create($value, bool $isClone = true)
    {
        return self::mixedMode($value, $isClone);
    }

    /**
     * @param  T  $value
     * @param  bool  $isClone
     * @return MethodChainingProxy<T>|T
     */
    public static function mixedMode($value, bool $isClone = true)
    {
        return new MethodChainingProxy($value, MethodChainingProxy::CALL_MODE_MIXED, $isClone);
    }

    /**
     * @param  T  $value
     * @param  bool  $isClone
     * @return MethodChainingProxy<T>|T
     */
    public static function pipeMode($value, bool $isClone = true)
    {
        return new MethodChainingProxy($value, MethodChainingProxy::CALL_MODE_PIPE, $isClone);
    }

    /**
     * @param  T  $value
     * @param  bool  $isClone
     * @return MethodChainingProxy<T>|T
     */
    public static function tapMode($value, bool $isClone = true)
    {
        return new MethodChainingProxy($value, MethodChainingProxy::CALL_MODE_TAP, $isClone);
    }
}