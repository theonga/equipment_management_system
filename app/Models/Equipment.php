<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'model',
        'acquisition_year',
        'acquisition_month',
        'company_id', 
        'is_new',
    ];

     /**
     * Get the company that owns the equipment.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
