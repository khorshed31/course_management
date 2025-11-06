<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CustomPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\FileSaver;

class CustomPageController extends Controller
{
    use FileSaver;

    public function index()
    {
        $pages = CustomPage::orderBy('position')->orderByDesc('id')->paginate(15);
        return view('panel.pages.custom_pages.index', compact('pages'));
    }

    public function create()
    {
        $page = new CustomPage();
        return view('panel.pages.custom_pages.form', compact('page'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:150',
            'icon'         => 'nullable|string|max:100',
            'is_published' => 'nullable|boolean',
            'position'     => 'nullable|integer|min:0|max:100000',
            'content'      => 'nullable|string',
            'attachment'   => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,pdf|max:5120',
        ]);
        $data['is_published'] = (bool)($data['is_published'] ?? false);

        DB::transaction(function () use (&$data, $request) {
            $page = CustomPage::create($data);

            if ($request->hasFile('attachment')) {
                
                // upload_file($uploaded_file, $model, $db_field, $base_path)
                $this->upload_file($request->file('attachment'), $page, 'attachment', 'pages');
               
            }
        });

        return redirect()->route('admin.custom-pages.index')->with('success', 'Page created.');
    }

    public function edit(CustomPage $custom_page)
    {
        $page = $custom_page;
        return view('panel.pages.custom_pages.form', compact('page'));
    }

    public function update(Request $request, CustomPage $custom_page)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:150',
            'icon'         => 'nullable|string|max:100',
            'is_published' => 'nullable|boolean',
            'position'     => 'nullable|integer|min:0|max:100000',
            'content'      => 'nullable|string',
            'attachment'   => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,pdf|max:5120',
            'remove_attachment' => 'nullable|boolean',
        ]);
        $data['is_published'] = (bool)($data['is_published'] ?? false);

        DB::transaction(function () use (&$data, $request, $custom_page) {
            $custom_page->update($data);

            // Remove old file if requested
            if ($request->boolean('remove_attachment') && $custom_page->attachment) {
                $abs = public_path($custom_page->attachment);
                if (is_file($abs)) @unlink($abs);
                $custom_page->update(['attachment' => null]);
            }

            // Upload new file
            if ($request->hasFile('attachment')) {
                $this->upload_file($request->file('attachment'), $custom_page, 'attachment', 'pages');
            }
        });

        return redirect()->route('admin.custom-pages.index')->with('success', 'Page updated.');
    }

    public function destroy(CustomPage $custom_page)
    {
        if ($custom_page->attachment) {
            $abs = public_path($custom_page->attachment);
            if (is_file($abs)) @unlink($abs);
        }
        $custom_page->delete();

        return back()->with('success', 'Page deleted.');
    }
}
