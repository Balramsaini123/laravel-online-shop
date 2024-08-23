<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use App\Repositories\TempimageRepository;
use Illuminate\Support\Facades\File;
use App\Traits\JsonResponseTrait;

class CategoryService extends BaseService {

    use JsonResponseTrait;

    public function __construct(CategoryRepository $repository, TempimageRepository $tempImageRepository){
        $this->repository = $repository;
        $this->tempImageRepository = $tempImageRepository;
    }

    public function create(array $data){

        //image save here
        if (!empty($data['image_id'])) {
            $newImageName = $this->imageUpload($data['image_id'], $data['slug']);
        }
        if (isset($newImageName)) {
            $data['image'] = $newImageName;
        }

        $this->repository->create($data);

        return $this->successResponse(true, route('categories.index'), 'Category created successfully');

    }

    public function update($id, array $data){
            
        //image save here
        $category = $this->repository->find($id);
        if (!empty($data['image_id'])) {

            $newImageName = $this->imageUpload($data['image_id'], $category->id);

            //delete old image
            $oldImage = $category->image;
            File::delete(public_path() . '/uploads/category/thumb/' . $oldImage);
            File::delete(public_path() . '/uploads/category/' . $oldImage);
        }
        if(isset($newImageName)){
            $data['image'] = $newImageName;
        }

        $this->repository->update($id, $data);

        return $this->successResponse(true, route('categories.index'), 'Category updated successfully');
      
    }

    public function delete($id){

       $category = $this->repository->find($id);

        // Delete the category image files if they exist
        if ($category->image) {
            $thumbPath = public_path('uploads/category/thumb/' . $category->image);
            $imagePath = public_path('uploads/category/' . $category->image);

            if (File::exists($thumbPath)) {
                File::delete($thumbPath);
            }

            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }

        if($this->repository->delete($id)){
            return $this->successResponse(true, route('categories.index'), 'Category deleted successfully');
        }

    }

}
