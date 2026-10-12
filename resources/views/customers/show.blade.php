@extends('layouts.app')

@section('title', $customer->name . ' - Müşteri Detayı | KargoTakip PRO')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1">{{ $customer->name }}</h1>
        <p class="text-muted mb-0">Müşteri detay bilgileri ve ilişkili sipariş kayıtları.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i> Düzenle
        </a>
        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Listeye Dön
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 fs-6">İletişim & Adres Bilgileri</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="text-muted d-block small">E-Posta:</span>
                    <a href="mailto:{{ $customer->email }}" class="fw-semibold text-decoration-none">
                        {{ $customer->email }}
                    </a>
                </div>
                <div class="mb-3">
                    <span class="text-muted d-block small">Telefon:</span>
                    <span class="fw-semibold">{{ $customer->phone ?? 'Belirtilmedi' }}</span>
                </div>
                <div class="mb-3">
                    <span class="text-muted d-block small">Şehir / İlçe:</span>
                    <span class="fw-semibold">{{ $customer->city ?? 'Belirtilmedi' }}</span>
                </div>
                <div class="mb-3">
                    <span class="text-muted d-block small">Tam Adres:</span>
                    <p class="mb-0 text-secondary">{{ $customer->address ?? 'Adres kaydı yok' }}</p>
                </div>
                <div class="pt-2 border-top">
                    <span class="text-muted d-block small">Kayıt Tarihi:</span>
                    <small class="text-secondary">{{ $customer->created_at->format('d.m.Y H:i') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fs-6">Sipariş Geçmişi ({{ $customer->orders->count() }})</h5>
                <a href="{{ route('orders.create') }}?customer_id={{ $customer->id }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-plus-lg me-1"></i> Bu Müşteriye Sipariş Aç
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Sipariş No</th>
                            <th>Tutar</th>
                            <th>Durum</th>
                            <th>Kargo Bilgisi</th>
                            <th>Tarih</th>
                            <th class="text-end">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customer->orders as $ord)
                            <tr>
                                <td>
                                    <a href="{{ route('orders.show', $ord) }}" class="fw-semibold text-primary font-monospace text-decoration-none">
                                        {{ $ord->order_number }}
                                    </a>
                                </td>
                                <td><strong>{{ $ord->formatted_total }}</strong></td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-dark border">
                                        {{ $ord->status->name }}
                                    </span>
                                </td>
                                <td>
                                    @if($ord->shipment)
                                        <a href="{{ route('shipments.show', $ord->shipment) }}" class="text-decoration-none small">
                                            <i class="bi bi-truck me-1"></i>{{ $ord->shipment->company }} ({{ $ord->shipment->tracking_number }})
                                        </a>
                                    @else
                                        <span class="text-muted small">Kargo Yok</span>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $ord->created_at->format('d.m.Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('orders.show', $ord) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    Bu müşteriye ait henüz sipariş kaydı bulunmuyor.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
