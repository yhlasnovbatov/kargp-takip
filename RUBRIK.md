# Kargo ve Sipariş Takip Sistemi — Proje Değerlendirme Rubriği

Bu belge, **Laravel ile Kargo ve Sipariş Takip Sistemi** projesinin değerlendirilmesi için hazırlanmış, ölçülebilir ve nesnel kanıtlara dayanan 100 puanlık başarı rubriğidir.

---

## 1. Genel Proje Bilgileri

- **Proje Konusu:** Kargo ve Sipariş Takip Sistemi
- **Temel Varlıklar:** `Customer` (Müşteri), `Order` (Sipariş), `Shipment` (Kargo), `Status` (Durum)
- **Kritik İş Kuralı:** *"Teslim edilmiş sipariş iptal edilemez."*
- **Teknoloji Yığını:** PHP 8.2+, Laravel 12, SQLite / MySQL, Bootstrap 5.3 CDN, Blade Şablon Motoru

---

## 2. Değerlendirme Kriterleri ve Puan Dağılımı

### Kriter 1: En Az İki Varlık İçin Tam CRUD (25 Puan)
*Açıklama: Belirtilen varlıklardan en az ikisinde (projede 4 varlığın tümünde) 7 standart kaynak metodunun (`index`, `create`, `store`, `show`, `edit`, `update`, `destroy`) ve arayüzlerinin eksiksiz çalışması.*

- **Ölçülebilir Kanıtlar:**
  - Rota Listesi: `php artisan route:list` (Customer, Status, Order, Shipment için resource rotaları)
  - Controller'lar: [CustomerController.php](file:///c:/Users/LAB-HERSEK/Documents/wt-projeler/wt-proje1/kargo-takip/app/Http/Controllers/CustomerController.php), [OrderController.php](file:///c:/Users/LAB-HERSEK/Documents/wt-projeler/wt-proje1/kargo-takip/app/Http/Controllers/OrderController.php), [ShipmentController.php](file:///c:/Users/LAB-HERSEK/Documents/wt-projeler/wt-proje1/kargo-takip/app/Http/Controllers/ShipmentController.php), [StatusController.php](file:///c:/Users/LAB-HERSEK/Documents/wt-projeler/wt-proje1/kargo-takip/app/Http/Controllers/StatusController.php)
  - Test Kanıtı: `tests/Feature/CrudResourceTest.php` (`php artisan test --filter=CrudResourceTest`)
- **Puanlama Koşulları:**
  - **Tam Puan (25):** En az iki varlıkta (veya tüm varlıklarda) 7 CRUD metodunun tamamı tanımlı; formlar üzerinden veri ekleme, listeleme, detay görüntüleme, güncelleme ve silme sorunsuz çalışıyor.
  - **Kısmi Puan (12 - 18):** CRUD işlemlerinin büyük kısmı çalışıyor fakat bazı metodlar (örn. `show` veya `destroy`) eksik, rota çakışması var veya sayfalama/form hatası bulunuyor.
  - **Puan Yok (0):** CRUD yapısı oluşturulmamış veya formlar kaydedilemiyor/hata veriyor.

---

### Kriter 2: Konsepte Uygunluk (10 Puan)
*Açıklama: Varlıkların (Customer, Order, Shipment, Status) kargo ve sipariş takip etki alanına (domain) uygun alanlarla modellenmesi.*

- **Ölçülebilir Kanıtlar:**
  - `Customer`: `name`, `email`, `phone`, `address`, `city`
  - `Status`: `name`, `slug` (`pending`, `preparing`, `shipped`, `delivered`, `cancelled`), `color`, `description`
  - `Order`: `customer_id`, `status_id`, `order_number`, `total_amount`, `note`
  - `Shipment`: `order_id`, `company`, `tracking_number`, `shipped_at`, `delivered_at`, `current_status_desc`
  - Migration Dosyaları: `database/migrations/2026_10_12_075028_*.php` .. `075031_*.php`
- **Puanlama Koşulları:**
  - **Tam Puan (10):** Tüm varlıklar ve veri alanları kargo/sipariş takibi senaryosuna birebir hizmet ediyor, eksik temel alan bulunmuyor.
  - **Kısmi Puan (5):** Varlıklar oluşturulmuş ancak kargo takibi için elzem alanlar (örn. takip no, teslim tarihi, sipariş tutarı) eksik veya uyumsuz.
  - **Puan Yok (0):** Varlıklar kargo/sipariş takip konseptinden kopuk veya generic isimlerle bırakılmış.

---

### Kriter 3: İş Kuralı Uygulaması (15 Puan)
*Açıklama: "Teslim edilmiş sipariş iptal edilemez." iş kuralının Model, Controller ve Arayüz (Blade) olmak üzere 3 katmanda birden güvenceye alınması.*

- **Ölçülebilir Kanıtlar:**
  - **1. Katman (Model):** [Order.php](file:///c:/Users/LAB-HERSEK/Documents/wt-projeler/wt-proje1/kargo-takip/app/Models/Order.php) içinde `isDelivered()` metodu ve `booted()` updating olayında `delivered -> cancelled` geçişinde `\DomainException('Teslim edilmiş sipariş iptal edilemez.')` fırlatılması.
  - **2. Katman (Controller):** [OrderController.php](file:///c:/Users/LAB-HERSEK/Documents/wt-projeler/wt-proje1/kargo-takip/app/Http/Controllers/OrderController.php) içinde `cancel()` metodu (`orders.cancel` POST rotası). Sipariş teslim edilmişse `back()->with('error', ...)`, değilse `status_id`='cancelled' yapıp `back()->with('success', ...)`. `update()` metodunda `DomainException`'ın yakalanıp `withErrors` ile forma iletilmesi.
  - **3. Katman (Arayüz):** [orders/index.blade.php](file:///c:/Users/LAB-HERSEK/Documents/wt-projeler/wt-proje1/kargo-takip/resources/views/orders/index.blade.php) ve [orders/show.blade.php](file:///c:/Users/LAB-HERSEK/Documents/wt-projeler/wt-proje1/kargo-takip/resources/views/orders/show.blade.php) üzerinde teslim edilmiş siparişlerde "İptal Et" butonunun görünmemesi; [orders/_form.blade.php](file:///c:/Users/LAB-HERSEK/Documents/wt-projeler/wt-proje1/kargo-takip/resources/views/orders/_form.blade.php) içinde durum select'inde "İptal" seçeneğinin `disabled` olması; [layouts/app.blade.php](file:///c:/Users/LAB-HERSEK/Documents/wt-projeler/wt-proje1/kargo-takip/resources/views/layouts/app.blade.php) içinde `session('error')` ve `session('success')` mesajlarının gösterilmesi.
  - Test Kanıtı: `tests/Feature/OrderCancelTest.php`
- **Puanlama Koşulları:**
  - **Tam Puan (15):** Kural 3 katmanda da (Model, Controller, Arayüz) eksiksiz uygulanmış ve testlerle doğrulanabiliyor.
  - **Kısmi Puan (7 - 10):** Kural 1 veya 2 katmanda var (örn. sadece Controller kontrolü var fakat model veya arayüzde koruma yok).
  - **Puan Yok (0):** Teslim edilmiş sipariş iptal edilebiliyor veya kural kodlanmamış.

---

### Kriter 4: Doğrulama (FormRequest Sınıfları) (10 Puan)
*Açıklama: Form verilerinin Controller içinde dağınık yazılmak yerine özel FormRequest sınıflarıyla doğrulanması; required, email, unique, exists kurallarının ve anlamlı hata mesajlarının bulunması.*

- **Ölçülebilir Kanıtlar:**
  - [CustomerRequest.php](file:///c:/Users/LAB-HERSEK/Documents/wt-projeler/wt-proje1/kargo-takip/app/Http/Requests/CustomerRequest.php) (`required`, `email`, `unique` ignoring ID)
  - [StatusRequest.php](file:///c:/Users/LAB-HERSEK/Documents/wt-projeler/wt-proje1/kargo-takip/app/Http/Requests/StatusRequest.php) (`required`, `unique` ignoring ID)
  - [OrderRequest.php](file:///c:/Users/LAB-HERSEK/Documents/wt-projeler/wt-proje1/kargo-takip/app/Http/Requests/OrderRequest.php) (`required`, `exists:customers,id`, `exists:statuses,id`, `unique:orders,order_number`)
  - [ShipmentRequest.php](file:///c:/Users/LAB-HERSEK/Documents/wt-projeler/wt-proje1/kargo-takip/app/Http/Requests/ShipmentRequest.php) (`required`, `exists:orders,id`, `unique:shipments,tracking_number`)
  - `messages()` metotları ile Türkçe hata açıklamaları.
- **Puanlama Koşulları:**
  - **Tam Puan (10):** Tüm kaynaklar için FormRequest sınıfları mevcut; `required`, `email`, `unique` (güncellemede ID ignore), `exists` kuralları ve anlaşılır hata mesajları tanımlı.
  - **Kısmi Puan (5):** Doğrulama yapılıyor fakat FormRequest yerine doğrudan controller içinde `$request->validate()` yazılmış veya `unique`/`exists` kuralları eksik.
  - **Puan Yok (0):** Form doğrulama kuralları tanımlanmamış.

---

### Kriter 5: İlişkiler ve Veritabanı Mimarisi (10 Puan)
*Açıklama: Tablolar arasında yabancı anahtar (foreign key) kısıtlamaları, Eloquent ilişkileri, tohumlayıcılar (seeders) ve N+1 sorgularının engellenmesi.*

- **Ölçülebilir Kanıtlar:**
  - İlişkiler: `Customer` hasMany `Order`, `Order` belongsTo `Customer`, `Order` belongsTo `Status`, `Order` hasOne `Shipment`, `Shipment` belongsTo `Order`.
  - N+1 Çözümü: [OrderController.php](file:///c:/Users/LAB-HERSEK/Documents/wt-projeler/wt-proje1/kargo-takip/app/Http/Controllers/OrderController.php) içinde:
    `Order::with(['customer', 'status', 'shipment'])->latest()->paginate(10);`
  - Seeder'lar: [StatusSeeder.php](file:///c:/Users/LAB-HERSEK/Documents/wt-projeler/wt-proje1/kargo-takip/database/seeders/StatusSeeder.php) ve [DatabaseSeeder.php](file:///c:/Users/LAB-HERSEK/Documents/wt-projeler/wt-proje1/kargo-takip/database/seeders/DatabaseSeeder.php)
- **Puanlama Koşulları:**
  - **Tam Puan (10):** Migration'larda `foreignId()->constrained()->cascadeOnDelete()` tanımlı; Eloquent ilişkileri eksiksiz; `StatusSeeder` mevcut; `Order::with(...)` ile N+1 önlenmiş.
  - **Kısmi Puan (5):** İlişkiler kurulu ancak N+1 önlemi alınmamış veya Seeder eksik/hatalı.
  - **Puan Yok (0):** Yabancı anahtarlar veya model ilişkileri tanımlanmamış.

---

### Kriter 6: Otomatik Testler (10 Puan)
*Açıklama: İş kuralının ve CRUD akışlarının PHPUnit / Pest feature testleriyle doğrulanması, tüm testlerin başarıyla geçmesi.*

- **Ölçülebilir Kanıtlar:**
  - [OrderCancelTest.php](file:///c:/Users/LAB-HERSEK/Documents/wt-projeler/wt-proje1/kargo-takip/tests/Feature/OrderCancelTest.php):
    1. `test_teslim_edilmis_siparis_iptal_edilemez` (error flash kontrolü, durumun delivered kalması)
    2. `test_beklemedeki_siparis_iptal_edilebilir` (success flash kontrolü, durumun cancelled olması)
    3. `test_teslim_edilmis_siparisin_durumu_update_ile_cancelled_yapilamaz` (DomainException ve validasyon hatası kontrolü)
  - [CrudResourceTest.php](file:///c:/Users/LAB-HERSEK/Documents/wt-projeler/wt-proje1/kargo-takip/tests/Feature/CrudResourceTest.php): 4 model için tam CRUD akış testi.
  - Çalıştırma Komutu: `php artisan test` (Sonuç: 9 passed, 90 assertions)
- **Puanlama Koşulları:**
  - **Tam Puan (10):** `php artisan test` komutu çalıştırıldığında tüm testler yeşil (PASS); teslim edilmiş siparişin iptal edilememe kuralı testlerle doğrudan kanıtlanıyor.
  - **Kısmi Puan (5):** Testler yazılmış ancak bazıları başarısız (FAIL) veya sadece temel testler mevcut, iş kuralı test edilmemiş.
  - **Puan Yok (0):** Projede test yazılmamış veya test komutu hata veriyor.

---

### Kriter 7: Kod Kalitesi ve Düzen (10 Puan)
*Açıklama: PSR kodlama standartları, sade controller'lar (skinny controllers), ortak Blade form parçaları (`_form.blade.php`), Bootstrap 5 arayüz düzeni.*

- **Ölçülebilir Kanıtlar:**
  - Ortak Form Parçaları: `customers/_form.blade.php`, `statuses/_form.blade.php`, `orders/_form.blade.php`, `shipments/_form.blade.php` (kod tekrarı engellenmiş).
  - Ana Şablon: [layouts/app.blade.php](file:///c:/Users/LAB-HERSEK/Documents/wt-projeler/wt-proje1/kargo-takip/resources/views/layouts/app.blade.php) (Menü: Siparişler, Müşteriler, Kargolar, Durumlar; Flash mesaj kutuları).
  - Controller'larda iş mantığı ve validasyon FormRequest/Model katmanlarına delege edilmiş.
- **Puanlama Koşulları:**
  - **Tam Puan (10):** Kod yapısı modüler; `_form.blade.php` partial'ları ile DRY prensibine uyulmuş; controller'lar sade ve okunabilir; isimlendirmeler standartlara uygun.
  - **Kısmi Puan (5):** Kod çalışıyor ancak form parçaları her blade dosyasına kopyalanmış (kod tekrarı var) veya controller'lar gereksiz karmaşık.
  - **Puan Yok (0):** Spagetti kod, şablon yapısı kurulmamış, okunaksız yapı.

---

### Kriter 8: README Dokümantasyonu (5 Puan)
*Açıklama: Projenin nasıl kurulacağı, gereksinimleri, testlerin nasıl çalıştırılacağı ve kargo takip sisteminin nasıl kullanılacağını anlatan rehber.*

- **Ölçülebilir Kanıtlar:**
  - [README.md](file:///c:/Users/LAB-HERSEK/Documents/wt-projeler/wt-proje1/kargo-takip/README.md) dosyası.
  - Kurulum adımları (`composer install`, `.env`, `php artisan migrate --seed`, `php artisan serve`).
  - Test adımları (`php artisan test`).
  - Rota listesi ve iş kuralı açıklaması.
- **Puanlama Koşulları:**
  - **Tam Puan (5):** Kurulum, test çalıştırma, rota tablosu ve kullanım talimatları açık, adım adım ve hatasız belgelenmiş.
  - **Kısmi Puan (2 - 3):** README mevcut fakat adımlar yüzeysel, eksik veya varsayılan metinler temizlenmemiş.
  - **Puan Yok (0):** README dosyası yok veya varsayılan Laravel taslağı değiştirilmemiş.

---

### Kriter 9: Git ve Güvenlik Kullanımı (5 Puan)
*Açıklama: Düzenli versiyon kontrolü, `.gitignore` dosyasının doğru yapılandırılması ve `.env` dosyasının kesinlikle versiyon kontrolüne commit edilmemiş olması.*

- **Ölçülebilir Kanıtlar:**
  - `.gitignore` dosyası: `.env`, `/vendor`, `/node_modules`, `database/*.sqlite` kayıtları mevcut.
  - `.env.example` dosyası şablon olarak hazır.
  - Canlı API/anahtar sızıntısı bulunmuyor.
- **Puanlama Koşulları:**
  - **Tam Puan (5):** `.env` dosyası commit edilmemiş, `.gitignore` kuralları eksiksiz, `.env.example` mevcut ve commit geçmişi düzenli.
  - **Kısmi Puan (2 - 3):** `.gitignore` mevcut ancak commit yapısı dağınık veya `.env.example` eksik.
  - **Puan Yok (0):** `.env` hassas anahtarlarla birlikte git deposuna commit edilmiş.

---

## 3. Puanlama Özeti Tablosu

| # | Kriter Başlığı | Ölçüm ve Kanıt Alanı | Maks. Puan |
| :-: | :--- | :--- | :-: |
| **1** | **Tam CRUD İşlemleri** | En az iki (projede 4) varlık için 7 metod, route ve formlar | **25** |
| **2** | **Konsepte Uygunluk** | Customer, Order, Shipment, Status kargo takip modellemesi | **10** |
| **3** | **İş Kuralı Uygulaması** | "Teslim edilmiş sipariş iptal edilemez" (Model, Controller, Blade) | **15** |
| **4** | **Doğrulama (FormRequest)** | `required`, `email`, `unique`, `exists` kuralları ve hata mesajları | **10** |
| **5** | **İlişkiler ve Veritabanı** | Foreign key, Eloquent ilişkileri, StatusSeeder, N+1 önleme | **10** |
| **6** | **Otomatik Testler** | `OrderCancelTest` ve `CrudResourceTest` (`php artisan test`) | **10** |
| **7** | **Kod Kalitesi ve Düzen** | `_form` parçaları, layouts/app menüsü, Bootstrap 5, sade mimari | **10** |
| **8** | **README Dokümantasyonu** | Adım adım kurulum, kullanım, test ve rota rehberi | **5** |
| **9** | **Git ve Güvenlik** | `.env` commit edilmemesi, `.gitignore`, `.env.example` | **5** |
| | **TOPLAM** | **100 Üzerinden Başarı Değerlendirmesi** | **100** |

---

## 4. Hızlı Doğrulama Komutları

Değerlendirici için projeyi saniyeler içinde doğrulamaya yarayan terminal komutları:

```bash
# 1. Rota listesini doğrula (CRUD ve cancel rotaları)
php artisan route:list

# 2. Otomatik testleri çalıştır (İş kuralı ve CRUD testleri)
php artisan test

# 3. Yalnızca teslimat iptal kuralı testlerini çalıştır
php artisan test --filter=OrderCancelTest

# 4. Veritabanını tohumla ve durumu incele
php artisan db:seed
```
