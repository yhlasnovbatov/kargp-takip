<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Status;
use Database\Seeders\StatusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCancelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // StatusSeeder'ı çalıştır
        $this->seed(StatusSeeder::class);
    }

    /**
     * Test: Teslim edilmiş sipariş iptal edilemez
     * (error flash'ı var, durum hâlâ delivered)
     */
    public function test_teslim_edilmis_siparis_iptal_edilemez(): void
    {
        $customer = Customer::create([
            'name' => 'Ahmet Yılmaz',
            'email' => 'ahmet@example.com',
            'phone' => '0532 555 10 20',
        ]);

        $deliveredStatus = Status::where('slug', 'delivered')->firstOrFail();

        $order = Order::create([
            'customer_id' => $customer->id,
            'status_id' => $deliveredStatus->id,
            'order_number' => 'ORD-DELIVERED-001',
            'total_amount' => 1250.00,
        ]);

        $this->assertTrue($order->isDelivered());

        // Cancel rotasına POST isteği gönder
        $response = $this->from(route('orders.show', $order))
            ->post(route('orders.cancel', $order));

        // Yönlendirme ve error flash mesajı kontrolü
        $response->assertRedirect(route('orders.show', $order));
        $response->assertSessionHas('error', 'Teslim edilmiş sipariş iptal edilemez.');

        // Siparişin durumu hâlâ delivered olmalı
        $order->refresh();
        $this->assertEquals('delivered', $order->status->slug);
        $this->assertTrue($order->isDelivered());
    }

    /**
     * Test: Beklemedeki sipariş iptal edilebilir
     * (success flash'ı var, durum cancelled)
     */
    public function test_beklemedeki_siparis_iptal_edilebilir(): void
    {
        $customer = Customer::create([
            'name' => 'Ayşe Demir',
            'email' => 'ayse@example.com',
            'phone' => '0543 444 30 40',
        ]);

        $pendingStatus = Status::where('slug', 'pending')->firstOrFail();

        $order = Order::create([
            'customer_id' => $customer->id,
            'status_id' => $pendingStatus->id,
            'order_number' => 'ORD-PENDING-001',
            'total_amount' => 750.00,
        ]);

        $this->assertFalse($order->isDelivered());

        // Cancel rotasına POST isteği gönder
        $response = $this->from(route('orders.show', $order))
            ->post(route('orders.cancel', $order));

        // Yönlendirme ve success flash mesajı kontrolü
        $response->assertRedirect(route('orders.show', $order));
        $response->assertSessionHas('success', 'Sipariş iptal edildi.');

        // Sipariş durumu cancelled olmalı
        $order->refresh();
        $this->assertEquals('cancelled', $order->status->slug);
    }

    /**
     * Test: Teslim edilmiş siparişin durumu update ile cancelled yapılamaz
     */
    public function test_teslim_edilmis_siparisin_durumu_update_ile_cancelled_yapilamaz(): void
    {
        $customer = Customer::create([
            'name' => 'Mehmet Kaya',
            'email' => 'mehmet@example.com',
            'phone' => '0555 333 50 60',
        ]);

        $deliveredStatus = Status::where('slug', 'delivered')->firstOrFail();
        $cancelledStatus = Status::where('slug', 'cancelled')->firstOrFail();

        $order = Order::create([
            'customer_id' => $customer->id,
            'status_id' => $deliveredStatus->id,
            'order_number' => 'ORD-DELIVERED-002',
            'total_amount' => 2100.00,
        ]);

        $this->assertTrue($order->isDelivered());

        // Update rotasına cancelled durumu ile PUT isteği gönder
        $response = $this->from(route('orders.edit', $order))
            ->put(route('orders.update', $order), [
                'customer_id' => $customer->id,
                'status_id' => $cancelledStatus->id,
                'order_number' => $order->order_number,
                'total_amount' => $order->total_amount,
            ]);

        // Yönlendirme ve validasyon/DomainException hata kontrolü
        $response->assertRedirect(route('orders.edit', $order));
        $response->assertSessionHasErrors();

        // Sipariş durumu iptal edilmemiş, hâlâ delivered kalmış olmalı
        $order->refresh();
        $this->assertEquals('delivered', $order->status->slug);
        $this->assertTrue($order->isDelivered());
    }
}
