@extends('layouts.app')

@section('title', 'Siparişler | KargoTakip PRO')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1">Siparişler</h1>
        <p class="text-muted mb-0">Tüm siparişleri, müşteri detaylarını ve teslimat süreçlerini yönetin.</p>
    </div>
    <a href="{{ route('orders.create') }}" class="btn btn-primary d-flex align-items-center gap-1" id="btn-create-order">
        <i class="bi bi-cart-plus-fill"></i> Yeni Sipariş Oluştur
    </a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col" style="width: 140px;">Sipariş No</th>
                    <th scope="col">Müşteri</th>
                    <th scope="col">Tutar</th>
                    <th scope="col">Durum</th>
                    <th scope="col">Kargo Durumu</th>
                    <th scope="col">Tarih</th>
                    <th scope="col" class="text-end" style="width: 180px;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr id="order-row-{{ $order->id }}">
                        <td>
                            <a href="{{ route('orders.show', $order) }}" class="fw-bold font-monospace text-primary text-decoration-none">
                                {{ $order->order_number }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('customers.show', $order->customer) }}" class="fw-semibold text-dark text-decoration-none">
                                {{ $order->customer->name }}
                            </a>
                            <small class="text-muted d-block">{{ $order->customer->city ?? 'Türkiye' }}</small>
                        </td>
                        <td>
                            <strong class="text-dark">{{ $order->formatted_total }}</strong>
                        </td>
                        <td>
                            <span class="badge" style="background-color: {{ $order->status->color ?? '#64748b' }}; color: #ffffff;">
                                {{ $order->status->name }}
                            </span>
                        </td>
                        <td>
                            @if($order->shipment)
                                <a href="{{ route('shipments.show', $order->shipment) }}" class="text-decoration-none d-block">
                                    <span class="fw-semibold text-dark"><i class="bi bi-truck me-1"></i>{{ $order->shipment->company }}</span>
                                    <small class="font-monospace text-primary d-block">{{ $order->shipment->tracking_number }}</small>
                                </a>
                            @else
                                <a href="{{ route('shipments.create') }}?order_id={{ $order->id }}" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.78rem;">
                                    + Kargo Tanımla
                                </a>
                            @endif
                        </td>
                        <td class="small text-muted">
                            {{ $order->created_at->format('d.m.Y H:i') }}
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary" title="Görüntüle">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('orders.edit', $order) }}" class="btn btn-outline-primary" title="Düzenle">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if(!$order->isDelivered() && $order->status?->slug !== 'cancelled')
                                    <form action="{{ route('orders.cancel', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu siparişi iptal etmek istediğinizden emin misiniz?')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-warning" title="İptal Et">
                                            <i class="bi bi-x-octagon"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('orders.destroy', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu siparişi silmek istediğinizden emin misiniz?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Sil">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                            Kayıtlı sipariş bulunamadı.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
        <div class="card-footer bg-white d-flex justify-content-center py-3">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
