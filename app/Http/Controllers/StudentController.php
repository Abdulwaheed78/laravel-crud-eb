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
        $students = Student::where('is_active','1')->latest()->paginate(10);
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

        return redirect()->back()->with('success', 'CSV uploaded successfully! Processing started...');
    }
}
