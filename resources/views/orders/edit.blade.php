@extends('layouts.app')

@section('title', 'Sipariş Düzenle | KargoTakip PRO')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1">Siparişi Düzenle: {{ $order->order_number }}</h1>
                <p class="text-muted mb-0">Sipariş durumunu, tutarını ve notunu güncelleyin.</p>
            </div>
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Listeye Dön
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('orders.update', $order) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('orders._form')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
