<?php

namespace App\Models;

use DomainException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'status_id',
        'order_number',
        'total_amount',
        'note',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    /**
     * Model boot olayları
     */
    protected static function booted(): void
    {
        static::updating(function (Order $order) {
            if ($order->isDirty('status_id')) {
                $originalStatusId = $order->getOriginal('status_id');
                $originalStatus = Status::find($originalStatusId);
                $newStatus = Status::find($order->status_id);

                if ($originalStatus?->slug === 'delivered' && $newStatus?->slug === 'cancelled') {
                    throw new DomainException('Teslim edilmiş sipariş iptal edilemez.');
                }
            }
        });
    }

    /**
     * Siparişin teslim edilip edilmediğini kontrol eder
     */
    public function isDelivered(): bool
    {
        if ($this->relationLoaded('status')) {
            return $this->status?->slug === 'delivered';
        }

        return $this->status()->value('slug') === 'delivered';
    }

    /**
     * Siparişin müşterisi
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Siparişin güncel durumu
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Siparişin kargo bilgisi
     */
    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class);
    }

    /**
     * Formatlanmış para birimi
     */
    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total_amount, 2, ',', '.') . ' ₺';
    }
}
