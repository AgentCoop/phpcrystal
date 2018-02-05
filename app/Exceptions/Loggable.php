<?php

namespace App\Exceptions;

use App\Services\AbstractSupport;
use Auth;

use App\Jobs\Support\ErrorReport;

/**
 *
 */
class Loggable extends AbstractException
{
    protected $fields = [];
    protected $dataBag = [];

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
        $funcName  = @$backtrace[1]['function'];
        $className = @$backtrace[1]['class'];

        $logEntry = AbstractSupport::logEntryFactory();
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
                ->setRequestMimeType(@$_SERVER['CONTENT_TYPE'])
            ;

            $entityBody = file_get_contents('php://input');

            // Do not save large request entity body
            $logEntry->setRequestBody(substr($entityBody, 0, 1024));
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

    /**
     * @return $this
     */
    public function withReport($recipients)
    {
        $recipients = (array)$recipients;

        foreach ($recipients as $email) {
            dispatch(new ErrorReport($email, $this->getCode(), $this->getMessage()))
                ->onQueue('mailer');
        }

        return $this;
    }
}
