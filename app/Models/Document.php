<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;


class Document extends Model
{   
    use HasFactory;

    protected $fillable = [
        'user_id',
        'uploader_id',
        'locator_no',
        'subject',
        'received_from',
        'date_received',
        'date_filed', // Automatically handled in the controller
        'details',
        'status',
        'original_file_name',
        'hashed_file_name',
        'file_path',
        'uploaded_from',
        'is_reply'
    ];

    public function uploader()
{
    return $this->belongsTo(User::class, 'uploader_id');
}


    

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function documents() {
        return $this->hasMany(Document::class, 'user_id');
    }
    
    public function approve($id)
{
    $document = Document::findOrFail($id);
    $document->status = 'approved';
    $document->save();

    // Notify the user or take additional actions as needed
    return redirect()->back()->with('success', 'Document approved successfully');
}

public function reject(Request $request, $id)
{
    $document = Document::findOrFail($id);
    $document->status = 'rejected';
    $document->remarks = $request->input('remarks'); // Add remarks for rejection
    $document->save();

    // Notify the user or take additional actions as needed
    return redirect()->back()->with('success', 'Document rejected successfully');
}
public function sections()
{
    return $this->belongsToMany(Section::class, 'document_sections');
}
public function section()
{
    return $this->belongsTo(Section::class, 'section_id', 'id'); // This assumes a section_id field in documents table
}

public function selectedSections()
{
    return $this->belongsToMany(Section::class, 'document_sections')->withPivot('user_id');
}

public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function sectionComments()
    {
        return $this->hasMany(Comment::class, 'document_id')->whereHas('user', function ($query) {
            $query->where('usertype', 'section'); // Only fetch comments from Section users
        });
    }
    
    public function receivingComments()
{
    return $this->hasMany(Comment::class, 'document_id')->whereHas('user', function ($query) {
        $query->where('role', 'receiving'); // Only fetch comments from Receiving users
    });
}


    public function accomplishStatus()
    {
        $totalSections = $this->sections()->count();
        $accomplishedCount = $this->comments()->where('accomplish_status', 1)->count();
    
        if ($accomplishedCount == 0) {
            return 'Pending';
        }
        return "Accomplished ({$accomplishedCount}/{$totalSections})";
    }

    public function verifierComments()
    {
        return $this->hasMany(LibrarianComment::class, 'document_id')->whereHas('user', function ($query) {
            $query->where('role', 'verifier'); // Fetch only Verifier comments
        });
    }


    
    public function librarianComments()
    {
        return $this->hasMany(LibrarianComment::class, 'document_id')->whereHas('user', function ($query) {
            $query->whereIn('role', ['ACL', 'CL']); // Fetch only ACL and CL comments
        });
    }
public function documentSections()
{
    return $this->hasMany(DocumentSection::class, 'document_id');
}

    
public function replyComments()
{
    return $this->hasMany(ReplyComment::class);
}


}



