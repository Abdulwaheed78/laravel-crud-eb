<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;  // Import User model
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
        // Validate inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'image' => 'nullable|image|max:2048',  // max 2MB image
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('users', 'public');
        }

        // Create user
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'image' => $imagePath,
        ]);

        return redirect()->route('index')->with('success', 'User created successfully!');
    }

    // Show edit form
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('edit', compact('user'));
    }

    // Update user info
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validate inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,$id",
            'image' => 'nullable|image|max:2048',
        ]);

        // Handle image update
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }
            $imagePath = $request->file('image')->store('users', 'public');
            $user->image = $imagePath;
        }

        // Update fields
        $user->name = $request->name;
        $user->email = $request->email;

        $user->save();

        return redirect()->route('index')->with('success', 'User updated successfully!');
    }

    // Delete user
    public function delete($id)
    {
        $user = User::findOrFail($id);

        // Delete image file if exists
        if ($user->image && Storage::disk('public')->exists($user->image)) {
            Storage::disk('public')->delete($user->image);
        }

        $user->delete();

        return redirect()->route('index')->with('success', 'User deleted successfully!');
    }
}
