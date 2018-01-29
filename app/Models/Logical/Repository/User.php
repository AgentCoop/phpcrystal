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
}
