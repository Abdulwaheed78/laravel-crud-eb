@extends('base')

@section('title', 'Upload Students CSV')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-4">Upload Students CSV</h2>

            {{-- ✅ Flash Messages --}}
            @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @elseif (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- ✅ Upload Form --}}
            <form action="{{ route('students.upload.csv') }}" method="POST" enctype="multipart/form-data" class="mb-4">
                @csrf
                <div class="mb-3">
                    <label for="csv_file" class="form-label">Select CSV File</label>
                    <input type="file" name="csv_file" id="csv_file" class="form-control" required accept=".csv">
                </div>
                <button type="submit" class="btn btn-primary">Upload</button>
                <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">Back</a>

            </form>
        </div>
    </div>
</div>
@endsection