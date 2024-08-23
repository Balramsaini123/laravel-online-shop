<?php

namespace App\Http\Controllers\admin;

use App\DataTables\ProductDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\ProductService;
use App\Services\BrandService;
use App\Services\CategoryService;
use App\Services\SubcategoryService;
use App\Traits\JsonResponseTrait;

class ProductController extends Controller
{
    use JsonResponseTrait;

    public function __construct(
        protected ProductService $productService,
        protected BrandService $brandService,
        protected CategoryService $categoryService,
        protected SubcategoryService $subcategoryService
    ){
    }

    public function index(ProductDataTable $dataTable){

        return $dataTable->render("admin.products.list");

    }

    public function create()
    {
        $categories = $this->categoryService->getByOrder();
        $brands = $this->brandService->getByOrder();
        return view("admin.products.create", compact("categories", "brands"));
    }

    public function store(Request $request)
    {
        if ($request->track_qty == 'Yes') {
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'slug' => 'required|unique:products',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products',
            'track_qty' => 'required|in:Yes,No',
            'category_id' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No',
        ]);

        if ($validator->passes()) {
           
            return $this->productService->create($request->all());

        } else {
            return $this->validationErrorResponse(false, $validator->errors());
        }
    }

    public function edit(Request $request, $id)
    {
        $product = $this->productService->find($id);

        if (empty($product)) {

            return redirect()->route('products.index')->with('error', 'Product not found');
        }

        $productImages = $this->productService->getProductImages($product->id);
        $subCategories = $this->subcategoryService->getSubCategories($product->category_id);
        $categories = $this->categoryService->getByOrder();
        $brands = $this->brandService->getByOrder();

        return view("admin.products.edit", compact("product", "categories", "brands", "subCategories", "productImages"));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'title' => 'required',
            'slug' => 'required',
            'price' => 'required|numeric',
            'sku' => 'required',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No',
        ];

        if ($request->track_qty == 'Yes') {
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {
            return $this->productService->update($id, $request->all());
        } else {
            return $this->validationErrorResponse(false, $validator->errors());
        }
    }

    public function destroy($id)
    {
        return $this->productService->delete($id);
    }
}
