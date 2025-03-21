<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\Section; 
use App\Models\Document;

class AdminController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {


        $request->validate([
   
            'name' => ['required', 'string', 'max:255'],
            'employee_id' => ['required', 'string', 'unique:users'],            
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'usertype' => ['required', 'string', 'in:admin,boss,user,section'],
            
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'employee_id.unique' => 'The employee ID must be unique.',
            'email.unique' => 'The email address is already taken.',
            'email.email' => 'The email address must be a valid email address.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);



        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'employee_id' =>$request->employee_id,
            'usertype' => $request->usertype,
            'password' => Hash::make($request->password),
            'role' => $request->role,
 
        ]);

        

        event(new Registered($user));

        

        return redirect()->route('admin.createUser')->with('success', 'User created successfully!');
    }
    public function listUsers()
    {
        $users = User::all();
        
        return view('admin.users', compact('users')); // Ensure this view exists
    }   

    public function createUserForm()
    {
        // Logic to show the create user form
        $activeMenu = 'Create User';
        $sidebarActive = 'User Management';
        return view('admin.create-users', compact('activeMenu', 'sidebarActive')); // Ensure this view exists
    }
    public function dashboard()
    {
        // Get the necessary counts from the database
        $incomingCount = Document::where('uploaded_from', 'incoming')->count(); // Assuming 'status' column is used for 'incoming' status
        $outgoingCount = Document::where('uploaded_from', 'outgoing')->count(); // Assuming 'status' column is used for 'outgoing' status
        $userCount = User::count(); // Get the total count of users
        $sectionCount = Section::count(); // Get the total count of sections
    
        // Pass the counts to the view
        return view('admin.adminDashboard', compact('incomingCount', 'outgoingCount', 'userCount', 'sectionCount'));
    }
    
    
    public function editUser(Request $request, User $selectedUser): View
    {
    
        
        return view('admin.edit-users', compact('selectedUser'));
    }
    
    

    
    
    public function destroyUser($id)
    {
        $user = User::findOrFail($id); // Find the user by ID
        $user->delete(); // Delete the user

        return redirect()->route('admin.users')->with('success', 'User deleted successfully');
    }

    public function updateUser(Request $request, $id)
{
    // Find the user by ID
    $user = User::findOrFail($id);

    // Validate the incoming request
    $validatedData = $request->validate([
        'employee_id' => 'required|string|max:255',
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email,' . $user->id, // Ensure email is unique, ignoring the current user
        'usertype' => 'required|string',
        'role' => 'required|string',
        'password' => 'nullable|string|min:8|confirmed', // Optional password validation
    ]);

    // Update user details
    $user->employee_id = $validatedData['employee_id'];
    $user->name = $validatedData['name'];
    $user->email = $validatedData['email'];
    $user->usertype = $validatedData['usertype'];
    $user->role = $validatedData['role'];

    // Check if a new password is provided and hash it
    if ($request->filled('password')) {
        $user->password = bcrypt($validatedData['password']);
    }

    // Save the updated user information
    $user->save();

    // Redirect back to the user list with a success message
    return redirect()->route('admin.users')->with('success', 'User updated successfully.');
}

public function createSection()
{
    $sections = Section::all();
    return view('admin.create-section', compact('sections'));
}

// Store the new role in the database
public function storeSection(Request $request)
{
    $request->validate([
        'role_name' => 'required|string|max:255|unique:sections,name',
    ],[
        'role_name.unique' => 'This section name already exist. Please choose a different name.',

]);

    Section::create([
        'name' => $request->role_name,
        'category' => 'Section',
    ]);

    return redirect()->route('sections.create')->with('success', 'Section created successfully.');
}

// SectionController.php (or wherever you handle it)
public function fetchSections($usertype)
{
    if ($usertype === 'section') {
        $sections = Section::all(); // Fetch all sections
        return response()->json(['sections' => $sections]);
    }

    // No need to fetch for admin or boss since they are fixed
    return response()->json(['sections' => []]);
}

// SectionController.php
public function deleteSection(Request $request)
{
    $ids = $request->input('ids');
    Section::whereIn('id', $ids)->delete();

    // Redirect back to the create-section page with a success message
    return redirect()->route('sections.create')->with('success', 'Sections successfully deleted.');
}


public function updateSection(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255|unique:sections,name,' . $id,
    ]);

    $section = Section::findOrFail($id);
    $section->name = $request->name;
    $section->save();

    return response()->json(['success' => 'Section updated successfully.']);
}



}
