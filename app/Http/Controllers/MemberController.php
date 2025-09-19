<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Designation;
use App\Models\Branch;
use App\Models\Religion;
use App\Models\Relation;
use App\Models\MemberUniqueId;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Member::with(['designation', 'branch', 'religion', 'introducer', 'nomineeRelation', 'user']);
            
            // Handle DataTables server-side processing
            if ($request->has('draw')) {
                $totalRecords = Member::count();
                $filteredRecords = $query->count();
                
                Log::info('DataTable request', [
                    'draw' => $request->draw,
                    'totalRecords' => $totalRecords,
                    'filteredRecords' => $filteredRecords,
                    'request_data' => $request->all()
                ]);
                
                // Apply search
                if ($request->has('search') && !empty($request->search['value'])) {
                    $searchValue = $request->search['value'];
                    $query->where(function($q) use ($searchValue) {
                        $q->where('name', 'like', "%{$searchValue}%")
                          ->orWhere('email', 'like', "%{$searchValue}%")
                          ->orWhere('mobile', 'like', "%{$searchValue}%")
                          ->orWhere('unique_id', 'like', "%{$searchValue}%")
                          ->orWhere('member_unique_id', 'like', "%{$searchValue}%");
                    });
                    $filteredRecords = $query->count();
                }
                
                // Apply ordering
                if ($request->has('order')) {
                    $orderColumn = $request->order[0]['column'];
                    $orderDir = $request->order[0]['dir'];
                    $columns = ['id', 'name', 'picture', 'unique_id', 'designation_id', 'branch_id', 'email', 'mobile', 'date_of_join'];
                    
                    if (isset($columns[$orderColumn])) {
                        $query->orderBy($columns[$orderColumn], $orderDir);
                    }
                }
                
                // Apply pagination
                $start = $request->start ?? 0;
                $length = $request->length ?? 10;
                $members = $query->skip($start)->take($length)->get();
                
                $response = [
                    'draw' => intval($request->draw),
                    'recordsTotal' => $totalRecords,
                    'recordsFiltered' => $filteredRecords,
                    'data' => $members
                ];
                
                Log::info('DataTable response', [
                    'draw' => $response['draw'],
                    'recordsTotal' => $response['recordsTotal'],
                    'recordsFiltered' => $response['recordsFiltered'],
                    'data_count' => count($response['data']),
                    'first_member' => $response['data']->first()
                ]);
                
                return response()->json($response);
            }
            
            // Fallback for non-DataTables requests
            $members = $query->paginate(10);
            
            return response()->json([
                'data' => $members->items(),
                'current_page' => $members->currentPage(),
                'last_page' => $members->lastPage(),
                'per_page' => $members->perPage(),
                'total' => $members->total(),
                'links' => $members->links()->toHtml()
            ]);
        }
        
        return view('content.members.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $designations = Designation::all()->where("designation_type", "Member");
        $branches = Branch::all();
        $religions = Religion::all();
        $relations = Relation::all();
        $members = Member::select('id', 'name', 'unique_id')->get();

         // Get the last serial
         $latest = MemberUniqueId::latest('serial')->first();
         $nextSerial = $latest ? $latest->serial + 1 : 1;
         // Format as M-0001, M-0002, ...
        $member_unique_id = 'M-' . str_pad($nextSerial, 4, '0', STR_PAD_LEFT);
        
        return view('content.members.create', compact('designations', 'branches', 'religions', 'relations', 'members', 'member_unique_id', 'nextSerial'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Custom validation messages
        $messages = [
            'email.unique' => 'This email is already taken.',
            'mobile.unique' => 'This mobile number is already registered.',
            'nid_number.unique' => 'This NID number is already registered.',
        ];

        // Validation rules
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'mobile' => [
                'required',
                'string',
                'max:15',
                Rule::unique('members', 'mobile'),
            ],
            'email' => [
                'required',
                'string',
                'max:255',
                Rule::unique('members', 'email'),
            ],
            'nid_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('members', 'nid_number'),
            ],
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'designation_id' => 'required|exists:designations,id',
            'date_of_join' => 'required|date',
            'branch_id' => 'required|exists:branches,id',
            'present_address' => 'required|string',
            'permanent_address' => 'required|string',
            'introducer_id' => 'nullable|exists:members,id',
            'religion_id' => 'required|exists:religions,id',
            'nominee_name' => 'nullable|string|max:255',
            'nominee_relation_id' => 'nullable|exists:relations,id',
            'nominee_phone' => 'nullable|string|max:15',
        ], $messages);

        $data = $validatedData;
        $user = Auth::user();
        $userId = $user->id;

        // Handle file upload
        if ($request->hasFile('picture')) {
            $data['picture'] = $request->file('picture')->store('profile_pictures', 'public');
        }

        $data['user_id'] = $userId;

        // Create member (unique_id, temp_username, temp_password will be auto-generated by model)
        $member = Member::create($data);

        // Create unique employee ID record for new employee
        if (!$request->id) {
            MemberUniqueId::create([
                'serial' => $request->serial,
                'member_id' => $member->id,
                'member_unique_id' => $request->member_unique_id
            ]);
        }

        // Create user account for the member
        $user = User::create([
            'name' => $member->name,
            'email' => $member->email,
            'username' => $member->temp_username,
            'password' => Hash::make($member->temp_password),
            'rule_id' => 2, // Assuming 2 is the member role
        ]);

        // Update member with user_id
        $member->update(['user_id' => $user->id]);

        return response()->json([
            'success' => true,
            'message' => 'Member created successfully.',
            'member' => $member->load(['designation', 'branch', 'religion', 'introducer'])
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
        $member->load(['designation', 'branch', 'religion', 'introducer', 'nomineeRelation', 'user']);
        return response()->json(['member' => $member]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member)
    {
        $designations = Designation::all();
        $branches = Branch::all();
        $religions = Religion::all();
        $relations = Relation::all();
        $members = Member::where('id', '!=', $member->id)->select('id', 'name', 'unique_id')->get();
        
        return view('content.members.edit', compact('member', 'designations', 'branches', 'religions', 'relations', 'members'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Member $member)
    {
        // Custom validation messages
        $messages = [
            'email.unique' => 'This email is already taken.',
            'mobile.unique' => 'This mobile number is already registered.',
            'nid_number.unique' => 'This NID number is already registered.',
        ];

        // Validation rules
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'mobile' => [
                'required',
                'string',
                'max:15',
                Rule::unique('members', 'mobile')->ignore($member->id),
            ],
            'email' => [
                'required',
                'string',
                'max:255',
                Rule::unique('members', 'email')->ignore($member->id),
            ],
            'nid_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('members', 'nid_number')->ignore($member->id),
            ],
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'designation_id' => 'required|exists:designations,id',
            'date_of_join' => 'required|date',
            'branch_id' => 'required|exists:branches,id',
            'present_address' => 'required|string',
            'permanent_address' => 'required|string',
            'introducer_id' => 'nullable|exists:members,id',
            'religion_id' => 'required|exists:religions,id',
            'nominee_name' => 'nullable|string|max:255',
            'nominee_relation_id' => 'nullable|exists:relations,id',
            'nominee_phone' => 'nullable|string|max:15',
        ], $messages);

        $data = $validatedData;

        // Handle file upload
        if ($request->hasFile('picture')) {
            // Delete old picture
            if ($member->picture && Storage::disk('public')->exists($member->picture)) {
                Storage::disk('public')->delete($member->picture);
            }
            $data['picture'] = $request->file('picture')->store('profile_pictures', 'public');
        }

        // Update member
        $member->update($data);

        // Update user account
        if ($member->user) {
            $member->user->update([
                'name' => $member->name,
                'email' => $member->email,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Member updated successfully.',
            'member' => $member->load(['designation', 'branch', 'religion', 'introducer'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        // Delete associated files
        if ($member->picture && Storage::disk('public')->exists($member->picture)) {
            Storage::disk('public')->delete($member->picture);
        }

        // Delete user account
        if ($member->user) {
            $member->user->delete();
        }

        $member->delete();

        return response()->json([
            'success' => true,
            'message' => 'Member deleted successfully.'
        ]);
    }

    /**
     * Get members for dropdown (for introducer selection)
     */
    public function getMembers()
    {
        $members = Member::select('id', 'name', 'unique_id')->get();
        return response()->json(['members' => $members]);
    }

    /**
     * Check if email is unique
     */
    public function checkEmailUnique(Request $request)
    {
        $query = Member::where('email', $request->email);
        
        if ($request->has('id')) {
            $query->where('id', '!=', $request->id);
        }
        
        $exists = $query->exists();
        
        return response()->json(['unique' => !$exists]);
    }

    /**
     * Check if mobile is unique
     */
    public function checkMobileUnique(Request $request)
    {
        $query = Member::where('mobile', $request->mobile);
        
        if ($request->has('id')) {
            $query->where('id', '!=', $request->id);
        }
        
        $exists = $query->exists();
        
        return response()->json(['unique' => !$exists]);
    }

    /**
     * Check if NID is unique
     */
    public function checkNidUnique(Request $request)
    {
        $query = Member::where('nid_number', $request->nid_number);
        
        if ($request->has('id')) {
            $query->where('id', '!=', $request->id);
        }
        
        $exists = $query->exists();
        
        return response()->json(['unique' => !$exists]);
    }
}
