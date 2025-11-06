<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class BookPublicController extends Controller
{
    public function buy(Request $request, Book $book)
    {
        // Hook into your real checkout here if you have one.
        // For demo: create/ensure a paid purchase.
        BookPurchase::firstOrCreate(
            ['user_id' => $request->user()->id, 'book_id' => $book->id],
            ['amount_cents' => (int) ($book->price * 100), 'status' => 'paid']
        );

        return back()->with('success', 'Purchase successful. You can now preview and download.');
    }

    public function preview(Request $request, Book $book)
    {
        // must be published and purchased (or uploader/admin—adjust if you like)
        abort_unless($this->canAccess($request->user()->id, $book), 403, 'Please buy this book to preview.');

        $abs = public_path($book->file_path);
        abort_unless(is_file($abs), 404, 'PDF not found.');

        return Response::file($abs, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.Str::slug($book->title).'.pdf"',
        ]);
    }

    public function download(Request $request, Book $book)
    {
        abort_unless($this->canAccess($request->user()->id, $book), 403, 'Please buy this book to download.');

        $abs = public_path($book->file_path);
        abort_unless(is_file($abs), 404, 'PDF not found.');

        $book->increment('downloads_count');

        return Response::download($abs, Str::slug($book->title).'.pdf', [
            'Content-Type' => 'application/pdf'
        ]);
    }

    private function canAccess(int $userId, Book $book): bool
    {
        $isPublished = $book->status === 'published' && $book->published_at && $book->published_at->isPast();
        $purchased = $book->purchases()
            ->where('user_id', $userId)
            ->where('status', 'paid')
            ->exists();

        // Uploader always allowed too (optional)
        $isUploader = $book->uploaded_by === $userId;

        return $isPublished && ($purchased || $isUploader);
    }
}
