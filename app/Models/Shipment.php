<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'company',
        'tracking_number',
        'shipped_at',
        'delivered_at',
        'current_status_desc',
    ];

    protected $casts = [
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Kargonun bağlı olduğu sipariş
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
