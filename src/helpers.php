<?php

if (! function_exists('parse_value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @param  mixed  ...$args
     * @return mixed
     */
    function parse_value(mixed $value, ...$args)
    {
        return $value instanceof Closure ? $value(...$args) : $value;
    }
}