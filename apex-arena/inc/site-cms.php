<?php
/**
 * APEX ARENA — Tam Kapsamlı Site İçerik & Tasarım CMS Stüdyosu (v2.3.0)
 *
 * Yöneticilerin sitenin her bir parçasını (Marka, Tema Vurgu Rengi, Üst Menü, Ana Sayfa tüm bölümleri
 * ve görünürlük anahtarları, Üyelik Paketleri & Fiyatları, Tesis Olanakları, Protein Kafe Menüsü,
 * 7 İç Sayfa Hero metinleri, Pre-Footer, Footer & WhatsApp) kod yazmadan WP Admin'den düzenlemesini sağlar.
 *
 * @package ApexArena
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Varsayılan tüm site CMS içerikleri
 */
function apex_get_cms_defaults() {
    return [
        // 1. Marka, Renk, Menü & Footer
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

        'hero_title'          => 'Sınırlarını Arenada Aş.',
        'hero_subtitle'       => 'İki FIBA basketbol salonu, profesyonel voleybol sahası, lisanslı hakem hizmeti, sertifikalı PT kadrosu ve 7/24 AI Spor Koçu tek çatı altında.',
        'hero_cta1'           => 'Üyeliği Başlat',
        'hero_cta2'           => 'Salon Kirala',
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

        // 3. Tesis Olanakları Metinleri
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
 * Kayıtlı tüm CMS verilerini getir (WP object cache uyumlu)
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
 * Bir ana sayfa bölümünün görünür olup olmadığını kontrol et
 */
function apex_cms_show( $key ) {
    $cms = apex_get_cms();
    return ! empty( $cms[ $key ] );
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
 * WP Admin — Site İçerik & Tasarım CMS Stüdyosu (Tek Ekrandan Tüm Site Yönetimi)
 */
function apex_admin_site_cms_page() {
    $notice = '';
    $active_tab = sanitize_key( $_GET['tab'] ?? ( $_POST['apex_cms_tab'] ?? 'brand' ) );
    $allowed_tabs = [ 'brand', 'home', 'plans', 'facilities', 'pages' ];
    if ( ! in_array( $active_tab, $allowed_tabs, true ) ) {
        $active_tab = 'brand';
    }

    // Varsayılanlara Sıfırla
    if ( isset( $_POST['apex_cms_reset'] ) && check_admin_referer( 'apex_site_cms_nonce' ) ) {
        delete_option( 'apex_site_cms' );
        delete_option( 'apex_cms_plans' );
        delete_option( 'apex_cms_cafe_menu' );
        $notice = '<div class="notice notice-info"><p>Tüm site içerikleri, üyelik paketleri ve kafe menüsü fabrika varsayılanlarına sıfırlandı.</p></div>';
    }

    // Kaydetme İşlemleri
    if ( isset( $_POST['apex_cms_save'] ) && check_admin_referer( 'apex_site_cms_nonce' ) ) {
        $cur = get_option( 'apex_site_cms', [] );
        if ( ! is_array( $cur ) ) {
            $cur = [];
        }

        if ( $active_tab === 'brand' ) {
            $text_keys = [
                'brand_first', 'brand_second', 'nav_pt', 'nav_trainers', 'nav_courts', 'nav_facilities',
                'nav_membership', 'nav_news', 'nav_contact', 'nav_login_label', 'nav_join_label',
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

            // Ayrıca temel iletişim ayarlarını apex_arena_settings ile senkronla
            $s = apex_get_settings();
            $s['phone']         = sanitize_text_field( wp_unslash( $_POST['site_phone'] ?? $s['phone'] ) );
            $s['email']         = sanitize_email( wp_unslash( $_POST['site_email'] ?? $s['email'] ) );
            $s['address']       = sanitize_text_field( wp_unslash( $_POST['site_address'] ?? $s['address'] ) );
            $s['weekday_hours'] = sanitize_text_field( wp_unslash( $_POST['site_weekday_hours'] ?? $s['weekday_hours'] ) );
            $s['weekend_hours'] = sanitize_text_field( wp_unslash( $_POST['site_weekend_hours'] ?? $s['weekend_hours'] ) );
            update_option( 'apex_arena_settings', $s );
            update_option( 'apex_site_cms', $cur );
            $notice = '<div class="notice notice-success"><p>✅ <strong>Marka, Menü, Renk ve Footer ayarları güncellendi!</strong></p></div>';
        } elseif ( $active_tab === 'home' ) {
            $toggles = [ 'show_hero', 'show_3d_dumbbell', 'show_courts', 'show_pt', 'show_trainers', 'show_facilities', 'show_membership', 'show_announcements', 'show_3d_arch', 'show_reviews' ];
            foreach ( $toggles as $tk ) {
                $cur[ $tk ] = ! empty( $_POST[ $tk ] ) ? 1 : 0;
            }
            $fields = [
                'hero_title', 'hero_cta1', 'hero_cta2',
                'kpi1_val', 'kpi1_lbl', 'kpi2_val', 'kpi2_lbl', 'kpi3_val', 'kpi3_lbl', 'kpi4_val', 'kpi4_lbl',
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
            $areas = [ 'hero_subtitle', 'monolith_manifesto', 'home_courts_desc', 'home_pt_desc', 'home_tr_desc', 'home_arch_desc' ];
            foreach ( $areas as $ak ) {
                if ( isset( $_POST[ $ak ] ) ) {
                    $cur[ $ak ] = sanitize_textarea_field( wp_unslash( $_POST[ $ak ] ) );
                }
            }
            update_option( 'apex_site_cms', $cur );
            $notice = '<div class="notice notice-success"><p>✅ <strong>Ana Sayfa metinleri ve bölüm görünürlük ayarları kaydedildi!</strong></p></div>';
        } elseif ( $active_tab === 'plans' ) {
            $slugs = [ 'classic', 'pro', 'elite' ];
            $saved_plans = [];
            foreach ( $slugs as $sl ) {
                $feats_raw = sanitize_textarea_field( wp_unslash( $_POST[ "plan_{$sl}_features" ] ?? '' ) );
                $feats     = array_values( array_filter( array_map( 'trim', explode( "\n", $feats_raw ) ) ) );
                $saved_plans[ $sl ] = [
                    'slug'          => $sl,
                    'name'          => sanitize_text_field( wp_unslash( $_POST[ "plan_{$sl}_name" ] ?? strtoupper( $sl ) ) ),
                    'badge'         => sanitize_text_field( wp_unslash( $_POST[ "plan_{$sl}_badge" ] ?? '' ) ),
                    'monthly_price' => max( 100, absint( $_POST[ "plan_{$sl}_monthly" ] ?? 1490 ) ),
                    'annual_price'  => max( 100, absint( $_POST[ "plan_{$sl}_annual" ] ?? 1190 ) ),
                    'featured'      => ( ( $_POST['plan_featured_slug'] ?? 'pro' ) === $sl ),
                    'summary'       => sanitize_textarea_field( wp_unslash( $_POST[ "plan_{$sl}_summary" ] ?? '' ) ),
                    'features'      => $feats,
                ];
            }
            update_option( 'apex_cms_plans', $saved_plans );
            $notice = '<div class="notice notice-success"><p>✅ <strong>Üyelik Paketleri ve fiyatları güncellendi!</strong> (Ana sayfa, /uyelik/ ve kayıt API anında yenilendi.)</p></div>';
        } elseif ( $active_tab === 'facilities' ) {
            $fkeys = [ 'fac1_stat', 'fac1_role', 'fac1_title', 'fac2_role', 'fac2_title', 'fac3_stat', 'fac3_role', 'fac3_title' ];
            foreach ( $fkeys as $fk ) {
                if ( isset( $_POST[ $fk ] ) ) {
                    $cur[ $fk ] = sanitize_text_field( wp_unslash( $_POST[ $fk ] ) );
                }
            }
            $cur['fac1_desc'] = sanitize_textarea_field( wp_unslash( $_POST['fac1_desc'] ?? '' ) );
            $cur['fac2_desc'] = sanitize_textarea_field( wp_unslash( $_POST['fac2_desc'] ?? '' ) );
            $cur['fac3_desc'] = sanitize_textarea_field( wp_unslash( $_POST['fac3_desc'] ?? '' ) );
            update_option( 'apex_site_cms', $cur );

            // Protein Kafe Menü Kalemlerini Kaydet
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
            $notice = '<div class="notice notice-success"><p>✅ <strong>Tesis Olanakları ve Protein Kafe Menüsü (' . count( $menu_items ) . ' ürün) kaydedildi!</strong></p></div>';
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

    // Güncel verileri doğrudan DB'den taze oku (static cache bypass için wp_parse_args)
    $raw_cms  = get_option( 'apex_site_cms', [] );
    $c        = wp_parse_args( is_array( $raw_cms ) ? $raw_cms : [], apex_get_cms_defaults() );
    $s        = apex_get_settings();
    $plans    = apex_get_membership_plans();
    $menu     = apex_get_cafe_menu();
    $tab_base = admin_url( 'admin.php?page=apex-site-cms&tab=' );
    ?>
    <style>
        .apex-cms-wrap { max-width: 1320px; margin: 16px 20px 44px 0; font-family: -apple-system, BlinkMacSystemFont, 'Plus Jakarta Sans', 'Segoe UI', sans-serif; color: #f5f5f7; }
        .apex-cms-wrap * { box-sizing: border-box; }
        .apex-cms-wrap .notice { background: #161922 !important; color: #f5f5f7 !important; border: 1px solid rgba(255,255,255,0.12) !important; border-left-width: 4px !important; border-radius: 12px; padding: 10px 16px; margin: 0 0 16px; }
        .apex-cms-wrap .notice p { color: #f5f5f7 !important; margin: 4px 0; font-size: 13.5px; }
        .apex-cms-wrap .notice-success { border-left-color: #a4ff2e !important; }
        .apex-cms-wrap .notice-info { border-left-color: #26e6ff !important; }
        .apex-cms-banner {
            background: radial-gradient(ellipse 46% 115% at 92% 0%, rgba(250,17,79,0.22), transparent 62%), radial-gradient(ellipse 40% 100% at 8% 100%, rgba(38,230,255,0.12), transparent 60%), #111217;
            border: 1px solid rgba(255,255,255,0.09); border-radius: 20px; padding: 22px 26px; display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; margin-bottom: 18px;
        }
        .apex-cms-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 18px; }
        .apex-cms-tab {
            padding: 10px 18px; border-radius: 999px; font-size: 13px; font-weight: 700; text-decoration: none;
            color: #a1a1aa; background: #13151c; border: 1px solid rgba(255,255,255,0.09); transition: all 0.2s ease;
        }
        .apex-cms-tab:hover { color: #fff; border-color: rgba(255,255,255,0.22); }
        .apex-cms-tab.is-active { background: linear-gradient(135deg,#fa114f,#d40f40); color: #fff; border-color: rgba(255,120,150,0.5); box-shadow: 0 8px 22px rgba(250,17,79,0.3); }
        .apex-cms-card { background: linear-gradient(180deg,#16171d,#111216); border: 1px solid rgba(255,255,255,0.08); border-radius: 18px; padding: 22px 24px; margin-bottom: 18px; }
        .apex-cms-card h2 { margin: 0 0 14px; font-size: 16px; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 8px; }
        .apex-cms-grid-2 { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
        .apex-cms-grid-3 { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
        .apex-cms-grid-4 { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
        .apex-cms-field { display: flex; flex-direction: column; gap: 6px; }
        .apex-cms-field label { font-size: 12px; font-weight: 700; color: #d4d4d8; }
        .apex-cms-wrap input[type="text"],
        .apex-cms-wrap input[type="email"],
        .apex-cms-wrap input[type="number"],
        .apex-cms-wrap textarea,
        .apex-cms-wrap select {
            background: #0c0d12 !important;
            border: 1px solid rgba(255,255,255,0.18) !important;
            border-radius: 10px;
            padding: 9px 12px;
            color: #ffffff !important;
            font-size: 13px;
            color-scheme: dark;
            width: 100%;
            max-width: 100%;
        }
        .apex-cms-wrap select option { background: #151720 !important; color: #ffffff !important; }
        .apex-cms-wrap textarea { min-height: 76px; line-height: 1.5; }
        .apex-cms-wrap input:focus, .apex-cms-wrap textarea:focus, .apex-cms-wrap select:focus { border-color: #fa114f !important; box-shadow: 0 0 0 2px rgba(250,17,79,0.28) !important; outline: none; }
        .apex-cms-toggles { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 10px; }
        .apex-cms-toggle {
            display: flex; align-items: center; gap: 8px; padding: 10px 12px; border-radius: 12px;
            background: #0d0f15; border: 1px solid rgba(255,255,255,0.09); font-size: 12.5px; font-weight: 600; color: #e4e4e7; cursor: pointer;
        }
        .apex-cms-btn { display: inline-flex; align-items: center; gap: 7px; padding: 10px 18px; border-radius: 999px; font-size: 13px; font-weight: 700; border: 1px solid rgba(255,255,255,0.15); background: rgba(255,255,255,0.06); color: #fff; cursor: pointer; text-decoration: none; }
        .apex-cms-btn:hover { background: rgba(255,255,255,0.14); color: #fff; }
        .apex-cms-btn-red { background: linear-gradient(135deg,#fa114f,#d40f40); border-color: rgba(255,110,145,0.45); }
        .apex-cms-cpt-bar { display: flex; flex-wrap: wrap; gap: 10px; }
        .apex-cms-cafe-row { display: grid; grid-template-columns: 1.3fr 0.7fr 1.2fr 0.55fr 1.6fr; gap: 8px; align-items: center; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.06); }
        @media (max-width: 1080px) {
            .apex-cms-grid-3, .apex-cms-grid-4, .apex-cms-toggles { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .apex-cms-grid-2, .apex-cms-cafe-row { grid-template-columns: 1fr; }
        }
    </style>

    <div class="wrap apex-cms-wrap">
        <?php echo $notice; ?>

        <div class="apex-cms-banner">
            <div>
                <div style="font-size:11px;font-weight:800;letter-spacing:0.09em;text-transform:uppercase;color:#26e6ff;margin-bottom:5px;">
                    ● TAM KONTROL SİTE İÇERİK &amp; TASARIM CMS · v<?php echo esc_html( APEX_VERSION ); ?>
                </div>
                <h1 style="margin:0;font-size:22px;font-weight:800;color:#fff;">Site İçerik Stüdyosu (Her Parçayı Düzenle)</h1>
                <p style="margin:6px 0 0;font-size:13px;color:#a1a1aa;">
                    Marka, renk, üst menü, ana sayfa bölümleri (aç/kapa), üyelik fiyatları, kafe menüsü ve tüm 7 iç sayfanın metinlerini buradan anında güncelleyin.
                </p>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" class="apex-cms-btn">Canlı Siteyi Aç ↗</a>
                <form method="post" onsubmit="return confirm('Tüm CMS özelleştirmeleri fabrika varsayılanlarına dönecek. Emin misiniz?');" style="margin:0;">
                    <?php wp_nonce_field( 'apex_site_cms_nonce' ); ?>
                    <button type="submit" name="apex_cms_reset" class="apex-cms-btn">↺ Varsayılana Sıfırla</button>
                </form>
            </div>
        </div>

        <nav class="apex-cms-tabs" aria-label="CMS Bölümleri">
            <a class="apex-cms-tab <?php echo $active_tab === 'brand' ? 'is-active' : ''; ?>" href="<?php echo esc_url( $tab_base . 'brand' ); ?>">1. Marka, Renk, Menü &amp; Footer</a>
            <a class="apex-cms-tab <?php echo $active_tab === 'home' ? 'is-active' : ''; ?>" href="<?php echo esc_url( $tab_base . 'home' ); ?>">2. Ana Sayfa &amp; Bölüm Kontrolü</a>
            <a class="apex-cms-tab <?php echo $active_tab === 'plans' ? 'is-active' : ''; ?>" href="<?php echo esc_url( $tab_base . 'plans' ); ?>">3. Üyelik Paketleri &amp; Fiyatlar</a>
            <a class="apex-cms-tab <?php echo $active_tab === 'facilities' ? 'is-active' : ''; ?>" href="<?php echo esc_url( $tab_base . 'facilities' ); ?>">4. Tesis &amp; Protein Kafe Menüsü</a>
            <a class="apex-cms-tab <?php echo $active_tab === 'pages' ? 'is-active' : ''; ?>" href="<?php echo esc_url( $tab_base . 'pages' ); ?>">5. Tüm 7 İç Sayfa &amp; CPT Yönetimi</a>
        </nav>

        <form method="post">
            <?php wp_nonce_field( 'apex_site_cms_nonce' ); ?>
            <input type="hidden" name="apex_cms_tab" value="<?php echo esc_attr( $active_tab ); ?>">

            <?php if ( $active_tab === 'brand' ) : ?>
                <div class="apex-cms-card">
                    <h2>Marka Kimliği, Tema Vurgu Rengi &amp; Üst Menü</h2>
                    <div class="apex-cms-grid-3">
                        <div class="apex-cms-field"><label>Marka İlk Kelime</label><input type="text" name="brand_first" value="<?php echo esc_attr( $c['brand_first'] ); ?>"></div>
                        <div class="apex-cms-field"><label>Marka İkinci Kelime</label><input type="text" name="brand_second" value="<?php echo esc_attr( $c['brand_second'] ); ?>"></div>
                        <div class="apex-cms-field">
                            <label>Ana Vurgu Rengi (Accent Color)</label>
                            <div style="display:flex;gap:8px;align-items:center;">
                                <input type="color" name="accent_color" value="<?php echo esc_attr( $c['accent_color'] ); ?>" style="width:52px;height:38px;border:0;background:none;cursor:pointer;">
                                <span style="font-size:12.5px;color:#a1a1aa;">Varsayılan: #E31837 (APEX Kırmızı)</span>
                            </div>
                        </div>
                    </div>
                    <h3 style="font-size:13px;color:#a1a1aa;margin:18px 0 10px;text-transform:uppercase;letter-spacing:0.08em;">Üst Navigasyon Menü Etiketleri</h3>
                    <div class="apex-cms-grid-4">
                        <div class="apex-cms-field"><label>Menü 1 (PT)</label><input type="text" name="nav_pt" value="<?php echo esc_attr( $c['nav_pt'] ); ?>"></div>
                        <div class="apex-cms-field"><label>Menü 2 (Antrenörler)</label><input type="text" name="nav_trainers" value="<?php echo esc_attr( $c['nav_trainers'] ); ?>"></div>
                        <div class="apex-cms-field"><label>Menü 3 (Salonlar)</label><input type="text" name="nav_courts" value="<?php echo esc_attr( $c['nav_courts'] ); ?>"></div>
                        <div class="apex-cms-field"><label>Menü 4 (Tesis)</label><input type="text" name="nav_facilities" value="<?php echo esc_attr( $c['nav_facilities'] ); ?>"></div>
                        <div class="apex-cms-field"><label>Menü 5 (Üyelik)</label><input type="text" name="nav_membership" value="<?php echo esc_attr( $c['nav_membership'] ); ?>"></div>
                        <div class="apex-cms-field"><label>Menü 6 (Duyurular)</label><input type="text" name="nav_news" value="<?php echo esc_attr( $c['nav_news'] ); ?>"></div>
                        <div class="apex-cms-field"><label>Menü 7 (İletişim)</label><input type="text" name="nav_contact" value="<?php echo esc_attr( $c['nav_contact'] ); ?>"></div>
                        <div class="apex-cms-field"><label>Üye Girişi / Bize Katıl</label><div style="display:flex;gap:6px;"><input type="text" name="nav_login_label" value="<?php echo esc_attr( $c['nav_login_label'] ); ?>"><input type="text" name="nav_join_label" value="<?php echo esc_attr( $c['nav_join_label'] ); ?>"></div></div>
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

            <?php elseif ( $active_tab === 'home' ) : ?>
                <div class="apex-cms-card">
                    <h2>Ana Sayfa Bölüm Görünürlüğü (Tek Tıkla Göster / Gizle)</h2>
                    <div class="apex-cms-toggles">
                        <?php
                        $sec_list = [
                            'show_hero'          => 'Hero Video Tur',
                            'show_3d_dumbbell'   => '3D Dambıl Monolit',
                            'show_courts'        => 'Maç Salonları',
                            'show_pt'            => 'PT Paketleri',
                            'show_trainers'      => 'Antrenörler',
                            'show_facilities'    => 'Tesis Olanakları',
                            'show_membership'    => 'Üyelik Paketleri',
                            'show_announcements' => 'Duyurular',
                            'show_3d_arch'       => '3D Mimari Kesit',
                            'show_reviews'       => 'Üye Yorumları',
                        ];
                        foreach ( $sec_list as $sk => $slbl ) : ?>
                        <label class="apex-cms-toggle">
                            <input type="checkbox" name="<?php echo esc_attr( $sk ); ?>" value="1" <?php checked( ! empty( $c[ $sk ] ) ); ?>>
                            <span><?php echo esc_html( $slbl ); ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="apex-cms-card">
                    <h2>Hero Bölümü, 4 KPI Kartı &amp; 3D Dambıl Tipografisi</h2>
                    <div class="apex-cms-grid-2">
                        <div class="apex-cms-field"><label>Hero Ana Başlık</label><input type="text" name="hero_title" value="<?php echo esc_attr( $c['hero_title'] ); ?>"></div>
                        <div class="apex-cms-field"><label>Hero Buton 1 / Buton 2</label><div style="display:flex;gap:8px;"><input type="text" name="hero_cta1" value="<?php echo esc_attr( $c['hero_cta1'] ); ?>"><input type="text" name="hero_cta2" value="<?php echo esc_attr( $c['hero_cta2'] ); ?>"></div></div>
                        <div class="apex-cms-field" style="grid-column:1/-1;"><label>Hero Alt Açıklama</label><textarea name="hero_subtitle"><?php echo esc_textarea( $c['hero_subtitle'] ); ?></textarea></div>
                    </div>
                    <div class="apex-cms-grid-4" style="margin-top:12px;">
                        <?php for ( $k = 1; $k <= 4; $k++ ) : ?>
                        <div class="apex-cms-field">
                            <label>KPI <?php echo $k; ?> (Başlık &amp; Alt Metin)</label>
                            <input type="text" name="kpi<?php echo $k; ?>_val" value="<?php echo esc_attr( $c[ "kpi{$k}_val" ] ); ?>" style="margin-bottom:5px;">
                            <input type="text" name="kpi<?php echo $k; ?>_lbl" value="<?php echo esc_attr( $c[ "kpi{$k}_lbl" ] ); ?>">
                        </div>
                        <?php endfor; ?>
                    </div>
                    <div class="apex-cms-grid-3" style="margin-top:14px;">
                        <div class="apex-cms-field"><label>3D Monolit Satır 1</label><input type="text" name="monolith_line1" value="<?php echo esc_attr( $c['monolith_line1'] ); ?>"></div>
                        <div class="apex-cms-field"><label>3D Monolit Satır 2</label><input type="text" name="monolith_line2" value="<?php echo esc_attr( $c['monolith_line2'] ); ?>"></div>
                        <div class="apex-cms-field"><label>3D Sağ Alt Manifesto</label><textarea name="monolith_manifesto"><?php echo esc_textarea( $c['monolith_manifesto'] ); ?></textarea></div>
                    </div>
                </div>

                <div class="apex-cms-card">
                    <h2>Ana Sayfa Bölüm Başlıkları &amp; Açıklamaları</h2>
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
                        <div class="apex-cms-field"><label>Üye Yorumları — Üst Etiket &amp; Başlık</label><input type="text" name="home_rev_eyebrow" value="<?php echo esc_attr( $c['home_rev_eyebrow'] ); ?>" style="margin-bottom:5px;"><input type="text" name="home_rev_title" value="<?php echo esc_attr( $c['home_rev_title'] ); ?>"></div>
                    </div>
                </div>

            <?php elseif ( $active_tab === 'plans' ) : ?>
                <div class="apex-cms-grid-3">
                    <?php foreach ( [ 'classic', 'pro', 'elite' ] as $sl ) :
                        $pl = $plans[ $sl ];
                    ?>
                    <div class="apex-cms-card">
                        <h2><?php echo esc_html( $pl['name'] ); ?></h2>
                        <div style="display:grid;gap:10px;">
                            <label style="display:flex;align-items:center;gap:8px;font-size:12.5px;color:#a4ff2e;cursor:pointer;">
                                <input type="radio" name="plan_featured_slug" value="<?php echo esc_attr( $sl ); ?>" <?php checked( ! empty( $pl['featured'] ) ); ?>>
                                <strong>Öne Çıkan (Önerilen) Paket Yap</strong>
                            </label>
                            <div class="apex-cms-field"><label>Paket Adı</label><input type="text" name="plan_<?php echo $sl; ?>_name" value="<?php echo esc_attr( $pl['name'] ); ?>"></div>
                            <div class="apex-cms-field"><label>Üst Rozet</label><input type="text" name="plan_<?php echo $sl; ?>_badge" value="<?php echo esc_attr( $pl['badge'] ); ?>"></div>
                            <div class="apex-cms-grid-2">
                                <div class="apex-cms-field"><label>Aylık Fiyat (₺)</label><input type="number" name="plan_<?php echo $sl; ?>_monthly" value="<?php echo esc_attr( $pl['monthly_price'] ); ?>"></div>
                                <div class="apex-cms-field"><label>Yıllık Aylık (₺)</label><input type="number" name="plan_<?php echo $sl; ?>_annual" value="<?php echo esc_attr( $pl['annual_price'] ); ?>"></div>
                            </div>
                            <div class="apex-cms-field"><label>Kısa Özet</label><textarea name="plan_<?php echo $sl; ?>_summary"><?php echo esc_textarea( $pl['summary'] ); ?></textarea></div>
                            <div class="apex-cms-field"><label>Özellikler (Her satır 1 madde)</label><textarea name="plan_<?php echo $sl; ?>_features" style="min-height:155px;"><?php echo esc_textarea( implode( "\n", $pl['features'] ) ); ?></textarea></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            <?php elseif ( $active_tab === 'facilities' ) : ?>
                <div class="apex-cms-card">
                    <h2>Tesis Olanakları Kart Metinleri (Ana Sayfa &amp; Tesis Sayfası)</h2>
                    <div class="apex-cms-grid-3">
                        <div style="display:grid;gap:10px;">
                            <strong style="color:#26e6ff;">01 · Soyunma Odası &amp; Sauna</strong>
                            <div class="apex-cms-field"><label>Üst Metrik</label><input type="text" name="fac1_stat" value="<?php echo esc_attr( $c['fac1_stat'] ); ?>"></div>
                            <div class="apex-cms-field"><label>Alt Etiket</label><input type="text" name="fac1_role" value="<?php echo esc_attr( $c['fac1_role'] ); ?>"></div>
                            <div class="apex-cms-field"><label>Başlık</label><input type="text" name="fac1_title" value="<?php echo esc_attr( $c['fac1_title'] ); ?>"></div>
                            <div class="apex-cms-field"><label>Açıklama</label><textarea name="fac1_desc"><?php echo esc_textarea( $c['fac1_desc'] ); ?></textarea></div>
                        </div>
                        <div style="display:grid;gap:10px;">
                            <strong style="color:#a4ff2e;">02 · Akıllı Kapalı Otopark</strong>
                            <div class="apex-cms-field"><label>Alt Etiket</label><input type="text" name="fac2_role" value="<?php echo esc_attr( $c['fac2_role'] ); ?>"></div>
                            <div class="apex-cms-field"><label>Başlık</label><input type="text" name="fac2_title" value="<?php echo esc_attr( $c['fac2_title'] ); ?>"></div>
                            <div class="apex-cms-field"><label>Açıklama</label><textarea name="fac2_desc"><?php echo esc_textarea( $c['fac2_desc'] ); ?></textarea></div>
                        </div>
                        <div style="display:grid;gap:10px;">
                            <strong style="color:#ffbe0b;">03 · Apex Protein Kafe</strong>
                            <div class="apex-cms-field"><label>Üst Metrik</label><input type="text" name="fac3_stat" value="<?php echo esc_attr( $c['fac3_stat'] ); ?>"></div>
                            <div class="apex-cms-field"><label>Alt Etiket</label><input type="text" name="fac3_role" value="<?php echo esc_attr( $c['fac3_role'] ); ?>"></div>
                            <div class="apex-cms-field"><label>Başlık</label><input type="text" name="fac3_title" value="<?php echo esc_attr( $c['fac3_title'] ); ?>"></div>
                            <div class="apex-cms-field"><label>Açıklama</label><textarea name="fac3_desc"><?php echo esc_textarea( $c['fac3_desc'] ); ?></textarea></div>
                        </div>
                    </div>
                </div>

                <div class="apex-cms-card">
                    <h2>Apex Protein Kafe Menüsü (Fiyat, Makro &amp; Yeni Ürün Ekleme)</h2>
                    <p style="font-size:12.5px;color:#9ca3af;margin:0 0 12px;">Bir ürünü silmek için adını boş bırakın; en alttaki boş satırı doldurarak yeni kafe ürünü ekleyebilirsiniz.</p>
                    <div class="apex-cms-cafe-row" style="font-size:11px;font-weight:700;color:#8c90a0;text-transform:uppercase;">
                        <span>Ürün Adı</span><span>Kategori</span><span>Makro Bilgisi</span><span>Fiyat (₺)</span><span>Açıklama</span>
                    </div>
                    <?php
                    $menu_edit = $menu;
                    $menu_edit[] = [ 'name' => '', 'category' => 'shake', 'macros' => '', 'price' => 150, 'desc' => '' ];
                    foreach ( $menu_edit as $mi ) : ?>
                    <div class="apex-cms-cafe-row">
                        <input type="text" name="cafe_name[]" value="<?php echo esc_attr( $mi['name'] ); ?>" placeholder="+ Yeni ürün adı...">
                        <select name="cafe_cat[]">
                            <option value="shake" <?php selected( $mi['category'], 'shake' ); ?>>Shake</option>
                            <option value="bowl" <?php selected( $mi['category'], 'bowl' ); ?>>Fit Bowl</option>
                            <option value="coffee" <?php selected( $mi['category'], 'coffee' ); ?>>Kahve</option>
                            <option value="snack" <?php selected( $mi['category'], 'snack' ); ?>>Atıştırmalık</option>
                        </select>
                        <input type="text" name="cafe_macros[]" value="<?php echo esc_attr( $mi['macros'] ); ?>" placeholder="30g Protein · 280 kcal">
                        <input type="number" name="cafe_price[]" value="<?php echo esc_attr( $mi['price'] ); ?>">
                        <input type="text" name="cafe_desc[]" value="<?php echo esc_attr( $mi['desc'] ); ?>" placeholder="Kısa içerik açıklaması">
                    </div>
                    <?php endforeach; ?>
                </div>

            <?php elseif ( $active_tab === 'pages' ) : ?>
                <div class="apex-cms-card">
                    <h2>Hızlı İçerik Yönetimi (Salonlar, PT Paketleri, Antrenörler &amp; Duyurular)</h2>
                    <p style="font-size:12.5px;color:#9ca3af;margin:0 0 12px;">Bireysel salonları, PT paketlerini, antrenör profillerini/fotoğraflarını ve duyuruları doğrudan WordPress kayıtları olarak ekleyin veya düzenleyin:</p>
                    <div class="apex-cms-cpt-bar">
                        <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=apex_court' ) ); ?>" class="apex-cms-btn">🏀 Salonları Düzenle</a>
                        <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=apex_pt_package' ) ); ?>" class="apex-cms-btn">🏋️ PT Paketlerini Düzenle</a>
                        <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=apex_trainer' ) ); ?>" class="apex-cms-btn">👤 Antrenörleri Düzenle</a>
                        <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=apex_announcement' ) ); ?>" class="apex-cms-btn">📢 Duyuruları Düzenle</a>
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
                <span style="font-size:12.5px;color:#8c90a0;">Kaydettiğiniz anda tüm ön yüz sayfaları otomatik güncellenir.</span>
            </div>
        </form>
    </div>
    <?php
}
