@extends('layouts.app')

@section('title', 'Yeni Sipariş Oluştur | KargoTakip PRO')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1">Yeni Sipariş Oluştur</h1>
                <p class="text-muted mb-0">Müşteri ve durum seçerek yeni bir sipariş kaydı oluşturun.</p>
            </div>
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Listeye Dön
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('orders.store') }}" method="POST">
                    @csrf
                    @include('orders._form')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
