<?php

namespace Liyuze\MethodChainingProxy\Tests\Stubs;

class Incr
{
    /**
     * @var int
     */
    protected int $value = 0;

    /**
     * @param  int  $value
     */
    public function __construct(int $value)
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * @param  int  $value
     * @return void
     */
    public function incr(int $value = 1)
    {
        $this->value += $value;
    }

    /**
     * @param  int  $value
     * @return void
     */
    public function decr(int $value = 1)
    {
        $this->value -= $value;
    }

    /**
     * @param  int  $value
     * @return $this
     */
    public function incrPipe(int $value = 1)
    {
        $this->value += $value;

        return $this;
    }

    /**
     * @param  int  $value
     * @return $this
     */
    public function decrPipe(int $value = 1)
    {
        $this->value -= $value;

        return $this;
    }
}