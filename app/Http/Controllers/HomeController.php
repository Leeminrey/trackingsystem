<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\User;
use App\Models\Section;
use App\Models\DocumentSection;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

    public function sectionsDashboard()
{
    // Get the logged-in user's role and section ID
    $users = User::All();
    $user = Auth::user();
    $userRole = $user->role;
    $sectionId = $this->getSectionIdFromRole($userRole);

    // Fetch only document IDs **specifically assigned** to this section
    $documentIds = DocumentSection::where('section_id', $sectionId)
        ->pluck('document_id');

    // Fetch counts only for documents assigned **specifically** to the logged-in user's section
    // Count approved documents specifically assigned to the section
    $approvedCount = Document::where('user_id', $user->id)
    ->where('status', 'approved')
    ->count();

    $completedCount = Document::where('user_id', $user->id)
    ->where('status', 'completed')
    ->count();  

    // Count pending documents specifically assigned to the section
    $pendingCount = Document::where('user_id', $user->id)
    ->whereIn('status', ['pending', 'pending in CL','checking'])
    ->count();

    // Count rejected documents specifically assigned to the section
    $rejectedCount = Document::where('user_id', $user->id)
    ->where('status', 'rejected')
    ->count();

    // Fetch only documents **explicitly assigned** to this section
    $documents = Document::with(['user', 'section'])
        ->whereIn('id', $documentIds)
        ->orderBy('date_received', 'desc')
        ->get();

    // Return the view with the filtered counts and documents
    return view('sections.sectionsDashboard', compact('documents', 'approvedCount', 'pendingCount', 'rejectedCount','completedCount', 'users'));
}

    
private function getSectionIdFromRole($role)
{
    $section = Section::where('name', $role)->first(); 

    return $section ? $section->id : null;
}

    
    public function index()
    {
        $user = auth()->user();
    
        if ($user->usertype === 'admin') {
            return view('admin.adminDashboard'); // Path to admin dashboard view
        } elseif ($user->usertype === 'boss') {
            // Fetch documents that need to be reviewed by the boss
            $documents = Document::where('status', 'pending')->get(); // Adjust the condition as needed
            return view('boss.dashboard', compact('documents')); // Pass documents to the view
        } else {
            return view('dashboard'); // Path to user dashboard view
        }
    }

    public function bossDashboard()
{
    // Get the current user's role
    $currentRole = auth()->user()->role;
    $users = User::All();

    // Determine which documents to show based on the user's role
    if ($currentRole === 'CL') { // Boss 1's role
        // Fetch documents waiting for Boss 1's approval
        $documents = Document::where('status', 'pending in CL')->get();
    } elseif ($currentRole === 'ACL') { // Boss 2's role
        // Fetch documents waiting for Boss 2's approval
        $documents = Document::where('status', 'pending')->get();
    } else {
        $documents = []; // Handle case where user is neither Boss 1 nor Boss 2
    }

    return view('boss.bossDashboard', compact('documents', 'users')); // Return the same dashboard view for both
}

public function history()
{
    // Get the current user's role
    $currentRole = auth()->user()->role;

    // Initialize the documents variable
    $documents = [];

    // Determine which documents to show based on the user's role
    if ($currentRole === 'CL') { // CL role
        // Fetch documents with 'pending in CL', 'approved', and 'rejected' statuses, ordered by date_received descending
        $documents = Document::whereIn('status', ['pending in CL', 'approved', 'rejected'])
            ->orderBy('date_received', 'asc')
            ->get();
    } elseif ($currentRole === 'ACL') { // ACL role
        // Fetch documents with 'pending', 'approved', and 'rejected' statuses, ordered by date_received descending
        $documents = Document::whereIn('status', ['pending in CL', 'pending', 'rejected'])
            ->orderBy('date_received', 'asc')
            ->get();
    }

    return view('boss.history', compact('documents'));
}

public function ongoing()
{
   
    $activeMenu = 'Pending Documents';
    $sidebarActive = 'Document';
    
    // Get the current user's role
    $currentRole = auth()->user()->role;

    // Initialize the documents variable
    $documents = [];

    // Determine which documents to show based on the user's role
    if ($currentRole === 'CL' || $currentRole === 'ACL' ) { // CL role
        // Fetch documents with 'pending in CL', 'approved', and 'rejected' statuses, ordered by date_received descending
        $documents = Document::whereIn('status', ['pending in CL', 'pending', 'approved'])
            ->orderBy('date_received', 'asc')
            ->get();
    }

    return view('components.ongoing', compact('documents','activeMenu', 'sidebarActive'));

    
}




}