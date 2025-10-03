@extends('base')

@section('title', 'Users')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class=" col-6">
            <h1>User List</h1>
        </div>
        <div class=" col-6 text-end">
            <a href="/create" class="btn btn-primary">Create + </a>
        </div>
    </div>


    {{-- Show incoming flash messages --}}
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    {{-- Users table --}}
    <table class="table table-striped table-bordered mt-4">
        <thead class="table-info">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Image</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $index => $user)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if ($user->image)
                    <img src="{{ asset('storage/' . $user->image) }}" alt="User Image" width="60" height="60" class="rounded-circle">
                    @else
                    <span class="text-muted">No Image</span>
                    @endif
                    <a href="{{ url('/edit/' . $user->id) }}" class="btn btn-info">Edit</a>
                    <a href="{{ url('/delete/' . $user->id) }}" class="btn btn-danger">Del</a>

                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">No users found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection