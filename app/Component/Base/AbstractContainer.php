<?php
namespace App\Component\Base;

const ITEM_OPERATION_ADD = 1;
const ITEM_OPERATION_REMOVE = 2;
const ITEM_OPERATION_NEW_VALUE = 3;

abstract class AbstractContainer
{
    /** @var string */
    protected $keyPrefix = '';

    /** @var array */
    protected $items = [];

    protected $changesTracker = [];

    protected $nestedContainers = array();

    /**
     * @var boolean
     */
    protected $allowOverride = true;

    /** @var string */
    protected $filename;

    /**
     * @return $this
     */
    public static function createFromFile($filename)
    {
        return (new static())->loadFromFile($filename);
    }

    /**
     * Wrapper for the object constructor
     *
     * @return $this
     */
    public static function createFromArray(array $items)
    {
        $container = new static($items);

        return $container;
    }

    /**
     *
     */
    final public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     *
    */
    final public function getKeyPrefix() : string
    {
        return $this->keyPrefix;
    }

    /**
     *
    */
    final public function setKeyPrefix($prefix) : self
    {
        $this->keyPrefix = $prefix;

        return $this;
    }

    /**
     * @return void
    */
    public function reload()
    {
        $this->loadFromFile($this->filename);
    }

    /**
     * @return $this
     */
    public function loadFromFile($filename)
    {
        $this->filename = $filename;

        require($filename);

        return $this;
    }

    /**
     * @return string
     */
    private function normalizeKey($key)
    {
        return empty($this->keyPrefix) ? $key : ( $this->keyPrefix . '.' . $key );
    }

    /**
     * Converts object to a string if it supports ::toString method
     *
     * @return mixed
     */
    protected function expandItemValue($value)
    {
        if (is_object($value) && method_exists($value, 'toString')) {
            return $value->toString();
        } else if (is_array($value)) {
            foreach ($value as $arrKey => $arrValue) {
                $value[$arrKey] = $this->expandItemValue($arrValue);
            }
        }

        return $value;
    }

    /**
     * @return void
     */
    private function getAllKeysHelper($keyPrefix, $arr, &$result)
    {
        foreach ($arr as $key => $value) {
            $itemKey = empty($keyPrefix) ? $key : ($keyPrefix . '.' . $key);
            if (is_array($value)) {
                $this->getAllKeysHelper($itemKey, $value, $result);
            } else {
                $result[] = $itemKey;
            }
        }
    }

    /**
     * @return array
     */
    final public function getItems()
    {
        return $this->items;
    }

    /**
     * @return $this
     */
    final public function setItems(array $items)
    {
        $this->flush();
        $this->items = $items;

        return $this;
    }

    /**
     * @return mixed
     */
    public function get($itemKey, $defaultValue = null, $autoExpand = true)
    {
        $parts = explode('.', $this->normalizeKey($itemKey));
        $arrRef = &$this->items;

        while (count($parts) > 1) {
            $segment = array_shift($parts);
            if ( ! array_key_exists($segment, $arrRef) ||
                ! is_array($arrRef[$segment]))
            {
                if ($defaultValue) {
                    $this->set($itemKey, $defaultValue);
                    return $defaultValue;
                } else {
                    return null;
                }
            } else {
                $arrRef = &$arrRef[$segment];
            }
        }

        $lastKey = end($parts);
        if ( ! isset($arrRef[$lastKey])) {
            return null;
        }

        $item = $arrRef[$lastKey];

        return $autoExpand ? $this->expandItemValue($item) : $item;
    }

    /**
     * Set an item to a given value using dot notation
     *
     * @return $this
     */
    public function set($itemKey, $value)
    {
        $parts = explode('.', $this->normalizeKey($itemKey));
        $arrRef = &$this->items;

        while (count($parts) > 1) {
            $segment = array_shift($parts);
            if ( ! array_key_exists($segment, $arrRef) ||
                ! is_array($arrRef[$segment]))
            {
                $arrRef[$segment] = array();
            }
            $arrRef = &$arrRef[$segment];
        }

        $lastKey = end($parts);

        if ( ! array_key_exists($lastKey, $arrRef)) {
            $this->changesTracker[$itemKey] = ITEM_OPERATION_ADD;
        } else {
            $this->changesTracker[$itemKey] = ITEM_OPERATION_NEW_VALUE;
        }

        $arrRef[$lastKey] = $value;

        // if value being set is an object return it so that its method
        // chaining may be achieved
        if (is_object($value)) {
            return $value;
        }
    }

    /**
     * Returns true if item with the given key exists
     *
     * @return boolean
     */
    final public function has($itemKey)
    {
        $parts = explode('.', $this->normalizeKey($itemKey));
        $arrRef = &$this->items;

        while (count($parts) > 1) {
            $segment = array_shift($parts);
            if ( ! array_key_exists($segment, $arrRef)) {
                return false;
            } else {
                $arrRef = &$arrRef[$segment];
            }
        }

        $lastKey = end($parts);
        return array_key_exists($lastKey, $arrRef);
    }

    /**
     * Asserts that item value is set to true
     *
     * @return bool
     */
    final public function assertTrue($itemKey)
    {
        return $this->get($itemKey) === true;
    }

    /**
     * Asserts that item value is set to false
     *
     * @return bool
     */
    final public function assertFalse($itemKey)
    {
        return $this->get($itemKey) === false;
    }

    /**
     * @return bool
     */
    final public function hasChanges()
    {
        return count($this->changesTracker) > 0;
    }

    /**
     * @return void
     */
    final public function flush()
    {
        $this->changesTracker = [];
        $this->items = [];
    }

    /**
     * @return boolean
     */
    final public function isObject($key)
    {
        $value = $this->get($key, null, false);

        return is_object($value) ? true : false;
    }

    /**
     * @return void
     */
    final public function addItems($itemsArray)
    {
        $this->items = array_merge($this>items, $this->convertArray($itemsArray));
    }

    /**
     * @return array
     */
    private function toArrayHelper($itemsArray)
    {
        $result = array();

        foreach ($itemsArray as $name => $value) {
            if (is_array($value)) {
                $result[$name] = $this->toArrayHelper($value);
            } else {
                $result[$name] = $value;
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->toArrayHelper($this->items);
    }

    /**
     * @return integer
     */
    final public function getCount()
    {
        return count($this->items);
    }

    /**
     * @return boolean
     */
    final public function isEmpty()
    {
        return $this->getCount() == 0 ? true : false;
    }

    /**
     * @return array
     */
    final public function getAllKeys()
    {
        $result = array();

        $this->getAllKeysHelper('', $this->items, $result);

        return $result;
    }

    /**
     * @return $this
     */
    public function merge($container, $prefix = null)
    {
        if (null == $container) {
            return $this;
        }

        foreach ($container->getAllKeys() as $itemKey) {
            if ($prefix != null) {
                $itemKey = $prefix . '.' . $itemKey;
            }
            $this->set($itemKey, $container->get($itemKey));
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function pluck($key, $throwExcepIfNull = false)
    {
        $pluckedItem = $this->get($key);

        if (null === $pluckedItem) {
            if ($throwExcepIfNull) {
                System\MethodInvocation::create('AbstractContainer::pluck invocation failed for key `%s`', null, $key)
                    ->addParam($key)
                    ->_throw();
            } else {
                return new static();
            }
        } elseif (is_array($pluckedItem)) {
            return static::createFromArray($pluckedItem);
        } else {
            return $pluckedItem;
        }
    }
}
