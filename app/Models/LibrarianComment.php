<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibrarianComment extends Model
{
    use HasFactory;

     // Specify the table associated with the model (optional if the table name follows conventions)
     protected $table = 'librarians_comments';

     // Define which columns are mass assignable
     protected $fillable = ['document_id', 'user_id', 'comment'];
 
     // Define the relationships
     public function document()
     {
         return $this->belongsTo(Document::class);
     }
 
     public function user()
     {
         return $this->belongsTo(User::class);
     }
}
