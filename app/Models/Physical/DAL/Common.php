<?php

namespace App\Models\Physical\DAL;

trait Common
{
    /**
     * @throws \RuntimeException
     * @return void
    */
    final public static function checkOrderDir($dir)
    {
        if ( ! in_array($dir, ['desc', 'asc'])) {
            throw new \RuntimeException(sprintf('Not a valid order direction %s', $dir));
        }
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
     * @return array
     */
    protected function parseCommaSeparatedValues($strValues)
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
    protected function sanitize($value)
    {
        return trim(strip_tags($value));
    }

    /**
     * @return array
     */
    protected function sanitizeArray(array $values)
    {
        $results = [];

        foreach ($values as $value) {
            $results[] = $this->sanitize($value);
        }

        return $results;
    }
}