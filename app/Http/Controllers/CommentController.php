<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Comment;
use App\Models\ReplyComment;
use App\Models\LibrarianComment;


class CommentController extends Controller
{

 public function store(Request $request, $documentId)
{
    $request->validate([
        'comments'    => 'required|string|max:500',
        'action_type' => 'required|string',
    ]);
    

    $document = Document::findOrFail($documentId);

    // Prevent uploader from marking their own document as accomplished
    if ($document->uploader_id == auth()->id() && $request->action_type === 'accomplish') {
        return redirect()->back()->with('error', "You cannot mark your own document as accomplished.");
    }

    if ($request->action_type === 'accomplish') {
        // Handle accomplish action
        $existingComment = LibrarianComment::where('document_id', $document->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existingComment) {
            $existingComment->update([
                'comments'          => $request->comments,
                'accomplish_status' => 1,
                'reply_phase'       => $document->is_reply,  // Sync with document's is_reply
            ]);
        } else {
            LibrarianComment::create([
                'document_id'       => $document->id,
                'user_id'           => auth()->id(),
                'comments'          => $request->comments,
                'accomplish_status' => 1,
                'reply_phase'       => $document->is_reply,  // Sync with document's is_reply
            ]);
        }

        $message = "Comment added and marked as accomplished successfully.";
    } elseif ($request->action_type === 'reply') {
        // Handle reply action and save in 'reply_comments' table
        ReplyComment::create([
            'document_id' => $document->id,
            'user_id'     => auth()->id(),
            'comment'     => $request->comments,
        ]);


        // Update document: Set is_reply and status
        $document->update([
            'is_reply'      => 1,
            'status'        => 'checking',
            'uploaded_from' => 'outgoing',
            'user_id'       => auth()->id(),
        ]);

        // Add "RE:" prefix only if not already added
        $fieldsToUpdate = ['locator_no', 'subject', 'received_from'];
        foreach ($fieldsToUpdate as $field) {
            if (!str_starts_with($document->$field, 'RE:')) {
                $document->$field = 'RE: ' . $document->$field;
            }
        }
        $document->save();

        $message = "Reply added successfully. Document status set to checking.";
    } else {
        // Handle regular comments — Sync reply_phase with document's is_reply
        LibrarianComment::create([
            'document_id'  => $document->id,
            'user_id'      => auth()->id(),
            'comments'     => $request->comments,
            'reply_phase'  => 1,  // Sync with document's is_reply
        ]);

        $message = "Comment added successfully.";
    }

    return redirect()->back()->with('success', $message);
}



    protected function anySectionAccomplished(Document $document)
    {
        $accomplishedCount = $document->comments()
                                      ->where('accomplish_status', 1)
                                      ->distinct()
                                      ->count('commenter_id');
    
        return $accomplishedCount >= 1;
    }
    
    /**
     * Returns a status message showing the accomplished count over total sections.
     */
    protected function updateDocumentAccomplishStatus(Document $document)
    {
        $sectionsCount = $document->sections()->count();
        $accomplishedCount = $document->comments()
                                      ->where('accomplish_status', 1)
                                      ->distinct()
                                      ->count('commenter_id');
    
        return "Accomplished ({$accomplishedCount}/{$sectionsCount})";
    }


}
