<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentSection extends Model
{
    use HasFactory;
    protected $table = 'document_sections'; // Specify your table name if it differs from the plural form of the model

    protected $fillable = ['document_id', 'section_id', 'user_id']; // Add fillable fields as needed

    // Define relationships if necessary
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
    public function users()
    {
        return $this->belongsTo(Section::class);
    }
}
