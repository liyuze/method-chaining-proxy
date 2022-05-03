<?php

namespace Liyuze\MethodChainingProxy\Tests\Stubs;

class Cat
{
    /**
     * @var string
     */
    public string $name;
    /**
     * @var int
     */
    public int $age;

    /**
     * @param  string  $name
     * @param  int  $age
     */
    public function __construct(string $name, int $age)
    {
        $this->name = $name;
        $this->age = $age;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param  string  $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int
     */
    public function getAge(): int
    {
        return $this->age;
    }

    /**
     * @param  int  $age
     */
    public function setAge(int $age): void
    {
        $this->age = $age;
    }

    /**
     * @param  \Closure  $closure
     * @return $this
     */
    public function do(\Closure $closure): self
    {
        $closure();

        return $this;
    }
}