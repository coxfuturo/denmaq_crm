<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
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

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
