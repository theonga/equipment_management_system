<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    // Define fillable attributes
    protected $fillable = ['name'];

    // Define the relationship with the User model
    public function users()
    {
        // Assuming the existence of a 'role_user' pivot table
        return $this->belongsToMany(User::class);
    }
}