<?php

namespace App\Component\Package;


class ControllerMetaClass extends AbstractMetaClass
{

    /**
     * A module manifest file may hold data for annotations related to different components. This method selects only
     * relevant annotations.
     *
     * @param $all
     *
     * @return array
     */
    public function selectRelevantAnnotations($all)
    {
        $results = [];

        foreach ($all as $annot) {
            if ($annot instanceof Annotation\SecurityPolicy) {
                $results[] = $annot;
            }
        }

        return $results;
    }
}