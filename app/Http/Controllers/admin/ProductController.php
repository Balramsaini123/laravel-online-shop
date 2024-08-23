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

    public $productService;
    public $brandService;
    public $categoryService;
    public $subcategoryService;

    public function __construct(

        ProductService $productService,
        BrandService $brandService,
        CategoryService $categoryService,
        SubcategoryService $subcategoryService

        ){

        $this->productService = $productService;
        $this->brandService = $brandService;
        $this->categoryService = $categoryService;
        $this->subcategoryService = $subcategoryService;

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

        } else {
            return $this->validationErrorResponse(false, $validator->errors());
        }
    }

    public function destroy($id)
    {
        return $this->productService->delete($id);
    }

    public function getProducts(Request $request)
    {
        $tempProduct = [];
        if($request->term != ""){
            $products = Product::where('title', 'like', '%' . $request->term . '%')->get();

            if($products != null){
                foreach ($products as $product) {
                    $tempProduct[] = array('id' => $product->id, 'text' => $product->title);
                }
            }
        }

        return response()->json([
            'tags' => $tempProduct,
            'status' => true,
        ]);
    }
}
