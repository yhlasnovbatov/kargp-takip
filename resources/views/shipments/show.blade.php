@extends('layouts.app')

@section('title', $shipment->tracking_number . ' - Kargo Detayı | KargoTakip PRO')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h1 class="h3 mb-0 font-monospace text-primary">{{ $shipment->tracking_number }}</h1>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                {{ $shipment->company }}
            </span>
        </div>
        <p class="text-muted mb-0">Kargo hareketleri ve teslimat ayrıntıları.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('tracking.index', ['q' => $shipment->tracking_number]) }}" target="_blank" class="btn btn-outline-success">
            <i class="bi bi-box-arrow-up-right me-1"></i> Müşteri Takip Ekranı
        </a>
        <a href="{{ route('shipments.edit', $shipment) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i> Düzenle
        </a>
        <a href="{{ route('shipments.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Listeye Dön
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 fs-6">Kargo Takip Bilgileri</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <span class="text-muted small d-block">Takip Numarası:</span>
                        <span class="fw-bold font-monospace fs-5 text-dark">{{ $shipment->tracking_number }}</span>
                    </div>
                    <div class="col-6">
                        <span class="text-muted small d-block">Taşıyıcı Firma:</span>
                        <span class="fw-bold fs-6 text-dark"><i class="bi bi-truck me-1 text-primary"></i>{{ $shipment->company }}</span>
                    </div>
                    <div class="col-6">
                        <span class="text-muted small d-block">Gönderim Tarihi:</span>
                        <span>{{ $shipment->shipped_at ? $shipment->shipped_at->format('d.m.Y H:i') : 'Henüz Sevk Edilmedi' }}</span>
                    </div>
                    <div class="col-6">
                        <span class="text-muted small d-block">Teslimat Tarihi:</span>
                        <span class="{{ $shipment->delivered_at ? 'text-success fw-bold' : 'text-muted' }}">
                            {{ $shipment->delivered_at ? $shipment->delivered_at->format('d.m.Y H:i') : 'Teslim Bekleniyor' }}
                        </span>
                    </div>
                    <div class="col-12 border-top pt-3">
                        <span class="text-muted small d-block">Son Konum & Durum:</span>
                        <div class="alert alert-light border mt-1 mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-geo-alt-fill text-danger fs-5"></i>
                            <div>
                                <span class="fw-semibold text-dark">{{ $shipment->current_status_desc ?? 'İşlem görüyor' }}</span>
                                <small class="text-muted d-block">Son Güncelleme: {{ $shipment->updated_at->format('d.m.Y H:i') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fs-6">Bağlı Sipariş & Müşteri</h5>
                <a href="{{ route('orders.show', $shipment->order) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye me-1"></i> Sipariş Detayı
                </a>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="text-muted small d-block">Sipariş No:</span>
                    <span class="fw-bold font-monospace text-primary">{{ $shipment->order->order_number }}</span>
                    <span class="badge ms-2" style="background-color: {{ $shipment->order->status->color }}; color: #fff;">
                        {{ $shipment->order->status->name }}
                    </span>
                </div>
                <div class="mb-3">
                    <span class="text-muted small d-block">Sipariş Tutarı:</span>
                    <span class="fw-bold text-success">{{ $shipment->order->formatted_total }}</span>
                </div>
                <div class="mb-3">
                    <span class="text-muted small d-block">Alıcı Müşteri:</span>
                    <a href="{{ route('customers.show', $shipment->order->customer) }}" class="fw-semibold text-decoration-none">
                        {{ $shipment->order->customer->name }}
                    </a>
                </div>
                <div class="mb-3">
                    <span class="text-muted small d-block">İletişim:</span>
                    <span>{{ $shipment->order->customer->phone ?? '-' }} | {{ $shipment->order->customer->email }}</span>
                </div>
                <div>
                    <span class="text-muted small d-block">Teslimat Adresi:</span>
                    <p class="mb-0 text-secondary">{{ $shipment->order->customer->address ?? 'Adres girilmemiş' }} ({{ $shipment->order->customer->city }})</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
