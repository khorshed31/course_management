<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookRequest;
use App\Models\Book;
use App\Models\BookPurchase;
use App\Traits\FileSaver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class BookController extends Controller
{
    use FileSaver;

    public function index()
    {
        $books = Book::with('uploader')->latest()->paginate(20);
        return view('panel.pages.books.index', compact('books'));
    }

    public function create()
    {
        return view('panel.pages.books.create');
    }

    public function store(BookRequest $request)
    {
        $book = Book::create([
            'uploaded_by'  => auth()->id(),
            'title'        => $request->title,
            'slug'         => Str::slug($request->title) . '-' . Str::random(6),
            'author'       => $request->author,
            'description'  => $request->description,
            'pages'        => $request->pages,
            'price'        => $request->price,
            'status'       => $request->status,
            'published_at' => $request->status === 'published'
                                ? ($request->published_at ?: now())
                                : null,
            // file columns filled after trait uploads
            'cover_path'   => null,
            'file_path'    => '', // will be set below
        ]);

        if ($request->hasFile('cover')) {
            // DB column name = cover_path, base dir = books/covers
            $this->upload_file($request->file('cover'), $book, 'cover_path', 'books/covers');
        }
        if ($request->hasFile('pdf')) {
            // DB column name = file_path, base dir = books/pdfs
            $this->upload_file($request->file('pdf'), $book, 'file_path', 'books/pdfs');
        }

        return redirect()->route('admin.books.index')->with('success', 'Book created.');
    }

    public function edit(Book $book)
    {
        return view('panel.pages.books.edit', compact('book'));
    }

    public function update(BookRequest $request, Book $book)
    {
        $book->update([
            'title'        => $request->title,
            'author'       => $request->author,
            'description'  => $request->description,
            'pages'        => $request->pages,
            'price'        => $request->price,
            'status'       => $request->status,
            'published_at' => $request->status === 'published'
                                ? ($request->published_at ?: ($book->published_at ?? now()))
                                : null,
        ]);

        if ($request->hasFile('cover')) {
            $this->upload_file($request->file('cover'), $book, 'cover_path', 'books/covers');
        }
        if ($request->hasFile('pdf')) {
            $this->upload_file($request->file('pdf'), $book, 'file_path', 'books/pdfs');
        }

        return redirect()->route('admin.books.index')->with('success', 'Book updated.');
    }

    public function destroy(Book $book)
    {
        // delete physical files
        foreach (['cover_path','file_path'] as $field) {
            if ($book->$field) {
                $abs = public_path($book->$field);
                if (is_file($abs)) @unlink($abs);
            }
        }
        $book->delete();
        return redirect()->route('admin.books.index')->with('success', 'Book deleted.');
    }

    // Optional: stream/download + increment counter
    public function download(Book $book)
    {
        $path = public_path($book->file_path);
        abort_unless(is_file($path), 404);

        // atomic increment
        $book->increment('downloads_count');

        return Response::download($path, Str::slug($book->title).'.pdf', [
            'Content-Type' => 'application/pdf'
        ]);
    }
    public function preview(Book $book)
    {
    
        $abs = public_path($book->file_path);
        abort_unless(is_file($abs), 404, 'PDF file not found.');

        // Return inline (no forced download)
        return Response::file($abs, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.Str::slug($book->title).'.pdf"',
            'X-Frame-Options'     => 'SAMEORIGIN', // keep embedding friendly within same site
        ]);
    }

    // Optional: public show page
    public function purchase_book(Request $request)
    {
        $user = $request->user();

        // Eager-load the related Book for each purchase
        $purchases = BookPurchase::with(['book' => function ($q) {
                $q->select('id','slug','title','author','cover_path','price','file_path','status','published_at');
            }])
            ->where('user_id', $user->id)
            ->where('status', 'paid') // only fully purchased
            ->latest()
            ->paginate(12);

        return view('panel.pages.library.index', compact('purchases'));
    }
}
