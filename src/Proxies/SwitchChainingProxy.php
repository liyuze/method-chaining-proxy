<?php

namespace Liyuze\MethodChainingProxy\Proxies;

use Liyuze\MethodChainingProxy\Traits\CanBreak;

/**
 * @template T
 * @mixin T
 * @template-extends MethodChainingProxy<T>
 * @property-read SwitchChainingProxy<T>|T $breakChaining
 */
class SwitchChainingProxy extends MethodChainingProxy
{
    use CanBreak {
        breakChaining as _breakChaining;
    }

    /**
     * @var mixed
     */
    protected mixed $switchValue;

    /**
     * @var bool
     */
    protected bool $isStrict;

    /**
     * @var bool
     */
    protected bool $isMatching;

    /**
     * @param  T  $value
     * @param  mixed  $switchValue
     * @param  bool  $isStrict
     * @param  int  $callMode
     */
    public function __construct($value, mixed $switchValue, bool $isStrict = false, int $callMode = self::CALL_MODE_MIXED)
    {
        $this->switchValue = parse_value($switchValue);
        $this->isStrict = $isStrict;
        parent::__construct($value, $callMode);
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function caseChaining(mixed $value): self
    {
        $this->isMatching = (! $this->isBreaking) && ($this->isStrict ? $this->switchValue === $value : $this->switchValue == $value);

        return $this;
    }

    /**
     * @param  int  $level
     * @return $this
     */
    public function breakChaining(int $level = 1): self
    {
        if ($this->isMatching) {
            $this->_breakChaining($level);
        }

        return $this;
    }

    /**
     * @return T
     */
    public function endSwitchChaining(): mixed
    {
        return $this->popValue();
    }

    /**
     * @param  string  $key
     * @return $this
     */
    public function __get(string $key): self
    {
        if ($key == 'breakChaining') {
            return $this->breakChaining();
        }

        if ($this->isMatching) {
            return parent::__get($key);
        }

        return $this;
    }

    /**
     * @param  string  $method
     * @param  mixed[]  $parameters
     * @return $this
     */
    public function __call(string $method, array $parameters): self
    {
        if ($this->isMatching) {
            return parent::__call($method, $parameters);
        }

        return $this;
    }
}