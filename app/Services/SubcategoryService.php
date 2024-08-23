<?php

namespace App\Services;

use App\Repositories\SubcategoryRepository;
use App\Traits\JsonResponseTrait;

class SubcategoryService extends BaseService
{
    
    use JsonResponseTrait;

    public $repository;

    public function __construct(SubcategoryRepository $repository){
        $this->repository = $repository;
    }

    public function create(array $data){

        $this->repository->create($data);

        return $this->successResponse(true, route('sub-categories.index'), 'Sub Category created successfully');

    }

    public function update($id, $data){
        
        $this->repository->update($id, $data);

        return $this->successResponse(true, route('sub-categories.index'), 'SubCategory updated successfully');

    }

    public function delete($id){

        $this->repository->delete($id);

        return $this->successResponse(true, route('sub-categories.index'), 'Category deleted successfully');

    }

    public function getSubCategories($category_id){

        return $this->repository->getSubCategories($category_id);
        
    }

}
