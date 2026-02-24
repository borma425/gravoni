<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);
        $items = [];
        $subtotal = 0;

        foreach ($cart as $key => $item) {
            $product = Product::find($item['product_id']);
            if (!$product) continue;

            $price = $item['price'];
            $qty = (int)($item['quantity'] ?? 1);
            $rowTotal = $price * $qty;
            $subtotal += $rowTotal;

            $items[] = [
                'key' => $key,
                'product' => $product,
                'quantity' => $qty,
                'size' => $item['size'] ?? '',
                'color' => $item['color'] ?? '',
                'price' => $price,
                'row_total' => $rowTotal,
            ];
        }

        return view('store.cart', compact('items', 'subtotal'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:99',
            'size' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
        ]);

        $product = Product::findOrFail($request->product_id);
        $price = $product->discounted_price ?: $product->selling_price;
        $key = $request->product_id . '_' . ($request->size ?? '') . '_' . ($request->color ?? '');

        $cart = session('cart', []);
        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $request->quantity;
        } else {
            $cart[$key] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $request->quantity,
                'size' => $request->size ?? '',
                'color' => $request->color ?? '',
                'price' => (float) $price,
            ];
        }

        session(['cart' => $cart]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تمت الإضافة للسلة',
                'cart_count' => collect($cart)->sum('quantity'),
            ]);
        }

        return redirect()->route('store.cart')->with('success', 'تمت إضافة المنتج للسلة');
    }

    public function update(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $cart = session('cart', []);
        if (isset($cart[$request->key])) {
            $cart[$request->key]['quantity'] = $request->quantity;
            session(['cart' => $cart]);
        }

        return redirect()->route('store.cart')->with('success', 'تم تحديث السلة');
    }

    public function remove(Request $request, string $key)
    {
        $cart = session('cart', []);
        unset($cart[$key]);
        session(['cart' => $cart]);

        return redirect()->route('store.cart')->with('success', 'تم حذف المنتج من السلة');
    }
}
