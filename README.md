# APEX ARENA — Spor ve Performans Kompleksi (WordPress Özel Tema & Ekosistem)

![APEX ARENA Ana Sayfa — 60/120 FPS Scroll Mimari Tur & Animasyonlu WhatsApp Hattı](teslim_belgeleri/ekran_goruntuleri/16_whatsapp_hero_v170.png)

> **Modern spor kompleksleri için sıfırdan geliştirilmiş, tam entegre özel WordPress teması ve çok rollü yönetim ekosistemi (`v2.0.0`).**  
> 60/120 FPS kaydırmayla oynayan mimari tur videosu, Three.js prosedürel 3D dambıl sahnesi, **Koça Özel Grafiksel Öğrenci Yönetim Paneli (5 SVG Grafik + Beslenme Takvimi + 1-1 Mesajlaşma)**, interaktif tesis kat planı, makro hesaplı Protein Kafe ön sipariş sepeti, canlı **Gemini 2.5 Flash AI Spor Koçu**, lisanslı hakem + `.ics` takvim / WhatsApp maç davetli salon kiralama, **Excel/CSV Rapor Merkezi**, gerçek **Google Maps** ve **WCAG 2.2 AA/AAA** erişilebilirlik merkezini tek çatı altında sunar.

---

## Projenin Amacı

**APEX ARENA**, bir spor ve performans kompleksinin 3 farklı kullanıcı rolünü tek bir özel WordPress + MySQL mimarisi üzerinde kesintisiz ve tam responsive (`1440px` Masaüstü · `768px` Tablet · `390px` Mobil) biçimde yönetmek amacıyla geliştirilmiştir:
1. **Ziyaretçiler & Üyeler:** İnteraktif kat planıyla tesisi keşfeder, 3 soruluk akıllı quizle paketini bulur, salon/PT rezervasyonu yapar (`.ics` takvime ekler & WhatsApp maç daveti paylaşır), antrenman çıkışı Protein Kafe siparişi verir, dijital QR kartını kullanır ve kendi koçunun hazırladığı canlı beslenme takvimiyle 1-1 mesajlaşır.
2. **Koçlar (`apex_coach` Özel Rolü):** Sadece kendi öğrencilerini görebildikleri izole grafiksel panelden seans/ilerleme/uyum grafiklerini takip eder, öğrenci durumuna göre 5 öğünlük beslenme takvimi + koç önerisi yayınlar ve birebir mesajlaşır.
3. **Yöneticiler (`root`):** Canlı MySQL gelir dağılımını izler, tek tıkla Türkçe Excel uyumlu UTF-8 BOM CSV raporları indirir, kafe siparişlerini ve üye yorumlarını yönetir, tüm koçların portföylerini denetler.

---

## v2.0.0 Yeni Modüller & Ekran Görüntüleri

### 1. Koça Özel Grafiksel Öğrenci Yönetim Paneli (`inc/coach-portal.php`)
- **Tam Rol İzolasyonu (`apex_coach`):** Koçlar `/wp-login.php` üzerinden giriş yaptığında doğrudan **Koç Grafik & Öğrenci Yönetim Paneli**'ne (`admin.php?page=apex-coach-portal`) yönlendirilir. WordPress'in Eklentiler/Ayarlar/Kullanıcılar menüleri gizlenir ve her koç **yalnızca kendi atanmış öğrencilerini** görür ve düzenleyebilir *(REST API düzeyinde de `coach_user_id` sahiplik kontrolü uygulanır)*.
- **5 Kartlık Koyu SVG Analiz Ekranı (0 Harici CDN):**
  1. **Seans Karşılaştırması (Gruplu Sütun Grafiği):** Planlanan vs Tamamlanan PT seansları
  2. **Gelişim & Uyum Trendi (Yumuşak Alan Grafiği):** Hedef ilerleme vs Beslenme/Program uyumu
  3. **Öğrenci Durum Dağılımı (Pasta / Donut Grafiği):** *Hedefte, Dikkat, Yeni Başladı, Yoğun Takip*
  4. **Program Uyum Oranları (İlerleme Barları):** Öğrenci bazlı canlı uyum yüzdeleri
  5. **Öğrenci Durum Tablosu:** Renkli durum rozetleri, `↑` / `↓` trend göstergeleri ve tek tıkla düzenleme
- **Canlı Beslenme Takvimi & Birebir Mesajlaşma:** Koçun kaydettiği **5 öğünlük beslenme takvimi + makrolar**, **koç önerisi** ve **birebir mesajlar** öğrencinin `/uyelik/?panel=1` paneline anında senkronize olur.

![Koç Grafik & Öğrenci Yönetim Paneli — Rol İzolasyonlu 5 Kartlı SVG Analiz](teslim_belgeleri/ekran_goruntuleri/22_koc_grafik_paneli_v200.png)

| Üye Panelinde Canlı Koç Beslenme Takvimi & 1-1 Sohbet | Admin Operasyon Merkezi — Canlı Gelir & CSV Raporları |
| :---: | :---: |
| ![Üye Beslenme ve Koç Sohbet](teslim_belgeleri/ekran_goruntuleri/24_uye_beslenme_koc_sohbet_v200.png) | ![Admin Gelir ve CSV Raporları](teslim_belgeleri/ekran_goruntuleri/26_admin_gelir_csv_v200.png) |

### 2. İnteraktif Tesis Kat Planı, Canlı 5 Yıldızlı Yorumlar & Protein Kafe Ön Sipariş Sepeti
- **İnteraktif SVG Mimari Kat Planı (`/#kat-plani`):** 6 tıklanabilir kompleks bölgesi (*Basketbol 1, Basketbol 2, Voleybol Pro, PT & Kuvvet, Soyunma & Sauna, Kafe & Otopark*) ile anlık teknik özellik ve doğrudan rezervasyon bağlantısı.
- **Canlı 5 Yıldızlı Üye Değerlendirmeleri (`/#yorumlar`):** Yıldız seçici, dinamik ortalama puan güncellemesi ve MySQL'e kaydedilen üye yorum formu.
- **EV Şarj Monitörü, Saatlik Yoğunluk Grafiği & Kafe Sepeti (`/tesis-olanaklari/`):** 6 adet 22 kW EV şarj istasyonu anlık durumu, Hafta İçi / Cumartesi / Pazar saatlik yoğunluk tahmin barları ve anlık kalori/protein makro toplamlı **Antrenman Çıkışı Kafe Ön Sipariş Sepeti**.

| İnteraktif SVG Tesis Kat Planı (`/#kat-plani`) | Protein Kafe Makro Hesaplı Ön Sipariş Sepeti (`/tesis-olanaklari/#kafe`) |
| :---: | :---: |
| ![İnteraktif Kat Planı](teslim_belgeleri/ekran_goruntuleri/23_interaktif_kat_plani_v200.png) | ![Kafe Ön Sipariş Sepeti](teslim_belgeleri/ekran_goruntuleri/25_kafe_on_siparis_sepeti_v200.png) |

### 3. `.ics` Takvim İndirme, WhatsApp Maç Daveti & Akıllı Paket Bulucu Quiz
- **Rezervasyon Onay Aksiyonları (`/salon-kiralama/`, `/pt-egitmenler/`, `/uyelik/`):** Her salon kiralaması ve PT randevusu sonrası tek tıkla **Google / Apple / Outlook `.ics` takvim dosyası indirme** ve otomatik metinli **💬 WhatsApp Maç Daveti Paylaşma** butonu.
- **3 Soruluk Akıllı Paket & PT Bulucu (`/uyelik/#paket-bulucu`):** Hedef, haftalık sıklık ve koçluk tercihine göre en uygun paketi ve antrenörü önerip tek tıkla kayıt formuna aktarır; altında detaylı **Paket Karşılaştırma Tablosu** yer alır.
- **Üye Haftalık Antrenman & Kalori Günlüğü (`/uyelik/#antrenman-takip`):** Üyeler günlük antrenman ve yaktıkları kaloriyi anlık kaydedip grafiksel olarak takip eder; kayıt koçun ilerleme paneline de yansır.

---

## Temel Mimari & Performans Mühendisliği (`v1.0.0` → `v2.0.0`)

- **60/120 FPS Scroll-Driven Mimari Tur Videosu (`assets/js/scroll-cinema.js`):** 240 adet HD WebP kare, off-main-thread GPU `createImageBitmap`, O(1) LRU önbellek, sıfır zorunlu layout ve mobilde %50 daha az veri tüketen adaptif kare adımı (`frameStep: 2`).
- **3D Cast-Iron Spin-Lock Dumbbell (`assets/js/motion-3d.js`):** Three.js prosedürel CAD dambıl, **`HAREKETİN YENİ MERKEZİ`** dev kinetik tipografisi, lazy WebGL yükleme ve görünüm dışı %0 CPU/GPU uyku modu.
- **Canlı Gemini 2.5 Flash AI Spor Koçu & Animasyonlu WhatsApp Destek Hattı:** Tüm sayfalarda çift sonar halkalı, parlama geçişli WhatsApp hattı + veritabanı destekli canlı Gemini 2.5 Flash AI asistanı.
- **İletişim + Gerçek Google Maps (`/iletisim/`):** İki sütunlu **Bize Yazın** + 5 kartlı **İletişim Bilgileri** (tek tıkla kopyalama + Liquid Glass toast + canlı **Şu An Açık/Kapalı** rozeti) + gerçek Ataşehir / İstanbul Google Maps haritası.
- **WCAG 2.2 AA/AAA Erişilebilirlik, KVKK & Yoast SEO (`tr_TR`):** Yazı boyutu, yüksek kontrast, aydınlık mod, *Lexend* disleksi fontu, sesli okuma, KVKK/çerez modalı, üst GPU scroll ilerleme çizgisi, SVG halkalı Yukarı Çık butonu ve `Ctrl+K` arama kısayolu.

![3D Cast-Iron Dumbbell — HAREKETİN YENİ MERKEZİ](teslim_belgeleri/ekran_goruntuleri/17_3d_dumbbell_hareketin_merkezi_v170.png)

---

## Demo, Koç & Yönetim Giriş Bilgileri

| Rol | Giriş Adresi | Kullanıcı Adı | Şifre | Açıklama |
| :--- | :--- | :--- | :--- | :--- |
| **WordPress Yönetici (Admin)** | `/wp-login.php` | `root` | `korkut2006` | Tam yönetim, Gelir/CSV, Kafe/Yorumlar, Tüm Koçlar |
| **Koç Portalı (Kerem Alkan)** | `/wp-login.php` | `koc_kerem` | `ApexKoc2026!` | Yalnızca kendi 5 öğrencisi (Grafik + Beslenme + 1-1 Sohbet) |
| **Koç Portalı (Elif Karaca)** | `/wp-login.php` | `koc_elif` | `ApexKoc2026!` | Yalnızca kendi 2 öğrencisi (Rol izolasyonlu) |
| **Demo Üye (QR & Koç Takibi)** | `/uyelik/#giris` | `uye_demo` | `ApexUye2026!` | QR Kart + Koç Beslenme Takvimi + 1-1 Mesaj + Antrenman Günlüğü |

---

## Kurulum ve Çalıştırma Rehberi (XAMPP)

### Yöntem A — Tam Veritabanı ile Birebir Kurulum (Önerilen)
1. [XAMPP](https://www.apachefriends.org/) üzerinden **Apache** ve **MySQL (MariaDB)** servislerini başlatın.
2. Temiz bir WordPress kurulumunu `C:\xampp\htdocs\spor_kompleksi\wordpress\` dizinine yerleştirin.
3. Bu repodaki `apex-arena/` klasörünü `wordpress/wp-content/themes/apex-arena/` içine kopyalayın (veya `teslim_belgeleri/apex-arena-envato-theme.zip` dosyasını WP Admin → **Görünüm → Temalar → Yeni Ekle → Tema Yükle** üzerinden yükleyin).
4. `http://localhost/phpmyadmin` adresinde **`spor_kompleksi_wp`** (`utf8mb4_unicode_ci`) veritabanını oluşturun ve `teslim_belgeleri/spor_kompleksi_wp.sql` dosyasını içe aktarın.
5. Tarayıcıdan `http://localhost/spor_kompleksi/wordpress/` adresini açın.

### Yöntem B — Sadece Tema Kurulumu
1. `teslim_belgeleri/apex-arena-envato-theme.zip` paketini WordPress panelinden yükleyip **Etkinleştir** butonuna tıklayın (11 özel MySQL tablosu, `apex_coach` rolü, demo koç/öğrenci/beslenme/yorum/kafe verileri ve 4 Custom Post Type otomatik oluşturulur).
2. İsteğe bağlı olarak **Araçlar → İçe Aktar → WordPress** üzerinden `teslim_belgeleri/apex-arena-wordpress-export.xml` dosyasını yükleyebilirsiniz.
3. Canlı Gemini 2.5 Flash AI sohbeti için **WP Admin → APEX ARENA → AI & Ayarlar** ekranına kendi Google AI Studio Gemini API anahtarınızı girebilirsiniz *(güvenlik nedeniyle paylaşılan SQL yedeğindeki API anahtarı maskelenmiştir; anahtar girilmezse yerel Hibrit WP Veritabanı Motoru otomatik çalışır)*.

---

## WordPress REST API Uç Noktaları (`/wp-json/apex/v1/`)

| Metod | Uç Nokta | Açıklama |
| :--- | :--- | :--- |
| `GET` | `/availability?type=court\|trainer&id=X&date=YYYY-MM-DD` | Salon veya antrenör canlı saat müsaitliği |
| `POST` | `/book-court` | Basketbol / Voleybol salonu + lisanslı hakem rezervasyonu |
| `POST` | `/book-pt` | Online PT antrenman randevu kaydı |
| `POST` | `/membership/register` & `/membership/login` | Dijital üyelik kaydı, oturum açma ve QR üretimi |
| `POST` | `/booking/cancel` | Üye panelinden aktif rezervasyon iptali |
| `POST` | `/contact` | İletişim formu veritabanı kaydı |
| `POST` | `/chat` | Gemini 2.5 Flash + WP Hibrit AI Spor Koçu |
| `GET` / `POST` | `/reviews` | Canlı 5 yıldızlı üye değerlendirmeleri |
| `POST` | `/cafe-order` | Apex Protein Kafe antrenman çıkışı ön siparişi |
| `POST` | `/workout-log` | Üye haftalık antrenman & kalori günlüğü kaydı |
| `POST` | `/member/message` | Üyeden koçuna 1-1 canlı mesaj gönderimi |
| `POST` | `/coach/save-student` | Koçun kendi öğrencisinin durum, öneri & beslenme takvimini kaydetmesi |
| `POST` | `/coach/message` | Koçtan kendi öğrencisine 1-1 canlı mesaj gönderimi |

---

## Depo ve Teslim Dosyaları Yapısı

```text
spor_kompleksi/
├── README.md                              # Proje dokümantasyonu ve kullanım rehberi (v2.0.0)
├── CHANGELOG.md                           # Sürüm geçmişi (v1.0.0 → v2.0.0)
├── LICENSE                                # GNU GPL v2+ Lisansı
├── apex-arena/                            # WordPress Özel Tema Kaynak Kodları (v2.0.0)
│   ├── style.css, functions.php, theme.json
│   ├── front-page.php, header.php, footer.php
│   ├── page-*.php (pt-egitmenler, antrenorler, salon-kiralama, tesis-olanaklari, uyelik, duyurular, iletisim, sss)
│   ├── inc/ (database, post-types, helpers, ai-chatbot, api-endpoints, admin-panel, coach-portal, admin-login-theme, customizer)
│   ├── template-parts/ (accessibility-widget, chatbot-widget, cookie-banner)
│   └── assets/ (css/, js/, images/, video-frames/)
└── teslim_belgeleri/                      # Akademik & Kurumsal Teslim Paketi
    ├── apex-arena-envato-theme.zip        # Kuruluma hazır WordPress özel tema ZIP paketi (v2.0.0)
    ├── spor_kompleksi_wp.sql              # Güncel, 11 özel tablolu & API anahtarı temizlenmiş MySQL yedeği
    ├── apex-arena-wordpress-export.xml    # WordPress WXR 1.2 içerik dışa aktarımı
    ├── Sistem_Kullanim_Kitapcigi.md       # Sistem kullanım kitapçığı (Markdown)
    ├── Sistem_Kullanim_Kitapcigi.html     # Yazdırılabilir kullanım kitapçığı (HTML/PDF)
    └── ekran_goruntuleri/                 # Masaüstü (1440px), Koç Portalı & Mobil (390px) ekran görüntüleri
```

---

## Responsive Doğrulama (Masaüstü · Tablet · Mobil)

Tüm sayfalar **Masaüstü (`1440×900`)**, **Tablet (`768×1024`)** ve **Mobil (`390×844`)** kırılımlarında `0px` yatay taşma ve `0` yerel JS/PHP hatasıyla test edilmiştir.

| Mobil EV Şarj & Otopark (`390×844`) | Mobil Canlı Üye Yorumları (`390×844`) |
| :---: | :---: |
| ![Mobil EV Şarj](teslim_belgeleri/ekran_goruntuleri/28_mobil_ev_otopark_v200.png) | ![Mobil Yorumlar](teslim_belgeleri/ekran_goruntuleri/29_mobil_yorumlar_v200.png) |

---

**Geliştirici:** İbrahim Korkut · **Sürüm:** `v2.0.0` · **Lisans:** GPL v2 or later
