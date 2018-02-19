<?php

namespace PhpCrystal\Core\Services\Package\Annotation;

/**
 * Annotation class for @Middleware().
 *
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class Middleware
{
    private $className;
    private $group;
    private $name;

    /**
     * @param array $data An array of key/value parameters
     *
     * @throws \BadMethodCallException
     */
    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            $method = 'set'.str_replace('_', '', $key);

            if (!method_exists($this, $method)) {
                throw new \BadMethodCallException(sprintf('Unknown property "%s" on annotation "%s".', $key, get_class($this)));
            }

            $this->$method($value);
        }
    }

    /**
     * @return string
    */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @return void
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return void
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }

    /**
     * @return string
    */
    public function getCommonName()
    {
        if ( ! empty($this->name)) {
            return $this->name;
        } else if ( ! empty($this->group)) {
            return $this->group;
        } else if ( ! empty($this->className)) {
            return $this->className;
        } else {
            throw new \RuntimeException(sprintf('Empty attribute value for middleware annotation'));
        }
    }
}
