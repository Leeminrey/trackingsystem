<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Comment;

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

    // Check if the user has already commented
    $existingComment = Comment::where('document_id', $document->id)
                              ->where('commenter_id', auth()->user()->id)
                              ->first();

    if ($existingComment) {
        $existingComment->comments = $request->comments;

        // Set accomplish status only for 'accomplish' action
        if ($request->action_type === 'accomplish') {
            $existingComment->accomplish_status = 1;
        } else {
            $existingComment->accomplish_status = 0; // Reset accomplish status for replies
        }

        $existingComment->save();
    } else {
        Comment::create([
            'document_id'       => $document->id,
            'commenter_id'      => auth()->user()->id,
            'comments'          => $request->comments,
            'accomplish_status' => $request->action_type === 'accomplish' ? 1 : 0,
        ]);
    }

    // If it's an accomplish action, check and update status
    if ($request->action_type === 'accomplish' && $this->anySectionAccomplished($document)) {
        $document->update(['status' => 'completed']);
    }

    // If it's a reply, change document status back to 'pending' and update fields with "RE:"
    if ($request->action_type === 'reply') {
        if ($document->status === 'approved') {
            $document->status = 'pending';
            $document->uploaded_from = 'incoming';
        }

        // Add "RE:" to specific fields (prevent double "RE:" prefix)
        $document->locator_no = str_starts_with($document->locator_no, 'RE:') ? $document->locator_no : 'RE: ' . $document->locator_no;
        $document->subject = str_starts_with($document->subject, 'RE:') ? $document->subject : 'RE: ' . $document->subject;
        $document->received_from = str_starts_with($document->received_from, 'RE:') ? $document->received_from : 'RE: ' . $document->received_from;
        $document->user->name = str_starts_with($document->user->name, 'RE:') ? $document->user->name : 'RE: ' . $document->user->name;
        $document->save();
    }

    // Prepare success message
    $message = $request->action_type === 'accomplish'
        ? "Comment added and marked as accomplished successfully. " . $this->updateDocumentAccomplishStatus($document)
        : "Reply added successfully. Document status set to pending and details updated.";

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
