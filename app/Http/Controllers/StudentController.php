<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Student as StudentModel;
use Illuminate\Http\Request;
use App\Models\Nationality;
use App\Models\Religion;
use App\Models\Board;
use App\Models\Technology;
use App\Models\Shift;
use App\Models\AcademicYear; // Assuming you have an AcademicYear model
use App\Models\Semester; // Assuming you have a Semester model
use App\Models\StudentUniqueId;
use App\Models\SscPassingYear;
use App\Models\SscPassingSession;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentsExport;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StudentModel::with(['academicYear', 'semester', 'technology', 'shift']);

        // Apply academic year filter
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        // Apply semester filter
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('full_name_in_english_block_letter', 'like', "%{$searchTerm}%")
                    ->orWhere('father_name_in_english_block_letter', 'like', "%{$searchTerm}%")
                    ->orWhere('mother_name_in_english_block_letter', 'like', "%{$searchTerm}%")
                    ->orWhere('personal_number', 'like', "%{$searchTerm}%")
                    ->orWhere('student_unique_id', 'like', "%{$searchTerm}%")
                    ->orWhereHas('academicYear', function ($q) use ($searchTerm) {
                        $q->where('academic_year_name', 'like', "%{$searchTerm}%");
                    })
                    ->orWhereHas('semester', function ($q) use ($searchTerm) {
                        $q->where('semester_name', 'like', "%{$searchTerm}%");
                    });
            });
        }

        // Get pagination per page
        $perPage = $request->get('per_page', 10);

        // Paginate results
        $students = $query->latest()->paginate($perPage)->withQueryString();

        // Get filter options with caching
        $academicYears = cache()->remember('academic_years', 3600, function () {
            return AcademicYear::all();
        });
        $semesters = cache()->remember('semesters', 3600, function () {
            return Semester::all();
        });

        return view('content.students.index', compact('students', 'academicYears', 'semesters'));
    }



    public function generatePdf($id)
    {
        $student = StudentModel::with([
            'nationality',
            'religion',
            'board',
            'technology',
            'academic_year',
            'shift',
            'semester'
        ])->findOrFail($id);

        $appSetting = AppSetting::first() ?? (object) ['app_name' => 'My Application', 'logo' => null];

        $pdf = Pdf::loadView('content.students.details_pdf', compact('student', 'appSetting'));
        return $pdf->download($student->full_name_in_english_block_letter . '.pdf');
    }

    public function getLastSerialNumber(Request $request)
    {
        $academicYearId = $request->input('academic_year_id');

        // Fetch the latest serial number for the selected academic year
        $lastSerial = StudentUniqueId::whereHas('student', function ($query) use ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        })->max('serial');

        // Increment the serial number for the next student
        $nextSerial = $lastSerial ? $lastSerial + 1 : 1;

        return response()->json([
            'next_serial' => $nextSerial
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Use caching for lookup data that doesn't change often
        $nationalities = cache()->remember('nationalities', 3600, function () {
            return Nationality::all();
        });
        $religions = cache()->remember('religions', 3600, function () {
            return Religion::all();
        });
        $boards = cache()->remember('boards', 3600, function () {
            return Board::all();
        });
        $technologies = cache()->remember('technologies', 3600, function () {
            return Technology::all();
        });
        $shifts = cache()->remember('shifts', 3600, function () {
            return Shift::all();
        });
        $academic_years = cache()->remember('academic_years', 3600, function () {
            return AcademicYear::all();
        });
        $semesters = cache()->remember('semesters', 3600, function () {
            return Semester::all();
        });
        $ssc_passing_years = cache()->remember('ssc_passing_years', 3600, function () {
            return SscPassingYear::all();
        });
        $ssc_passing_sessions = cache()->remember('ssc_passing_sessions', 3600, function () {
            return SscPassingSession::all();
        });

        // Get the last serial
        $latest = StudentUniqueId::latest('serial')->first();
        $nextSerial = $latest ? $latest->serial + 1 : 1;

        // Format as S-0001, S-0002, ...
        $student_unique_id = 'S-' . str_pad($nextSerial, 4, '0', STR_PAD_LEFT);


        return view('content.students.create', compact('nationalities', 'religions', 'boards', 'technologies', 'shifts', 'academic_years', 'semesters', 'ssc_passing_years', 'ssc_passing_sessions', 'student_unique_id', 'nextSerial'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debug: Log the incoming request data
        Log::info('Student store request data:', $request->all());
        Log::info('Student store request files:', $request->allFiles());

        try {
            $request->validate([
                'full_name_in_banglai' => 'required',
                'full_name_in_english_block_letter' => 'required',
                'father_name_in_banglai' => 'required',
                'father_name_in_english_block_letter' => 'required',
                'mother_name_in_banglai' => 'required',
                'mother_name_in_english_block_letter' => 'required',
                'personal_number' => 'required|unique:students,personal_number',
                'guardian_phone'  => 'required|string|regex:/^[0-9]{11}$/|different:personal_number',
                'email' => 'required|email|unique:students,email',
                'guardian_phone' => 'required',
                'present_address' => 'required',
                'permanent_address' => 'required',
                'date_of_birth' => 'required|date',
                'ssc_or_equivalent_institute_name' => 'required',
                'ssc_or_equivalent_roll_number' => 'required',
                'ssc_or_equivalent_registration_number' => 'required',
                'ssc_or_equivalent_passing_year_id' => 'required',
                'ssc_or_equivalent_session_id' => 'required',
                'ssc_or_equivalent_gpa' => 'required',
                'picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'nationality_id' => 'required',
                'religion_id' => 'required',
                'board_id' => 'required',
                'technology_id' => 'required',
                'shift_id' => 'required',
                'academic_year_id' => 'required',
                'semester_id' => 'required',
                'student_unique_id' => 'required',
                'gender' => 'required',
                
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Student validation failed:', $e->errors());
            throw $e;
        }

        $input = $request->all();

        if ($image = $request->file('picture')) {
            $destinationPath = 'assets/images/students/';
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileImage);
            $input['picture'] = "$profileImage";
        }

        $student = StudentModel::create($input);

        StudentUniqueId::create([
            'student_unique_id' => $request->student_unique_id,
            'serial' => $request->serial, // Serial part of unique_id
            'student_id' => $student->id,
        ]);

        return response()->json(['message' => 'Student created successfully.', 'student' => $student]);
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentModel $student)
    {
        $appSetting = AppSetting::first(); // Assuming you want to get the first record, adjust if needed
        return view('content.students.show', compact('student', 'appSetting'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudentModel $student)
    {
        // Use caching for lookup data that doesn't change often
        $nationalities = cache()->remember('nationalities', 3600, function () {
            return Nationality::all();
        });
        $religions = cache()->remember('religions', 3600, function () {
            return Religion::all();
        });
        $boards = cache()->remember('boards', 3600, function () {
            return Board::all();
        });
        $technologies = cache()->remember('technologies', 3600, function () {
            return Technology::all();
        });
        $shifts = cache()->remember('shifts', 3600, function () {
            return Shift::all();
        });
        $academic_years = cache()->remember('academic_years', 3600, function () {
            return AcademicYear::all();
        });
        $semesters = cache()->remember('semesters', 3600, function () {
            return Semester::all();
        });
        $ssc_passing_years = cache()->remember('ssc_passing_years', 3600, function () {
            return SscPassingYear::all();
        });
        $ssc_passing_sessions = cache()->remember('ssc_passing_sessions', 3600, function () {
            return SscPassingSession::all();
        });

        return view('content.students.edit', compact('student', 'nationalities', 'religions', 'boards', 'technologies', 'shifts', 'academic_years', 'semesters', 'ssc_passing_years', 'ssc_passing_sessions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StudentModel $student)
    {
        $request->validate([
            'full_name_in_banglai' => 'required',
            'full_name_in_english_block_letter' => 'required',
            'father_name_in_banglai' => 'required',
            'father_name_in_english_block_letter' => 'required',
            'mother_name_in_banglai' => 'required',
            'mother_name_in_english_block_letter' => 'required',
            'personal_number' => 'required|unique:students,personal_number,' . $student->id,
            'guardian_phone'  => 'required|string|regex:/^[0-9]{11}$/|different:personal_number',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'guardian_phone' => 'required',
            'present_address' => 'required',
            'permanent_address' => 'required',
            'date_of_birth' => 'required|date',
            'ssc_or_equivalent_institute_name' => 'required',
            'ssc_or_equivalent_roll_number' => 'required',
            'ssc_or_equivalent_registration_number' => 'required',
            'ssc_or_equivalent_passing_year_id' => 'required',
            'ssc_or_equivalent_session_id' => 'required',
            'ssc_or_equivalent_gpa' => 'required',
            'picture' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'nationality_id' => 'required',
            'religion_id' => 'required',
            'board_id' => 'required',
            'technology_id' => 'required',
            'shift_id' => 'required',
            'academic_year_id' => 'required',
            'semester_id' => 'required',
            'gender' => 'required',
            
        ]);

        $input = $request->all();

        if ($image = $request->file('picture')) {
            $destinationPath = 'assets/images/students/';
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileImage);
            $input['picture'] = "$profileImage";
        } else {
            unset($input['picture']);
        }

        $student->update($input);

        return response()->json(['message' => 'Student updated successfully.', 'student' => $student]);
    }

    /**
     * Check if personal number already exists
     */
    public function checkPersonalNumberDuplicate(Request $request)
    {
        $request->validate([
            'personal_number' => 'required|string|regex:/^[0-9]{11}$/'
        ]);

        $personalNumber = $request->personal_number;
        $studentId = $request->student_id; // For edit form, exclude current student

        $query = StudentModel::where('personal_number', $personalNumber);

        // If editing, exclude current student from duplicate check
        if ($studentId) {
            $query->where('id', '!=', $studentId);
        }

        $exists = $query->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'This personal number is already registered.' : 'Personal number is available.'
        ]);
    }

    /**
     * Check if email already exists
     */
    public function checkEmailDuplicate(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->email;
        $studentId = $request->student_id; // For edit form, exclude current student

        $query = StudentModel::where('email', $email);

        // If editing, exclude current student from duplicate check
        if ($studentId) {
            $query->where('id', '!=', $studentId);
        }

        $exists = $query->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'This email is already registered.' : 'Email is available.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentModel $student)
    {
        $student->delete();
        return response()->json(['message' => 'Student deleted successfully.']);
    }

    /**
     * Export students to Excel
     */
    public function exportExcel(Request $request)
    {
        $query = StudentModel::with(['academicYear', 'semester', 'technology', 'shift']);

        // Apply academic year filter
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        // Apply semester filter
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('full_name_in_english_block_letter', 'like', "%{$searchTerm}%")
                    ->orWhere('father_name_in_english_block_letter', 'like', "%{$searchTerm}%")
                    ->orWhere('mother_name_in_english_block_letter', 'like', "%{$searchTerm}%")
                    ->orWhere('personal_number', 'like', "%{$searchTerm}%")
                    ->orWhere('student_unique_id', 'like', "%{$searchTerm}%")
                    ->orWhereHas('academicYear', function ($q) use ($searchTerm) {
                        $q->where('academic_year_name', 'like', "%{$searchTerm}%");
                    })
                    ->orWhereHas('semester', function ($q) use ($searchTerm) {
                        $q->where('semester_name', 'like', "%{$searchTerm}%");
                    });
            });
        }

        $students = $query->get();

        return Excel::download(new StudentsExport($students), 'students_' . date('Y-m-d_H-i-s') . '.xlsx');
    }

    /**
     * Export students to PDF
     */
    public function exportPdf(Request $request)
    {
        $query = StudentModel::with(['academicYear', 'semester', 'technology', 'shift']);

        // Apply academic year filter
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        // Apply semester filter
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('full_name_in_english_block_letter', 'like', "%{$searchTerm}%")
                    ->orWhere('father_name_in_english_block_letter', 'like', "%{$searchTerm}%")
                    ->orWhere('mother_name_in_english_block_letter', 'like', "%{$searchTerm}%")
                    ->orWhere('personal_number', 'like', "%{$searchTerm}%")
                    ->orWhere('student_unique_id', 'like', "%{$searchTerm}%")
                    ->orWhereHas('academicYear', function ($q) use ($searchTerm) {
                        $q->where('academic_year_name', 'like', "%{$searchTerm}%");
                    })
                    ->orWhereHas('semester', function ($q) use ($searchTerm) {
                        $q->where('semester_name', 'like', "%{$searchTerm}%");
                    });
            });
        }

        $students = $query->get();

        $pdf = Pdf::loadView('content.students.export-pdf', compact('students'));
        return $pdf->download('students_' . date('Y-m-d_H-i-s') . '.pdf');
    }
}
