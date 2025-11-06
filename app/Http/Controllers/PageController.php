<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\CustomPage;

class PageController extends Controller
{
    public function show(string $slug)
    {
        $page = CustomPage::where('slug',$slug)->where('is_published', true)->firstOrFail();
        return view('panel.pages.custom_pages.pages', compact('page'));
    }
}
