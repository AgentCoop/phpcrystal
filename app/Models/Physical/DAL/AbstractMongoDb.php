<?php

namespace App\Models\Physical\DAL;

use App\Models\Physical\DAL\Common;
use Carbon\Carbon;
use Jenssegers\Mongodb\Eloquent\Model;

abstract class AbstractMongoDb extends Model
{
    use Common;

    /**
     * Return total count of documents in the collection
     *
     * @return integer
     */
    final public static function getTotalCount()
    {
        $count = DB::collection((new static())->collection)->raw(function($collection) {
            return $collection->count();
        });

        return $count;
    }

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
     * @return array|false
    */
    private function prepareMultiValued($mixed)
    {
        if (is_string($mixed)) {
            return $this->sanitizeArray($this->parseCommaSeparatedValues($mixed));
        } else if (is_array($mixed)) {
            return $this->sanitizeArray($mixed);
        } else if (is_null($mixed)) {
            return [];
        } else {
            return false;
        }
    }

    /**
     * @return $this
     */
    protected function validateAndSet($attrName, $mixed, array $allowedValues)
    {
        $attrValues = $this->prepareMultiValued($mixed);

        if ($attrValues === false) {
            throw new \RuntimeException(sprintf('Wrong data type %s [%s:%s]',
                gettype($mixed), static::class, $attrName));
        }

        foreach ($attrValues as $attrValue) {
            if ( ! in_array($attrValue, $allowedValues, true)) {
                throw new \UnexpectedValueException(sprintf('Attribute value "%s" is not allowed [%s:%s]',
                    $attrValue, static::class, $attrName));
            }
        }

        $this->{$attrName} = $attrValues;

        return $this;
    }
}
