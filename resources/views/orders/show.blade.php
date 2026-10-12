@extends('layouts.app')

@section('title', $order->order_number . ' - Sipariş Detayı | KargoTakip PRO')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h1 class="h3 mb-0 font-monospace text-primary">{{ $order->order_number }}</h1>
            <span class="badge" style="background-color: {{ $order->status->color ?? '#64748b' }}; color: #ffffff;">
                {{ $order->status->name }}
            </span>
        </div>
        <p class="text-muted mb-0">Sipariş ayrıntıları, müşteri bilgileri ve kargo takibi.</p>
    </div>
    <div class="d-flex gap-2">
        @if(!$order->isDelivered() && $order->status?->slug !== 'cancelled')
            <form action="{{ route('orders.cancel', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu siparişi iptal etmek istediğinizden emin misiniz?')">
                @csrf
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-x-octagon me-1"></i> Siparişi İptal Et
                </button>
            </form>
        @endif
        <a href="{{ route('orders.edit', $order) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i> Düzenle
        </a>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Listeye Dön
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Sipariş ve Tutar Bilgileri -->
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 fs-6">Sipariş Bilgileri</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <span class="text-muted small d-block">Sipariş No:</span>
                        <span class="fw-bold font-monospace fs-5 text-dark">{{ $order->order_number }}</span>
                    </div>
                    <div class="col-6">
                        <span class="text-muted small d-block">Toplam Tutar:</span>
                        <span class="fw-bold fs-5 text-success">{{ $order->formatted_total }}</span>
                    </div>
                    <div class="col-6">
                        <span class="text-muted small d-block">Güncel Durum:</span>
                        <span class="badge fs-6 mt-1" style="background-color: {{ $order->status->color }}; color: #fff;">
                            {{ $order->status->name }}
                        </span>
                    </div>
                    <div class="col-6">
                        <span class="text-muted small d-block">Sipariş Tarihi:</span>
                        <span class="text-secondary">{{ $order->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                    <div class="col-12 border-top pt-3">
                        <span class="text-muted small d-block">Sipariş Notu:</span>
                        <p class="mb-0 text-secondary bg-light p-3 rounded">{{ $order->note ?? 'Not girilmemiş' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Müşteri Bilgileri -->
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fs-6">Müşteri (Alıcı) Bilgisi</h5>
                <a href="{{ route('customers.show', $order->customer) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-person me-1"></i> Müşteri Profili
                </a>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="text-muted small d-block">Ad Soyad:</span>
                    <span class="fw-bold fs-6">{{ $order->customer->name }}</span>
                </div>
                <div class="mb-3">
                    <span class="text-muted small d-block">E-Posta:</span>
                    <a href="mailto:{{ $order->customer->email }}" class="text-decoration-none">
                        {{ $order->customer->email }}
                    </a>
                </div>
                <div class="mb-3">
                    <span class="text-muted small d-block">Telefon:</span>
                    <span>{{ $order->customer->phone ?? 'Belirtilmedi' }}</span>
                </div>
                <div>
                    <span class="text-muted small d-block">Teslimat Adresi:</span>
                    <p class="mb-0 text-secondary">{{ $order->customer->address ?? 'Adres girilmemiş' }} ({{ $order->customer->city }})</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Kargo Durumu -->
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fs-6">Kargo ve Gönderi Bilgisi</h5>
                @if(!$order->shipment)
                    <a href="{{ route('shipments.create') }}?order_id={{ $order->id }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-truck me-1"></i> Bu Siparişe Kargo Tanımla
                    </a>
                @endif
            </div>
            <div class="card-body">
                @if($order->shipment)
                    <div class="row g-3 align-items-center">
                        <div class="col-md-3">
                            <span class="text-muted small d-block">Kargo Firması:</span>
                            <span class="fw-bold fs-6 text-dark"><i class="bi bi-truck me-1 text-primary"></i>{{ $order->shipment->company }}</span>
                        </div>
                        <div class="col-md-3">
                            <span class="text-muted small d-block">Takip Numarası:</span>
                            <span class="fw-bold font-monospace fs-6 text-primary">{{ $order->shipment->tracking_number }}</span>
                        </div>
                        <div class="col-md-3">
                            <span class="text-muted small d-block">Gönderim Tarihi:</span>
                            <span>{{ $order->shipment->shipped_at ? $order->shipment->shipped_at->format('d.m.Y H:i') : 'Henüz yola çıkmadı' }}</span>
                        </div>
                        <div class="col-md-3">
                            <span class="text-muted small d-block">Teslim Tarihi:</span>
                            <span class="{{ $order->shipment->delivered_at ? 'text-success fw-bold' : '' }}">
                                {{ $order->shipment->delivered_at ? $order->shipment->delivered_at->format('d.m.Y H:i') : 'Teslim bekleniyor' }}
                            </span>
                        </div>
                        <div class="col-12 border-top pt-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small d-block">Son Konum & Durum:</span>
                                <span class="fw-semibold text-dark">{{ $order->shipment->current_status_desc ?? 'İşlem görüyor' }}</span>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('shipments.show', $order->shipment) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Kargo Detayı
                                </a>
                                <a href="{{ route('tracking.index', ['q' => $order->shipment->tracking_number]) }}" target="_blank" class="btn btn-outline-success btn-sm">
                                    <i class="bi bi-search me-1"></i> Müşteri Takip Sayfası
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-box-seam fs-1 d-block mb-2 text-secondary"></i>
                        Bu sipariş için henüz kargo kaydı oluşturulmadı.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
