@extends('base')

@section('title', 'Image Upload Queue')

@section('content')
<div class="container py-5">

    {{-- 🔹 Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Image Upload (Queued via Beanstalkd)</h2>
        <a href="{{ route('images.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-clockwise"></i> Refresh</a>
    </div>

    {{-- 🔹 Upload Form --}}
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-body">
            <form action="{{ route('images.store') }}" method="POST" enctype="multipart/form-data" class="row align-items-center g-3">
                @csrf
                <div class="col-md-10">
                    <input type="file" name="image" class="form-control" required>
                </div>
                <div class="col-md-2 text-end">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-cloud-upload"></i>  Upload
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 🔹 Display Images Table (DataTable enabled) --}}
    <div class="card shadow-sm p-3">
        <div class="table-responsive">
            <table id="students-table" class="table table-striped table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Preview</th>
                        <th>File Name</th>
                        <th>Uploaded</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($images as $index => $img)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <img src="{{ $img->base64 }}" alt="{{ $img->file_name }}" width="80" height="80" class="rounded shadow-sm" style="object-fit: cover;">
                        </td>
                        <td>{{ $img->file_name }}</td>
                        <td>{{ \Carbon\Carbon::parse($img->created_at)->diffForHumans() }}</td>
                        <td class="text-center">
                            <form action="{{ route('images.delete', $img->_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this image?')" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No images uploaded yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection