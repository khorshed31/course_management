<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Book;

class SessionCart
{
    protected string $key = 'cart'; // session key

    public function get(): array
    {
        return session()->get($this->key, [
            'currency' => 'د.ك',
            'items'    => [], // each: key => [item_type,item_id,title,slug,image_path,unit_price,final_price]
        ]);
    }

    protected function put(array $cart): void
    {
        session()->put($this->key, $cart);
    }

    public function clear(): void
    {
        session()->forget($this->key);
    }

    public function subtotal(): float
    {
        $cart = $this->get();
        return (float) array_sum(array_map(function ($it) {
            return (float)$it['final_price'];
        }, $cart['items']));
    }

    public function totalQty(): int
    {
        $cart = $this->get();
        return count($cart['items']); // Only count unique items
    }

    public function totalPrice(): float
    {
        $cart = $this->get();
        // Sum the 'final_price' for each item in the cart
        return (float) array_sum(array_map(function ($item) {
            return (float) $item['final_price'];
        }, $cart['items']));
    }

    /**
     * Add a course or book to the cart.
     * No duplicates, only one of each course.
     */
    public function add(string $type, string $slug): void
    {
        $cart = $this->get();

        // If it's a course
        if ($type === 'course') {
            $course = Course::where('slug', $slug)->firstOrFail();
            $base  = (float) $course->price;
            $promo = app(\App\Services\PromotionService::class)->bestForCourse($course->id);
            $final = $promo
                ? app(\App\Services\PromotionService::class)->applyDiscount($base, $promo)
                : $base;

            $payload = [
                'item_type'   => 'course',
                'item_id'     => $course->id,
                'title'       => $course->title,
                'slug'        => $course->slug,
                'image_path'  => $course->image ?? $course->thumbnail ?? null,
                'unit_price'  => $base,
                'final_price' => $final,
            ];
        } else { // it's a book
            $book = Book::where('slug', $slug)->firstOrFail();
            $base  = (float) $book->price;
            $final = $base;

            $payload = [
                'item_type'   => 'book',
                'item_id'     => $book->id,
                'title'       => $book->title,
                'slug'        => $book->slug,
                'image_path'  => $book->cover_path ?? $book->thumbnail ?? null,
                'unit_price'  => $base,
                'final_price' => $final,
            ];
        }

        // Use the unique identifier for each item
        $key = "{$payload['item_type']}:{$payload['item_id']}";

        // Only add to the cart if not already present
        if (!isset($cart['items'][$key])) {
            $cart['items'][$key] = $payload;
        }

        $this->put($cart);
    }

    public function remove(string $key): void
    {
        $cart = $this->get();
        unset($cart['items'][$key]);
        $this->put($cart);
    }
}