<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_users', 'user_id', 'role_id');
    }

    public function comments(): BelongsToMany
    {
        return $this->belongsToMany(Ticket::class, 'comments', 'user_id', 'ticket_id');
    }

    public function userComment(): hasMany
    {
        return $this->hasMany(Comment::class);
    }

    // Define the tickets that belong to the user
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'creator');
    }

    // Define the tickets that the user has assigned
    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assignor');
    }

    // Define the tickets that the user is assigned to
    public function assignedToTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assignee');
    }
}
