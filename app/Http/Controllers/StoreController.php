<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(12);
        return view('store.index', compact('products'));
    }

    public function show(Product $product)
    {
        return view('store.show', compact('product'));
    }
}
