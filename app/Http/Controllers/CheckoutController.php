<?php

namespace App\Http\Controllers;

use App\Services\SessionCart;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    /**
     * Show the checkout page (cart-based).
     *
     * GET /checkout
     */
    public function page(SessionCart $cart)
    {
        $bag       = $cart->get();        // ['currency' => '...', 'items' => [...]]
        $items     = $bag['items'];       // keyed by "course:ID" / "book:ID"
        $subtotal  = $cart->subtotal();   // sum(final_price * qty)
        $final     = $subtotal;           // add tax/fees here later if needed

        return view('frontend.pages.checkout', [
            'cart'       => $bag,
            'items'      => $items,
            'subtotal'   => $subtotal,
            'finalPrice' => $final,
        ]);
    }

    /**
     * Handle checkout submission for the entire cart.
     *
     * POST /checkout
     */
    public function submit(Request $request, SessionCart $cart)
    {
        $bag = $cart->get();

        if (empty($bag['items'])) {
            return back()->with('error', 'Your cart is empty.');
        }

        // TODO: Integrate your payment flow here.
        // Example steps (no DB required if you don’t want it):
        // - Create a payment intent with $cart->subtotal()
        // - On success, grant access to courses/books
        // - Clear the cart: $cart->clear();

        return back()->with('success', 'Checkout initialized (connect this to your payment gateway).');
    }
}
