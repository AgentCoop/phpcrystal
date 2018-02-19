<?php

class Manifest
{
    private $baseDir;
    private $routeUriPrefix;
    private $routeHost;

    public function __construct()
    {
    }

    public function getBaseDir()
    {
        return $this->baseDir;
    }
}