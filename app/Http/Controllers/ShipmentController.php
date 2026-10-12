<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShipmentRequest;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ShipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $shipments = Shipment::with(['order.customer', 'order.status'])
            ->latest()
            ->paginate(10);

        return view('shipments.index', compact('shipments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $shipment = new Shipment();
        $orders = Order::with('customer')->latest()->get();

        return view('shipments.create', compact('shipment', 'orders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShipmentRequest $request): RedirectResponse
    {
        $shipment = Shipment::create($request->validated());

        return redirect()->route('shipments.index')
            ->with('success', "{$shipment->tracking_number} takip numaralı kargo başarıyla oluşturuldu.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Shipment $shipment): View
    {
        $shipment->load(['order.customer', 'order.status']);

        return view('shipments.show', compact('shipment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shipment $shipment): View
    {
        $orders = Order::with('customer')->latest()->get();

        return view('shipments.edit', compact('shipment', 'orders'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ShipmentRequest $request, Shipment $shipment): RedirectResponse
    {
        $shipment->update($request->validated());

        return redirect()->route('shipments.index')
            ->with('success', "{$shipment->tracking_number} takip numaralı kargo başarıyla güncellendi.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shipment $shipment): RedirectResponse
    {
        $trackingNumber = $shipment->tracking_number;
        $shipment->delete();

        return redirect()->route('shipments.index')
            ->with('success', "{$trackingNumber} takip numaralı kargo başarıyla silindi.");
    }
}
