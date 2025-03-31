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
    
        // Check if the user is allowed to accomplish the document
        $canAccomplish = ($document->uploader_id == auth()->id() && $document->is_reply) || ($document->uploader_id != auth()->id());
    
        if ($request->action_type === 'accomplish' && $canAccomplish) {
            // Handle "Accomplish" action
            $existingComment = LibrarianComment::where('document_id', $document->id)
                ->where('user_id', auth()->id())
                ->first();
    
            if ($existingComment) {
                $existingComment->update([
                    'comments'          => $request->comments,
                    'accomplish_status' => 1,
                    'reply_phase'       => $document->is_reply,
                ]);
            } else {
                Comment::create([
                    'document_id'       => $document->id,
                    'commenter_id'      => auth()->user()->id,
                    'comments'          => $request->comments,
                    'accomplish_status' => 1,
                ]);
            }
    
            // If at least one section accomplished, mark document as completed
            if ($this->anySectionAccomplished($document)) {
                $document->update(['status' => 'completed']);
            }
    
            // Update accomplishment status message
            $accomplishmentStatus = $this->updateDocumentAccomplishStatus($document);
    
            $message = "Comment added and marked as accomplished successfully. {$accomplishmentStatus}";
    
        } elseif ($request->action_type === 'reply') {
            // Handle "Reply" action
            ReplyComment::create([
                'document_id' => $document->id,
                'user_id'     => auth()->id(),
                'comment'     => $request->comments,
            ]);
    
            // Update document details when replying
            $document->update([
                'is_reply'      => 1,
                'status'        => 'checking',
                'uploaded_from' => 'outgoing',
                'user_id'       => auth()->id(),
            ]);
    
            // Add "RE:" prefix if not already added
            $fieldsToUpdate = ['locator_no', 'subject', 'received_from'];
            foreach ($fieldsToUpdate as $field) {
                if (!str_starts_with($document->$field, 'RE:')) {
                    $document->$field = 'RE: ' . $document->$field;
                }
            }
            $document->save();
    
            $message = "Reply added successfully. Document status set to checking.";
    
        } else {
            // Handle regular comments
            LibrarianComment::create([
                'document_id'  => $document->id,
                'user_id'      => auth()->id(),
                'comments'     => $request->comments,
                'reply_phase'  => 1,
            ]);
    
            $message = "Comment added successfully.";
        }
    
        return redirect()->back()->with('success', $message);
    }
    

    protected function anySectionAccomplished(Document $document)
{
    // Check if at least one section has marked the document as "completed"
    return $document->documentSections()
                    ->where('status', 'completed')
                    ->exists();
}

/**
 * Returns a status message showing the accomplished count over total sections.
 */
protected function updateDocumentAccomplishStatus(Document $document)
{
    // Get total number of assigned sections
    $sectionsCount = $document->sections()->count();

    // Count how many sections have accomplished it
    $accomplishedCount = $document->comments()
                                  ->where('accomplish_status', 1) // Only count completed ones
                                  ->distinct()
                                  ->count('commenter_id');

    // Update document status in the "documents" table only when all sections have completed it
    if ($accomplishedCount === $sectionsCount && $sectionsCount > 0) {
        $document->update(['status' => 'completed']);
    }

    // Return progress status
    return "Accomplished ({$accomplishedCount}/{$sectionsCount})";
}
    
    


}
