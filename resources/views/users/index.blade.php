@extends('base')

@section('title', 'Users')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-6">
            <h1>User List</h1>
        </div>
        <div class="col-6 text-end">
            <a href="/user/create" class="btn btn-sm btn-outline-primary"><i class="bi bi-plus-circle"></i> Create</a>
            <a href="{{ route('users.upload.form') }}" class="btn btn-sm btn-outline-success"><i class="bi bi-cloud-arrow-up"></i> Upload CSV</a>
            <a href="{{ route('users.export') }}" class="btn btn-sm btn-outline-dark">
                <i class="bi bi-download"></i> Export CSV
            </a>
        </div>
    </div>

    {{-- ✅ Users Table inside a Card --}}
    <div class="card shadow-sm mt-4">
        <div class="card-body p-3">
            <table id="students-table" class="table table-striped table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $index => $user)
                    <tr>
                        {{-- Maintain numbering --}}
                        <td>{{ $index + 1 }}</td>

                        {{-- Image --}}
                        <td>
                            @if ($user->image)
                            <img src="{{ asset('storage/' . $user->image) }}" alt="User Image"
                                width="40" height="40" class="rounded">
                            @else
                            <span class="text-muted">No Image</span>
                            @endif
                        </td>

                        {{-- Name / Email --}}
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>

                        {{-- Actions --}}
                        <td class="text-center">
                            <a href="{{ url('/user/edit/' . $user->id) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil-square"></i> Edit</a>
                            <a href="{{ url('/user/delete/' . $user->id) }}"
                                class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('Are you sure you want to delete this user?');">
                                <i class="bi bi-trash3"></i> Delete
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection