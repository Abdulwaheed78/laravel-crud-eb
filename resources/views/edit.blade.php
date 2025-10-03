@extends('base')

@section('title', 'Edit User')

@section('content')
<div class="container mt-5">
    <h1>Edit User</h1>

    <!-- Display validation errors -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ url('/update/' . $user->id) }}" method="POST" enctype="multipart/form-data" class="mt-4">
        @csrf
        @method('PUT') {{-- Spoof PUT method for Laravel --}}
        
        <div class="mb-3">
            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
            <input 
                type="text" 
                class="form-control" 
                id="name" 
                name="name" 
                value="{{ old('name', $user->name) }}" 
                required
                placeholder="Enter full name"
            >
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
            <input 
                type="email" 
                class="form-control" 
                id="email" 
                name="email" 
                value="{{ old('email', $user->email) }}" 
                required
                placeholder="Enter email address"
            >
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Profile Image</label>
            <input 
                type="file" 
                class="form-control" 
                id="image" 
                name="image" 
                accept="image/*"
            >
            @if ($user->image)
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $user->image) }}" alt="Current Image" width="100" class="rounded">
                    <p class="small text-muted">Current Image</p>
                </div>
            @endif
        </div>

        <button type="submit" class="btn btn-primary">Update User</button>
        <a href="{{ url('/') }}" class="btn btn-danger">Back</a>
    </form>
</div>
@endsection
