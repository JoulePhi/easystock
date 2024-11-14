<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'sku',
        'category_id',
        'unit',
        'minimum_stock',
        'current_stock',
        'price',
        'cost',
        'vendor_id',
        'is_active',
        'image',
    ];

    protected $casts = [
        'minimum_stock' => 'integer',
        'current_stock' => 'integer',
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'image_url',
    ];



    public function getImageUrlAttribute()
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : null;
    }

    public function category()
    {
        return $this->belongsTo(ItemCategory::class, 'category_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function stockAlerts()
    {
        return $this->hasMany(StockAlert::class);
    }
}
