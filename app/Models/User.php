<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
     
    
    use HasFactory, Notifiable;

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'employee_id',
        'password',
        'usertype',
        'role',
    ];

    public function isReceiving()
    {
        return $this->usertype === 'user' && $this->role === 'receiving';
    }

    



    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function section()
{
    return $this->belongsTo(Section::class);
}

public function sections()
{
    return $this->belongsToMany(Section::class, 'section_user');
}

public function librarianComments()
{
    return $this->hasMany(LibrarianComment::class);
}




    
}
