<?php

namespace App\Services;

use App\Component\Mvc\Controller\AbstractService;
use Illuminate\Support\Facades\Config;

use App\Models\Physical\Support\Logging\MongoDB\ErrorEntry as ErrorEntryMongoDb;
use App\Models\Physical\Support\Logging\MySQL\ErrorEntry as ErrorEntryMySql;

class Factory
{
    const MYSQL_CONN_NAME = 'mysql';
    const MONGODB_CONN_NAME = 'mongodb';

    /**
     *
    */
    public static function getNestedDependencies($className, $methodName)
    {
        $parentDeps = self::getMethodInjectedServices($className, $methodName);

        foreach ($parentDeps as &$depItem) {
            $depClassName = $depItem['className'];

            if (is_subclass_of($depClassName, AbstractService::class)) {
                $depItem['child'] = self::getMethodInjectedServices($depClassName, $methodName);
            }
        }
    }

    /**
     * @return array
     */
    public static function getMethodInjectedServices($className, $methodName)
    {
        $result = [];
        $refMethod = new \ReflectionMethod($className, $methodName);
        $refParams = $refMethod->getParameters();

        foreach ($refParams as $param) {
            $typeHinted = $param->getClass();

            if ( ! $typeHinted->isInstantiable()) {
                throw new \RuntimeException();
            }

            $result[] = ['className' => $typeHinted->name, 'is_interface' => $typeHinted->isInterface()];
        }

        return $result;
    }

    /**
     * @return string
     */
    final protected static function getDefaultConnName()
    {
        return Config::get('database.default');
    }

    /**
     * @return MySqlErrorEntry|MongoDbErrorEntry
     */
    final public static function logEntryFactory()
    {
        $conn = static::getDefaultConnName();

        switch ($conn) {
            case self::MYSQL_CONN_NAME:
                return new ErrorEntryMySql();

            case self::MONGODB_CONN_NAME:
                return new ErrorEntryMongoDb();

            default:
                throw new \RuntimeException(sprintf('Unsupported database %s', $conn));
        }
    }
}
