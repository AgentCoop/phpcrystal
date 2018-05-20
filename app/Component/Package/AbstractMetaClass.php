<?php

namespace App\Component\Package;

use App\Component\Base\Creatable;

use App\Services\PackageManager;

/**
 * Class AbstractMetaClass
 *
 * @package App\Component\Package
 */
abstract class AbstractMetaClass
{
    use Creatable;

    /** @var \App\Services\PackageManager */
    private $packageManager;

    protected $className;

    protected $moduleName;

    protected $moduleAnnotations = [];

    protected $classAnnotations = [];

    protected $annotationsMethodMap = [];


    abstract public function selectRelevantAnnotations($all);

    protected function createModuleAnnotations(): array
    {
        $results = [];

        $manifest = $this->packageManager
            ->getModuleByName($this->getModuleName())
            ->getManifest();

        $manifestKeys = $manifest->getTopLevelKeys();

        foreach ($manifestKeys as $key) {
            $annotClass = '\\App\Component\\Package\\Annotation\\' . studly_case($key);

            if ( ! class_exists($annotClass)) {
                continue;
            }

            $results[] = new $annotClass($manifest->pluck($key)->toArray(), null);
        }

        return $results;
    }

    public static function getMethodFullNameFromReflection(\ReflectionMethod $reflectionMethod)
    {
        return $reflectionMethod->class . '@' . $reflectionMethod->name;
    }

    /***
     *
     */
    final public function __construct($className, $moduleName)
    {
        $this->packageManager = app()->make(PackageManager::class);
        $this->moduleName = $moduleName;
        $this->className = $className;

        $moduleAnnotations = $this->createModuleAnnotations();

        foreach ($this->selectRelevantAnnotations($moduleAnnotations) as $annot) {
            $this->addModuleAnnotation($annot);
        }
    }

    /**
     * @return array
     */
    final public function __sleep()
    {
        return ['moduleName', 'className', 'moduleAnnotations', 'classAnnotations', 'annotationsMethodMap'];
    }

    public function getClassName() : string
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getModuleName() : string
    {
        return $this->moduleName;
    }

    /**
     * @return $this
     */
    public function setModuleName($name) : self
    {
        $this->moduleName = $name;

        return $this;
    }

    /**
     * @param $annot
     *
     * @return $this
     */
    public function addModuleAnnotation($annot)
    {
        $this->moduleAnnotations[] = $annot;

        return $this;
    }

    /**
     * @param $annot
     *
     * @return $this
     */
    public function addClassAnnotation($annot)
    {
        $this->classAnnotations[] = $annot;

        return $this;
    }

    /**
     * @param $annot
     *
     * @return $this
     */
    public function addMethodAnnotation($annot, \ReflectionMethod $refMethod)
    {
        $arrRef = &$this->annotationsMethodMap[$refMethod->name];

        if (is_null($arrRef)) {
            $arrRef = [];
        }

        $arrRef[] = $annot;

        return $this;
    }

    public function getMergedAnnotations($annotClassName, $targetMethodName = null)
    {
        $annotsChain = [];

        foreach ($this->moduleAnnotations as $annot) {
            if ($annotClassName == get_class($annot)) {
                $annotsChain[] = $annot;
                break;
            }
        }

        foreach ($this->classAnnotations as $annot) {
            if ($annotClassName == get_class($annot)) {
                $annotsChain[] = $annot;
                break;
            }
        }

        foreach ((array)$this->annotationsMethodMap[$targetMethodName] as $annot) {
            if ($annotClassName == get_class($annot)) {
                $annotsChain[] = $annot;
                break;
            }
        }

        if (empty($annotsChain)) {
            throw new \RuntimeException();
        }

        $parent = $annotsChain[0];

        for ($i = 1; $i < count($annotsChain); $i++) {
            $parent->merge($annotsChain[$i]);
        }

        return $parent;
    }
}