<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Jobs\UserJob;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // Show all users
    public function index()
    {
        $users = User::all();
        return view('index', compact('users'));
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
        UserJob::dispatch('create', [], $user->id);

        return redirect()->route('index')->with('success', 'User created successfully!');
    }

    // Show edit form
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('edit', compact('user'));
    }

    // Update user
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => "required|email|unique:users,email,$id",
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
        UserJob::dispatch('update', [], $user->id);

        return redirect()->route('index')->with('success', 'User updated successfully!');
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
        UserJob::dispatch('delete', [], $id);

        return redirect()->route('index')->with('success', 'User deleted successfully!');
    }
}
