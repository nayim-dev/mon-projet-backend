<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // TRÈS IMPORTANT

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Assure-toi que 'role' est bien ici
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}