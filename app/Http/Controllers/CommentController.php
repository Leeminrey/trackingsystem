<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Comment;
use App\Models\ReplyComment;
use App\Models\LibrarianComment;


class CommentController extends Controller
{

    // public function store(Request $request, $documentId)
    // {
    //     $request->validate([
    //         'comments' => 'required|string|max:500',
    //     ]);
    
    //     $document = Document::findOrFail($documentId);
    
    //     // Prevent uploader from marking their own document as accomplished
    //     if ($document->uploader_id == auth()->user()->id) {
    //         return redirect()->back()->with('error', "You cannot mark your own document as accomplished.");
    //     }
    
    //     // Check if the user has already commented and marked as accomplished
    //     $existingComment = Comment::where('document_id', $document->id)
    //                               ->where('commenter_id', auth()->user()->id)
    //                               ->first();
    
    //     if ($existingComment) {
    //         $existingComment->comments = $request->comments;
    //         $existingComment->accomplish_status = 1;
    //         $existingComment->save();
    //     } else {
    //         Comment::create([
    //             'document_id'       => $document->id,
    //             'commenter_id'      => auth()->user()->id,
    //             'comments'          => $request->comments,
    //             'accomplish_status' => 1,
    //         ]);
    //     }
    
    //     // Update status to "completed" if at least one section has marked it as accomplished
    //     if ($this->anySectionAccomplished($document)) {
    //         $document->update(['status' => 'completed']);
    //     }
    
    //     // Get accomplishment status message for display (e.g., "Accomplished (1/2)")
    //     $accomplishmentStatus = $this->updateDocumentAccomplishStatus($document);
    
    //     return redirect()->back()->with('success', "Comment added successfully. {$accomplishmentStatus}");
    // }

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
