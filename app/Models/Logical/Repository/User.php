<?php

namespace App\Models\Logical\Repository;

trait User
{
    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $this->sanitize($email);

        return $this;
    }

    /**
     * @return string
     */
    final public function getRole()
    {
        return $this->role;
    }

    /**
     * @return $this
     */
    final public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return in_array(self::ROLE_ADMIN, $this->getRoles());
    }
}
