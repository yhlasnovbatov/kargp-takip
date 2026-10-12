<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label fw-semibold">Durum Adı <span class="text-danger">*</span></label>
        <input 
            type="text" 
            name="name" 
            id="name" 
            class="form-control @error('name') is-invalid @enderror" 
            value="{{ old('name', $status->name ?? '') }}" 
            placeholder="Örn: Beklemede, Kargoda vb." 
            required
        >
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="slug" class="form-label fw-semibold">Kısa Kod (Slug)</label>
        <input 
            type="text" 
            name="slug" 
            id="slug" 
            class="form-control @error('slug') is-invalid @enderror" 
            value="{{ old('slug', $status->slug ?? '') }}" 
            placeholder="Örn: beklemede (boş bırakılırsa otomatik üretilir)"
        >
        @error('slug')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="color" class="form-label fw-semibold">Renk Kodu</label>
        <div class="input-group">
            <input 
                type="color" 
                class="form-control form-control-color" 
                id="color_picker" 
                value="{{ old('color', $status->color ?? '#6366f1') }}" 
                title="Renk Seçin"
                onchange="document.getElementById('color').value = this.value"
            >
            <input 
                type="text" 
                name="color" 
                id="color" 
                class="form-control @error('color') is-invalid @enderror" 
                value="{{ old('color', $status->color ?? '#6366f1') }}" 
                placeholder="#6366f1"
                onchange="document.getElementById('color_picker').value = this.value"
            >
        </div>
        @error('color')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="description" class="form-label fw-semibold">Açıklama</label>
        <textarea 
            name="description" 
            id="description" 
            rows="3" 
            class="form-control @error('description') is-invalid @enderror" 
            placeholder="Bu durumun açıklaması ve sürecin detayı..."
        >{{ old('description', $status->description ?? '') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mt-4 d-flex justify-content-end gap-2">
    <a href="{{ route('statuses.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-x-circle me-1"></i> İptal
    </a>
    <button type="submit" class="btn btn-primary" id="btn-submit-status">
        <i class="bi bi-check-lg me-1"></i> {{ isset($status->id) ? 'Güncelle' : 'Kaydet' }}
    </button>
</div>
