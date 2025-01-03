<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\UserProductPermission;

class ProductController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $products = Product::all()->map(function ($product) use ($user) {
            $permission = UserProductPermission::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->first();
            $product->is_active = $permission ? $permission->is_active : false;
            return $product;
        });

        return view('home', compact('products'));
    }
}
