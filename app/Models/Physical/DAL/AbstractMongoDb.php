<?php

namespace App\Models\Physical\DAL;

use App\Models\Physical\DAL\Common;
use Carbon\Carbon;
use Jenssegers\Mongodb\Eloquent\Model;

abstract class AbstractMongoDb extends Model
{
    use Common;

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
     * @return $this
     */
    protected function validateAndSet($attrName, $attrValues, array $allowedValues)
    {
        $attrValues = (array)$attrValues;

        foreach ($attrValues as $attrValue) {
            if ( ! in_array($attrValue, $allowedValues, true)) {
                throw new \UnexpectedValueException(sprintf('The value "%s" for the attribute "%s:%s" is not a valid one!',
                    $attrValue, static::class, $attrName));
            }
        }

        $this->{$attrName} = $attrValues;

        return $this;
    }
}
