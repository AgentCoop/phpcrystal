<?php

namespace App\Services\View\Frontend;

use App\Services\View\AbstractView;

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