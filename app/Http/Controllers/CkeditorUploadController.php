<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CkeditorUploadController extends Controller
{
    public function store(Request $request)
    {
        // CKEditor 5 SimpleUploadAdapter sends the file as "upload"
        $request->validate([
            'upload' => 'required|file|mimes:jpg,jpeg,png,webp,gif|max:5120',
        ]);

        $file = $request->file('upload');

        // Store under storage/app/public/pages/editor/YYYY/mm
        $subdir = 'pages/editor/'.date('Y').'/'.date('m');
        $name   = Str::random(16).'.'.$file->getClientOriginalExtension();

        $path = $file->storeAs($subdir, $name, 'public'); // disk=public
        $url  = '/storage/'.$path; // public URL

        // CKEditor expects { url: '...' }
        return response()->json(['url' => $url]);
    }
}
