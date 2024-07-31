<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        // Find the product by ID, including its images
        $product = Product::with('product_images')->find($request->id);

        // Check if the product exists
        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found',
            ]);
        }

        // Check if the product is already in the cart
        $cartContent = Cart::content();
        $productAlreadyExist = $cartContent->contains(function ($item) use ($product) {
            return $item->id == $product->id;
        });

        // Determine the message and status based on product existence in cart
        // $id, $name, $qty, $price, $options, $taxrate
        if (!$productAlreadyExist) {
            Cart::add($product->id, $product->title, 1, $product->price, [
                'productImage' => $product->product_images->isNotEmpty() ? $product->product_images->first() : '',
            ]);
            $status = true;
            $message = $product->title . ' added to cart successfully';
        } else {
            $status = false;
            $message = 'already exists in the cart';
        }

        // Return the response as JSON
        return response()->json([
            'status' => $status,
            'message' => $message,
        ]);
    }


    public function cart()
    {
        $categories = Category::orderBy("name", "ASC")->with(['sub_categories' => function ($query) {
            $query->where('showHome', 'Yes');
        }])->where("showHome", "Yes")->get();

        $cartContent = Cart::content();
        return view("front.cart", compact("categories","cartContent"));
    }
}
