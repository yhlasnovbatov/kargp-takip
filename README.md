# 📦 Kargo ve Sipariş Takip Sistemi (Laravel 12)

Kargo ve sipariş hareketlerinin gerçek zamanlı izlendiği, müşteri, sipariş, kargo ve durum yönetiminin tam CRUD mimarisiyle gerçekleştirildiği modern bir Laravel web uygulaması.

---

## 🚀 Temel Özellikler

- **4 Ana Varlık İçin Tam CRUD:** `Customer` (Müşteri), `Status` (Durum), `Order` (Sipariş), `Shipment` (Kargo)
- **Kritik İş Kuralı:** *"Teslim edilmiş sipariş iptal edilemez."*
  - **Model:** `Order::isDelivered()` kontrolü ve updating olayı ile `\DomainException` fırlatma.
  - **Controller:** `POST /orders/{order}/cancel` rotası ve `update()` içinde `DomainException` yakalayarak `withErrors` ile forma bildirme.
  - **Arayüz:** Teslim edilmiş siparişlerde "İptal Et" butonunun gizlenmesi; düzenleme formunda "İptal" seçeneğinin `disabled` yapılması.
- **FormRequest Doğrulaması:** `required`, `email`, `unique` (ID ignore), `exists` kuralları ve anlaşılır Türkçe hata mesajları.
- **N+1 Sorgu Önleme & Sayfalama:** `Order::with(['customer', 'status', 'shipment'])->paginate(10)`
- **Bootstrap 5.3 CDN Arayüzü:** Ortak `_form.blade.php` bileşenleri, `layouts/app.blade.php` ana şablonu, flash bildirimleri (`session('success')`, `session('error')`, `$errors`).
- **Otomatik Testler:** PHPUnit Feature testleri (`php artisan test`) ile %100 doğrulama.

---

## 🛠️ Kurulum Adımları

### 1. Gereksinimler
- PHP 8.2 veya üzeri
- Composer 2.x
- SQLite veya MySQL veritabanı

### 2. Proje Kurulumu
```bash
# Bağımlılıkları yükleyin
composer install

# Çevre değişkenleri dosyasını kopyalayın
copy .env.example .env

# Uygulama anahtarını üretin
php artisan key:generate
```

### 3. Veritabanı ve Tohumlama (Seed)
```bash
# Tabloları oluşturun ve varsayılan durumlar ile örnek verileri yükleyin
php artisan migrate --seed
```

### 4. Geliştirme Sunucusunu Başlatma
```bash
php artisan serve
```
Tarayıcınızda açın:
- **Siparişler (CRUD):** [http://127.0.0.1:8000/orders](http://127.0.0.1:8000/orders)
- **Müşteriler (CRUD):** [http://127.0.0.1:8000/customers](http://127.0.0.1:8000/customers)
- **Kargolar (CRUD):** [http://127.0.0.1:8000/shipments](http://127.0.0.1:8000/shipments)
- **Durumlar (CRUD):** [http://127.0.0.1:8000/statuses](http://127.0.0.1:8000/statuses)
- **Kargo Sorgulama:** [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 🧪 Testlerin Çalıştırılması

Tüm iş kuralları ve CRUD fonksiyonları otomatik testlerle korunmaktadır:

```bash
# Tüm testleri çalıştırın
php artisan test

# Sadece teslimat ve iptal iş kuralı testlerini çalıştırın
php artisan test --filter=OrderCancelTest

# CRUD kaynak testlerini çalıştırın
php artisan test --filter=CrudResourceTest
```

---

## 📋 Değerlendirme Rubriği

Projenin 100 puanlık ölçülebilir ve kanıtlanabilir değerlendirme kriterleri için [RUBRIK.md](file:///c:/Users/LAB-HERSEK/Documents/wt-projeler/wt-proje1/kargo-takip/RUBRIK.md) dosyasına göz atabilirsiniz.

---

## 📄 Lisans
Bu proje eğitim ve değerlendirme amaçlı geliştirilmiştir. Açık kaynaklıdır.
