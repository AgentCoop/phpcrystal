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
 *   @Attribute("mode", type = "string"),
 *   @Attribute("disabled", type = "bool")
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
        return (array)$this->roles;
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
        return (array)$this->permissions;
    }

    /**
     * @return $this
     */
    public function setPermissions($perms) : self
    {
        $this->permissions = (array)$perms;

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

    public function merge($descendant)
    {
        parent::merge($descendant);

        // If annotation is disabled, do not merge its attributes into the parent one.
        // The resulting annotation will be disabled as well.
        if ($descendant->getDisabled()) {
            return;
        }

        switch ($descendant->getMode()) {
            case self::MODE_OVERWRITE:
                $this
                    ->setRoles($descendant->getRoles())
                    ->setPermissions($descendant->getPermissions())
                    ->setNotAuthenticatedPage($descendant->getNotAuthenticatedPage())
                    ->setNotAuthorizedPage($descendant->getNotAuthorizedPage());
                break;

            case self::MODE_MERGE:
                $this
                    ->setRoles($this->mergeArrays($this->getRoles(), $descendant->getRoles()))
                    ->setPermissions($this->mergeArrays($this->getPermissions(), $descendant->getPermissions()))
                ;

                if (is_null($this->permissions)) {
                    $this->setPermissions($descendant->getPermissions());
                }

                if (is_null($this->not_authenticated_page)) {
                    $this->setNotAuthenticatedPage($descendant->getNotAuthenticatedPage());
                }

                if (is_null($this->not_authorized_page)) {
                    $this->setNotAuthorizedPage($descendant->getNotAuthorizedPage());
                }

                break;
        }
    }
}
