<?php

namespace Liyuze\MethodChainingProxy\Factories;

use Liyuze\MethodChainingProxy\Proxies\IfChainingProxy;
use Liyuze\MethodChainingProxy\Proxies\MethodChainingProxy;
use Liyuze\MethodChainingProxy\Proxies\SwitchChainingProxy;

class ControlChainingFactory
{
    /**
     * @template T
     * @param  T  $value
     * @param  mixed  $determineValue
     * @param  int  $callMode
     * @return IfChainingProxy<T>
     */
    public static function if(mixed $value, mixed $determineValue, int $callMode = MethodChainingProxy::CALL_MODE_MIXED)
    {
        return new IfChainingProxy($value, $determineValue, $callMode);
    }

    /**
     * @template T
     * @param  T  $value
     * @param  mixed  $determineValue
     * @param  int  $callMode
     * @return IfChainingProxy<T>
     */
    public static function unless(mixed $value, mixed $determineValue, int $callMode = MethodChainingProxy::CALL_MODE_MIXED)
    {
        $determineValue = function () use ($determineValue) {
            return ! (bool) parse_value($determineValue);
        };

        return new IfChainingProxy($value, $determineValue, $callMode);
    }

    /**
     * @template T
     * @param  T  $value
     * @param  mixed  $switchValue
     * @param  bool  $isStrict
     * @param  int  $callMode
     * @return SwitchChainingProxy<T>
     */
    public static function switch(mixed $value, mixed $switchValue, bool $isStrict = false, int $callMode = MethodChainingProxy::CALL_MODE_MIXED)
    {
        return new SwitchChainingProxy($value, $switchValue, $isStrict, $callMode);
    }
}