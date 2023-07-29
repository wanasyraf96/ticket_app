<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'creator',
        'assignor',
        'assignee',
        'title',
        'description',
        'priority',
        'status',
        'completed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'deleted_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Define the user who owns the ticket
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator');
    }

    // Define the user who assigned the ticket
    public function assignor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignor');
    }

    // Define the user who is assigned to the ticket
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee');
    }

    public function ticketComment(): HasMany
    {
        return $this->hasMany(Comment::class, 'ticket_id');
    }

    public function comments(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'comments', 'ticket_id', 'user_id');
    }
}
