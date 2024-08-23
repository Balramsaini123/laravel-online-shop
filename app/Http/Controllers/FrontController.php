<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy("name","ASC")->with(['sub_categories' => function ($query) {
            $query->where('showHome', 'Yes');
        }])->where("showHome","Yes")->get();

        $featureProducts = Product::where("is_featured","Yes")
                                    ->orderBy('id','DESC')
                                    ->take(8)
                                    ->where("status","1")->get();
        $latestProducts = Product::orderBy('id','DESC')
                                    ->where('status','1')
                                    ->take(8)->get();
                                    
        return view('front.home',compact("categories","featureProducts","latestProducts"));
    }
}
