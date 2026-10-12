@extends('layouts.app')

@section('title', 'Yeni Durum Ekle | KargoTakip PRO')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1">Yeni Durum Tanımla</h1>
                <p class="text-muted mb-0">Sipariş süreci için yeni bir durum aşaması oluşturun.</p>
            </div>
            <a href="{{ route('statuses.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Listeye Dön
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('statuses.store') }}" method="POST">
                    @csrf
                    @include('statuses._form')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
