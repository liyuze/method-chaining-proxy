<?php

namespace Liyuze\MethodChainingProxy\Traits;

use Liyuze\MethodChainingProxy\Proxies\IfChainingProxy;
use Liyuze\MethodChainingProxy\Proxies\MethodChainingProxy;

/**
 * @template T of MethodChainingProxy
 */
trait HasMethodChaining
{
    /**
     * @param  mixed  $determineValue
     * @param  ?int  $callMode
     * @return IfChainingProxy<T>
     */
    public function ifChaining(mixed $determineValue, ?int $callMode = null): IfChainingProxy
    {
        return new IfChainingProxy($this, $determineValue, $callMode ?? $this->callMode);
    }

    /**
     * @param  mixed  $determineValue
     * @param  ?int  $callMode
     * @return IfChainingProxy<T>
     */
    public function unlessChaining(mixed $determineValue, ?int $callMode = null): IfChainingProxy
    {
        $determineValue = function () use ($determineValue) {
            return ! (bool) parse_value($determineValue);
        };

        return new IfChainingProxy($this, $determineValue, $callMode ?? $this->callMode);
    }
}