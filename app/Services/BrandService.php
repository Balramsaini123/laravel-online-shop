<?php

namespace App\Services;

use App\Repositories\BrandRepository;
use App\Traits\JsonResponseTrait;

class BrandService extends BaseService {

    use JsonResponseTrait;

    public $repository;

    public function __construct(BrandRepository $repository){
        $this->repository = $repository;
    }

    public function create(array $data){

        $this->repository->create($data);

        return $this->successResponse(true, route('brands.index'), 'Brand created successfully');

    }

    public function update($id, array $data){

        if($this->repository->update($id, $data)){
            return $this->successResponse(true, route('brands.index'), 'Brand updated successfully');
        }

    }

    public function delete($id){

        if($this->repository->delete($id)){
            return $this->successResponse(true, route('brands.index'), 'Brand deleted successfully');
        }

    }

}
