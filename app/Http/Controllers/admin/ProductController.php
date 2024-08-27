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
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ProductImage;

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

        $productImages = ProductImage::where('product_id', $product->id)->get();
        $subCategories = SubCategory::where('category_id', $product->category_id)->get();
        $categories = Category::orderBy('name', 'ASC')->get();
        $brands = Brand::orderBy('name', 'ASC')->get();

        $relatedProducts = [];
        //fetch related product
        if($product->related_products != ''){
            $productArray = explode(',', $product->related_products);
            $relatedProducts = Product::whereIn('id', $productArray)->with('product_images')->get();
        }
        return view("admin.products.edit", compact("product", "categories", "brands", "subCategories", "productImages", "relatedProducts"));


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
