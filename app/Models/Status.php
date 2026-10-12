<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    use HasFactory;

    public const BEKLEMEDE = 'Beklemede';
    public const HAZIRLANIYOR = 'Hazırlanıyor';
    public const KARGODA = 'Kargoda';
    public const TESLIM_EDILDI = 'Teslim Edildi';
    public const IPTAL = 'İptal';

    public const SLUG_PENDING = 'pending';
    public const SLUG_PREPARING = 'preparing';
    public const SLUG_SHIPPED = 'shipped';
    public const SLUG_DELIVERED = 'delivered';
    public const SLUG_CANCELLED = 'cancelled';

    protected $fillable = [
        'name',
        'slug',
        'color',
        'badge_class',
        'icon',
        'description',
    ];

    /**
     * Bu duruma ait siparişler
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
