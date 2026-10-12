@extends('layouts.app')

@section('title', 'Müşteriler | KargoTakip PRO')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1">Müşteriler</h1>
        <p class="text-muted mb-0">Sistemde kayıtlı müşterileri ve sipariş geçmişlerini yönetin.</p>
    </div>
    <a href="{{ route('customers.create') }}" class="btn btn-primary d-flex align-items-center gap-1" id="btn-create-customer">
        <i class="bi bi-person-plus-fill"></i> Yeni Müşteri Ekle
    </a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col" style="width: 70px;">#ID</th>
                    <th scope="col">Ad Soyad</th>
                    <th scope="col">E-Posta</th>
                    <th scope="col">Telefon</th>
                    <th scope="col">Şehir</th>
                    <th scope="col" class="text-center">Sipariş Sayısı</th>
                    <th scope="col" class="text-end" style="width: 180px;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr id="customer-row-{{ $customer->id }}">
                        <td class="text-muted fw-semibold">{{ $customer->id }}</td>
                        <td>
                            <a href="{{ route('customers.show', $customer) }}" class="fw-semibold text-decoration-none text-dark">
                                {{ $customer->name }}
                            </a>
                        </td>
                        <td>
                            <a href="mailto:{{ $customer->email }}" class="text-muted text-decoration-none">
                                <i class="bi bi-envelope me-1"></i>{{ $customer->email }}
                            </a>
                        </td>
                        <td>{{ $customer->phone ?? '-' }}</td>
                        <td>
                            @if($customer->city)
                                <span class="badge bg-light text-dark border">{{ $customer->city }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                {{ $customer->orders_count }} Sipariş
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-secondary" title="Görüntüle">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline-primary" title="Düzenle">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu müşteriyi silmek istediğinizden emin misiniz? (İlişkili siparişler de silinebilir)')">
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
                            <i class="bi bi-people fs-1 d-block mb-2 text-secondary"></i>
                            Henüz kayıtlı müşteri bulunmuyor.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($customers->hasPages())
        <div class="card-footer bg-white d-flex justify-content-center py-3">
            {{ $customers->links() }}
        </div>
    @endif
</div>
@endsection
