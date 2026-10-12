@extends('layouts.app')

@section('title', $status->name . ' - Durum Detayı | KargoTakip PRO')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h1 class="h3 mb-0">{{ $status->name }}</h1>
            <span class="badge" style="background-color: {{ $status->color ?? '#64748b' }}; color: #ffffff;">
                {{ $status->name }}
            </span>
        </div>
        <p class="text-muted mb-0">Durum detay bilgisi ve bu duruma ait siparişler.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('statuses.edit', $status) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i> Düzenle
        </a>
        <a href="{{ route('statuses.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Listeye Dön
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 fs-6">Durum Tanım Bilgileri</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="text-muted d-block small">Durum Adı:</span>
                    <span class="fw-semibold">{{ $status->name }}</span>
                </div>
                <div class="mb-3">
                    <span class="text-muted d-block small">Kısa Kod (Slug):</span>
                    <code>{{ $status->slug }}</code>
                </div>
                <div class="mb-3">
                    <span class="text-muted d-block small">Tema Rengi:</span>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span style="display: inline-block; width: 24px; height: 24px; border-radius: 4px; background-color: {{ $status->color }}; border: 1px solid #cbd5e1;"></span>
                        <code>{{ $status->color }}</code>
                    </div>
                </div>
                <div class="mb-3">
                    <span class="text-muted d-block small">Açıklama:</span>
                    <p class="mb-0 text-secondary">{{ $status->description ?? 'Açıklama girilmedi' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 fs-6">Bu Durumdaki Siparişler ({{ $status->orders->count() }})</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Sipariş No</th>
                            <th>Müşteri</th>
                            <th>Tutar</th>
                            <th>Tarih</th>
                            <th class="text-end">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($status->orders as $ord)
                            <tr>
                                <td>
                                    <a href="{{ route('orders.show', $ord) }}" class="fw-semibold font-monospace text-decoration-none">
                                        {{ $ord->order_number }}
                                    </a>
                                </td>
                                <td>{{ $ord->customer->name }}</td>
                                <td><strong>{{ $ord->formatted_total }}</strong></td>
                                <td class="small text-muted">{{ $ord->created_at->format('d.m.Y H:i') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('orders.show', $ord) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    Bu duruma atanmış sipariş bulunmuyor.
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
