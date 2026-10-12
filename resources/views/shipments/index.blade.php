@extends('layouts.app')

@section('title', 'Kargo Gönderileri | KargoTakip PRO')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1">Kargo Gönderileri</h1>
        <p class="text-muted mb-0">Taşıyıcı firmalara teslim edilen paketleri ve takip kodlarını yönetin.</p>
    </div>
    <a href="{{ route('shipments.create') }}" class="btn btn-primary d-flex align-items-center gap-1" id="btn-create-shipment">
        <i class="bi bi-truck-flatbed"></i> Yeni Kargo Kaydı
    </a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col">Takip No</th>
                    <th scope="col">Kargo Firması</th>
                    <th scope="col">Sipariş No</th>
                    <th scope="col">Alıcı Müşteri</th>
                    <th scope="col">Güncel Durum / Konum</th>
                    <th scope="col">Gönderim</th>
                    <th scope="col">Teslimat</th>
                    <th scope="col" class="text-end" style="width: 180px;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @forelse($shipments as $shipment)
                    <tr id="shipment-row-{{ $shipment->id }}">
                        <td>
                            <a href="{{ route('shipments.show', $shipment) }}" class="fw-bold font-monospace text-primary text-decoration-none">
                                {{ $shipment->tracking_number }}
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-truck me-1"></i>{{ $shipment->company }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('orders.show', $shipment->order) }}" class="font-monospace text-secondary text-decoration-none small">
                                {{ $shipment->order->order_number }}
                            </a>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $shipment->order->customer->name ?? '-' }}</div>
                            <small class="text-muted">{{ $shipment->order->customer->city ?? '' }}</small>
                        </td>
                        <td class="small" style="max-width: 200px;">
                            <span class="text-secondary">{{ $shipment->current_status_desc ?? 'Şubede işlem görüyor' }}</span>
                        </td>
                        <td class="small text-muted">
                            {{ $shipment->shipped_at ? $shipment->shipped_at->format('d.m.Y H:i') : '-' }}
                        </td>
                        <td class="small">
                            @if($shipment->delivered_at)
                                <span class="text-success fw-bold">
                                    <i class="bi bi-check2-circle me-1"></i>{{ $shipment->delivered_at->format('d.m.Y H:i') }}
                                </span>
                            @else
                                <span class="text-muted">Dağıtımda</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('shipments.show', $shipment) }}" class="btn btn-outline-secondary" title="Görüntüle">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('shipments.edit', $shipment) }}" class="btn btn-outline-primary" title="Düzenle">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('shipments.destroy', $shipment) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu kargo kaydını silmek istediğinizden emin misiniz?')">
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
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-truck fs-1 d-block mb-2 text-secondary"></i>
                            Henüz kargo kaydı bulunmuyor.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($shipments->hasPages())
        <div class="card-footer bg-white d-flex justify-content-center py-3">
            {{ $shipments->links() }}
        </div>
    @endif
</div>
@endsection
