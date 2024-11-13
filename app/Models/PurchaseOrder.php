<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;


    const STATUSES = [
        'draft' => 'Draft',
        'sent' => 'Sent to Vendor',
        'received' => 'Received',
        'cancelled' => 'Cancelled'
    ];


    protected $fillable = [
        'po_number',
        'vendor_id',
        'user_id',
        'status',
        'total_amount',
        'notes',
        'expected_date',
        'received_date'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'expected_date' => 'date',
        'received_date' => 'date'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
}
