<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    public function costumes()
    {
        return $this->hasMany(Costume::class);
    }
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'address',     
        'profile_picture',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function store()
    {
        return $this->hasOne(RenterStore::class);
    }

    public function renter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function renterStore()
    {
        return $this->renter->store();
    }
}