<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Country;

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

    public function updateCart(Request $request)
    {
        $rowId = $request->rowId;
        $qty = $request->qty;

        $itemInfo = Cart::get($rowId);
        $product = Product::find($itemInfo->id);

        if($product->track_qty == 'Yes'){
            if($qty <= $product->qty){
                Cart::update($rowId, $qty);
                $message = 'Cart updated successfully';
                $status = true;
            }else{
                $message = 'Out of stock';
                $status = false;
            }
        }else{
            Cart::update($rowId, $qty);
            $message = 'Cart updated successfully';
            $status = true;
        }
        
        Session::flash('flashMessage', $message);

        return response()->json([
            'status' => $status,
            'message' => $message,
        ]);
    }

    public function deleteItem(Request $request)
    {
        Cart::remove($request->rowId);
        
        return response()->json([
            'status' => true,
            'message' => 'Item removed successfully',
        ]);
    }

    public function checkout()
    {
        $categories = Category::orderBy("name", "ASC")->with(['sub_categories' => function ($query) {
            $query->where('showHome', 'Yes');
        }])->where("showHome", "Yes")->get();

        if (Cart::count() == 0) {
            return redirect()->route('front.cart');
        }

        if (Auth::check() == false) {

            if (!session()->has('url.intended')) {
                session(['url.intended' => url()->current()]);
            }
            
            return redirect()->route('account.login');
        }

        session()->forget('url.intended');

        $cartContent = Cart::content();
        $countries = Country::orderBy("name", "ASC")->get();
        return view("front.checkout", compact("categories","cartContent","countries"));
    }
}
