@extends('base')

@section('title', 'Student Details')

@section('content')
<div class="container my-5">
    <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
        <div class="row g-0">

            {{-- Left Side: Profile --}}
            <div class="col-md-4 bg-light d-flex flex-column align-items-center justify-content-center py-4">
                <div class="text-center">
                    @if ($student->profile_photo)
                    <img src="{{ asset('storage/' . $student->profile_photo) }}"
                        alt="Profile Photo" class="rounded-circle mb-3"
                        width="140" height="140" style="object-fit: cover;">
                    @else
                    <img src="data:image/png;base64,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"
                        alt="No Photo" height="140" class="rounded-circle mb-3">
                    @endif
                    <h5 class="fw-bold">{{ $student->first_name }} {{ $student->last_name }}</h5>
                    <p class="text-muted mb-1">{{ $student->department ?? 'N/A' }}</p>
                    <p>
                        @if ($student->is_active)
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </p>
                    @if ($student->has_scholarship)
                    <span class="badge bg-info text-dark">Scholarship</span>
                    @endif
                </div>
            </div>

            {{-- Right Side: Details --}}
            <div class="col-md-8">
                <div class="card-body p-4">
                    <h4 class="card-title mb-4">Student Information</h4>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Email:</strong> {{ $student->email ?? 'N/A' }}
                        </div>
                        <div class="col-md-6">
                            <strong>Phone:</strong> {{ $student->phone ?? 'N/A' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Roll Number:</strong> {{ $student->roll_number }}
                        </div>
                        <div class="col-md-6">
                            <strong>Age:</strong> {{ $student->age ?? '-' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Gender:</strong> {{ ucfirst($student->gender) ?? '-' }}
                        </div>
                        <div class="col-md-6">
                            <strong>Date of Birth:</strong>
                            {{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('d M Y') : '-' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Admission Date:</strong>
                            {{ $student->admission_date ? \Carbon\Carbon::parse($student->admission_date)->format('d M Y, h:i A') : '-' }}
                        </div>
                        <div class="col-md-6">
                            <strong>Class Time:</strong> {{ $student->class_time ?? '-' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Course:</strong> {{ $student->course ?? '-' }}
                        </div>
                        <div class="col-md-6">
                            <strong>Batch:</strong> {{ $student->batch ?? '-' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Grade:</strong> {{ $student->grade ?? '-' }}
                        </div>
                        <div class="col-md-6">
                            <strong>Favorite Color:</strong>
                            <span class="badge"
                                style="background-color: {{ $student->favorite_color ?? '#000' }}">
                                {{ $student->favorite_color ?? '#000000' }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Address:</strong><br>
                        {{ $student->address ?? '—' }}
                    </div>

                    <div class="mb-3">
                        <strong>Bio:</strong><br>
                        <p class="mb-0">{{ $student->bio ?? '—' }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>Website:</strong><br>
                        @if ($student->website)
                        <a href="{{ $student->website }}" target="_blank">{{ $student->website }}</a>
                        @else
                        —
                        @endif
                    </div>
                    @php
                    $hobbies = $student->hobbies;

                    if (is_string($hobbies)) {
                    $decoded = json_decode($hobbies, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $hobbies = $decoded;
                    } elseif ($hobbies === '' || $hobbies === null) {
                    $hobbies = [];
                    } else {
                    $hobbies = array_filter(array_map('trim', explode(',', $hobbies)));
                    }
                    }

                    if (!is_array($hobbies)) {
                    $hobbies = [];
                    }
                    @endphp

                    @if (!empty($hobbies))
                    @foreach ($hobbies as $hobby)
                    <span class="badge bg-secondary me-1">{{ $hobby }}</span>
                    @endforeach
                    @else
                    <span class="text-muted">No hobbies listed</span>
                    @endif

                    <div class="mt-4 d-flex justify-content-end gap-3">
                        <a href="{{ route('students.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left-circle"></i> Back</a>
                        <a href="{{ route('students.edit', $student->id) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil-square"></i> Edit</a>
                        <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('Are you sure you want to delete this student?')">
                                <i class="bi bi-trash3"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection