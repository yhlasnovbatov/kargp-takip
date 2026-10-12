<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
    ];

    /**
     * Müşterinin siparişleri
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Müşterinin kargoları (siparişler üzerinden)
     */
    public function shipments(): HasManyThrough
    {
        return $this->hasManyThrough(Shipment::class, Order::class);
    }
}
