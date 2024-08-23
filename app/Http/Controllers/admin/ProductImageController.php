<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProductimageService;

class ProductImageController extends Controller
{
    public function __construct(
        protected ProductimageService $productImageService
    ){
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
