<?php

namespace Liyuze\MethodChainingProxy\Proxies;

use Liyuze\MethodChainingProxy\Traits\HasMethodChaining;

/**
 * @template T
 * @mixin T
 * @property-read  MethodChainingProxy<T>|T tap
 * @property-read  MethodChainingProxy<T>|T pipe
 * @property-read  MethodChainingProxy<T>|T mixed
 * @property-read  MethodChainingProxy<T>|T tapOnce
 * @property-read  MethodChainingProxy<T>|T pipeOnce
 */
class MethodChainingProxy
{
    use HasMethodChaining;

    const CALL_MODE_PIPE = 1;
    const CALL_MODE_TAP = 2;
    const CALL_MODE_MIXED = 3;

    /**
     * @var T
     */
    protected $proxyValue;

    /**
     * @var T
     */
    protected $cloneValue;

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
    protected function tryCloneValue($target, bool $isClone)
    {
        if ($isClone && is_object($target) && (! (method_exists($target, '__clone') && (! is_callable([$target, '__clone']))))) {
            return clone $target;
        }

        return $target;
    }

    /**
     * @return T
     */
    public function popValue()
    {
        return $this->proxyValue;
    }

    /**
     * @return T
     */
    public function popCloneValue()
    {
        return $this->cloneValue;
    }


    /**
     * @return self<T>|T
     */
    public function tap()
    {
        return $this->setCallMode(self::CALL_MODE_TAP);
    }

    /**
     * @param  int  $mode
     * @return self<T>|T
     */
    protected function setCallMode(int $mode)
    {
        $this->callMode = $mode;

        return $this;
    }

    /**
     * @return self<T>|T
     */
    public function pipe()
    {
        return $this->setCallMode(self::CALL_MODE_PIPE);
    }

    /**
     * @return self<T>|T
     */
    public function mixed()
    {
        return $this->setCallMode(self::CALL_MODE_MIXED);
    }

    /**
     * @return self<T>|T
     */
    public function tapOnce()
    {
        return $this->setOnceCallMode(self::CALL_MODE_TAP);
    }

    /**
     * @param ?int  $mode
     * @return self<T>|T
     */
    protected function setOnceCallMode(?int $mode)
    {
        $this->onceCallMode = $mode;

        return $this;
    }

    /**
     * @return self<T>|T
     */
    public function pipeOnce()
    {
        return $this->setOnceCallMode(self::CALL_MODE_PIPE);
    }

    /**
     * @param  \Closure  $closure
     * @return self<T>|T
     */
    public function after(\Closure $closure)
    {
        $closure($this->proxyValue, $this->cloneValue, $this);

        return $this;
    }

    /**
     * @param  string  $name
     * @param  mixed  $value
     * @return self<T>|T
     */
    public function pick(string $name, &$value)
    {
        $value = $this->proxyValue->{$name};

        return $this;
    }

    /**
     * @param  mixed  $value
     * @param  string  $method
     * @param  array  $parameters
     * @return self<T>|T
     */
    public function methodPick(&$value, string $method, ...$parameters)
    {
        $value = $this->proxyValue->{$method}(...$parameters);

        return $this;
    }

    /**
     * @param  string  $key
     * @return self<T>|T
     */
    public function __get(string $key)
    {
        if (in_array($key, ['tap', 'tapOnce', 'pipe', 'pipeOnce', 'mixed'])) {
            return $this->{$key}();
        }

        $this->proxyValue = is_array($this->proxyValue) ? $this->proxyValue[$key] : $this->proxyValue->{$key};

        return $this;
    }

    /**
     * @param  string  $method
     * @param  array  $parameters
     * @return self<T>|T
     */
    public function __call(string $method, array $parameters)
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
