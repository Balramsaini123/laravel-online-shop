<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProductimageService;

class ProductImageController extends Controller
{
    public $productImageService;

    public function __construct(ProductimageService $productImageService){
        $this->productImageService = $productImageService;
    }

    public function update(Request $request)
    {
        return $this->productImageService->update($request);
    }

    public function destroy(Request $request)
    {
        return $this->productImageService->delete($request);
    }
}
