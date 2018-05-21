<?php

namespace App\Models\Physical\DAL;

use Illuminate\Database\Eloquent\Model;

use App\Component\Base\Creatable;

abstract class AbstractPostgres extends Model
{
    use Common;

    /**
     * @return static
     */
    public static function create()
    {
        $model = new static();

        $model->save();
        $model->refresh();

        return $model;
    }
}
