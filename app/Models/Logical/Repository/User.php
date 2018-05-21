<?php

namespace App\Models\Logical\Repository;

use Illuminate\Database\Eloquent\Collection;

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
    public function getPassword() : string
    {
        return $this->password;
    }

    /**
     * @return $this
     */
    public function setPassword($pass)
    {
        $this->password = $pass;

        return $this;
    }

    /**
     * @return Collection
     */
    final public function getRoles() : Collection
    {
        return $this->roles()->getResults();
    }

    /**
     * @return $this
     */
    final public function addRoles($roles) : self
    {
        $this->roles()->saveMany($roles);

        return $this;
    }
}
