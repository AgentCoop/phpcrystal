<?php
namespace App\Services\View;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

abstract class AbstractView
{
    /** @var mixed $ds A datasource for the view component */
    protected $ds;

    /**
     * @return static
     */
    final public static function create($dataSource = null, ...$rest)
    {
        return new static($dataSource, ...$rest);
    }

    /**
     *
    */
    public function __construct($dataSource, ...$rest)
    {
        $this->ds = $dataSource;
    }

    /**
     * @return string
     */
    final protected static function getCacheKey($keyExtraParams = [])
    {
        $key = 'view_' . static::class;

        if (empty($keyExtraParams)) {
            return $key;
        }

        $key .= '_';

        foreach ($keyExtraParams as $paramName => $paramVal) {
            $key .= $paramName . '_' . $paramVal;
        }

        return $key;
    }

    /**
     * @return array
     */
    final public function storeDataInCache($keyExtraParams = [], $expireInMins = 60)
    {
        $data = $this->getData();

        $expiresAt = Carbon::now()
            ->addMinutes($expireInMins);

        Cache::put(self::getCacheKey($keyExtraParams), serialize($data), $expiresAt);

        return $data;
    }

    /**
     * @return array
     */
    public function getDataCached($keyExtraParams = [])
    {
        if (app()->environment('local')) {
            return $this->getData();
        }

        $key = self::getCacheKey($keyExtraParams);
        $refresh = request()->attributes->has('refresh_cache');

        if (Cache::has($key) && ! $refresh) {
            return @unserialize(Cache::get($key));
        } else {
            return $this->storeDataInCache($keyExtraParams);
        }
    }

    /**
     * @param $items Traversable
     * @param $nColumns integer Number of columns
     *
     * @return array
     */
    final protected function splitInColumns(\Traversable $items, $nColumns)
    {
        $columns = [];

        $perColumn = ceil(count($items) / $nColumns);

        for ($i = 0; $i < $nColumns; $i++) {
            $columns[] = array_slice($items, $i * $perColumn, $perColumn , true);
        }

        return $columns;
    }

    /**
     * @return string
     */
    private function phpEval($__php, $__data = [])
    {
        $obLevel = ob_get_level();
        ob_start();
        extract($__data, EXTR_SKIP);

        try {
            eval('?' . '>' . $__php);
        } catch (Exception $e) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }

            throw $e;
        } catch (Throwable $e) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }

            throw new FatalThrowableError($e);
        }

        return ob_get_clean();
    }

    /**
     * @return string
     */
    protected function renderBladeMarkup($bladeMarkup)
    {
        return $this->phpEval(Blade::compileString($bladeMarkup));
    }
}
