@extends('base')

@section('title', 'Edit User')

@section('content')
<div class="container d-flex justify-content-center align-items-center mt-5">
    <div class="card shadow-sm border-0 rounded-3 p-4" style="max-width: 600px; width: 100%;">
        {{-- Edit Form --}}
        <form action="{{ url('/user/update/' . $user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Name --}}
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

            {{-- Email --}}
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

            {{-- Profile Image --}}
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
                    <div class="mt-3 text-center">
                        <img src="{{ asset('storage/' . $user->image) }}" alt="Current Image" width="120" class="rounded shadow-sm border">
                        <p class="small text-muted mt-2">Current Image</p>
                    </div>
                @endif
            </div>

            {{-- Buttons --}}
            <div class="d-flex justify-content-end mt-4 gap-3">
                <a href="{{ url('/user/index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left-circle"></i> Back</a>
                <button type="submit" class="btn btn-sm btn-outline-primary">Submit</button>
            </div>
        </form>
    </div>
</div>
@endsection
