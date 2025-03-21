<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

     // Specify the table name (if not following Laravel's naming convention)
     protected $table = 'comments';

     // Specify the fields that are mass assignable
     protected $fillable = [
         'document_id',
         'commenter_id',
         'comments',
         'accomplish_status',
     ];
 
     // Define the relationship with the Document model
     public function user()
     {
         return $this->belongsTo(User::class, 'commenter_id');
     }
     
     public function document()
     {
         return $this->belongsTo(Document::class);
     }

     public function section()
    {
        return $this->belongsTo(Section::class, 'section_id'); 
    }

    public function commenter()
    {
        return $this->belongsTo(User::class, 'commenter_id');
    }

}
