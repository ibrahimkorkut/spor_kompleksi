<?php
/**
 * Yardımcı fonksiyonlar, ayarlar, üyelik paketleri ve medya çözümleyici
 *
 * @package ApexArena
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Varsayılan ve kayıtlı tema ayarlarını getir
 */
function apex_get_settings() {
    $defaults = [
        'phone'           => '0216 555 27 39',
        'email'           => 'info@apexarena.com.tr',
        'address'         => 'Barbaros Mah. Spor Bulvarı No: 42, Ataşehir / İstanbul',
        'weekday_hours'   => '06:30 – 23:30',
        'weekend_hours'   => '08:00 – 22:00',
        'referee_fee'     => 450,
        'live_occupancy'  => 34,
        'parking_free'    => 118,
        'parking_total'   => 180,
        'ai_provider'     => 'hybrid', // hybrid | gemini | openai | local
        'gemini_api_key'  => '',
        'gemini_model'    => 'gemini-2.5-flash',
        'openai_api_key'  => '',
        'openai_model'    => 'gpt-4o-mini',
        'github_url'           => 'https://github.com/ibrahimkorkut/spor_kompleksi',
        'envato_url'           => 'https://themeforest.net/',
        // v2.1.0 — Gmail SMTP & Otomatik E-posta Hatırlatma Ayarları
        'smtp_enabled'         => 1,
        'smtp_host'            => 'smtp.gmail.com',
        'smtp_port'            => 587,
        'smtp_encryption'      => 'tls',
        'smtp_user'            => '',
        'smtp_pass'            => '',
        'smtp_from_name'       => 'APEX ARENA Spor Kompleksi',
        'mail_on_register'     => 1,
        'mail_on_booking'      => 1,
        'mail_auto_reminders'  => 1,
        'reminder_hours_before'=> 24,
    ];

    $saved = get_option( 'apex_arena_settings', [] );
    return wp_parse_args( is_array( $saved ) ? $saved : [], $defaults );
}

/**
 * Aktif AI çalışma modunu belirle
 */
function apex_get_effective_ai_mode() {
    $s = apex_get_settings();
    if ( $s['ai_provider'] === 'local' ) {
        return 'local-hybrid';
    }
    if ( ! empty( $s['gemini_api_key'] ) && in_array( $s['ai_provider'], [ 'hybrid', 'gemini' ], true ) ) {
        return 'gemini-live';
    }
    if ( ! empty( $s['openai_api_key'] ) && in_array( $s['ai_provider'], [ 'hybrid', 'openai' ], true ) ) {
        return 'openai-live';
    }
    return 'local-hybrid';
}

/**
 * Tema içi yerel fotoğraf URL'si döndür (tamamen offline çalışır!)
 */
function apex_img_url( $filename, $fallback = 'hero-arena.jpg' ) {
    $filename = trim( (string) $filename );
    if ( $filename && preg_match( '#^https?://#i', $filename ) ) {
        return esc_url_raw( $filename );
    }
    $filename = ltrim( $filename, '/' );
    $path     = APEX_DIR . '/assets/images/' . $filename;
    if ( $filename && file_exists( $path ) ) {
        return APEX_URI . '/assets/images/' . $filename . '?v=' . filemtime( $path );
    }
    $fb_path = APEX_DIR . '/assets/images/' . $fallback;
    if ( file_exists( $fb_path ) ) {
        return APEX_URI . '/assets/images/' . $fallback . '?v=' . filemtime( $fb_path );
    }
    return APEX_URI . '/assets/images/hero-arena.svg';
}

/**
 * CPT görselini getir (önce WP Featured Image, yoksa _apex_photo yerel görseli)
 */
function apex_get_post_photo( $post_id, $fallback = 'hero-arena.jpg' ) {
    if ( has_post_thumbnail( $post_id ) ) {
        $url = get_the_post_thumbnail_url( $post_id, 'large' );
        if ( $url ) {
            return $url;
        }
    }
    $meta_photo = get_post_meta( $post_id, '_apex_photo', true );
    return apex_img_url( $meta_photo, $fallback );
}

/**
 * Türk Lirası formatı
 */
function apex_format_tl( $amount ) {
    return '₺' . number_format( (float) $amount, 0, ',', '.' );
}

/**
 * Sabit saat dilimleri (Salon ve PT takvimi için)
 */
function apex_get_time_slots() {
    return [
        '08:00', '09:00', '10:00', '11:00', '12:00', '13:00',
        '14:00', '15:00', '16:00', '17:00', '18:00', '19:00',
        '20:00', '21:00', '22:00',
    ];
}

/**
 * Üyelik paketleri tanımı
 */
function apex_get_membership_plans() {
    $defaults = [
        'classic' => [
            'slug'          => 'classic',
            'name'          => 'APEX CLASSIC',
            'badge'         => 'BAŞLANGIÇ PAKETİ',
            'monthly_price' => 1490,
            'annual_price'  => 1190,
            'featured'      => false,
            'summary'       => 'Düzenli fitness, kardiyo ve tesis konforu arayan sporcular için ideal giriş paketi.',
            'features'      => [
                'Fitness & serbest ağırlık alanı sınırsız kullanım',
                'Soyunma odası, yağmur duş & Fin saunası erişimi',
                'İlk 2 saat ücretsiz kapalı otopark hakkı',
                'Dijital QR üyelik kartı & online üye paneli',
                'Haftada 2 grup dersi rezervasyon hakkı',
                'Basketbol / Voleybol salon kiralamada %10 indirim',
            ],
        ],
        'pro' => [
            'slug'          => 'pro',
            'name'          => 'APEX PRO',
            'badge'         => 'EN POPÜLER PAKET',
            'monthly_price' => 2490,
            'annual_price'  => 1990,
            'featured'      => true,
            'summary'       => 'Sınırsız grup dersleri, aylık PT değerlendirmesi ve salon indirimleriyle tam kompleks deneyimi.',
            'features'      => [
                'Tüm fitness, fonksiyonel alan & grup dersleri sınırsız',
                'Aylık 2 bireysel PT ölçüm ve program güncelleme seansı',
                'Basketbol & Voleybol salon kiralamada %25 indirim',
                'Hakem ayarlama hizmetinde öncelikli rezervasyon',
                'İlk 3 saat ücretsiz kapalı otopark + EV şarj indirimi',
                'Apex Protein Kafe menüsünde %15 üye indirimi',
                'InBody 570 vücut kompozisyon analizi (ayda 1)',
            ],
        ],
        'elite' => [
            'slug'          => 'elite',
            'name'          => 'APEX ELITE + PT',
            'badge'         => 'SINIRSIZ PERFORMANS',
            'monthly_price' => 4290,
            'annual_price'  => 3490,
            'featured'      => false,
            'summary'       => 'Profesyonel sporcular ve hızlı dönüşüm hedefleyenler için VIP kişisel antrenörlük ve öncelikli salon erişimi.',
            'features'      => [
                'Aylık 8 bireysel PT seansı (seçtiğiniz antrenörle)',
                'Ayda 2 saat ücretsiz Basketbol veya Voleybol salonu',
                'Ücretsiz lisanslı hakem yönlendirmesi (ayda 1 maç)',
                'Kişiye özel VIP kilitli dolap & havlu servisi',
                'Sınırsız kapalı otopark & ücretsiz EV şarj (2 saat/gün)',
                'Apex Protein Kafe’de günlük 1 ücretsiz protein shake',
                '7/24 AI Spor Koçu kişiselleştirilmiş makro takibi',
            ],
        ],
    ];
    $saved = get_option( 'apex_cms_plans', [] );
    if ( is_array( $saved ) && ! empty( $saved ) ) {
        $out = [];
        foreach ( $saved as $slug => $plan ) {
            if ( ! is_array( $plan ) || empty( $plan['name'] ) ) {
                continue;
            }
            $clean_slug = sanitize_title( $plan['slug'] ?? $slug );
            if ( ! $clean_slug ) {
                continue;
            }
            $base = $defaults[ $clean_slug ] ?? [
                'slug'          => $clean_slug,
                'name'          => strtoupper( $clean_slug ),
                'badge'         => 'ÖZEL ÜYELİK PAKETİ',
                'monthly_price' => 1990,
                'annual_price'  => 1590,
                'featured'      => false,
                'summary'       => '',
                'features'      => [],
            ];
            $out[ $clean_slug ] = wp_parse_args( $plan, $base );
            $out[ $clean_slug ]['slug'] = $clean_slug;
        }
        if ( ! empty( $out ) ) {
            return $out;
        }
    }
    return $defaults;
}

/**
 * Apex Protein Kafe Menüsü (WP Admin Site CMS'ten düzenlenebilir)
 */
function apex_get_cafe_menu() {
    $saved = get_option( 'apex_cms_cafe_menu', [] );
    if ( is_array( $saved ) && ! empty( $saved ) ) {
        return $saved;
    }
    return [
        [ 'category' => 'shake', 'name' => 'Crimson Berry Whey Shake', 'macros' => '34g Protein · 260 kcal · 4g Şeker', 'price' => 165, 'desc' => 'İzole vanilya whey, taze frambuaz, yaban mersini, badem sütü ve chia tohumu.' ],
        [ 'category' => 'shake', 'name' => 'Apex Peanut Power Shake', 'macros' => '38g Protein · 390 kcal · Kompleks Karb', 'price' => 175, 'desc' => 'Çikolatalı whey, şekersiz yer fıstığı ezmesi, muz, yulaf ve soğuk espresso shot.' ],
        [ 'category' => 'shake', 'name' => 'Green Recovery Detox', 'macros' => '18g Bitkisel Protein · 195 kcal', 'price' => 155, 'desc' => 'Bezelye proteini, ıspanak, yeşil elma, zencefil, limon ve hindistan cevizi suyu.' ],
        [ 'category' => 'bowl', 'name' => 'Izgara Tavuk & Kinoa Arena Bowl', 'macros' => '46g Protein · 520 kcal · 42g Karb', 'price' => 285, 'desc' => 'Baharatlı ızgara tavuk göğsü, üç renkli kinoa, avokado, edamame ve limon-zeytinyağı sos.' ],
        [ 'category' => 'bowl', 'name' => 'Füme Somon Omega Bowl', 'macros' => '41g Protein · 560 kcal · Omega-3 Zengin', 'price' => 340, 'desc' => 'Norveç somonu, esmer pirinç, fırınlanmış tatlı patates, roka ve tahin sos.' ],
        [ 'category' => 'bowl', 'name' => 'Vegan Falafel & Humus Power Bowl', 'macros' => '28g Protein · 480 kcal · Yüksek Lif', 'price' => 255, 'desc' => 'Fırınlanmış nohut falafel, pancar humusu, köz biber, kinoa ve kabak çekirdeği.' ],
        [ 'category' => 'coffee', 'name' => 'Double Shot Cold Brew', 'macros' => '5 kcal · 180mg Doğal Kafein', 'price' => 115, 'desc' => 'Antrenman öncesi odak ve enerji için 18 saat soğuk demlenmiş Etiyopya çekirdeği.' ],
        [ 'category' => 'coffee', 'name' => 'Yulaf Sütlü Matcha Latte', 'macros' => '110 kcal · Antioksidan', 'price' => 135, 'desc' => 'Seremoni sınıfı Japon matcha çayı ve barista yulaf sütü.' ],
        [ 'category' => 'snack', 'name' => 'Ev Yapımı Protein Bar (2’li)', 'macros' => '22g Protein · 230 kcal · Rafine Şekersiz', 'price' => 120, 'desc' => 'Hurma, ham kakao, kavrulmuş badem ve whey protein ile günlük hazırlanır.' ],
    ];
}

/**
 * Breadcrumb (WCAG uyumlu)
 */
function apex_breadcrumbs() {
    if ( is_front_page() ) {
        return;
    }
    echo '<nav class="apex-breadcrumbs" aria-label="Sayfa konumu">';
    echo '<ol>';
    echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">Ana Sayfa</a></li>';
    if ( is_singular( 'apex_trainer' ) ) {
        echo '<li><a href="' . esc_url( home_url( '/antrenorler/' ) ) . '">Antrenörler</a></li>';
        echo '<li aria-current="page">' . esc_html( get_the_title() ) . '</li>';
    } elseif ( is_singular( 'apex_announcement' ) ) {
        echo '<li><a href="' . esc_url( home_url( '/duyurular/' ) ) . '">Salon Duyuruları</a></li>';
        echo '<li aria-current="page">' . esc_html( get_the_title() ) . '</li>';
    } elseif ( is_search() ) {
        echo '<li aria-current="page">Arama: ' . esc_html( get_search_query() ) . '</li>';
    } elseif ( is_404() ) {
        echo '<li aria-current="page">Sayfa Bulunamadı (404)</li>';
    } else {
        echo '<li aria-current="page">' . esc_html( get_the_title() ) . '</li>';
    }
    echo '</ol>';
    echo '</nav>';
}

/**
 * SVG QR kod üreteci (Üye Paneli Dijital Üyelik Kartı için tamamen yerel & deterministik)
 */
function apex_generate_member_qr_svg( $code ) {
    $hash = md5( (string) $code );
    $size = 11;
    $cell = 10;
    $pad  = 10;
    $dim  = $size * $cell + $pad * 2;
    $svg  = '<svg class="apex-qr-svg" viewBox="0 0 ' . $dim . ' ' . $dim . '" width="120" height="120" role="img" aria-label="Dijital Üyelik QR Kodu: ' . esc_attr( $code ) . '">';
    $svg .= '<rect width="' . $dim . '" height="' . $dim . '" rx="8" fill="#FFFFFF"/>';
    // 3 köşe konum işaretçisi
    $corners = [ [0,0], [$size-3,0], [0,$size-3] ];
    foreach ( $corners as [$cx, $cy] ) {
        $svg .= '<rect x="' . ( $pad + $cx * $cell ) . '" y="' . ( $pad + $cy * $cell ) . '" width="' . ( 3 * $cell ) . '" height="' . ( 3 * $cell ) . '" fill="#0A0B0E"/>';
        $svg .= '<rect x="' . ( $pad + ( $cx + 0.6 ) * $cell ) . '" y="' . ( $pad + ( $cy + 0.6 ) * $cell ) . '" width="' . ( 1.8 * $cell ) . '" height="' . ( 1.8 * $cell ) . '" fill="#FFFFFF"/>';
        $svg .= '<rect x="' . ( $pad + ( $cx + 1 ) * $cell ) . '" y="' . ( $pad + ( $cy + 1 ) * $cell ) . '" width="' . $cell . '" height="' . $cell . '" fill="#E31837"/>';
    }
    $idx = 0;
    for ( $r = 0; $r < $size; $r++ ) {
        for ( $c = 0; $c < $size; $c++ ) {
            $inCorner = ( $r < 4 && $c < 4 ) || ( $r < 4 && $c >= $size - 4 ) || ( $r >= $size - 4 && $c < 4 );
            if ( $inCorner ) {
                continue;
            }
            $val = hexdec( $hash[ $idx % strlen( $hash ) ] );
            $idx++;
            if ( ( $val + $r + $c ) % 2 === 0 ) {
                $svg .= '<rect x="' . ( $pad + $c * $cell ) . '" y="' . ( $pad + $r * $cell ) . '" width="' . ( $cell - 1 ) . '" height="' . ( $cell - 1 ) . '" rx="1.5" fill="#0A0B0E"/>';
            }
        }
    }
    $svg .= '</svg>';
    return $svg;
}
