<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Document;
use App\Models\Notification; 
use App\Models\Section;
use App\Models\User;
use App\Models\DocumentSection;
use App\Models\LibrarianComment;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DocumentController extends Controller
{
    use ValidatesRequests;

    protected $notificationController;

    public function __construct(NotificationController $notificationController)
    {
        $this->notificationController = $notificationController;
    }

    public function navigation()
    {
        $activeMenu = 'Dashboard'; // Set default active menu
        $sidebarActive = 'Dashboard';
        return view('components.dashboard', compact('activeMenu', 'sidebarActive'));
    }
    
    public function incoming()
    {
        // Get the logged-in user
        $user = Auth::user();
    
        // Check if the user is part of a section
        if ($user->usertype === 'section') {
            // Get the section ID from the user's role
            $sectionId = $this->getSectionIdFromRole($user->role);
    
            // Fetch document IDs that are designated for the user's section
            $documentIds = DocumentSection::where('section_id', $sectionId)
                ->pluck('document_id')
                ->toArray();
    
            // Retrieve only the documents that are meant for this  , ordered by date
            $documents = Document::whereIn('id', $documentIds)
                ->where('status', 'approved') // Assuming you want to show only approved documents
                ->with(['user'])
                ->orderBy('date_received', 'desc')
                ->get();
        } else {
            // For non-section users, apply the existing logic based on roles
            $documents = Document::with('user')
                ->orderBy('date_received', 'desc')
                ->get();
        }
    
        // Define the menu and sidebar settings
        $activeMenu = 'Incoming Documents';
        $sidebarActive = 'Document';
    
        // Return the view with the filtered documents
        return view('components.incoming', compact('documents', 'activeMenu', 'sidebarActive'));
    }

    private function getSectionIdFromRole($role)
{
    $section = Section::where('name', $role)->first(); 

    return $section ? $section->id : null;
}
    
    
    public function submitted()
    {
        $activeMenu = 'Submitted Documents';
        $sidebarActive = 'Document';
        return view('components.submitted', compact('activeMenu', 'sidebarActive'));
    }
    
    public function outcoming()
{
    // Get the logged-in user
    $user = Auth::user();

    if ($user->usertype === 'section') {
        // Section users should only see documents they uploaded
        $documents = Document::where('user_id', $user->id)
            ->where('uploaded_from', 'outgoing') // Ensure documents are marked as 'outgoing'
            ->orderBy('date_received', 'desc')
            ->with('user')
            ->get();
    } elseif ($user->role === 'ACL') {
        // ACL sees only documents pending their evaluation
        $documents = Document::where('uploaded_from', 'outgoing')
            ->where('status', 'pending')
            ->orderBy('date_received', 'desc')
            ->with('user')
            ->get();
    } elseif ($user->role === 'CL') {
        // CL sees only documents pending their evaluation
        $documents = Document::where('uploaded_from', 'outgoing')
            ->where('status', 'pending in CL')
            ->orderBy('date_received', 'desc')
            ->with('user')
            ->get();
    } else {
        // Non-section users (Admin, Boss, etc.) can see all outgoing documents
        $documents = Document::where('uploaded_from', 'outgoing')
            ->orderBy('date_received', 'desc')
            ->with('user')
            ->get();
    }

    // Define the menu and sidebar settings
    $activeMenu = 'Outgoing Documents';
    $sidebarActive = 'Document';

    // Return the view with the filtered documents
    return view('components.outcoming', compact('documents', 'activeMenu', 'sidebarActive'));
}

    
    
    
    
    

    public function uploadForm()
    {
        $activeMenu = 'Upload Files';
        $sidebarActive = 'Upload';
        return view('documents.upload', compact('activeMenu', 'sidebarActive'));
    }

 



    public function sorting(Request $request)
{
    $sort = $request->input('sort', 'asc'); // Default to ascending if no sort is provided

    // Fetch documents with sorting
    $documents = Document::with('user')
        ->orderBy('date', $sort) // Change 'date' to your actual date column name
        ->get();

    return view('components.incoming', compact('documents'));
}


    public function search(Request $request)
{
    $query = $request->input('query');
    $documents = Document::with('user')
        ->where('title', 'LIKE', "%{$query}%")
        ->orderBy('created_at', 'desc')
        ->get();

    return view('components.incoming', compact('documents'));
}

public function updateStatus(Request $request, $id)
{
    $document = Document::findOrFail($id);
    $action = $request->input('action');
    $currentRole = auth()->user()->role;

    // Store the librarian's comment in the LibrarianComment table
    $librarianComment = new LibrarianComment();
    $librarianComment->document_id = $document->id;
    $librarianComment->user_id = auth()->id(); // assuming the logged-in user is either ACL or CL
    $librarianComment->comment = $request->input('comments'); // comment from the request
    $librarianComment->save(); // Save the comment to the new table

    // The rest of the code for status update and notifications remains the same
    if ($currentRole === 'verifier') {
        if ($action === 'approve') {
            $document->status = 'pending'; // Move to ACL review
            $aclUserId = $this->getAclUserId(); // Get ACL user ID
            $this->notificationController->createNotification($aclUserId, $document->id, "The Verifier sent you a file {$document->original_file_name}. ");
            
           
        } elseif ($action === 'reject') {
            $document->status = 'rejected'; // Send back to the uploader
            $uploaderId = $document->user_id;
            $this->notificationController->createNotification($uploaderId, $document->id, "The file {$document->original_file_name} you uploaded has been rejected by the Verifier.");
            
            
        }
    } elseif ($currentRole === 'ACL') {
        if ($action === 'approve') {
            $document->status = 'pending in CL';
            $clUserId = $this->getClUserId();
            $this->notificationController->createNotification($clUserId, $document->id, "The Asst. City Librarian forwarded a file {$document->original_file_name} for another evaluation.");
        } elseif ($action === 'reject') {
            $document->status = 'rejected';
            $uploaderId = $document->user_id;
            $this->notificationController->createNotification($uploaderId, $document->id, "The file {$document->original_file_name} you uploaded has been rejected by the Asst. City Librarian.");
        }
    } elseif ($currentRole === 'CL') {
        if ($action === 'approve') {
            $document->status = 'approved';
            $uploaderId = $document->user_id;
            $this->notificationController->createNotification($uploaderId, $document->id, "The file {$document->original_file_name} you uploaded has been approved.");
    
            // In the 'CL' approve section for outgoing documents
            if ($document->uploaded_from === 'outgoing') {
                // Find the user with the 'Receiving' role
                $receiver = User::where('role', 'Receiving')->first();
                if ($receiver) {
                    $this->notificationController->createNotification($receiver->id, $document->id, "The City Librarian just sent you a file.");
                }
            } else {
                // Send notifications to selected sections (for non-outgoing documents)
                if ($document->selectedSections && $document->selectedSections->count() > 0) {
                    foreach ($document->selectedSections as $section) {
                        // Access user_id from the pivot table
                        $userId = $section->pivot->user_id;
                        $this->notificationController->createNotification($userId, $document->id, "The City Librarian just sent you a file.");
                    }
                }
            }
    
            $this->notificationController->removeRejectionNotification($document->id);
        } elseif ($action === 'reject') {
            $document->status = 'rejected';
            $uploaderId = $document->user_id;
            $this->notificationController->createNotification($uploaderId, $document->id, "The file {$document->original_file_name} you uploaded has been rejected by the City Librarian.");
        }
    }
    $document->save();
    
    return redirect()->route('documents.incoming')->with('success', 'Document status updated successfully.');
}




// public function show($id)
// {
//     // Fetch the document with its librarian comment
//     $document = Document::with([
//         'sections', // Fetch related sections
//         'sectionComments.user' => function ($query) {
//             $query->select('id', 'name', 'role'); // Fetch section commenter details
//         },
//     ])->findOrFail($id);

//     // Pass the librarian's comment separately for clarity
//     $librarianComment = $document->comments; // 'comments' from the 'documents' table

//     return view('components.document-details', compact('document', 'librarianComment'));
// }

public function show($id)
{
    // Fetch the document with its librarian comments
    $document = Document::with([
        'sections',
        'librarianComments.user' => function ($query) {
            $query->select('id', 'name', 'role');
        },
    ])->findOrFail($id);

    // Separate comments based on reply_phase
    $normalComments = $document->librarianComments->where('reply_phase', 0);
    $replyComments = $document->librarianComments->where('reply_phase', 1);

    // Pass the data to the view
    return view('components.document-details', compact('document', 'normalComments', 'replyComments'));
}

    public function view($filename)
{
    $path = storage_path('app/public/documents/' . $filename);
    if (file_exists($path)) {
        return response()->file($path);
    }
    return abort(404);
}


    public function index()
    {
        $documents = Document::all();
        return view('documents.index', compact('documents'));
    }

    // STORE 
    public function store(Request $request)
{
    // Validate input and check if locator_no already exists
    $request->validate([
        'locator_no' => 'required|string|max:255|unique:documents,locator_no',
        'subject' => 'required|string|max:255',
        'date_received' => 'required|date',
        'details' => 'nullable|string',
        'file' => 'required|array',
        'file.*' => 'file|mimes:pdf,doc,docx,xlsx,xls,jpg,jpeg,png',
    ], [
        'locator_no.unique' => 'The locator number already exists. Please enter a unique number.',
    ]);

    foreach ($request->file('file') as $file) {
        if ($file->isValid()) {
            $originalFileName = $file->getClientOriginalName();
            $hashedFileName = hash('sha256', time() . $originalFileName) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('documents', $hashedFileName, 'public');

            // Determine the 'uploaded_from' value based on user type
            $uploadedFrom = (auth()->user()->usertype === 'section') ? 'outgoing' : 'incoming';

            // Create the document with the user-provided locator number
            $document = Document::create([
                'user_id' => auth()->id(),
                'uploader_id' => auth()->id(),
                'locator_no' => $request->locator_no, // User-entered locator number
                'subject' => $request->subject,
                'received_from' => $request->received_from, // Add this
                'date_received' => $request->date_received,
                'date_filed' => now(),
                'details' => $request->details,
                'file_path' => $filePath,
                'original_file_name' => $originalFileName,
                'hashed_file_name' => $hashedFileName,
                'status' => $this->getDocumentStatus(auth()->user()->usertype),
                'uploaded_from' => $uploadedFrom,
            ]);

            // Ensure the document is assigned to the correct section
            if (auth()->user()->usertype === 'section') {
                $sectionId = $this->getSectionIdFromRole(auth()->user()->role);
                DocumentSection::create([
                    'document_id' => $document->id,
                    'section_id' => $sectionId,
                ]);
            }

            // Notify Verifier (if outgoing) or ACL (if incoming)
            if ($uploadedFrom === 'outgoing') {
                $verifierUserId = $this->getVerifierUserId();
                $this->notificationController->createNotification(
                    $verifierUserId, 
                    $document->id, 
                    "The " . auth()->user()->role . " just uploaded a file $originalFileName. The file is now in checking."
                );
            } elseif ($uploadedFrom === 'incoming') {
                $getAclUserId = $this->getAclUserId();
                $this->notificationController->createNotification(
                    $getAclUserId, 
                    $document->id, 
                    "The " . auth()->user()->role . " just uploaded a file $originalFileName from " . $uploadedFrom . "."
                );
            }
        }
    }

    if (auth()->user()->usertype === 'section') {
        return redirect()->route('sectionsDashboard')->with('success', 'Document uploaded successfully.');
    } elseif (auth()->user()->usertype === 'receiving') {
        return redirect()->route('dashboard')->with('success', 'Document uploaded successfully.');
    }


    // Redirect to the dashboard after successful upload
    return redirect()->route('dashboard')->with('success', 'Document uploaded successfully.');
}

    
    

    private function getDocumentStatus($usertype)
{
    if ($usertype === 'user') {
        return 'pending'; // If the user is "receiving", status is "pending".
    } elseif ($usertype === 'section') {
        return 'checking'; // If the user is from "section", status is "checking".
    }

    // Default fallback to "pending" if no other conditions match
    return 'pending';
}

// middleware
public function showFile($fileName) {
    // Construct the full path to the file
    $pathToFile = public_path('storage/documents/' . $fileName);
    \Log::info("Path to file: " . $pathToFile);
    
    // Check if the file exists
    if (!file_exists($pathToFile)) {
        abort(404); // File not found
    }

    // Get the file extension to set the correct Content-Type
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $contentType = 'application/octet-stream'; // Default content type

    // Set the Content-Type and Content-Disposition based on the file type
    switch ($extension) {
        case 'pdf':
            $contentType = 'application/pdf';
            break;
        case 'docx':
        case 'doc':
            $contentType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
            // For .doc files, you might want to use the following line instead
            // $contentType = 'application/msword';
            break;
        case 'pptx':
            $contentType = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
            break;
        case 'xls':
            $contentType = 'application/vnd.ms-excel'; // For .xls files
            break;
        case 'xlsx':
            $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'; // For .xlsx files
            break;
        case 'jpeg':
        case 'jpg':
            $contentType = 'image/jpeg'; // For JPEG and JPG files
            break;
        case 'png':
            $contentType = 'image/png'; // For PNG files
            break;
    }

    // Return the file response with the appropriate Content-Disposition
    return response()->file($pathToFile, [
        'Content-Type' => $contentType,
        'Content-Disposition' => 'inline', // Ensures the file is displayed in the browser
    ]);
}

public function getSelectedSections($documentId) {
    $sections = Section::all(['id', 'name']);
    $selectedSections = DocumentSection::where('document_id', $documentId)
        ->pluck('section_id')
        ->toArray();

        
    
    return response()->json([
        'selected_sections' => $selectedSections,
        'sections' => $sections,
        'selectedSectionIds' => $selectedSections,
    ]);
    
}


public function saveSections(Request $request, $id)
{
    $request->validate([
        'section_ids' => 'required|array',
        'section_ids.*' => 'exists:sections,id', // Validate section IDs
    ]);

    // Assuming you have a Document model and a DocumentSection pivot model
    $document = Document::findOrFail($id);

    // Prepare data for syncing
    $sections = [];
    foreach ($request->section_ids as $sectionId) {
        // Get the section name from the sections table using the section_id
        $section = Section::findOrFail($sectionId);
        $sectionName = $section->name;

        // Fetch the user_id from the users table where role matches section name
        $user = User::where('role', $sectionName)->first(); // Adjust this query if you need a different condition
        
        // If a matching user is found, assign the user_id, otherwise assign null
        $userId = $user ? $user->id : null;

        // Add the section and user_id to the sync data
        $sections[$sectionId] = ['user_id' => $userId];
    }

    // Sync sections with the document along with user_id
    $document->sections()->sync($sections);

    return response()->json(['message' => 'Sections saved successfully.']);
}



// In DocumentController.php
public function filterDocuments(Request $request)
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    $documents = Document::where('status', 'approved')
        ->whereBetween('date_received', [$startDate, $endDate])
        ->with('user') // assuming you want to include the uploader
        ->get();

    return response()->json($documents);
}



public function approveDocument(Request $request, $id)
{
    // Validate that section_ids is an array and required
    $request->validate([
        'section_ids' => 'required|array',
        'section_ids.*' => 'exists:document_sections,section_id', // Assuming you have a document_sections table
    ]);

    // Fetch the Document instance
    $document = Document::findOrFail($id);
    $document->status = 'approved';
    $document->save();

    // Get the sections the document will be assigned to
    $sections = Section::whereIn('id', $request->section_ids)->get();

    // Collect all users from these sections
    $users = $sections->flatMap(function ($section) {
        return $section->users; // Assuming Section has a many-to-many relationship with User
    });

    // Create a notification for each user
    foreach ($users as $user) {
        Notification::create([
            'user_id' => $user->id, // ID of the user receiving the notification
            'document_id' => $document->id,
            'message' => 'The City Librarian just sent you a file.',
            'is_read' => false, // Assuming unread notifications
        ]);
    }

    // Redirect or respond as needed
    return redirect()->route('documents.incoming')->with('success', 'Document approved and notifications sent successfully.');
}


private function getAclUserId()
{
    return User::where('role', 'ACL')->first()->id;
}
private function getVerifierUserId()
{
    return User::where('role', 'verifier')->first()->id;
}

private function getClUserId()
{
    return User::where('role', 'CL')->first()->id;
}

public function forwardDocumentToSections(Request $request, $documentId)
{
    // Get the document
    $document = Document::findOrFail($documentId);

    // Get the section IDs associated with this document
    $sectionIds = DocumentSection::where('document_id', $documentId)
        ->pluck('section_id'); // Fetch section_ids related to the document

    // Get the users of these sections
    $users = Section::whereIn('id', $sectionIds)
        ->with('users')  // Assuming Section has a 'users' relationship
        ->get()
        ->flatMap(function ($section) {
            return $section->users; // Get all users in the section
        });

    // Send notifications to each user
    foreach ($users as $user) {
        Notification::create([
            'user_id' => $user->id, // Notification for the user in this section
            'document_id' => $documentId,
            'message' => "The City Librarian just sent you a file {$document->original_file_name}.",
            'is_read' => false, // Unread notification
        ]);
    }


    // Optionally update the document's status or perform other actions
    $document->status = 'forwarded'; // Example status update
    $document->save();

    return redirect()->route('documents.incoming')->with('success', 'Document forwarded and notifications sent.');
}




public function getNotifications()
{
    // Get notifications for the authenticated user
    $notifications = Notification::where('user_id', auth()->id())->get();

    // Add uploader_name and minutes_ago for each notification
    $notifications = $notifications->map(function ($notification) {
        // Check if the user exists for this notification
        $notification->uploader_name = $notification->user ? $notification->user->name : 'Unknown';  // Fetch the uploader's name safely
        
        // Calculate the minutes ago the notification was created
        $notification->minutes_ago = $notification->created_at->diffInMinutes(now());  // Calculate time difference
        
        return $notification;  // Return updated notification
    });

    // Count of notifications
    $count = $notifications->count();

    return response()->json([
        'notifications' => $notifications,
        'count' => $count
    ]);
}

public function edit($id)
{
    $document = Document::findOrFail($id);

    // Ensure the user is authorized to edit this document (optional)
    if ($document->user_id !== auth()->user()->id) {
        return redirect()->route('documents.index')->with('error', 'You are not authorized to edit this document.');
    }

    return view('components.edit', compact('document'));  // Adjusted to point to 'components.edit'
}
public function destroy($id)
{
    $document = Document::findOrFail($id);

    // Ensure only the uploader can delete
    if (auth()->user()->id !== $document->user_id) {
        abort(403, 'Unauthorized action.');
    }

    // Delete the document
    $document->delete();

    // Check if the user is in the 'receiving' role
    if (auth()->user()->usertype === 'receiving') {
        return redirect()->route('dashboard')->with('success', 'Document deleted successfully.');
    }

    // Check if the user is in the 'section' role
    if (auth()->user()->usertype === 'section') {
        return redirect()->route('sectionsDashboard')->with('success', 'Document deleted successfully.');
    }

    // Optional fallback in case the role doesn't match any specific condition
    return redirect()->route('dashboard')->with('success', 'Document deleted successfully.');
}


public function update(Request $request, $id)
{
    $document = Document::findOrFail($id);

    // Ensure only the uploader can edit
    if (auth()->user()->id !== $document->user_id) {
        abort(403, 'Unauthorized action.');
    }

    // Validate input fields
    $request->validate([
        'locator_no' => 'required|string|max:255',
        'subject' => 'required|string|max:255',
        'date_received' => 'required|date',
        'details' => 'nullable|string',
        'file' => 'nullable|mimes:pdf,doc,docx,jpg,png|max:2048', // Allow different file types
    ]);

    // Update document details
    $document->locator_no = $request->locator_no;
    $document->subject = $request->subject;
    $document->date_received = $request->date_received;
    $document->details = $request->details;

    // Change status from "rejected" based on uploader's usertype
    if ($document->status === 'rejected') {
        $uploader = User::find($document->user_id); // Get uploader details

        if ($uploader && $uploader->usertype === 'section') {
            $document->status = 'checking'; // Set to "checking" for section uploads
        } elseif ($uploader && $uploader->role === 'receiving') {
            $document->status = 'pending'; // Set to "pending" for receiving uploads
        }
    }


    // Handle file upload
    if ($request->hasFile('file')) {
        // Delete the old file if it exists
        if ($document->hashed_file_name && Storage::exists('public/documents/' . $document->hashed_file_name)) {
            Storage::delete('public/documents/' . $document->hashed_file_name);
        }

        // Upload new file
        $file = $request->file('file');
        $originalFileName = $file->getClientOriginalName();
        $hashedFileName = hash('sha256', time() . $originalFileName) . '.' . $file->getClientOriginalExtension();

        // Store the file in the 'documents' directory within public storage
        $filePath = $file->storeAs('documents', $hashedFileName, 'public');

        // Update file details in the database
        $document->original_file_name = $originalFileName;
        $document->hashed_file_name = $hashedFileName;
        $document->file_path = $filePath; // Update the file_path with the new path
    }

    $document->save();

    return redirect()->route('dashboard')->with('success', 'Document updated successfully and is now pending approval.');
}


}

