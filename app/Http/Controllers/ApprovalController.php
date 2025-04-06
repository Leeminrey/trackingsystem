<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Document;

class ApprovalController extends Controller
{


    public function reject()
    {
        $activeMenu = 'Reject';
        $sidebarActive = 'Document';
    
        // Get the logged-in user
        $user = Auth::user();
    
        // Fetch rejected documents that belong to the original uploader
        $documents = Document::where(function ($query) use ($user) {
                $query->where('uploader_id', $user->id) // For files uploaded by the user
                      ->orWhere(function ($subQuery) use ($user) {
                          $subQuery->where('is_reply', 1) // Check if the file was replied
                                   ->where('status', 'rejected') // Ensure it's rejected
                                   ->where('uploader_id', $user->id); // Return to original uploader
                      });
            })
            ->where('status', 'rejected') // Only get rejected documents
            ->get();
    
        // Return the reject component with rejected documents
        return view('dashboard', compact('documents', 'activeMenu', 'sidebarActive'))
               ->with('slot', view('components.reject', compact('documents', 'activeMenu', 'sidebarActive')));
    }
    
    
    public function approved()
    {
        $activeMenu = 'Approval';
        $sidebarActive = 'Document';
    
        // Get the logged-in user
        $user = Auth::user();
    
        // Check if the user is ACL or CL
        if (in_array($user->role, ['ACL', 'CL'])) {
            // If ACL or CL, retrieve all approved documents
            $documents = Document::where('status', 'approved')->get();
        } else {
            // Otherwise, only retrieve approved documents uploaded by the user
            $documents = Document::where('user_id', $user->id)
                                 ->where('status', 'approved')
                                 ->get();
        }
    
        // Return the approve component with approved documents
        return view('dashboard', compact('documents', 'activeMenu', 'sidebarActive'))
               ->with('slot', view('components.approve', compact('documents', 'activeMenu', 'sidebarActive')));
    }

    public function completed()
    {
        $activeMenu = 'Completed';
        $sidebarActive = 'Document';
    
        // Get the logged-in user
        $user = Auth::user();
    
        // Check if the user is ACL or CL
        if (in_array($user->role, ['ACL', 'CL', 'receiving', 'admin1'])) {
            // If ACL or CL, retrieve all approved documents
            $documents = Document::where('status', 'completed')->get();
        } elseif ($user->usertype === 'section'){
            $documents = Document::where('status', 'completed')->get();
        }
        else {
            // Otherwise, only retrieve approved documents uploaded by the user
            $documents = Document::where('user_id', $user->id)
                                 ->where('status', 'completed')
                                 ->get();
        }
    
        // Return the approve component with approved documents
        return view('dashboard', compact('documents', 'activeMenu', 'sidebarActive'))
               ->with('slot', view('components.completed', compact('documents',  'activeMenu', 'sidebarActive')));
    }
    
    
    



    
}
