<?php

namespace App\Component\Package\Annotation;

abstract class AbstractBase
{
    const MODE_MERGE = 'merge';
    const MODE_OVERWRITE = 'overwrite';

    /** @var string */
    private $value;

    private $disabled = false;

    protected $mode;


    protected function mergeArrays(array $a, array $b)
    {
        return array_unique(array_merge($a, $b));
    }

    /**
     * @param array $data An array of key/value parameters
     *
     * @throws \RuntimeException
     */
    public function __construct(array $data, $mode = self::MODE_MERGE)
    {
        $this->mode = $mode;

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

    public function merge($descendant)
    {
        $this->disabled = $descendant->getDisabled();
    }

    /**
     * @return mixed
    */
    final public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getMode() : string
    {
        return $this->mode;
    }

    public function setMode($mode) : void
    {
        $this->mode = $mode;
    }

    /**
     * @return bool
     */
    public function getDisabled()
    {
        return $this->disabled;
    }

    /**
     * @return $this
     */
    public function setDisabled($value)
    {
        $this->disabled = boolval($value);

        return $this;
    }
}