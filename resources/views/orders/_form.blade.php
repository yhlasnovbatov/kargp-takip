<div class="row g-3">
    <!-- Müşteri Select -->
    <div class="col-md-6">
        <label for="customer_id" class="form-label fw-semibold">Müşteri Seçin <span class="text-danger">*</span></label>
        <select 
            name="customer_id" 
            id="customer_id" 
            class="form-select @error('customer_id') is-invalid @enderror" 
            required
        >
            <option value="">-- Müşteri Seçiniz --</option>
            @foreach($customers as $c)
                <option 
                    value="{{ $c->id }}" 
                    {{ old('customer_id', $order->customer_id ?? request('customer_id')) == $c->id ? 'selected' : '' }}
                >
                    {{ $c->name }} ({{ $c->email }})
                </option>
            @endforeach
        </select>
        @error('customer_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Durum Select -->
    <div class="col-md-6">
        <label for="status_id" class="form-label fw-semibold">Sipariş Durumu <span class="text-danger">*</span></label>
        <select 
            name="status_id" 
            id="status_id" 
            class="form-select @error('status_id') is-invalid @enderror" 
            required
        >
            <option value="">-- Durum Seçiniz --</option>
            @foreach($statuses as $st)
                <option 
                    value="{{ $st->id }}" 
                    {{ old('status_id', $order->status_id ?? '') == $st->id ? 'selected' : '' }}
                    {{ ($order->isDelivered() && $st->slug === 'cancelled') ? 'disabled' : '' }}
                >
                    {{ $st->name }}{{ ($order->isDelivered() && $st->slug === 'cancelled') ? ' (Teslim Edilmiş - İptal Edilemez)' : '' }}
                </option>
            @endforeach
        </select>
        @error('status_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Sipariş No -->
    <div class="col-md-6">
        <label for="order_number" class="form-label fw-semibold">Sipariş Numarası</label>
        <input 
            type="text" 
            name="order_number" 
            id="order_number" 
            class="form-control font-monospace @error('order_number') is-invalid @enderror" 
            value="{{ old('order_number', $order->order_number ?? '') }}" 
            placeholder="Boş bırakılırsa otomatik üretilir (Örn: ORD-20261012-001)"
        >
        <div class="form-text text-muted small">Benzersiz sipariş kodu. Boş bırakırsanız sistem otomatik üretir.</div>
        @error('order_number')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Sipariş Tutarı -->
    <div class="col-md-6">
        <label for="total_amount" class="form-label fw-semibold">Sipariş Tutarı (₺) <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">₺</span>
            <input 
                type="number" 
                step="0.01" 
                min="0"
                name="total_amount" 
                id="total_amount" 
                class="form-control @error('total_amount') is-invalid @enderror" 
                value="{{ old('total_amount', $order->total_amount ?? '0.00') }}" 
                placeholder="0.00" 
                required
            >
        </div>
        @error('total_amount')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <!-- Not -->
    <div class="col-12">
        <label for="note" class="form-label fw-semibold">Sipariş Notu</label>
        <textarea 
            name="note" 
            id="note" 
            rows="3" 
            class="form-control @error('note') is-invalid @enderror" 
            placeholder="Müşteri notu, özel paketleme veya teslimat uyarısı..."
        >{{ old('note', $order->note ?? '') }}</textarea>
        @error('note')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mt-4 d-flex justify-content-end gap-2">
    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-x-circle me-1"></i> İptal
    </a>
    <button type="submit" class="btn btn-primary" id="btn-submit-order">
        <i class="bi bi-check-lg me-1"></i> {{ isset($order->id) ? 'Siparişi Güncelle' : 'Siparişi Kaydet' }}
    </button>
</div>
