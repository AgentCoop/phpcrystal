<?php

namespace App\Component\Package\Annotation;

/**
 * Annotation class for @SecurityPolicy().
 *
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 * @Attributes({
 *   @Attribute("roles", type = "array"),
 *   @Attribute("permissions", type = "array"),
 *   @Attribute("mode", type = "string")
 * })
 */
class SecurityPolicy extends AbstractBase
{
    private $roles;

    /**
     * @return string
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @return $this
     */
    public function setRoles($roles) : self
    {
        $this->roles = (array)$roles;

        return $this;
    }

    public function merge(SecurityPolicy $input)
    {
        switch ($input->getMode()) {
            case self::MODE_OVERWRITE:
                $this->setRoles($input->getRoles());
                break;

            case self::MODE_MERGE:
                $this->setRoles($this->mergeArrays($this->getRoles(), $input->getRoles()));
                break;
        }
    }
}
