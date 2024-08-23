<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

class BaseService {

    public $repository;
    public $tempImageRepository;
    public $productImageRepository;

    public function __construct(
        
        $repository,
        $tempImageRepository,
        $productImageRepository
        
        ){

        $this->repository = $repository;
        $this->tempImageRepository = $tempImageRepository;
        $this->productImageRepository = $productImageRepository;
    }

    public function getAll(){

        return $this->repository->all();

    }

    public function getByOrder(){
        return $this->repository->getByOrder();
    }

    public function find($id){

        return $this->repository->find($id);

    }

    public function imageUpload($image_id, $prefix){

        $tempImage = $this->tempImageRepository->find($image_id);
        $extArray = explode('.', $tempImage->image);
        $ext = last($extArray);

        $newImageName = $prefix . '-' . time() . '.' . $ext;
        $spath = public_path() . '/temp/' . $tempImage->image;
        $dpath = public_path() . '/uploads/category/' . $newImageName;

        File::copy($spath, $dpath);

        //generate image thumbnail
        $dpath = public_path() . '/uploads/category/thumb/' . $newImageName;

        $img = Image::read($spath);
        $img->resize(450, 600);
        $img->save($dpath);

        return $newImageName;

    }

    public function productImageUpload($data, $product_id){

        foreach ($data['image_array'] as $temp_image_id) {
            $tempImageInfo = $this->tempImageRepository->find($temp_image_id);
            $extArray = explode('.', $tempImageInfo->image);
            $ext = last($extArray);
            $productImage = $this->productImageRepository->create([
                'product_id' => $product_id,
                'image' => 'NULL',
            ]);

            $imageName = $product_id . '-' . $productImage->id . '-' . time() . '.' . $ext;
            $this->productImageRepository->update($productImage->id, [
                'image' => $imageName,
            ]);

            //Generate Product Thumbnails

            //Large Image
            $sourcePath = public_path() . '/temp/' . $tempImageInfo->image;
            $destPath = public_path() . '/uploads/product/large/' . $imageName;
            $image = Image::read($sourcePath);
            $image->resize(1400, 933);
            $image->save($destPath);

            //Small Image
            $sourcePath = public_path() . '/temp/' . $tempImageInfo->image;
            $destPath = public_path() . '/uploads/product/small/' . $imageName;
            $image = Image::read($sourcePath);
            $image->resize(300, 300);
            $image->save($destPath);
        }

    }

}
