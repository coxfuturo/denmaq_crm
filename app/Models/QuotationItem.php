<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'item_name',
        'description',
        'quantity',
        'rate',
        'amount',
        'position',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'rate' => 'decimal:2',
        'amount' => 'decimal:2',
        'position' => 'integer',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
