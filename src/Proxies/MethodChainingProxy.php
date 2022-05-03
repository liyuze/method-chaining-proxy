<?php

namespace Liyuze\MethodChainingProxy\Proxies;

use Liyuze\MethodChainingProxy\Traits\HasMethodChaining;

/**
 * @template T
 * @mixin T
 * @property-read  MethodChainingProxy<T>|T $tap
 * @property-read  MethodChainingProxy<T>|T $pipe
 * @property-read  MethodChainingProxy<T>|T $mixed
 * @property-read  MethodChainingProxy<T>|T $tapOnce
 * @property-read  MethodChainingProxy<T>|T $pipeOnce
 */
class MethodChainingProxy
{
    /** @phpstan-use HasMethodChaining<T> */
    use HasMethodChaining;

    const CALL_MODE_PIPE = 1;
    const CALL_MODE_TAP = 2;
    const CALL_MODE_MIXED = 3;

    /**
     * @var T
     */
    protected mixed $proxyValue;

    /**
     * @var T
     */
    protected mixed $cloneValue;

    protected int $callMode = self::CALL_MODE_MIXED;

    protected ?int $onceCallMode = null;

    protected bool $isClone = true;

    /**
     * @param  T  $target
     * @param  int  $callMode
     * @param  bool  $isClone
     */
    public function __construct($target, int $callMode = self::CALL_MODE_MIXED, bool $isClone = true)
    {
        $this->proxyValue = $target;
        $this->cloneValue = $this->tryCloneValue($target, $isClone);
        $this->isClone = $isClone;
        $this->callMode = $callMode;
    }

    /**
     * @param  T  $target
     * @param  bool  $isClone
     * @return T
     */
    protected function tryCloneValue(mixed $target, bool $isClone)
    {
        if ($isClone && is_object($target) && (! (method_exists($target, '__clone') && (! is_callable([$target, '__clone']))))) {
            return clone $target;
        }

        return $target;
    }

    /**
     * @return T
     */
    public function popValue(): mixed
    {
        return $this->proxyValue;
    }

    /**
     * @return T
     */
    public function popCloneValue(): mixed
    {
        return $this->cloneValue;
    }

    /**
     * @return self<T>
     */
    public function switchMixedMode(): self
    {
        return $this->setCallMode(self::CALL_MODE_MIXED);
    }

    /**
     * @return self<T>
     */
    public function switchTapMode(): self
    {
        return $this->setCallMode(self::CALL_MODE_TAP);
    }

    /**
     * @return self<T>
     */
    public function switchPipeMode(): self
    {
        return $this->setCallMode(self::CALL_MODE_PIPE);
    }

    /**
     * @return self<T>
     */
    public function tapOnce(): self
    {
        return $this->setOnceCallMode(self::CALL_MODE_TAP);
    }

    /**
     * @return self<T>
     */
    public function pipeOnce(): self
    {
        return $this->setOnceCallMode(self::CALL_MODE_PIPE);
    }

    /**
     * @param  int  $mode
     * @return self<T>
     */
    protected function setCallMode(int $mode): self
    {
        $this->callMode = $mode;

        return $this;
    }

    /**
     * @param ?int  $mode
     * @return self<T>
     */
    protected function setOnceCallMode(?int $mode): self
    {
        $this->onceCallMode = $mode;

        return $this;
    }

    /**
     * @param  \Closure  $closure
     * @return self<T>
     */
    public function after(\Closure $closure): self
    {
        $closure($this->proxyValue, $this->cloneValue, $this);

        return $this;
    }

    /**
     * @param  string  $name
     * @param  mixed  $value
     * @return self<T>
     */
    public function pick(string $name, mixed &$value): self
    {
        $value = $this->proxyValue->{$name};

        return $this;
    }

    /**
     * @param  mixed  $value
     * @param  string  $method
     * @param  array<mixed>  $parameters
     * @return self<T>
     */
    public function methodPick(mixed &$value, string $method, ...$parameters): self
    {
        $value = $this->proxyValue->{$method}(...$parameters);

        return $this;
    }

    /**
     * @param  string  $key
     * @return self<T>
     */
    public function __get(string $key): self
    {
        if (in_array($key, ['tap', 'pipe', 'mixed'])) {
            $key = "switch{$key}Mode";

            return $this->{$key}();
        }

        if (in_array($key, ['tapOnce', 'pipeOnce'])) {
            return $this->{$key}();
        }

        $this->proxyValue = is_array($this->proxyValue) ? $this->proxyValue[$key] : $this->proxyValue->{$key};

        return $this;
    }

    /**
     * @param  string  $method
     * @param  array<mixed>  $parameters
     * @return self<T>
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
