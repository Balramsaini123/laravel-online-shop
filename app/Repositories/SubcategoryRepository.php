<?php

namespace App\Repositories;

use App\Models\SubCategory;

class SubcategoryRepository extends BaseRepository
{
    public function __construct(SubCategory $model)
    {
        $this->model = $model;
    }

    public function getSubCategories($category_id){

        return $this->model->where('category_id', $category_id)->get();

    }

}
