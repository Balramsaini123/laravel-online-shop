<?php

namespace App\Repositories;

use App\Models\ProductImage;

class ProductImageRepository extends BaseRepository
{
    public function __construct(ProductImage $model)
    {
        $this->model = $model;
    }

    public function getByProductId($product_id){

        return $this->model->where('product_id', $product_id)->get();

    }

    public function deleteByProductId($product_id){

        return $this->model->where('product_id', $product_id)->delete();

    }


}
