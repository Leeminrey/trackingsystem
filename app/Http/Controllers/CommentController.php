<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Comment;
use App\Models\ReplyComment;

class CommentController extends Controller
{

    public function store(Request $request, $documentId)
    {
        $request->validate([
            'comments' => 'required|string|max:500',
            'action_type' => 'required|string',
        ]);
    
        $document = Document::findOrFail($documentId);
    
        // Prevent uploader from marking their own document as accomplished
        if ($document->uploader_id == auth()->user()->id && $request->action_type === 'accomplish') {
            return redirect()->back()->with('error', "You cannot mark your own document as accomplished.");
        }
    
        if ($request->action_type === 'accomplish') {
            // Handle accomplish action (save in comments table)
            $existingComment = Comment::where('document_id', $document->id)
                                    ->where('commenter_id', auth()->user()->id)
                                    ->first();
    
            if ($existingComment) {
                $existingComment->comments = $request->comments;
                $existingComment->accomplish_status = 1;
                $existingComment->save();
            } else {
                Comment::create([
                    'document_id'       => $document->id,
                    'commenter_id'      => auth()->user()->id,
                    'comments'          => $request->comments,
                    'accomplish_status' => 1,
                ]);
            }
    
            // Check if the document should be marked as completed
            if ($this->anySectionAccomplished($document)) {
                $document->update(['status' => 'completed']);
            }
    
            $message = "Comment added and marked as accomplished successfully. " . $this->updateDocumentAccomplishStatus($document);
        } 
        elseif ($request->action_type === 'reply') {
            // Handle reply action (save in reply_comments table)
            ReplyComment::create([
                'document_id' => $document->id,
                'user_id'     => auth()->user()->id,
                'comment'     => $request->comments,
            ]);
    
            // Change document status back to 'pending' and update fields with "RE:"
            if ($document->status === 'approved') {
                $document->status = 'checking';
                $document->uploaded_from = 'outgoing';
            }
    
            $document->locator_no = str_starts_with($document->locator_no, 'RE:') ? $document->locator_no : 'RE: ' . $document->locator_no;
            $document->subject = str_starts_with($document->subject, 'RE:') ? $document->subject : 'RE: ' . $document->subject;
            $document->received_from = str_starts_with($document->received_from, 'RE:') ? $document->received_from : 'RE: ' . $document->received_from;
            $document->user->name = str_starts_with($document->user->name, 'RE:') ? $document->user->name : 'RE: ' . $document->user->name;
            $document->save();
    
            $message = "Reply added successfully. Document status set to pending and details updated.";
        }
    
        return redirect()->back()->with('success', $message);
    }


    
    
    /**
     * Checks if at least one assigned section has marked the document as accomplished.
     */
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
