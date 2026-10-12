@extends('layouts.app')

@section('title', 'Kargo Takip Sorgulama | KargoTakip PRO')
@section('meta_description', 'Kargo takip numaranızı veya sipariş numaranızı girerek kargonuzun nerede olduğunu anında öğrenin.')

@section('content')
<section class="hero-section">
    <div class="container">
        <span class="hero-pill">⚡ Gerçek Zamanlı Kargo Takibi</span>
        <h1 class="hero-title">Kargonuzun Anlık Durumunu Takip Edin</h1>
        <p class="hero-subtitle">
            Kargo takip numaranızı veya sipariş numaranızı girerek kargonuzun güncel durumunu, tahmini teslimat tarihini ve hareketlerini anında görüntüleyin.
        </p>

        <!-- Search Form -->
        <div class="search-card">
            <form action="{{ route('tracking.index') }}" method="GET" class="search-form" id="tracking-form">
                <div class="search-input-wrapper">
                    <span class="search-icon">🔍</span>
                    <input 
                        type="text" 
                        name="q" 
                        id="tracking-query" 
                        class="search-input" 
                        placeholder="Kargo Takip No (örn: YK-984210384) veya Sipariş No..." 
                        value="{{ $query }}"
                        autocomplete="off"
                        required
                    >
                </div>
                <button type="submit" class="btn-search" id="btn-submit-search">
                    <span>Sorgula</span>
                    <span>➜</span>
                </button>
            </form>
        </div>

        <!-- Sample Chips for Quick Test -->
        <div class="sample-chips">
            <span>Hızlı Test İçin Tıklayın:</span>
            <button type="button" class="sample-chip" onclick="fillAndSearch('YK-984210384')">
                🚚 YK-984210384 <small style="color: #818cf8;">(Kargoda)</small>
            </button>
            <button type="button" class="sample-chip" onclick="fillAndSearch('AR-419283712')">
                ✅ AR-419283712 <small style="color: #34d399;">(Teslim Edildi)</small>
            </button>
            <button type="button" class="sample-chip" onclick="fillAndSearch('MNG-77123984')">
                📦 MNG-77123984 <small style="color: #38bdf8;">(Hazırlanıyor)</small>
            </button>
            <button type="button" class="sample-chip" onclick="fillAndSearch('ORD-2026101-004')">
                ⏳ ORD-2026101-004 <small style="color: #fbbf24;">(Beklemede)</small>
            </button>
        </div>
    </div>
</section>

<div class="container">
    @if($query && !$result)
        <div class="result-card" style="text-align: center; border-color: rgba(239, 68, 68, 0.3);">
            <div style="font-size: 3rem; margin-bottom: 12px;">🔍❌</div>
            <h3 style="color: #ef4444; margin-bottom: 8px;">Kargo Bulunamadı</h3>
            <p style="color: var(--text-secondary); max-width: 500px; margin: 0 auto 20px;">
                "<strong>{{ $query }}</strong>" numaralı aramanız ile eşleşen kargo veya sipariş kaydı bulunamadı. Lütfen numarayı kontrol edip tekrar deneyin.
            </p>
            <a href="{{ route('tracking.index') }}" class="btn btn-secondary">Aramayı Temizle</a>
        </div>
    @elseif($result)
        @php
            $shipment = $result['shipment'] ?? null;
            $order = $result['order'];
            $customer = $result['customer'];
            $status = $result['status'];
            $statusName = $status->name;

            // Stepper hesaplama
            // Adımlar: 1 = Sipariş Alındı, 2 = Hazırlanıyor, 3 = Kargoda, 4 = Teslim Edildi
            $stepProgress = 25;
            $currentStepIndex = 1;

            if ($statusName === \App\Models\Status::HAZIRLANIYOR) {
                $stepProgress = 50;
                $currentStepIndex = 2;
            } elseif ($statusName === \App\Models\Status::KARGODA) {
                $stepProgress = 75;
                $currentStepIndex = 3;
            } elseif ($statusName === \App\Models\Status::TESLIM_EDILDI) {
                $stepProgress = 100;
                $currentStepIndex = 4;
            } elseif ($statusName === \App\Models\Status::IPTAL) {
                $stepProgress = 0;
                $currentStepIndex = 0;
            }
        @endphp

        <!-- Result Card -->
        <article class="result-card" id="tracking-result-box">
            <div class="result-header">
                <div class="tracking-primary-info">
                    <div class="company-avatar">
                        @if($shipment)
                            🚚
                        @else
                            📦
                        @endif
                    </div>
                    <div>
                        <div class="tracking-title">
                            <span>{{ $shipment ? $shipment->tracking_number : $order->order_number }}</span>
                            <button type="button" class="copy-btn" onclick="copyToClipboard('{{ $shipment ? $shipment->tracking_number : $order->order_number }}', this)">
                                📋 Kopyala
                            </button>
                        </div>
                        <div style="font-size: 0.9rem; color: var(--text-secondary); margin-top: 4px;">
                            {{ $shipment ? $shipment->company : 'Kargo Hazırlık Aşamasında' }} • Sipariş: <strong style="color: #fff;">{{ $order->order_number }}</strong>
                        </div>
                    </div>
                </div>

                <div>
                    @if($statusName === \App\Models\Status::BEKLEMEDE)
                        <span class="status-badge status-badge-pending">
                            <span class="dot"></span> Beklemede
                        </span>
                    @elseif($statusName === \App\Models\Status::HAZIRLANIYOR)
                        <span class="status-badge status-badge-preparing">
                            <span class="dot"></span> Hazırlanıyor
                        </span>
                    @elseif($statusName === \App\Models\Status::KARGODA)
                        <span class="status-badge status-badge-shipping">
                            <span class="dot"></span> Kargoda
                        </span>
                    @elseif($statusName === \App\Models\Status::TESLIM_EDILDI)
                        <span class="status-badge status-badge-delivered">
                            <span class="dot"></span> Teslim Edildi
                        </span>
                    @elseif($statusName === \App\Models\Status::IPTAL)
                        <span class="status-badge status-badge-cancelled">
                            <span class="dot"></span> İptal Edildi
                        </span>
                    @endif
                </div>
            </div>

            @if($statusName === \App\Models\Status::IPTAL)
                <div style="background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: var(--radius-md); padding: 18px; margin: 20px 0; color: #fca5a5;">
                    <div style="font-weight: 700; font-size: 1.05rem; margin-bottom: 4px;">⚠️ Bu sipariş iptal edilmiştir</div>
                    <div>{{ $order->note ?? 'Sipariş müşteri veya satıcı talebiyle iptal edilmiştir.' }}</div>
                </div>
            @else
                <!-- Timeline Stepper -->
                <div class="stepper-container">
                    <div class="stepper-track">
                        <div class="stepper-progress" style="width: {{ $stepProgress }}%;"></div>
                    </div>
                    <div class="stepper-steps">
                        <!-- Adım 1 -->
                        <div class="stepper-step {{ $currentStepIndex >= 1 ? ($currentStepIndex == 1 ? 'active' : 'completed') : '' }}">
                            <div class="step-node">{{ $currentStepIndex > 1 ? '✓' : '1' }}</div>
                            <div class="step-title">Sipariş Alındı</div>
                            <div class="step-desc">{{ $order->created_at->format('d.m.Y H:i') }}</div>
                        </div>

                        <!-- Adım 2 -->
                        <div class="stepper-step {{ $currentStepIndex >= 2 ? ($currentStepIndex == 2 ? 'active' : 'completed') : '' }}">
                            <div class="step-node">{{ $currentStepIndex > 2 ? '✓' : '2' }}</div>
                            <div class="step-title">Hazırlanıyor</div>
                            <div class="step-desc">Depoda Paketlendi</div>
                        </div>

                        <!-- Adım 3 -->
                        <div class="stepper-step {{ $currentStepIndex >= 3 ? ($currentStepIndex == 3 ? 'active' : 'completed') : '' }}">
                            <div class="step-node">{{ $currentStepIndex > 3 ? '✓' : '3' }}</div>
                            <div class="step-title">Kargoya Verildi</div>
                            <div class="step-desc">
                                {{ $shipment && $shipment->shipped_at ? $shipment->shipped_at->format('d.m.Y H:i') : 'Transferde' }}
                            </div>
                        </div>

                        <!-- Adım 4 -->
                        <div class="stepper-step {{ $currentStepIndex >= 4 ? 'completed' : '' }}">
                            <div class="step-node">{{ $currentStepIndex >= 4 ? '✓' : '4' }}</div>
                            <div class="step-title">Teslim Edildi</div>
                            <div class="step-desc">
                                {{ $shipment && $shipment->delivered_at ? $shipment->delivered_at->format('d.m.Y H:i') : 'Bekleniyor' }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Live Status Highlight Box -->
            @if($shipment && $shipment->current_status_desc)
                <div style="background: rgba(99, 102, 241, 0.08); border-left: 4px solid var(--primary); padding: 16px 20px; border-radius: var(--radius-sm); margin: 28px 0;">
                    <div style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: #a5b4fc; margin-bottom: 4px;">
                        📍 Son Kargo Hareketi & Konum
                    </div>
                    <div style="font-size: 1.05rem; font-weight: 600; color: #fff;">
                        {{ $shipment->current_status_desc }}
                    </div>
                    <div style="font-size: 0.82rem; color: var(--text-muted); margin-top: 4px;">
                        Son Güncelleme: {{ $shipment->updated_at->format('d.m.Y H:i') }}
                    </div>
                </div>
            @endif

            <!-- Detailed Grid -->
            <div class="details-grid">
                <div class="detail-item">
                    <span class="detail-label">Alıcı Müşteri</span>
                    <span class="detail-value">{{ $customer->name }}</span>
                    <span class="detail-sub">{{ $customer->city ?? 'Türkiye' }}</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Teslimat Adresi</span>
                    <span class="detail-value" style="font-size: 0.95rem;">{{ $customer->address }}</span>
                    <span class="detail-sub">Tel: {{ $customer->phone }}</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Kargo Firması</span>
                    <span class="detail-value">{{ $shipment ? $shipment->company : 'Belirlenmedi' }}</span>
                    <span class="detail-sub">Takip No: {{ $shipment ? $shipment->tracking_number : '-' }}</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Sipariş Tutarı</span>
                    <span class="detail-value" style="color: #38bdf8;">{{ $order->formatted_total }}</span>
                    <span class="detail-sub">Durum: {{ $status->name }}</span>
                </div>

                @if($shipment && $shipment->shipped_at)
                    <div class="detail-item">
                        <span class="detail-label">Kargoya Veriliş</span>
                        <span class="detail-value">{{ $shipment->shipped_at->format('d.m.Y H:i') }}</span>
                    </div>
                @endif

                @if($shipment && $shipment->delivered_at)
                    <div class="detail-item">
                        <span class="detail-label">Teslim Tarihi</span>
                        <span class="detail-value" style="color: #10b981;">{{ $shipment->delivered_at->format('d.m.Y H:i') }}</span>
                    </div>
                @endif
            </div>

            @if($order->note)
                <div style="margin-top: 20px; font-size: 0.9rem; color: var(--text-muted); background: rgba(255, 255, 255, 0.02); padding: 12px 16px; border-radius: var(--radius-sm);">
                    <strong style="color: var(--text-secondary);">📝 Sipariş Notu:</strong> {{ $order->note }}
                </div>
            @endif
        </article>
    @else
        <!-- Information Features Cards -->
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon-wrapper">📍</div>
                <h3>Gerçek Zamanlı Takip</h3>
                <p>Paketinizin depodan çıkışından kapınıza ulaştığı ana kadar her aşamayı anlık olarak harita ve durum akışında izleyin.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon-wrapper">🏢</div>
                <h3>Tüm Kargo Firmaları</h3>
                <p>Yurtiçi Kargo, Aras, MNG, Trendyol Express ve diğer tüm kargo entegrasyonlarını tek bir panel üzerinden kolayca yönetin.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon-wrapper">⚡</div>
                <h3>Hızlı & Kolay Sorgulama</h3>
                <p>Üyelik gerektirmeden sadece takip numaranızı veya sipariş kodunuzu yazarak tüm teslimat detaylarına ulaşın.</p>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function fillAndSearch(code) {
        const input = document.getElementById('tracking-query');
        input.value = code;
        document.getElementById('tracking-form').submit();
    }
</script>
@endpush
