<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Jobs\UserJob;
use App\Jobs\ProcessUser;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // Show all users
    public function index()
    {
        $users = User::orderBy('_id', 'desc')->get(); // 10 users per page
        return view('users.index', compact('users'));
    }


    // Store a new user
    public function create(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('users', 'public');
        }

        $user = User::create([
            'name'  => $request->name,
            'email' => $request->email,
            'image' => $imagePath,
        ]);

        // dispatch log job
        UserJob::dispatch('create', [
            'id' => $user->id,
            'first_name' => $user->name,
            'email' => $user->email,
        ], $user->id);

        return redirect()->route('userIndex')->with('success', 'User created successfully!');
    }

    // Show edit form
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    // Update user
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => "required|email|unique:users,email,$id,_id",
            'image' => 'nullable|image|max:2048',
        ]);


        $user = User::findOrFail($id);

        if ($request->hasFile('image')) {
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }
            $user->image = $request->file('image')->store('users', 'public');
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        // dispatch log job
        UserJob::dispatch('update', [
            'id' => $user->id,
            'first_name' => $user->name,
            'email' => $user->email,
        ], $user->id);

        return redirect()->route('userIndex')->with('success', 'User updated successfully!');
    }

    public function export()
    {
        $fileName = 'users_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $users = \App\Models\User::all(['id', 'image', 'name', 'email', 'created_at']);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () use ($users) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Image', 'Name', 'Email', 'Created At']);

            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->id,
                    $user->image,
                    $user->name,
                    $user->email,
                    $user->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $fileName, $headers);
    }


    // Delete user
    public function delete($id)
    {
        $user = User::findOrFail($id);

        if ($user->image && Storage::disk('public')->exists($user->image)) {
            Storage::disk('public')->delete($user->image);
        }

        $user->delete();

        // dispatch log job
        UserJob::dispatch('delete', [
            'id' => $user->id,
            'first_name' => $user->name,
            'email' => $user->email,
        ], $user->id);

        return redirect()->route('userIndex')->with('success', 'User deleted successfully!');
    }


    public function showUploadForm()
    {
        return view('users.upload');
    }

    public function uploadCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $path = $request->file('csv_file')->store('uploads', 'public');

        // Dispatch job to process CSV
        ProcessUser::dispatch($path);
        return redirect()->route('userIndex')->with('success', 'Users CSV uploaded successfully! Processing started...');
    }
}
