<?php
/**
 * APEX ARENA — Modüler Tam Kontrol Site İçerik & Tasarım CMS Stüdyosu (v2.4.0)
 *
 * Ana sayfanın her bir parçasını (bölüm sırası, özel blok ekleme/silme, bölüm aç/kapa,
 * hero rozeti/butonları/KPI kartları/kaydırma hikaye sahneleri, salon/PT/antrenör/duyuru
 * ekleme/çıkarma/gizleme, tesis kartları, üyelik paketleri, üst menü, pre-footer & yüzen
 * bileşenler) kod yazmadan WP Admin'den yönetmeyi sağlar.
 *
 * @package ApexArena
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Varsayılan tüm site CMS içerikleri & görünürlük bayrakları
 */
function apex_get_cms_defaults() {
    return [
        // 1. Marka, Renk, Menü, Global Bileşenler & Footer
        'brand_first'         => 'Apex',
        'brand_second'        => 'Arena',
        'accent_color'        => '#E31837',
        'nav_pt'              => 'PT Eğitmenler',
        'nav_trainers'        => 'Antrenörler',
        'nav_courts'          => 'Salon Kiralama',
        'nav_facilities'      => 'Tesis',
        'nav_membership'      => 'Üyelik',
        'nav_news'            => 'Duyurular',
        'nav_contact'         => 'İletişim',
        'nav_login_label'     => 'Üye Girişi',
        'nav_join_label'      => 'Bize Katıl',
        'whatsapp_phone'      => '0216 555 27 39',
        'whatsapp_message'    => 'Merhaba APEX ARENA, üyelik ve tesis hakkında bilgi almak istiyorum.',
        'map_query'           => 'Barbaros Mahallesi, Ataşehir, İstanbul',
        'prefooter_eyebrow'   => 'HEDEFİNİ ERTELEME · İLK ADIMI BUGÜN AT',
        'prefooter_title'     => 'ARENADA YERİNİ ALMA ZAMANI',
        'prefooter_desc'      => 'Ücretsiz tesis turu, InBody vücut analizi veya profesyonel maç salonu rezervasyonun tek tık uzağında.',
        'prefooter_cta1'      => 'Hemen Üye Ol',
        'prefooter_cta2'      => 'Salon Kirala',
        'footer_desc'         => '2 FIBA Basketbol Salonu, CEV Voleybol Sahası, lisanslı hakem hizmeti, sertifikalı PT kadrosu, 5 yıldızlı tesis olanakları ve 7/24 Hibrit AI Spor Koçu ile yeni nesil spor kompleksi.',
        'footer_copyright'    => 'APEX ARENA Spor ve Performans Kompleksi. Tüm hakları saklıdır.',
        'show_prefooter'      => 1,
        'show_whatsapp'       => 1,
        'show_ai_widget'      => 1,
        'show_a11y_widget'    => 1,

        // 2. Ana Sayfa Bölümleri & Görünürlük
        'show_hero'           => 1,
        'show_3d_dumbbell'    => 1,
        'show_courts'         => 1,
        'show_pt'             => 1,
        'show_trainers'       => 1,
        'show_facilities'     => 1,
        'show_membership'     => 1,
        'show_announcements'  => 1,
        'show_3d_arch'        => 1,
        'show_reviews'        => 1,

        // Hero Alt Parça Kontrolleri (v2.4.0)
        'show_hero_live_pill' => 1,
        'show_hero_cta1'      => 1,
        'show_hero_cta2'      => 1,
        'show_hero_ai_btn'    => 1,
        'show_hero_kpis'      => 1,
        'show_hero_chapters'  => 1,
        'hero_title'          => 'Sınırlarını Arenada Aş.',
        'hero_subtitle'       => 'İki FIBA basketbol salonu, profesyonel voleybol sahası, lisanslı hakem hizmeti, sertifikalı PT kadrosu ve 7/24 AI Spor Koçu tek çatı altında.',
        'hero_cta1'           => 'Üyeliği Başlat',
        'hero_cta1_url'       => '/uyelik/#paketler',
        'hero_cta2'           => 'Salon Kirala',
        'hero_cta2_url'       => '/salon-kiralama/',
        'hero_ai_label'       => 'AI Spor Koçu',
        'kpi1_val'            => '3 Salon',
        'kpi1_lbl'            => '2 Basketbol · 1 Voleybol · Hakem Hizmeti',
        'kpi2_val'            => 'Sertifikalı PT',
        'kpi2_lbl'            => 'Bireysel & Grupsal · Canlı Takvim',
        'kpi3_val'            => '180 Araç',
        'kpi3_lbl'            => 'Kapalı Otopark, 6 EV Şarj & Protein Kafe',
        'kpi4_val'            => '7/24 AI + WCAG',
        'kpi4_lbl'            => 'Gemini 2.5 AI & Erişilebilirlik Merkezi',

        'monolith_line1'      => 'HAREKETİN',
        'monolith_line2'      => 'YENİ MERKEZİ',
        'monolith_manifesto'  => "Her hedef için yeni bir başlangıç,\nher hareket için yeni bir güç.",

        // Bölüm Limitleri & Başlıkları
        'home_courts_limit'   => 0,
        'home_pt_limit'       => 0,
        'home_tr_limit'       => 0,
        'home_news_limit'     => 3,
        'home_reviews_limit'  => 6,
        'show_review_form'    => 1,

        'home_courts_eyebrow' => 'Salon ve Kiralama · FIBA & CEV',
        'home_courts_title'   => 'Profesyonel Maç Salonları',
        'home_courts_desc'    => 'FIBA standartlarında 2 basketbol ve CEV standartlarında 1 voleybol salonumuzu canlı saat seçimi ve lisanslı hakem opsiyonuyla rezerve edin.',
        'home_pt_eyebrow'     => 'PT Eğitmenler · Bireysel & Grupsal',
        'home_pt_title'       => 'PT Paketleri ve Seans Ücretleri',
        'home_pt_desc'        => 'Hedefine uygun birebir özel antrenman veya butik grup dersini seç, eğitim günlerini incele ve anında online randevu oluştur.',
        'home_tr_eyebrow'     => 'Uzman Kadro · Sertifika & Online Takvim',
        'home_tr_title'       => 'Antrenörlerimizle Tanış',
        'home_tr_desc'        => 'Her eğitmenimizin özgeçmişini, uluslararası sertifikalarını inceleyin ve canlı online takviminden doğrudan randevu alın.',
        'home_fac_eyebrow'    => 'Tesis Olanakları · Mimari Konfor',
        'home_fac_title'      => 'Soyunma Odası, Otopark ve Protein Kafe',
        'home_mem_eyebrow'    => 'Üyelik İşlemleri · Dijital QR Kart',
        'home_mem_title'      => 'Hedefine Uygun Üyeliği Seç',
        'home_news_eyebrow'   => 'Salon Duyuruları & Turnuvalar',
        'home_news_title'     => 'Güncel Duyurular',
        'home_arch_eyebrow'   => '3D MİMARİ KESİT MAKETİ · 14.500 M² · GERÇEK ZAMANLI WEBGL',
        'home_arch_title'     => 'Kompleksi 3D Mimaride Keşfet',
        'home_arch_desc'      => 'Farenizi 3D mimarideki herhangi bir salonun veya odanın üzerine getirin — kamera akıcı biçimde içeri yaklaşır. Tıklayarak odaya kilitlenebilir, sürükleyerek açıyı döndürebilirsiniz.',
        'home_rev_eyebrow'    => 'DOĞRULANMIŞ ÜYE DENEYİMLERİ',
        'home_rev_title'      => 'Üyelerimiz Ne Diyor?',

        // 3. Tesis Olanakları Varsayılan Metinleri
        'fac1_stat'           => '240 RFID DOLAP / 85°C SAUNA',
        'fac1_role'           => 'TOPARLANMA & SPA KONFORU',
        'fac1_title'          => 'Soyunma Odası & Fin Saunası',
        'fac1_desc'           => 'Temassız RFID akıllı dolaplar, yüksek basınçlı yağmur duş kabinleri, sedir ağacı Fin saunası, hijyenik havlu servisi ve engelsiz erişimli özel kabinler.',
        'fac2_role'           => 'PLAKA TANIMALI · DİREKT ASANSÖR',
        'fac2_title'          => 'Akıllı Kapalı Otopark',
        'fac2_desc'           => '180 araçlık kapalı otopark, üyelere ilk 3 saat ücretsiz park ayrıcalığı ve 6 adet hızlı elektrikli araç şarj noktası.',
        'fac3_stat'           => 'MAKRO MENÜ / PRO %15',
        'fac3_role'           => 'BESLENME & 3. DALGA KAHVE BARI',
        'fac3_title'          => 'Apex Protein Kafe',
        'fac3_desc'           => 'İzole whey shake’ler, diyetisyen onaylı makro hesaplı fit bowl tabakları, cold brew bar ve antrenman çıkışı hızlı ön sipariş.',

        // 4. 7 İç Sayfa Hero Başlıkları
        'page_pt_eyebrow'     => 'PT EĞİTMENLER · BİREYSEL & GRUPSAL ANTRENMAN SİSTEMİ',
        'page_pt_title'       => 'KİŞİSEL ANTRENMAN & GRUP PROGRAMLARI',
        'page_pt_desc'        => 'Bilimsel ölçüm (InBody 570), kişiye özel dönemleme, yüksek enerjili küçük grup dersleri, şeffaf seans ücretleri ve anlık online randevu sistemi.',
        'page_tr_eyebrow'     => 'UZMAN ANTRENÖR KADROSU · SERTİFİKALAR & CANLI TAKVİM',
        'page_tr_title'       => 'ANTRENÖRLER VE ONLİNE TAKVİM',
        'page_tr_desc'        => 'Her antrenörümüzün kimliğini, uzmanlık alanını, özgeçmişini, uluslararası sertifikalarını inceleyin ve canlı müsaitlik takviminden doğrudan randevu alın.',
        'page_courts_eyebrow' => 'SALON VE KİRALAMA · 2 BASKETBOL + 1 VOLEYBOL + HAKEM HİZMETİ',
        'page_courts_title'   => 'PROFESYONEL SALON KİRALAMA SİSTEMİ',
        'page_courts_desc'    => 'FIBA onaylı 2 adet basketbol salonumuz ve CEV standartlarında 1 adet voleybol salonumuzu teknik detayları ve fotoğraflarıyla inceleyin; lisanslı hakem ayarlama hizmetiyle anında rezerve edin.',
        'page_fac_eyebrow'    => 'TESİS OLANAKLARI · 5 YILDIZLI KONFOR & HİJYEN',
        'page_fac_title'      => 'SOYUNMA ODASI, OTOPARK VE PROTEİN KAFE',
        'page_fac_desc'       => 'Antrenman öncesi hazırlıktan maç sonrası toparlanmaya kadar her detayı düşünülmüş, engelsiz erişimli modern spor kompleksi olanaklarımız.',
        'page_mem_eyebrow'    => 'ÜYELİK İŞLEMLERİ · DİJİTAL QR KART & ONLİNE ÜYE PANELİ',
        'page_mem_title'      => 'ÜYELİK PAKETLERİ VE ÜYE PANELİ',
        'page_mem_desc'       => 'Hedefine uygun üyeliği seç, saniyeler içinde dijital QR üyelik kartını oluştur veya mevcut hesabınla giriş yaparak tüm salon ve PT rezervasyonlarını tek ekrandan yönet.',
        'page_news_eyebrow'   => 'SALON DUYURULARI & ETKİNLİK TAKVİMİ',
        'page_news_title'     => 'SALON DUYURULARI VE TURNUVALAR',
        'page_news_desc'      => 'Basketbol ve voleybol turnuvaları, salon bakım duyuruları, yeni grup dersleri ve üye kampanyalarını buradan takip edebilirsiniz.',
        'page_contact_eyebrow'=> 'İLETİŞİM · ULAŞIM & KURUMSAL DESTEK',
        'page_contact_title'  => 'BİZE ULAŞIN',
        'page_contact_desc'   => 'Ücretsiz tesis turu, kurumsal turnuva organizasyonu, PT danışmanlığı veya üyelik sorularınız için ekibimiz haftanın 7 günü yanınızda.',
    ];
}

/**
 * Kayıtlı tüm CMS verilerini getir
 */
function apex_get_cms() {
    $saved = get_option( 'apex_site_cms', [] );
    return wp_parse_args( is_array( $saved ) ? $saved : [], apex_get_cms_defaults() );
}

/**
 * Tek bir CMS metnini veya bayrağını güvenli biçimde oku
 */
function apex_cms( $key, $fallback = '' ) {
    $cms = apex_get_cms();
    if ( array_key_exists( $key, $cms ) && $cms[ $key ] !== '' ) {
        return $cms[ $key ];
    }
    $defs = apex_get_cms_defaults();
    return $defs[ $key ] ?? $fallback;
}

/**
 * Bir ana sayfa bölümünün veya alt parçasının görünür olup olmadığını kontrol et
 */
function apex_cms_show( $key ) {
    $cms = apex_get_cms();
    return ! empty( $cms[ $key ] );
}

/**
 * Site içi göreli yolları (/uyelik/) veya tam URL'leri (https://...) çöz
 */
function apex_resolve_cms_url( $url, $fallback = '/' ) {
    $u = trim( (string) $url );
    if ( $u === '' ) {
        return home_url( $fallback );
    }
    if ( preg_match( '#^https?://#i', $u ) || str_starts_with( $u, '#' ) ) {
        return $u;
    }
    return home_url( '/' . ltrim( $u, '/' ) );
}

/**
 * Yerleşik ana sayfa bölümlerinin kimlik-etiket-legacy-anahtar eşlemesi
 */
function apex_get_builtin_home_blocks_meta() {
    return [
        'hero'          => [ 'label' => 'Hero Video Tur & KPI Sahnesi',        'show_key' => 'show_hero' ],
        'dumbbell'      => [ 'label' => '3D Dambıl Monolit (HAREKETİN MERKEZİ)', 'show_key' => 'show_3d_dumbbell' ],
        'courts'        => [ 'label' => 'Profesyonel Maç Salonları',           'show_key' => 'show_courts' ],
        'pt'            => [ 'label' => 'PT Paketleri ve Seans Ücretleri',     'show_key' => 'show_pt' ],
        'trainers'      => [ 'label' => 'Antrenörler Portre Galerisi',         'show_key' => 'show_trainers' ],
        'facilities'    => [ 'label' => 'Tesis Olanakları Bento Galerisi',     'show_key' => 'show_facilities' ],
        'membership'    => [ 'label' => 'Üyelik Paketleri Tablosu',            'show_key' => 'show_membership' ],
        'announcements' => [ 'label' => 'Güncel Salon Duyuruları',             'show_key' => 'show_announcements' ],
        'arch3d'        => [ 'label' => '3D Mimari Kesit Maketi',              'show_key' => 'show_3d_arch' ],
        'reviews'       => [ 'label' => 'Üye Yorumları & Puanlama Formu',      'show_key' => 'show_reviews' ],
    ];
}

/**
 * Ana sayfadaki tüm bölümleri sıralı şekilde döndür (Yerleşik + Özel Bloklar)
 */
function apex_get_home_blocks() {
    $meta  = apex_get_builtin_home_blocks_meta();
    $cms   = apex_get_cms();
    $saved = get_option( 'apex_cms_home_blocks', [] );

    if ( ! is_array( $saved ) || empty( $saved ) ) {
        $out = [];
        foreach ( $meta as $id => $m ) {
            $out[] = [
                'id'      => $id,
                'type'    => 'builtin',
                'label'   => $m['label'],
                'enabled' => ! empty( $cms[ $m['show_key'] ] ),
            ];
        }
        return $out;
    }

    $seen = [];
    $out  = [];
    foreach ( $saved as $b ) {
        if ( ! is_array( $b ) || empty( $b['id'] ) ) {
            continue;
        }
        $id   = sanitize_key( $b['id'] );
        $type = ( ( $b['type'] ?? 'builtin' ) === 'custom' ) ? 'custom' : 'builtin';
        if ( $type === 'builtin' ) {
            if ( ! isset( $meta[ $id ] ) || isset( $seen[ $id ] ) ) {
                continue;
            }
            $seen[ $id ] = true;
            $out[] = [
                'id'      => $id,
                'type'    => 'builtin',
                'label'   => $meta[ $id ]['label'],
                'enabled' => isset( $b['enabled'] ) ? ! empty( $b['enabled'] ) : ! empty( $cms[ $meta[ $id ]['show_key'] ] ),
            ];
        } else {
            $out[] = [
                'id'        => $id,
                'type'      => 'custom',
                'label'     => sanitize_text_field( $b['title'] ?? 'Özel Bölüm' ),
                'enabled'   => ! empty( $b['enabled'] ),
                'layout'    => in_array( $b['layout'] ?? 'cta_banner', [ 'cta_banner', 'feature_cards', 'split_media' ], true ) ? $b['layout'] : 'cta_banner',
                'eyebrow'   => sanitize_text_field( $b['eyebrow'] ?? 'ÖZEL DUYURU & KAMPANYA' ),
                'title'     => sanitize_text_field( $b['title'] ?? 'Özel Bölüm Başlığı' ),
                'desc'      => sanitize_textarea_field( $b['desc'] ?? '' ),
                'cta_text'  => sanitize_text_field( $b['cta_text'] ?? 'Hemen İncele' ),
                'cta_url'   => sanitize_text_field( $b['cta_url'] ?? '/uyelik/#paketler' ),
                'image'     => sanitize_text_field( $b['image'] ?? 'hero-arena.jpg' ),
                'cards_raw' => sanitize_textarea_field( $b['cards_raw'] ?? '' ),
            ];
        }
    }
    foreach ( $meta as $id => $m ) {
        if ( ! isset( $seen[ $id ] ) ) {
            $out[] = [
                'id'      => $id,
                'type'    => 'builtin',
                'label'   => $m['label'],
                'enabled' => ! empty( $cms[ $m['show_key'] ] ),
            ];
        }
    }
    return $out;
}

/**
 * Dinamik Üst Navigasyon Öğeleri (header.php)
 */
function apex_get_nav_items() {
    $saved = get_option( 'apex_cms_nav_items', [] );
    if ( is_array( $saved ) && ! empty( $saved ) ) {
        $out = [];
        foreach ( $saved as $item ) {
            if ( empty( $item['label'] ) ) {
                continue;
            }
            $u = trim( (string) ( $item['url'] ?? '/' ) );
            $out[] = [
                'label'   => sanitize_text_field( $item['label'] ),
                'url'     => $u,
                'slug'    => sanitize_title( trim( (string) parse_url( $u, PHP_URL_PATH ), '/' ) ),
                'enabled' => ! isset( $item['enabled'] ) || ! empty( $item['enabled'] ),
            ];
        }
        if ( ! empty( $out ) ) {
            return $out;
        }
    }
    return [
        [ 'label' => apex_cms( 'nav_pt', 'PT Eğitmenler' ),      'url' => '/pt-egitmenler/',    'slug' => 'pt-egitmenler',    'enabled' => true ],
        [ 'label' => apex_cms( 'nav_trainers', 'Antrenörler' ),  'url' => '/antrenorler/',      'slug' => 'antrenorler',      'enabled' => true ],
        [ 'label' => apex_cms( 'nav_courts', 'Salon Kiralama' ), 'url' => '/salon-kiralama/',   'slug' => 'salon-kiralama',   'enabled' => true ],
        [ 'label' => apex_cms( 'nav_facilities', 'Tesis' ),      'url' => '/tesis-olanaklari/', 'slug' => 'tesis-olanaklari', 'enabled' => true ],
        [ 'label' => apex_cms( 'nav_membership', 'Üyelik' ),     'url' => '/uyelik/',           'slug' => 'uyelik',           'enabled' => true ],
        [ 'label' => apex_cms( 'nav_news', 'Duyurular' ),        'url' => '/duyurular/',        'slug' => 'duyurular',        'enabled' => true ],
        [ 'label' => apex_cms( 'nav_contact', 'İletişim' ),      'url' => '/iletisim/',         'slug' => 'iletisim',         'enabled' => true ],
    ];
}

/**
 * Dinamik Hero KPI Kartları
 */
function apex_get_hero_kpis() {
    $saved = get_option( 'apex_cms_hero_kpis', [] );
    if ( is_array( $saved ) && ! empty( $saved ) ) {
        $out = [];
        foreach ( $saved as $k ) {
            if ( empty( $k['val'] ) && empty( $k['lbl'] ) ) {
                continue;
            }
            $out[] = [
                'val' => sanitize_text_field( $k['val'] ?? '' ),
                'lbl' => sanitize_text_field( $k['lbl'] ?? '' ),
            ];
        }
        if ( ! empty( $out ) ) {
            return $out;
        }
    }
    return [
        [ 'val' => apex_cms( 'kpi1_val' ), 'lbl' => apex_cms( 'kpi1_lbl' ) ],
        [ 'val' => apex_cms( 'kpi2_val' ), 'lbl' => apex_cms( 'kpi2_lbl' ) ],
        [ 'val' => apex_cms( 'kpi3_val' ), 'lbl' => apex_cms( 'kpi3_lbl' ) ],
        [ 'val' => apex_cms( 'kpi4_val' ), 'lbl' => apex_cms( 'kpi4_lbl' ) ],
    ];
}

/**
 * Dinamik Scroll-Cinema Hikaye Bölümleri (01-04+)
 */
function apex_get_hero_chapters() {
    $saved = get_option( 'apex_cms_hero_chapters', [] );
    if ( is_array( $saved ) && ! empty( $saved ) ) {
        return $saved;
    }
    return [
        [
            'enabled'   => 1,
            'badge'     => '01 / Mimari Karşılama Lobisi',
            'title'     => 'Kapıdan İçeri, Yeni Bir Standart.',
            'desc'      => 'Doğal mermer, sıcak lineer mimari aydınlatma ve QR dijital üyelik geçişiyle saniyeler içinde antrenmana başla.',
            'cta1_text' => 'Üyelik Avantajlarını Gör →',
            'cta1_url'  => '/uyelik/#paketler',
            'cta2_text' => '',
            'cta2_url'  => '',
        ],
        [
            'enabled'   => 1,
            'badge'     => '02 / 180 Araçlık Akıllı Kapalı Otopark & 6 EV Şarj',
            'title'     => 'Sıfır Bekleme, Doğrudan Giriş.',
            'desc'      => 'Plaka tanımalı otomatik bariyerler, üyelere ilk 3 saat ücretsiz park ayrıcalığı ve elektrikli araç hızlı şarj istasyonları.',
            'cta1_text' => 'Tesis Olanaklarını İncele',
            'cta1_url'  => '/tesis-olanaklari/',
            'cta2_text' => '',
            'cta2_url'  => '',
        ],
        [
            'enabled'   => 1,
            'badge'     => '03 / 240 RFID Dolap · Yağmur Duş · Fin Saunası',
            'title'     => 'Otel Konforunda Toparlanma.',
            'desc'      => 'Dijital akıllı dolaplar, özel yağmur duş kabinleri ve sedir ağacı Fin saunasıyla yoğun antrenman sonrası tam yenilenme.',
            'cta1_text' => 'PT Programlarını İncele →',
            'cta1_url'  => '/pt-egitmenler/',
            'cta2_text' => '',
            'cta2_url'  => '',
        ],
        [
            'enabled'   => 1,
            'badge'     => '04 / Apex Arena Protein Kafe & Lounge',
            'title'     => 'Beslenme ve Sosyal Buluşma.',
            'desc'      => 'Makro hesaplı protein shake’ler, taze fit bowl menüleri ve 3. dalga kahve barıyla günün her saatinde enerji dolu mola.',
            'cta1_text' => 'Salon Kirala & Başla',
            'cta1_url'  => '/salon-kiralama/#rezervasyon',
            'cta2_text' => 'Kafe Menüsü',
            'cta2_url'  => '/tesis-olanaklari/#kafe',
        ],
    ];
}

/**
 * Dinamik Tesis Olanakları Kartları (Bento / Editoryal Galeri)
 */
function apex_get_facility_tiles() {
    $saved = get_option( 'apex_cms_facilities_items', [] );
    if ( is_array( $saved ) && ! empty( $saved ) ) {
        return $saved;
    }
    return [
        [
            'stat'            => apex_cms( 'fac1_stat' ),
            'role'            => apex_cms( 'fac1_role' ),
            'title'           => apex_cms( 'fac1_title' ),
            'desc'            => apex_cms( 'fac1_desc' ),
            'cta'             => 'Tesis Detayları',
            'url'             => '/tesis-olanaklari/',
            'image'           => 'facility-locker.jpg',
            'is_live_parking' => 0,
        ],
        [
            'stat'            => '118 BOŞ YER / 6× 22 KW EV',
            'role'            => apex_cms( 'fac2_role' ),
            'title'           => apex_cms( 'fac2_title' ),
            'desc'            => apex_cms( 'fac2_desc' ),
            'cta'             => 'Canlı EV & Doluluk',
            'url'             => '/tesis-olanaklari/#otopark',
            'image'           => 'facility-parking.jpg',
            'is_live_parking' => 1,
        ],
        [
            'stat'            => apex_cms( 'fac3_stat' ),
            'role'            => apex_cms( 'fac3_role' ),
            'title'           => apex_cms( 'fac3_title' ),
            'desc'            => apex_cms( 'fac3_desc' ),
            'cta'             => 'Menü & Ön Sipariş',
            'url'             => '/tesis-olanaklari/#kafe',
            'image'           => 'facility-cafe.jpg',
            'is_live_parking' => 0,
        ],
    ];
}

/**
 * Ana sayfada gizlenecek CPT ID listesi
 */
function apex_get_hidden_home_posts() {
    $hidden = get_option( 'apex_cms_hidden_posts', [] );
    return is_array( $hidden ) ? array_values( array_filter( array_map( 'absint', $hidden ) ) ) : [];
}

/**
 * Tema içi mevcut görseller listesi
 */
function apex_get_theme_image_choices() {
    $dir = APEX_DIR . '/assets/images';
    $out = [];
    if ( is_dir( $dir ) ) {
        foreach ( glob( $dir . '/*.{jpg,jpeg,png,webp}', GLOB_BRACE ) ?: [] as $f ) {
            $out[] = basename( $f );
        }
        sort( $out );
    }
    return $out;
}

/**
 * Admin panelinde seçilen özel vurgu rengini ön yüze uygula
 */
add_action( 'wp_head', function() {
    $accent = sanitize_hex_color( (string) apex_cms( 'accent_color', '#E31837' ) );
    if ( ! $accent || strtoupper( $accent ) === '#E31837' ) {
        return;
    }
    echo '<style id="apex-cms-accent">:root{--apex-crimson:' . esc_attr( $accent ) . ';--apex-crimson-hover:' . esc_attr( $accent ) . ';}</style>' . "\n";
}, 30 );

/**
 * WP Admin — 6 Sekmeli Modüler Site İçerik & Parça Yönetim Stüdyosu (v2.4.0)
 */
function apex_admin_site_cms_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Yetkisiz erişim.' );
    }

    $notice       = '';
    $active_tab   = sanitize_key( $_GET['tab'] ?? ( $_POST['apex_cms_tab'] ?? 'home' ) );
    $allowed_tabs = [ 'brand', 'home', 'items', 'plans', 'facilities', 'pages' ];
    if ( ! in_array( $active_tab, $allowed_tabs, true ) ) {
        $active_tab = 'home';
    }

    // 0. Varsayılanlara Sıfırla
    if ( isset( $_POST['apex_cms_reset'] ) && check_admin_referer( 'apex_site_cms_nonce' ) ) {
        foreach ( [
            'apex_site_cms', 'apex_cms_home_blocks', 'apex_cms_nav_items',
            'apex_cms_hero_kpis', 'apex_cms_hero_chapters', 'apex_cms_facilities_items',
            'apex_cms_hidden_posts', 'apex_cms_plans', 'apex_cms_cafe_menu',
        ] as $opt_key ) {
            delete_option( $opt_key );
        }
        $notice = '<div class="notice notice-info"><p>Tüm ana sayfa parçaları, bölüm sıralaması, özel bloklar, menü, paketler ve kafe menüsü fabrika varsayılanlarına sıfırlandı.</p></div>';
    }

    // Tab 2 Özel Aksiyonlar: Çöpe Taşıma veya Yeni CPT Ekleme
    if ( ( ! empty( $_POST['apex_cms_trash_post_id'] ) || isset( $_POST['apex_cms_trash_cpt'] ) ) && check_admin_referer( 'apex_site_cms_nonce' ) ) {
        $pid = absint( $_POST['apex_cms_trash_post_id'] ?? ( $_POST['trash_post_id'] ?? 0 ) );
        if ( $pid && current_user_can( 'delete_post', $pid ) ) {
            $ptitle = get_the_title( $pid );
            wp_trash_post( $pid );
            $notice = '<div class="notice notice-info"><p>🗑 <strong>' . esc_html( $ptitle ) . '</strong> çöpe taşındı (hem ana sayfadan hem siteden kaldırıldı).</p></div>';
        }
    }

    if ( isset( $_POST['apex_cms_add_cpt'] ) && check_admin_referer( 'apex_site_cms_nonce' ) ) {
        $cpt = sanitize_key( $_POST['add_cpt_type'] ?? '' );
        if ( in_array( $cpt, [ 'apex_court', 'apex_pt_package', 'apex_trainer', 'apex_announcement' ], true ) ) {
            $title   = sanitize_text_field( wp_unslash( $_POST['new_title'] ?? '' ) );
            $content = sanitize_textarea_field( wp_unslash( $_POST['new_content'] ?? '' ) );
            if ( $title !== '' ) {
                $new_id = wp_insert_post( [
                    'post_type'    => $cpt,
                    'post_status'  => 'publish',
                    'post_title'   => $title,
                    'post_content' => $content ?: $title,
                    'post_excerpt' => wp_trim_words( $content ?: $title, 20 ),
                    'menu_order'   => 99,
                ] );
                if ( $new_id && ! is_wp_error( $new_id ) ) {
                    if ( $cpt === 'apex_court' ) {
                        update_post_meta( $new_id, '_apex_sport', sanitize_key( wp_unslash( $_POST['new_sport'] ?? 'basketbol' ) ) );
                        update_post_meta( $new_id, '_apex_dimensions', sanitize_text_field( wp_unslash( $_POST['new_dim'] ?? '28×15m FIBA' ) ) );
                        update_post_meta( $new_id, '_apex_surface', sanitize_text_field( wp_unslash( $_POST['new_surface'] ?? 'FIBA Akçaağaç Parke' ) ) );
                        update_post_meta( $new_id, '_apex_hourly_rate', max( 100, absint( $_POST['new_rate'] ?? 1600 ) ) );
                        update_post_meta( $new_id, '_apex_referee_fee', max( 0, absint( $_POST['new_referee'] ?? 450 ) ) );
                        update_post_meta( $new_id, '_apex_photo', sanitize_text_field( wp_unslash( $_POST['new_photo'] ?? 'court-basketball-1.jpg' ) ) );
                    } elseif ( $cpt === 'apex_pt_package' ) {
                        $t_type = sanitize_key( wp_unslash( $_POST['new_pt_type'] ?? 'bireysel' ) );
                        update_post_meta( $new_id, '_apex_training_type', $t_type === 'grupsal' ? 'grupsal' : 'bireysel' );
                        update_post_meta( $new_id, '_apex_sessions', max( 1, absint( $_POST['new_sessions'] ?? 8 ) ) );
                        update_post_meta( $new_id, '_apex_total_price', max( 100, absint( $_POST['new_total_price'] ?? 8800 ) ) );
                        update_post_meta( $new_id, '_apex_per_session_fee', max( 50, absint( $_POST['new_per_fee'] ?? 1100 ) ) );
                        update_post_meta( $new_id, '_apex_days', sanitize_text_field( wp_unslash( $_POST['new_days'] ?? 'Pazartesi · Çarşamba · Cuma' ) ) );
                        update_post_meta( $new_id, '_apex_scope', sanitize_textarea_field( wp_unslash( $_POST['new_scope'] ?? "Kişiye özel program\nInBody analizi" ) ) );
                        update_post_meta( $new_id, '_apex_featured', ! empty( $_POST['new_featured'] ) ? '1' : '0' );
                    } elseif ( $cpt === 'apex_trainer' ) {
                        update_post_meta( $new_id, '_apex_role', sanitize_text_field( wp_unslash( $_POST['new_role'] ?? 'Atletik Performans Koçu' ) ) );
                        update_post_meta( $new_id, '_apex_specialty', sanitize_text_field( wp_unslash( $_POST['new_spec'] ?? 'Fonksiyonel Kuvvet & Kondisyon' ) ) );
                        update_post_meta( $new_id, '_apex_experience', max( 1, absint( $_POST['new_exp'] ?? 6 ) ) );
                        update_post_meta( $new_id, '_apex_rating', sanitize_text_field( wp_unslash( $_POST['new_rating'] ?? '4.9' ) ) );
                        update_post_meta( $new_id, '_apex_session_fee', max( 100, absint( $_POST['new_fee'] ?? 1400 ) ) );
                        update_post_meta( $new_id, '_apex_photo', sanitize_text_field( wp_unslash( $_POST['new_photo'] ?? 'trainer-kerem.jpg' ) ) );
                    } elseif ( $cpt === 'apex_announcement' ) {
                        update_post_meta( $new_id, '_apex_ann_category', sanitize_text_field( wp_unslash( $_POST['new_cat'] ?? 'Turnuva' ) ) );
                        update_post_meta( $new_id, '_apex_ann_location', sanitize_text_field( wp_unslash( $_POST['new_loc'] ?? 'Apex Arena' ) ) );
                        update_post_meta( $new_id, '_apex_ann_date', sanitize_text_field( wp_unslash( $_POST['new_date'] ?? date( 'd.m.Y' ) ) ) );
                    }
                    $notice = '<div class="notice notice-success"><p>✅ <strong>' . esc_html( $title ) . '</strong> eklendi ve sitede yayına alındı!</p></div>';
                }
            } else {
                $notice = '<div class="notice notice-error"><p>⚠️ Lütfen yeni kayıt için başlık girin.</p></div>';
            }
        }
    }

    if ( isset( $_POST['apex_cms_save_visibility'] ) && check_admin_referer( 'apex_site_cms_nonce' ) ) {
        $all_ids  = array_map( 'absint', (array) ( $_POST['all_home_cpt_ids'] ?? [] ) );
        $shown    = array_map( 'absint', (array) ( $_POST['show_home_cpt_ids'] ?? [] ) );
        $hidden   = array_values( array_diff( $all_ids, $shown ) );
        update_option( 'apex_cms_hidden_posts', $hidden );
        $notice = '<div class="notice notice-success"><p>✅ <strong>Ana sayfa salon / PT / antrenör / duyuru görünürlük ayarları kaydedildi!</strong></p></div>';
    }

    // Ana Kaydetme İşlemi
    if ( isset( $_POST['apex_cms_save'] ) && check_admin_referer( 'apex_site_cms_nonce' ) ) {
        $cur = get_option( 'apex_site_cms', [] );
        if ( ! is_array( $cur ) ) {
            $cur = [];
        }

        if ( $active_tab === 'brand' ) {
            $text_keys = [
                'brand_first', 'brand_second', 'nav_login_label', 'nav_join_label',
                'whatsapp_phone', 'whatsapp_message', 'map_query', 'prefooter_eyebrow', 'prefooter_title',
                'prefooter_cta1', 'prefooter_cta2', 'footer_copyright',
            ];
            foreach ( $text_keys as $k ) {
                if ( isset( $_POST[ $k ] ) ) {
                    $cur[ $k ] = sanitize_text_field( wp_unslash( $_POST[ $k ] ) );
                }
            }
            $cur['prefooter_desc'] = sanitize_textarea_field( wp_unslash( $_POST['prefooter_desc'] ?? '' ) );
            $cur['footer_desc']    = sanitize_textarea_field( wp_unslash( $_POST['footer_desc'] ?? '' ) );
            $hex = sanitize_hex_color( wp_unslash( $_POST['accent_color'] ?? '#E31837' ) );
            $cur['accent_color']   = $hex ?: '#E31837';

            foreach ( [ 'show_prefooter', 'show_whatsapp', 'show_ai_widget', 'show_a11y_widget' ] as $gt ) {
                $cur[ $gt ] = ! empty( $_POST[ $gt ] ) ? 1 : 0;
            }

            // Dinamik Üst Menü
            $nav_labels  = (array) ( $_POST['nav_item_label'] ?? [] );
            $nav_urls    = (array) ( $_POST['nav_item_url'] ?? [] );
            $nav_enabled = (array) ( $_POST['nav_item_enabled'] ?? [] );
            $new_nav     = [];
            for ( $i = 0; $i < count( $nav_labels ); $i++ ) {
                $lbl = sanitize_text_field( wp_unslash( $nav_labels[ $i ] ?? '' ) );
                $url = sanitize_text_field( wp_unslash( $nav_urls[ $i ] ?? '' ) );
                if ( $lbl === '' || $url === '' ) {
                    continue;
                }
                $new_nav[] = [
                    'label'   => $lbl,
                    'url'     => $url,
                    'slug'    => sanitize_title( trim( (string) parse_url( $url, PHP_URL_PATH ), '/' ) ),
                    'enabled' => ! empty( $nav_enabled[ $i ] ) ? 1 : 0,
                ];
            }
            update_option( 'apex_cms_nav_items', $new_nav );

            $s = apex_get_settings();
            $s['phone']         = sanitize_text_field( wp_unslash( $_POST['site_phone'] ?? $s['phone'] ) );
            $s['email']         = sanitize_email( wp_unslash( $_POST['site_email'] ?? $s['email'] ) );
            $s['address']       = sanitize_text_field( wp_unslash( $_POST['site_address'] ?? $s['address'] ) );
            $s['weekday_hours'] = sanitize_text_field( wp_unslash( $_POST['site_weekday_hours'] ?? $s['weekday_hours'] ) );
            $s['weekend_hours'] = sanitize_text_field( wp_unslash( $_POST['site_weekend_hours'] ?? $s['weekend_hours'] ) );
            update_option( 'apex_arena_settings', $s );
            update_option( 'apex_site_cms', $cur );
            $notice = '<div class="notice notice-success"><p>✅ <strong>Marka, Menü, Renk, Footer &amp; Yüzen Bileşenler güncellendi!</strong></p></div>';

        } elseif ( $active_tab === 'home' ) {
            // 1. Ana Sayfa Bölüm Sırası, Görünürlük ve Özel Bloklar
            $blk_ids     = (array) ( $_POST['blk_id'] ?? [] );
            $blk_types   = (array) ( $_POST['blk_type'] ?? [] );
            $blk_enabled = (array) ( $_POST['blk_enabled'] ?? [] );
            $blk_layouts = (array) ( $_POST['blk_layout'] ?? [] );
            $blk_eyebrows= (array) ( $_POST['blk_eyebrow'] ?? [] );
            $blk_titles  = (array) ( $_POST['blk_title'] ?? [] );
            $blk_descs   = (array) ( $_POST['blk_desc'] ?? [] );
            $blk_ctas    = (array) ( $_POST['blk_cta_text'] ?? [] );
            $blk_urls    = (array) ( $_POST['blk_cta_url'] ?? [] );
            $blk_imgs    = (array) ( $_POST['blk_image'] ?? [] );
            $blk_cards   = (array) ( $_POST['blk_cards_raw'] ?? [] );

            $meta        = apex_get_builtin_home_blocks_meta();
            $new_blocks  = [];
            for ( $i = 0; $i < count( $blk_ids ); $i++ ) {
                $bid   = sanitize_key( wp_unslash( $blk_ids[ $i ] ?? '' ) );
                $btype = ( wp_unslash( $blk_types[ $i ] ?? 'builtin' ) === 'custom' ) ? 'custom' : 'builtin';
                $on    = ! empty( $blk_enabled[ $i ] ) ? 1 : 0;
                if ( ! $bid ) {
                    continue;
                }
                if ( $btype === 'builtin' && isset( $meta[ $bid ] ) ) {
                    $cur[ $meta[ $bid ]['show_key'] ] = $on;
                    $new_blocks[] = [ 'id' => $bid, 'type' => 'builtin', 'enabled' => $on ];
                } elseif ( $btype === 'custom' ) {
                    $title = sanitize_text_field( wp_unslash( $blk_titles[ $i ] ?? 'Yeni Özel Bölüm' ) );
                    $new_blocks[] = [
                        'id'        => $bid,
                        'type'      => 'custom',
                        'enabled'   => $on,
                        'layout'    => sanitize_key( wp_unslash( $blk_layouts[ $i ] ?? 'cta_banner' ) ),
                        'eyebrow'   => sanitize_text_field( wp_unslash( $blk_eyebrows[ $i ] ?? '' ) ),
                        'title'     => $title,
                        'desc'      => sanitize_textarea_field( wp_unslash( $blk_descs[ $i ] ?? '' ) ),
                        'cta_text'  => sanitize_text_field( wp_unslash( $blk_ctas[ $i ] ?? '' ) ),
                        'cta_url'   => sanitize_text_field( wp_unslash( $blk_urls[ $i ] ?? '/uyelik/#paketler' ) ),
                        'image'     => sanitize_text_field( wp_unslash( $blk_imgs[ $i ] ?? 'hero-arena.jpg' ) ),
                        'cards_raw' => sanitize_textarea_field( wp_unslash( $blk_cards[ $i ] ?? '' ) ),
                    ];
                }
            }
            if ( ! empty( $new_blocks ) ) {
                update_option( 'apex_cms_home_blocks', $new_blocks );
            }

            // 2. Hero Parça Anahtarları & Metinler
            $hero_toggles = [ 'show_hero_live_pill', 'show_hero_cta1', 'show_hero_cta2', 'show_hero_ai_btn', 'show_hero_kpis', 'show_hero_chapters', 'show_review_form' ];
            foreach ( $hero_toggles as $ht ) {
                $cur[ $ht ] = ! empty( $_POST[ $ht ] ) ? 1 : 0;
            }

            $fields = [
                'hero_title', 'hero_cta1', 'hero_cta1_url', 'hero_cta2', 'hero_cta2_url', 'hero_ai_label',
                'monolith_line1', 'monolith_line2',
                'home_courts_eyebrow', 'home_courts_title',
                'home_pt_eyebrow', 'home_pt_title',
                'home_tr_eyebrow', 'home_tr_title',
                'home_fac_eyebrow', 'home_fac_title',
                'home_mem_eyebrow', 'home_mem_title',
                'home_news_eyebrow', 'home_news_title',
                'home_arch_eyebrow', 'home_arch_title',
                'home_rev_eyebrow', 'home_rev_title',
            ];
            foreach ( $fields as $fk ) {
                if ( isset( $_POST[ $fk ] ) ) {
                    $cur[ $fk ] = sanitize_text_field( wp_unslash( $_POST[ $fk ] ) );
                }
            }
            foreach ( [ 'home_courts_limit', 'home_pt_limit', 'home_tr_limit', 'home_news_limit', 'home_reviews_limit' ] as $lk ) {
                if ( isset( $_POST[ $lk ] ) ) {
                    $cur[ $lk ] = max( 0, min( 30, absint( $_POST[ $lk ] ) ) );
                }
            }
            $areas = [ 'hero_subtitle', 'monolith_manifesto', 'home_courts_desc', 'home_pt_desc', 'home_tr_desc', 'home_arch_desc' ];
            foreach ( $areas as $ak ) {
                if ( isset( $_POST[ $ak ] ) ) {
                    $cur[ $ak ] = sanitize_textarea_field( wp_unslash( $_POST[ $ak ] ) );
                }
            }

            // 3. Dinamik Hero KPI Kartları
            $kpi_vals = (array) ( $_POST['kpi_val'] ?? [] );
            $kpi_lbls = (array) ( $_POST['kpi_lbl'] ?? [] );
            $new_kpis = [];
            for ( $i = 0; $i < count( $kpi_vals ); $i++ ) {
                $v = sanitize_text_field( wp_unslash( $kpi_vals[ $i ] ?? '' ) );
                $l = sanitize_text_field( wp_unslash( $kpi_lbls[ $i ] ?? '' ) );
                if ( $v === '' && $l === '' ) {
                    continue;
                }
                $new_kpis[] = [ 'val' => $v, 'lbl' => $l ];
            }
            update_option( 'apex_cms_hero_kpis', $new_kpis );

            // 4. Dinamik Kaydırma Hikaye Bölümleri (Chapters 01..N)
            $ch_en    = (array) ( $_POST['ch_enabled'] ?? [] );
            $ch_badge = (array) ( $_POST['ch_badge'] ?? [] );
            $ch_title = (array) ( $_POST['ch_title'] ?? [] );
            $ch_desc  = (array) ( $_POST['ch_desc'] ?? [] );
            $ch_c1t   = (array) ( $_POST['ch_cta1_text'] ?? [] );
            $ch_c1u   = (array) ( $_POST['ch_cta1_url'] ?? [] );
            $ch_c2t   = (array) ( $_POST['ch_cta2_text'] ?? [] );
            $ch_c2u   = (array) ( $_POST['ch_cta2_url'] ?? [] );
            $new_chs  = [];
            for ( $i = 0; $i < count( $ch_title ); $i++ ) {
                $ct = sanitize_text_field( wp_unslash( $ch_title[ $i ] ?? '' ) );
                if ( $ct === '' ) {
                    continue;
                }
                $new_chs[] = [
                    'enabled'   => ! empty( $ch_en[ $i ] ) ? 1 : 0,
                    'badge'     => sanitize_text_field( wp_unslash( $ch_badge[ $i ] ?? '' ) ),
                    'title'     => $ct,
                    'desc'      => sanitize_textarea_field( wp_unslash( $ch_desc[ $i ] ?? '' ) ),
                    'cta1_text' => sanitize_text_field( wp_unslash( $ch_c1t[ $i ] ?? '' ) ),
                    'cta1_url'  => sanitize_text_field( wp_unslash( $ch_c1u[ $i ] ?? '' ) ),
                    'cta2_text' => sanitize_text_field( wp_unslash( $ch_c2t[ $i ] ?? '' ) ),
                    'cta2_url'  => sanitize_text_field( wp_unslash( $ch_c2u[ $i ] ?? '' ) ),
                ];
            }
            update_option( 'apex_cms_hero_chapters', $new_chs );

            update_option( 'apex_site_cms', $cur );
            $notice = '<div class="notice notice-success"><p>✅ <strong>Ana Sayfa bölüm sırası, özel bloklar, Hero parçaları ve başlıklar canlı siteye kaydedildi!</strong></p></div>';

        } elseif ( $active_tab === 'plans' ) {
            $p_slugs  = (array) ( $_POST['plan_slug'] ?? [] );
            $p_names  = (array) ( $_POST['plan_name'] ?? [] );
            $p_badges = (array) ( $_POST['plan_badge'] ?? [] );
            $p_month  = (array) ( $_POST['plan_monthly'] ?? [] );
            $p_annual = (array) ( $_POST['plan_annual'] ?? [] );
            $p_sum    = (array) ( $_POST['plan_summary'] ?? [] );
            $p_feats  = (array) ( $_POST['plan_features'] ?? [] );
            $feat_idx = absint( $_POST['plan_featured_idx'] ?? 1 );

            $saved_plans = [];
            for ( $i = 0; $i < count( $p_names ); $i++ ) {
                $nm = sanitize_text_field( wp_unslash( $p_names[ $i ] ?? '' ) );
                if ( $nm === '' ) {
                    continue;
                }
                $sl = sanitize_title( wp_unslash( $p_slugs[ $i ] ?? '' ) );
                if ( $sl === '' ) {
                    $sl = sanitize_title( $nm );
                }
                if ( isset( $saved_plans[ $sl ] ) ) {
                    $sl .= '-' . ( $i + 1 );
                }
                $feats_raw = sanitize_textarea_field( wp_unslash( $p_feats[ $i ] ?? '' ) );
                $feats     = array_values( array_filter( array_map( 'trim', explode( "\n", $feats_raw ) ) ) );
                $saved_plans[ $sl ] = [
                    'slug'          => $sl,
                    'name'          => $nm,
                    'badge'         => sanitize_text_field( wp_unslash( $p_badges[ $i ] ?? 'ÜYELİK PAKETİ' ) ),
                    'monthly_price' => max( 100, absint( $p_month[ $i ] ?? 1990 ) ),
                    'annual_price'  => max( 100, absint( $p_annual[ $i ] ?? 1590 ) ),
                    'featured'      => ( $i === $feat_idx ),
                    'summary'       => sanitize_textarea_field( wp_unslash( $p_sum[ $i ] ?? '' ) ),
                    'features'      => $feats,
                ];
            }
            if ( ! empty( $saved_plans ) ) {
                update_option( 'apex_cms_plans', $saved_plans );
                $notice = '<div class="notice notice-success"><p>✅ <strong>' . count( $saved_plans ) . ' üyelik paketi</strong> kaydedildi (Ana sayfa, /uyelik/ ve kayıt formu anında güncellendi).</p></div>';
            } else {
                $notice = '<div class="notice notice-error"><p>⚠️ En az 1 üyelik paketi bulunmalıdır.</p></div>';
            }

        } elseif ( $active_tab === 'facilities' ) {
            // Dinamik Tesis Kartları
            $f_stats = (array) ( $_POST['fac_stat'] ?? [] );
            $f_roles = (array) ( $_POST['fac_role'] ?? [] );
            $f_titles= (array) ( $_POST['fac_title'] ?? [] );
            $f_descs = (array) ( $_POST['fac_desc'] ?? [] );
            $f_ctas  = (array) ( $_POST['fac_cta'] ?? [] );
            $f_urls  = (array) ( $_POST['fac_url'] ?? [] );
            $f_imgs  = (array) ( $_POST['fac_image'] ?? [] );
            $f_live  = (array) ( $_POST['fac_live_parking'] ?? [] );

            $new_facs = [];
            for ( $i = 0; $i < count( $f_titles ); $i++ ) {
                $ft = sanitize_text_field( wp_unslash( $f_titles[ $i ] ?? '' ) );
                if ( $ft === '' ) {
                    continue;
                }
                $new_facs[] = [
                    'stat'            => sanitize_text_field( wp_unslash( $f_stats[ $i ] ?? '' ) ),
                    'role'            => sanitize_text_field( wp_unslash( $f_roles[ $i ] ?? '' ) ),
                    'title'           => $ft,
                    'desc'            => sanitize_textarea_field( wp_unslash( $f_descs[ $i ] ?? '' ) ),
                    'cta'             => sanitize_text_field( wp_unslash( $f_ctas[ $i ] ?? 'Tesis Detayları' ) ),
                    'url'             => sanitize_text_field( wp_unslash( $f_urls[ $i ] ?? '/tesis-olanaklari/' ) ),
                    'image'           => sanitize_text_field( wp_unslash( $f_imgs[ $i ] ?? 'facility-locker.jpg' ) ),
                    'is_live_parking' => ! empty( $f_live[ $i ] ) ? 1 : 0,
                ];
            }
            update_option( 'apex_cms_facilities_items', $new_facs );

            // İlk 3 tesis kartını fac1_*/fac2_*/fac3_* ile senkronize tut (page-tesis-olanaklari.php uyumu)
            for ( $ti = 0; $ti < 3; $ti++ ) {
                if ( isset( $new_facs[ $ti ] ) ) {
                    $n = $ti + 1;
                    if ( $n !== 2 ) {
                        $cur[ "fac{$n}_stat" ] = $new_facs[ $ti ]['stat'];
                    }
                    $cur[ "fac{$n}_role" ]  = $new_facs[ $ti ]['role'];
                    $cur[ "fac{$n}_title" ] = $new_facs[ $ti ]['title'];
                    $cur[ "fac{$n}_desc" ]  = $new_facs[ $ti ]['desc'];
                }
            }
            update_option( 'apex_site_cms', $cur );

            // Protein Kafe Menüsü
            $names = (array) ( $_POST['cafe_name'] ?? [] );
            $cats  = (array) ( $_POST['cafe_cat'] ?? [] );
            $macs  = (array) ( $_POST['cafe_macros'] ?? [] );
            $prs   = (array) ( $_POST['cafe_price'] ?? [] );
            $descs = (array) ( $_POST['cafe_desc'] ?? [] );
            $menu_items = [];
            for ( $i = 0; $i < count( $names ); $i++ ) {
                $nm = sanitize_text_field( wp_unslash( $names[ $i ] ?? '' ) );
                if ( $nm === '' ) {
                    continue;
                }
                $ct = sanitize_key( wp_unslash( $cats[ $i ] ?? 'shake' ) );
                if ( ! in_array( $ct, [ 'shake', 'bowl', 'coffee', 'snack' ], true ) ) {
                    $ct = 'shake';
                }
                $menu_items[] = [
                    'category' => $ct,
                    'name'     => $nm,
                    'macros'   => sanitize_text_field( wp_unslash( $macs[ $i ] ?? '' ) ),
                    'price'    => max( 10, absint( $prs[ $i ] ?? 150 ) ),
                    'desc'     => sanitize_text_field( wp_unslash( $descs[ $i ] ?? '' ) ),
                ];
            }
            if ( ! empty( $menu_items ) ) {
                update_option( 'apex_cms_cafe_menu', $menu_items );
            }
            $notice = '<div class="notice notice-success"><p>✅ <strong>Tesis Olanakları (' . count( $new_facs ) . ' kart) ve Kafe Menüsü (' . count( $menu_items ) . ' ürün) kaydedildi!</strong></p></div>';

        } elseif ( $active_tab === 'pages' ) {
            $pkeys = [
                'page_pt_eyebrow', 'page_pt_title', 'page_tr_eyebrow', 'page_tr_title',
                'page_courts_eyebrow', 'page_courts_title', 'page_fac_eyebrow', 'page_fac_title',
                'page_mem_eyebrow', 'page_mem_title', 'page_news_eyebrow', 'page_news_title',
                'page_contact_eyebrow', 'page_contact_title',
            ];
            foreach ( $pkeys as $pk ) {
                if ( isset( $_POST[ $pk ] ) ) {
                    $cur[ $pk ] = sanitize_text_field( wp_unslash( $_POST[ $pk ] ) );
                }
            }
            $pdescs = [ 'page_pt_desc', 'page_tr_desc', 'page_courts_desc', 'page_fac_desc', 'page_mem_desc', 'page_news_desc', 'page_contact_desc' ];
            foreach ( $pdescs as $pd ) {
                if ( isset( $_POST[ $pd ] ) ) {
                    $cur[ $pd ] = sanitize_textarea_field( wp_unslash( $_POST[ $pd ] ) );
                }
            }
            update_option( 'apex_site_cms', $cur );
            $notice = '<div class="notice notice-success"><p>✅ <strong>Tüm 7 iç sayfanın başlık ve açıklamaları kaydedildi!</strong></p></div>';
        }
    }

    // Taze verileri yükle
    $raw_cms     = get_option( 'apex_site_cms', [] );
    $c           = wp_parse_args( is_array( $raw_cms ) ? $raw_cms : [], apex_get_cms_defaults() );
    $s           = apex_get_settings();
    $plans       = apex_get_membership_plans();
    $menu        = apex_get_cafe_menu();
    $home_blocks = apex_get_home_blocks();
    $nav_items   = apex_get_nav_items();
    $hero_kpis   = apex_get_hero_kpis();
    $chapters    = apex_get_hero_chapters();
    $fac_tiles   = apex_get_facility_tiles();
    $hidden_ids  = apex_get_hidden_home_posts();
    $img_choices = apex_get_theme_image_choices();
    $tab_base    = admin_url( 'admin.php?page=apex-site-cms&tab=' );
    $cms_nonce   = wp_create_nonce( 'apex_site_cms_nonce' );
    ?>
    <style>
        .apex-cms-wrap { max-width: 1340px; margin: 16px 20px 44px 0; font-family: -apple-system, BlinkMacSystemFont, 'Plus Jakarta Sans', 'Segoe UI', sans-serif; color: #f5f5f7; }
        .apex-cms-wrap * { box-sizing: border-box; }
        .apex-cms-wrap .notice { background: #161922 !important; color: #f5f5f7 !important; border: 1px solid rgba(255,255,255,0.12) !important; border-left-width: 4px !important; border-radius: 12px; padding: 10px 16px; margin: 0 0 16px; }
        .apex-cms-wrap .notice p { color: #f5f5f7 !important; margin: 4px 0; font-size: 13.5px; }
        .apex-cms-wrap .notice-success { border-left-color: #a4ff2e !important; }
        .apex-cms-wrap .notice-info { border-left-color: #26e6ff !important; }
        .apex-cms-wrap .notice-error { border-left-color: #fa114f !important; }
        .apex-cms-banner {
            background: radial-gradient(ellipse 46% 115% at 92% 0%, rgba(250,17,79,0.22), transparent 62%), radial-gradient(ellipse 40% 100% at 8% 100%, rgba(38,230,255,0.12), transparent 60%), #111217;
            border: 1px solid rgba(255,255,255,0.09); border-radius: 20px; padding: 22px 26px; display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; margin-bottom: 18px;
        }
        .apex-cms-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 18px; }
        .apex-cms-tab {
            padding: 10px 16px; border-radius: 999px; font-size: 12.5px; font-weight: 700; text-decoration: none;
            color: #a1a1aa; background: #13151c; border: 1px solid rgba(255,255,255,0.09); transition: all 0.2s ease;
        }
        .apex-cms-tab:hover { color: #fff; border-color: rgba(255,255,255,0.22); }
        .apex-cms-tab.is-active { background: linear-gradient(135deg,#fa114f,#d40f40); color: #fff; border-color: rgba(255,120,150,0.5); box-shadow: 0 8px 22px rgba(250,17,79,0.3); }
        .apex-cms-card { background: linear-gradient(180deg,#16171d,#111216); border: 1px solid rgba(255,255,255,0.08); border-radius: 18px; padding: 22px 24px; margin-bottom: 18px; }
        .apex-cms-card-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 14px; }
        .apex-cms-card h2 { margin: 0; font-size: 16px; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 8px; }
        .apex-cms-grid-2 { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
        .apex-cms-grid-3 { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
        .apex-cms-grid-4 { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
        .apex-cms-field { display: flex; flex-direction: column; gap: 5px; min-width: 0; }
        .apex-cms-field label { font-size: 12px; font-weight: 700; color: #d4d4d8; }
        .apex-cms-wrap input[type="text"],
        .apex-cms-wrap input[type="email"],
        .apex-cms-wrap input[type="number"],
        .apex-cms-wrap textarea,
        .apex-cms-wrap select {
            background: #0c0d12 !important; border: 1px solid rgba(255,255,255,0.18) !important; border-radius: 10px;
            padding: 8px 11px; color: #ffffff !important; font-size: 13px; color-scheme: dark; width: 100%; max-width: 100%;
        }
        .apex-cms-wrap select option { background: #151720 !important; color: #ffffff !important; }
        .apex-cms-wrap textarea { min-height: 70px; line-height: 1.5; }
        .apex-cms-wrap input:focus, .apex-cms-wrap textarea:focus, .apex-cms-wrap select:focus { border-color: #fa114f !important; box-shadow: 0 0 0 2px rgba(250,17,79,0.28) !important; outline: none; }
        .apex-cms-toggles { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 10px; }
        .apex-cms-toggle {
            display: flex; align-items: center; gap: 8px; padding: 10px 12px; border-radius: 12px;
            background: #0d0f15; border: 1px solid rgba(255,255,255,0.09); font-size: 12.5px; font-weight: 600; color: #e4e4e7; cursor: pointer;
        }
        .apex-cms-btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px; border-radius: 999px; font-size: 12.5px; font-weight: 700; border: 1px solid rgba(255,255,255,0.16); background: rgba(255,255,255,0.06); color: #fff; cursor: pointer; text-decoration: none; }
        .apex-cms-btn:hover { background: rgba(255,255,255,0.14); color: #fff; }
        .apex-cms-btn-sm { padding: 5px 11px; font-size: 11.5px; }
        .apex-cms-btn-red { background: linear-gradient(135deg,#fa114f,#d40f40); border-color: rgba(255,110,145,0.45); }
        .apex-cms-btn-green { background: rgba(164,255,46,0.14); border-color: rgba(164,255,46,0.42); color: #c9ff66; }
        .apex-cms-btn-danger { background: rgba(250,17,79,0.14); border-color: rgba(250,17,79,0.38); color: #ff8aa8; }
        .apex-cms-rep-row { background: #0c0e14; border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 12px 14px; margin-bottom: 10px; }
        .apex-cms-rep-bar { display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; }
        .apex-cms-badge { font-size: 10.5px; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; padding: 3px 9px; border-radius: 999px; }
        .apex-cms-badge-builtin { background: rgba(38,230,255,0.12); color: #26e6ff; border: 1px solid rgba(38,230,255,0.28); }
        .apex-cms-badge-custom { background: rgba(164,255,46,0.14); color: #a4ff2e; border: 1px solid rgba(164,255,46,0.35); }
        .apex-cms-nav-row { display: grid; grid-template-columns: auto 1.2fr 1.6fr auto; gap: 10px; align-items: center; padding: 8px 10px; background: #0c0e14; border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; margin-bottom: 8px; }
        .apex-cms-cafe-row { display: grid; grid-template-columns: 1.2fr 0.7fr 1.1fr 0.5fr 1.5fr auto; gap: 8px; align-items: center; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.06); }
        .apex-cms-cpt-item { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; padding: 10px 12px; border-radius: 12px; background: #0c0e14; border: 1px solid rgba(255,255,255,0.08); margin-bottom: 8px; }
        .apex-cms-inline-add { margin-top: 12px; padding: 14px; border-radius: 14px; background: #0a0b10; border: 1px dashed rgba(250,17,79,0.4); }
        .apex-cms-wrap [hidden] { display: none !important; }
        .apex-cms-ord-num { display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; border-radius: 999px; background: rgba(255,255,255,0.08); color: #cbd5e1; font-size: 11px; font-weight: 800; }
        @media (max-width: 1080px) {
            .apex-cms-grid-3, .apex-cms-grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .apex-cms-grid-2, .apex-cms-nav-row, .apex-cms-cafe-row { grid-template-columns: 1fr; }
        }
    </style>

    <div class="wrap apex-cms-wrap">
        <?php echo $notice; ?>

        <div class="apex-cms-banner">
            <div>
                <div style="font-size:11px;font-weight:800;letter-spacing:0.09em;text-transform:uppercase;color:#26e6ff;margin-bottom:5px;">
                    ● MODÜLER ANA SAYFA &amp; SİTE İÇERİK STÜDYOSU · v<?php echo esc_html( APEX_VERSION ); ?>
                </div>
                <h1 style="margin:0;font-size:22px;font-weight:800;color:#fff;">Site İçerik CMS — Her Parçayı Ekle, Çıkar &amp; Sırala</h1>
                <p style="margin:6px 0 0;font-size:13px;color:#a1a1aa;">
                    Ana sayfadaki her bölümün sırasını değiştirin, özel bloklar ekleyin, Hero butonlarını/KPI’larını/kaydırma sahnelerini yönetin, salon/PT/antrenör/duyuru ekleyin veya çıkarın.
                </p>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" class="apex-cms-btn">Canlı Siteyi Aç ↗</a>
                <form method="post" onsubmit="return confirm('Tüm CMS özelleştirmeleri fabrika varsayılanlarına dönecek. Emin misiniz?');" style="margin:0;">
                    <input type="hidden" name="_wpnonce" value="<?php echo esc_attr( $cms_nonce ); ?>">
                    <button type="submit" name="apex_cms_reset" class="apex-cms-btn">↺ Varsayılana Sıfırla</button>
                </form>
            </div>
        </div>

        <nav class="apex-cms-tabs" aria-label="CMS Bölümleri">
            <a class="apex-cms-tab <?php echo $active_tab === 'home' ? 'is-active' : ''; ?>" href="<?php echo esc_url( $tab_base . 'home' ); ?>">1. Ana Sayfa Parçaları &amp; Sıralama</a>
            <a class="apex-cms-tab <?php echo $active_tab === 'items' ? 'is-active' : ''; ?>" href="<?php echo esc_url( $tab_base . 'items' ); ?>">2. Salon / PT / Antrenör / Duyuru Ekle-Çıkar</a>
            <a class="apex-cms-tab <?php echo $active_tab === 'brand' ? 'is-active' : ''; ?>" href="<?php echo esc_url( $tab_base . 'brand' ); ?>">3. Marka, Üst Menü &amp; Yüzen Bileşenler</a>
            <a class="apex-cms-tab <?php echo $active_tab === 'plans' ? 'is-active' : ''; ?>" href="<?php echo esc_url( $tab_base . 'plans' ); ?>">4. Üyelik Paketleri Ekle-Çıkar</a>
            <a class="apex-cms-tab <?php echo $active_tab === 'facilities' ? 'is-active' : ''; ?>" href="<?php echo esc_url( $tab_base . 'facilities' ); ?>">5. Tesis Kartları &amp; Protein Kafe</a>
            <a class="apex-cms-tab <?php echo $active_tab === 'pages' ? 'is-active' : ''; ?>" href="<?php echo esc_url( $tab_base . 'pages' ); ?>">6. Tüm 7 İç Sayfa Başlıkları</a>
        </nav>

        <?php if ( $active_tab === 'items' ) : ?>
            <!-- TAB 2: SALON, PT PAKETİ, ANTRENÖR VE DUYURU EKLE / ÇIKAR / ANA SAYFADA GİZLE -->
            <form method="post" id="apex-cms-items-form" style="margin-bottom:18px;">
                <input type="hidden" name="_wpnonce" value="<?php echo esc_attr( $cms_nonce ); ?>">
                <input type="hidden" name="apex_cms_tab" value="items">
                <div class="apex-cms-banner" style="padding:14px 20px;margin-bottom:0;">
                    <span style="font-size:13px;color:#e4e4e7;">Ana sayfada görmek istemediğiniz öğelerin işaretini kaldırıp kaydedin (veya sağdaki çöp butonuyla tamamen kaldırın).</span>
                    <button type="submit" name="apex_cms_save_visibility" class="apex-cms-btn apex-cms-btn-red">💾 Ana Sayfa Görünürlüğünü Kaydet</button>
                </div>

                <?php
                $cpt_sections = [
                    'apex_court' => [
                        'title' => '🏀 Maç Salonları (Basketbol & Voleybol)',
                        'btn'   => '+ Yeni Salon Ekle',
                        'meta'  => fn( $p ) => strtoupper( (string) get_post_meta( $p->ID, '_apex_sport', true ) ) . ' · ' . apex_format_tl( (int) get_post_meta( $p->ID, '_apex_hourly_rate', true ) ) . '/saat',
                    ],
                    'apex_pt_package' => [
                        'title' => '🏋️ PT Paketleri ve Grup Dersleri',
                        'btn'   => '+ Yeni PT Paketi Ekle',
                        'meta'  => fn( $p ) => strtoupper( (string) get_post_meta( $p->ID, '_apex_training_type', true ) ) . ' · ' . get_post_meta( $p->ID, '_apex_sessions', true ) . ' Seans · ' . apex_format_tl( (int) get_post_meta( $p->ID, '_apex_total_price', true ) ),
                    ],
                    'apex_trainer' => [
                        'title' => '👤 Antrenör Kadrosu',
                        'btn'   => '+ Yeni Antrenör Ekle',
                        'meta'  => fn( $p ) => get_post_meta( $p->ID, '_apex_role', true ) . ' · ' . get_post_meta( $p->ID, '_apex_experience', true ) . ' Yıl',
                    ],
                    'apex_announcement' => [
                        'title' => '📢 Salon Duyuruları & Turnuvalar',
                        'btn'   => '+ Yeni Duyuru Ekle',
                        'meta'  => fn( $p ) => ( get_post_meta( $p->ID, '_apex_ann_category', true ) ?: 'Duyuru' ) . ' · ' . ( get_post_meta( $p->ID, '_apex_ann_date', true ) ?: get_the_date( 'd.m.Y', $p->ID ) ),
                    ],
                ];
                ?>
                <div class="apex-cms-grid-2" style="margin-top:16px;">
                    <?php foreach ( $cpt_sections as $cpt_key => $cmeta ) :
                        $posts_list = get_posts( [ 'post_type' => $cpt_key, 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC' ] );
                    ?>
                    <div class="apex-cms-card" style="margin-bottom:0;">
                        <div class="apex-cms-card-head">
                            <h2><?php echo esc_html( $cmeta['title'] ); ?> (<?php echo count( $posts_list ); ?>)</h2>
                            <button type="button" class="apex-cms-btn apex-cms-btn-green apex-cms-btn-sm" data-toggle-box="add-box-<?php echo esc_attr( $cpt_key ); ?>"><?php echo esc_html( $cmeta['btn'] ); ?></button>
                        </div>

                        <?php foreach ( $posts_list as $item ) :
                            $is_shown = ! in_array( (int) $item->ID, $hidden_ids, true );
                        ?>
                        <div class="apex-cms-cpt-item">
                            <input type="hidden" name="all_home_cpt_ids[]" value="<?php echo esc_attr( $item->ID ); ?>">
                            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;flex:1;min-width:0;">
                                <input type="checkbox" name="show_home_cpt_ids[]" value="<?php echo esc_attr( $item->ID ); ?>" <?php checked( $is_shown ); ?>>
                                <div style="min-width:0;">
                                    <strong style="display:block;color:#fff;font-size:13.5px;"><?php echo esc_html( $item->post_title ); ?></strong>
                                    <small style="color:#8c90a0;font-size:11.5px;"><?php echo esc_html( $cmeta['meta']( $item ) ); ?></small>
                                </div>
                            </label>
                            <div style="display:flex;gap:6px;align-items:center;">
                                <a href="<?php echo esc_url( get_edit_post_link( $item->ID ) ); ?>" class="apex-cms-btn apex-cms-btn-sm" target="_blank">Düzenle</a>
                                <button type="submit" name="apex_cms_trash_post_id" value="<?php echo (int) $item->ID; ?>" class="apex-cms-btn apex-cms-btn-danger apex-cms-btn-sm" aria-label="<?php echo esc_attr( 'Çöpe Taşı: ' . $item->post_title ); ?>" onclick="return confirm('<?php echo esc_js( $item->post_title ); ?> kaydını çöpe taşımak (kaldırmak) istediğinize emin misiniz?');">🗑 Çıkar</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if ( empty( $posts_list ) ) : ?>
                            <p style="color:#8c90a0;font-size:13px;">Henüz kayıt yok. Sağ üstteki butondan ekleyin.</p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </form>

            <!-- Hızlı Yeni Öğe Ekleme Kutuları (Her CPT İçin) -->
            <div class="apex-cms-grid-2">
                <div class="apex-cms-card apex-cms-inline-add" id="add-box-apex_court" hidden>
                    <h2 style="margin-bottom:12px;">+ Yeni Maç Salonu Ekle</h2>
                    <form method="post" style="display:grid;gap:10px;">
                        <input type="hidden" name="_wpnonce" value="<?php echo esc_attr( $cms_nonce ); ?>">
                        <input type="hidden" name="apex_cms_tab" value="items">
                        <input type="hidden" name="add_cpt_type" value="apex_court">
                        <div class="apex-cms-grid-2">
                            <div class="apex-cms-field"><label>Salon Adı *</label><input type="text" name="new_title" placeholder="Örn. Arena Basketbol 3" required></div>
                            <div class="apex-cms-field"><label>Branş</label><select name="new_sport"><option value="basketbol">Basketbol</option><option value="voleybol">Voleybol</option></select></div>
                            <div class="apex-cms-field"><label>Ölçüler</label><input type="text" name="new_dim" value="28×15m FIBA"></div>
                            <div class="apex-cms-field"><label>Zemin</label><input type="text" name="new_surface" value="FIBA Akçaağaç Parke"></div>
                            <div class="apex-cms-field"><label>Saatlik Ücret (₺)</label><input type="number" name="new_rate" value="1600"></div>
                            <div class="apex-cms-field"><label>Görsel Dosyası</label>
                                <select name="new_photo"><?php foreach ( $img_choices as $im ) : ?><option value="<?php echo esc_attr( $im ); ?>" <?php selected( $im, 'court-basketball-1.jpg' ); ?>><?php echo esc_html( $im ); ?></option><?php endforeach; ?></select>
                            </div>
                        </div>
                        <div class="apex-cms-field"><label>Kısa Açıklama</label><textarea name="new_content" placeholder="Salon özellikleri..."></textarea></div>
                        <button type="submit" name="apex_cms_add_cpt" class="apex-cms-btn apex-cms-btn-red">✓ Salonu Oluştur ve Ana Sayfaya Ekle</button>
                    </form>
                </div>

                <div class="apex-cms-card apex-cms-inline-add" id="add-box-apex_pt_package" hidden>
                    <h2 style="margin-bottom:12px;">+ Yeni PT Paketi / Grup Programı Ekle</h2>
                    <form method="post" style="display:grid;gap:10px;">
                        <input type="hidden" name="_wpnonce" value="<?php echo esc_attr( $cms_nonce ); ?>">
                        <input type="hidden" name="apex_cms_tab" value="items">
                        <input type="hidden" name="add_cpt_type" value="apex_pt_package">
                        <div class="apex-cms-grid-2">
                            <div class="apex-cms-field"><label>Paket Adı *</label><input type="text" name="new_title" placeholder="Örn. Atletik Hız 10 Seans" required></div>
                            <div class="apex-cms-field"><label>Tür</label><select name="new_pt_type"><option value="bireysel">Bireysel PT</option><option value="grupsal">Grupsal</option></select></div>
                            <div class="apex-cms-field"><label>Seans Sayısı</label><input type="number" name="new_sessions" value="10"></div>
                            <div class="apex-cms-field"><label>Toplam Fiyat (₺)</label><input type="number" name="new_total_price" value="11000"></div>
                            <div class="apex-cms-field"><label>Seans Başı Ücret (₺)</label><input type="number" name="new_per_fee" value="1100"></div>
                            <div class="apex-cms-field"><label>Eğitim Günleri</label><input type="text" name="new_days" value="Salı · Perşembe · Cumartesi"></div>
                        </div>
                        <div class="apex-cms-field"><label>Kapsam Maddeleri (Her satır 1 madde)</label><textarea name="new_scope">Birebir teknik analiz
InBody 570 ölçümü
Haftalık makro planı</textarea></div>
                        <button type="submit" name="apex_cms_add_cpt" class="apex-cms-btn apex-cms-btn-red">✓ PT Paketini Oluştur</button>
                    </form>
                </div>

                <div class="apex-cms-card apex-cms-inline-add" id="add-box-apex_trainer" hidden>
                    <h2 style="margin-bottom:12px;">+ Yeni Antrenör Ekle</h2>
                    <form method="post" style="display:grid;gap:10px;">
                        <input type="hidden" name="_wpnonce" value="<?php echo esc_attr( $cms_nonce ); ?>">
                        <input type="hidden" name="apex_cms_tab" value="items">
                        <input type="hidden" name="add_cpt_type" value="apex_trainer">
                        <div class="apex-cms-grid-2">
                            <div class="apex-cms-field"><label>Ad Soyad *</label><input type="text" name="new_title" placeholder="Örn. Emre Kaya" required></div>
                            <div class="apex-cms-field"><label>Ünvan</label><input type="text" name="new_role" value="Performans & Kuvvet Koçu"></div>
                            <div class="apex-cms-field"><label>Uzmanlık Alanı</label><input type="text" name="new_spec" value="Hipertrofi, Mobilite & Atletizm"></div>
                            <div class="apex-cms-field"><label>Deneyim (Yıl) / Puan</label><div style="display:flex;gap:6px;"><input type="number" name="new_exp" value="7"><input type="text" name="new_rating" value="4.9"></div></div>
                            <div class="apex-cms-field"><label>Seans Ücreti (₺)</label><input type="number" name="new_fee" value="1450"></div>
                            <div class="apex-cms-field"><label>Portre Fotoğrafı</label>
                                <select name="new_photo"><?php foreach ( $img_choices as $im ) : ?><option value="<?php echo esc_attr( $im ); ?>" <?php selected( $im, 'trainer-kerem.jpg' ); ?>><?php echo esc_html( $im ); ?></option><?php endforeach; ?></select>
                            </div>
                        </div>
                        <div class="apex-cms-field"><label>Özgeçmiş / Biyografi</label><textarea name="new_content" placeholder="Antrenör özgeçmişi..."></textarea></div>
                        <button type="submit" name="apex_cms_add_cpt" class="apex-cms-btn apex-cms-btn-red">✓ Antrenörü Ekle</button>
                    </form>
                </div>

                <div class="apex-cms-card apex-cms-inline-add" id="add-box-apex_announcement" hidden>
                    <h2 style="margin-bottom:12px;">+ Yeni Salon Duyurusu / Turnuva Ekle</h2>
                    <form method="post" style="display:grid;gap:10px;">
                        <input type="hidden" name="_wpnonce" value="<?php echo esc_attr( $cms_nonce ); ?>">
                        <input type="hidden" name="apex_cms_tab" value="items">
                        <input type="hidden" name="add_cpt_type" value="apex_announcement">
                        <div class="apex-cms-grid-3">
                            <div class="apex-cms-field"><label>Duyuru Başlığı *</label><input type="text" name="new_title" placeholder="Örn. Kış 3x3 Basketbol Kupası" required></div>
                            <div class="apex-cms-field"><label>Kategori</label><select name="new_cat"><option value="Turnuva">Turnuva</option><option value="Salon Duyurusu">Salon Duyurusu</option><option value="Kampanya">Kampanya</option><option value="Grup Dersi">Grup Dersi</option></select></div>
                            <div class="apex-cms-field"><label>Tarih &amp; Konum</label><div style="display:flex;gap:6px;"><input type="text" name="new_date" value="<?php echo esc_attr( date( 'd.m.Y' ) ); ?>"><input type="text" name="new_loc" value="Arena 1"></div></div>
                        </div>
                        <div class="apex-cms-field"><label>Duyuru Metni</label><textarea name="new_content" placeholder="Etkinlik veya duyuru detayları..."></textarea></div>
                        <button type="submit" name="apex_cms_add_cpt" class="apex-cms-btn apex-cms-btn-red">✓ Duyuruyu Yayınla</button>
                    </form>
                </div>
            </div>

        <?php else : ?>
        <form method="post">
            <input type="hidden" name="_wpnonce" value="<?php echo esc_attr( $cms_nonce ); ?>">
            <input type="hidden" name="apex_cms_tab" value="<?php echo esc_attr( $active_tab ); ?>">

            <?php if ( $active_tab === 'home' ) : ?>
                <!-- 1. BÖLÜM SIRALAMA, GÖSTER/GİZLE & ÖZEL BLOK EKLEME -->
                <div class="apex-cms-card">
                    <div class="apex-cms-card-head">
                        <div>
                            <h2>Ana Sayfa Bölüm Sırası, Görünürlük &amp; Özel Bloklar</h2>
                            <p style="margin:4px 0 0;font-size:12.5px;color:#9ca3af;">Yukarı/aşağı butonlarıyla bölümleri yeniden sıralayın, tikleri kaldırıp gizleyin veya <strong>+ Yeni Özel Bölüm Ekle</strong> ile sıfırdan bölüm ekleyin.</p>
                        </div>
                        <button type="button" class="apex-cms-btn apex-cms-btn-green" id="apex-add-custom-block-btn">+ Yeni Özel Bölüm Ekle</button>
                    </div>

                    <div id="apex-blocks-list">
                        <?php foreach ( $home_blocks as $bi => $blk ) :
                            $is_custom = ( $blk['type'] === 'custom' );
                        ?>
                        <div class="apex-cms-rep-row" data-rep-item>
                            <input type="hidden" name="blk_id[]" value="<?php echo esc_attr( $blk['id'] ); ?>">
                            <input type="hidden" name="blk_type[]" value="<?php echo esc_attr( $blk['type'] ); ?>">
                            <div class="apex-cms-rep-bar">
                                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                                    <span class="apex-cms-ord-num"><?php echo (int) ( $bi + 1 ); ?></span>
                                    <label style="display:inline-flex;align-items:center;gap:6px;cursor:pointer;font-weight:700;color:#fff;">
                                        <input type="hidden" name="blk_enabled[]" value="<?php echo $blk['enabled'] ? '1' : '0'; ?>" data-cb-mirror>
                                        <input type="checkbox" <?php checked( $blk['enabled'] ); ?> onchange="this.previousElementSibling.value=this.checked?'1':'0'">
                                        <span class="blk-display-title"><?php echo esc_html( $blk['label'] ); ?></span>
                                    </label>
                                    <span class="apex-cms-badge <?php echo $is_custom ? 'apex-cms-badge-custom' : 'apex-cms-badge-builtin'; ?>">
                                        <?php echo $is_custom ? 'Özel Blok' : 'Yerleşik'; ?>
                                    </span>
                                </div>
                                <div style="display:flex;gap:6px;align-items:center;">
                                    <button type="button" class="apex-cms-btn apex-cms-btn-sm" data-rep-up title="Yukarı Taşı" aria-label="<?php echo esc_attr( 'Yukarı Taşı: ' . $blk['label'] ); ?>">↑</button>
                                    <button type="button" class="apex-cms-btn apex-cms-btn-sm" data-rep-down title="Aşağı Taşı" aria-label="<?php echo esc_attr( 'Aşağı Taşı: ' . $blk['label'] ); ?>">↓</button>
                                    <?php if ( $is_custom ) : ?>
                                        <button type="button" class="apex-cms-btn apex-cms-btn-sm" data-toggle-custom-edit aria-label="<?php echo esc_attr( 'Düzenle: ' . $blk['label'] ); ?>">Düzenle</button>
                                        <button type="button" class="apex-cms-btn apex-cms-btn-danger apex-cms-btn-sm" data-rep-del aria-label="<?php echo esc_attr( 'Sil: ' . $blk['label'] ); ?>">✕ Sil</button>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if ( $is_custom ) : ?>
                            <div class="apex-custom-edit-body apex-cms-grid-3" style="margin-top:12px;padding-top:12px;border-top:1px solid rgba(255,255,255,0.08);" hidden>
                                <div class="apex-cms-field"><label>Şablon Tipi</label>
                                    <select name="blk_layout[]">
                                        <option value="cta_banner" <?php selected( $blk['layout'], 'cta_banner' ); ?>>Tanıtım / Kampanya Bandı</option>
                                        <option value="feature_cards" <?php selected( $blk['layout'], 'feature_cards' ); ?>>Çoklu Özellik Kartları</option>
                                        <option value="split_media" <?php selected( $blk['layout'], 'split_media' ); ?>>Görsel + Metin (Split)</option>
                                    </select>
                                </div>
                                <div class="apex-cms-field"><label>Üst Etiket (Eyebrow)</label><input type="text" name="blk_eyebrow[]" value="<?php echo esc_attr( $blk['eyebrow'] ); ?>"></div>
                                <div class="apex-cms-field"><label>Bölüm Başlığı</label><input type="text" name="blk_title[]" value="<?php echo esc_attr( $blk['title'] ); ?>" oninput="this.closest('[data-rep-item]').querySelector('.blk-display-title').textContent=this.value"></div>
                                <div class="apex-cms-field"><label>Buton Metni</label><input type="text" name="blk_cta_text[]" value="<?php echo esc_attr( $blk['cta_text'] ); ?>"></div>
                                <div class="apex-cms-field"><label>Buton Linki</label><input type="text" name="blk_cta_url[]" value="<?php echo esc_attr( $blk['cta_url'] ); ?>"></div>
                                <div class="apex-cms-field"><label>Görsel (Split Şablonu İçin)</label>
                                    <select name="blk_image[]"><?php foreach ( $img_choices as $im ) : ?><option value="<?php echo esc_attr( $im ); ?>" <?php selected( $blk['image'], $im ); ?>><?php echo esc_html( $im ); ?></option><?php endforeach; ?></select>
                                </div>
                                <div class="apex-cms-field" style="grid-column:1/-1;"><label>Açıklama</label><textarea name="blk_desc[]"><?php echo esc_textarea( $blk['desc'] ); ?></textarea></div>
                                <div class="apex-cms-field" style="grid-column:1/-1;"><label>Özellik Kartları (Her satır: Başlık | Açıklama | Etiket — yalnızca Kart şablonunda)</label><textarea name="blk_cards_raw[]"><?php echo esc_textarea( $blk['cards_raw'] ); ?></textarea></div>
                            </div>
                            <?php else : ?>
                                <input type="hidden" name="blk_layout[]" value="">
                                <input type="hidden" name="blk_eyebrow[]" value="">
                                <input type="hidden" name="blk_title[]" value="">
                                <input type="hidden" name="blk_desc[]" value="">
                                <input type="hidden" name="blk_cta_text[]" value="">
                                <input type="hidden" name="blk_cta_url[]" value="">
                                <input type="hidden" name="blk_image[]" value="">
                                <input type="hidden" name="blk_cards_raw[]" value="">
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- 2. HERO PARÇA KONTROLLERİ, KPI KARTLARI & KAYDIRMA HİKAYE SAHNELERİ -->
                <div class="apex-cms-card">
                    <h2>Hero Parçaları (Tek Tek Aç / Kapa &amp; Düzenle)</h2>
                    <div class="apex-cms-toggles" style="margin-bottom:14px;">
                        <label class="apex-cms-toggle"><input type="checkbox" name="show_hero_live_pill" value="1" <?php checked( ! empty( $c['show_hero_live_pill'] ) ); ?>><span>Canlı Yoğunluk &amp; Otopark Rozeti</span></label>
                        <label class="apex-cms-toggle"><input type="checkbox" name="show_hero_cta1" value="1" <?php checked( ! empty( $c['show_hero_cta1'] ) ); ?>><span>1. Ana Buton (Üyeliği Başlat)</span></label>
                        <label class="apex-cms-toggle"><input type="checkbox" name="show_hero_cta2" value="1" <?php checked( ! empty( $c['show_hero_cta2'] ) ); ?>><span>2. Buton (Salon Kirala)</span></label>
                        <label class="apex-cms-toggle"><input type="checkbox" name="show_hero_ai_btn" value="1" <?php checked( ! empty( $c['show_hero_ai_btn'] ) ); ?>><span>AI Spor Koçu Butonu</span></label>
                        <label class="apex-cms-toggle"><input type="checkbox" name="show_hero_kpis" value="1" <?php checked( ! empty( $c['show_hero_kpis'] ) ); ?>><span>Alt 4 KPI İstatistik Kartı</span></label>
                        <label class="apex-cms-toggle"><input type="checkbox" name="show_hero_chapters" value="1" <?php checked( ! empty( $c['show_hero_chapters'] ) ); ?>><span>Kaydırma Hikaye Sahneleri (01-04)</span></label>
                    </div>
                    <div class="apex-cms-grid-3">
                        <div class="apex-cms-field"><label>Hero Ana Başlık</label><input type="text" name="hero_title" value="<?php echo esc_attr( $c['hero_title'] ); ?>"></div>
                        <div class="apex-cms-field"><label>1. Buton Metni &amp; Linki</label><div style="display:flex;gap:6px;"><input type="text" name="hero_cta1" value="<?php echo esc_attr( $c['hero_cta1'] ); ?>"><input type="text" name="hero_cta1_url" value="<?php echo esc_attr( $c['hero_cta1_url'] ); ?>"></div></div>
                        <div class="apex-cms-field"><label>2. Buton Metni &amp; Linki</label><div style="display:flex;gap:6px;"><input type="text" name="hero_cta2" value="<?php echo esc_attr( $c['hero_cta2'] ); ?>"><input type="text" name="hero_cta2_url" value="<?php echo esc_attr( $c['hero_cta2_url'] ); ?>"></div></div>
                        <div class="apex-cms-field" style="grid-column:span 2;"><label>Hero Alt Açıklama</label><textarea name="hero_subtitle"><?php echo esc_textarea( $c['hero_subtitle'] ); ?></textarea></div>
                        <div class="apex-cms-field"><label>AI Butonu Etiketi</label><input type="text" name="hero_ai_label" value="<?php echo esc_attr( $c['hero_ai_label'] ); ?>"></div>
                    </div>

                    <div class="apex-cms-card-head" style="margin-top:18px;">
                        <h3 style="margin:0;font-size:13.5px;color:#fff;">Hero KPI Kartları (Ekle / Çıkar / Sırala)</h3>
                        <button type="button" class="apex-cms-btn apex-cms-btn-green apex-cms-btn-sm" id="apex-add-kpi-btn">+ Yeni KPI Kartı Ekle</button>
                    </div>
                    <div id="apex-kpis-list">
                        <?php foreach ( $hero_kpis as $kp ) : ?>
                        <div class="apex-cms-nav-row" data-rep-item style="grid-template-columns:auto 1fr 1.6fr auto;">
                            <div style="display:flex;gap:4px;"><button type="button" class="apex-cms-btn apex-cms-btn-sm" data-rep-up>↑</button><button type="button" class="apex-cms-btn apex-cms-btn-sm" data-rep-down>↓</button></div>
                            <input type="text" name="kpi_val[]" value="<?php echo esc_attr( $kp['val'] ); ?>" placeholder="Başlık (Örn. 3 Salon)">
                            <input type="text" name="kpi_lbl[]" value="<?php echo esc_attr( $kp['lbl'] ); ?>" placeholder="Alt Açıklama">
                            <button type="button" class="apex-cms-btn apex-cms-btn-danger apex-cms-btn-sm" data-rep-del>✕ Sil</button>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="apex-cms-card-head" style="margin-top:18px;">
                        <h3 style="margin:0;font-size:13.5px;color:#fff;">Kaydırma Hikaye Sahneleri (Scroll Cinema 01–04)</h3>
                        <button type="button" class="apex-cms-btn apex-cms-btn-green apex-cms-btn-sm" id="apex-add-chapter-btn">+ Yeni Sahne Ekle</button>
                    </div>
                    <div id="apex-chapters-list">
                        <?php foreach ( $chapters as $ch ) : ?>
                        <div class="apex-cms-rep-row" data-rep-item>
                            <div class="apex-cms-rep-bar" style="margin-bottom:8px;">
                                <label style="display:inline-flex;align-items:center;gap:6px;cursor:pointer;font-weight:700;color:#fff;">
                                    <input type="hidden" name="ch_enabled[]" value="<?php echo ! empty( $ch['enabled'] ) ? '1' : '0'; ?>" data-cb-mirror>
                                    <input type="checkbox" <?php checked( ! empty( $ch['enabled'] ) ); ?> onchange="this.previousElementSibling.value=this.checked?'1':'0'">
                                    <span>Aktif Sahne</span>
                                </label>
                                <div style="display:flex;gap:6px;">
                                    <button type="button" class="apex-cms-btn apex-cms-btn-sm" data-rep-up>↑</button>
                                    <button type="button" class="apex-cms-btn apex-cms-btn-sm" data-rep-down>↓</button>
                                    <button type="button" class="apex-cms-btn apex-cms-btn-danger apex-cms-btn-sm" data-rep-del>✕ Sil</button>
                                </div>
                            </div>
                            <div class="apex-cms-grid-3">
                                <div class="apex-cms-field"><label>Üst Rozet</label><input type="text" name="ch_badge[]" value="<?php echo esc_attr( $ch['badge'] ?? '' ); ?>"></div>
                                <div class="apex-cms-field"><label>Sahne Başlığı</label><input type="text" name="ch_title[]" value="<?php echo esc_attr( $ch['title'] ?? '' ); ?>"></div>
                                <div class="apex-cms-field"><label>1. Buton (Metin · Link)</label><div style="display:flex;gap:6px;"><input type="text" name="ch_cta1_text[]" value="<?php echo esc_attr( $ch['cta1_text'] ?? '' ); ?>"><input type="text" name="ch_cta1_url[]" value="<?php echo esc_attr( $ch['cta1_url'] ?? '' ); ?>"></div></div>
                                <div class="apex-cms-field" style="grid-column:span 2;"><label>Açıklama</label><input type="text" name="ch_desc[]" value="<?php echo esc_attr( $ch['desc'] ?? '' ); ?>"></div>
                                <div class="apex-cms-field"><label>2. Buton (Opsiyonel)</label><div style="display:flex;gap:6px;"><input type="text" name="ch_cta2_text[]" value="<?php echo esc_attr( $ch['cta2_text'] ?? '' ); ?>" placeholder="Metin"><input type="text" name="ch_cta2_url[]" value="<?php echo esc_attr( $ch['cta2_url'] ?? '' ); ?>" placeholder="Link"></div></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- 3. 3D DAMBIL & BÖLÜM BAŞLIKLARI + ÖĞE LİMİTLERİ -->
                <div class="apex-cms-card">
                    <h2>3D Dambıl Tipografisi, Bölüm Başlıkları &amp; Öğe Sayısı Limitleri</h2>
                    <div class="apex-cms-grid-3" style="margin-bottom:14px;">
                        <div class="apex-cms-field"><label>3D Monolit Satır 1</label><input type="text" name="monolith_line1" value="<?php echo esc_attr( $c['monolith_line1'] ); ?>"></div>
                        <div class="apex-cms-field"><label>3D Monolit Satır 2</label><input type="text" name="monolith_line2" value="<?php echo esc_attr( $c['monolith_line2'] ); ?>"></div>
                        <div class="apex-cms-field"><label>3D Sağ Alt Manifesto</label><textarea name="monolith_manifesto"><?php echo esc_textarea( $c['monolith_manifesto'] ); ?></textarea></div>
                    </div>
                    <div class="apex-cms-grid-4" style="margin-bottom:14px;">
                        <div class="apex-cms-field"><label>Salon Limiti (0=Tümü)</label><input type="number" name="home_courts_limit" min="0" value="<?php echo esc_attr( $c['home_courts_limit'] ); ?>"></div>
                        <div class="apex-cms-field"><label>PT Limiti (0=Tümü)</label><input type="number" name="home_pt_limit" min="0" value="<?php echo esc_attr( $c['home_pt_limit'] ); ?>"></div>
                        <div class="apex-cms-field"><label>Antrenör Limiti (0=Tümü)</label><input type="number" name="home_tr_limit" min="0" value="<?php echo esc_attr( $c['home_tr_limit'] ); ?>"></div>
                        <div class="apex-cms-field"><label>Duyuru / Yorum Limiti</label><div style="display:flex;gap:6px;"><input type="number" name="home_news_limit" min="0" value="<?php echo esc_attr( $c['home_news_limit'] ); ?>" title="Duyuru"><input type="number" name="home_reviews_limit" min="0" value="<?php echo esc_attr( $c['home_reviews_limit'] ); ?>" title="Yorum"></div></div>
                    </div>
                    <div class="apex-cms-grid-2">
                        <div class="apex-cms-field"><label>Salonlar — Üst Etiket &amp; Başlık</label><input type="text" name="home_courts_eyebrow" value="<?php echo esc_attr( $c['home_courts_eyebrow'] ); ?>" style="margin-bottom:5px;"><input type="text" name="home_courts_title" value="<?php echo esc_attr( $c['home_courts_title'] ); ?>"></div>
                        <div class="apex-cms-field"><label>Salonlar — Açıklama</label><textarea name="home_courts_desc"><?php echo esc_textarea( $c['home_courts_desc'] ); ?></textarea></div>
                        <div class="apex-cms-field"><label>PT Paketleri — Üst Etiket &amp; Başlık</label><input type="text" name="home_pt_eyebrow" value="<?php echo esc_attr( $c['home_pt_eyebrow'] ); ?>" style="margin-bottom:5px;"><input type="text" name="home_pt_title" value="<?php echo esc_attr( $c['home_pt_title'] ); ?>"></div>
                        <div class="apex-cms-field"><label>PT Paketleri — Açıklama</label><textarea name="home_pt_desc"><?php echo esc_textarea( $c['home_pt_desc'] ); ?></textarea></div>
                        <div class="apex-cms-field"><label>Antrenörler — Üst Etiket &amp; Başlık</label><input type="text" name="home_tr_eyebrow" value="<?php echo esc_attr( $c['home_tr_eyebrow'] ); ?>" style="margin-bottom:5px;"><input type="text" name="home_tr_title" value="<?php echo esc_attr( $c['home_tr_title'] ); ?>"></div>
                        <div class="apex-cms-field"><label>Antrenörler — Açıklama</label><textarea name="home_tr_desc"><?php echo esc_textarea( $c['home_tr_desc'] ); ?></textarea></div>
                        <div class="apex-cms-field"><label>3D Mimari Kesit — Üst Etiket &amp; Başlık</label><input type="text" name="home_arch_eyebrow" value="<?php echo esc_attr( $c['home_arch_eyebrow'] ); ?>" style="margin-bottom:5px;"><input type="text" name="home_arch_title" value="<?php echo esc_attr( $c['home_arch_title'] ); ?>"></div>
                        <div class="apex-cms-field"><label>3D Mimari Kesit — Açıklama</label><textarea name="home_arch_desc"><?php echo esc_textarea( $c['home_arch_desc'] ); ?></textarea></div>
                        <div class="apex-cms-field"><label>Tesis Olanakları — Üst Etiket &amp; Başlık</label><input type="text" name="home_fac_eyebrow" value="<?php echo esc_attr( $c['home_fac_eyebrow'] ); ?>" style="margin-bottom:5px;"><input type="text" name="home_fac_title" value="<?php echo esc_attr( $c['home_fac_title'] ); ?>"></div>
                        <div class="apex-cms-field"><label>Üyelik Paketleri — Üst Etiket &amp; Başlık</label><input type="text" name="home_mem_eyebrow" value="<?php echo esc_attr( $c['home_mem_eyebrow'] ); ?>" style="margin-bottom:5px;"><input type="text" name="home_mem_title" value="<?php echo esc_attr( $c['home_mem_title'] ); ?>"></div>
                        <div class="apex-cms-field"><label>Duyurular — Üst Etiket &amp; Başlık</label><input type="text" name="home_news_eyebrow" value="<?php echo esc_attr( $c['home_news_eyebrow'] ); ?>" style="margin-bottom:5px;"><input type="text" name="home_news_title" value="<?php echo esc_attr( $c['home_news_title'] ); ?>"></div>
                        <div class="apex-cms-field">
                            <label>Üye Yorumları — Üst Etiket, Başlık &amp; Form</label>
                            <input type="text" name="home_rev_eyebrow" value="<?php echo esc_attr( $c['home_rev_eyebrow'] ); ?>" style="margin-bottom:5px;">
                            <input type="text" name="home_rev_title" value="<?php echo esc_attr( $c['home_rev_title'] ); ?>" style="margin-bottom:6px;">
                            <label style="display:flex;align-items:center;gap:6px;font-weight:600;cursor:pointer;"><input type="checkbox" name="show_review_form" value="1" <?php checked( ! empty( $c['show_review_form'] ) ); ?>> Yorum Gönderme Formunu Ana Sayfada Göster</label>
                        </div>
                    </div>
                </div>

            <?php elseif ( $active_tab === 'brand' ) : ?>
                <div class="apex-cms-card">
                    <h2>Marka Kimliği, Vurgu Rengi &amp; Global Bileşen Anahtarları</h2>
                    <div class="apex-cms-grid-3" style="margin-bottom:14px;">
                        <div class="apex-cms-field"><label>Marka İlk Kelime</label><input type="text" name="brand_first" value="<?php echo esc_attr( $c['brand_first'] ); ?>"></div>
                        <div class="apex-cms-field"><label>Marka İkinci Kelime</label><input type="text" name="brand_second" value="<?php echo esc_attr( $c['brand_second'] ); ?>"></div>
                        <div class="apex-cms-field">
                            <label>Ana Vurgu Rengi (Accent Color)</label>
                            <div style="display:flex;gap:8px;align-items:center;">
                                <input type="color" name="accent_color" value="<?php echo esc_attr( $c['accent_color'] ); ?>" style="width:52px;height:38px;border:0;background:none;cursor:pointer;">
                                <span style="font-size:12.5px;color:#a1a1aa;">Varsayılan: #E31837</span>
                            </div>
                        </div>
                    </div>
                    <div class="apex-cms-toggles" style="margin-bottom:16px;">
                        <label class="apex-cms-toggle"><input type="checkbox" name="show_prefooter" value="1" <?php checked( ! empty( $c['show_prefooter'] ) ); ?>><span>Alt Pre-Footer CTA Bandı</span></label>
                        <label class="apex-cms-toggle"><input type="checkbox" name="show_whatsapp" value="1" <?php checked( ! empty( $c['show_whatsapp'] ) ); ?>><span>Yüzen WhatsApp Hattı</span></label>
                        <label class="apex-cms-toggle"><input type="checkbox" name="show_ai_widget" value="1" <?php checked( ! empty( $c['show_ai_widget'] ) ); ?>><span>Yüzen Apex AI Koç Balonu</span></label>
                        <label class="apex-cms-toggle"><input type="checkbox" name="show_a11y_widget" value="1" <?php checked( ! empty( $c['show_a11y_widget'] ) ); ?>><span>WCAG Erişilebilirlik Merkezi</span></label>
                    </div>

                    <div class="apex-cms-card-head">
                        <h3 style="margin:0;font-size:14px;color:#fff;">Üst Navigasyon Menü Bağlantıları (Ekle / Çıkar / Sırala)</h3>
                        <button type="button" class="apex-cms-btn apex-cms-btn-green apex-cms-btn-sm" id="apex-add-nav-btn">+ Yeni Menü Linki Ekle</button>
                    </div>
                    <div id="apex-nav-list">
                        <?php foreach ( $nav_items as $ni ) : ?>
                        <div class="apex-cms-nav-row" data-rep-item>
                            <div style="display:flex;gap:6px;align-items:center;">
                                <input type="hidden" name="nav_item_enabled[]" value="<?php echo ! empty( $ni['enabled'] ) ? '1' : '0'; ?>" data-cb-mirror>
                                <input type="checkbox" <?php checked( ! empty( $ni['enabled'] ) ); ?> onchange="this.previousElementSibling.value=this.checked?'1':'0'" title="Göster">
                                <button type="button" class="apex-cms-btn apex-cms-btn-sm" data-rep-up>↑</button>
                                <button type="button" class="apex-cms-btn apex-cms-btn-sm" data-rep-down>↓</button>
                            </div>
                            <input type="text" name="nav_item_label[]" value="<?php echo esc_attr( $ni['label'] ); ?>" placeholder="Menü Etiketi">
                            <input type="text" name="nav_item_url[]" value="<?php echo esc_attr( $ni['url'] ); ?>" placeholder="/sayfa-yolu/ veya https://...">
                            <button type="button" class="apex-cms-btn apex-cms-btn-danger apex-cms-btn-sm" data-rep-del>✕ Sil</button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="apex-cms-grid-2" style="margin-top:12px;">
                        <div class="apex-cms-field"><label>Üst Menü Giriş Butonu Etiketi</label><input type="text" name="nav_login_label" value="<?php echo esc_attr( $c['nav_login_label'] ); ?>"></div>
                        <div class="apex-cms-field"><label>Üst Menü Kayıt Butonu Etiketi</label><input type="text" name="nav_join_label" value="<?php echo esc_attr( $c['nav_join_label'] ); ?>"></div>
                    </div>
                </div>

                <div class="apex-cms-card">
                    <h2>İletişim, WhatsApp, Harita &amp; Footer</h2>
                    <div class="apex-cms-grid-3">
                        <div class="apex-cms-field"><label>Telefon</label><input type="text" name="site_phone" value="<?php echo esc_attr( $s['phone'] ); ?>"></div>
                        <div class="apex-cms-field"><label>Kurumsal E-posta</label><input type="email" name="site_email" value="<?php echo esc_attr( $s['email'] ); ?>"></div>
                        <div class="apex-cms-field"><label>WhatsApp Numarası</label><input type="text" name="whatsapp_phone" value="<?php echo esc_attr( $c['whatsapp_phone'] ); ?>"></div>
                        <div class="apex-cms-field"><label>Adres</label><input type="text" name="site_address" value="<?php echo esc_attr( $s['address'] ); ?>"></div>
                        <div class="apex-cms-field"><label>Hafta İçi / Hafta Sonu Saatleri</label><div style="display:flex;gap:6px;"><input type="text" name="site_weekday_hours" value="<?php echo esc_attr( $s['weekday_hours'] ); ?>"><input type="text" name="site_weekend_hours" value="<?php echo esc_attr( $s['weekend_hours'] ); ?>"></div></div>
                        <div class="apex-cms-field"><label>Google Harita Konum Sorgusu</label><input type="text" name="map_query" value="<?php echo esc_attr( $c['map_query'] ); ?>"></div>
                    </div>
                    <div class="apex-cms-grid-2" style="margin-top:14px;">
                        <div class="apex-cms-field"><label>Pre-Footer Üst Etiket</label><input type="text" name="prefooter_eyebrow" value="<?php echo esc_attr( $c['prefooter_eyebrow'] ); ?>"></div>
                        <div class="apex-cms-field"><label>Pre-Footer Başlık</label><input type="text" name="prefooter_title" value="<?php echo esc_attr( $c['prefooter_title'] ); ?>"></div>
                        <div class="apex-cms-field"><label>Pre-Footer Açıklama</label><textarea name="prefooter_desc"><?php echo esc_textarea( $c['prefooter_desc'] ); ?></textarea></div>
                        <div class="apex-cms-field"><label>Footer Marka Açıklaması</label><textarea name="footer_desc"><?php echo esc_textarea( $c['footer_desc'] ); ?></textarea></div>
                        <div class="apex-cms-field"><label>Pre-Footer Buton 1 / Buton 2</label><div style="display:flex;gap:8px;"><input type="text" name="prefooter_cta1" value="<?php echo esc_attr( $c['prefooter_cta1'] ); ?>"><input type="text" name="prefooter_cta2" value="<?php echo esc_attr( $c['prefooter_cta2'] ); ?>"></div></div>
                        <div class="apex-cms-field"><label>Footer Telif Metni</label><input type="text" name="footer_copyright" value="<?php echo esc_attr( $c['footer_copyright'] ); ?>"></div>
                        <div class="apex-cms-field" style="grid-column:1/-1;"><label>WhatsApp Varsayılan Karşılama Mesajı</label><input type="text" name="whatsapp_message" value="<?php echo esc_attr( $c['whatsapp_message'] ); ?>"></div>
                    </div>
                </div>

            <?php elseif ( $active_tab === 'plans' ) : ?>
                <div class="apex-cms-card">
                    <div class="apex-cms-card-head">
                        <div>
                            <h2>Üyelik Paketleri (Ekle / Çıkar / Fiyat Düzenle)</h2>
                            <p style="margin:4px 0 0;font-size:12.5px;color:#9ca3af;">Ana sayfa, /uyelik/ ve online kayıt formu buradaki paket listesini kullanır.</p>
                        </div>
                        <button type="button" class="apex-cms-btn apex-cms-btn-green" id="apex-add-plan-btn">+ Yeni Üyelik Paketi Ekle</button>
                    </div>

                    <div class="apex-cms-grid-3" id="apex-plans-list">
                        <?php $p_idx = 0; foreach ( $plans as $pl ) : ?>
                        <div class="apex-cms-rep-row" data-rep-item style="margin-bottom:0;display:grid;gap:10px;">
                            <div class="apex-cms-rep-bar">
                                <label style="display:flex;align-items:center;gap:6px;font-size:12.5px;color:#a4ff2e;cursor:pointer;font-weight:700;">
                                    <input type="radio" name="plan_featured_idx" value="<?php echo esc_attr( $p_idx ); ?>" <?php checked( ! empty( $pl['featured'] ) ); ?>>
                                    <span>Öne Çıkan (Popüler)</span>
                                </label>
                                <button type="button" class="apex-cms-btn apex-cms-btn-danger apex-cms-btn-sm" data-rep-del>✕ Paketi Sil</button>
                            </div>
                            <input type="hidden" name="plan_slug[]" value="<?php echo esc_attr( $pl['slug'] ); ?>">
                            <div class="apex-cms-field"><label>Paket Adı</label><input type="text" name="plan_name[]" value="<?php echo esc_attr( $pl['name'] ); ?>"></div>
                            <div class="apex-cms-field"><label>Üst Rozet</label><input type="text" name="plan_badge[]" value="<?php echo esc_attr( $pl['badge'] ); ?>"></div>
                            <div class="apex-cms-grid-2">
                                <div class="apex-cms-field"><label>Aylık (₺)</label><input type="number" name="plan_monthly[]" value="<?php echo esc_attr( $pl['monthly_price'] ); ?>"></div>
                                <div class="apex-cms-field"><label>Yıllık Ay (₺)</label><input type="number" name="plan_annual[]" value="<?php echo esc_attr( $pl['annual_price'] ); ?>"></div>
                            </div>
                            <div class="apex-cms-field"><label>Kısa Özet</label><textarea name="plan_summary[]"><?php echo esc_textarea( $pl['summary'] ); ?></textarea></div>
                            <div class="apex-cms-field"><label>Özellikler (Her satır 1 madde)</label><textarea name="plan_features[]" style="min-height:135px;"><?php echo esc_textarea( implode( "\n", $pl['features'] ) ); ?></textarea></div>
                        </div>
                        <?php $p_idx++; endforeach; ?>
                    </div>
                </div>

            <?php elseif ( $active_tab === 'facilities' ) : ?>
                <div class="apex-cms-card">
                    <div class="apex-cms-card-head">
                        <div>
                            <h2>Tesis Olanakları Kartları (Ekle / Çıkar / Sırala)</h2>
                            <p style="margin:4px 0 0;font-size:12.5px;color:#9ca3af;">Ana sayfadaki Tesis Olanakları Bento Galerisi kartlarını dilediğiniz gibi ekleyip çıkarın.</p>
                        </div>
                        <button type="button" class="apex-cms-btn apex-cms-btn-green" id="apex-add-fac-btn">+ Yeni Tesis Kartı Ekle</button>
                    </div>
                    <div id="apex-fac-list">
                        <?php foreach ( $fac_tiles as $fi => $ft ) : ?>
                        <div class="apex-cms-rep-row" data-rep-item>
                            <div class="apex-cms-rep-bar" style="margin-bottom:10px;">
                                <strong style="color:#fff;font-size:14px;">Tesis Kartı</strong>
                                <div style="display:flex;gap:8px;align-items:center;">
                                    <label style="display:inline-flex;align-items:center;gap:5px;font-size:12px;color:#a4ff2e;cursor:pointer;">
                                        <input type="hidden" name="fac_live_parking[]" value="<?php echo ! empty( $ft['is_live_parking'] ) ? '1' : '0'; ?>" data-cb-mirror>
                                        <input type="checkbox" <?php checked( ! empty( $ft['is_live_parking'] ) ); ?> onchange="this.previousElementSibling.value=this.checked?'1':'0'">
                                        <span>Canlı Otopark Metriği</span>
                                    </label>
                                    <button type="button" class="apex-cms-btn apex-cms-btn-sm" data-rep-up>↑</button>
                                    <button type="button" class="apex-cms-btn apex-cms-btn-sm" data-rep-down>↓</button>
                                    <button type="button" class="apex-cms-btn apex-cms-btn-danger apex-cms-btn-sm" data-rep-del>✕ Sil</button>
                                </div>
                            </div>
                            <div class="apex-cms-grid-3">
                                <div class="apex-cms-field"><label>Başlık</label><input type="text" name="fac_title[]" value="<?php echo esc_attr( $ft['title'] ?? '' ); ?>"></div>
                                <div class="apex-cms-field"><label>Alt Etiket</label><input type="text" name="fac_role[]" value="<?php echo esc_attr( $ft['role'] ?? '' ); ?>"></div>
                                <div class="apex-cms-field"><label>Üst Metrik</label><input type="text" name="fac_stat[]" value="<?php echo esc_attr( $ft['stat'] ?? '' ); ?>"></div>
                                <div class="apex-cms-field"><label>Buton Metni</label><input type="text" name="fac_cta[]" value="<?php echo esc_attr( $ft['cta'] ?? 'Tesis Detayları' ); ?>"></div>
                                <div class="apex-cms-field"><label>Bağlantı URL</label><input type="text" name="fac_url[]" value="<?php echo esc_attr( $ft['url'] ?? '/tesis-olanaklari/' ); ?>"></div>
                                <div class="apex-cms-field"><label>Görsel Dosyası</label>
                                    <select name="fac_image[]"><?php foreach ( $img_choices as $im ) : ?><option value="<?php echo esc_attr( $im ); ?>" <?php selected( $ft['image'] ?? '', $im ); ?>><?php echo esc_html( $im ); ?></option><?php endforeach; ?></select>
                                </div>
                                <div class="apex-cms-field" style="grid-column:1/-1;"><label>Açıklama</label><textarea name="fac_desc[]"><?php echo esc_textarea( $ft['desc'] ?? '' ); ?></textarea></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="apex-cms-card">
                    <div class="apex-cms-card-head">
                        <div>
                            <h2>Apex Protein Kafe Menüsü (Ekle / Çıkar)</h2>
                            <p style="margin:4px 0 0;font-size:12.5px;color:#9ca3af;">Fiyat, makro veya ürün eklemek/silmek için satır butonlarını kullanın.</p>
                        </div>
                        <button type="button" class="apex-cms-btn apex-cms-btn-green apex-cms-btn-sm" id="apex-add-cafe-btn">+ Yeni Ürün Ekle</button>
                    </div>
                    <div class="apex-cms-cafe-row" style="font-size:11px;font-weight:700;color:#8c90a0;text-transform:uppercase;">
                        <span>Ürün Adı</span><span>Kategori</span><span>Makro Bilgisi</span><span>Fiyat (₺)</span><span>Açıklama</span><span>İşlem</span>
                    </div>
                    <div id="apex-cafe-list">
                        <?php foreach ( $menu as $mi ) : ?>
                        <div class="apex-cms-cafe-row" data-rep-item>
                            <input type="text" name="cafe_name[]" value="<?php echo esc_attr( $mi['name'] ); ?>">
                            <select name="cafe_cat[]">
                                <option value="shake" <?php selected( $mi['category'], 'shake' ); ?>>Shake</option>
                                <option value="bowl" <?php selected( $mi['category'], 'bowl' ); ?>>Fit Bowl</option>
                                <option value="coffee" <?php selected( $mi['category'], 'coffee' ); ?>>Kahve</option>
                                <option value="snack" <?php selected( $mi['category'], 'snack' ); ?>>Atıştırmalık</option>
                            </select>
                            <input type="text" name="cafe_macros[]" value="<?php echo esc_attr( $mi['macros'] ); ?>">
                            <input type="number" name="cafe_price[]" value="<?php echo esc_attr( $mi['price'] ); ?>">
                            <input type="text" name="cafe_desc[]" value="<?php echo esc_attr( $mi['desc'] ); ?>">
                            <button type="button" class="apex-cms-btn apex-cms-btn-danger apex-cms-btn-sm" data-rep-del>✕</button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            <?php elseif ( $active_tab === 'pages' ) : ?>
                <div class="apex-cms-card">
                    <h2>Hızlı Kayıt Yönetimi (WordPress CPT)</h2>
                    <div style="display:flex;flex-wrap:wrap;gap:10px;">
                        <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=apex_court' ) ); ?>" class="apex-cms-btn">🏀 Salonlar (WP Liste)</a>
                        <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=apex_pt_package' ) ); ?>" class="apex-cms-btn">🏋️ PT Paketleri (WP Liste)</a>
                        <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=apex_trainer' ) ); ?>" class="apex-cms-btn">👤 Antrenörler (WP Liste)</a>
                        <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=apex_announcement' ) ); ?>" class="apex-cms-btn">📢 Duyurular (WP Liste)</a>
                    </div>
                </div>

                <div class="apex-cms-card">
                    <h2>Tüm 7 İç Sayfanın Üst Hero Başlıkları &amp; Açıklamaları</h2>
                    <div class="apex-cms-grid-2">
                        <?php
                        $page_defs = [
                            'pt'      => '1. PT Eğitmenler (/pt-egitmenler/)',
                            'tr'      => '2. Antrenörler (/antrenorler/)',
                            'courts'  => '3. Salon Kiralama (/salon-kiralama/)',
                            'fac'     => '4. Tesis Olanakları (/tesis-olanaklari/)',
                            'mem'     => '5. Üyelik İşlemleri (/uyelik/)',
                            'news'    => '6. Salon Duyuruları (/duyurular/)',
                            'contact' => '7. İletişim (/iletisim/)',
                        ];
                        foreach ( $page_defs as $pk => $plbl ) : ?>
                        <div style="background:#0d0f15;border:1px solid rgba(255,255,255,0.08);border-radius:14px;padding:14px;display:grid;gap:8px;">
                            <strong style="color:#fff;font-size:13.5px;"><?php echo esc_html( $plbl ); ?></strong>
                            <div class="apex-cms-field"><label>Üst Etiket</label><input type="text" name="page_<?php echo $pk; ?>_eyebrow" value="<?php echo esc_attr( $c[ "page_{$pk}_eyebrow" ] ); ?>"></div>
                            <div class="apex-cms-field"><label>Ana H1 Başlık</label><input type="text" name="page_<?php echo $pk; ?>_title" value="<?php echo esc_attr( $c[ "page_{$pk}_title" ] ); ?>"></div>
                            <div class="apex-cms-field"><label>Açıklama</label><textarea name="page_<?php echo $pk; ?>_desc"><?php echo esc_textarea( $c[ "page_{$pk}_desc" ] ); ?></textarea></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div style="display:flex;gap:12px;align-items:center;margin-top:8px;">
                <button type="submit" name="apex_cms_save" class="apex-cms-btn apex-cms-btn-red" style="padding:12px 26px;font-size:14px;">💾 Değişiklikleri Canlı Siteye Kaydet</button>
                <span style="font-size:12.5px;color:#8c90a0;">Kaydettiğiniz anda ana sayfa ve ilgili sayfalar otomatik güncellenir.</span>
            </div>
        </form>
        <?php endif; ?>
    </div>

    <script>
    (function(){
        const imgOpts = <?php echo wp_json_encode( array_map( fn( $im ) => '<option value="' . esc_attr( $im ) . '">' . esc_html( $im ) . '</option>', $img_choices ) ); ?>.join('');

        function refreshOrderNumbers() {
            const list = document.getElementById('apex-blocks-list');
            if (!list) return;
            list.querySelectorAll(':scope > [data-rep-item]').forEach((item, idx) => {
                const badge = item.querySelector('.apex-cms-ord-num');
                if (badge) badge.textContent = String(idx + 1);
            });
        }

        // Ortak repeater olayları (↑ / ↓ / ✕ Sil / Düzenle / Kutular)
        document.addEventListener('click', function(e) {
            const targetEl = e.target instanceof Element ? e.target : e.target?.parentElement;
            if (!targetEl) return;

            const up = targetEl.closest('[data-rep-up]');
            if (up) {
                e.preventDefault();
                const row = up.closest('[data-rep-item]');
                const prev = row?.previousElementSibling;
                if (row && prev && prev.hasAttribute('data-rep-item')) {
                    row.parentNode.insertBefore(row, prev);
                    refreshOrderNumbers();
                }
                return;
            }
            const down = targetEl.closest('[data-rep-down]');
            if (down) {
                e.preventDefault();
                const row = down.closest('[data-rep-item]');
                const next = row?.nextElementSibling;
                if (row && next && next.hasAttribute('data-rep-item')) {
                    row.parentNode.insertBefore(next, row);
                    refreshOrderNumbers();
                }
                return;
            }
            const del = targetEl.closest('[data-rep-del]');
            if (del) {
                e.preventDefault();
                const row = del.closest('[data-rep-item]');
                if (row) row.remove();
                reindexPlanRadios();
                refreshOrderNumbers();
                return;
            }
            const editBtn = targetEl.closest('[data-toggle-custom-edit]');
            if (editBtn) {
                e.preventDefault();
                const box = editBtn.closest('[data-rep-item]')?.querySelector('.apex-custom-edit-body');
                if (box) {
                    const willShow = box.hasAttribute('hidden') || box.hidden || box.style.display === 'none';
                    box.hidden = !willShow;
                    box.style.display = willShow ? 'grid' : 'none';
                }
                return;
            }
            const tog = targetEl.closest('[data-toggle-box]');
            if (tog) {
                e.preventDefault();
                const target = document.getElementById(tog.getAttribute('data-toggle-box'));
                if (target) {
                    const willShow = target.hasAttribute('hidden') || target.hidden || target.style.display === 'none';
                    target.hidden = !willShow;
                    target.style.display = willShow ? 'block' : 'none';
                    if (willShow) target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });

        function reindexPlanRadios() {
            const rows = document.querySelectorAll('#apex-plans-list [data-rep-item]');
            rows.forEach((r, idx) => {
                const rad = r.querySelector('input[name="plan_featured_idx"]');
                if (rad) rad.value = String(idx);
            });
        }

        // + Yeni Özel Bölüm Ekle (Ana Sayfa Tab 1)
        document.getElementById('apex-add-custom-block-btn')?.addEventListener('click', function() {
            const list = document.getElementById('apex-blocks-list');
            if (!list) return;
            const id = 'custom_' + Date.now().toString(36);
            const nextOrd = list.querySelectorAll(':scope > [data-rep-item]').length + 1;
            const div = document.createElement('div');
            div.className = 'apex-cms-rep-row';
            div.setAttribute('data-rep-item', '');
            div.innerHTML = `
                <input type="hidden" name="blk_id[]" value="${id}">
                <input type="hidden" name="blk_type[]" value="custom">
                <div class="apex-cms-rep-bar">
                    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                        <span class="apex-cms-ord-num">${nextOrd}</span>
                        <label style="display:inline-flex;align-items:center;gap:6px;cursor:pointer;font-weight:700;color:#fff;">
                            <input type="hidden" name="blk_enabled[]" value="1" data-cb-mirror>
                            <input type="checkbox" checked onchange="this.previousElementSibling.value=this.checked?'1':'0'">
                            <span class="blk-display-title">Yeni Özel Bölüm</span>
                        </label>
                        <span class="apex-cms-badge apex-cms-badge-custom">Özel Blok</span>
                    </div>
                    <div style="display:flex;gap:6px;">
                        <button type="button" class="apex-cms-btn apex-cms-btn-sm" data-rep-up title="Yukarı Taşı" aria-label="Yukarı Taşı: Yeni Özel Bölüm">↑</button>
                        <button type="button" class="apex-cms-btn apex-cms-btn-sm" data-rep-down title="Aşağı Taşı" aria-label="Aşağı Taşı: Yeni Özel Bölüm">↓</button>
                        <button type="button" class="apex-cms-btn apex-cms-btn-sm" data-toggle-custom-edit>Düzenle</button>
                        <button type="button" class="apex-cms-btn apex-cms-btn-danger apex-cms-btn-sm" data-rep-del>✕ Sil</button>
                    </div>
                </div>
                <div class="apex-custom-edit-body apex-cms-grid-3" style="margin-top:12px;padding-top:12px;border-top:1px solid rgba(255,255,255,0.08);">
                    <div class="apex-cms-field"><label>Şablon Tipi</label>
                        <select name="blk_layout[]"><option value="cta_banner">Tanıtım / Kampanya Bandı</option><option value="feature_cards">Çoklu Özellik Kartları</option><option value="split_media">Görsel + Metin (Split)</option></select>
                    </div>
                    <div class="apex-cms-field"><label>Üst Etiket</label><input type="text" name="blk_eyebrow[]" value="ÖZEL KAMPANYA &amp; DUYURU"></div>
                    <div class="apex-cms-field"><label>Bölüm Başlığı</label><input type="text" name="blk_title[]" value="Yeni Özel Bölüm" oninput="this.closest('[data-rep-item]').querySelector('.blk-display-title').textContent=this.value"></div>
                    <div class="apex-cms-field"><label>Buton Metni</label><input type="text" name="blk_cta_text[]" value="Hemen İncele"></div>
                    <div class="apex-cms-field"><label>Buton Linki</label><input type="text" name="blk_cta_url[]" value="/uyelik/#paketler"></div>
                    <div class="apex-cms-field"><label>Görsel</label><select name="blk_image[]">${imgOpts}</select></div>
                    <div class="apex-cms-field" style="grid-column:1/-1;"><label>Açıklama</label><textarea name="blk_desc[]">Yeni bölüm açıklaması...</textarea></div>
                    <div class="apex-cms-field" style="grid-column:1/-1;"><label>Özellik Kartları (Başlık | Açıklama | Etiket)</label><textarea name="blk_cards_raw[]" placeholder="Hızlı Başlangıç | İlk 7 gün ücretsiz PT analizi | HEDİYE"></textarea></div>
                </div>`;
            list.appendChild(div);
            div.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });

        // + Yeni KPI Kartı Ekle
        document.getElementById('apex-add-kpi-btn')?.addEventListener('click', function() {
            const list = document.getElementById('apex-kpis-list');
            if (!list) return;
            const div = document.createElement('div');
            div.className = 'apex-cms-nav-row';
            div.setAttribute('data-rep-item', '');
            div.style.gridTemplateColumns = 'auto 1fr 1.6fr auto';
            div.innerHTML = `<div style="display:flex;gap:4px;"><button type="button" class="apex-cms-btn apex-cms-btn-sm" data-rep-up>↑</button><button type="button" class="apex-cms-btn apex-cms-btn-sm" data-rep-down>↓</button></div>
                <input type="text" name="kpi_val[]" value="" placeholder="Örn. 14.500 m²">
                <input type="text" name="kpi_lbl[]" value="" placeholder="Alt Açıklama">
                <button type="button" class="apex-cms-btn apex-cms-btn-danger apex-cms-btn-sm" data-rep-del>✕ Sil</button>`;
            list.appendChild(div);
        });

        // + Yeni Kaydırma Sahnesi Ekle
        document.getElementById('apex-add-chapter-btn')?.addEventListener('click', function() {
            const list = document.getElementById('apex-chapters-list');
            if (!list) return;
            const div = document.createElement('div');
            div.className = 'apex-cms-rep-row';
            div.setAttribute('data-rep-item', '');
            div.innerHTML = `<div class="apex-cms-rep-bar" style="margin-bottom:8px;">
                <label style="display:inline-flex;align-items:center;gap:6px;cursor:pointer;font-weight:700;color:#fff;"><input type="hidden" name="ch_enabled[]" value="1" data-cb-mirror><input type="checkbox" checked onchange="this.previousElementSibling.value=this.checked?'1':'0'"><span>Aktif Sahne</span></label>
                <div style="display:flex;gap:6px;"><button type="button" class="apex-cms-btn apex-cms-btn-sm" data-rep-up>↑</button><button type="button" class="apex-cms-btn apex-cms-btn-sm" data-rep-down>↓</button><button type="button" class="apex-cms-btn apex-cms-btn-danger apex-cms-btn-sm" data-rep-del>✕ Sil</button></div>
            </div>
            <div class="apex-cms-grid-3">
                <div class="apex-cms-field"><label>Üst Rozet</label><input type="text" name="ch_badge[]" value="Yeni Alan"></div>
                <div class="apex-cms-field"><label>Sahne Başlığı</label><input type="text" name="ch_title[]" value="Yeni Sahne Başlığı"></div>
                <div class="apex-cms-field"><label>1. Buton</label><div style="display:flex;gap:6px;"><input type="text" name="ch_cta1_text[]" value="İncele →"><input type="text" name="ch_cta1_url[]" value="/uyelik/"></div></div>
                <div class="apex-cms-field" style="grid-column:span 2;"><label>Açıklama</label><input type="text" name="ch_desc[]" value="Sahne açıklaması..."></div>
                <div class="apex-cms-field"><label>2. Buton (Opsiyonel)</label><div style="display:flex;gap:6px;"><input type="text" name="ch_cta2_text[]" value=""><input type="text" name="ch_cta2_url[]" value=""></div></div>
            </div>`;
            list.appendChild(div);
        });

        // + Yeni Menü Linki Ekle
        document.getElementById('apex-add-nav-btn')?.addEventListener('click', function() {
            const list = document.getElementById('apex-nav-list');
            if (!list) return;
            const div = document.createElement('div');
            div.className = 'apex-cms-nav-row';
            div.setAttribute('data-rep-item', '');
            div.innerHTML = `<div style="display:flex;gap:6px;align-items:center;"><input type="hidden" name="nav_item_enabled[]" value="1" data-cb-mirror><input type="checkbox" checked onchange="this.previousElementSibling.value=this.checked?'1':'0'"><button type="button" class="apex-cms-btn apex-cms-btn-sm" data-rep-up>↑</button><button type="button" class="apex-cms-btn apex-cms-btn-sm" data-rep-down>↓</button></div>
                <input type="text" name="nav_item_label[]" value="" placeholder="Örn. Kampanyalar">
                <input type="text" name="nav_item_url[]" value="/duyurular/" placeholder="/yol/">
                <button type="button" class="apex-cms-btn apex-cms-btn-danger apex-cms-btn-sm" data-rep-del>✕ Sil</button>`;
            list.appendChild(div);
        });

        // + Yeni Üyelik Paketi Ekle
        document.getElementById('apex-add-plan-btn')?.addEventListener('click', function() {
            const list = document.getElementById('apex-plans-list');
            if (!list) return;
            const idx = list.querySelectorAll('[data-rep-item]').length;
            const div = document.createElement('div');
            div.className = 'apex-cms-rep-row';
            div.setAttribute('data-rep-item', '');
            div.style.cssText = 'margin-bottom:0;display:grid;gap:10px;';
            div.innerHTML = `<div class="apex-cms-rep-bar"><label style="display:flex;align-items:center;gap:6px;font-size:12.5px;color:#a4ff2e;cursor:pointer;font-weight:700;"><input type="radio" name="plan_featured_idx" value="${idx}"><span>Öne Çıkan</span></label><button type="button" class="apex-cms-btn apex-cms-btn-danger apex-cms-btn-sm" data-rep-del>✕ Paketi Sil</button></div>
                <input type="hidden" name="plan_slug[]" value="">
                <div class="apex-cms-field"><label>Paket Adı</label><input type="text" name="plan_name[]" value="APEX VIP"></div>
                <div class="apex-cms-field"><label>Üst Rozet</label><input type="text" name="plan_badge[]" value="YENİ PAKET"></div>
                <div class="apex-cms-grid-2"><div class="apex-cms-field"><label>Aylık (₺)</label><input type="number" name="plan_monthly[]" value="3290"></div><div class="apex-cms-field"><label>Yıllık Ay (₺)</label><input type="number" name="plan_annual[]" value="2690"></div></div>
                <div class="apex-cms-field"><label>Kısa Özet</label><textarea name="plan_summary[]">Özel VIP kompleks deneyimi.</textarea></div>
                <div class="apex-cms-field"><label>Özellikler</label><textarea name="plan_features[]" style="min-height:110px;">7/24 VIP Erişim
Ücretsiz EV Şarj</textarea></div>`;
            list.appendChild(div);
            reindexPlanRadios();
        });

        // + Yeni Tesis Kartı Ekle
        document.getElementById('apex-add-fac-btn')?.addEventListener('click', function() {
            const list = document.getElementById('apex-fac-list');
            if (!list) return;
            const div = document.createElement('div');
            div.className = 'apex-cms-rep-row';
            div.setAttribute('data-rep-item', '');
            div.innerHTML = `<div class="apex-cms-rep-bar" style="margin-bottom:10px;"><strong style="color:#fff;font-size:14px;">Yeni Tesis Kartı</strong><div style="display:flex;gap:8px;align-items:center;"><label style="display:inline-flex;align-items:center;gap:5px;font-size:12px;color:#a4ff2e;cursor:pointer;"><input type="hidden" name="fac_live_parking[]" value="0" data-cb-mirror><input type="checkbox" onchange="this.previousElementSibling.value=this.checked?'1':'0'"><span>Canlı Otopark Metriği</span></label><button type="button" class="apex-cms-btn apex-cms-btn-sm" data-rep-up>↑</button><button type="button" class="apex-cms-btn apex-cms-btn-sm" data-rep-down>↓</button><button type="button" class="apex-cms-btn apex-cms-btn-danger apex-cms-btn-sm" data-rep-del>✕ Sil</button></div></div>
                <div class="apex-cms-grid-3">
                    <div class="apex-cms-field"><label>Başlık</label><input type="text" name="fac_title[]" value="Soğuk Şok & Kriyoterapi"></div>
                    <div class="apex-cms-field"><label>Alt Etiket</label><input type="text" name="fac_role[]" value="TOPARLANMA ALANI"></div>
                    <div class="apex-cms-field"><label>Üst Metrik</label><input type="text" name="fac_stat[]" value="-110°C / 3 DK"></div>
                    <div class="apex-cms-field"><label>Buton Metni</label><input type="text" name="fac_cta[]" value="Tesis Detayları"></div>
                    <div class="apex-cms-field"><label>Bağlantı URL</label><input type="text" name="fac_url[]" value="/tesis-olanaklari/"></div>
                    <div class="apex-cms-field"><label>Görsel</label><select name="fac_image[]">${imgOpts}</select></div>
                    <div class="apex-cms-field" style="grid-column:1/-1;"><label>Açıklama</label><textarea name="fac_desc[]">Maç sonrası hızlı kas toparlanması ve inflamasyon kontrolü.</textarea></div>
                </div>`;
            list.appendChild(div);
        });

        // + Yeni Kafe Ürünü Ekle
        document.getElementById('apex-add-cafe-btn')?.addEventListener('click', function() {
            const list = document.getElementById('apex-cafe-list');
            if (!list) return;
            const div = document.createElement('div');
            div.className = 'apex-cms-cafe-row';
            div.setAttribute('data-rep-item', '');
            div.innerHTML = `<input type="text" name="cafe_name[]" value="" placeholder="Ürün Adı">
                <select name="cafe_cat[]"><option value="shake">Shake</option><option value="bowl">Fit Bowl</option><option value="coffee">Kahve</option><option value="snack">Atıştırmalık</option></select>
                <input type="text" name="cafe_macros[]" value="" placeholder="30g Protein · 250 kcal">
                <input type="number" name="cafe_price[]" value="150">
                <input type="text" name="cafe_desc[]" value="" placeholder="Kısa açıklama">
                <button type="button" class="apex-cms-btn apex-cms-btn-danger apex-cms-btn-sm" data-rep-del>✕</button>`;
            list.appendChild(div);
        });
    })();
    </script>
    <?php
}
