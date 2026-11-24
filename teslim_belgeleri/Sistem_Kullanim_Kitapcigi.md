# APEX ARENA — Spor ve Performans Kompleksi
## WordPress Özel Tema Sistem Kullanım Kitapçığı (v1.7.1)

- **Canlı Yerel URL:** `http://localhost/spor_kompleksi/wordpress/`
- **GitHub Deposu:** `https://github.com/ibrahimkorkut/spor_kompleksi`
- **Özel Tema Dizini:** `wordpress/wp-content/themes/apex-arena/` (v1.7.1)
- **Veritabanı Yedeği:** `teslim_belgeleri/spor_kompleksi_wp.sql` *(API anahtarı güvenlik için maskelenmiştir)*
- **WordPress İçerik Dışa Aktarımı (WXR 1.2):** `teslim_belgeleri/apex-arena-wordpress-export.xml`
- **Envato / WP Yükleme Paketi:** `teslim_belgeleri/apex-arena-envato-theme.zip`
- **Figma Tasarım Sistemi:** https://www.figma.com/design/hlmHQON7ZrmxI3T3yQj4qC

---

## 1. Test & Yönetim Giriş Bilgileri

| Rol | Giriş URL | Kullanıcı Adı | Şifre |
|---|---|---|---|
| **WordPress Yönetici (Admin)** | `http://localhost/spor_kompleksi/wordpress/wp-login.php` | `root` | `korkut2006` |
| **Hazır Demo Üye (Üye Paneli & QR)** | `http://localhost/spor_kompleksi/wordpress/uyelik/#giris` | `uye_demo` | `ApexUye2026!` |

> **Not:** `wp-login.php` sayfası tema içinde özel koyu kurumsal **APEX ARENA — Yönetim Paneli Girişi** tasarımıyla (`inc/admin-login-theme.php`) özelleştirilmiştir.

---

## 2. Kurulum Adımları (XAMPP / Yerel Sunucu)

1. XAMPP üzerinden **Apache** ve **MySQL (MariaDB)** servislerini başlatın.
2. Tema dosyalarını `wp-content/themes/apex-arena/` klasörüne kopyalayın (veya `teslim_belgeleri/apex-arena-envato-theme.zip` dosyasını **Görünüm → Temalar → Yeni Ekle → Tema Yükle** ekranından yükleyin).
3. `http://localhost/phpmyadmin` üzerinden `spor_kompleksi_wp` (`utf8mb4_unicode_ci`) veritabanını oluşturun ve `teslim_belgeleri/spor_kompleksi_wp.sql` dosyasını içe aktarın.
4. *(İsteğe Bağlı)* Canlı Gemini AI sohbeti için **WP Admin → APEX ARENA → AI & Ayarlar** ekranına kendi Google AI Studio Gemini API anahtarınızı girin (anahtar girilmezse yerel Hibrit WP Veritabanı Motoru otomatik devreye girer).
5. Tarayıcıdan `http://localhost/spor_kompleksi/wordpress/` adresini açın.

---

## 3. Öne Çıkan Modüller ve Sayfa Haritası (v1.7.1)

1. **60/120 FPS Scroll-Driven Mimari Tur Videosu & 3D Dambıl Sahnesi (`/`):**
   - 240 adet HD WebP kareden oluşan, kaydırdıkça kare kare ilerleyen mimari kompleks turu (`assets/js/scroll-cinema.js`); off-main-thread GPU `createImageBitmap`, O(1) LRU önbellek, sıfır zorunlu layout ve mobil cihazlarda %50 daha az veri tüketen adaptif kare adımı (`frameStep: 2`).
   - Three.js ile prosedürel olarak modellenmiş **3D Cast-Iron Spin-Lock Dumbbell** (`assets/js/motion-3d.js`); `HAREKETİN YENİ MERKEZİ` dev kinetik tipografisi, lazy WebGL başlatma ve görünüm dışındayken %0 CPU/GPU tüketen otomatik uyku modu.
2. **PT Eğitmenler (`/pt-egitmenler/`):** Bireysel & Grupsal antrenman kategorileri, 6 PT paketi ve seans ücretleri, haftalık eğitim programı tablosu, antrenör seçimi ve canlı MySQL online randevu formu.
3. **Antrenörler (`/antrenorler/` ve `/antrenor/{slug}/`):** 4 sertifikalı antrenör, tıklanabilir **Resmi Sertifika Doğrulama Modalı** ve mobil yatay kaydırmalı canlı haftalık online takvim.
4. **Salon ve Kiralama (`/salon-kiralama/`):** 2 FIBA Basketbol + 1 CEV Voleybol salonu, fotoğraf Lightbox galerisi, **Lisanslı Hakem Ayarlama Hizmeti (+₺450)** ve çakışma korumalı canlı saat rezervasyonu.
5. **Tesis Olanakları (`/tesis-olanaklari/`):** 240 RFID akıllı dolap, yağmur duş & Fin saunası, 180 araçlık kapalı otopark + 6 EV şarj istasyonu ve kategori filtreli **Apex Protein Kafe Menüsü**.
6. **Üyelik İşlemleri (`/uyelik/`):** Aylık/Yıllık (%20 indirimli) fiyat geçişi, çift yönlü kavisli kayar **Üye Girişi / Üye Ol** kartı, **Dijital Turnike/Otopark QR Kartı**, rezervasyon iptali ve anlık **VKİ & Günlük Kalori Hesaplayıcı**.
7. **İletişim, Gerçek Google Maps & WhatsApp Hattı (`/iletisim/`):** İki sütunlu **Bize Yazın** + 5 kartlı **İletişim Bilgileri** düzeni, tek tıkla panoya kopyalama + toast bildirimi, gerçek zamanlı **"Şu An Açık / Kapalı"** tesis durum rozeti, gerçek etkileşimli **Ataşehir / İstanbul Google Maps** haritası ve tüm sayfalarda sağ alt köşede yüzen **WhatsApp Destek Hattı** butonu.
8. **Canlı Gemini 2.5 Flash AI Spor Koçu:** Doğal Türkçe sohbet edebilen, salon/PT/üyelik/VKİ verilerini canlı WordPress veritabanından besleyen ve scrollbar kirliliği olmayan yüzen AI asistanı.
9. **WCAG 2.2 AA/AAA Erişilebilirlik Merkezi & KVKK/Çerez Yönetimi:** Yazı boyutu, yüksek kontrast AAA, aydınlık mod, disleksi dostu *Lexend* yazı tipi, okuma kılavuzu, büyük imleç, Türkçe sesli okuma, KVKK/çerez tercih modalı ve resmi **Yoast SEO (`tr_TR`)** + `/sitemap_index.xml`.
10. **Mikro-Etkileşim Detayları (v1.7.1):** Üst GPU okuma/scroll ilerleme çizgisi, sol altta canlı SVG ilerleme halkalı **Akıllı Yukarı Çık** butonu, **`Ctrl+K` / `/`** klavye arama kısayolu, masaüstü kart imleç ışığı (pointer spotlight) ve GPU scroll-reveal geçişleri.

---

## 4. WordPress REST API Uç Noktaları (`/wp-json/apex/v1/`)

- `GET /availability?type=court|trainer&id=X&date=YYYY-MM-DD` — Canlı saat müsaitliği
- `POST /book-court` — Basketbol / Voleybol salonu + hakem rezervasyonu
- `POST /book-pt` — Online PT randevu kaydı
- `POST /membership/register` & `POST /membership/login` — Dijital üyelik & oturum
- `POST /booking/cancel` — Üye panelinden rezervasyon iptali
- `POST /contact` — İletişim formu kaydı
- `POST /chat` — Gemini 2.5 Flash + WP Hibrit AI Spor Koçu
