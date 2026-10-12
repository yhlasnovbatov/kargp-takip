<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrudResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Customer CRUD Testi: Index, Create, Store, Show, Edit, Update, Destroy
     */
    public function test_customer_full_crud(): void
    {
        // 1. Index
        $response = $this->get(route('customers.index'));
        $response->assertStatus(200);
        $response->assertSee('Müşteriler');

        // 2. Create form
        $response = $this->get(route('customers.create'));
        $response->assertStatus(200);

        // 3. Store (Kayıt Ekle)
        $uniqueEmail = 'test_cust_' . uniqid() . '@example.com';
        $postData = [
            'name' => 'Caner Öztürk',
            'email' => $uniqueEmail,
            'phone' => '0532 999 88 77',
            'city' => 'Eskişehir',
            'address' => 'Vişnelik Mah. Park Cad. No: 10',
        ];

        $response = $this->post(route('customers.store'), $postData);
        $response->assertRedirect(route('customers.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('customers', [
            'email' => $uniqueEmail,
            'name' => 'Caner Öztürk',
        ]);

        $customer = Customer::where('email', $uniqueEmail)->first();

        // 4. Show
        $response = $this->get(route('customers.show', $customer));
        $response->assertStatus(200);
        $response->assertSee('Caner Öztürk');

        // 5. Edit form
        $response = $this->get(route('customers.edit', $customer));
        $response->assertStatus(200);

        // 6. Update (Kayıt Düzenle)
        $updateData = [
            'name' => 'Caner Öztürk Güncellendi',
            'email' => $uniqueEmail,
            'phone' => '0532 111 22 33',
            'city' => 'Ankara',
            'address' => 'Çankaya Cad. No: 5',
        ];
        $response = $this->put(route('customers.update', $customer), $updateData);
        $response->assertRedirect(route('customers.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Caner Öztürk Güncellendi',
            'city' => 'Ankara',
        ]);

        // 7. Destroy (Kayıt Sil)
        $response = $this->delete(route('customers.destroy', $customer));
        $response->assertRedirect(route('customers.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('customers', [
            'id' => $customer->id,
        ]);
    }

    /**
     * Status CRUD Testi: Index, Create, Store, Show, Edit, Update, Destroy
     */
    public function test_status_full_crud(): void
    {
        // 1. Index
        $response = $this->get(route('statuses.index'));
        $response->assertStatus(200);
        $response->assertSee('Durumlar');

        // 2. Create form
        $response = $this->get(route('statuses.create'));
        $response->assertStatus(200);

        // 3. Store (Kayıt Ekle)
        $uniqueName = 'Yeni Durum ' . uniqid();
        $postData = [
            'name' => $uniqueName,
            'slug' => 'durum-' . uniqid(),
            'color' => '#8b5cf6',
            'description' => 'Test aşaması durum açıklaması',
        ];
        $response = $this->post(route('statuses.store'), $postData);
        $response->assertRedirect(route('statuses.index'));
        $response->assertSessionHas('success');

        $status = Status::where('name', $uniqueName)->first();
        $this->assertNotNull($status);

        // 4. Show
        $response = $this->get(route('statuses.show', $status));
        $response->assertStatus(200);

        // 5. Edit form
        $response = $this->get(route('statuses.edit', $status));
        $response->assertStatus(200);

        // 6. Update (Kayıt Düzenle)
        $updatedName = $uniqueName . ' (Revize)';
        $response = $this->put(route('statuses.update', $status), [
            'name' => $updatedName,
            'slug' => $status->slug,
            'color' => '#ec4899',
            'description' => 'Güncellenmiş açıklama',
        ]);
        $response->assertRedirect(route('statuses.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('statuses', [
            'id' => $status->id,
            'name' => $updatedName,
        ]);

        // 7. Destroy (Kayıt Sil)
        $response = $this->delete(route('statuses.destroy', $status));
        $response->assertRedirect(route('statuses.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('statuses', [
            'id' => $status->id,
        ]);
    }

    /**
     * Order CRUD Testi: Index, Create, Store, Show, Edit, Update, Destroy
     */
    public function test_order_full_crud(): void
    {
        $customer = Customer::first();
        $status = Status::first();

        // 1. Index (with N+1 prevention: with(['customer','status','shipment']))
        $response = $this->get(route('orders.index'));
        $response->assertStatus(200);
        $response->assertSee('Siparişler');

        // 2. Create form
        $response = $this->get(route('orders.create'));
        $response->assertStatus(200);

        // 3. Store (Kayıt Ekle)
        $orderNumber = 'TEST-ORD-' . uniqid();
        $postData = [
            'customer_id' => $customer->id,
            'status_id' => $status->id,
            'order_number' => $orderNumber,
            'total_amount' => 1950.50,
            'note' => 'CRUD Testi sipariş notudur.',
        ];
        $response = $this->post(route('orders.store'), $postData);
        $response->assertRedirect(route('orders.index'));
        $response->assertSessionHas('success');

        $order = Order::where('order_number', $orderNumber)->first();
        $this->assertNotNull($order);

        // 4. Show
        $response = $this->get(route('orders.show', $order));
        $response->assertStatus(200);
        $response->assertSee($orderNumber);

        // 5. Edit form
        $response = $this->get(route('orders.edit', $order));
        $response->assertStatus(200);

        // 6. Update (Kayıt Düzenle)
        $response = $this->put(route('orders.update', $order), [
            'customer_id' => $customer->id,
            'status_id' => $status->id,
            'order_number' => $orderNumber,
            'total_amount' => 2450.00,
            'note' => 'Güncellenmiş test sipariş notu.',
        ]);
        $response->assertRedirect(route('orders.index'));
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('2450.00', $order->total_amount);

        // 7. Destroy (Kayıt Sil)
        $response = $this->delete(route('orders.destroy', $order));
        $response->assertRedirect(route('orders.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('orders', [
            'id' => $order->id,
        ]);
    }

    /**
     * Shipment CRUD Testi: Index, Create, Store, Show, Edit, Update, Destroy
     */
    public function test_shipment_full_crud(): void
    {
        // Kargo atanmamış bir sipariş bul veya oluştur
        $customer = Customer::first();
        $status = Status::first();
        $tempOrder = Order::create([
            'customer_id' => $customer->id,
            'status_id' => $status->id,
            'order_number' => 'ORD-SHIP-TEST-' . uniqid(),
            'total_amount' => 850.00,
        ]);

        // 1. Index
        $response = $this->get(route('shipments.index'));
        $response->assertStatus(200);
        $response->assertSee('Kargo Gönderileri');

        // 2. Create form
        $response = $this->get(route('shipments.create') . '?order_id=' . $tempOrder->id);
        $response->assertStatus(200);

        // 3. Store (Kayıt Ekle)
        $trackingNumber = 'TEST-YK-' . uniqid();
        $postData = [
            'order_id' => $tempOrder->id,
            'company' => 'Yurtiçi Kargo',
            'tracking_number' => $trackingNumber,
            'current_status_desc' => 'Kadıköy Şubesinde Dağıtımda',
        ];
        $response = $this->post(route('shipments.store'), $postData);
        $response->assertRedirect(route('shipments.index'));
        $response->assertSessionHas('success');

        $shipment = Shipment::where('tracking_number', $trackingNumber)->first();
        $this->assertNotNull($shipment);

        // 4. Show
        $response = $this->get(route('shipments.show', $shipment));
        $response->assertStatus(200);
        $response->assertSee($trackingNumber);

        // 5. Edit form
        $response = $this->get(route('shipments.edit', $shipment));
        $response->assertStatus(200);

        // 6. Update (Kayıt Düzenle)
        $response = $this->put(route('shipments.update', $shipment), [
            'order_id' => $tempOrder->id,
            'company' => 'Aras Kargo',
            'tracking_number' => $trackingNumber,
            'current_status_desc' => 'Alıcıya Teslim Edildi',
        ]);
        $response->assertRedirect(route('shipments.index'));
        $response->assertSessionHas('success');

        $shipment->refresh();
        $this->assertEquals('Aras Kargo', $shipment->company);

        // 7. Destroy (Kayıt Sil)
        $response = $this->delete(route('shipments.destroy', $shipment));
        $response->assertRedirect(route('shipments.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('shipments', [
            'id' => $shipment->id,
        ]);

        // Temizlik: geçici siparişi de sil
        $tempOrder->delete();
    }
}
