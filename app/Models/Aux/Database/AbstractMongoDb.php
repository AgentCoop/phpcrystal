<?php

namespace App\Models\Aux\Database;

use Carbon\Carbon;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

abstract class AbstractMongoDb extends Eloquent
{
    /**
     * @return $this
     */
    final public static function findOrNew($attrs)
    {
        $result = static::query()
            ->where($attrs)
            ->first();

        return $result ? $result : new static();
    }

    /**
     *
     */
    public static function createDefaultQuery()
    {
        return static::query()
            ->orderBy(self::CREATED_AT, 'desc');
    }

    /**
     * @return string
     */
    final public function getId()
    {
        return $this->getAttribute('_id');
    }

    /**
     * @return \Carbon\Carbon|null
     */
    final public function getCreatedAt()
    {
        return $this->{self::CREATED_AT};
    }

    /**
     * @return $this
     */
    final public function setCreatedAt($datetime)
    {
        $this->{self::CREATED_AT} = is_string($datetime) ? Carbon::parse($datetime) : $datetime;

        return $this;
    }

    /**
     * @return \Carbon\Carbon|null
     */
    final public function getUpdatedAt()
    {
        return $this->{self::UPDATED_AT};
    }

    /**
     * @return $this
     */
    final public function setUpdatedAt($datetime)
    {
        $this->{self::UPDATED_AT} = is_string($datetime) ? Carbon::parse($datetime) : $datetime;

        return $this;
    }

    /**
     * @return $this
     */
    protected function addMultiValue($attrName, $value, $unique = true)
    {
        if (empty($value)) {
            return $this;
        }

        $multiValues = (array) $this->{$attrName};
        $multiValues[] = $value;

        if ($unique) {
            $multiValues = array_unique($multiValues);
        }

        $this->{$attrName} = $multiValues;

        return $this;
    }

    /**
     * @return $this
     */
    protected function setArrayValues($attrName, $mixed, $toLowerCase = false)
    {
        if (is_array($mixed)) {
            $rawValues = $mixed;
        } else {
            $rawValues = explode(',', $mixed);
        }

        $values = array_map(function($value) use ($toLowerCase) {
            $result = self::sanitize($value);

            return $toLowerCase ? strtolower($result) : $result;
        }, $rawValues);

        $this->{$attrName} = $values;

        return $this;
    }

    /**
     * @return array
     */
    protected function parseCommandSeparatedValues($strValues)
    {
        if (empty($strValues)) {
            return [];
        }

        $values = array_map(function($value)  {
            return trim($value);
        }, explode(',', $strValues));

        return $values;
    }

    /**
     * @return $this
     */
    protected function assertAndSet($attrName, $attrValues, array $allowedValues)
    {
        $attrValues = (array)$attrValues;

        foreach ($attrValues as $attrValue) {
            if ( ! in_array($attrValue, $allowedValues)) {
                throw new \RuntimeException(sprintf('Not allowed value "%s" for the attribute %s:%s',
                    $attrValue, static::class, $attrName));
            }
        }

        $this->{$attrName} = $attrValues;

        return $this;
    }

    /**
     * @return bool
     */
    protected function strToBool($str)
    {
        switch (strtolower($str)) {
            case '1':
            case 'on':
            case 'true':
                return true;

            default:
                return false;
        }
    }

    /**
     * @return string
     */
    protected static function sanitize($value)
    {
        return trim(strip_tags($value));
    }

    /**
     * @return array
     */
    protected static function sanitizeArray(array $arrData)
    {
        $result = [];

        foreach ($arrData as $item) {
            $result[] = self::sanitize($item);
        }

        return $result;
    }
}
