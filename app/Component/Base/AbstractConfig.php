<?php

namespace App\Component\Base;

abstract class AbstractConfig extends AbstractContainer
{
    final public function section($name) : void
    {
        $this->setKeyPrefix($name);
    }

    final public function close() : void
    {
        $this->setKeyPrefix(null);
    }
}
