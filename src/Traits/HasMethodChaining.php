<?php

namespace Liyuze\MethodChainingProxy\Traits;

use Liyuze\MethodChainingProxy\Proxies\IfChainingProxy;
use Liyuze\MethodChainingProxy\Proxies\MethodChainingProxy;

/**
 * @template T of MethodChainingProxy
 */
trait HasMethodChaining
{
    protected int $callMode = MethodChainingProxy::CALL_MODE_MIXED;

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