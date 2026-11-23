# APEX ARENA — Spor ve Performans Kompleksi
## WordPress Özel Tema Sistem Kullanım Kitapçığı (v1.0.1)

- **Canlı Yerel URL:** `http://localhost/spor_kompleksi/wordpress/`
- **Özel Tema Dizini:** `wordpress/wp-content/themes/apex-arena/`
- **Veritabanı:** `spor_kompleksi_wp` (`teslim_belgeleri/spor_kompleksi_wp.sql`)
- **WordPress Dışa Aktarım (WXR 1.2):** `teslim_belgeleri/apex-arena-wordpress-export.xml` (40 öğe)
- **Tema Paketi (Envato / WP Upload ZIP):** `teslim_belgeleri/apex-arena-envato-theme.zip`
- **Figma Tasarım Dosyası:** https://www.figma.com/design/hlmHQON7ZrmxI3T3yQj4qC
- **Git Deposu:** `apex-arena-git-repo/`

---

## 1. Test & Yönetim Giriş Bilgileri

| Rol | Giriş URL | Kullanıcı Adı | Şifre |
|---|---|---|---|
| **WordPress Yönetici (Admin)** | `http://localhost/spor_kompleksi/wordpress/wp-admin/` | `root` | `korkut2006` |
| **Hazır Demo Üye (Üye Paneli & QR)** | `http://localhost/spor_kompleksi/wordpress/uyelik/#giris` | `uye_demo` | `ApexUye2026!` |

---

## 2. Kurulum Adımları (XAMPP)

1. XAMPP üzerinden **Apache** ve **MySQL** servislerini başlatın.
2. `wordpress` klasörünü `C:\xampp\htdocs\spor_kompleksi\wordpress\` yoluna kopyalayın.
3. `http://localhost/phpmyadmin` üzerinden `spor_kompleksi_wp` (`utf8mb4_unicode_ci`) veritabanını oluşturun ve `teslim_belgeleri/spor_kompleksi_wp.sql` dosyasını içe aktarın.
4. Tarayıcıdan `http://localhost/spor_kompleksi/wordpress/` adresini açın.

---

## 3. Modüller ve Sayfa Haritası (100 Puan Değerlendirme Kapsamı)

1. **PT Eğitmenler (`/pt-egitmenler/` — 30 Puan):** Bireysel & Grupsal antrenmanlar, 6 PT paketi ve seans ücretleri, eğitim kapsam/içerik detayları, haftalık eğitim günleri tablosu, antrenör kart seçimi ve canlı MySQL online randevu formu.
2. **Antrenörler (`/antrenorler/` ve `/antrenor/{slug}/` — 5 Puan):** 4 sertifikalı antrenör (Kerem Alkan, Selin Yılmaz, Mert Demir, Elif Karaca), fotoğraf, uzmanlık alanı, özgeçmiş, tıklanabilir Sertifika Doğrulama Modalı ve canlı haftalık online takvim.
3. **Salon ve Kiralama (`/salon-kiralama/` — 5 Puan):** 2 Adet Basketbol Salonu (`Arena Basketbol 1 FIBA`, `Arena Basketbol 2 Performans`) + 1 Adet Voleybol Salonu (`Arena Voleybol Pro CEV`), fotoğraf Lightbox galerisi, **Lisanslı Hakem Ayarlama Hizmeti (+₺450)** ve çakışma korumalı canlı saat rezervasyonu.
4. **Tesis Olanakları (`/tesis-olanaklari/` — 5 Puan):** 240 akıllı dolaplı Soyunma Odası & Yağmur Duş & Sauna, canlı doluluk göstergeli 180 araçlık Kapalı Otopark + 6 EV Şarj, makro hesaplı ve filtreli Apex Protein Kafe Menüsü.
5. **Üyelik İşlemleri (`/uyelik/` — 10 Puan):** Aylık/Yıllık (%20 indirimli) paketler (`APEX CLASSIC`, `APEX PRO`, `APEX ELITE + PT`), KVKK/sözleşme onaylı online kayıt, Üye Girişi, **Dijital Turnike/Otopark QR Kartı**, rezervasyon listesi/iptali ve VKİ & Kalori Hesaplayıcı.
6. **Erişilebilirlik İşlemleri (WCAG 2.2 AA — 10 Puan):** Sol alttaki Erişilebilirlik Merkezi üzerinden Yazı Boyutu (%85–%145), Yüksek Kontrast AAA, Aydınlık Mod, Disleksi Dostu *Lexend* Fontu, Link Vurgula, Animasyon Durdur, Gri Tonlama, Okuma Kılavuzu, Büyük İmleç, Türkçe Sesli Okuma ve `/erisilebilirlik-beyani/`.
7. **Hibrit AI Spor Koçu (Chatbot):** WP Yönetim Paneli → `APEX ARENA → AI & Ayarlar` ekranından Google Gemini veya OpenAI API anahtarı girildiğinde canlı LLM + RAG; anahtar girilmediğinde/offline ortamda canlı WP veritabanını sorgulayan Türkçe akıllı asistan ve anlık VKİ analiz motoru.
8. **Kurumsal, Yasal ve Duyuru Sayfaları:** `/duyurular/`, `/iletisim/`, `/sss/`, `/cerez-politikasi/` (+ İnteraktif Çerez Tercih Banner & Modalı), `/kvkk-aydinlatma-metni/`, `/gizlilik-politikasi/`, `/uyelik-sozlesmesi/`.

---

## 4. WordPress Yönetim Paneli ve REST API

- **WP Admin Menüsü:** `APEX ARENA` → Genel Bakış, Salon Rezervasyonları (Onayla/İptal + Hakem Rozeti), PT Randevuları, Üyelikler & İletişim Mesajları, AI & Ayarlar.
- **REST API Uç Noktaları (`/wp-json/apex/v1/`):**
  - `GET /availability?type=court|trainer&id=X&date=YYYY-MM-DD`
  - `POST /book-court`
  - `POST /book-pt`
  - `POST /membership/register` & `POST /membership/login`
  - `POST /booking/cancel`
  - `POST /contact`
  - `POST /chat`

---

## 5. GitHub & Envato Paketleme

`apex-arena-git-repo/` klasörünü GitHub hesabınıza göndermek için:

```bash
cd C:\xampp\htdocs\spor_kompleksi\apex-arena-git-repo
git remote add origin https://github.com/KULLANICI_ADIN/apex-arena-wordpress-theme.git
git push -u origin main
```
