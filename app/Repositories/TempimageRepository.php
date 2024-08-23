<?php

namespace App\Repositories;

use App\Models\TempImage;

class TempimageRepository extends BaseRepository
{
    public function __construct(TempImage $model)
    {
        $this->model = $model;
    }

}
