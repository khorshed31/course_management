<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SessionCart;
use App\Models\Course;
use App\Models\Book;

class CartController extends Controller
{
public function add(Request $request, SessionCart $cart)
{
    $data = $request->validate([
        'type' => 'required|in:course,book',
        'slug' => 'required',
    ]);

    $item = null;
    if ($data['type'] === 'course') {
        $item = Course::where('slug', $data['slug'])->first();
    } elseif ($data['type'] === 'book') {
        $item = Book::where('slug', $data['slug'])->first();
    }

    if (!$item) {
        return response()->json([
            'ok' => false,
            'message' => 'Item not found',
        ]);
    }

    // Check if the item is already in the cart
    $cartItems = $cart->get()['items'];
    $existingItemKey = null;

    foreach ($cartItems as $key => $cartItem) {
        if ($cartItem['item_type'] === $data['type'] && $cartItem['slug'] === $item->slug) {
            $existingItemKey = $key;
            break;
        }
    }

    if ($existingItemKey !== null) {
        return response()->json([
            'ok' => false,
            'message' => 'This item is already in your cart.',
        ]);
    }

    // Add item to the cart
    $cart->add($data['type'], $item->slug);

    // Prepare cart data for frontend
    $cartData = $cart->get();

    return response()->json([
        'ok'          => true,
        'message'     => 'Item added to your cart!',
        'cart_count'  => $cart->totalQty(),
        'cart_total'  => $cart->totalPrice(),          // numeric total
        'currency'    => $cartData['currency'] ?? 'د.ك',
        // 'old_total' => ... // add if you need original price (before discount)
    ]);
}

    public function remove(Request $request, SessionCart $cart)
    {
        $data = $request->validate(['key' => 'required|string']);
        $cart->remove($data['key']);
        return back()->with('success', 'Item removed.');
    }

    public function removeFromCart(Request $request, SessionCart $cart)
    {
        // Validate the key of the item to be removed
        $data = $request->validate(['slug' => 'required|string']);

        // Remove the item
        $cart->remove($data['slug']);

        return response()->json([
            'ok' => true,
            'message' => 'Item removed from cart.',
            'cart_count' => $cart->totalQty(),
        ]);
    }

    public function clear(SessionCart $cart)
    {
        $cart->clear();
        return back()->with('success', 'Cart cleared.');
    }
}
