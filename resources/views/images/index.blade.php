@extends('base')

@section('title', 'Image Upload Queue')

@section('content')
<div class="container py-5">

    {{-- 🔹 Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Image Upload (Queued via Beanstalkd)</h2>
        <a href="{{ route('images.index') }}" class="btn btn-outline-secondary btn-sm">Refresh</a>
    </div>

    {{-- 🔹 Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- 🔹 Upload Form --}}
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <form action="{{ route('images.store') }}" method="POST" enctype="multipart/form-data" class="row align-items-center g-3">
                @csrf
                <div class="col-md-10">
                    <input type="file" name="image" class="form-control" required>
                </div>
                <div class="col-md-2 text-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-cloud-upload"></i> Queue Upload
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 🔹 Display Stored Images --}}
    <div class="row g-4">
        @forelse($images as $img)
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm h-100 border-0">
                    <img src="{{ $img->base64 }}" class="card-img-top rounded-top" alt="{{ $img->file_name }}" style="height: 200px; object-fit: cover;">
                    <div class="card-body text-center">
                        <p class="card-text text-truncate mb-1">{{ $img->file_name }}</p>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($img->created_at)->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted mb-0">No images uploaded yet.</p>
            </div>
        @endforelse
    </div>

    {{-- 🔹 Pagination --}}
    <div class="mt-4 d-flex justify-content-center">
        {{ $images->links('pagination::bootstrap-5') }}
    </div>

</div>
@endsection
