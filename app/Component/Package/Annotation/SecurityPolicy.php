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
 *   @Attribute("not_authenticated_page", type = "string"),
 *   @Attribute("not_authorized_page", type = "string"),
 *   @Attribute("mode", type = "string")
 * })
 */
class SecurityPolicy extends AbstractBase
{
    private $roles;
    private $permissions = [];
    private $not_authenticated_page;
    private $not_authorized_page;

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

    /**
     * @return array
     */
    public function getPermissions() : array
    {
        return $this->permissions;
    }

    /**
     * @return $this
     */
    public function setPermissions($perms) : self
    {
        $this->roles = (array)$perms;

        return $this;
    }

    /**
     * @return string
     */
    public function getNotAuthenticatedPage() : ?string
    {
        return $this->not_authenticated_page;
    }

    /**
     * @return $this
     */
    public function setNotAuthenticatedPage($route)
    {
        $this->not_authenticated_page = $route;

        return $this;
    }

    /**
     * @return string
     */
    public function getNotAuthorizedPage() : ?string
    {
        return $this->not_authorized_page;
    }

    /**
     * @return $this
     */
    public function setNotAuthorizedPage($route)
    {
        $this->not_authorized_page = $route;

        return $this;
    }

    public function merge(SecurityPolicy $input)
    {
        switch ($input->getMode()) {
            case self::MODE_OVERWRITE:
                $this
                    ->setRoles($input->getRoles())
                    ->setPermissions($input->getPermissions())
                    ->setNotAuthenticatedPage($input->getNotAuthenticatedPage())
                    ->setNotAuthorizedPage($input->getNotAuthorizedPage());
                break;

            case self::MODE_MERGE:
                $this
                    ->setRoles($this->mergeArrays($this->getRoles(), $input->getRoles()))
                    ->setPermissions($this->mergeArrays($this->getPermissions(), $input->getPermissions()))
                ;

                if (is_null($this->permissions)) {
                    $this->setPermissions($input->getPermissions());
                }

                if (is_null($this->not_authenticated_page)) {
                    $this->setNotAuthenticatedPage($input->getNotAuthenticatedPage());
                }

                if (is_null($this->not_authorized_page)) {
                    $this->setNotAuthorizedPage($input->getNotAuthorizedPage());
                }

                break;
        }
    }
}
