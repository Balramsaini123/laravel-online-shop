<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request, $categorySlug = null, $subCategorySlug = null)
    {
        $categorySelected = '';
        $subCategorySelected = '';
        $brandsArray = [];

        $categories = Category::orderBy("name", "ASC")->with(['sub_categories' => function ($query) {
            $query->where('showHome', 'Yes');
        }])->where("showHome", "Yes")->get();
        $brands = Brand::orderBy('name', 'ASC')->where('status', 1)->get();

        $products = Product::where('status', 1);

        //Apply filter

        if (!empty($categorySlug)) {
            $category = Category::where('slug', $categorySlug)->first();
            $products = $products->where('category_id', $category->id);
            $categorySelected = $category->id;
        }

        if (!empty($subCategorySlug)) {
            $subcategory = SubCategory::where('slug', $subCategorySlug)->first();
            $products = $products->where('sub_category_id', $subcategory->id);
            $subCategorySelected = $subcategory->id;
        }

        if (!empty($request->get("brand"))) {
            $brandsArray = explode(',', $request->get("brand"));
            $products = $products->whereIn('brand_id', $brandsArray);
        }

        if ($request->get('price_max') != '' && $request->get('price_min') != '') {
            $products = $products->whereBetween('price', [intval($request->get('price_min')), intval($request->get('price_max'))]);
        }

        $products = $products->orderBy('id', 'DESC');
        $products = $products->paginate(5);

        $priceMax = (intval($request->get('price_max')) == 0) ? 100000 : $request->get('price_max');
        $priceMin = intval($request->get('price_min'));

        return view("front.shop", compact("categories", "brands", "products", "categorySelected", "subCategorySelected", "brandsArray", "priceMax", "priceMin"));
    }

    public function product($slug)
    {
        $categories = Category::orderBy("name", "ASC")->with(['sub_categories' => function ($query) {
            $query->where('showHome', 'Yes');
        }])->where("showHome", "Yes")->get();
        $product = Product::where('slug', $slug)->with('product_images')->first();

        if ($product == null) {
            abort(404);
        }

        $relatedProducts = [];
        //fetch related product
        if($product->related_products != ''){
            $productArray = explode(',', $product->related_products);
            $relatedProducts = Product::whereIn('id', $productArray)->get();
        }

        return view("front.product", compact("product","categories","relatedProducts"));
    }
}
