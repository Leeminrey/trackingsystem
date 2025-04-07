<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; ;
use App\Models\Document;
use App\Models\User;

class DashboardController extends Controller
{
    
    public function dashboard()
{

    $users = User::All();
    // Get the logged-in user
    $user = Auth::user();

    // Initialize query for documents
    $documentsQuery = Document::query();

    // Apply filtering based on user role
    if ($user->role === 'receiving') {
        // Receiving role: Fetch all incoming documents + approved outgoing
        $documentsQuery->where(function ($query) {
            $query->where('uploaded_from', 'incoming')
                  ->orWhere(function ($query) {
                      $query->where('uploaded_from', 'outgoing')
                            ->where('status', 'approved');
                  });
        });
    } elseif ($user->role === 'verifier') {
        // Verifier role: Fetch only "checking" status documents from outgoing
        $documentsQuery->where('uploaded_from', 'outgoing')
                       ->where('status', 'checking');
    } else {
        // Default: Fetch only documents uploaded by the user
        $documentsQuery->where('user_id', $user->id);
    }

    // Fetch documents ordered by date_received
    $documents = $documentsQuery->orderBy('date_received', 'desc')->get();

    // Fetch document counts based on status for the logged-in user
    $approve = Document::where('user_id', $user->id)
    ->where('status', 'approved')
    ->count();

    $pending = Document::where('user_id', $user->id)
    ->whereIn('status', ['pending', 'pending in CL'])
    ->count();

    $rejected = Document::where('user_id', $user->id)
    ->where('status', 'rejected')
    ->count();

    $completed = Document::where('user_id', $user->id)
    ->where('status', 'completed')
    ->count();
    // Pass documents and counts to the view
    return view('components.dashboard', compact('documents', 'approve', 'pending', 'rejected','completed', 'users'));
}

    
    

    public function profileEdit()
    {
        //
        return view('profile.edit'); // Ensure this matches your profile edit view
    }



}
