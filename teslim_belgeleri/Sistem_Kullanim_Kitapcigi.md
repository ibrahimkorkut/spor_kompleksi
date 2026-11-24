# APEX ARENA — Spor ve Performans Kompleksi
## WordPress Özel Tema Sistem Kullanım Kitapçığı (v2.0.0)

- **Canlı Yerel URL:** `http://localhost/spor_kompleksi/wordpress/`
- **GitHub Deposu:** `https://github.com/ibrahimkorkut/spor_kompleksi`
- **Özel Tema Dizini:** `wordpress/wp-content/themes/apex-arena/` (`v2.0.0`)
- **Veritabanı Yedeği:** `teslim_belgeleri/spor_kompleksi_wp.sql` *(API anahtarı güvenlik için maskelenmiştir)*
- **WordPress İçerik Dışa Aktarımı (WXR 1.2):** `teslim_belgeleri/apex-arena-wordpress-export.xml`
- **Envato / WP Yükleme Paketi:** `teslim_belgeleri/apex-arena-envato-theme.zip`
- **Figma Tasarım Sistemi:** https://www.figma.com/design/hlmHQON7ZrmxI3T3yQj4qC

---

## 1. Test, Koç & Yönetim Giriş Bilgileri

| Rol | Giriş URL | Kullanıcı Adı | Şifre | Kapsam |
|---|---|---|---|---|
| **WordPress Yönetici (Admin)** | `http://localhost/spor_kompleksi/wordpress/wp-login.php` | `root` | `korkut2006` | Tam yönetim + Gelir/CSV + Tüm Koçlar |
| **Koç Portalı — Kerem Alkan** | `http://localhost/spor_kompleksi/wordpress/wp-login.php` | `koc_kerem` | `ApexKoc2026!` | Yalnızca kendi 5 öğrencisi (Grafik + Beslenme + 1-1 Mesaj) |
| **Koç Portalı — Elif Karaca** | `http://localhost/spor_kompleksi/wordpress/wp-login.php` | `koc_elif` | `ApexKoc2026!` | Yalnızca kendi 2 öğrencisi (Tam rol izolasyonu) |
| **Hazır Demo Üye (QR & Koç Takibi)** | `http://localhost/spor_kompleksi/wordpress/uyelik/#giris` | `uye_demo` | `ApexUye2026!` | QR Kart + Koç Beslenme Takvimi + 1-1 Sohbet + Antrenman Günlüğü |

> **Rol İzolasyonu Notu:** `apex_coach` rolüne sahip koçlar `/wp-login.php` üzerinden giriş yaptıklarında otomatik olarak **Koç Grafik & Öğrenci Yönetim Paneli** (`admin.php?page=apex-coach-portal`) ekranına yönlendirilir. WP Yönetici ayar/eklenti menüleri gizlenir ve her koç yalnızca kendisine atanmış öğrencileri görebilir ve düzenleyebilir.

---

## 2. Kurulum Adımları (XAMPP / Yerel Sunucu)

1. XAMPP üzerinden **Apache** ve **MySQL (MariaDB)** servislerini başlatın.
2. Tema dosyalarını `wp-content/themes/apex-arena/` klasörüne kopyalayın (veya `teslim_belgeleri/apex-arena-envato-theme.zip` dosyasını **Görünüm → Temalar → Yeni Ekle → Tema Yükle** ekranından yükleyin).
3. `http://localhost/phpmyadmin` üzerinden `spor_kompleksi_wp` (`utf8mb4_unicode_ci`) veritabanını oluşturun ve `teslim_belgeleri/spor_kompleksi_wp.sql` dosyasını içe aktarın.
4. *(İsteğe Bağlı)* Canlı Gemini 2.5 Flash AI sohbeti için **WP Admin → APEX ARENA → AI & Ayarlar** ekranına kendi Google AI Studio Gemini API anahtarınızı girin (anahtar girilmezse yerel Hibrit WP Veritabanı Motoru otomatik devreye girer).
5. Tarayıcıdan `http://localhost/spor_kompleksi/wordpress/` adresini açın.

---

## 3. Öne Çıkan Modüller ve Sayfa Haritası (v2.0.0)

1. **Koça Özel Grafiksel Öğrenci Yönetim Paneli (`/wp-admin/admin.php?page=apex-coach-portal`):**
   - Harici CDN kütüphanesi olmadan %100 saf SVG ile çizilen 5 kartlık koyu analiz ekranı: **Gruplu Sütun Grafiği** (Planlanan vs Tamamlanan Seanslar), **Yumuşak Alan/Çizgi Grafiği** (Hedef İlerleme vs Program Uyumu), **Pasta/Donut Grafik** (Öğrenci Durum Dağılımı), **Program Uyum Barları** ve **Renkli Durum Rozetli Öğrenci Tablosu** (`↑` / `↓` trend göstergeleri).
   - **İnteraktif Koç Çalışma Alanı:** Öğrenci durumu/kilo/hedef + **Koç Önerisi**, **5 Öğünlük Günlük Beslenme Takvimi & Makro Hedefleri** ve **Birebir Canlı Mesajlaşma** — yapılan her güncelleme öğrencinin `/uyelik/?panel=1` paneline anında yansır.
2. **Yönetici Gelir Grafiği, Excel/CSV Rapor Merkezi & Moderasyon (`APEX ARENA` WP Admin):**
   - Salon, PT, Üyelik ve Protein Kafe gelirlerinin canlı MySQL dağılımı; Türkçe Excel uyumlu UTF-8 BOM **tek tıkla CSV indirme** (`Salonlar`, `PT`, `Üyelikler`, `Kafe`, `Yorumlar`) ve **Kafe & Yorumlar** yönetim sayfası.
3. **60/120 FPS Scroll-Driven Mimari Tur Videosu & 3D Dambıl Sahnesi (`/`):**
   - 240 adet HD WebP kareyle kaydırdıkça ilerleyen mimari tur (`scroll-cinema.js`) ve Three.js prosedürel **3D Cast-Iron Dumbbell** (`HAREKETİN YENİ MERKEZİ`).
   - **İnteraktif SVG Tesis Kat Planı (`#kat-plani`):** 6 tıklanabilir tesis bölgesi + canlı teknik özellik ve rezervasyon paneli.
   - **Canlı 5 Yıldızlı Üye Değerlendirmeleri (`#yorumlar`):** Anlık ortalama puan hesaplama ve MySQL'e kaydedilen yorum gönderme formu.
4. **Tesis Olanakları, EV Şarj, Yoğunluk Grafiği & Protein Kafe Sepeti (`/tesis-olanaklari/`):**
   - 240 RFID akıllı dolap, yağmur duş & Fin saunası, 180 araçlık otopark + **6× 22 kW EV Şarj Monitörü**, Hafta İçi / Cumartesi / Pazar **Saatlik Tesis Yoğunluk Tahmin Grafiği** ve makro hesaplı **Antrenman Çıkışı Kafe Ön Sipariş Sepeti**.
5. **Salon Kiralama + `.ics` Takvim İndirme & WhatsApp Maç Daveti (`/salon-kiralama/`, `/pt-egitmenler/`):**
   - 2 FIBA Basketbol + 1 CEV Voleybol salonu, **Lisanslı Hakem (+₺450)**, çakışma koruması; rezervasyon/randevu onayında tek tıkla **Google/Apple/Outlook `.ics` takvim indirme** ve **WhatsApp Maç Daveti Paylaşma**.
6. **Üyelik, Akıllı Paket Bulucu Quiz & Canlı Üye Paneli (`/uyelik/`):**
   - 3 soruluk **Akıllı Üyelik & PT Paketi Bulucu**, detaylı paket karşılaştırma tablosu, kavisli kayar Giriş/Kayıt kartı, **Dijital QR Kartı**, koçtan canlı senkron **Beslenme Takvimi + Öneri + 1-1 Sohbet**, **Haftalık Antrenman & Kalori Günlüğü** ve VKİ hesaplayıcı.
7. **İletişim, Gerçek Google Maps, Animasyonlu WhatsApp Hattı & Gemini 2.5 Flash AI (`/iletisim/`):**
   - İki sütunlu iletişim düzeni, tek tıkla kopyalama + Liquid Glass toast, canlı **"Şu An Açık/Kapalı"** rozeti, gerçek **Ataşehir / İstanbul Google Maps**, çift sonar halkalı animasyonlu **WhatsApp Destek Hattı**, **Gemini 2.5 Flash AI Spor Koçu**, **WCAG 2.2 AA/AAA Erişilebilirlik Merkezi** ve **Yoast SEO (`tr_TR`)**.

---

## 4. WordPress REST API Uç Noktaları (`/wp-json/apex/v1/`)

- `GET /availability?type=court|trainer&id=X&date=YYYY-MM-DD` — Canlı saat müsaitliği
- `POST /book-court` — Basketbol / Voleybol salonu + hakem rezervasyonu
- `POST /book-pt` — Online PT randevu kaydı
- `POST /membership/register` & `POST /membership/login` — Dijital üyelik & oturum
- `POST /booking/cancel` — Üye panelinden rezervasyon iptali
- `POST /contact` — İletişim formu kaydı
- `POST /chat` — Gemini 2.5 Flash + WP Hibrit AI Spor Koçu
- `GET /reviews` & `POST /reviews` — Canlı 5 yıldızlı üye değerlendirmeleri
- `POST /cafe-order` — Apex Protein Kafe antrenman çıkışı ön siparişi
- `POST /workout-log` — Üye haftalık antrenman & kalori kaydı
- `POST /member/message` — Üyeden koçuna 1-1 canlı mesaj
- `POST /coach/save-student` — Koçun kendi öğrencisinin durum, öneri & 5 öğünlük beslenme takvimini kaydetmesi *(rol + sahiplik kontrollü)*
- `POST /coach/message` — Koçtan kendi öğrencisine 1-1 canlı mesaj *(rol + sahiplik kontrollü)*
