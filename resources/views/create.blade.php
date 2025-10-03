@extends('base')

@section('title', 'Users')

@section('content')
<div class="container mt-5">
    <h1>Create User</h1>

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

    <form action="{{ url('/create') }}" method="POST" enctype="multipart/form-data" class="mt-4">
        @csrf

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

        <button type="submit" class="btn btn-primary">Create User</button>
        <a href="/" class="btn btn-danger">Back</a>
    </form>


</div>
@endsection
