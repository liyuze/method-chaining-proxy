<?php

namespace Liyuze\MethodChainingProxy\Proxies;

/**
 * @template T
 * @mixin T
 * @template-extends MethodChainingProxy<T>
 */
class IfChainingProxy extends MethodChainingProxy
{
    /**
     * @var bool
     */
    public bool $determineValue;

    /**
     * @param  T  $value
     * @param  mixed  $determineValue
     * @param  int  $callMode
     */
    public function __construct(mixed $value, mixed $determineValue, int $callMode = MethodChainingProxy::CALL_MODE_MIXED)
    {
        $this->determineValue = (bool) parse_value($determineValue);
        parent::__construct($value, $callMode);
    }

    /**
     * @return $this
     */
    public function elseChaining(): self
    {
        $this->determineValue = ! $this->determineValue;

        return $this;
    }

    /**
     * @return T|mixed
     */
    public function endIfChaining(): mixed
    {
        return $this->popValue();
    }

    /**
     * @return T|mixed
     */
    public function endUnlessChaining(): mixed
    {
        return $this->popValue();
    }

    /**
     * @param  string  $key
     * @return $this
     */
    public function __get(string $key): self
    {
        if ($this->determineValue) {
            return parent::__get($key);
        }

        return $this;
    }

    /**
     * @param  string  $method
     * @param  array<mixed>  $parameters
     * @return $this
     */
    public function __call(string $method, array $parameters): self
    {
        if ($this->determineValue) {
            return parent::__call($method, $parameters);
        }

        return $this;
    }
}