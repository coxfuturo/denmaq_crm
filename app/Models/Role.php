<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'guard_name',
        'name_alias',
        'icon',
        'description',
        'position',
        'status',
        'created_by',
    ];

    protected $casts = [
        'position' => 'integer',
        'status' => 'boolean',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}