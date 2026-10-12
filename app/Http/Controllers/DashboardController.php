<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Yönetim Paneli Ana Sayfası
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status');
        $search = trim($request->query('search', ''));
        $tab = $request->query('tab', 'overview');

        // İstatistikler
        $totalOrdersCount = Order::count();
        $totalRevenue = Order::whereHas('status', function ($q) {
            $q->where('name', '!=', Status::IPTAL);
        })->sum('total_amount');

        $statusCounts = [
            Status::BEKLEMEDE => Order::whereHas('status', fn ($q) => $q->where('name', Status::BEKLEMEDE))->count(),
            Status::HAZIRLANIYOR => Order::whereHas('status', fn ($q) => $q->where('name', Status::HAZIRLANIYOR))->count(),
            Status::KARGODA => Order::whereHas('status', fn ($q) => $q->where('name', Status::KARGODA))->count(),
            Status::TESLIM_EDILDI => Order::whereHas('status', fn ($q) => $q->where('name', Status::TESLIM_EDILDI))->count(),
            Status::IPTAL => Order::whereHas('status', fn ($q) => $q->where('name', Status::IPTAL))->count(),
        ];

        $totalCustomersCount = Customer::count();
        $totalShipmentsCount = Shipment::count();

        // Siparişler sorgusu
        $ordersQuery = Order::with(['customer', 'status', 'shipment'])->latest();

        if (!empty($statusFilter)) {
            $ordersQuery->whereHas('status', function ($q) use ($statusFilter) {
                $q->where('slug', $statusFilter)->orWhere('name', $statusFilter);
            });
        }

        if (!empty($search)) {
            $ordersQuery->where(function ($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $orders = $ordersQuery->get();

        // Kargolar
        $shipments = Shipment::with(['order.customer', 'order.status'])->latest()->get();

        // Müşteriler
        $customers = Customer::withCount('orders')->latest()->get();

        // Tüm durumlar
        $statuses = Status::all();

        return view('dashboard.index', compact(
            'totalOrdersCount',
            'totalRevenue',
            'statusCounts',
            'totalCustomersCount',
            'totalShipmentsCount',
            'orders',
            'shipments',
            'customers',
            'statuses',
            'statusFilter',
            'search',
            'tab'
        ));
    }

    /**
     * Sipariş Durumu Güncelleme
     */
    public function updateOrderStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status_id' => 'required|exists:statuses,id',
        ]);

        $order->update(['status_id' => $validated['status_id']]);
        $status = Status::find($validated['status_id']);

        // İlgili kargo tarihlerini senkronize et
        if ($order->shipment) {
            if ($status->name === Status::TESLIM_EDILDI && !$order->shipment->delivered_at) {
                $order->shipment->update([
                    'delivered_at' => Carbon::now(),
                    'current_status_desc' => 'Alıcıya başarıyla teslim edildi',
                ]);
            } elseif ($status->name === Status::KARGODA && !$order->shipment->shipped_at) {
                $order->shipment->update([
                    'shipped_at' => Carbon::now(),
                    'current_status_desc' => 'Kargo şirketine teslim edildi ve yola çıktı',
                ]);
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Sipariş durumu '{$status->name}' olarak güncellendi.",
                'status' => $status,
            ]);
        }

        return redirect()->back()->with('success', "Sipariş durumu '{$status->name}' olarak güncellendi.");
    }

    /**
     * Yeni Sipariş Oluşturma
     */
    public function storeOrder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'status_id' => 'required|exists:statuses,id',
            'total_amount' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:500',
            'company' => 'nullable|string|max:100',
            'tracking_number' => 'nullable|string|max:100|unique:shipments,tracking_number',
        ]);

        // Otomatik sipariş numarası üretimi
        $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        $order = Order::create([
            'customer_id' => $validated['customer_id'],
            'status_id' => $validated['status_id'],
            'order_number' => $orderNumber,
            'total_amount' => $validated['total_amount'],
            'note' => $validated['note'] ?? null,
        ]);

        // Kargo bilgisi de girildiyse shipment oluştur
        if (!empty($validated['company']) && !empty($validated['tracking_number'])) {
            $status = Status::find($validated['status_id']);
            $shippedAt = ($status->name === Status::KARGODA || $status->name === Status::TESLIM_EDILDI) ? Carbon::now() : null;
            $deliveredAt = ($status->name === Status::TESLIM_EDILDI) ? Carbon::now() : null;

            Shipment::create([
                'order_id' => $order->id,
                'company' => $validated['company'],
                'tracking_number' => $validated['tracking_number'],
                'shipped_at' => $shippedAt,
                'delivered_at' => $deliveredAt,
                'current_status_desc' => 'Kargo kaydı oluşturuldu',
            ]);
        }

        return redirect()->route('dashboard.index', ['tab' => 'orders'])
            ->with('success', "{$order->order_number} numaralı yeni sipariş başarıyla oluşturuldu.");
    }

    /**
     * Siparişe Kargo Ekleme veya Güncelleme
     */
    public function storeShipment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'company' => 'required|string|max:100',
            'tracking_number' => 'required|string|max:100|unique:shipments,tracking_number,' . $request->input('shipment_id'),
            'current_status_desc' => 'nullable|string|max:255',
        ]);

        $order = Order::findOrFail($validated['order_id']);

        $shipment = Shipment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'company' => $validated['company'],
                'tracking_number' => $validated['tracking_number'],
                'current_status_desc' => $validated['current_status_desc'] ?? 'Kargo şubede işlem görüyor',
                'shipped_at' => Carbon::now(),
            ]
        );

        // Sipariş durumunu Kargoda yap (eğer henüz beklemede veya hazırlanıyorsa)
        $kargodaStatus = Status::where('name', Status::KARGODA)->first();
        if ($kargodaStatus && in_array($order->status->name, [Status::BEKLEMEDE, Status::HAZIRLANIYOR])) {
            $order->update(['status_id' => $kargodaStatus->id]);
        }

        return redirect()->route('dashboard.index', ['tab' => 'shipments'])
            ->with('success', "{$shipment->tracking_number} takip numaralı kargo kaydedildi.");
    }

    /**
     * Yeni Müşteri Ekleme
     */
    public function storeCustomer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:customers,email',
            'phone' => 'nullable|string|max:30',
            'city' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
        ]);

        Customer::create($validated);

        return redirect()->route('dashboard.index', ['tab' => 'customers'])
            ->with('success', "Müşteri '{$validated['name']}' başarıyla eklendi.");
    }
}
