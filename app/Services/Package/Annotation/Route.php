<?php

namespace App\Services\Package\Annotation;

use Symfony\Component\Routing\Annotation\Route as SymfonyAnnotationRoute;

/**
 * Annotation class for @Route().
 *
 * @Annotation
 * @Target({"METHOD"})
 * @Attributes({
 *   @Attribute("methods", type = "array")
 * })
 */
class Route extends SymfonyAnnotationRoute
{

}
