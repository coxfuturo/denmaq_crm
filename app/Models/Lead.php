<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'company_name',
        'email',
        'phone',
        'alternate_phone',
        'source',
        'service',
        'status',
        'assigned_to',
        'follow_up_date',
        'budget',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
        'budget' => 'decimal:2',
    ];

    public function assignedUser()
    {
        return $this->belongsTo(
            User::class,
            'assigned_to'
        );
    }

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
}
