<?php

namespace Liyuze\MethodChainingProxy\Tests\Stubs;

class Cat
{
    /**
     * @var string|null
     */
    public ?string $name;
    /**
     * @var int|null
     */
    public ?int $age;

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
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  string  $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param  int  $age
     */
    public function setAge(int $age)
    {
        $this->age = $age;
    }

    /**
     * @param  \Closure  $closure
     * @return $this
     */
    public function do(\Closure $closure)
    {
        $closure();

        return $this;
    }
}