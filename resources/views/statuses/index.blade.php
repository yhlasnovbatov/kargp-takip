@extends('layouts.app')

@section('title', 'Sipariş Durumları | KargoTakip PRO')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1">Durumlar (Statuses)</h1>
        <p class="text-muted mb-0">Sipariş ve kargo akışındaki aşamaları tanımlayın (Beklemede, Hazırlanıyor, Kargoda, Teslim Edildi, İptal).</p>
    </div>
    <a href="{{ route('statuses.create') }}" class="btn btn-primary d-flex align-items-center gap-1" id="btn-create-status">
        <i class="bi bi-plus-circle-fill"></i> Yeni Durum Ekle
    </a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col" style="width: 70px;">#ID</th>
                    <th scope="col">Durum Adı & Rozet</th>
                    <th scope="col">Kısa Kod (Slug)</th>
                    <th scope="col">Açıklama</th>
                    <th scope="col" class="text-center">Kullanılan Sipariş</th>
                    <th scope="col" class="text-end" style="width: 180px;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @forelse($statuses as $status)
                    <tr id="status-row-{{ $status->id }}">
                        <td class="text-muted fw-semibold">{{ $status->id }}</td>
                        <td>
                            <span class="badge d-inline-flex align-items-center gap-1 fs-6" style="background-color: {{ $status->color ?? '#64748b' }}; color: #ffffff;">
                                <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> {{ $status->name }}
                            </span>
                        </td>
                        <td><code class="text-dark bg-light px-2 py-1 rounded">{{ $status->slug }}</code></td>
                        <td class="text-muted small" style="max-width: 320px;">
                            {{ $status->description ?? '-' }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary-subtle text-dark border">
                                {{ $status->orders_count }} Sipariş
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('statuses.show', $status) }}" class="btn btn-outline-secondary" title="Görüntüle">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('statuses.edit', $status) }}" class="btn btn-outline-primary" title="Düzenle">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('statuses.destroy', $status) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu durumu silmek istediğinizden emin misiniz?')">
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
                        <td colspan="6" class="text-center py-5 text-muted">
                            Henüz durum tanımlanmamış.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($statuses->hasPages())
        <div class="card-footer bg-white d-flex justify-content-center py-3">
            {{ $statuses->links() }}
        </div>
    @endif
</div>
@endsection
