<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'category',
        'section_id',
        'user_id',
    ];

    public function documents()
{
    return $this->belongsToMany(Document::class, 'document_sections');
}
public function users()
{
    return $this->hasMany(User::class);
}



    
}
