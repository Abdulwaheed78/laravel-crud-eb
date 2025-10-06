@extends('base')

@section('title', 'Create Student')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow-sm border-0 rounded-3 p-4" style="max-width: 800px; width: 100%;">
        <h3 class="mb-4 text-center">Create Student</h3>

        {{-- ✅ Success Message --}}
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <strong>Success:</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        {{-- ❌ Error Message (flashed from controller/job) --}}
        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <strong>Error:</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        {{-- ⚠️ Validation Errors --}}
        @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif


        {{-- Create Student Form --}}
        <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Row 1: Names --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="first_name" class="form-label">First Name *</label>
                    <input type="text" class="form-control" name="first_name" value="{{ old('first_name') }}" required>
                </div>
                <div class="col-md-6">
                    <label for="last_name" class="form-label">Last Name *</label>
                    <input type="text" class="form-control" name="last_name" value="{{ old('last_name') }}" required>
                </div>
            </div>

            {{-- Row 2: Email / Phone --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" name="phone" value="{{ old('phone') }}">
                </div>
            </div>

            {{-- Row 3: Roll Number / Age --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="roll_number" class="form-label">Roll Number *</label>
                    <input type="text" class="form-control" name="roll_number" value="{{ old('roll_number') }}" required>
                </div>
                <div class="col-md-6">
                    <label for="age" class="form-label">Age</label>
                    <input type="number" class="form-control" name="age" value="{{ old('age') }}">
                </div>
            </div>

            {{-- Row 4: Gender / DOB --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label d-block">Gender</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" value="male" {{ old('gender')=='male'?'checked':'' }}>
                        <label class="form-check-label">Male</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" value="female" {{ old('gender')=='female'?'checked':'' }}>
                        <label class="form-check-label">Female</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" value="other" {{ old('gender')=='other'?'checked':'' }}>
                        <label class="form-check-label">Other</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" name="date_of_birth" value="{{ old('date_of_birth') }}">
                </div>
            </div>

            {{-- Row 5: Admission Date / Class Time --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="admission_date" class="form-label">Admission Date</label>
                    <input type="datetime-local" class="form-control" name="admission_date" value="{{ old('admission_date') }}">
                </div>
                <div class="col-md-6">
                    <label for="class_time" class="form-label">Class Time</label>
                    <input type="time" class="form-control" name="class_time" value="{{ old('class_time') }}">
                </div>
            </div>

            {{-- Row 6: Address / Bio --}}
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" name="address" value="{{ old('address') }}">
            </div>

            <div class="mb-3">
                <label for="bio" class="form-label">Bio</label>
                <textarea class="form-control" name="bio" rows="3">{{ old('bio') }}</textarea>
            </div>

            {{-- Row 7: Course / Department / Batch --}}
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="course" class="form-label">Course</label>
                    <input type="text" class="form-control" name="course" value="{{ old('course') }}">
                </div>
                <div class="col-md-4">
                    <label for="department" class="form-label">Department</label>
                    <select class="form-select" name="department">
                        <option value="">Select Department</option>
                        <option value="CSE" {{ old('department')=='CSE'?'selected':'' }}>CSE</option>
                        <option value="ECE" {{ old('department')=='ECE'?'selected':'' }}>ECE</option>
                        <option value="ME" {{ old('department')=='ME'?'selected':'' }}>ME</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="batch" class="form-label">Batch (Year)</label>
                    <input type="number" class="form-control" name="batch" value="{{ old('batch') }}">
                </div>
            </div>

            {{-- Row 8: Checkboxes --}}
            <div class="row mb-3">
                <div class="col-md-6 form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active')?'checked':'' }}>
                    <label class="form-check-label">Is Active</label>
                </div>
                <div class="col-md-6 form-check">
                    <input class="form-check-input" type="checkbox" name="has_scholarship" value="1" {{ old('has_scholarship')?'checked':'' }}>
                    <label class="form-check-label">Has Scholarship</label>
                </div>
            </div>

            {{-- Row 9: Profile Photo --}}
            <div class="mb-3">
                <label for="profile_photo" class="form-label">Profile Photo</label>
                <input type="file" class="form-control" name="profile_photo" accept="image/*">
            </div>

            {{-- Row 10: Grade / Website / Color --}}
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="grade" class="form-label">Grade</label>
                    <input type="number" step="0.01" class="form-control" name="grade" value="{{ old('grade') }}">
                </div>
                <div class="col-md-4">
                    <label for="website" class="form-label">Website</label>
                    <input type="url" class="form-control" name="website" value="{{ old('website') }}">
                </div>
                <div class="col-md-4">
                    <label for="favorite_color" class="form-label">Favorite Color</label>
                    <input type="color" class="form-control form-control-color" name="favorite_color" value="{{ old('favorite_color', '#000000') }}">
                </div>
            </div>

            {{-- Row 11: Password --}}
            <div class="mb-3">
                <label for="password" class="form-label">Password *</label>
                <input type="password" class="form-control" name="password" required>
            </div>

            {{-- Row 12: Hobbies (JSON) --}}
            <div class="mb-3">
                <label class="form-label">Hobbies</label><br>
                @php
                $hobbies = ['Reading', 'Music', 'Sports', 'Gaming', 'Travel'];
                @endphp
                @foreach ($hobbies as $hobby)
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="hobbies[]" value="{{ $hobby }}"
                        {{ (is_array(old('hobbies')) && in_array($hobby, old('hobbies'))) ? 'checked' : '' }}>
                    <label class="form-check-label">{{ $hobby }}</label>
                </div>
                @endforeach
            </div>

            {{-- Hidden Token --}}
            <input type="hidden" name="secret_token" value="{{ Str::random(16) }}">

            {{-- Buttons --}}
            <div class="d-flex justify-content-end mt-4 gap-3">
                <a href="{{ route('students.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                <button type="submit" class="btn btn-sm btn-primary">Create Student</button>
            </div>
        </form>
    </div>
</div>
@endsection