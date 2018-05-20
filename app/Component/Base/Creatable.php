<?php

namespace App\Component\Base;

trait Creatable
{
    /**
     * @return static
     */
    public static function create(...$args)
    {
        return new static(...$args);
    }
}