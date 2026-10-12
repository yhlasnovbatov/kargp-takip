<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label fw-semibold">Ad Soyad <span class="text-danger">*</span></label>
        <input 
            type="text" 
            name="name" 
            id="name" 
            class="form-control @error('name') is-invalid @enderror" 
            value="{{ old('name', $customer->name ?? '') }}" 
            placeholder="Örn: Ahmet Yılmaz" 
            required
        >
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label fw-semibold">E-Posta Adresi <span class="text-danger">*</span></label>
        <input 
            type="email" 
            name="email" 
            id="email" 
            class="form-control @error('email') is-invalid @enderror" 
            value="{{ old('email', $customer->email ?? '') }}" 
            placeholder="Örn: ahmet@example.com" 
            required
        >
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="phone" class="form-label fw-semibold">Telefon Numarası</label>
        <input 
            type="text" 
            name="phone" 
            id="phone" 
            class="form-control @error('phone') is-invalid @enderror" 
            value="{{ old('phone', $customer->phone ?? '') }}" 
            placeholder="Örn: 0532 555 10 20"
        >
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="city" class="form-label fw-semibold">Şehir / İl</label>
        <input 
            type="text" 
            name="city" 
            id="city" 
            class="form-control @error('city') is-invalid @enderror" 
            value="{{ old('city', $customer->city ?? '') }}" 
            placeholder="Örn: İstanbul"
        >
        @error('city')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="address" class="form-label fw-semibold">Adres Detayı</label>
        <textarea 
            name="address" 
            id="address" 
            rows="3" 
            class="form-control @error('address') is-invalid @enderror" 
            placeholder="Mahalle, cadde, sokak, bina ve daire no..."
        >{{ old('address', $customer->address ?? '') }}</textarea>
        @error('address')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mt-4 d-flex justify-content-end gap-2">
    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-x-circle me-1"></i> İptal
    </a>
    <button type="submit" class="btn btn-primary" id="btn-submit-customer">
        <i class="bi bi-check-lg me-1"></i> {{ isset($customer->id) ? 'Güncelle' : 'Kaydet' }}
    </button>
</div>
