<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\Status;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin Kullanıcı
        User::firstOrCreate(
            ['email' => 'admin@kargotakip.com'],
            [
                'name' => 'Kargo Yönetici',
                'password' => bcrypt('password123'),
            ]
        );

        // 2. Durumlar (Statuses) - StatusSeeder çalıştır
        $this->call(StatusSeeder::class);
        $statusMap = Status::all()->keyBy('name');

        // 3. Müşteriler (Customers)
        $customersData = [
            [
                'name' => 'Ahmet Yılmaz',
                'email' => 'ahmet.yilmaz@example.com',
                'phone' => '0532 555 10 20',
                'address' => 'Bağdat Caddesi No: 142 Daire: 5, Kadıköy',
                'city' => 'İstanbul',
            ],
            [
                'name' => 'Ayşe Demir',
                'email' => 'ayse.demir@example.com',
                'phone' => '0543 444 30 40',
                'address' => 'Tunalı Hilmi Caddesi No: 88, Çankaya',
                'city' => 'Ankara',
            ],
            [
                'name' => 'Mehmet Kaya',
                'email' => 'mehmet.kaya@example.com',
                'phone' => '0555 333 50 60',
                'address' => 'Kordon Boyu Sokak No: 12/3, Alsancak',
                'city' => 'İzmir',
            ],
            [
                'name' => 'Zeynep Çelik',
                'email' => 'zeynep.celik@example.com',
                'phone' => '0505 222 70 80',
                'address' => 'Fatih Sultan Mehmet Bulvarı No: 45, Nilüfer',
                'city' => 'Bursa',
            ],
            [
                'name' => 'Emre Şahin',
                'email' => 'emre.sahin@example.com',
                'phone' => '0536 111 90 12',
                'address' => 'Konyaaltı Cad. Deniz Mah. Güneş Apt. No: 20',
                'city' => 'Antalya',
            ],
        ];

        $createdCustomers = [];
        foreach ($customersData as $cData) {
            $createdCustomers[] = Customer::updateOrCreate(['email' => $cData['email']], $cData);
        }

        // 4. Siparişler & Kargolar (Orders & Shipments)
        $now = Carbon::now();

        // Sipariş 1: Kargoda (Yurtiçi Kargo)
        $order1 = Order::updateOrCreate(
            ['order_number' => 'ORD-2026101-001'],
            [
                'customer_id' => $createdCustomers[0]->id,
                'status_id' => $statusMap[Status::KARGODA]->id,
                'total_amount' => 2450.00,
                'note' => 'Kırılabilir hassas paket, zili çalmadan önce lütfen arayın.',
                'created_at' => $now->copy()->subDays(2),
            ]
        );
        Shipment::updateOrCreate(
            ['tracking_number' => 'YK-984210384'],
            [
                'order_id' => $order1->id,
                'company' => 'Yurtiçi Kargo',
                'shipped_at' => $now->copy()->subDays(1)->setHour(14)->setMinute(30),
                'delivered_at' => null,
                'current_status_desc' => 'Kadıköy Dağıtım Merkezinde - Kurye dağıtıma çıktı',
            ]
        );

        // Sipariş 2: Teslim Edildi (Aras Kargo)
        $order2 = Order::updateOrCreate(
            ['order_number' => 'ORD-2026101-002'],
            [
                'customer_id' => $createdCustomers[1]->id,
                'status_id' => $statusMap[Status::TESLIM_EDILDI]->id,
                'total_amount' => 1180.50,
                'note' => 'Adres ofis adresidir, mesai saatleri içinde teslim edilmelidir.',
                'created_at' => $now->copy()->subDays(4),
            ]
        );
        Shipment::updateOrCreate(
            ['tracking_number' => 'AR-419283712'],
            [
                'order_id' => $order2->id,
                'company' => 'Aras Kargo',
                'shipped_at' => $now->copy()->subDays(3)->setHour(10)->setMinute(15),
                'delivered_at' => $now->copy()->subDay()->setHour(15)->setMinute(40),
                'current_status_desc' => 'Alıcıya bizzat imza karşılığı teslim edildi',
            ]
        );

        // Sipariş 3: Hazırlanıyor (MNG Kargo takip no atanmış)
        $order3 = Order::updateOrCreate(
            ['order_number' => 'ORD-2026101-003'],
            [
                'customer_id' => $createdCustomers[2]->id,
                'status_id' => $statusMap[Status::HAZIRLANIYOR]->id,
                'total_amount' => 3890.00,
                'note' => 'Hediye paketi yapılsın, fatura pakete konulmasın.',
                'created_at' => $now->copy()->subHours(18),
            ]
        );
        Shipment::updateOrCreate(
            ['tracking_number' => 'MNG-77123984'],
            [
                'order_id' => $order3->id,
                'company' => 'MNG Kargo',
                'shipped_at' => null,
                'delivered_at' => null,
                'current_status_desc' => 'Kargo barkodu oluşturuldu, depoda sevk bekliyor',
            ]
        );

        // Sipariş 4: Beklemede
        $order4 = Order::updateOrCreate(
            ['order_number' => 'ORD-2026101-004'],
            [
                'customer_id' => $createdCustomers[3]->id,
                'status_id' => $statusMap[Status::BEKLEMEDE]->id,
                'total_amount' => 640.00,
                'note' => 'Havale ile ödeme yapıldı, dekont onayı bekleniyor.',
                'created_at' => $now->copy()->subHours(4),
            ]
        );

        // Sipariş 5: İptal
        $order5 = Order::updateOrCreate(
            ['order_number' => 'ORD-2026101-005'],
            [
                'customer_id' => $createdCustomers[4]->id,
                'status_id' => $statusMap[Status::IPTAL]->id,
                'total_amount' => 1520.00,
                'note' => 'Müşteri yanlış adres girdiği gerekçesiyle siparişi iptal etti.',
                'created_at' => $now->copy()->subDays(1),
            ]
        );

        // Sipariş 6: Kargoda (Trendyol Express)
        $order6 = Order::updateOrCreate(
            ['order_number' => 'ORD-2026101-006'],
            [
                'customer_id' => $createdCustomers[3]->id,
                'status_id' => $statusMap[Status::KARGODA]->id,
                'total_amount' => 875.25,
                'note' => 'Kapıda bırakılabilir.',
                'created_at' => $now->copy()->subDays(1)->subHours(8),
            ]
        );
        Shipment::updateOrCreate(
            ['tracking_number' => 'TY-558291039'],
            [
                'order_id' => $order6->id,
                'company' => 'Trendyol Express',
                'shipped_at' => $now->copy()->subHours(12),
                'delivered_at' => null,
                'current_status_desc' => 'Bursa Transfer Merkezinden yola çıktı',
            ]
        );
    }
}
