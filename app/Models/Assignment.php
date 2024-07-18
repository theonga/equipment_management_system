<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Assignment extends Model
{
    use HasFactory;

   protected $fillable = [
        'equipment_id',
        'user_id',
        'assigned_at',
        'returned_at',
        'assigned_by_user_id',
        'status_on_return',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id');
    }

    protected static function boot()
    {
        parent::boot();

        // Automatically set assigned_by_user_id when creating a new assignment
        static::creating(function ($assignment) {
            // Check if there's an authenticated user
            if (Auth::check()) { 
                $assignment->assigned_by_user_id = Auth::id();
            }
        });
    }
}
