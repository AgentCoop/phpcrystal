<?php

namespace App\Component\Package\Annotation;

interface AnnotationInterface
{
    public function merge(AnnotationInterface $descendent);
}