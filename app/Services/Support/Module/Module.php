<?php
namespace App\Services\Support\Module;

class Module
{
    /** @var string */
    private $name;

    /** @var string */
    private $basedir;

    /** @var Manifest */
    private $manifest;

    public function __construct(Manifest $manifest, $basedir)
    {
        $this->manifest = $manifest;
        $this->basedir = $basedir;
    }

    /**
     * @return string
    */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Manifest
    */
    public function getManifest()
    {
        return $this->manifest;
    }

    /**
     * @return string
     */
    public function getBaseDir()
    {
        return $this->basedir;
    }
}