<?php

namespace App\Models\Logical\Repository;

trait User
{
    /**
     * @return array
     */
    public function getRoles()
    {
        $roles = (array)$this->roles;

        return $roles;
    }

    /**
     * @param mixed $roles Array or comma-separated list of roles
     *
     * @return $this
     */
    public function setRoles($mixed)
    {
        if (is_string($mixed)) {
            $roles = $this->parseCommaSeparatedValues($mixed);
        } else {
            $roles = (array)$mixed;
        }

        return $this->validateAndSet('roles', $roles, [self::ROLE_ADMIN]);
    }
}
