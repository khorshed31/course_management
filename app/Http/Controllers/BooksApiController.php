<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class BooksApiController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 9);
        $page    = (int) $request->input('page', 1);

        $books = Book::query()
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->paginate($perPage, ['*'], 'page', $page);

        $html = View::make('frontend.pages.book_cards', compact('books'))->render();

        return response()->json([
            'html'         => $html,
            'current_page' => $books->currentPage(),
            'last_page'    => $books->lastPage(),
            'total'        => $books->total(),
        ]);
    }
}