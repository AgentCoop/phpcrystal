<?php

namespace App\Models\Logical\Support\Logging;

trait ErrorEntry
{
    /**
     * @return string
     */
    public function getFunctionName()
    {
        return $this->func_name;
    }

    /**
     * @return $this
     */
    public function setFunctionName($funcName)
    {
        $this->func_name = $funcName;

        return $this;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->class_name;
    }

    /**
     * @return $this
     */
    public function setClassName($className)
    {
        $this->class_name = $className;

        return $this;
    }

    /**
     * @return $this
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return $this
     */
    public function setLine($line)
    {
        $this->line = $line;

        return $this;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return static
     */
    public function setFile($filename)
    {
        $this->file = $filename;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return static
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->req_method;
    }

    /**
     * @return static
     */
    public function setRequestMethod($method)
    {
        $this->req_method = $method;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequestUri()
    {
        return $this->req_uri;
    }

    /**
     * @return static
     */
    public function setRequestUri($uri)
    {
        $this->req_uri = $uri;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequestBody()
    {
        return $this->req_body;
    }

    /**
     * @return static
     */
    public function setRequestBody($body)
    {
        $this->req_body = $body;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequestMimeType()
    {
        return $this->req_mime_type;
    }

    /**
     * @return static
     */
    public function setRequestMimeType($mimeType)
    {
        $this->req_mime_type = $mimeType;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequestQuery()
    {
        return $this->request_query;
    }

    /**
     * @return static
     */
    public function setRequestQuery($queryStr)
    {
        $this->request_query = $queryStr;

        return $this;
    }

    /**
     * @return string
     */
    public function getRemoteAddr()
    {
        return $this->remoteAddr;
    }

    /**
     * @return static
     */
    public function setRemoteAddr($remoteAddr)
    {
        $this->remoteAddr = $remoteAddr;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @return static
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return static
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return integer
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return static
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return static
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }
}
