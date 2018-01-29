<?php

namespace App\Exceptions;

use App\Models\Physical\Support\Logging\MongoDB\ErrorEntry as ErrorEntryMongoDb;
use App\Models\Physical\Support\Logging\MySQL\ErrorEntry as ErrorEntryMySql;

use Auth;
use Illuminate\Support\Facades\Config;

/**
 *
 */
class Loggable extends AbstractException
{
    protected $fields = [];
    protected $dataBag = [];

    /**
     * @return MySqlErrorEntry|MongoDbErrorEntry
    */
    final public static function logEntryFactory()
    {
        $conn = Config::get('database.default');

        switch ($conn) {
            case 'mysql':
                return new ErrorEntryMySql();

            case 'mongodb':
                return new ErrorEntryMongoDb();

            default:
                throw new \RuntimeException(sprintf('Unsupported database %s', $conn));
        }
    }

    /**
     * @return $this
     */
    public function attachDataBag(array $data)
    {
        $this->dataBag = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function getDataBag()
    {
        return $this->dataBag;
    }

    /**
     * @return $this
     */
    public function addField($name, $value)
    {
        $this->fields[$name] = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getField($name)
    {
        return $this->fields[$name];
    }

    /**
     * @return static
     */
    final public function save()
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $funcName  = $backtrace[2]['function'];
        $className= $backtrace[2]['class'];

        $logEntry = self::logEntryFactory();
        $logEntry
            ->setType($this->getType())
            ->setFunctionName($funcName)
            ->setClassName($className)
            ->setFields($this->fields)
            ->setCode($this->getCode())
            ->setMessage($this->getMessage())
        ;

        if (php_sapi_name() == "cli") {
            $logEntry
                ->setUserAgent('cli')
            ;
        } else {
            $logEntry
                ->setRequestMethod(@$_SERVER['REQUEST_METHOD'])
                ->setRequestUri(@$_SERVER['REQUEST_URI'])
                ->setRequestQuery(@$_SERVER['QUERY_STRING'])
                ->setRemoteAddr(@$_SERVER['REMOTE_ADDR'])
                ->setUserAgent(@$_SERVER['HTTP_USER_AGENT'])
            ;

            $entityBody = file_get_contents('php://input');

            // Do not save large request entity body
            if ($entityBody > 512) {
                $logEntry->setRequestBody('len:' . strlen($entityBody) . ';body:' . substr($entityBody, 0, 512));
            } else {
                $logEntry->setRequestBody($entityBody);
            }
        }

        $logEntry->save();

        return $this;
    }

    /**
     * @return void
     */
    public function _throw($save = true)
    {
        if ($save) {
            $this->save();
        }

        throw $this;
    }
}
