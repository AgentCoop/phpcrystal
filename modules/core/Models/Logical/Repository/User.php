<?php

namespace PhpCrystal\Core\Models\Logical\Repository;

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
     * @return array
    */
    public function getRoles()
    {
        return (array)$this->roles;
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return in_array(self::ROLE_ADMIN, $this->getRoles());
    }
}
