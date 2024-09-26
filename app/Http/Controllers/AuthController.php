<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Category;
use App\Models\User;

class AuthController extends Controller
{
    public function login()
    {
        $categories = Category::orderBy("name","ASC")->with(['sub_categories' => function ($query) {
            $query->where('showHome', 'Yes');
        }])->where("showHome","Yes")->get();

        return view('front.account.login',compact("categories"));
    }

    public function register()
    {
        $categories = Category::orderBy("name","ASC")->with(['sub_categories' => function ($query) {
            $query->where('showHome', 'Yes');
        }])->where("showHome","Yes")->get();

        return view('front.account.register', compact("categories"));
    }

    public function processRegister(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'phone' => 'required',
        ]);

        if ($validator->passes()) {

            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->phone = $request->phone;
            $user->save();

            Session::flash('success', 'User account created successfully');

            return response()->json([
                'status' => true,
            ]);
        } else {

            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ]);
        }
    }

    public function authenticate(Request $request)
    {
        $cridentials = $request->only('email', 'password');
        if (auth()->attempt($cridentials)) {

            if (session()->has('url.intended')) {
                return redirect(session()->get('url.intended'));
            }

            return redirect()->route('account.profile');
        } else {
            return redirect()->back()->with('error', 'Invalid email or password');
        }
    }

    public function profile()
    {
        $categories = Category::orderBy("name","ASC")->with(['sub_categories' => function ($query) {
            $query->where('showHome', 'Yes');
        }])->where("showHome","Yes")->get();
        return view('front.account.profile',compact("categories"));
    }
    public function logout()
    {
        auth()->logout();
        return redirect()->route('account.login')->with('success', 'You have been logged out successfully.');
    }
}
