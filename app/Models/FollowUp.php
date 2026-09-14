<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FollowUp extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'follow_ups';

    protected $fillable = [
        'lead_id',
        'client_id',
        'assigned_to',
        'follow_up_date',
        'follow_up_time',
        'type',
        'subject',
        'notes',
        'priority',
        'status',
        'next_follow_up_date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
        'follow_up_time' => 'datetime:H:i',
        'next_follow_up_date' => 'date',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}