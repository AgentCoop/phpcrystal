<?php
namespace App\Services\Package\Module;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Routing\Loader\AnnotationClassLoader;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Component\Routing\Route;

use App\Services\Filesystem\Finder;
use App\Services\Base\PhpParser;

use Doctrine\Common\Annotations\SimpleAnnotationReader;

use App\Services\Package\Annotation\Route as AnnotationRoute;
use App\Services\Package\Annotation\Middleware as AnnotationMiddleware;
use App\Services\Package\Manager as PackageManager;

class Locator implements FileLocatorInterface
{
    public function locate($name, $currentPath = null, $first = true)
    {
        return $name;
    }
}

class Module extends AnnotationClassLoader
{
    /** @var string */
    private $basedir;

    /** @var Manifest */
    private $manifest;

    /** @var array */
    private $routes = [];

    /** @var string */
    private $name;

    /** @var array */
    private $middlewareMap = [];

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
    public function __construct(Manifest $manifest, $basedir)
    {
        $this->manifest = $manifest;
        $this->basedir = $basedir;

        $parts = explode(DIRECTORY_SEPARATOR, $basedir);
        $this->name = strtolower(array_pop($parts));

        $reader = $this->createAnnotationReader();

        // Before parsing, all annotation classes must be loaded
        class_exists(AnnotationRoute::class, true);
        class_exists(AnnotationMiddleware::class, true);

        parent::__construct($reader);

        $controllersBaseDir = $basedir . '/Http/Controllers/';

        Finder::findPhpFiles($controllersBaseDir, function($filename) use($reader) {
            $parser = PhpParser::loadFromFile($filename);

            if ( ! ($className = $parser->extractClassName())) {
                return;
            }

            $reflectionClass = new \ReflectionClass($className);
            $annots = $reader->getClassAnnotations($reflectionClass);

            foreach ($annots as $annot) {
                if ($annot instanceof AnnotationMiddleware) {
                    if ( ! isset($this->middlewareMap[$className])) {
                        $this->middlewareMap[$className] = [];
                    }

                    $this->middlewareMap[$className][] = $annot;
                }
            }
        })
            ->run();

        (new AnnotationDirectoryLoader(new Locator(), $this))->load($controllersBaseDir);
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
    public function getName()
    {
        return $this->name;
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

        file_put_contents($this->getRoutesDumpFilename(), $this->generateRoutes());
    }
}