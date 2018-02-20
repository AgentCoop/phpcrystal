<?php

namespace App\Services\Filesystem;

use Symfony\Component\Finder\Finder as SymfonyFinder;

class Finder
{
    /** @var integer|null */
    private $maxDepth;

    /** @var string */
    private $pattern;

    /** @var SymfonyFinder */
    private $finder;

    /** @var callable */
    private $onFileCallback;

    /** @var callable */
    private $onDirCallback;

    /** @var callable */
    private $onLinkCallback;

    /**
     * @return void
    */
    public function run()
    {
        if ($this->maxDepth) {
            $this->finder->depth('<= ' . $this->maxDepth);
        }

        foreach ($this->finder as $file) {
            if ($file->isDir() && is_callable($this->onDirCallback)) {
                call_user_func_array(
                    $this->onDirCallback,
                    [$file->getRealPath(), $file]
                );
            } else if ($file->isFile() && is_callable($this->onFileCallback)) {
                call_user_func_array(
                    $this->onFileCallback,
                    [$file->getRealPath(), $file]
                );
            } else if ($file->isLink() && is_callable($this->onLinkCallback)) {
                call_user_func_array(
                    $this->onLinkCallback,
                    [$file->getRealPath(), $file]
                );
            }
        }
    }

    /**
     * @return Finder
     */
    public static function findByFileExt($dirs, $fileExt, callable $onFileCb)
    {
        $scanner = static::create($dirs, $onFileCb);

        $scanner->finder->files()->name('*.' . $fileExt);
    }

    /**
     * @return static
     */
    public static function findByFilename($dirs, $filename, callable $onFileCb)
    {
        $scanner = static::create($dirs, $onFileCb);

        $scanner->finder->files()->name($filename);

        return $scanner;
    }

    /**
     * @return static
     */
    public static function findPhpFiles($dirs, callable $onFileCb)
    {
        $scanner = static::create($dirs, $onFileCb);

        $scanner->finder->files()->name('*.php');

        return $scanner;
    }

    /**
     * @return static
    */
    public static function create($dirs, callable $onFileCb = null, callable $onDirCb = null, callable $onLinkCb = null)
    {
        return new static($dirs, $onFileCb, $onDirCb, $onLinkCb);
    }

    /**
     *
    */
    public function __construct($dirs, callable $onFileCb = null, callable $onDirCb = null, callable $onLinkCb = null)
    {
        $this->finder = new SymfonyFinder();
        $this->finder->in($dirs);
        $this->onFileCallback = $onFileCb;
        $this->onDirCallback = $onDirCb;
        $this->onLinkCallback = $onLinkCb;
    }

    /**
     * @return $this
    */
    public function setMaxDepth($level)
    {
        $this->maxDepth = intval($level);

        return $this;
    }

    /**
     * @return $this
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;

        return $this;
    }
}