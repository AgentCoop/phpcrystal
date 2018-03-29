<?php
namespace App\Services\Package\Module;

use Faker\Provider\File;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Routing\Loader\AnnotationClassLoader;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Component\Routing\Route;

use App\Component\Mvc\Controller\AbstractService;

use App\Services\Filesystem as Filesystem;
use App\Services\Base\PhpParser;

use Doctrine\Common\Annotations\SimpleAnnotationReader;

use App\Services\Package\Annotation as Annotation;
use App\Services\Package\Manager as PackageManager;
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
    private static $taggedServices = [];

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

    /**
     *
    */
    private function dumpServiceProviders() : void
    {
        $content = $this->generateServiceProviders();
        Filesystem\Aux::append(storage_path(PackageManager::SERVICE_PROVIDERS_DUMP_FILENAME), $content);
    }

    /**
     * @return void
    */
    protected function configureRoute(Route $route, \ReflectionClass $class, \ReflectionMethod $method, $annot)
    {
        $this->routes[] = ['annot' => $annot, 'method' => $method, 'route' => $route];
    }

    /**
     * @return SimpleAnnotationReader
    */
    private function createAnnotationReader()
    {
        $reader = new SimpleAnnotationReader();
        $reader->addNamespace('\\App\\Services\\Package\\Annotation');

        return $reader;
    }

    /**
     *
    */
    public function __construct(Manifest $manifest, $basedir, $name = null)
    {
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

        parent::__construct($this->createAnnotationReader());
    }

    /**
     * @return string
    */
    public function getRoutesDumpFilename()
    {
        return base_path('/routes/') . $this->getName() . '_module.php';
    }

    /**
     * @return string
    */
    public function generateRoutes()
    {
        $timestamp = date('Y-m-d h:i:s');
        $moduleName = $this->getName();
        $content = <<<DOC
<?php
//
//  Auto-generated on $timestamp, module $moduleName, DO NOT modify this file     
//

DOC;

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

            $reflectionClass = new \ReflectionClass($className);
            $annots = $this->annotReader->getClassAnnotations($reflectionClass);

            foreach ($annots as $annot) {
                if ($annot instanceof Annotation\Middleware) {
                    if ( ! isset($this->middlewareMap[$className])) {
                        $this->middlewareMap[$className] = [];
                    }

                    $this->middlewareMap[$className][] = $annot;
                }
            }
        })
            ->run();

        (new AnnotationDirectoryLoader(new Locator(), $this))->load($this->controllersBaseDir);

        file_put_contents($this->getRoutesDumpFilename(), $this->generateRoutes());

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

        $this->dumpServiceProviders();

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
}
