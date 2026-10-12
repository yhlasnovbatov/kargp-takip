<div class="row g-3">
    <!-- Sipariş Select -->
    <div class="col-md-6">
        <label for="order_id" class="form-label fw-semibold">Sipariş Seçin <span class="text-danger">*</span></label>
        <select 
            name="order_id" 
            id="order_id" 
            class="form-select @error('order_id') is-invalid @enderror" 
            required
        >
            <option value="">-- Kargo Bağlanacak Siparişi Seçin --</option>
            @foreach($orders as $ord)
                <option 
                    value="{{ $ord->id }}" 
                    {{ old('order_id', $shipment->order_id ?? request('order_id')) == $ord->id ? 'selected' : '' }}
                >
                    {{ $ord->order_number }} — {{ $ord->customer->name ?? 'Müşteri' }} ({{ $ord->formatted_total }})
                </option>
            @endforeach
        </select>
        @error('order_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Kargo Firması -->
    <div class="col-md-6">
        <label for="company" class="form-label fw-semibold">Kargo Firması <span class="text-danger">*</span></label>
        @php
            $currentCompany = old('company', $shipment->company ?? 'Yurtiçi Kargo');
        @endphp
        <select 
            name="company" 
            id="company" 
            class="form-select @error('company') is-invalid @enderror" 
            required
        >
            <option value="Yurtiçi Kargo" {{ $currentCompany === 'Yurtiçi Kargo' ? 'selected' : '' }}>Yurtiçi Kargo</option>
            <option value="Aras Kargo" {{ $currentCompany === 'Aras Kargo' ? 'selected' : '' }}>Aras Kargo</option>
            <option value="MNG Kargo" {{ $currentCompany === 'MNG Kargo' ? 'selected' : '' }}>MNG Kargo</option>
            <option value="Trendyol Express" {{ $currentCompany === 'Trendyol Express' ? 'selected' : '' }}>Trendyol Express</option>
            <option value="Sürat Kargo" {{ $currentCompany === 'Sürat Kargo' ? 'selected' : '' }}>Sürat Kargo</option>
            <option value="PTT Kargo" {{ $currentCompany === 'PTT Kargo' ? 'selected' : '' }}>PTT Kargo</option>
        </select>
        @error('company')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Takip No -->
    <div class="col-md-6">
        <label for="tracking_number" class="form-label fw-semibold">Takip Numarası <span class="text-danger">*</span></label>
        <div class="input-group">
            <input 
                type="text" 
                name="tracking_number" 
                id="tracking_number" 
                class="form-control font-monospace @error('tracking_number') is-invalid @enderror" 
                value="{{ old('tracking_number', $shipment->tracking_number ?? '') }}" 
                placeholder="Örn: YK-984210384" 
                required
            >
            <button class="btn btn-outline-secondary" type="button" onclick="generateTrackingCode()">
                <i class="bi bi-arrow-repeat"></i> Kod Üret
            </button>
        </div>
        <div class="form-text text-muted small">Benzersiz kargo barkod / takip numarası.</div>
        @error('tracking_number')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <!-- Son Durum / Konum Açıklaması -->
    <div class="col-md-6">
        <label for="current_status_desc" class="form-label fw-semibold">Güncel Konum & Durum Notu</label>
        <input 
            type="text" 
            name="current_status_desc" 
            id="current_status_desc" 
            class="form-control @error('current_status_desc') is-invalid @enderror" 
            value="{{ old('current_status_desc', $shipment->current_status_desc ?? 'Şubede işlem görüyor') }}" 
            placeholder="Örn: Kadıköy Dağıtım Şubesinde, Dağıtıma çıktı"
        >
        @error('current_status_desc')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Gönderim Tarihi -->
    <div class="col-md-6">
        <label for="shipped_at" class="form-label fw-semibold">Gönderim Tarihi</label>
        <input 
            type="datetime-local" 
            name="shipped_at" 
            id="shipped_at" 
            class="form-control @error('shipped_at') is-invalid @enderror" 
            value="{{ old('shipped_at', isset($shipment->shipped_at) ? $shipment->shipped_at->format('Y-m-d\TH:i') : '') }}"
        >
        @error('shipped_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Teslim Tarihi -->
    <div class="col-md-6">
        <label for="delivered_at" class="form-label fw-semibold">Teslim Tarihi</label>
        <input 
            type="datetime-local" 
            name="delivered_at" 
            id="delivered_at" 
            class="form-control @error('delivered_at') is-invalid @enderror" 
            value="{{ old('delivered_at', isset($shipment->delivered_at) ? $shipment->delivered_at->format('Y-m-d\TH:i') : '') }}"
        >
        @error('delivered_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mt-4 d-flex justify-content-end gap-2">
    <a href="{{ route('shipments.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-x-circle me-1"></i> İptal
    </a>
    <button type="submit" class="btn btn-primary" id="btn-submit-shipment">
        <i class="bi bi-check-lg me-1"></i> {{ isset($shipment->id) ? 'Kargoyu Güncelle' : 'Kargoyu Kaydet' }}
    </button>
</div>

<script>
    function generateTrackingCode() {
        const company = document.getElementById('company').value;
        let prefix = 'YK';
        if (company.includes('Aras')) prefix = 'AR';
        else if (company.includes('MNG')) prefix = 'MNG';
        else if (company.includes('Trendyol')) prefix = 'TY';
        else if (company.includes('Sürat')) prefix = 'SR';
        else if (company.includes('PTT')) prefix = 'PTT';

        const rand = Math.floor(10000000 + Math.random() * 90000000);
        document.getElementById('tracking_number').value = prefix + '-' + rand;
    }
</script>
