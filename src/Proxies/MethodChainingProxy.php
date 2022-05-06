<?php

namespace Liyuze\MethodChainingProxy\Proxies;

use Liyuze\MethodChainingProxy\Traits\HasMethodChaining;

/**
 * @template T
 * @mixin T
 * @property-read  MethodChainingProxy<T>|T $tapMode
 * @property-read  MethodChainingProxy<T>|T $pipeMode
 * @property-read  MethodChainingProxy<T>|T $mixedMode
 * @property-read  MethodChainingProxy<T>|T $tapOnce
 * @property-read  MethodChainingProxy<T>|T $pipeOnce
 */
class MethodChainingProxy
{
    /** @phpstan-use HasMethodChaining<$this> */
    use HasMethodChaining;

    public const CALL_MODE_PIPE = 1;
    public const CALL_MODE_TAP = 2;
    public const CALL_MODE_MIXED = 3;

    /**
     * @var T
     */
    protected mixed $proxyValue;

    protected int $callMode = self::CALL_MODE_MIXED;

    protected ?int $onceCallMode = null;

    /**
     * @param  T  $target
     * @param  int  $callMode
     */
    public function __construct(mixed $target, int $callMode = self::CALL_MODE_MIXED)
    {
        $this->proxyValue = $target;
        $this->callMode = $callMode;
    }

    /**
     * @return T
     */
    public function popValue(): mixed
    {
        return $this->proxyValue;
    }

    /**
     * @return $this
     */
    public function switchToMixedMode(): self
    {
        return $this->setCallMode(self::CALL_MODE_MIXED);
    }

    /**
     * @return $this
     */
    public function switchToTapMode(): self
    {
        return $this->setCallMode(self::CALL_MODE_TAP);
    }

    /**
     * @return $this
     */
    public function switchToPipeMode(): self
    {
        return $this->setCallMode(self::CALL_MODE_PIPE);
    }

    /**
     * @return $this
     */
    public function tapOnce(): self
    {
        return $this->setOnceCallMode(self::CALL_MODE_TAP);
    }

    /**
     * @return $this
     */
    public function pipeOnce(): self
    {
        return $this->setOnceCallMode(self::CALL_MODE_PIPE);
    }

    /**
     * @param  int  $mode
     * @return $this
     */
    protected function setCallMode(int $mode): self
    {
        $this->callMode = $mode;

        return $this;
    }

    /**
     * @param ?int  $mode
     * @return $this
     */
    protected function setOnceCallMode(?int $mode): self
    {
        $this->onceCallMode = $mode;

        return $this;
    }

    /**
     * @param  \Closure  $closure
     * @return $this
     */
    public function after(\Closure $closure): self
    {
        $result = $closure($this->proxyValue);
        if ($result !== null) {
            $this->proxyValue = $result;
        }

        return $this;
    }

    /**
     * @param  string  $name
     * @param  mixed  $value
     * @return $this
     */
    public function pick(string $name, mixed &$value): self
    {
        $value = $this->proxyValue->{$name};

        return $this;
    }

    /**
     * @param  mixed  $value
     * @param  string  $method  mixed|T
     * @param  array<mixed>  $parameters
     * @return $this
     */
    public function methodPick(mixed &$value, string $method, ...$parameters): self
    {
        $value = $this->proxyValue->{$method}(...$parameters);

        return $this;
    }

    /**
     * @param  string  $key
     * @return $this
     */
    protected function callDynamicProperty(string $key): self
    {
        if (in_array($key, ['tapMode', 'pipeMode', 'mixedMode'])) {
            $key = "switchTo{$key}";

            return $this->{$key}();
        }

        if (in_array($key, ['tapOnce', 'pipeOnce'])) {
            return $this->{$key}();
        }

        return $this;
    }

    /**
     * @return  array<string>
     */
    protected function dynamicPropertyList(): array
    {
        return ['tapMode', 'pipeMode', 'mixedMode', 'tapOnce', 'pipeOnce'];
    }

    /**
     * @param  string  $key
     * @return $this
     */
    public function __get(string $key): self
    {
        if (in_array($key, $this->dynamicPropertyList())) {
            return $this->callDynamicProperty($key);
        }

        $this->proxyValue = is_array($this->proxyValue) ? $this->proxyValue[$key] : $this->proxyValue->{$key};

        return $this;
    }

    /**
     * @param  string  $method
     * @param  array<mixed>  $parameters
     * @return $this
     */
    public function __call(string $method, array $parameters): self
    {
        $result = $this->proxyValue->{$method}(...$parameters);

        if ($this->onceCallMode !== null) {
            if ($this->onceCallMode == self::CALL_MODE_PIPE) {
                $this->proxyValue = $result;
            }
            $this->onceCallMode = null;
        } elseif ($this->callMode == self::CALL_MODE_PIPE || ($this->callMode == self::CALL_MODE_MIXED && ! is_null($result))) {
            $this->proxyValue = $result;
        }

        return $this;
    }
}
