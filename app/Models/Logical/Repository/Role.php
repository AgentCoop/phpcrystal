<?php

namespace App\Models\Logical\Repository;

trait Role
{
    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->{self::COL_NAME};
    }

    /**
     * @return $this
     */
    public function setName($name)
    {
        $this->{self::COL_NAME} = $name;

        return $this;
    }
}
