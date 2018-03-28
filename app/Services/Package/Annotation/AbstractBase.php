<?php

namespace App\Services\Package\Annotation;

abstract class AbstractBase
{
    /** @var string */
    private $value;

    /**
     * @param array $data An array of key/value parameters
     *
     * @throws \RuntimeException
     */
    public function __construct(array $data)
    {
        if (isset($data['value'])) {
            $this->value = $data['value'];
            unset($data['value']);
        }

        foreach ($data as $key => $value) {
            $method = 'set'.str_replace('_', '', $key);

            if (!method_exists($this, $method)) {
                throw new \RuntimeException(sprintf('Unknown property "%s" on annotation "%s".', $key, get_class($this)));
            }

            $this->$method($value);
        }
    }

    /**
     * @return mixed
    */
    final public function getValue()
    {
        return $this->value;
    }
}