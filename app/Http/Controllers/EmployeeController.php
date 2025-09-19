<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\Nationality;
use App\Models\Religion;
use App\Models\Board;
use App\Models\Technology;
use App\Models\Shift;
use App\Models\Designation;
use App\Models\EmployeeUniqueId;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function index(Request $request)
    {
        if ($request->ajax()) {
            $teachers = Employee::with('designation')->get();
            return response()->json(['data' => $teachers]);
        }
        return view('content.employees.index');
    }

    public function create()
    {
        $nationalities = Nationality::all();
        $religions = Religion::all();
        $designations = Designation::where("designation_type", "Employee")->get();
        $religions = Religion::all();

        // Get the last serial
        $latest = EmployeeUniqueId::latest('serial')->first();
        $nextSerial = $latest ? $latest->serial + 1 : 1;

        // Format as E-0001, E-0002, ...
        $employee_unique_id = 'E-' . str_pad($nextSerial, 4, '0', STR_PAD_LEFT);

        return view('content.employees.create', compact('nationalities', 'religions', 'designations', 'employee_unique_id', 'nextSerial'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Custom validation messages
        $messages = [
            'email.unique' => 'This email is already taken.',
            'mobile.unique' => 'This mobile number is already registered.',
        ];

        // Validation rules
        $validatedData = $request->validate([
            'employee_name' => 'required|string|max:255',
            'gender' => 'required|string',
            'employee_unique_id' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'mobile' => [
                'required',
                'string',
                'max:15',
                Rule::unique('employees', 'mobile')->ignore($request->id),
            ],
            'email' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('employees', 'email')->ignore($request->id),
            ],
            'nid' => 'required|string|max:20',
            'religion_id' => 'required|exists:religions,id',
            'designation_id' => 'required|exists:designations,id',
            'present_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'cv_upload' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'ssc_or_equivalent_group' => 'nullable|string|max:255',
            'ssc_result' => 'nullable|string|max:255',
            'ssc_documents_upload' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'hsc_or_equivalent_group' => 'nullable|string|max:255',
            'hsc_result' => 'nullable|string|max:255',
            'hsc_documents_upload' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'bachelor_or_equivalent_group' => 'nullable|string|max:255',
            'result' => 'nullable|string|max:255',
            'honors_documents_upload' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'master_or_equivalent_group' => 'nullable|string|max:255',
            'masters_result' => 'nullable|string|max:255',
            'masters_document_upload' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'years_of_experience' => 'nullable|integer',
            'date_of_join' => 'nullable|date',
            'basic_salary' => 'nullable|numeric',
            'house_rent' => 'nullable|numeric',
            'medical_allowance' => 'nullable|numeric',
            'other_allowance' => 'nullable|numeric',
            'gross_salary' => 'nullable|numeric',
        ], $messages);

        $data = $validatedData;
        $user = Auth::user();
        $userId = $user->id;

        // Handle file uploads
        if ($request->hasFile('picture')) {
            $data['picture'] = $request->file('picture')->store('profile_pictures', 'public');
        }
        if ($request->hasFile('cv_upload')) {
            $data['cv_upload'] = $request->file('cv_upload')->store('cvs', 'public');
        }
        if ($request->hasFile('ssc_documents_upload')) {
            $data['ssc_documents_upload'] = $request->file('ssc_documents_upload')->store('documents', 'public');
        }
        if ($request->hasFile('hsc_documents_upload')) {
            $data['hsc_documents_upload'] = $request->file('hsc_documents_upload')->store('documents', 'public');
        }
        if ($request->hasFile('honors_documents_upload')) {
            $data['honors_documents_upload'] = $request->file('honors_documents_upload')->store('documents', 'public');
        }
        if ($request->hasFile('masters_document_upload')) {
            $data['masters_document_upload'] = $request->file('masters_document_upload')->store('documents', 'public');
        }

        $data['user_id'] = $userId;

        // Create or update employee
        $employee = Employee::updateOrCreate(
            ['id' => $request->id],
            $data
        );

        // Create unique employee ID record for new employee
        if (!$request->id) {
            EmployeeUniqueId::create([
                'serial' => $request->serial,
                'employee_id' => $employee->id,
                'employee_unique_id' => $request->employee_unique_id
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Employee saved successfully.'
        ]);
    }


    public function edit(Employee $employee)
    {
        $nationalities = Nationality::all();
        $religions = Religion::all();
        $designations = Designation::where("designation_type", "Employee")->get();
        return view('content.employees.edit', compact('employee', 'nationalities', 'religions', 'designations'));
    }

    public function destroy(Employee $employee)
    {
        // Delete associated files
        $this->deleteFile($employee->picture);
        $this->deleteFile($employee->cv_upload);
        $this->deleteFile($employee->ssc_documents_upload);
        $this->deleteFile($employee->hsc_documents_upload);
        $this->deleteFile($employee->honors_documents_upload);
        $this->deleteFile($employee->masters_document_upload);

        $employee->delete();

        return response()->json(['success' => true, 'message' => 'Employee deleted successfully.']);
    }

    private function deleteFile($filePath)
    {
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
    }
}
