@extends('layouts.app')

@section('title', 'Yönetim Paneli | KargoTakip PRO')
@section('meta_description', 'Kargo takip, sipariş ve müşteri yönetim paneli')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="dashboard-title">
            <h1>⚙️ Kargo & Sipariş Kontrol Paneli</h1>
            <p>Müşteri siparişlerini, kargo hareketlerini ve teslimat durumlarını yönetin.</p>
        </div>
        <div class="dashboard-actions">
            <button type="button" class="btn btn-primary" onclick="openModal('modal-order')">
                <span>➕</span> Yeni Sipariş
            </button>
            <button type="button" class="btn btn-secondary" onclick="openModal('modal-shipment')">
                <span>🚚</span> Kargo Ekle
            </button>
            <button type="button" class="btn btn-secondary" onclick="openModal('modal-customer')">
                <span>👥</span> Müşteri Ekle
            </button>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="metrics-grid">
        <div class="metric-card" style="--metric-color: #6366f1;">
            <div class="metric-label">Toplam Sipariş</div>
            <div class="metric-value">{{ $totalOrdersCount }}</div>
            <div class="metric-sub">{{ $totalShipmentsCount }} Kargo Kaydı</div>
        </div>

        <div class="metric-card" style="--metric-color: #f59e0b;">
            <div class="metric-label">Beklemede</div>
            <div class="metric-value" style="color: #fbbf24;">{{ $statusCounts[\App\Models\Status::BEKLEMEDE] ?? 0 }}</div>
            <div class="metric-sub">Onay Bekliyor</div>
        </div>

        <div class="metric-card" style="--metric-color: #0ea5e9;">
            <div class="metric-label">Hazırlanıyor</div>
            <div class="metric-value" style="color: #38bdf8;">{{ $statusCounts[\App\Models\Status::HAZIRLANIYOR] ?? 0 }}</div>
            <div class="metric-sub">Depoda Paketleniyor</div>
        </div>

        <div class="metric-card" style="--metric-color: #818cf8;">
            <div class="metric-label">Kargoda</div>
            <div class="metric-value" style="color: #a5b4fc;">{{ $statusCounts[\App\Models\Status::KARGODA] ?? 0 }}</div>
            <div class="metric-sub">Transfer Aşamasında</div>
        </div>

        <div class="metric-card" style="--metric-color: #10b981;">
            <div class="metric-label">Teslim Edildi</div>
            <div class="metric-value" style="color: #34d399;">{{ $statusCounts[\App\Models\Status::TESLIM_EDILDI] ?? 0 }}</div>
            <div class="metric-sub">Başarıyla Ulaştı</div>
        </div>

        <div class="metric-card" style="--metric-color: #ef4444;">
            <div class="metric-label">İptal Edildi</div>
            <div class="metric-value" style="color: #f87171;">{{ $statusCounts[\App\Models\Status::IPTAL] ?? 0 }}</div>
            <div class="metric-sub">İptal / İade</div>
        </div>

        <div class="metric-card" style="--metric-color: #06b6d4;">
            <div class="metric-label">Toplam Ciro</div>
            <div class="metric-value" style="font-size: 1.45rem; color: #38bdf8;">{{ number_format($totalRevenue, 2, ',', '.') }} ₺</div>
            <div class="metric-sub">{{ $totalCustomersCount }} Müşteri</div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="tabs-nav" id="dashboard-tabs">
        <button type="button" class="tab-btn {{ $tab === 'overview' || $tab === '' ? 'active' : '' }}" onclick="switchTab('overview')">
            📊 Genel Bakış
        </button>
        <button type="button" class="tab-btn {{ $tab === 'orders' ? 'active' : '' }}" onclick="switchTab('orders')">
            📦 Siparişler <span class="tab-badge">{{ $orders->count() }}</span>
        </button>
        <button type="button" class="tab-btn {{ $tab === 'shipments' ? 'active' : '' }}" onclick="switchTab('shipments')">
            🚚 Kargolar <span class="tab-badge">{{ $shipments->count() }}</span>
        </button>
        <button type="button" class="tab-btn {{ $tab === 'customers' ? 'active' : '' }}" onclick="switchTab('customers')">
            👥 Müşteriler <span class="tab-badge">{{ $customers->count() }}</span>
        </button>
    </div>

    <!-- TAB 1: Genel Bakış -->
    <div id="tab-content-overview" class="tab-pane" style="{{ ($tab === 'overview' || $tab === '') ? '' : 'display: none;' }}">
        <div class="panel-card">
            <div class="panel-header">
                <h2>Son Siparişler ve Kargo Hareketleri</h2>
                <button type="button" class="btn btn-secondary btn-sm" onclick="switchTab('orders')">Tüm Siparişleri Gör ➜</button>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Sipariş No</th>
                            <th>Müşteri</th>
                            <th>Tutar</th>
                            <th>Durum</th>
                            <th>Kargo Takip</th>
                            <th>Tarih</th>
                            <th>Hızlı İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders->take(6) as $order)
                            <tr id="row-order-{{ $order->id }}">
                                <td>
                                    <span class="order-code">{{ $order->order_number }}</span>
                                </td>
                                <td>
                                    <div style="font-weight: 600;">{{ $order->customer->name }}</div>
                                    <small style="color: var(--text-muted);">{{ $order->customer->city ?? 'Türkiye' }}</small>
                                </td>
                                <td><strong>{{ $order->formatted_total }}</strong></td>
                                <td>
                                    <span class="status-badge status-badge-{{ $order->status->slug }}" id="badge-order-{{ $order->id }}">
                                        <span class="dot"></span> {{ $order->status->name }}
                                    </span>
                                </td>
                                <td>
                                    @if($order->shipment)
                                        <div>
                                            <a href="{{ route('tracking.index', ['q' => $order->shipment->tracking_number]) }}" style="color: #38bdf8; font-family: monospace; font-weight: 600;">
                                                🚚 {{ $order->shipment->tracking_number }}
                                            </a>
                                            <div style="font-size: 0.78rem; color: var(--text-muted);">{{ $order->shipment->company }}</div>
                                        </div>
                                    @else
                                        <span style="color: var(--text-muted); font-size: 0.82rem;">Henüz Verilmedi</span>
                                    @endif
                                </td>
                                <td style="font-size: 0.85rem; color: var(--text-muted);">
                                    {{ $order->created_at->format('d.m.Y H:i') }}
                                </td>
                                <td>
                                    <select class="status-dropdown" onchange="quickUpdateStatus({{ $order->id }}, this.value)">
                                        @foreach($statuses as $st)
                                            <option value="{{ $st->id }}" {{ $order->status_id == $st->id ? 'selected' : '' }}>
                                                {{ $st->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 30px;">
                                    Henüz kayıtlı sipariş bulunmuyor.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: Siparişler -->
    <div id="tab-content-orders" class="tab-pane" style="{{ $tab === 'orders' ? '' : 'display: none;' }}">
        <div class="panel-card">
            <div class="panel-header">
                <h2>Tüm Siparişler</h2>
                <div class="panel-filters">
                    <form method="GET" action="{{ route('dashboard.index') }}" style="display: flex; gap: 8px;">
                        <input type="hidden" name="tab" value="orders">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">Tüm Durumlar</option>
                            @foreach($statuses as $st)
                                <option value="{{ $st->slug }}" {{ $statusFilter == $st->slug ? 'selected' : '' }}>
                                    {{ $st->name }}
                                </option>
                            @endforeach
                        </select>
                        <input 
                            type="text" 
                            name="search" 
                            class="form-control" 
                            placeholder="Sipariş No / Müşteri Ara..." 
                            value="{{ $search }}"
                        >
                        <button type="submit" class="btn btn-secondary btn-sm">Filtrele</button>
                        @if($statusFilter || $search)
                            <a href="{{ route('dashboard.index', ['tab' => 'orders']) }}" class="btn btn-secondary btn-sm">Temizle</a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Sipariş No</th>
                            <th>Müşteri</th>
                            <th>Tutar</th>
                            <th>Durum</th>
                            <th>Kargo Takip</th>
                            <th>Not</th>
                            <th>Oluşturulma</th>
                            <th>Durum Değiştir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr id="row-order-tab-{{ $order->id }}">
                                <td>
                                    <span class="order-code">{{ $order->order_number }}</span>
                                </td>
                                <td>
                                    <div style="font-weight: 600;">{{ $order->customer->name }}</div>
                                    <small style="color: var(--text-muted);">{{ $order->customer->phone }}</small>
                                </td>
                                <td><strong>{{ $order->formatted_total }}</strong></td>
                                <td>
                                    <span class="status-badge status-badge-{{ $order->status->slug }}" id="badge-order-tab-{{ $order->id }}">
                                        <span class="dot"></span> {{ $order->status->name }}
                                    </span>
                                </td>
                                <td>
                                    @if($order->shipment)
                                        <a href="{{ route('tracking.index', ['q' => $order->shipment->tracking_number]) }}" style="color: #38bdf8; font-family: monospace; font-weight: 600;">
                                            {{ $order->shipment->company }} <br>
                                            <small>{{ $order->shipment->tracking_number }}</small>
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="openShipmentModalForOrder({{ $order->id }}, '{{ $order->order_number }}')">
                                            + Kargo Ekle
                                        </button>
                                    @endif
                                </td>
                                <td style="max-width: 180px; font-size: 0.82rem; color: var(--text-secondary); text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                    {{ $order->note ?? '-' }}
                                </td>
                                <td style="font-size: 0.85rem; color: var(--text-muted);">
                                    {{ $order->created_at->format('d.m.Y H:i') }}
                                </td>
                                <td>
                                    <select class="status-dropdown" onchange="quickUpdateStatus({{ $order->id }}, this.value)">
                                        @foreach($statuses as $st)
                                            <option value="{{ $st->id }}" {{ $order->status_id == $st->id ? 'selected' : '' }}>
                                                {{ $st->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 40px;">
                                    Aramaya uygun sipariş bulunamadı.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 3: Kargolar -->
    <div id="tab-content-shipments" class="tab-pane" style="{{ $tab === 'shipments' ? '' : 'display: none;' }}">
        <div class="panel-card">
            <div class="panel-header">
                <h2>Kargo Gönderileri</h2>
                <button type="button" class="btn btn-primary btn-sm" onclick="openModal('modal-shipment')">
                    ➕ Yeni Kargo Kaydı
                </button>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Takip No</th>
                            <th>Kargo Firması</th>
                            <th>Sipariş No</th>
                            <th>Alıcı Müşteri</th>
                            <th>Son Hareket / Konum</th>
                            <th>Gönderim Tarihi</th>
                            <th>Teslim Tarihi</th>
                            <th>Sorgula</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shipments as $shipment)
                            <tr>
                                <td>
                                    <strong style="color: #38bdf8; font-family: monospace;">{{ $shipment->tracking_number }}</strong>
                                </td>
                                <td>
                                    <span style="background: rgba(255, 255, 255, 0.08); padding: 4px 10px; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 600;">
                                        {{ $shipment->company }}
                                    </span>
                                </td>
                                <td><span class="order-code">{{ $shipment->order->order_number }}</span></td>
                                <td>
                                    <div style="font-weight: 600;">{{ $shipment->order->customer->name }}</div>
                                    <small style="color: var(--text-muted);">{{ $shipment->order->customer->city }}</small>
                                </td>
                                <td style="max-width: 220px; font-size: 0.85rem; color: #cbd5e1;">
                                    {{ $shipment->current_status_desc ?? 'Şubede işlem görüyor' }}
                                </td>
                                <td style="font-size: 0.85rem; color: var(--text-muted);">
                                    {{ $shipment->shipped_at ? $shipment->shipped_at->format('d.m.Y H:i') : '-' }}
                                </td>
                                <td style="font-size: 0.85rem;">
                                    @if($shipment->delivered_at)
                                        <span style="color: #10b981; font-weight: 600;">{{ $shipment->delivered_at->format('d.m.Y H:i') }}</span>
                                    @else
                                        <span style="color: var(--text-muted);">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('tracking.index', ['q' => $shipment->tracking_number]) }}" class="btn btn-secondary btn-sm" target="_blank">
                                        🔍 Takip
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 40px;">
                                    Henüz kargo kaydı bulunmuyor.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 4: Müşteriler -->
    <div id="tab-content-customers" class="tab-pane" style="{{ $tab === 'customers' ? '' : 'display: none;' }}">
        <div class="panel-card">
            <div class="panel-header">
                <h2>Kayıtlı Müşteriler</h2>
                <button type="button" class="btn btn-primary btn-sm" onclick="openModal('modal-customer')">
                    ➕ Yeni Müşteri
                </button>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Müşteri Adı</th>
                            <th>E-Posta</th>
                            <th>Telefon</th>
                            <th>Şehir / İlçe</th>
                            <th>Adres</th>
                            <th>Toplam Sipariş</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td>
                                    <strong style="color: #fff;">{{ $customer->name }}</strong>
                                </td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->phone ?? '-' }}</td>
                                <td>{{ $customer->city ?? '-' }}</td>
                                <td style="max-width: 260px; font-size: 0.85rem; color: var(--text-secondary);">
                                    {{ $customer->address ?? '-' }}
                                </td>
                                <td>
                                    <span style="background: rgba(99, 102, 241, 0.2); color: #a5b4fc; font-weight: 700; padding: 4px 12px; border-radius: var(--radius-full); font-size: 0.85rem;">
                                        {{ $customer->orders_count }} Sipariş
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px;">
                                    Henüz müşteri kaydı bulunmuyor.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL: YENİ SİPARİŞ ================= -->
<div id="modal-order" class="modal-overlay">
    <div class="modal-dialog">
        <form action="{{ route('dashboard.orders.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h3 class="modal-title">📦 Yeni Sipariş Oluştur</h3>
                <button type="button" class="modal-close" onclick="closeModal('modal-order')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Müşteri Seçin *</label>
                    <select name="customer_id" class="form-select" style="width: 100%;" required>
                        <option value="">Seçiniz...</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Başlangıç Durumu *</label>
                        <select name="status_id" class="form-select" style="width: 100%;" required>
                            @foreach($statuses as $st)
                                <option value="{{ $st->id }}" {{ $st->name === \App\Models\Status::BEKLEMEDE ? 'selected' : '' }}>
                                    {{ $st->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Sipariş Tutarı (₺) *</label>
                        <input type="number" step="0.01" name="total_amount" class="form-control" style="width: 100%;" placeholder="0.00" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Sipariş Notu</label>
                    <textarea name="note" class="form-control" style="width: 100%; min-height: 70px;" placeholder="Teslimat notu, kırılabilir uyarısı vb..."></textarea>
                </div>

                <div style="border-top: 1px dashed var(--border-subtle); padding-top: 16px; margin-top: 16px;">
                    <h4 style="font-size: 0.95rem; margin-bottom: 12px; color: #a5b4fc;">🚚 Kargo Bilgisi (İsteğe Bağlı)</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Kargo Firması</label>
                            <select name="company" class="form-select" style="width: 100%;">
                                <option value="">Firma Seçin...</option>
                                <option value="Yurtiçi Kargo">Yurtiçi Kargo</option>
                                <option value="Aras Kargo">Aras Kargo</option>
                                <option value="MNG Kargo">MNG Kargo</option>
                                <option value="Trendyol Express">Trendyol Express</option>
                                <option value="Sürat Kargo">Sürat Kargo</option>
                                <option value="PTT Kargo">PTT Kargo</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Takip No</label>
                            <input type="text" name="tracking_number" class="form-control" style="width: 100%;" placeholder="örn: YK-12345678">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-order')">İptal</button>
                <button type="submit" class="btn btn-primary">Siparişi Kaydet</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL: KARGO EKLE ================= -->
<div id="modal-shipment" class="modal-overlay">
    <div class="modal-dialog">
        <form action="{{ route('dashboard.shipments.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h3 class="modal-title">🚚 Siparişe Kargo Tanımla</h3>
                <button type="button" class="modal-close" onclick="closeModal('modal-shipment')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Sipariş Seçin *</label>
                    <select name="order_id" id="shipment-order-select" class="form-select" style="width: 100%;" required>
                        <option value="">Sipariş Seçiniz...</option>
                        @foreach($orders as $ord)
                            <option value="{{ $ord->id }}">
                                {{ $ord->order_number }} - {{ $ord->customer->name }} ({{ $ord->status->name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Kargo Firması *</label>
                        <select name="company" class="form-select" style="width: 100%;" required>
                            <option value="Yurtiçi Kargo">Yurtiçi Kargo</option>
                            <option value="Aras Kargo">Aras Kargo</option>
                            <option value="MNG Kargo">MNG Kargo</option>
                            <option value="Trendyol Express">Trendyol Express</option>
                            <option value="Sürat Kargo">Sürat Kargo</option>
                            <option value="PTT Kargo">PTT Kargo</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Takip Numarası *</label>
                        <div style="display: flex; gap: 6px;">
                            <input type="text" name="tracking_number" id="shipment-tracking-input" class="form-control" style="flex: 1;" placeholder="Takip No..." required>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="generateTrackingCode()">Üret</button>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Güncel Durum / Konum Açıklaması</label>
                    <input type="text" name="current_status_desc" class="form-control" style="width: 100%;" placeholder="örn: Transfer Merkezinden Yola Çıktı" value="Kargo şubede işlem görüyor">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-shipment')">İptal</button>
                <button type="submit" class="btn btn-primary">Kargoyu Kaydet</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL: YENİ MÜŞTERİ ================= -->
<div id="modal-customer" class="modal-overlay">
    <div class="modal-dialog">
        <form action="{{ route('dashboard.customers.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h3 class="modal-title">👥 Yeni Müşteri Kaydı</h3>
                <button type="button" class="modal-close" onclick="closeModal('modal-customer')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Ad Soyad *</label>
                    <input type="text" name="name" class="form-control" style="width: 100%;" placeholder="örn: Caner Öztürk" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">E-Posta *</label>
                        <input type="email" name="email" class="form-control" style="width: 100%;" placeholder="caner@example.com" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Telefon</label>
                        <input type="text" name="phone" class="form-control" style="width: 100%;" placeholder="0532 000 00 00">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Şehir / İlçe</label>
                    <input type="text" name="city" class="form-control" style="width: 100%;" placeholder="örn: İstanbul, Beşiktaş">
                </div>

                <div class="form-group">
                    <label class="form-label">Tam Adres</label>
                    <textarea name="address" class="form-control" style="width: 100%; min-height: 70px;" placeholder="Mahalle, sokak, apartman ve daire no..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-customer')">İptal</button>
                <button type="submit" class="btn btn-primary">Müşteriyi Kaydet</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Tab Değiştirme
    function switchTab(tabName) {
        document.querySelectorAll('.tab-pane').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));

        const targetPane = document.getElementById(`tab-content-${tabName}`);
        if (targetPane) {
            targetPane.style.display = 'block';
        }

        // Aktif buton seçimi
        const buttons = document.querySelectorAll('.tab-btn');
        if (tabName === 'overview') buttons[0].classList.add('active');
        else if (tabName === 'orders') buttons[1].classList.add('active');
        else if (tabName === 'shipments') buttons[2].classList.add('active');
        else if (tabName === 'customers') buttons[3].classList.add('active');

        // URL history güncelle
        const url = new URL(window.location);
        url.searchParams.set('tab', tabName);
        window.history.replaceState({}, '', url);
    }

    // Modal Açma & Kapatma
    function openModal(id) {
        document.getElementById(id).classList.add('open');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
    }

    function openShipmentModalForOrder(orderId, orderNumber) {
        const select = document.getElementById('shipment-order-select');
        select.value = orderId;
        generateTrackingCode();
        openModal('modal-shipment');
    }

    function generateTrackingCode() {
        const prefixes = ['YK', 'AR', 'MNG', 'TY', 'SR', 'PTT'];
        const prefix = prefixes[Math.floor(Math.random() * prefixes.length)];
        const randNum = Math.floor(10000000 + Math.random() * 90000000);
        document.getElementById('shipment-tracking-input').value = `${prefix}-${randNum}`;
    }

    // Hızlı Durum Güncelleme (AJAX)
    function quickUpdateStatus(orderId, statusId) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/yonetim/siparisler/${orderId}/durum`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ status_id: statusId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                // Badge güncelle
                const badge1 = document.getElementById(`badge-order-${orderId}`);
                if (badge1) {
                    badge1.className = `status-badge status-badge-${data.status.slug}`;
                    badge1.innerHTML = `<span class="dot"></span> ${data.status.name}`;
                }
                const badge2 = document.getElementById(`badge-order-tab-${orderId}`);
                if (badge2) {
                    badge2.className = `status-badge status-badge-${data.status.slug}`;
                    badge2.innerHTML = `<span class="dot"></span> ${data.status.name}`;
                }
            } else {
                showToast(data.message || 'Güncelleme başarısız.', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Bağlantı hatası oluştu.', 'error');
        });
    }

    // Modal dışına tıklandığında kapatma
    window.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal-overlay')) {
            e.target.classList.remove('open');
        }
    });
</script>
@endpush
