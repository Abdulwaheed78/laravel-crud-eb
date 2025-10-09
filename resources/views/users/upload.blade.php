@extends('base')

@section('title', 'Upload Students CSV')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-4">Upload Students CSV</h2>

            {{-- ✅ Upload Form --}}
            <form action="{{ route('users.upload.csv') }}" method="POST" enctype="multipart/form-data" class="mb-4">
                @csrf
                <div class="mb-3">
                    <label for="csv_file" class="form-label">Select CSV File</label>
                    <input type="file" name="csv_file" id="csv_file" class="form-control" required accept=".csv">
                </div>
                <a href="{{ route('userIndex') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left-circle"></i> Back</a>
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-cloud-arrow-up"></i> Upload</button>

            </form>
        </div>
    </div>
</div>
@endsection