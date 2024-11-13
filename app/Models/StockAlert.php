<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'threshold_quantity',
        'status',
        'resolved_at'
    ];

    protected $casts = [
        'threshold_quantity' => 'decimal:2',
        'resolved_at' => 'datetime'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
