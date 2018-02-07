<?php

namespace App\Models\Physical\DAL;

use Illuminate\Support\Facades\DB;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Builder;

abstract class AbstractMongoDb extends Model
{
    const ORDER_DIR_DESC = 'desc';
    const ORDER_DIR_ASC = 'asc';

    use Common;

    /**
     * @return void
    */
    final public static function orderByCreatedAt(Builder $query, $dir)
    {
        self::checkOrderDir($dir);

        $query->orderBy(self::CREATED_AT, $dir);
    }

    /**
     * @return void
     */
    final public static function orderByUpdatedAt(Builder $query, $dir)
    {
        self::checkOrderDir($dir);

        $query->orderBy(self::UPDATED_AT, $dir);
    }

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
     * @return array
     */
    public static function getPaged($pageNum, $perPage, callable $orderCb = null, callable $filterCb = null)
    {
        $query = static::query();

        $itemsTotalCount = static::getTotalCount();
        $pagesTotalCount = ceil($itemsTotalCount / $perPage);

        $offset = intval(($pageNum - 1) * $perPage);

        if ($offset) {
            $query->skip($offset);
        }

        // Order items in ascending or descending direction
        if ($orderCb) {
            $orderCb($query);
        }

        $query->take(intval($perPage));

        $items = $query->get();

        // Filter out the selected items
        if ($filterCb) {
            $items = $filterCb($items);
        }

        return [
            'items' => $items,
            'items.count' => $itemsTotalCount,
            'pages.count' => $pagesTotalCount,
            'pages.current' => $pageNum
        ];
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
