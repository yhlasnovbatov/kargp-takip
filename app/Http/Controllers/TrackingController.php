<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shipment;
use App\Models\Status;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackingController extends Controller
{
    /**
     * Kargo Takip Ana Sayfası
     */
    public function index(Request $request): View
    {
        $query = trim($request->query('q', ''));
        $result = null;

        if (!empty($query)) {
            $result = $this->findTracking($query);
        }

        // Örnek takip numaraları (kullanıcının hemen test edebilmesi için)
        $sampleShipments = Shipment::with(['order.status', 'order.customer'])
            ->latest()
            ->take(4)
            ->get();

        return view('tracking.index', [
            'query' => $query,
            'result' => $result,
            'sampleShipments' => $sampleShipments,
        ]);
    }

    /**
     * AJAX Arama Endpoint'i
     */
    public function search(Request $request): JsonResponse
    {
        $query = trim($request->input('query', ''));

        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Lütfen bir takip numarası veya sipariş numarası giriniz.',
            ], 422);
        }

        $result = $this->findTracking($query);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Belirtilen takip veya sipariş numarası ile eşleşen kargo bulunamadı.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Takip No veya Sipariş No üzerinden kargo bulma
     */
    private function findTracking(string $query): ?array
    {
        // 1. Önce kargo takip numarası ile ara
        $shipment = Shipment::with(['order.customer', 'order.status'])
            ->where('tracking_number', 'LIKE', $query)
            ->first();

        // 2. Bulunamadıysa sipariş numarası ile ara
        if (!$shipment) {
            $order = Order::with(['customer', 'status', 'shipment'])
                ->where('order_number', 'LIKE', $query)
                ->first();

            if ($order && $order->shipment) {
                $shipment = $order->shipment;
                $shipment->setRelation('order', $order);
            } elseif ($order) {
                // Sipariş var ama henüz kargoya verilmemiş olabilir
                return [
                    'type' => 'order_only',
                    'order' => $order,
                    'customer' => $order->customer,
                    'status' => $order->status,
                    'shipment' => null,
                ];
            }
        }

        if (!$shipment) {
            return null;
        }

        return [
            'type' => 'shipment',
            'shipment' => $shipment,
            'order' => $shipment->order,
            'customer' => $shipment->order->customer,
            'status' => $shipment->order->status,
        ];
    }
}
