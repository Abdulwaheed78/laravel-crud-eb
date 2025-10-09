<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\StudentJob;
use App\Models\Student;
use App\Jobs\ProcessStudentsCsv;

class StudentController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index()
    {
        $students = Student::where('is_active', '1')->latest()->get();
        return view('students.index', compact('students'));
    }


    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        return view('students.create');
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request)
    {
        $data = $request->except('_token');

        // Handle file upload
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('students', 'public');
            $data['profile_photo'] = $path;
        }

        // Dispatch async job
        StudentJob::dispatch('create', $data);

        return redirect()
            ->route('students.create')
            ->with('success', 'Student Create has been scheduled and will be processed shortly!');
    }


    /**
     * Display the specified student.
     */
    public function show($id)
    {
        $student = Student::findOrFail($id);
        return view('students.show', compact('student'));
    }


    /**
     * Show the form for editing the specified student.
     */
    public function edit($id)
    {
        $student = Student::findOrFail($id);
        return view('students.edit', compact('student'));
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $request->except('_token');
        // Handle file upload before dispatching
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('students', 'public');
            $data['profile_photo'] = $path; // store only the path
        }
        $data['id'] = $id;

        // for ($i = 1; $i <= 10; $i++) {
        //     StudentJob::dispatch('update', $data);
        // } // for 10 times update

        StudentJob::dispatch('update', $data);
        // 🔁 Redirect back to create page with flash message
        return redirect()
            ->back()
            ->with('success', 'Student update has been scheduled and will be processed shortly!');
    }


    public function export()
    {
        $fileName = 'students_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $students = \App\Models\Student::orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () use ($students) {
            $handle = fopen('php://output', 'w');

            // Write CSV headers
            fputcsv($handle, [
                '_id',
                'First Name',
                'Last Name',
                'Email',
                'Phone',
                'Roll Number',
                'Age',
                'Gender',
                'Date of Birth',
                'Admission Date',
                'Class Time',
                'Address',
                'Bio',
                'Course',
                'Department',
                'Batch',
                'Is Active',
                'Has Scholarship',
                'Grade',
                'Website',
                'Favorite Color',
                'Hobbies',
                'Profile Photo',
                'Created At',
                'Updated At'
            ]);

            // Write each record
            foreach ($students as $student) {
                fputcsv($handle, [
                    (string) $student->_id,
                    $student->first_name,
                    $student->last_name,
                    $student->email,
                    $student->phone,
                    $student->roll_number,
                    $student->age,
                    $student->gender,
                    $student->date_of_birth,
                    $student->admission_date,
                    $student->class_time,
                    $student->address,
                    $student->bio,
                    $student->course,
                    $student->department,
                    $student->batch,
                    $student->is_active ? 'Active' : 'Inactive',
                    $student->has_scholarship ? 'Yes' : 'No',
                    $student->grade,
                    $student->website,
                    $student->favorite_color,
                    is_array($student->hobbies) ? implode(', ', $student->hobbies) : $student->hobbies,
                    $student->profile_photo,
                    optional($student->created_at)->format('Y-m-d H:i:s'),
                    optional($student->updated_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $fileName, $headers);
    }



    /**
     * Remove the specified student from storage.
     */
    public function destroy($id)
    {
        StudentJob::dispatch('delete', ['id' => $id]);

        return redirect()->route('students.index')->with(['message' => 'Delete job dispatched']);
    }


    public function showUploadForm()
    {
        return view('students.upload');
    }

    public function uploadCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $path = $request->file('csv_file')->store('uploads', 'public');

        // Dispatch job to process CSV
        ProcessStudentsCsv::dispatch($path);

        return redirect()->route('students.index')->with('success', 'CSV uploaded successfully! Processing started...');
    }
}
