<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\StoreImageJob;
use App\Models\Image;
use Illuminate\Support\Facades\Log;


class ImageController extends Controller
{
    public function index()
    {
        $images = Image::latest()->paginate(10);
        return view('images.index', compact('images'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        $file = $request->file('image');
        $tempPath = $file->store('tmp', 'local'); // ✅ stored temporarily in storage/app/tmp

        // Queue only metadata + temp path (not file content)
        StoreImageJob::dispatch(
            $file->getClientOriginalName(),
            $file->getMimeType(),
            $tempPath
        );

        Log::info('📦 Image queued to Beanstalkd', ['path' => $tempPath]);

        return redirect()->back()->with('success', 'Image upload queued!');
    }
}
