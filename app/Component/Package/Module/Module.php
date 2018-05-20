<?php
namespace App\Component\Package\Module;

use Illuminate\Routing\Controller;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Routing\Loader\AnnotationClassLoader;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Component\Routing\Route;

use App\Component\Mvc\Controller\AbstractService;

use App\Component\Base\Filesystem as Filesystem;
use App\Component\Base\PhpParser;
use App\Component\Package\ControllersMapItem;
use App\Component\Package\ControllerMetaClass;

use Doctrine\Common\Annotations\SimpleAnnotationReader;


use App\Component\Package\Annotation as Annotation;
use App\Services\PackageManager;
use App\Services\Factory;


class Locator implements FileLocatorInterface
{
    public function locate($name, $currentPath = null, $first = true)
    {
        return $name;
    }
}

class Module extends AnnotationClassLoader
{
    private $annotReader;

    /** @var string */
    private $basedir;

    /** @var string */
    private $servicesBaseDir;

    /** @var string */
    private $controllersBaseDir;

    /** @var Manifest */
    private $manifest;

    /** @var array */
    private $routes = [];

    /** @var string */
    private $name;

    /** @var array */
    private $middlewareMap = [];

    /** @var array */
    private $servicesMap = [];

    /** @var array */
    private $controllersMap = [];

    private $metaClassesMap = [];

    /** @var array */
    private static $taggedServices = [];

    private function extractClassAnnotations($metaClass)
    {
        $className = $metaClass->getClassName();

        $refClass = new \ReflectionClass($className);
        $classAnnots = $this->annotReader->getClassAnnotations($refClass);

        foreach ($classAnnots as $annot) {
            $metaClass->addClassAnnotation($annot);
        }

        return $this;
    }

    private function extractMethodsAnnotations($metaClass)
    {
        $className = $metaClass->getClassName();
        $refClass = new \ReflectionClass($className);

        foreach ($refClass->getMethods() as $refMethod) {
            if ($refMethod->isPrivate() || $refMethod->isProtected()) {
                continue;
            }

            $methodAnnots = $this->annotReader->getMethodAnnotations($refMethod);

            foreach ($methodAnnots as $annot) {
                $metaClass->addMethodAnnotation($annot, $refMethod);
            }
        }

        return $this;
    }

    /**
     *
    */
    public static function generateTaggedServices() : string
    {
        $content = '';

        foreach (self::$taggedServices as $tagName => $serviceClassNames) {
            $content .= sprintf("\$this->app->tag(%s, '%s');\n",
                PhpParser::toPhpArray($serviceClassNames), $tagName);
        }

        return $content;
    }

    /**
     * @return string
    */
    private function generateServiceProviders()
    {
        $content = '';

        foreach ($this->servicesMap as $className => $annot) {
            /** @var Annotation\Service $annot */
            $type  = $annot->getValue();
            $callName = AbstractService::TYPES_CONTAINER_CALLS_MAP[$type];
            $serviceTag = $annot->getTag();

            if ($serviceTag) {
                if ( ! isset(self::$taggedServices[$serviceTag])) {
                    self::$taggedServices[$serviceTag] = [];
                }

                self::$taggedServices[$serviceTag][] = $className;
            }

            // Bind interfaces implemented by the class
            $implements = class_implements($className);

            foreach ($implements as $interfaceName) {
                $record = sprintf("\$this->app->bind('%s', '%s');\n",
                    $interfaceName, $className);

                $content .= $record;
            }

            $dependencies = Factory::getMethodInjectedServices($className, '__construct');
            $appMakeCalls = [];

            foreach ($dependencies as $item) {
                $appMakeCalls[] = sprintf('$app->make(%s::class)',
                    PhpParser::toFQCN($item['className']));
            }

            $record = sprintf("\$this->app->%s(%s::class, function(\$app) { return (new %s(%s))->setModuleName('%s')->setLazyInit(%d); });\n",
                $callName, $className, $className, join(',', $appMakeCalls), $this->getName(), $annot->getLazyInit());

            $content .= $record;
        }

        return $content;
    }


    private function dumpServiceProviders($env) : void
    {
        $content = $this->generateServiceProviders();
        $filename = PackageManager::generateDumpFilename(PackageManager::SERVICE_PROVIDERS_DUMP_BASENAME, $env);
        Filesystem\Aux::append($filename, $content);
    }

    protected function configureRoute(Route $route, \ReflectionClass $class, \ReflectionMethod $method, $annot)
    {
        $this->routes[] = ['annot' => $annot, 'method' => $method, 'route' => $route];
    }

    private function createAnnotationReader()
    {
        $reader = new SimpleAnnotationReader();
        $reader->addNamespace('\\App\\Component\\Package\\Annotation');

        return $reader;
    }

    /**
     *
     */
    public function __construct(Manifest $manifest, $name = null)
    {
        $basedir = $manifest->getBaseDir();

        $this->manifest = $manifest;
        $this->basedir = $basedir;
        $this->servicesBaseDir = $basedir . join(DIRECTORY_SEPARATOR, ['', 'Services']);
        $this->controllersBaseDir = $basedir . join(DIRECTORY_SEPARATOR, ['', 'Http', 'Controllers']);
        $this->annotReader = $this->createAnnotationReader();

        if (is_null($name)) {
            $parts = explode(DIRECTORY_SEPARATOR, $basedir);
            $this->name = strtolower(array_pop($parts));
        } else {
            $this->name = $name;
        }

        // Before parsing, all annotation classes must be loaded
        class_exists(Annotation\Route::class, true);
        class_exists(Annotation\Middleware::class, true);
        class_exists(Annotation\Service::class, true);
        class_exists(Annotation\SecurityPolicy::class, true);

        parent::__construct($this->createAnnotationReader());
    }

    /**
     * @return string
    */
    public function generateRoutes()
    {
        $content = '';

        foreach ($this->routes as $item) {
            /** @var Route $route */
            $route = $item['route'];

            /** @var AnnotationRoute $annot */
            $annot = $item['annot'];

            /** @var \ReflectionMethod $reflectionMethod */
            $reflectionMethod = $item['method'];

            $record = 'Route::';

            if ( empty($methods = $route->getMethods())) {
                $methods = ['get'];
            } else {
                $methods = array_map('strtolower', $methods);
            }

            $record .= sprintf("match(%s, '%s', ['as' => '%s', 'uses' => '%s@%s'])",
                PhpParser::toPhpArray($methods),
                $route->getPath(),
                $annot->getName(),
                $reflectionMethod->class,
                $reflectionMethod->name
            );

            if (isset($this->middlewareMap[$reflectionMethod->class])) {
                $middlewareAnnots = $this->middlewareMap[$reflectionMethod->class];
                $middlewareNames = array_map(function($annot) { return $annot->getCommonName(); }, $middlewareAnnots);

                $record .= sprintf('->middleware(%s)', PhpParser::toPhpArray($middlewareNames));
            }

            // Routing params constraints
            $requirements = $annot->getRequirements();

            if ( ! empty($requirements)) {
                $record .= sprintf('->where(%s)', PhpParser::toPhpArray($requirements, true));
            }

            $record .= ';';
            $content .= $record . "\n";
        }

        return $content;
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

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return $this
     */
    public function setName($name) : self
    {
        $this->name = $name;

        return $this;
    }

    /**
     *
    */
    public function buildControllers($env) : bool
    {
        // The core module does not have controllers
        if ($this->getName() == PackageManager::CORE_MODULE_NAME) {
            return false;
        }

        Filesystem\Finder::findPhpFiles($this->controllersBaseDir, function($filename) {
            $parser = PhpParser::loadFromFile($filename);

            if ( ! ($className = $parser->extractClassName())) {
                return;
            }

            if ( ! is_subclass_of($className, Controller::class)) {
                throw new \RuntimeException(sprintf('Class "%s" must be a subclass of "%s"',
                    $className, Controller::class));
            }

            $metaClass = new ControllerMetaClass($className, $this->getName());
            $this->metaClassesMap[$className] = $metaClass;

            $this
                ->extractClassAnnotations($metaClass)
                ->extractMethodsAnnotations($metaClass);
        })
            ->run();

        (new AnnotationDirectoryLoader(new Locator(), $this))->load($this->controllersBaseDir);

        $dumpFilename = PackageManager::generateDumpFilename(PackageManager::MODULE_ROUTES_DUMP_BASENAME,
            $this->getName(), $env);

        Filesystem\Aux::phpAutogenerated($dumpFilename);
        Filesystem\Aux::append($dumpFilename, $this->generateRoutes(), 0666);

        return true;
    }

    /**
     *
    */
    public function buildServices($env = PackageManager::LOCAL_ENV) : bool
    {
        if ( ! file_exists($this->servicesBaseDir)) {
            return false;
        }

        Filesystem\Finder::findPhpFiles($this->servicesBaseDir, function($filename) {
            $parser = PhpParser::loadFromFile($filename);

            if ( ! ($className = $parser->extractClassName())) {
                return;
            }

            $reflectionClass = new \ReflectionClass($className);
            $annots = $this->annotReader->getClassAnnotations($reflectionClass);

            // Any AbstractService subclass must have a corresponding annotation
            if (empty($annots) && is_subclass_of($className, AbstractService::class)) {
                throw new \RuntimeException(sprintf('Missing service annotation for %s',
                    $className));
            }

            foreach ($annots as $annot) {
                if ($annot instanceof  Annotation\Service &&
                    is_subclass_of($className, AbstractService::class))
                {
                    if ( ! in_array($annot->getValue(), AbstractService::TYPES)) {
                        throw new \RuntimeException(sprintf('Invalid service type specified "%s"',
                            $this->getValue()));
                    }

                    $this->servicesMap[$className] = $annot;
                }
            }
        })
            ->run();

        $this->dumpServiceProviders($env);

        return true;
    }

    /**
     * @return void
    */
    public function build($env = PackageManager::LOCAL_ENV)
    {
        $manifest = $this->getManifest();

        $manifest
            ->setEnv($env)
            ->reload();

        $this->buildServices($env);
        $this->buildControllers($env);
    }

    /**
     *
    */
    public function getControllersMap(): array
    {
        return $this->controllersMap;
    }

    public function getMetaClassesMap() : array
    {
        return $this->metaClassesMap;
    }
}
