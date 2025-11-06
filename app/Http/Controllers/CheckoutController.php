<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Book;
use App\Services\PromotionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    protected $promotionService;

    public function __construct(PromotionService $promotionService)
    {
        $this->promotionService = $promotionService;
    }

    /**
     * Show the checkout page for a Course or Book.
     *
     * GET /checkout/{type}/{slug}
     */
    public function page(string $type, string $slug)
    {
        // Resolve item by type
        if ($type === 'course') {
            $item = Course::where('slug', $slug)->firstOrFail();

            // Course-specific promotion (keep your existing logic)
            $promotion  = $this->promotionService->bestForCourse($item->id);
            $finalPrice = $promotion
                ? $this->promotionService->applyDiscount($item->price, $promotion)
                : $item->price;

        } elseif ($type === 'book') {
            $item       = Book::where('slug', $slug)->firstOrFail();
            $promotion  = null;                 // If you later add book promos, adjust here
            $finalPrice = $item->price;         // No discount by default
        } else {
            abort(404);
        }

        // Render a type-aware checkout blade
        // NOTE: Update your blade to read $type and $item (instead of only $course).
        return view('frontend.pages.checkout', [
            'type'       => $type,       // 'course' or 'book'
            'item'       => $item,       // Course|Book model
            'promotion'  => $promotion,
            'finalPrice' => $finalPrice,
        ]);
    }

    /**
     * Handle the Buy Now submission (both course & book).
     *
     * POST /checkout
     */
    public function submit(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:course,book',
            'slug' => 'required|string',
        ]);

        if ($data['type'] === 'course') {
            $item = Course::where('slug', $data['slug'])->firstOrFail();
            $promotion  = $this->promotionService->bestForCourse($item->id);
            $amount     = $promotion
                ? $this->promotionService->applyDiscount($item->price, $promotion)
                : $item->price;

        

        } else { // book
            $item    = Book::where('slug', $data['slug'])->firstOrFail();
            $amount  = $item->price;
            
        }

        
        // $order = Order::create([...]);
        // return redirect()->route('payment.gateway', $order);

        return back()->with('success', 'Checkout initialized (wire this to your payment flow).');
    }
}
