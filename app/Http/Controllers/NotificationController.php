<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Document; // Assuming you have a Document model
use App\Models\User; // Assuming you have a User model to fetch users
use App\Models\DocumentSection;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{

    
   // Method to create a new notification
   public function createNotification($userId, $documentId, $message)
{
    // Get the document by its ID
    $document = Document::find($documentId);

    // Check if the document exists and retrieve the uploader's user_id
    if ($document) {
        $uploaderName = $document->user->name;  // Get the uploader's name from the associated user
    } else {
        // Handle the case where the document does not exist
        $uploaderName = 'Unknown User';  // Default name in case of an issue
    }

    // Create the notification
    Notification::create([
        'user_id' => $userId,
        'document_id' => $documentId,
        'uploader_name' => $uploaderName,  // Store the uploader's name
        'message' => $message,
        'is_read' => false,
    ]);
}

   

   

   

   // Method to fetch notifications for the logged-in user
   public function fetchNotifications()
   {
       $notifications = Notification::where('user_id', Auth::id())
           ->orderBy('created_at', 'desc')
           ->get();

       return response()->json([
           'unreadCount' => $notifications->where('is_read', false)->count(),
           'notifications' => $notifications,
       ]);
   }

   // Mark notification as read
// app/Http/Controllers/NotificationController.php

public function markAsRead($id)
{
    try {
        // Find the notification by ID and update the read status to 1
        $notification = Notification::findOrFail($id);
        $notification->is_read = 1; // Set to 1 to mark as read
        $notification->save();

        return response()->json(['message' => 'Notification marked as read'], 200);
    } catch (\Exception $e) {
        // Handle errors gracefully
        return response()->json(['message' => 'Error marking notification as read'], 500);
    }
}


   // Remove rejected notifications if file later approved
   public function removeRejectionNotification($documentId)
   {
       Notification::where('document_id', $documentId)
           ->where('message', 'like', '%rejected%')
           ->delete();
   }

}
