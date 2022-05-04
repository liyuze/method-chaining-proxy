<?php

namespace Liyuze\MethodChainingProxy\Tests\Stubs;

class Dog
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
     * @return void
     */
    public function setName(string $name)
    {
        $this->name = $name;
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
     * @return void
     */
    public function setAge(int $age)
    {
        $this->age = $age;
    }
}