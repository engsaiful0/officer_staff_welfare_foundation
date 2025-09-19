<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use App\Models\TeacherUniqueId;
use App\Models\Religion;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TeachersExport;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Teacher::with('designation');
        
        // Apply designation filter
        if ($request->filled('designation_id')) {
            $query->where('designation_id', $request->designation_id);
        }
        
        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('teacher_name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('mobile', 'like', "%{$searchTerm}%")
                  ->orWhere('teacher_unique_id', 'like', "%{$searchTerm}%")
                  ->orWhereHas('designation', function ($q) use ($searchTerm) {
                      $q->where('designation_name', 'like', "%{$searchTerm}%");
                  });
            });
        }
        
        // Get pagination per page
        $perPage = $request->get('per_page', 10);
        
        // Paginate results
        $teachers = $query->paginate($perPage)->withQueryString();
        
        $designations = Designation::all();
        
        return view('content.teachers.index', compact('teachers', 'designations'));
    }
    public function getTeachers(Request $request)
    {
        $teachers = Teacher::with('designation')->get();
        return response()->json([
            'data' => $teachers,
        ]);
    }


    public function getAllTeachers(Request $request)
    {
        if ($request->ajax()) {
            $totalData = Teacher::count();
            $query = Teacher::with('designation');

            // Apply designation filter
            if ($request->has('designation_id') && !empty($request->input('designation_id'))) {
                $query->where('designation_id', $request->input('designation_id'));
            }

            $totalFiltered = $query->count();

            if ($request->has('search') && !empty($request->input('search')['value'])) {
                $searchValue = $request->input('search')['value'];
                $query->where(function ($q) use ($searchValue) {
                    $q->where('teacher_name', 'like', "%{$searchValue}%")
                        ->orWhere('email', 'like', "%{$searchValue}%")
                        ->orWhere('mobile', 'like', "%{$searchValue}%")
                        ->orWhere('teacher_unique_id', 'like', "%{$searchValue}%")
                        ->orWhereHas('designation', function ($q) use ($searchValue) {
                            $q->where('designation_name', 'like', "%{$searchValue}%");
                        });
                });
                $totalFiltered = $query->count();
            }

            $teachers = $query->offset($request->input('start'))
                ->limit($request->input('length'))
                ->get();

            $data = [
                'draw' => intval($request->input('draw')),
                'recordsTotal' => intval($totalData),
                'recordsFiltered' => intval($totalFiltered),
                'data' => $teachers,
            ];

            return response()->json($data);
        }
    }

    public function create()
    {
        $designations = Designation::all();
        $religions = Religion::all();

        // Get the last serial
        $latest = TeacherUniqueId::latest('serial')->first();
        $nextSerial = $latest ? $latest->serial + 1 : 1;

        // Format as T-0001, T-0002, ...
        $teacher_unique_id = 'T-' . str_pad($nextSerial, 4, '0', STR_PAD_LEFT);

        return view('content.teachers.create', compact('designations', 'religions', 'teacher_unique_id', 'nextSerial'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'teacher_name' => 'required|string|max:255',
                'gender' => 'required|string|max:10',
                'email' => 'nullable|email|unique:teachers,email',
                'mobile' => 'required|string|unique:teachers,mobile|max:20',
                'designation_id' => 'required|exists:designations,id',
                'medical_allowance' => 'nullable|numeric|min:0',
                'other_allowance' => 'nullable|numeric|min:0',
                'bachelor_or_equivalent_group' => 'nullable|string|max:255',
                'bachelor_or_equivalent_gpa' => 'nullable|numeric|min:0|max:4',
                'master_or_equivalent_group' => 'nullable|string|max:255',
                'master_or_equivalent_gpa' => 'nullable|numeric|min:0|max:4',
                'nid_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $teacher = new Teacher($request->all());
            $user = Auth::user();
            $userId = $user->id;


            if ($request->hasFile('picture')) {
                $picture = $request->file('picture');
                $pictureName = time() . '_' . $picture->getClientOriginalName();
                $picture->move(public_path('profile_pictures'), $pictureName);
                $teacher->picture = $pictureName;
            }

            if ($request->hasFile('nid_picture')) {
                $nidPicture = $request->file('nid_picture');
                $nidPictureName = time() . '_' . $nidPicture->getClientOriginalName();
                $nidPicture->move(public_path('nid_pictures'), $nidPictureName);
                $teacher->nid_picture = $nidPictureName;
            }
            $teacher->user_id = $userId;
            $teacher->save();

            // Save into teacher_unique_ids
            TeacherUniqueId::create([
                'teacher_unique_id' => $request->teacher_unique_id,
                'serial' => $request->serial,
                'teacher_id' => $teacher->id,
            ]);

            return response()->json(['success' => true, 'message' => 'Teacher added successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function edit(Teacher $teacher)
    {
        $designations = Designation::all();
        return view('content.teachers.edit', compact('teacher', 'designations'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'teacher_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:teachers,email,' . $teacher->id,
            'mobile' => 'required|string|max:20',
            'designation_id' => 'required|exists:designations,id',
            'medical_allowance' => 'nullable|numeric|min:0',
            'other_allowance' => 'nullable|numeric|min:0',
            'bachelor_or_equivalent_group' => 'nullable|string|max:255',
            'bachelor_or_equivalent_gpa' => 'nullable|numeric|min:0|max:4',
            'master_or_equivalent_group' => 'nullable|string|max:255',
            'master_or_equivalent_gpa' => 'nullable|numeric|min:0|max:4',
            'nid_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $teacher->fill($request->all());

        if ($request->hasFile('picture')) {
            if ($teacher->picture && file_exists(public_path('profile_pictures/' . $teacher->picture))) {
                unlink(public_path('profile_pictures/' . $teacher->picture));
            }
            $picture = $request->file('picture');
            $pictureName = time() . '_' . $picture->getClientOriginalName();
            $picture->move(public_path('profile_pictures'), $pictureName);
            $teacher->picture = $pictureName;
        }

        if ($request->hasFile('nid_picture')) {
            if ($teacher->nid_picture && file_exists(public_path('nid_pictures/' . $teacher->nid_picture))) {
                unlink(public_path('nid_pictures/' . $teacher->nid_picture));
            }
            $nidPicture = $request->file('nid_picture');
            $nidPictureName = time() . '_' . $nidPicture->getClientOriginalName();
            $nidPicture->move(public_path('nid_pictures'), $nidPictureName);
            $teacher->nid_picture = $nidPictureName;
        }

        $teacher->save();

        return response()->json(['success' => true, 'message' => 'Teacher updated successfully.']);
    }

    public function destroy(Teacher $teacher)
    {
        if ($teacher->picture && file_exists(public_path('profile_pictures/' . $teacher->picture))) {
            unlink(public_path('profile_pictures/' . $teacher->picture));
        }
        if ($teacher->nid_picture && file_exists(public_path('nid_pictures/' . $teacher->nid_picture))) {
            unlink(public_path('nid_pictures/' . $teacher->nid_picture));
        }

        $teacher->delete();

        return response()->json(['success' => true, 'message' => 'Teacher deleted successfully.']);
    }

    /**
     * Export teachers to Excel
     */
    public function exportExcel(Request $request)
    {
        $query = Teacher::with('designation');
        
        // Apply designation filter
        if ($request->filled('designation_id')) {
            $query->where('designation_id', $request->designation_id);
        }
        
        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('teacher_name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('mobile', 'like', "%{$searchTerm}%")
                  ->orWhere('teacher_unique_id', 'like', "%{$searchTerm}%")
                  ->orWhereHas('designation', function ($q) use ($searchTerm) {
                      $q->where('designation_name', 'like', "%{$searchTerm}%");
                  });
            });
        }
        
        $teachers = $query->get();
        
        return Excel::download(new TeachersExport($teachers), 'teachers_' . date('Y-m-d_H-i-s') . '.xlsx');
    }

    /**
     * Export teachers to PDF
     */
    public function exportPdf(Request $request)
    {
        $query = Teacher::with('designation');
        
        // Apply designation filter
        if ($request->filled('designation_id')) {
            $query->where('designation_id', $request->designation_id);
        }
        
        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('teacher_name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('mobile', 'like', "%{$searchTerm}%")
                  ->orWhere('teacher_unique_id', 'like', "%{$searchTerm}%")
                  ->orWhereHas('designation', function ($q) use ($searchTerm) {
                      $q->where('designation_name', 'like', "%{$searchTerm}%");
                  });
            });
        }
        
        $teachers = $query->get();
        
        $pdf = \PDF::loadView('content.teachers.export-pdf', compact('teachers'));
        return $pdf->download('teachers_' . date('Y-m-d_H-i-s') . '.pdf');
    }
}
