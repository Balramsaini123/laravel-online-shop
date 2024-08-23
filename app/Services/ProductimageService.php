<?php

namespace App\Services;

use App\Repositories\ProductImageRepository;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\File;

class ProductimageService {

    public $productImageRepository;

    public function __construct(ProductImageRepository $productImageRepository){
        $this->productImageRepository = $productImageRepository;
    }

    public function update($request){

        $image = $request->image;
        $ext = $image->getClientOriginalExtension();
        $sourcePath = $image->getPathName();

        $productImage = $this->productImageRepository->create([
            'product_id' => $request->product_id,
            'image' => 'NULL',
        ]);

        $imageName = $request->product_id . '-' . $productImage->id . '-' . time() . '.' . $ext;
        $productImage = $this->productImageRepository->update($productImage->id, [
            'image' => $imageName,
        ]);

        //Large Image
        $destPath = public_path() . '/uploads/product/large/' . $imageName;
        $image = Image::read($sourcePath);
        $image->resize(1400, 933);
        $image->save($destPath);

        //Small Image
        $destPath = public_path() . '/uploads/product/small/' . $imageName;
        $image = Image::read($sourcePath);
        $image->resize(300, 300);
        $image->save($destPath);

        return response()->json([
            'status' => true,
            'image_id' => $productImage->id,
            'ImagePath' => asset('uploads/product/small/'.$productImage->image),
            'message' => 'Image saved successfully',
        ]);

    }

    public function delete($request){

        $productImage = $this->productImageRepository->find($request->id);

        File::delete(public_path('uploads/product/large/'.$productImage->image));
        File::delete(public_path('uploads/product/small/'.$productImage->image));

        $this->productImageRepository->delete($request->id);

        return response()->json([
            'status' => true,
            'message' => 'Image deleted successfully',
        ]);

    }

}
