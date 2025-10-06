@extends('base')

@section('title', 'Create User')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow-sm border-0 rounded-3 p-4" style="max-width: 600px; width: 100%;">

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Create Form --}}
        <form action="{{ url('/user/create') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Name --}}
            <div class="mb-3">
                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}" 
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
                    value="{{ old('email') }}" 
                    required
                    placeholder="Enter email address"
                >
            </div>

            {{-- Profile Image --}}
            <div class="mb-3">
                <label for="image" class="form-label">Profile Image <span class="text-danger">*</span></label>
                <input 
                    type="file" 
                    class="form-control" 
                    id="image" 
                    name="image" 
                    accept="image/*" 
                    required
                >
            </div>

            {{-- Buttons --}}
            <div class="d-flex justify-content-end mt-4 gap-3">
                <a href="{{ url('/user/index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                <button type="submit" class="btn btn-sm btn-primary">Create User</button>
            </div>
        </form>
    </div>
</div>
@endsection
