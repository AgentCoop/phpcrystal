<?php

namespace App\Component\Package\Annotation;

use App\Component\Mvc\Controller\AbstractService;

/**
 * Annotation class for @Service().
 *
 * @Annotation
 * @Target({"CLASS"})
 * @Attributes({
 *   @Attribute("tag", type = "string"),
 *   @Attribute("lazyInit", type = "bool")
 * })*
 */
class Service extends AbstractBase
{
    /** @var string */
    private $tag;

    /** @var bool */
    private $lazyInit;

    /**
     *
    */
    public function getTag() : ?string
    {
        return $this->tag;
    }

    /**
     *
     */
    public function setTag($tagName) : self
    {
        $this->tag = $tagName;

        return $this;
    }

    /**
     *
     */
    public function getLazyInit() : ?string
    {
        return $this->lazyInit;
    }

    /**
     *
     */
    public function setLazyInit($val) : self
    {
        $this->lazyInit = boolval($val);

        return $this;
    }
}
