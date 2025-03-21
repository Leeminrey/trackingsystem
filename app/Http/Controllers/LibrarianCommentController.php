<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LibrarianComment;
use App\Models\Document;

class LibrarianCommentController extends Controller
{
    // Constructor for middleware (optional)
    public function __construct()
    {
        // Uncomment if you want to add authentication
        // $this->middleware('auth');
    }

    // Store a new comment
    public function store(Request $request)
{
    // Validate incoming request
    $request->validate([
        'document_id' => 'required|exists:documents,id',
        'user_id' => 'required|exists:users,id', // Ensure it's a valid librarian (user)
        'comment' => 'required|string|max:1000',
    ]);

    // Check if the librarian has already commented on this document
    $existingComment = LibrarianComment::where('document_id', $request->document_id)
                                       ->where('user_id', $request->user_id)
                                       ->first();

    if ($existingComment) {
        // Update the existing comment
        $existingComment->comment = $request->comment;
        $existingComment->save(); // Save the updated comment
    } else {
        // Create a new comment if no existing comment
        $comment = LibrarianComment::create([
            'document_id' => $request->document_id,
            'user_id' => $request->user_id, // Assuming the logged-in user is the librarian
            'comment' => $request->comment,
        ]);
    }

    // Return the updated comment or success message
    return response()->json([
        'message' => $existingComment ? 'Comment updated successfully' : 'Comment added successfully',
        'comment' => $existingComment ? $existingComment : $comment,
    ], 201);
}


   

    // Optionally, you can create a method to update a comment
    public function update(Request $request, $commentId)
    {
        $comment = LibrarianComment::findOrFail($commentId);

        // Validate and update the comment
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $comment->update([
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'Comment updated successfully',
            'comment' => $comment,
        ]);
    }




}
