<?php

namespace App\Services\Package\Annotation;

use App\Component\Mvc\Controller\AbstractService;

/**
 * Annotation class for @Service().
 *
 * @Annotation
 * @Target({"CLASS"})
 * @Attributes({
 *   @Attribute("tag", type = "string")
 * })*
 */
class Service extends AbstractBase
{
    /** @var string */
    private $tag;

    /**
     *
    */
    public function getTag() : string
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
}
