<?php

namespace App\Frontoffice\Services\View;

use PhpCrystal\Core\Component\Mvc\AbstractView;

class Index extends AbstractView
{
    /**
     * @return array
    */
    public function getData()
    {
        $data['page_title'] = 'Laravel';

        return $data;
    }
}