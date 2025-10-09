@extends('base')

@section('title', 'Students')

@section('content')
<div class="container mt-5">
    <div class="row mb-3">
        <div class="col-6">
            <h1>Student List</h1>
        </div>
        <div class="col-6 text-end">
            <a href="{{ route('students.create') }}" class="btn btn-sm btn-outline-primary me-2">
                <i class="bi bi-plus-circle"></i> Create
            </a>
            <a href="{{ route('students.upload.form') }}" class="btn btn-sm btn-outline-success">
               <i class="bi bi-cloud-arrow-up"></i> Upload CSV 
            </a>
            <a href="{{ route('students.export') }}" class="btn btn-sm btn-outline-dark">
                <i class="bi bi-download"></i> Export CSV
            </a>


        </div>
    </div>

    <div class="card shadow-sm rounded-3">
        <div class="card-body">
            <table id="students-table" class="table table-striped table-hover w-100">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Photo</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Roll No</th>
                        <th>Course</th>
                        <th>Department</th>
                        <th>Batch</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $student)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if ($student->profile_photo)
                            <img src="{{ asset('storage/' . $student->profile_photo) }}" width="40" height="40" class="rounded-circle object-fit-cover">
                            @else
                            <span class="text-muted">No Image</span>
                            @endif
                        </td>
                        <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                        <td>{{ $student->email }}</td>
                        <td>{{ $student->roll_number }}</td>
                        <td>{{ $student->course ?? '-' }}</td>
                        <td>{{ $student->department ?? '-' }}</td>
                        <td>{{ $student->batch ?? '-' }}</td>
                        <td>
                            @if ($student->is_active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        {{-- Action Buttons --}}
                        <td class="text-end">
                            <a href="{{ route('students.show', $student->id) }}" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-eye"></i> View
                            </a>
                            <a href="{{ route('students.edit', $student->id) }}" class="btn btn-sm btn-outline-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this student?')">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection