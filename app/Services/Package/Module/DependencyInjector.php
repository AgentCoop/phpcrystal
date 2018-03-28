<?php

namespace App\Services\Package\Module;

use App\Component\Mvc\Controller\AbstractService;

class DependencyInjector
{
    /** @var array */
    private $circularReferenceTracker = [];

    /** @var string */
    private $className;

    private $methodName;

    /**
     *
     */
    public function __construct($className, $methodName = '__construct')
    {
        $this->className = $className;
        $this->methodName = $methodName;
    }

    /**
     * @return \ReflectionMethod
     */
    private function getInjectorReflection($className, $methodName): \ReflectionMethod
    {
        return new \ReflectionMethod($className, $methodName);
    }

    /**
     * @return void
     */
    private function circularReferenceCheck()
    {
        if (count(array_unique($this->circularReferenceTracker)) != count($this->circularReferenceTracker)) {
            FrameworkRuntimeError::create('A circular dependency for the class "%s" has been detected', null, $this->rootClient)
                ->_throw();
        }
    }

    /**
     *
     */
    private function retrieveDependencyData(\ReflectionParameter $param): array
    {
        $data = [];

        $typeHinted = $param->getClass();

        $data['className'] = $typeHinted->name;
        $data['paramName'] = $param->getName();
        $data['allowsNull'] = $param->allowsNull();

        return $data;
    }

    /**
     * @return array
     */
    private function getInjectorDeps(\ReflectionMethod $injector): array
    {
        $results = [];
        $params = $injector->getParameters();

        foreach ($params as $param) {
            $results[] = $this->retrieveDependencyData($param);
        }

        return $results;
    }

    /**
     * @return array|null
     */
    private function getNestedDependencies(&$parentNode, \ReflectionMethod $injector): void
    {
        $deps = $this->getInjectorDeps($injector);

        if (!count($deps)) {
            $parentNode['deps'] = null;

            return;
        }

        foreach ($deps as $index => $depData) {
            $className = $depData['className'];

            $parentNode['deps'][$index] = ['depth' => $parentNode['depth'] + 1];
            $parentNode['deps'][$index]['data'] = $depData;
            $this->getNestedDependencies($parentNode['deps'][$index],
                $this->getInjectorReflection($className, $this->methodName));
        }
    }

    /**
     * @return array
     */
    public function getDependencyTree()
    {
        $treeStruc = ['deps' => null, 'depth' => 0];

        $this->getNestedDependencies($treeStruc,
            $this->getInjectorReflection($this->className, $this->methodName));

        return $treeStruc;
    }
}
