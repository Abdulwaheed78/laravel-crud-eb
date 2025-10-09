<?php

namespace App\Http\Controllers;

use App\Events\ImageActionEvent;
use Illuminate\Http\Request;
use App\Jobs\StoreImageJob;
use App\Models\Image;
use Illuminate\Support\Facades\Log;


class ImageController extends Controller
{
    public function index()
    {
        $images = Image::latest()->get();
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



    public function delete($id)
    {
        $image = Image::find($id);
        $image->delete();
        event(new ImageActionEvent('deleted', $image->_id, true));
        return redirect()
            ->route('images.index')
            ->with([
                'success' => 'Image is scheduled for delete operation.',
                'info' => 'The queue worker will process it soon.',
            ]);
    }
}
