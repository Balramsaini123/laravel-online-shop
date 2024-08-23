<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Repositories\TempimageRepository;
use App\Repositories\ProductImageRepository;
use Illuminate\Support\Facades\Session;
use App\Traits\JsonResponseTrait;
use Illuminate\Support\Facades\File;

class ProductService extends BaseService {

    use JsonResponseTrait;

    public function __construct(ProductRepository $repository, TempimageRepository $tempImageRepository, ProductImageRepository $productImageRepository){
        $this->repository = $repository;
        $this->tempImageRepository = $tempImageRepository;
        $this->productImageRepository = $productImageRepository;
    }

    public function create(array $data){
        
        $product = $this->repository->create($data);

        //Gallery pics
        if (!empty($data['image_array'])) {
            $this->productImageUpload($data, $product->id);
        }
        Session::flash('flashMessage', 'Product created successfully');

        return $this->successResponse(true, route('products.index'), 'Product created successfully');

    }

    public function update($id, array $data){

        $this->repository->update($id, $data);
        Session::flash('flashMessage', 'Product updated successfully');

        return $this->successResponse(true, route('products.index'), 'Product updated successfully');

    }

    public function delete($id){

        $productImages = $this->getProductImages($id);

        if (!empty($productImages)) {
            foreach ($productImages as $productImage) {
                File::delete(public_path('uploads/product/large/' . $productImage->image));
                File::delete(public_path('uploads/product/small/' . $productImage->image));
            }

            $this->productImageRepository->deleteByProductId($id);
        }

        if($this->repository->delete($id)){
            Session::flash('flashMessage', 'Product deleted successfully');
            return $this->successResponse(true, route('products.index'), 'Product deleted successfully');
        }
        
    }

    public function getProductImages($product_id){

        return $this->productImageRepository->getByProductId($product_id);

    }

}
