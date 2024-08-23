<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository extends BaseRepository
{
    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function all(){

        return $this->model->latest("id")->with('product_images')->paginate(5);
    
    }

}
