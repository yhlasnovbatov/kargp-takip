<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => Status::BEKLEMEDE,
                'slug' => 'pending',
                'color' => '#f59e0b',
                'badge_class' => 'status-badge-pending',
                'icon' => 'clock',
                'description' => 'Sipariş sistemde kayıtlı, hazırlık ve onay bekleniyor.',
            ],
            [
                'name' => Status::HAZIRLANIYOR,
                'slug' => 'preparing',
                'color' => '#0ea5e9',
                'badge_class' => 'status-badge-preparing',
                'icon' => 'package',
                'description' => 'Ürünler paketleniyor ve sevk irsaliyesi düzenleniyor.',
            ],
            [
                'name' => Status::KARGODA,
                'slug' => 'shipped',
                'color' => '#6366f1',
                'badge_class' => 'status-badge-shipping',
                'icon' => 'truck',
                'description' => 'Kargo şirketine teslim edildi, alıcı adresine transfer ediliyor.',
            ],
            [
                'name' => Status::TESLIM_EDILDI,
                'slug' => 'delivered',
                'color' => '#10b981',
                'badge_class' => 'status-badge-delivered',
                'icon' => 'check-circle',
                'description' => 'Kargo alıcıya başarıyla teslim edildi.',
            ],
            [
                'name' => Status::IPTAL,
                'slug' => 'cancelled',
                'color' => '#ef4444',
                'badge_class' => 'status-badge-cancelled',
                'icon' => 'x-circle',
                'description' => 'Sipariş müşteri veya mağaza talebiyle iptal edildi.',
            ],
        ];

        foreach ($statuses as $data) {
            Status::updateOrCreate(
                ['name' => $data['name']],
                $data
            );
        }
    }
}
