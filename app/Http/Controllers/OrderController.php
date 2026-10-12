<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Status;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // N+1 sorgudan kaçınmak için Order::with(['customer','status','shipment']) ve paginate(10)
        $orders = Order::with(['customer', 'status', 'shipment'])
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $order = new Order();
        $customers = Customer::orderBy('name')->get();
        $statuses = Status::orderBy('name')->get();

        return view('orders.create', compact('order', 'customers', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderRequest $request): RedirectResponse
    {
        $order = Order::create($request->validated());

        return redirect()->route('orders.index')
            ->with('success', "{$order->order_number} numaralı sipariş başarıyla oluşturuldu.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): View
    {
        $order->load(['customer', 'status', 'shipment']);

        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order): View
    {
        $customers = Customer::orderBy('name')->get();
        $statuses = Status::orderBy('name')->get();

        return view('orders.edit', compact('order', 'customers', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderRequest $request, Order $order): RedirectResponse
    {
        try {
            $order->update($request->validated());

            return redirect()->route('orders.index')
                ->with('success', "{$order->order_number} numaralı sipariş başarıyla güncellendi.");
        } catch (DomainException $e) {
            return back()->withInput()->withErrors([
                'status_id' => $e->getMessage(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Siparişi iptal et
     */
    public function cancel(Order $order): RedirectResponse
    {
        if ($order->isDelivered()) {
            return back()->with('error', 'Teslim edilmiş sipariş iptal edilemez.');
        }

        $cancelledStatus = Status::where('slug', 'cancelled')->firstOrFail();
        $order->update(['status_id' => $cancelledStatus->id]);

        return back()->with('success', 'Sipariş iptal edildi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order): RedirectResponse
    {
        $orderNumber = $order->order_number;
        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', "{$orderNumber} numaralı sipariş başarıyla silindi.");
    }
}
