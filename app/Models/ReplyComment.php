<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReplyComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'user_id',
        'comment',
    ];

    // Relationship with Document
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    // Relationship with User (commenter)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
