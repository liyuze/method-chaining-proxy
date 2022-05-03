<?php

namespace Liyuze\MethodChainingProxy\Traits;

use Liyuze\MethodChainingProxy\Proxies\IfChainingProxy;

/**
 * @template T
 */
trait HasMethodChaining
{
    /**
     * @param  mixed  $determineValue
     * @param  ?int  $callMode
     * @return IfChainingProxy<T>
     */
    public function if(mixed $determineValue, ?int $callMode = null): IfChainingProxy
    {
        return new IfChainingProxy($this, $determineValue, $callMode ?? $this->callMode);
    }

    /**
     * @param  mixed  $determineValue
     * @param  ?int  $callMode
     * @return IfChainingProxy<T>
     */
    public function unless(mixed $determineValue, ?int $callMode = null): IfChainingProxy
    {
        $determineValue = function () use ($determineValue) {
            return ! (bool) parse_value($determineValue);
        };

        return new IfChainingProxy($this, $determineValue, $callMode ?? $this->callMode);
    }
}