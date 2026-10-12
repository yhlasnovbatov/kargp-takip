@extends('layouts.app')

@section('title', 'Yeni Kargo Tanımla | KargoTakip PRO')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1">Yeni Kargo Tanımla</h1>
                <p class="text-muted mb-0">Mevcut bir siparişe kargo firması ve takip kodu atayın.</p>
            </div>
            <a href="{{ route('shipments.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Listeye Dön
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('shipments.store') }}" method="POST">
                    @csrf
                    @include('shipments._form')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
