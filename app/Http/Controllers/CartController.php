<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SessionCart;

class CartController extends Controller
{
    public function add(Request $request, SessionCart $cart)
    {
        $data = $request->validate([
            'type' => 'required|in:course,book',
            'slug' => 'required',
        ]);

        $cart->add($data['type'], $data['slug']);

        return redirect()->route('checkout.page')->with('success', 'Item added to cart.');
    }

    public function remove(Request $request, SessionCart $cart)
    {
        $data = $request->validate(['key' => 'required|string']);
        $cart->remove($data['key']);
        return back()->with('success', 'Item removed.');
    }

    public function clear(SessionCart $cart)
    {
        $cart->clear();
        return back()->with('success', 'Cart cleared.');
    }
}
