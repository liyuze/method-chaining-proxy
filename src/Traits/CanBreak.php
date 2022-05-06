<?php

namespace Liyuze\MethodChainingProxy\Traits;

use Liyuze\MethodChainingProxy\Proxies\MethodChainingProxy;

trait CanBreak
{
    protected bool $isBreaking = false;

    abstract public function popValue(): mixed;

    /**
     * @param  int  $level
     * @return self
     */
    public function breakChaining(int $level = 1): self
    {
        if ($level > 0) {
            $this->isBreaking = true;
            $level--;
            if ($level > 0) {
                $this->transmitBreak($level);
            }
        }

        return $this;
    }

    /**
     * @param  int  $level
     * @return void
     */
    public function transmitBreak(int $level): void
    {
        $parent = $this->popValue();
        while (true) {
            if (! ($parent instanceof MethodChainingProxy)) {
                break;
            }

            if (method_exists($parent, 'breakChaining')) {
                $parent->breakChaining($level);

                break;
            }

            $parent = $parent->popValue();
        }
    }
}