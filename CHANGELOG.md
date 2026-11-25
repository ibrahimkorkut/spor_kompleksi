# Changelog — APEX ARENA WordPress Theme

## [2.2.1] — 2026-11-25
- **Editoryal Antrenör Portre Galerisi (`front-page.php`, `assets/css/main.css`, `assets/images/trainer-mert.jpg`):** Ana sayfadaki "Antrenörlerimizle Tanış" bölümü klasik şablon kutu kartlarından çıkarılarak asimetrik dergi/lookbook ritmine sahip tam çerçeve (full-bleed) editoryal portre galerisine dönüştürüldü; üst indeks numaraları (`01`–`04`), hover ile renklenen sinematik film tonlaması, genişleyen kırmızı editoryal çizgi, `Profil & Takvim ↗` alt bağlantısı ve Mert Demir için voleybol salonunda doğal 35mm portre fotoğrafı eklendi.

## [2.2.0] — 2026-11-25
- **İnteraktif 3D Mimari Kesit Spor Kompleksi (`assets/js/complex-3d.js`, `front-page.php`):** Ana sayfadaki `#kat-plani` bölümü, sıfır CDN (yerel Three.js WebGL) ile çalışan tam izometrik **3D Mimari Kesit (Dollhouse Cutaway)** spor kompleksi maketine yükseltildi:
  - **6 Detaylı 3D Oda & Alan:** FIBA akçaağaç parkeli Basketbol 1 (çift 3D pota, cam panya, canlı `84 : 79` LED skorboard), CEV Taraflex Voleybol Pro (3D file, hakem kürsüsü, top arabası), traverten merkez lobi (heykelsi 3D sarmal merdiven, QR turnikeler, EV şarjlı araç bölmesi), PT & Kuvvet Stüdyosu (çift olimpik power squat rack, yeşil sprint turf pisti, koşu bantları, dambıl rafı), RFID Soyunma + Cam Yağmur Duş + 85°C Sedir Fin Saunası (sıcak amber ışık & yanan sauna taşları) ve Apex Protein Kafe & Lounge (mermer barista barı, tabureler, ahşap masalar, iç mekan bitkileri).
  - **Mouse Hover ile Oda İçine 3D Kamera Yaklaşması:** Farenizi 3D mimarideki herhangi bir odanın veya hızlı bölge çipinin üzerine getirdiğinizde kamera akıcı üstel sönümleme ile doğrudan o odanın içine yaklaşır, zemin çerçeve ışığı yanar ve sağdaki teknik detay paneli anlık güncellenir.
  - **Tıklayarak Kilitleme & Sürükleyerek Döndürme:** Herhangi bir odaya tıklayarak kamerayı kilitleyebilir (`ESC` veya `← Genel Bakış` ile çıkış), sahneyi sürükleyerek açıyı değiştirebilir veya `⟳ 3D Tur` modunu açabilirsiniz.

## [2.1.0] — 2026-11-25
- **Gmail SMTP, Kayıt Hoş Geldin & Otomatik Hatırlatma Motoru (`inc/mailer.php`):** Sıfır eklenti bağımlılığıyla WordPress dahili PHPMailer motoru Gmail SMTP (`smtp.gmail.com:587` TLS / `465` SSL + 16 haneli Google Uygulama Şifresi) ile entegre edildi:
  - **Otomatik Kayıt Hoş Geldin & Dijital QR Kart E-postası:** `/uyelik/` üzerinden yeni üye kaydı yapıldığında üyenin e-posta adresine karanlık/crimson APEX ARENA tasarımlı, QR kodlu ve paket özetli Hoş Geldin e-postası gönderilir.
  - **Salon Kiralama & PT Randevu Onay E-postaları:** Her salon ve PT rezervasyonu sonrası onay kodu, tarih/saat ve hakem detaylarını içeren HTML e-posta otomatik üretilir.
  - **Saatlik WP-Cron Otomatik Hatırlatma Sistemi:** Yaklaşan salon/PT randevularına 24 saat kala ve bitimine 7 gün kalan aktif üyeliklere otomatik hatırlatma e-postaları gönderilir (mükerrer gönderim `ref_key` ile engellenir).
  - **Obsidian Neon E-posta & Hatırlatma Merkezi (`WP Admin → APEX ARENA → E-posta & Hatırlatma`):** Gmail SMTP & Uygulama Şifresi kurulumu, tek tıkla Test Maili gönderimi, seçili üyeye anlık hatırlatma, yaklaşan randevuları manuel tarama, CSV dışa aktarım ve tüm gönderimlerin canlı HTML `iframe` önizlemesi (`wp_apex_email_logs` tablosu).
  - **Üye Paneli E-posta Bildirimleri (`page-uyelik.php`, `assets/js/booking.js`):** Üyeler hatırlatma tercihlerini açıp kapatabilir, tek tıkla Hoş Geldin/QR veya Antrenman Hatırlatma mailini kendilerine gönderebilir ve son e-postalarını canlı önizleyebilir (`POST /member/mail-action`, `GET /member/emails`).

## [2.0.2] — 2026-11-25
- **Apple Fitness Neon Glow Coach Portal (`inc/coach-portal.php`):** Upgraded the 5-card Coach Portal to a dynamic, obsidian-matte neon glow telemetry studio:
  - Replaced static pie chart with 3 concentric **Apple Fitness Glowing Activity Rings** (Neon Pink Progress, Electric Lime Sessions, Electric Cyan Nutrition) featuring orbiting 3D luminous tip orbs, `rAF` sweep animations, 3-column tabular metrics, and a live `Öğrenci` / `Genel` mode toggle.
  - Upgraded Bar Chart (`Seans Performansı`) and Dual-Curve Area Chart (`Gelişim & Beslenme Trendi`) with interactive floating glass hover tooltips, full Turkish first names, neon glow spotlighting on the active student, and click-to-select synchronization across all 5 cards.
  - Upgraded `Program Uyumu` bars with luminous neon tip orbs + click selection, added instant status filter pills (`Tümü`, `Hedefte`, `Dikkat`, `Yeni`, `Takip`) to `Öğrenci Takip Listesi`, and enabled zero-reload real-time chart & ring re-animation on save.

## [2.0.1] — 2026-11-25
- **WhatsApp Green Ghost-Ripple Loop:** Upgraded floating WhatsApp button with 3 staggered translucent green ghost circles expanding continuously (`1.0× → 2.12×`) while fading smoothly from `0.82 → 0` opacity in an infinite loop with `0px` mobile horizontal overflow.
- **Footer GitHub Repository Link:** Pointed `GitHub Tema Deposu ↗` and `Theme URI` directly to `https://github.com/ibrahimkorkut/spor_kompleksi`.

## [2.0.0] — 2026-11-25
- **Coach-Only Graphical Management Portal (`inc/coach-portal.php`):** Added custom `apex_coach` WordPress role (`koc_kerem`, `koc_elif`) with strict student isolation (each coach sees & manages ONLY their assigned students), automatic login redirect to `apex-coach-portal`, hidden admin menus, 5 dark zero-CDN SVG analytics cards (Grouped Bar, Smooth Area, Pie/Donut, Program Adherence Bars, Student Status Table with pills & trend arrows), plus an interactive Coach Studio to update status/advice, edit a 5-meal Nutrition Calendar + macros, and send live 1-on-1 messages synced immediately to the student's `/uyelik/?panel=1` portal.
- **Admin Revenue Analytics, CSV Exports & Moderation (`inc/admin-panel.php`):** Added live MySQL revenue distribution bars (Courts, PT, Memberships, Protein Cafe), 1-click Turkish Excel UTF-8 BOM CSV exports across 5 tables, sidebar unread badge, and `Kafe & Yorumlar` admin moderation screen.
- **Interactive SVG Complex Floor Plan & Live 5-Star Reviews (`front-page.php`):** Added 6-zone clickable architectural floor plan (`#kat-plani`) with live technical inspector and a live MySQL-backed 5-star review grid + submission form (`#yorumlar`).
- **EV Charger Monitor, Hourly Crowd Forecast & Protein Cafe Pre-Order Cart (`page-tesis-olanaklari.php`):** Added 6× 22 kW EV charger status bays, interactive Weekday/Sat/Sun hourly occupancy forecast bars, and a macro-calculating post-workout cafe pre-order cart synced to MySQL (`POST /cafe-order`).
- **Smart Package Finder Quiz, Comparison Matrix, Workout Log & Live Coach Sync (`page-uyelik.php`):** Added 3-step personalized package & PT recommendation quiz (`#paket-bulucu`), detailed comparison table, logged-in member view of Coach Nutrition Calendar + Advice + 1-on-1 Live Chat, and interactive Weekly Workout & Calorie Logger (`POST /workout-log`) with instant KPI badge updates.
- **RFC 5545 `.ics` Calendar Export & WhatsApp Match Invite (`assets/js/main.js`, `assets/js/booking.js`):** Added 1-click `.ics` download and pre-filled WhatsApp match/appointment invite sharing on court bookings, PT appointments, and member dashboard rows.
- **Database & REST API (`inc/database.php`, `inc/api-endpoints.php`):** Added 6 new MySQL tables (`coach_students`, `nutrition_plans`, `coach_messages`, `reviews`, `cafe_orders`, `workout_logs`) and 7 new REST endpoints with strict ownership & capability checks.

## [1.7.2] — 2026-11-25
- **WhatsApp FAB Motion Polish:** Upgraded floating WhatsApp button with dual staggered emerald sonar rings, diagonal glass sheen sweep, periodic chime icon nod synced with elastic notification badge pop, and hover Liquid Glass tooltip.

## [1.7.1] — 2026-11-25
- **Responsive Hardening:** Fixed mobile `390px` grid overflow on `/antrenorler/` by enforcing `minmax(0, 1fr)` and `min-width: 0` across `.apex-panel`, `.apex-split-profile`, `.apex-split-booking`, and `.apex-table-wrap`.
- **QA:** Verified `10/10` routes across Desktop (`1440×900`), Tablet (`768×1024`), and Mobile (`390×844`) with `0px` horizontal overflow and `0` local JS errors.

## [1.7.0] — 2026-11-25
- **UX Micro-Interactions:** Added global GPU scroll-progress hairline, bottom-left Back-to-Top button with live SVG progress ring, `Ctrl+K` / `/` keyboard search shortcut, one-click clipboard copy buttons with Liquid Glass toast notifications, live real-time complex Open/Closed badge, desktop pointer spotlight sheen, and GPU scroll-reveal transitions.
- **Mobile Clearance:** Added safe footer bottom padding on mobile so floating launchers never overlap footer links.

## [1.6.3] — 2026-11-25
- **Contact Redesign & Real Google Maps:** Redesigned `/iletisim/` with a clean 2-column dark/crimson layout (`Bize Yazın` form + 5 stacked `İletişim Bilgileri` cards) and embedded real interactive Google Maps for Ataşehir / İstanbul.

## [1.6.2] — 2026-11-25
- **3D Monolith Refinement:** Removed corner pills/buttons for a cleaner editorial canvas, updated giant gold typography to `HAREKETİN / YENİ MERKEZİ` and bottom-right manifesto copy.

## [1.6.1] — 2026-11-25
- **WhatsApp Hotline:** Added floating circular WhatsApp button with pulsing halo, red `1` badge, hover tooltip, and dynamic `wa.me` link right above `Apex AI Koç`.

## [1.6.0] — 2026-11-24
- **60/120 FPS Performance Pass:** Zero forced layouts during scroll, source-matched `<canvas>` backing resolution, concurrency-limited directional `ImageBitmap` decode queue with `O(1)` LRU eviction, adaptive mobile frame stride, lazy Three.js initialization with fast 32-bit `Math.imul` procedural noise hash, and true off-screen WebGL `rAF` sleep.

## [1.5.2] — 2026-11-24
- **Curved Sliding Membership Auth:** Redesigned `/uyelik/` Login/Register stage with dual-mode curved crimson overlay switcher and horizontal BMI calculator bar.

## [1.5.1] — 2026-11-24
- **Custom Dark Admin Login:** Branded `/wp-login.php` with `APEX ARENA — Yönetim Paneli Girişi` dark theme.

## [1.5.0] — 2026-11-24
- **3D Cast-Iron Spin-Lock Dumbbell:** Procedural CAD Three.js sculpture with stepped cast-iron plates, 6-lobe star spin-lock nuts, threaded bar shafts, and studio PMREM lighting.

## [1.4.0] — 2026-11-24
- **240-Frame Architectural Fly-Through & Gemini 2.5 Flash AI Coach:** Upgraded Hero scroll cinema to 240 HD WebP frames, integrated Yoast SEO (`tr_TR`), and connected live Gemini 2.5 Flash conversational AI with database grounding.

## [1.0.0] — 2026-11-23
- Initial APEX ARENA release with FIBA/CEV court rental + referee option, PT packages & trainer live calendars, facilities, QR membership dashboard, WCAG 2.2 AA accessibility center, and KVKK/cookie management.
