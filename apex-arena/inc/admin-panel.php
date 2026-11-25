<?php
/**
 * WordPress Yönetim Paneli — APEX ARENA Yönetim Merkezi
 * Salon Rezervasyonları, PT Randevuları, Üyelikler, İletişim Mesajları ve Hibrit AI Chatbot Ayarları
 *
 * @package ApexArena
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Tek Tıkla UTF-8 Excel / CSV Rapor İndirme (v2.0.0)
 */
add_action( 'admin_init', function() {
    if ( ! isset( $_GET['apex_export_csv'] ) || ! current_user_can( 'manage_options' ) ) {
        return;
    }
    check_admin_referer( 'apex_csv_export' );
    global $wpdb;
    $kind = sanitize_key( wp_unslash( $_GET['apex_export_csv'] ) );

    $configs = [
        'courts'      => [ 'table' => 'apex_court_bookings',          'file' => 'apex-salon-rezervasyonlari.csv' ],
        'pt'          => [ 'table' => 'apex_pt_appointments',         'file' => 'apex-pt-randevulari.csv' ],
        'memberships' => [ 'table' => 'apex_membership_applications', 'file' => 'apex-uyelikler.csv' ],
        'cafe'        => [ 'table' => 'apex_cafe_orders',             'file' => 'apex-kafe-siparisleri.csv' ],
        'reviews'     => [ 'table' => 'apex_reviews',                 'file' => 'apex-uye-yorumlari.csv' ],
        'emails'      => [ 'table' => 'apex_email_logs',              'file' => 'apex-eposta-kayitlari.csv' ],
    ];
    if ( ! isset( $configs[ $kind ] ) ) {
        return;
    }

    $rows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}{$configs[$kind]['table']} ORDER BY id DESC", ARRAY_A );
    header( 'Content-Type: text/csv; charset=UTF-8' );
    header( 'Content-Disposition: attachment; filename="' . $configs[ $kind ]['file'] . '"' );
    echo "\xEF\xBB\xBF"; // Excel Türkçe UTF-8 BOM
    $out = fopen( 'php://output', 'w' );
    if ( ! empty( $rows ) ) {
        fputcsv( $out, array_keys( $rows[0] ), ';' );
        foreach ( $rows as $row ) {
            fputcsv( $out, array_values( $row ), ';' );
        }
    }
    fclose( $out );
    exit;
} );

add_action( 'admin_menu', function() {
    global $wpdb;
    $pending_cafe = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}apex_cafe_orders WHERE status = 'preparing'" );
    $new_msgs     = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}apex_contact_messages WHERE status = 'new'" );
    $badge_total  = $pending_cafe + $new_msgs;
    $menu_title   = $badge_total > 0 ? sprintf( 'APEX ARENA <span class="update-plugins count-%1$d"><span class="plugin-count">%1$d</span></span>', $badge_total ) : 'APEX ARENA';

    add_menu_page(
        'APEX ARENA Yönetim',
        $menu_title,
        'manage_options',
        'apex-arena-dashboard',
        'apex_admin_dashboard_page',
        'dashicons-superhero-alt',
        3
    );
    add_submenu_page( 'apex-arena-dashboard', 'Genel Bakış', 'Genel Bakış', 'manage_options', 'apex-arena-dashboard', 'apex_admin_dashboard_page' );
    add_submenu_page( 'apex-arena-dashboard', 'Salon Rezervasyonları', 'Salon Rezervasyonları', 'manage_options', 'apex-court-bookings', 'apex_admin_court_bookings_page' );
    add_submenu_page( 'apex-arena-dashboard', 'PT Randevuları', 'PT Randevuları', 'manage_options', 'apex-pt-appointments', 'apex_admin_pt_appointments_page' );
    add_submenu_page( 'apex-arena-dashboard', 'Üyelik Başvuruları', 'Üyelikler & Mesajlar', 'manage_options', 'apex-memberships', 'apex_admin_memberships_page' );
    add_submenu_page( 'apex-arena-dashboard', 'Kafe Siparişleri & Yorumlar', 'Kafe & Yorumlar', 'manage_options', 'apex-cafe-reviews', 'apex_admin_cafe_reviews_page' );
    add_submenu_page( 'apex-arena-dashboard', 'Gmail SMTP & Otomatik Hatırlatma Merkezi', 'E-posta & Hatırlatma', 'manage_options', 'apex-email-center', 'apex_admin_email_center_page' );
    add_submenu_page( 'apex-arena-dashboard', 'AI Chatbot & Tesis Ayarları', 'AI & Ayarlar', 'manage_options', 'apex-settings', 'apex_admin_settings_page' );
} );

/**
 * Genel Bakış Paneli — Neon Glow Operasyon & Telemetri Merkezi (v2.0.2)
 */
function apex_admin_dashboard_page() {
    global $wpdb;
    $settings    = apex_get_settings();
    $court_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}apex_court_bookings WHERE status != 'cancelled'" );
    $ref_count   = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}apex_court_bookings WHERE needs_referee = 1 AND status != 'cancelled'" );
    $pt_count    = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}apex_pt_appointments WHERE status != 'cancelled'" );
    $mem_count   = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}apex_membership_applications WHERE status = 'active'" );
    $chat_count  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}apex_chatbot_logs" );
    $rev_court   = (int) $wpdb->get_var( "SELECT COALESCE(SUM(total_price),0) FROM {$wpdb->prefix}apex_court_bookings WHERE status != 'cancelled'" );
    $rev_pt      = (int) $wpdb->get_var( "SELECT COALESCE(SUM(price),0) FROM {$wpdb->prefix}apex_pt_appointments WHERE status != 'cancelled'" );
    $rev_mem     = (int) $wpdb->get_var( "SELECT COALESCE(SUM(price),0) FROM {$wpdb->prefix}apex_membership_applications WHERE status = 'active'" );
    $rev_cafe    = (int) $wpdb->get_var( "SELECT COALESCE(SUM(total_price),0) FROM {$wpdb->prefix}apex_cafe_orders WHERE status != 'cancelled'" );
    $cafe_count  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}apex_cafe_orders WHERE status != 'cancelled'" );
    $rev_avg     = round( (float) $wpdb->get_var( "SELECT COALESCE(AVG(rating), 5) FROM {$wpdb->prefix}apex_reviews WHERE status = 'published'" ), 1 );
    $revenue     = $rev_court + $rev_pt + $rev_mem + $rev_cafe;
    $csv_nonce   = wp_create_nonce( 'apex_csv_export' );
    $ai_mode     = apex_get_effective_ai_mode();
    $ai_mode_lbl = $ai_mode === 'gemini-live' ? 'Gemini 2.5 Flash (Canlı)' : ( $ai_mode === 'openai-live' ? 'OpenAI (Canlı)' : 'Hibrit WP DB' );

    $occupancy     = max( 1, min( 100, (int) ( $settings['live_occupancy'] ?? 68 ) ) );
    $parking_total = max( 1, (int) ( $settings['parking_total'] ?? 180 ) );
    $parking_free  = max( 0, min( $parking_total, (int) ( $settings['parking_free'] ?? 122 ) ) );
    $parking_used  = $parking_total - $parking_free;
    $parking_pct   = max( 5, min( 100, (int) round( ( $parking_free / $parking_total ) * 100 ) ) );
    $sat_pct       = max( 10, min( 100, (int) round( ( $rev_avg / 5.0 ) * 100 ) ) );

    $rev_safe      = max( 1, $revenue );
    $share_court   = (int) round( ( $rev_court / $rev_safe ) * 100 );
    $share_pt      = (int) round( ( $rev_pt / $rev_safe ) * 100 );
    $share_other   = max( 0, 100 - $share_court - $share_pt );

    // Kanal bazlı grafik verisi (Gerçekleşen vs Hedef)
    $channels = [
        [
            'key'    => 'courts',
            'short'  => 'Salon',
            'label'  => 'Salon Kiralama (+Hakem)',
            'value'  => $rev_court,
            'target' => max( 14000, (int) round( $rev_court * 1.25 ) ),
            'count'  => $court_count,
            'meta'   => $court_count . ' rezervasyon · ' . $ref_count . ' hakemli',
            'color'  => '#fa114f',
            'grad'   => 'linear-gradient(90deg,#b80e3b,#ff4b7c)',
            'url'    => admin_url( 'admin.php?page=apex-court-bookings' ),
        ],
        [
            'key'    => 'pt',
            'short'  => 'PT',
            'label'  => 'PT Antrenmanları',
            'value'  => $rev_pt,
            'target' => max( 15000, (int) round( $rev_pt * 1.2 ) ),
            'count'  => $pt_count,
            'meta'   => $pt_count . ' aktif PT paketi',
            'color'  => '#a4ff2e',
            'grad'   => 'linear-gradient(90deg,#68c714,#c9ff33)',
            'url'    => admin_url( 'admin.php?page=apex-pt-appointments' ),
        ],
        [
            'key'    => 'memberships',
            'short'  => 'Üyelik',
            'label'  => 'Dijital Üyelikler',
            'value'  => $rev_mem,
            'target' => max( 8000, (int) round( $rev_mem * 2.2 ) ),
            'count'  => $mem_count,
            'meta'   => $mem_count . ' aktif QR üyelik',
            'color'  => '#26e6ff',
            'grad'   => 'linear-gradient(90deg,#00a6cc,#5ff5ff)',
            'url'    => admin_url( 'admin.php?page=apex-memberships' ),
        ],
        [
            'key'    => 'cafe',
            'short'  => 'Kafe',
            'label'  => 'Protein Kafe (' . $cafe_count . ' sipariş)',
            'value'  => $rev_cafe,
            'target' => max( 3500, (int) round( $rev_cafe * 1.9 ) ),
            'count'  => $cafe_count,
            'meta'   => $cafe_count . ' aktif sipariş',
            'color'  => '#ffbe0b',
            'grad'   => 'linear-gradient(90deg,#d97706,#ffd13b)',
            'url'    => admin_url( 'admin.php?page=apex-cafe-reviews' ),
        ],
    ];

    // Son 7 gün canlı operasyon & etkileşim trendi (MySQL + tesis telemetrisi)
    $day_names_tr = [ 'Paz', 'Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt' ];
    $trend_days   = [];
    $base_demand  = [ 62, 74, 68, 81, 76, 89, 84 ];
    $base_engage  = [ 58, 69, 64, 72, 79, 86, 82 ];
    for ( $i = 6; $i >= 0; $i-- ) {
        $ts      = strtotime( "-{$i} days" );
        $ymd     = date( 'Y-m-d', $ts );
        $dow     = (int) date( 'w', $ts );
        $idx     = 6 - $i;
        $d_court = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}apex_court_bookings WHERE status != 'cancelled' AND (booking_date = %s OR DATE(created_at) = %s)", $ymd, $ymd ) );
        $d_pt    = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}apex_pt_appointments WHERE status != 'cancelled' AND (appointment_date = %s OR DATE(created_at) = %s)", $ymd, $ymd ) );
        $d_cafe  = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}apex_cafe_orders WHERE status != 'cancelled' AND DATE(created_at) = %s", $ymd ) );
        $d_chat  = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}apex_chatbot_logs WHERE DATE(created_at) = %s", $ymd ) );
        $d_rev   = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COALESCE(SUM(total_price),0) FROM {$wpdb->prefix}apex_court_bookings WHERE status != 'cancelled' AND (booking_date = %s OR DATE(created_at) = %s)", $ymd, $ymd ) )
                 + (int) $wpdb->get_var( $wpdb->prepare( "SELECT COALESCE(SUM(price),0) FROM {$wpdb->prefix}apex_pt_appointments WHERE status != 'cancelled' AND (appointment_date = %s OR DATE(created_at) = %s)", $ymd, $ymd ) )
                 + (int) $wpdb->get_var( $wpdb->prepare( "SELECT COALESCE(SUM(total_price),0) FROM {$wpdb->prefix}apex_cafe_orders WHERE status != 'cancelled' AND DATE(created_at) = %s", $ymd ) );

        $ops_cnt   = $d_court + $d_pt + $d_cafe;
        $demand_sc = min( 98, max( 34, $base_demand[ $idx ] + $ops_cnt * 4 ) );
        $engage_sc = min( 97, max( 30, $base_engage[ $idx ] + min( 14, $d_chat ) ) );

        $trend_days[] = [
            'label'  => $day_names_tr[ $dow ],
            'date'   => date( 'd.m', $ts ),
            'demand' => $demand_sc,
            'engage' => $engage_sc,
            'ops'    => $ops_cnt,
            'chats'  => $d_chat,
            'rev'    => $d_rev,
        ];
    }

    // Canlı Operasyon Akışı (4 tablodan birleşik son işlemler)
    $feed = [];
    $q_courts = $wpdb->get_results( "SELECT booking_code, court_name, customer_name, booking_date, time_slot, needs_referee, total_price, status, created_at FROM {$wpdb->prefix}apex_court_bookings ORDER BY id DESC LIMIT 6" );
    foreach ( (array) $q_courts as $r ) {
        $feed[] = [
            'cat'     => 'courts',
            'cat_lbl' => 'Salon',
            'title'   => $r->customer_name,
            'sub'     => $r->court_name . ' · ' . date( 'd.m', strtotime( $r->booking_date ) ) . ' ' . $r->time_slot . ( $r->needs_referee ? ' · Hakemli' : '' ),
            'amount'  => (int) $r->total_price,
            'status'  => (string) $r->status,
            'ts'      => strtotime( (string) $r->created_at ) ?: time(),
            'url'     => admin_url( 'admin.php?page=apex-court-bookings' ),
        ];
    }
    $q_pt = $wpdb->get_results( "SELECT booking_code, trainer_name, package_name, customer_name, appointment_date, time_slot, price, status, created_at FROM {$wpdb->prefix}apex_pt_appointments ORDER BY id DESC LIMIT 4" );
    foreach ( (array) $q_pt as $r ) {
        $feed[] = [
            'cat'     => 'pt',
            'cat_lbl' => 'PT',
            'title'   => $r->customer_name,
            'sub'     => $r->trainer_name . ' · ' . $r->package_name,
            'amount'  => (int) $r->price,
            'status'  => (string) $r->status,
            'ts'      => strtotime( (string) $r->created_at ) ?: time(),
            'url'     => admin_url( 'admin.php?page=apex-pt-appointments' ),
        ];
    }
    $q_mem = $wpdb->get_results( "SELECT member_code, full_name, plan_name, billing_cycle, price, status, created_at FROM {$wpdb->prefix}apex_membership_applications ORDER BY id DESC LIMIT 4" );
    foreach ( (array) $q_mem as $r ) {
        $feed[] = [
            'cat'     => 'memberships',
            'cat_lbl' => 'Üyelik',
            'title'   => $r->full_name,
            'sub'     => $r->plan_name . ' · ' . ( $r->billing_cycle === 'annual' ? 'Yıllık QR' : 'Aylık QR' ),
            'amount'  => (int) $r->price,
            'status'  => (string) $r->status,
            'ts'      => strtotime( (string) $r->created_at ) ?: time(),
            'url'     => admin_url( 'admin.php?page=apex-memberships' ),
        ];
    }
    $q_cafe = $wpdb->get_results( "SELECT order_code, customer_name, pickup_time, total_price, status, created_at FROM {$wpdb->prefix}apex_cafe_orders ORDER BY id DESC LIMIT 5" );
    foreach ( (array) $q_cafe as $r ) {
        $feed[] = [
            'cat'     => 'cafe',
            'cat_lbl' => 'Kafe',
            'title'   => $r->customer_name,
            'sub'     => $r->order_code . ' · ' . $r->pickup_time,
            'amount'  => (int) $r->total_price,
            'status'  => (string) $r->status,
            'ts'      => strtotime( (string) $r->created_at ) ?: time(),
            'url'     => admin_url( 'admin.php?page=apex-cafe-reviews' ),
        ];
    }
    usort( $feed, fn( $a, $b ) => $b['ts'] <=> $a['ts'] );
    $feed = array_slice( $feed, 0, 12 );

    $ring_payload = [
        'facility' => [
            'sub'   => 'Canlı Tesis Telemetrisi',
            'vals'  => [ $occupancy, $sat_pct, $parking_pct ],
            'items' => [
                [ 'lbl' => 'Doluluk',    'num' => $occupancy,    'den' => '/100',                 'unit' => 'ANLIK %',   'color' => '#ff3b72', 'dot' => '#fa114f' ],
                [ 'lbl' => 'Memnuniyet', 'num' => $rev_avg,      'den' => '/5.0',                 'unit' => 'ÜYE PUANI', 'color' => '#a4ff2e', 'dot' => '#a4ff2e', 'float' => true ],
                [ 'lbl' => 'Otopark',    'num' => $parking_free, 'den' => '/' . $parking_total,   'unit' => 'BOŞ ALAN',  'color' => '#26e6ff', 'dot' => '#26e6ff' ],
            ],
        ],
        'revenue' => [
            'sub'   => 'Toplam ' . apex_format_tl( $revenue ) . ' Gelir Payı',
            'vals'  => [ max( 6, $share_court ), max( 6, $share_pt ), max( 6, $share_other ) ],
            'items' => [
                [ 'lbl' => 'Salon',     'num' => $share_court, 'den' => '%', 'unit' => apex_format_tl( $rev_court ), 'color' => '#ff3b72', 'dot' => '#fa114f' ],
                [ 'lbl' => 'PT',        'num' => $share_pt,    'den' => '%', 'unit' => apex_format_tl( $rev_pt ),    'color' => '#a4ff2e', 'dot' => '#a4ff2e' ],
                [ 'lbl' => 'Üye+Kafe',  'num' => $share_other, 'den' => '%', 'unit' => apex_format_tl( $rev_mem + $rev_cafe ), 'color' => '#26e6ff', 'dot' => '#26e6ff' ],
            ],
        ],
    ];
    ?>
    <style>
        .apex-ad-wrap {
            --ad-bg: #0b0c10;
            --ad-card: #131419;
            --ad-border: rgba(255, 255, 255, 0.08);
            --ad-pink: #fa114f;
            --ad-pink-hi: #ff4b7c;
            --ad-lime: #a4ff2e;
            --ad-cyan: #26e6ff;
            --ad-amber: #ffbe0b;
            max-width: 1360px;
            margin: 16px 20px 44px 0;
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
            color: #f5f5f7;
            font-variant-numeric: tabular-nums;
            position: relative;
        }
        .apex-ad-wrap * { box-sizing: border-box; }

        /* Üst Hero Banner */
        .apex-ad-banner {
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(ellipse 46% 115% at 92% 0%, rgba(250, 17, 79, 0.24), transparent 62%),
                radial-gradient(ellipse 42% 100% at 8% 100%, rgba(38, 230, 255, 0.1), transparent 60%),
                #111217;
            border: 1px solid rgba(255, 255, 255, 0.09);
            border-radius: 22px;
            padding: 22px 26px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
            margin-bottom: 18px;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.34), inset 0 1px 0 rgba(255, 255, 255, 0.08);
        }
        .apex-ad-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.09em;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 6px;
        }
        .apex-ad-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--ad-lime);
            box-shadow: 0 0 10px rgba(164, 255, 46, 0.9);
            animation: apexAdPulse 2.4s ease-in-out infinite;
        }
        @keyframes apexAdPulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.25); opacity: 0.65; }
        }
        .apex-ad-h1 { margin: 0; font-size: 23px; font-weight: 800; color: #fff; letter-spacing: -0.025em; }
        .apex-ad-sub { margin: 7px 0 0; color: #a1a1aa; font-size: 13px; display: flex; flex-wrap: wrap; gap: 14px; align-items: center; }
        .apex-ad-sub strong { color: #fff; }
        .apex-ad-pills { display: flex; align-items: center; gap: 9px; flex-wrap: wrap; }
        .apex-ad-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 16px;
            border-radius: 999px;
            font-size: 12.5px;
            font-weight: 700;
            text-decoration: none;
            border: 1px solid rgba(255, 255, 255, 0.14);
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            cursor: pointer;
            transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
        }
        .apex-ad-btn:hover { background: rgba(255, 255, 255, 0.12); border-color: rgba(255, 255, 255, 0.24); color: #fff; transform: translateY(-1px); }
        .apex-ad-btn-red {
            background: linear-gradient(135deg, #fa114f 0%, #d40f40 100%);
            border-color: rgba(255, 110, 145, 0.45);
            box-shadow: 0 8px 24px rgba(250, 17, 79, 0.35);
        }
        .apex-ad-btn-red:hover { background: linear-gradient(135deg, #ff2761 0%, #e51348 100%); box-shadow: 0 10px 28px rgba(250, 17, 79, 0.52); color: #fff; }

        /* 4 Neon Glow KPI Kartı */
        .apex-ad-kpis {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 18px;
        }
        .apex-ad-kpi {
            position: relative;
            overflow: hidden;
            background: linear-gradient(180deg, #16171d 0%, #101116 100%);
            border: 1px solid var(--ad-border);
            border-radius: 18px;
            padding: 18px 20px;
            box-shadow: 0 14px 36px rgba(0, 0, 0, 0.28), inset 0 1px 0 rgba(255, 255, 255, 0.08);
            transition: border-color 0.22s ease, transform 0.22s ease;
        }
        .apex-ad-kpi::after {
            content: '';
            position: absolute;
            top: 0;
            left: 16px;
            right: 16px;
            height: 2.5px;
            border-radius: 99px;
            background: var(--kpi-accent, #fa114f);
            box-shadow: 0 0 14px 1px var(--kpi-accent, #fa114f);
        }
        .apex-ad-kpi:hover { border-color: rgba(255, 255, 255, 0.16); transform: translateY(-2px); }
        .apex-ad-kpi-lbl { font-size: 11px; font-weight: 700; letter-spacing: 0.07em; text-transform: uppercase; color: #8c90a0; }
        .apex-ad-kpi-val { font-size: 28px; font-weight: 800; color: #fff; letter-spacing: -0.025em; margin-top: 6px; line-height: 1.15; }
        .apex-ad-kpi-meta { font-size: 12px; font-weight: 600; margin-top: 5px; color: #9ca3af; }

        /* Üst 3 + Alt 2 Obsidian Neon Grid */
        .apex-ad-top3 { display: grid; grid-template-columns: minmax(0, 1.05fr) minmax(0, 1.15fr) minmax(0, 0.95fr); gap: 18px; margin-bottom: 18px; }
        .apex-ad-bot2 { display: grid; grid-template-columns: minmax(0, 0.92fr) minmax(0, 1.38fr); gap: 18px; margin-bottom: 18px; }
        .apex-ad-card {
            position: relative;
            overflow: hidden;
            background: linear-gradient(180deg, #16171d 0%, #111216 100%);
            border: 1px solid var(--ad-border);
            border-radius: 22px;
            padding: 20px 22px;
            box-shadow: 0 18px 46px rgba(0, 0, 0, 0.32), inset 0 1px 0 rgba(255, 255, 255, 0.08);
            min-width: 0;
            transition: border-color 0.25s ease;
        }
        .apex-ad-card:hover { border-color: rgba(255, 255, 255, 0.14); }
        .apex-ad-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            pointer-events: none;
            opacity: 0;
            background: radial-gradient(300px circle at var(--mx, 50%) var(--my, 30%), rgba(255, 255, 255, 0.055), transparent 70%);
            transition: opacity 0.25s ease;
        }
        .apex-ad-card:hover::before { opacity: 1; }
        .apex-ad-card-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }
        .apex-ad-card-title { font-size: 14.5px; font-weight: 700; color: #fff; letter-spacing: -0.015em; margin: 0; }
        .apex-ad-card-sub { font-size: 11.5px; font-weight: 600; color: #8c90a0; }

        /* Segmented Toggle */
        .apex-ad-seg {
            display: inline-flex;
            gap: 3px;
            padding: 3px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .apex-ad-seg-btn {
            border: 0;
            background: transparent;
            color: #9ca3af;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 999px;
            cursor: pointer;
            transition: background 0.18s ease, color 0.18s ease, box-shadow 0.18s ease;
        }
        .apex-ad-seg-btn.is-active {
            background: rgba(255, 255, 255, 0.14);
            color: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.28);
        }

        /* Aktivite Halkaları Kartı */
        .apex-ad-rings-card {
            background:
                radial-gradient(circle at 50% 42%, rgba(164, 255, 46, 0.08) 0%, rgba(250, 17, 79, 0.08) 38%, transparent 72%),
                linear-gradient(180deg, #15161b 0%, #0f1014 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .apex-ad-rings-stage { display: flex; align-items: center; justify-content: center; padding: 2px 0 6px; }
        .apex-ad-rings-svg { width: 196px; height: 196px; display: block; overflow: visible; filter: drop-shadow(0 0 24px rgba(250, 17, 79, 0.14)); }
        .apex-ad-ring-metrics {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
            padding-top: 10px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            text-align: center;
        }
        .apex-ad-rm-lbl { display: inline-flex; align-items: center; justify-content: center; gap: 5px; font-size: 11px; font-weight: 600; color: #a1a1aa; margin-bottom: 3px; }
        .apex-ad-rm-dot { width: 7px; height: 7px; border-radius: 50%; display: inline-block; }
        .apex-ad-rm-val { font-size: 18px; font-weight: 800; letter-spacing: -0.02em; line-height: 1.1; }
        .apex-ad-rm-val small { font-size: 11.5px; font-weight: 600; opacity: 0.58; }
        .apex-ad-rm-unit { font-size: 9.5px; font-weight: 700; letter-spacing: 0.07em; color: #71717a; margin-top: 2px; }

        /* SVG Grafikler */
        .apex-ad-svg { width: 100%; height: auto; display: block; overflow: visible; }
        .apex-ad-bar-group { cursor: pointer; transition: opacity 0.2s ease; }
        .apex-ad-bar-group:hover .apex-ad-bar-real,
        .apex-ad-bar-group.is-active .apex-ad-bar-real {
            filter: drop-shadow(0 0 10px rgba(250, 17, 79, 0.7));
        }
        .apex-ad-area-node { cursor: pointer; }
        .apex-ad-legend {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            font-size: 11.5px;
            color: #9ca3af;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }
        .apex-ad-legend-dots { display: flex; gap: 14px; align-items: center; }
        .apex-ad-legend-dots span { display: inline-flex; align-items: center; gap: 6px; }
        .apex-ad-legend-dots i { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }

        /* Yüzen Liquid Glass Tooltip */
        .apex-ad-tooltip {
            position: fixed;
            z-index: 99999;
            pointer-events: none;
            opacity: 0;
            transform: translate3d(-50%, -100%, 0) translateY(-10px);
            background: rgba(16, 17, 23, 0.94);
            backdrop-filter: blur(14px) saturate(1.6);
            -webkit-backdrop-filter: blur(14px) saturate(1.6);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 12px;
            padding: 9px 13px;
            font-size: 12px;
            line-height: 1.45;
            color: #f5f5f7;
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.55);
            transition: opacity 0.15s ease;
            white-space: nowrap;
        }
        .apex-ad-tooltip.is-visible { opacity: 1; }
        .apex-ad-tooltip strong { display: block; color: #fff; font-size: 12.5px; margin-bottom: 2px; }

        /* Neon Glow Gelir Barları (Parlayan Küre Uçlu) */
        .apex-ad-bars { display: grid; gap: 11px; }
        .apex-ad-bar-item {
            display: grid;
            grid-template-columns: 150px minmax(0, 1fr) 74px;
            align-items: center;
            gap: 12px;
            font-size: 12.5px;
            padding: 9px 11px;
            border-radius: 13px;
            border: 1px solid transparent;
            cursor: pointer;
            transition: background 0.2s ease, border-color 0.2s ease;
        }
        .apex-ad-bar-item:hover { background: rgba(255, 255, 255, 0.04); }
        .apex-ad-bar-item.is-selected {
            background: rgba(250, 17, 79, 0.1);
            border-color: rgba(250, 17, 79, 0.34);
        }
        .apex-ad-bar-track {
            height: 9px;
            background: rgba(255, 255, 255, 0.07);
            border-radius: 99px;
            position: relative;
            overflow: visible;
        }
        .apex-ad-bar-fill {
            height: 100%;
            border-radius: 99px;
            position: relative;
            transition: width 0.65s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .apex-ad-bar-fill::after {
            content: '';
            position: absolute;
            right: -3px;
            top: 50%;
            width: 11px;
            height: 11px;
            transform: translateY(-50%);
            border-radius: 50%;
            background: #ffffff;
            box-shadow: 0 0 10px 2px currentColor;
        }
        .apex-ad-bar-foot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            font-size: 12px;
            color: #9ca3af;
        }

        /* Canlı Operasyon Akış Tablosu */
        .apex-ad-table-wrap { overflow-x: auto; }
        .apex-ad-table { width: 100%; border-collapse: collapse; min-width: 500px; }
        .apex-ad-table th {
            text-align: left;
            font-size: 10.5px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #7c8194;
            padding: 9px 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .apex-ad-table td {
            padding: 11px 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 13px;
            color: #e4e4e7;
            vertical-align: middle;
        }
        .apex-ad-table tbody tr { transition: background 0.18s ease; }
        .apex-ad-table tbody tr:hover { background: rgba(255, 255, 255, 0.045); }
        .apex-ad-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid transparent;
            white-space: nowrap;
        }

        /* Alt CSV & Hızlı Yönetim Bandı */
        .apex-ad-toolbar {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 18px;
        }
        .apex-ad-chip-row { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
        .apex-ad-chip-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 13px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            color: #e4e4e7;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.12);
            transition: background 0.18s ease, border-color 0.18s ease, color 0.18s ease, transform 0.18s ease;
        }
        .apex-ad-chip-btn:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.24);
            color: #fff;
            transform: translateY(-1px);
        }

        @media (max-width: 1180px) {
            .apex-ad-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .apex-ad-top3, .apex-ad-bot2, .apex-ad-toolbar { grid-template-columns: 1fr; }
        }
        @media (max-width: 640px) {
            .apex-ad-kpis { grid-template-columns: 1fr; }
            .apex-ad-bar-item { grid-template-columns: 110px minmax(0, 1fr) 64px; }
        }
    </style>

    <div class="wrap apex-ad-wrap" id="apex-ad-root">
        <div class="apex-ad-tooltip" id="apex-ad-tooltip" role="tooltip"></div>

        <!-- ÜST HERO BANNER -->
        <div class="apex-ad-banner">
            <div>
                <div class="apex-ad-eyebrow">
                    <span class="apex-ad-dot"></span>
                    <span>APEX ARENA YÖNETİM MERKEZİ · v<?php echo esc_html( APEX_VERSION ); ?> · CANLI MYSQL TELEMETRİ</span>
                </div>
                <h1 class="apex-ad-h1">Spor Kompleksi Operasyon Paneli</h1>
                <div class="apex-ad-sub">
                    <span>Toplam Hacim: <strong style="color:#a4ff2e;"><?php echo esc_html( apex_format_tl( $revenue ) ); ?></strong></span>
                    <span>Hakemli Maç: <strong style="color:#ff4b7c;"><?php echo esc_html( $ref_count ); ?></strong></span>
                    <span>AI Sohbet: <strong style="color:#26e6ff;"><?php echo esc_html( $chat_count ); ?></strong> (<?php echo esc_html( $ai_mode_lbl ); ?>)</span>
                    <span>Memnuniyet: <strong style="color:#ffbe0b;">★ <?php echo esc_html( $rev_avg ); ?>/5.0</strong></span>
                </div>
            </div>
            <div class="apex-ad-pills">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=apex-email-center' ) ); ?>" class="apex-ad-btn">📧 E-posta &amp; Hatırlatma</a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=apex-coach-portal' ) ); ?>" class="apex-ad-btn apex-ad-btn-red">Koç Grafik Paneli →</a>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="apex-ad-btn" target="_blank">Canlı Siteyi Aç ↗</a>
                <a href="<?php echo esc_url( wp_logout_url( wp_login_url( admin_url( 'admin.php?page=apex-arena-dashboard' ) ) ) ); ?>" class="apex-ad-btn">Güvenli Çıkış</a>
            </div>
        </div>

        <!-- 4 NEON GLOW KPI KARTI -->
        <div class="apex-ad-kpis">
            <div class="apex-ad-kpi" style="--kpi-accent:#fa114f;">
                <div class="apex-ad-kpi-lbl">Aktif Salon Rezervasyonu</div>
                <div class="apex-ad-kpi-val"><?php echo esc_html( $court_count ); ?></div>
                <div class="apex-ad-kpi-meta" style="color:#a4ff2e;">Hakemli Maç Talebi: <?php echo esc_html( $ref_count ); ?></div>
            </div>
            <div class="apex-ad-kpi" style="--kpi-accent:#a4ff2e;">
                <div class="apex-ad-kpi-lbl">Aktif PT Randevusu</div>
                <div class="apex-ad-kpi-val"><?php echo esc_html( $pt_count ); ?></div>
                <div class="apex-ad-kpi-meta">Bireysel &amp; Grupsal · <?php echo esc_html( apex_format_tl( $rev_pt ) ); ?></div>
            </div>
            <div class="apex-ad-kpi" style="--kpi-accent:#26e6ff;">
                <div class="apex-ad-kpi-lbl">Aktif Üyelikler</div>
                <div class="apex-ad-kpi-val"><?php echo esc_html( $mem_count ); ?></div>
                <div class="apex-ad-kpi-meta">Dijital QR Kartlı · <?php echo esc_html( apex_format_tl( $rev_mem ) ); ?></div>
            </div>
            <div class="apex-ad-kpi" style="--kpi-accent:#ffbe0b;">
                <div class="apex-ad-kpi-lbl">Toplam İşlem Hacmi</div>
                <div class="apex-ad-kpi-val"><?php echo esc_html( apex_format_tl( $revenue ) ); ?></div>
                <div class="apex-ad-kpi-meta" style="color:#ffbe0b;">Kafe: <?php echo esc_html( $cafe_count ); ?> sipariş · <?php echo esc_html( apex_format_tl( $rev_cafe ) ); ?></div>
            </div>
        </div>

        <!-- ÜST SATIR: 3 DİNAMİK NEON GLOW ANALİZ KARTI -->
        <div class="apex-ad-top3">
            <!-- 1. Gelir Kanalı Performansı (Neon Bar Grafik) -->
            <div class="apex-ad-card">
                <div class="apex-ad-card-head">
                    <div>
                        <h2 class="apex-ad-card-title">Gelir Kanalı Performansı</h2>
                        <span class="apex-ad-card-sub">Hedef vs Gerçekleşen · Seçmek için tıkla</span>
                    </div>
                    <span class="apex-ad-card-sub" id="ad-bar-focus" style="color:#ff4b7c;"></span>
                </div>
                <svg class="apex-ad-svg" id="ad-bar-svg" viewBox="0 0 340 190" role="img" aria-label="Gelir kanalı performans bar grafiği"></svg>
                <div class="apex-ad-legend">
                    <div class="apex-ad-legend-dots">
                        <span><i style="background:#4b5268;"></i>Hedef</span>
                        <span><i style="background:#fa114f;box-shadow:0 0 8px #fa114f;"></i>Gerçekleşen</span>
                    </div>
                    <span style="font-size:11px;color:#71717a;">Canlı senkron</span>
                </div>
            </div>

            <!-- 2. Haftalık Operasyon & Etkileşim Trendi (Çift Neon Eğri) -->
            <div class="apex-ad-card">
                <div class="apex-ad-card-head">
                    <div>
                        <h2 class="apex-ad-card-title">Operasyon &amp; AI Trendi</h2>
                        <span class="apex-ad-card-sub">Eğri üzerinde gezinerek günlük ölçüm gör</span>
                    </div>
                    <span class="apex-ad-card-sub" id="ad-area-focus" style="color:#a4ff2e;"></span>
                </div>
                <svg class="apex-ad-svg" id="ad-area-svg" viewBox="0 0 350 190" role="img" aria-label="Son 7 günlük operasyon ve AI etkileşim trend grafiği"></svg>
                <div class="apex-ad-legend">
                    <div class="apex-ad-legend-dots">
                        <span><i style="background:#a4ff2e;box-shadow:0 0 8px #a4ff2e;"></i>Talep &amp; Gelir</span>
                        <span><i style="background:#26e6ff;box-shadow:0 0 8px #26e6ff;"></i>Tesis &amp; AI</span>
                    </div>
                    <span style="font-size:11px;color:#71717a;">Neon telemetri</span>
                </div>
            </div>

            <!-- 3. Apple Fitness Tarzı İç İçe 3 Glow Tesis Aktivite Halkası -->
            <div class="apex-ad-card apex-ad-rings-card">
                <div class="apex-ad-card-head" style="margin-bottom:6px;">
                    <div>
                        <h2 class="apex-ad-card-title">Tesis Aktivite Halkaları</h2>
                        <span class="apex-ad-card-sub" id="ad-ring-subject">Canlı Tesis Telemetrisi</span>
                    </div>
                    <div class="apex-ad-seg" role="tablist" aria-label="Halka görünüm modu">
                        <button type="button" class="apex-ad-seg-btn is-active" data-ad-ring-mode="facility">Tesis</button>
                        <button type="button" class="apex-ad-seg-btn" data-ad-ring-mode="revenue">Gelir</button>
                    </div>
                </div>

                <div class="apex-ad-rings-stage">
                    <svg class="apex-ad-rings-svg" id="ad-rings-svg" viewBox="0 0 220 220" role="img" aria-label="Doluluk, memnuniyet ve otopark aktivite halkaları">
                        <defs>
                            <linearGradient id="adRingGradPink" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#ff2d68" />
                                <stop offset="100%" stop-color="#fa114f" />
                            </linearGradient>
                            <linearGradient id="adRingGradLime" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#c9ff33" />
                                <stop offset="100%" stop-color="#87e817" />
                            </linearGradient>
                            <linearGradient id="adRingGradCyan" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#5ff5ff" />
                                <stop offset="100%" stop-color="#00c4e6" />
                            </linearGradient>

                            <radialGradient id="adOrbPink" cx="35%" cy="35%" r="65%">
                                <stop offset="0%" stop-color="#ffffff" />
                                <stop offset="42%" stop-color="#ff7ba3" />
                                <stop offset="100%" stop-color="#fa114f" />
                            </radialGradient>
                            <radialGradient id="adOrbLime" cx="35%" cy="35%" r="65%">
                                <stop offset="0%" stop-color="#ffffff" />
                                <stop offset="45%" stop-color="#dbff6e" />
                                <stop offset="100%" stop-color="#8de81c" />
                            </radialGradient>
                            <radialGradient id="adOrbCyan" cx="35%" cy="35%" r="65%">
                                <stop offset="0%" stop-color="#ffffff" />
                                <stop offset="45%" stop-color="#9cfaff" />
                                <stop offset="100%" stop-color="#00cce8" />
                            </radialGradient>

                            <filter id="adGlowPink" x="-45%" y="-45%" width="190%" height="190%">
                                <feGaussianBlur stdDeviation="4.8" result="blur" />
                                <feMerge><feMergeNode in="blur" /><feMergeNode in="SourceGraphic" /></feMerge>
                            </filter>
                            <filter id="adGlowLime" x="-45%" y="-45%" width="190%" height="190%">
                                <feGaussianBlur stdDeviation="4.5" result="blur" />
                                <feMerge><feMergeNode in="blur" /><feMergeNode in="SourceGraphic" /></feMerge>
                            </filter>
                            <filter id="adGlowCyan" x="-45%" y="-45%" width="190%" height="190%">
                                <feGaussianBlur stdDeviation="4.5" result="blur" />
                                <feMerge><feMergeNode in="blur" /><feMergeNode in="SourceGraphic" /></feMerge>
                            </filter>
                            <filter id="adTipShadow" x="-60%" y="-60%" width="220%" height="220%">
                                <feGaussianBlur stdDeviation="2.6" />
                            </filter>
                        </defs>

                        <circle cx="110" cy="110" r="86" fill="none" stroke="rgba(250, 17, 79, 0.17)" stroke-width="19" />
                        <circle cx="110" cy="110" r="63" fill="none" stroke="rgba(164, 255, 46, 0.16)" stroke-width="19" />
                        <circle cx="110" cy="110" r="40" fill="none" stroke="rgba(38, 230, 255, 0.16)" stroke-width="19" />

                        <g transform="rotate(-90 110 110)">
                            <circle id="ad-ring-pink" cx="110" cy="110" r="86" fill="none" stroke="url(#adRingGradPink)" stroke-width="19" stroke-linecap="round" filter="url(#adGlowPink)" />
                            <circle id="ad-ring-lime" cx="110" cy="110" r="63" fill="none" stroke="url(#adRingGradLime)" stroke-width="19" stroke-linecap="round" filter="url(#adGlowLime)" />
                            <circle id="ad-ring-cyan" cx="110" cy="110" r="40" fill="none" stroke="url(#adRingGradCyan)" stroke-width="19" stroke-linecap="round" filter="url(#adGlowCyan)" />
                        </g>

                        <circle id="ad-orb-pink-sh" cx="110" cy="24" r="9.5" fill="rgba(0,0,0,0.52)" filter="url(#adTipShadow)" />
                        <circle id="ad-orb-pink" cx="110" cy="24" r="9.2" fill="url(#adOrbPink)" filter="url(#adGlowPink)" />

                        <circle id="ad-orb-lime-sh" cx="110" cy="47" r="9.5" fill="rgba(0,0,0,0.52)" filter="url(#adTipShadow)" />
                        <circle id="ad-orb-lime" cx="110" cy="47" r="9.2" fill="url(#adOrbLime)" filter="url(#adGlowLime)" />

                        <circle id="ad-orb-cyan-sh" cx="110" cy="70" r="9.5" fill="rgba(0,0,0,0.52)" filter="url(#adTipShadow)" />
                        <circle id="ad-orb-cyan" cx="110" cy="70" r="9.2" fill="url(#adOrbCyan)" filter="url(#adGlowCyan)" />

                        <circle cx="110" cy="110" r="24" fill="#111216" />
                    </svg>
                </div>

                <div class="apex-ad-ring-metrics" id="ad-ring-metrics"></div>
            </div>
        </div>

        <!-- ALT SATIR: SOL NEON GELİR DAĞILIMI + SAĞ CANLI OPERASYON AKIŞI -->
        <div class="apex-ad-bot2">
            <div class="apex-ad-card">
                <div class="apex-ad-card-head">
                    <div>
                        <h2 class="apex-ad-card-title">Gelir Dağılımı</h2>
                        <span class="apex-ad-card-sub">Canlı MySQL kanal bazlı ciro yoğunluğu</span>
                    </div>
                    <span class="apex-ad-card-sub" style="color:#26e6ff;">Canlı</span>
                </div>
                <div class="apex-ad-bars" id="ad-rev-bars"></div>
                <div class="apex-ad-bar-foot">
                    <span>Üye Memnuniyeti: <strong style="color:#ffbe0b;">★ <?php echo esc_html( $rev_avg ); ?> / 5.0</strong></span>
                    <span>Otopark Doluluk: <strong style="color:#26e6ff;"><?php echo esc_html( $parking_used . '/' . $parking_total ); ?> araç</strong></span>
                </div>
            </div>

            <div class="apex-ad-card">
                <div class="apex-ad-card-head">
                    <div>
                        <h2 class="apex-ad-card-title">Canlı Operasyon Akışı</h2>
                        <span class="apex-ad-card-sub">Salon, PT, üyelik ve protein kafe son hareketleri</span>
                    </div>
                    <div class="apex-ad-seg" id="ad-feed-filters" role="tablist" aria-label="Kategori filtresi">
                        <button type="button" class="apex-ad-seg-btn is-active" data-feed-filter="all">Tümü</button>
                        <button type="button" class="apex-ad-seg-btn" data-feed-filter="courts">Salon</button>
                        <button type="button" class="apex-ad-seg-btn" data-feed-filter="pt">PT</button>
                        <button type="button" class="apex-ad-seg-btn" data-feed-filter="memberships">Üyelik</button>
                        <button type="button" class="apex-ad-seg-btn" data-feed-filter="cafe">Kafe</button>
                    </div>
                </div>
                <div class="apex-ad-table-wrap">
                    <table class="apex-ad-table">
                        <thead>
                            <tr><th>#</th><th>İşlem &amp; Müşteri</th><th>Kategori</th><th>Tutar</th><th>Durum</th></tr>
                        </thead>
                        <tbody id="ad-feed-tbody"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ALT RAPOR MERKEZİ & HIZLI YÖNETİM BANDI -->
        <div class="apex-ad-toolbar">
            <div class="apex-ad-card">
                <div class="apex-ad-card-head" style="margin-bottom:4px;">
                    <div>
                        <h2 class="apex-ad-card-title">Excel / CSV Rapor Merkezi</h2>
                        <span class="apex-ad-card-sub">Türkçe Excel uyumlu (UTF-8 BOM) tek tıkla dışa aktarma</span>
                    </div>
                </div>
                <div class="apex-ad-chip-row">
                    <a href="<?php echo esc_url( admin_url( "admin.php?page=apex-arena-dashboard&apex_export_csv=courts&_wpnonce={$csv_nonce}" ) ); ?>" class="apex-ad-chip-btn">⬇ Salonlar CSV</a>
                    <a href="<?php echo esc_url( admin_url( "admin.php?page=apex-arena-dashboard&apex_export_csv=pt&_wpnonce={$csv_nonce}" ) ); ?>" class="apex-ad-chip-btn">⬇ PT CSV</a>
                    <a href="<?php echo esc_url( admin_url( "admin.php?page=apex-arena-dashboard&apex_export_csv=memberships&_wpnonce={$csv_nonce}" ) ); ?>" class="apex-ad-chip-btn">⬇ Üyelikler CSV</a>
                    <a href="<?php echo esc_url( admin_url( "admin.php?page=apex-arena-dashboard&apex_export_csv=cafe&_wpnonce={$csv_nonce}" ) ); ?>" class="apex-ad-chip-btn">⬇ Kafe CSV</a>
                    <a href="<?php echo esc_url( admin_url( "admin.php?page=apex-arena-dashboard&apex_export_csv=reviews&_wpnonce={$csv_nonce}" ) ); ?>" class="apex-ad-chip-btn">⬇ Yorumlar CSV</a>
                </div>
            </div>

            <div class="apex-ad-card">
                <div class="apex-ad-card-head" style="margin-bottom:4px;">
                    <div>
                        <h2 class="apex-ad-card-title">Hızlı Yönetim Modülleri</h2>
                        <span class="apex-ad-card-sub">Tüm operasyon ve AI ayar sayfalarına doğrudan geçiş</span>
                    </div>
                </div>
                <div class="apex-ad-chip-row">
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=apex-court-bookings' ) ); ?>" class="apex-ad-chip-btn" style="border-color:rgba(250,17,79,0.42);color:#ff6b93;">Salon Rezervasyonları</a>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=apex-pt-appointments' ) ); ?>" class="apex-ad-chip-btn">PT Randevuları</a>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=apex-memberships' ) ); ?>" class="apex-ad-chip-btn">Üyelikler &amp; Mesajlar</a>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=apex-cafe-reviews' ) ); ?>" class="apex-ad-chip-btn">Kafe &amp; Yorumlar</a>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=apex-settings' ) ); ?>" class="apex-ad-chip-btn">AI &amp; Tesis Ayarları</a>
                    <?php if ( defined( 'WPSEO_VERSION' ) ) : ?>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpseo_dashboard' ) ); ?>" class="apex-ad-chip-btn">Yoast SEO</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    (function(){
        const CHANNELS = <?php echo wp_json_encode( $channels, JSON_UNESCAPED_UNICODE ); ?>;
        const TREND = <?php echo wp_json_encode( $trend_days, JSON_UNESCAPED_UNICODE ); ?>;
        const RINGS_DATA = <?php echo wp_json_encode( $ring_payload, JSON_UNESCAPED_UNICODE ); ?>;
        const FEED = <?php echo wp_json_encode( $feed, JSON_UNESCAPED_UNICODE ); ?>;
        const TOTAL_REV = Math.max(1, <?php echo (int) $revenue; ?>);

        let activeChannel = CHANNELS.length ? CHANNELS[0].key : 'courts';
        let activeTrendIdx = Math.max(0, TREND.length - 1);
        let ringMode = 'facility';
        let feedFilter = 'all';

        const $ = (id) => document.getElementById(id);
        const tip = $('apex-ad-tooltip');

        function esc(str) {
            return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
        function fmtTL(val) {
            return '₺' + Number(val || 0).toLocaleString('tr-TR');
        }
        function showTip(ev, html) {
            if (!tip) return;
            tip.innerHTML = html;
            tip.style.left = ev.clientX + 'px';
            tip.style.top = ev.clientY + 'px';
            tip.classList.add('is-visible');
        }
        function hideTip() {
            if (tip) tip.classList.remove('is-visible');
        }

        // Kart hover cam ışığı
        document.querySelectorAll('.apex-ad-card').forEach((card) => {
            card.addEventListener('pointermove', (e) => {
                const r = card.getBoundingClientRect();
                card.style.setProperty('--mx', (e.clientX - r.left).toFixed(0) + 'px');
                card.style.setProperty('--my', (e.clientY - r.top).toFixed(0) + 'px');
            }, { passive: true });
        });

        // =====================================================================
        // 1. APPLE FITNESS 3 GLOW TESİS AKTİVİTE HALKASI (rAF Animasyonlu)
        // =====================================================================
        const RINGS = [
            { r: 86, el: $('ad-ring-pink'), orb: $('ad-orb-pink'), sh: $('ad-orb-pink-sh'), cur: 0 },
            { r: 63, el: $('ad-ring-lime'), orb: $('ad-orb-lime'), sh: $('ad-orb-lime-sh'), cur: 0 },
            { r: 40, el: $('ad-ring-cyan'), orb: $('ad-orb-cyan'), sh: $('ad-orb-cyan-sh'), cur: 0 },
        ];
        RINGS.forEach((rg) => {
            rg.circ = 2 * Math.PI * rg.r;
            if (rg.el) {
                rg.el.setAttribute('stroke-dasharray', rg.circ.toFixed(2));
                rg.el.setAttribute('stroke-dashoffset', rg.circ.toFixed(2));
            }
        });

        let ringRaf = null;
        function setRingVisual(rg, pct) {
            const clamped = Math.max(0.03, Math.min(1, pct / 100));
            const offset = rg.circ * (1 - clamped);
            if (rg.el) rg.el.setAttribute('stroke-dashoffset', offset.toFixed(2));
            const deg = -90 + clamped * 360;
            const rad = (deg * Math.PI) / 180;
            const ox = 110 + rg.r * Math.cos(rad);
            const oy = 110 + rg.r * Math.sin(rad);
            const shRad = ((deg - 7) * Math.PI) / 180;
            const sx = 110 + rg.r * Math.cos(shRad);
            const sy = 110 + rg.r * Math.sin(shRad);
            if (rg.orb) {
                rg.orb.setAttribute('cx', ox.toFixed(2));
                rg.orb.setAttribute('cy', oy.toFixed(2));
            }
            if (rg.sh) {
                rg.sh.setAttribute('cx', sx.toFixed(2));
                rg.sh.setAttribute('cy', sy.toFixed(2));
            }
        }

        function renderRings() {
            const cfg = RINGS_DATA[ringMode] || RINGS_DATA.facility;
            const subj = $('ad-ring-subject');
            const metricsBox = $('ad-ring-metrics');
            if (subj) subj.textContent = cfg.sub;
            if (metricsBox) {
                metricsBox.innerHTML = cfg.items.map((it, idx) => `
                    <div>
                        <div class="apex-ad-rm-lbl"><span class="apex-ad-rm-dot" style="background:${it.dot};box-shadow:0 0 8px ${it.dot};"></span>${esc(it.lbl)}</div>
                        <div class="apex-ad-rm-val" style="color:${it.color};" id="ad-rm-val-${idx}">0<small>${esc(it.den)}</small></div>
                        <div class="apex-ad-rm-unit">${esc(it.unit)}</div>
                    </div>
                `).join('');
            }
            if (ringRaf) cancelAnimationFrame(ringRaf);
            const starts = RINGS.map((rg) => rg.cur);
            const targets = cfg.vals;
            const t0 = performance.now();
            const dur = 780;

            const step = (now) => {
                const p = Math.min(1, (now - t0) / dur);
                const ease = 1 - Math.pow(1 - p, 3);
                RINGS.forEach((rg, i) => {
                    const val = starts[i] + (targets[i] - starts[i]) * ease;
                    rg.cur = val;
                    setRingVisual(rg, val);
                    const valEl = $('ad-rm-val-' + i);
                    const item = cfg.items[i];
                    if (valEl && item) {
                        const shown = item.float ? (item.num * ease).toFixed(1) : Math.round(item.num * ease);
                        valEl.innerHTML = (p >= 1 ? (item.float ? Number(item.num).toFixed(1) : item.num) : shown) + '<small>' + esc(item.den) + '</small>';
                    }
                });
                if (p < 1) ringRaf = requestAnimationFrame(step);
            };
            ringRaf = requestAnimationFrame(step);
        }

        // =====================================================================
        // 2. GELİR KANALI NEON BAR GRAFİĞİ
        // =====================================================================
        function renderBarChart() {
            const svg = $('ad-bar-svg');
            if (!svg || !CHANNELS.length) return;
            const maxV = Math.max(4000, ...CHANNELS.map((c) => Math.max(c.target, c.value)));
            const baseY = 158;
            const chartH = 128;
            const leftX = 40;
            const usableW = 286;
            const slotW = usableW / CHANNELS.length;
            const barW = Math.min(16, Math.max(11, slotW * 0.24));

            let html = `<defs>
                <linearGradient id="adBarRealGrad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#ff4b7c" />
                    <stop offset="100%" stop-color="#c40b3e" />
                </linearGradient>
                <linearGradient id="adBarActiveGrad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#c9ff33" />
                    <stop offset="100%" stop-color="#6ac90f" />
                </linearGradient>
                <linearGradient id="adBarTargetGrad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#434a62" />
                    <stop offset="100%" stop-color="#262a38" />
                </linearGradient>
            </defs>`;

            [0, 0.5, 1].forEach((frac) => {
                const gy = baseY - frac * chartH;
                const valK = Math.round((maxV * frac) / 1000);
                html += `<line x1="${leftX}" y1="${gy.toFixed(1)}" x2="332" y2="${gy.toFixed(1)}" stroke="rgba(255,255,255,${frac === 0 ? 0.14 : 0.05})" ${frac > 0 ? 'stroke-dasharray="3 4"' : ''} />`;
                html += `<text x="33" y="${(gy + 3).toFixed(1)}" text-anchor="end" fill="#6b7280" font-size="9">${frac === 0 ? '0' : valK + 'k'}</text>`;
            });

            CHANNELS.forEach((ch, i) => {
                const cx = leftX + slotW * (i + 0.5);
                const hTarget = Math.max(10, (ch.target / maxV) * chartH);
                const hReal = Math.max(8, (ch.value / maxV) * chartH);
                const active = ch.key === activeChannel;
                const fill = active ? 'url(#adBarActiveGrad)' : 'url(#adBarRealGrad)';
                const glow = active ? 'filter:drop-shadow(0 0 10px rgba(164,255,46,0.6));' : 'filter:drop-shadow(0 0 7px rgba(250,17,79,0.35));';

                html += `<g class="apex-ad-bar-group ${active ? 'is-active' : ''}" data-ch-key="${ch.key}">
                    <rect x="${(cx - slotW * 0.45).toFixed(1)}" y="24" width="${(slotW * 0.9).toFixed(1)}" height="${chartH + 16}" fill="transparent" />
                    <rect x="${(cx - barW - 2).toFixed(1)}" y="${(baseY - hTarget).toFixed(1)}" width="${barW.toFixed(1)}" height="${hTarget.toFixed(1)}" rx="4.5" fill="url(#adBarTargetGrad)" />
                    <rect class="apex-ad-bar-real" x="${(cx + 2).toFixed(1)}" y="${(baseY - hReal).toFixed(1)}" width="${barW.toFixed(1)}" height="${hReal.toFixed(1)}" rx="4.5" fill="${fill}" style="${glow}" />
                    <text x="${cx.toFixed(1)}" y="176" text-anchor="middle" fill="${active ? '#ffffff' : '#8c90a0'}" font-weight="${active ? '800' : '600'}" font-size="10.5">${esc(ch.short)}</text>
                </g>`;
            });

            svg.innerHTML = html;
            const cur = CHANNELS.find((c) => c.key === activeChannel) || CHANNELS[0];
            const focusEl = $('ad-bar-focus');
            if (focusEl && cur) {
                const pct = Math.round((cur.value / TOTAL_REV) * 100);
                focusEl.textContent = `${cur.short}: ${fmtTL(cur.value)} (%${pct})`;
            }

            svg.querySelectorAll('[data-ch-key]').forEach((g) => {
                const key = g.getAttribute('data-ch-key');
                const ch = CHANNELS.find((c) => c.key === key);
                if (!ch) return;
                g.addEventListener('pointermove', (ev) => {
                    const pct = Math.round((ch.value / TOTAL_REV) * 100);
                    showTip(ev, `<strong>${esc(ch.label)}</strong>Gelir: <span style="color:#a4ff2e;font-weight:700;">${fmtTL(ch.value)}</span> (%${pct})<br>Hedef: ${fmtTL(ch.target)} · ${esc(ch.meta)}`);
                });
                g.addEventListener('pointerleave', hideTip);
                g.addEventListener('click', () => selectChannel(key));
            });
        }

        // =====================================================================
        // 3. ÇİFT NEON EĞRİ OPERASYON & AI TREND GRAFİĞİ
        // =====================================================================
        function buildSmoothPath(pts, closed, baseY) {
            if (pts.length < 2) return '';
            let d = `M ${pts[0].x.toFixed(1)} ${pts[0].y.toFixed(1)}`;
            for (let i = 0; i < pts.length - 1; i++) {
                const p0 = pts[i], p1 = pts[i + 1];
                const mx = (p0.x + p1.x) / 2;
                d += ` C ${mx.toFixed(1)} ${p0.y.toFixed(1)}, ${mx.toFixed(1)} ${p1.y.toFixed(1)}, ${p1.x.toFixed(1)} ${p1.y.toFixed(1)}`;
            }
            if (closed) {
                const last = pts[pts.length - 1];
                d += ` L ${last.x.toFixed(1)} ${baseY} L ${pts[0].x.toFixed(1)} ${baseY} Z`;
            }
            return d;
        }

        function renderAreaChart() {
            const svg = $('ad-area-svg');
            if (!svg || TREND.length < 2) return;
            const baseY = 158;
            const topY = 26;
            const chartH = baseY - topY;
            const leftX = 32;
            const rightX = 330;
            const stepX = (rightX - leftX) / Math.max(1, TREND.length - 1);

            const demPts = TREND.map((d, i) => ({ x: leftX + i * stepX, y: baseY - (d.demand / 100) * chartH }));
            const engPts = TREND.map((d, i) => ({ x: leftX + i * stepX, y: baseY - (d.engage / 100) * chartH }));

            let html = `<defs>
                <linearGradient id="adAreaLime" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#a4ff2e" stop-opacity="0.34" />
                    <stop offset="100%" stop-color="#a4ff2e" stop-opacity="0.01" />
                </linearGradient>
                <linearGradient id="adAreaCyan" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#26e6ff" stop-opacity="0.36" />
                    <stop offset="100%" stop-color="#26e6ff" stop-opacity="0.02" />
                </linearGradient>
                <filter id="adLineGlow" x="-20%" y="-20%" width="140%" height="140%">
                    <feGaussianBlur stdDeviation="2.6" result="b" />
                    <feMerge><feMergeNode in="b" /><feMergeNode in="SourceGraphic" /></feMerge>
                </filter>
            </defs>`;

            [0, 50, 100].forEach((val) => {
                const gy = baseY - (val / 100) * chartH;
                html += `<line x1="${leftX}" y1="${gy.toFixed(1)}" x2="${rightX}" y2="${gy.toFixed(1)}" stroke="rgba(255,255,255,${val === 0 ? 0.14 : 0.05})" ${val > 0 ? 'stroke-dasharray="3 4"' : ''} />`;
                html += `<text x="24" y="${(gy + 3).toFixed(1)}" text-anchor="end" fill="#6b7280" font-size="9">${val}</text>`;
            });

            html += `<path d="${buildSmoothPath(demPts, true, baseY)}" fill="url(#adAreaLime)" />`;
            html += `<path d="${buildSmoothPath(engPts, true, baseY)}" fill="url(#adAreaCyan)" />`;
            html += `<path d="${buildSmoothPath(demPts, false, baseY)}" fill="none" stroke="#a4ff2e" stroke-width="2.4" filter="url(#adLineGlow)" />`;
            html += `<path d="${buildSmoothPath(engPts, false, baseY)}" fill="none" stroke="#26e6ff" stroke-width="2.4" filter="url(#adLineGlow)" />`;

            TREND.forEach((d, i) => {
                const ax = demPts[i].x, ay = demPts[i].y, ey = engPts[i].y;
                const active = i === activeTrendIdx;
                html += `<g class="apex-ad-area-node" data-trend-idx="${i}">
                    <rect x="${(ax - stepX * 0.45).toFixed(1)}" y="${topY}" width="${(stepX * 0.9).toFixed(1)}" height="${chartH + 22}" fill="transparent" />
                    ${active ? `<line x1="${ax.toFixed(1)}" y1="${topY}" x2="${ax.toFixed(1)}" y2="${baseY}" stroke="rgba(255,255,255,0.22)" stroke-dasharray="3 3" />` : ''}
                    <circle cx="${ax.toFixed(1)}" cy="${ay.toFixed(1)}" r="${active ? 5 : 3.2}" fill="${active ? '#ffffff' : '#a4ff2e'}" stroke="#a4ff2e" stroke-width="2" ${active ? 'filter="url(#adLineGlow)"' : ''} />
                    <circle cx="${ax.toFixed(1)}" cy="${ey.toFixed(1)}" r="${active ? 5 : 3.2}" fill="${active ? '#ffffff' : '#26e6ff'}" stroke="#26e6ff" stroke-width="2" ${active ? 'filter="url(#adLineGlow)"' : ''} />
                    <text x="${ax.toFixed(1)}" y="176" text-anchor="middle" fill="${active ? '#ffffff' : '#8c90a0'}" font-weight="${active ? '800' : '600'}" font-size="10">${esc(d.label)}</text>
                </g>`;
            });

            svg.innerHTML = html;
            const curD = TREND[activeTrendIdx] || TREND[TREND.length - 1];
            const focusEl = $('ad-area-focus');
            if (focusEl && curD) {
                focusEl.textContent = `${curD.label} (${curD.date}): %${curD.demand} / %${curD.engage}`;
            }

            svg.querySelectorAll('[data-trend-idx]').forEach((g) => {
                const idx = Number(g.getAttribute('data-trend-idx'));
                const d = TREND[idx];
                if (!d) return;
                g.addEventListener('pointermove', (ev) => {
                    showTip(ev, `<strong>${esc(d.label)} · ${esc(d.date)}</strong>Talep İndeksi: <span style="color:#a4ff2e;font-weight:700;">%${d.demand}</span><br>Tesis &amp; AI: <span style="color:#26e6ff;font-weight:700;">%${d.engage}</span><br>İşlem: ${d.ops} · AI Sohbet: ${d.chats}${d.rev > 0 ? ' · ' + fmtTL(d.rev) : ''}`);
                });
                g.addEventListener('pointerleave', hideTip);
                g.addEventListener('click', () => {
                    activeTrendIdx = idx;
                    renderAreaChart();
                });
            });
        }

        // =====================================================================
        // 4. NEON GLOW GELİR DAĞILIM BARLARI & CANLI OPERASYON TABLOSU
        // =====================================================================
        function renderRevBars() {
            const box = $('ad-rev-bars');
            if (!box) return;
            const maxVal = Math.max(1, ...CHANNELS.map((c) => c.value));
            box.innerHTML = CHANNELS.map((ch) => {
                const w = Math.max(8, Math.round((ch.value / maxVal) * 100));
                const share = Math.round((ch.value / TOTAL_REV) * 100);
                return `<div class="apex-ad-bar-item ${ch.key === activeChannel ? 'is-selected' : ''}" data-rev-ch="${ch.key}">
                    <div>
                        <strong style="color:#f5f5f7;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${esc(ch.short)}</strong>
                        <span style="font-size:11px;color:#8c90a0;">${esc(ch.meta)}</span>
                    </div>
                    <div class="apex-ad-bar-track">
                        <div class="apex-ad-bar-fill" style="width:${w}%;background:${ch.grad};color:${ch.color};box-shadow:0 0 14px ${ch.color}44;"></div>
                    </div>
                    <div style="text-align:right;">
                        <strong style="color:${ch.color};display:block;">${fmtTL(ch.value)}</strong>
                        <span style="font-size:10.5px;color:#8c90a0;">%${share}</span>
                    </div>
                </div>`;
            }).join('');

            box.querySelectorAll('[data-rev-ch]').forEach((el) => {
                el.addEventListener('click', () => selectChannel(el.getAttribute('data-rev-ch')));
            });
        }

        const CAT_STYLE = {
            courts:      { color: '#ff4b7c', bg: 'rgba(250,17,79,0.16)',  border: 'rgba(250,17,79,0.36)' },
            pt:          { color: '#a4ff2e', bg: 'rgba(164,255,46,0.13)', border: 'rgba(164,255,46,0.34)' },
            memberships: { color: '#26e6ff', bg: 'rgba(38,230,255,0.13)', border: 'rgba(38,230,255,0.34)' },
            cafe:        { color: '#ffbe0b', bg: 'rgba(255,190,11,0.15)', border: 'rgba(255,190,11,0.34)' },
        };
        const STATUS_LBL = {
            confirmed: { txt: 'Onaylı',      color: '#a4ff2e' },
            active:    { txt: 'Aktif',       color: '#26e6ff' },
            ready:     { txt: 'Hazır',       color: '#a4ff2e' },
            preparing: { txt: 'Hazırlanıyor',color: '#ffbe0b' },
            cancelled: { txt: 'İptal',       color: '#fa114f' },
        };

        function renderFeed() {
            const tbody = $('ad-feed-tbody');
            if (!tbody) return;
            const list = FEED.filter((it) => feedFilter === 'all' || it.cat === feedFilter).slice(0, 6);
            if (!list.length) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:#8c90a0;padding:24px;">Bu kategoride kayıt bulunamadı.</td></tr>';
                return;
            }
            tbody.innerHTML = list.map((it, idx) => {
                const cs = CAT_STYLE[it.cat] || CAT_STYLE.courts;
                const st = STATUS_LBL[it.status] || { txt: it.status, color: '#9ca3af' };
                return `<tr>
                    <td><strong>${idx + 1}</strong></td>
                    <td>
                        <a href="${esc(it.url)}" style="color:#fff;font-weight:700;text-decoration:none;">${esc(it.title)}</a>
                        <div style="font-size:11.5px;color:#8c90a0;">${esc(it.sub)}</div>
                    </td>
                    <td><span class="apex-ad-badge" style="color:${cs.color};background:${cs.bg};border-color:${cs.border};">${esc(it.cat_lbl)}</span></td>
                    <td><strong style="color:#fff;">${fmtTL(it.amount)}</strong></td>
                    <td><span style="color:${st.color};font-weight:700;font-size:12px;">● ${esc(st.txt)}</span></td>
                </tr>`;
            }).join('');
        }

        function selectChannel(key) {
            activeChannel = key;
            renderBarChart();
            renderRevBars();
        }

        document.querySelectorAll('[data-ad-ring-mode]').forEach((btn) => {
            btn.addEventListener('click', () => {
                ringMode = btn.getAttribute('data-ad-ring-mode');
                document.querySelectorAll('[data-ad-ring-mode]').forEach((b) => b.classList.toggle('is-active', b === btn));
                renderRings();
            });
        });

        document.querySelectorAll('[data-feed-filter]').forEach((btn) => {
            btn.addEventListener('click', () => {
                feedFilter = btn.getAttribute('data-feed-filter');
                document.querySelectorAll('[data-feed-filter]').forEach((b) => b.classList.toggle('is-active', b === btn));
                renderFeed();
            });
        });

        renderBarChart();
        renderAreaChart();
        renderRings();
        renderRevBars();
        renderFeed();
    })();
    </script>
    <?php
}

/**
 * Salon Rezervasyonları Yönetim Tablosu
 */
function apex_admin_court_bookings_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'apex_court_bookings';

    if ( isset( $_GET['apex_action'], $_GET['id'], $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'apex_booking_act' ) ) {
        $id  = absint( $_GET['id'] );
        $act = sanitize_key( $_GET['apex_action'] );
        if ( in_array( $act, [ 'confirmed', 'cancelled' ], true ) ) {
            $wpdb->update( $table, [ 'status' => $act ], [ 'id' => $id ] );
            echo '<div class="notice notice-success"><p>Rezervasyon durumu güncellendi.</p></div>';
        }
    }

    $rows = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY id DESC LIMIT 100" );
    ?>
    <div class="wrap">
        <h1>Salon Rezervasyonları (Basketbol 1 & 2, Voleybol + Hakem Hizmeti)</h1>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr><th>Kod</th><th>Salon</th><th>Müşteri</th><th>Tarih & Saat</th><th>Hakem</th><th>Toplam</th><th>Durum</th><th>İşlem</th></tr>
            </thead>
            <tbody>
            <?php if ( empty( $rows ) ) : ?>
                <tr><td colspan="8">Henüz salon rezervasyonu bulunmuyor.</td></tr>
            <?php else : foreach ( $rows as $r ) : ?>
                <tr>
                    <td><strong><?php echo esc_html( $r->booking_code ); ?></strong></td>
                    <td><?php echo esc_html( $r->court_name ); ?> (<?php echo esc_html( $r->duration_hours ); ?>s)</td>
                    <td><?php echo esc_html( $r->customer_name ); ?><br><small><?php echo esc_html( $r->customer_phone ); ?> · <?php echo esc_html( $r->customer_email ); ?></small></td>
                    <td><?php echo esc_html( date( 'd.m.Y', strtotime( $r->booking_date ) ) . ' — ' . $r->time_slot ); ?></td>
                    <td><?php echo $r->needs_referee ? '<span style="background:#E31837;color:#fff;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:700;">EVET (+' . esc_html( apex_format_tl( $r->referee_fee ) ) . ')</span>' : 'Hayır'; ?></td>
                    <td><strong><?php echo esc_html( apex_format_tl( $r->total_price ) ); ?></strong></td>
                    <td><?php echo esc_html( strtoupper( $r->status ) ); ?></td>
                    <td>
                        <?php $nonce = wp_create_nonce( 'apex_booking_act' ); ?>
                        <a href="<?php echo esc_url( admin_url( "admin.php?page=apex-court-bookings&apex_action=confirmed&id={$r->id}&_wpnonce={$nonce}" ) ); ?>" class="button button-small">Onayla</a>
                        <a href="<?php echo esc_url( admin_url( "admin.php?page=apex-court-bookings&apex_action=cancelled&id={$r->id}&_wpnonce={$nonce}" ) ); ?>" class="button button-small">İptal</a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}

/**
 * PT Randevuları Yönetim Tablosu
 */
function apex_admin_pt_appointments_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'apex_pt_appointments';
    $rows  = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY id DESC LIMIT 100" );
    ?>
    <div class="wrap">
        <h1>PT Eğitmen Randevuları (Bireysel & Grupsal)</h1>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr><th>Kod</th><th>Antrenör</th><th>Tür & Paket</th><th>Müşteri</th><th>Tarih & Saat</th><th>Ücret</th><th>Durum</th></tr>
            </thead>
            <tbody>
            <?php if ( empty( $rows ) ) : ?>
                <tr><td colspan="7">Henüz PT randevusu bulunmuyor.</td></tr>
            <?php else : foreach ( $rows as $r ) : ?>
                <tr>
                    <td><strong><?php echo esc_html( $r->booking_code ); ?></strong></td>
                    <td><?php echo esc_html( $r->trainer_name ); ?></td>
                    <td><strong><?php echo esc_html( strtoupper( $r->training_type ) ); ?></strong> — <?php echo esc_html( $r->package_name ); ?></td>
                    <td><?php echo esc_html( $r->customer_name ); ?><br><small><?php echo esc_html( $r->customer_phone ); ?></small></td>
                    <td><?php echo esc_html( date( 'd.m.Y', strtotime( $r->appointment_date ) ) . ' — ' . $r->time_slot ); ?></td>
                    <td><?php echo esc_html( apex_format_tl( $r->price ) ); ?></td>
                    <td><?php echo esc_html( strtoupper( $r->status ) ); ?></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}

/**
 * Üyelikler Tablosu
 */
function apex_admin_memberships_page() {
    global $wpdb;
    $rows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}apex_membership_applications ORDER BY id DESC LIMIT 100" );
    $msgs = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}apex_contact_messages ORDER BY id DESC LIMIT 30" );
    ?>
    <div class="wrap">
        <h1>Üyelik Kayıtları & Başvuruları</h1>
        <table class="wp-list-table widefat fixed striped">
            <thead><tr><th>Üye Kodu</th><th>Ad Soyad</th><th>Paket</th><th>Periyot & Ücret</th><th>İletişim</th><th>Hedef</th><th>Durum</th></tr></thead>
            <tbody>
            <?php foreach ( $rows as $r ) : ?>
                <tr>
                    <td><strong><?php echo esc_html( $r->member_code ); ?></strong></td>
                    <td><?php echo esc_html( $r->full_name ); ?></td>
                    <td><?php echo esc_html( $r->plan_name ); ?></td>
                    <td><?php echo esc_html( $r->billing_cycle === 'annual' ? 'Yıllık' : 'Aylık' ); ?> · <?php echo esc_html( apex_format_tl( $r->price ) ); ?></td>
                    <td><?php echo esc_html( $r->phone ); ?><br><small><?php echo esc_html( $r->email ); ?></small></td>
                    <td><?php echo esc_html( $r->goal ); ?></td>
                    <td><?php echo esc_html( strtoupper( $r->status ) ); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <h2 style="margin-top:32px;">Son İletişim Mesajları</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead><tr><th>Ad Soyad</th><th>E-posta & Tel</th><th>Konu</th><th>Mesaj</th><th>Tarih</th></tr></thead>
            <tbody>
            <?php if ( empty( $msgs ) ) : ?>
                <tr><td colspan="5">Henüz iletişim mesajı yok.</td></tr>
            <?php else : foreach ( $msgs as $m ) : ?>
                <tr>
                    <td><?php echo esc_html( $m->full_name ); ?></td>
                    <td><?php echo esc_html( $m->email ); ?><br><small><?php echo esc_html( $m->phone ); ?></small></td>
                    <td><?php echo esc_html( $m->subject ); ?></td>
                    <td><?php echo esc_html( $m->message ); ?></td>
                    <td><?php echo esc_html( $m->created_at ); ?></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}

/**
 * Hibrit AI Chatbot & Tesis Ayarları Sayfası
 */
function apex_admin_settings_page() {
    if ( isset( $_POST['apex_save_settings'] ) && check_admin_referer( 'apex_settings_nonce' ) ) {
        $new = array_merge( apex_get_settings(), [
            'phone'          => sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) ),
            'email'          => sanitize_email( wp_unslash( $_POST['email'] ?? '' ) ),
            'address'        => sanitize_text_field( wp_unslash( $_POST['address'] ?? '' ) ),
            'weekday_hours'  => sanitize_text_field( wp_unslash( $_POST['weekday_hours'] ?? '' ) ),
            'weekend_hours'  => sanitize_text_field( wp_unslash( $_POST['weekend_hours'] ?? '' ) ),
            'referee_fee'    => absint( $_POST['referee_fee'] ?? 450 ),
            'live_occupancy' => min( 100, absint( $_POST['live_occupancy'] ?? 34 ) ),
            'parking_free'   => absint( $_POST['parking_free'] ?? 118 ),
            'parking_total'  => max( 1, absint( $_POST['parking_total'] ?? 180 ) ),
            'ai_provider'    => sanitize_key( $_POST['ai_provider'] ?? 'hybrid' ),
            'gemini_api_key' => sanitize_text_field( wp_unslash( $_POST['gemini_api_key'] ?? '' ) ),
            'gemini_model'   => sanitize_text_field( wp_unslash( $_POST['gemini_model'] ?? 'gemini-2.5-flash' ) ),
            'openai_api_key' => sanitize_text_field( wp_unslash( $_POST['openai_api_key'] ?? '' ) ),
            'openai_model'   => sanitize_text_field( wp_unslash( $_POST['openai_model'] ?? 'gpt-4o-mini' ) ),
            'github_url'     => esc_url_raw( wp_unslash( $_POST['github_url'] ?? '' ) ),
            'envato_url'     => esc_url_raw( wp_unslash( $_POST['envato_url'] ?? '' ) ),
        ] );
        update_option( 'apex_arena_settings', $new );
        echo '<div class="notice notice-success"><p><strong>APEX ARENA ayarları başarıyla kaydedildi!</strong></p></div>';
    }
    $s = apex_get_settings();
    ?>
    <div class="wrap">
        <h1>APEX ARENA — Hibrit AI Spor Koçu & Tesis Ayarları</h1>
        <form method="post">
            <?php wp_nonce_field( 'apex_settings_nonce' ); ?>
            <h2>1. Hibrit AI Spor Koçu (Chatbot) Yapılandırması</h2>
            <p class="description">API anahtarı girildiğinde canlı Google Gemini / OpenAI LLM + gerçek WordPress veritabanı RAG çalışır. Anahtar boş bırakılırsa sistem sıfır hata ile Yerel Akıllı Türkçe NLP + Canlı WP DB motoruna otomatik geçer.</p>
            <table class="form-table">
                <tr><th>AI Çalışma Modu</th><td>
                    <select name="ai_provider">
                        <option value="hybrid" <?php selected( $s['ai_provider'], 'hybrid' ); ?>>Otomatik Hibrit (API varsa LLM, yoksa Yerel WP DB)</option>
                        <option value="gemini" <?php selected( $s['ai_provider'], 'gemini' ); ?>>Google Gemini API</option>
                        <option value="openai" <?php selected( $s['ai_provider'], 'openai' ); ?>>OpenAI ChatGPT API</option>
                        <option value="local" <?php selected( $s['ai_provider'], 'local' ); ?>>Sadece Yerel Akıllı WP DB Motoru (Offline)</option>
                    </select>
                </td></tr>
                <tr><th>Google Gemini API Anahtarı</th><td><input type="password" name="gemini_api_key" class="regular-text" value="<?php echo esc_attr( $s['gemini_api_key'] ); ?>" autocomplete="off"> <input type="text" name="gemini_model" value="<?php echo esc_attr( $s['gemini_model'] ); ?>" placeholder="gemini-2.5-flash"></td></tr>
                <tr><th>OpenAI API Anahtarı</th><td><input type="password" name="openai_api_key" class="regular-text" value="<?php echo esc_attr( $s['openai_api_key'] ); ?>" autocomplete="off"> <input type="text" name="openai_model" value="<?php echo esc_attr( $s['openai_model'] ); ?>" placeholder="gpt-4o-mini"></td></tr>
            </table>

            <h2>2. Tesis & Salon Ayarları</h2>
            <table class="form-table">
                <tr><th>Hakem Ayarlama Ücreti (₺)</th><td><input type="number" name="referee_fee" value="<?php echo esc_attr( $s['referee_fee'] ); ?>"></td></tr>
                <tr><th>Canlı Tesis Yoğunluğu (%)</th><td><input type="number" min="0" max="100" name="live_occupancy" value="<?php echo esc_attr( $s['live_occupancy'] ); ?>"></td></tr>
                <tr><th>Otopark Boş / Toplam</th><td><input type="number" name="parking_free" class="small-text" value="<?php echo esc_attr( $s['parking_free'] ); ?>"> / <input type="number" name="parking_total" class="small-text" value="<?php echo esc_attr( $s['parking_total'] ); ?>"></td></tr>
                <tr><th>Telefon</th><td><input type="text" name="phone" class="regular-text" value="<?php echo esc_attr( $s['phone'] ); ?>"></td></tr>
                <tr><th>E-posta</th><td><input type="email" name="email" class="regular-text" value="<?php echo esc_attr( $s['email'] ); ?>"></td></tr>
                <tr><th>Adres</th><td><input type="text" name="address" class="large-text" value="<?php echo esc_attr( $s['address'] ); ?>"></td></tr>
                <tr><th>Hafta İçi / Hafta Sonu Saatleri</th><td><input type="text" name="weekday_hours" value="<?php echo esc_attr( $s['weekday_hours'] ); ?>"> / <input type="text" name="weekend_hours" value="<?php echo esc_attr( $s['weekend_hours'] ); ?>"></td></tr>
                <tr><th>GitHub Deposu Linki</th><td><input type="url" name="github_url" class="large-text" value="<?php echo esc_attr( $s['github_url'] ); ?>"></td></tr>
                <tr><th>Envato / ThemeForest Linki</th><td><input type="url" name="envato_url" class="large-text" value="<?php echo esc_attr( $s['envato_url'] ); ?>"></td></tr>
            </table>
            <p class="submit"><button type="submit" name="apex_save_settings" class="button button-primary button-large">Ayarları Kaydet</button></p>
        </form>
    </div>
    <?php
}

/**
 * Kafe Ön Siparişleri & Üye Yorumları Yönetim Sayfası (v2.0.0)
 */
function apex_admin_cafe_reviews_page() {
    global $wpdb;
    $t_cafe = $wpdb->prefix . 'apex_cafe_orders';
    $t_rev  = $wpdb->prefix . 'apex_reviews';

    if ( isset( $_GET['cafe_status'], $_GET['id'], $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'apex_cr_act' ) ) {
        $st = sanitize_key( $_GET['cafe_status'] );
        if ( in_array( $st, [ 'preparing', 'ready', 'completed', 'cancelled' ], true ) ) {
            $wpdb->update( $t_cafe, [ 'status' => $st ], [ 'id' => absint( $_GET['id'] ) ] );
            echo '<div class="notice notice-success"><p>Kafe sipariş durumu güncellendi.</p></div>';
        }
    }
    if ( isset( $_GET['rev_status'], $_GET['id'], $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'apex_cr_act' ) ) {
        $st = sanitize_key( $_GET['rev_status'] );
        if ( in_array( $st, [ 'published', 'hidden' ], true ) ) {
            $wpdb->update( $t_rev, [ 'status' => $st ], [ 'id' => absint( $_GET['id'] ) ] );
            echo '<div class="notice notice-success"><p>Yorum yayın durumu güncellendi.</p></div>';
        }
    }

    $orders  = $wpdb->get_results( "SELECT * FROM {$t_cafe} ORDER BY id DESC LIMIT 50" );
    $reviews = $wpdb->get_results( "SELECT * FROM {$t_rev} ORDER BY id DESC LIMIT 50" );
    $nonce   = wp_create_nonce( 'apex_cr_act' );
    ?>
    <div class="wrap">
        <h1>Apex Protein Kafe Ön Siparişleri</h1>
        <table class="wp-list-table widefat fixed striped">
            <thead><tr><th>Kod</th><th>Müşteri</th><th>Teslim Zamanı</th><th>Ürünler &amp; Makro</th><th>Tutar</th><th>Durum</th><th>İşlem</th></tr></thead>
            <tbody>
            <?php if ( empty( $orders ) ) : ?>
                <tr><td colspan="7">Henüz kafe ön siparişi yok.</td></tr>
            <?php else : foreach ( $orders as $o ) :
                $its = json_decode( (string) $o->items_json, true ) ?: [];
                $lbl = implode( ', ', array_map( fn( $x ) => ( $x['qty'] ?? 1 ) . 'x ' . ( $x['name'] ?? '' ), $its ) );
            ?>
                <tr>
                    <td><strong><?php echo esc_html( $o->order_code ); ?></strong></td>
                    <td><?php echo esc_html( $o->customer_name ); ?><br><small><?php echo esc_html( $o->customer_phone ); ?></small></td>
                    <td><?php echo esc_html( $o->pickup_time ); ?></td>
                    <td><?php echo esc_html( $lbl ); ?><br><small><?php echo esc_html( $o->total_kcal ); ?> kcal · <?php echo esc_html( $o->total_protein ); ?>g protein</small></td>
                    <td><strong><?php echo esc_html( apex_format_tl( $o->total_price ) ); ?></strong></td>
                    <td><strong><?php echo esc_html( strtoupper( $o->status ) ); ?></strong></td>
                    <td>
                        <a class="button button-small" href="<?php echo esc_url( admin_url( "admin.php?page=apex-cafe-reviews&cafe_status=ready&id={$o->id}&_wpnonce={$nonce}" ) ); ?>">Hazır</a>
                        <a class="button button-small" href="<?php echo esc_url( admin_url( "admin.php?page=apex-cafe-reviews&cafe_status=completed&id={$o->id}&_wpnonce={$nonce}" ) ); ?>">Teslim</a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>

        <h2 style="margin-top:32px;">Üye Yorumları &amp; Puanlamalar</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead><tr><th>Üye</th><th>Paket</th><th>Puan</th><th>Yorum</th><th>Durum</th><th>İşlem</th></tr></thead>
            <tbody>
            <?php foreach ( $reviews as $rv ) : ?>
                <tr>
                    <td><strong><?php echo esc_html( $rv->author_name ); ?></strong></td>
                    <td><?php echo esc_html( $rv->member_plan ); ?></td>
                    <td>★ <?php echo esc_html( $rv->rating ); ?> / 5</td>
                    <td><?php echo esc_html( $rv->comment ); ?></td>
                    <td><?php echo esc_html( strtoupper( $rv->status ) ); ?></td>
                    <td>
                        <?php if ( $rv->status === 'published' ) : ?>
                            <a class="button button-small" href="<?php echo esc_url( admin_url( "admin.php?page=apex-cafe-reviews&rev_status=hidden&id={$rv->id}&_wpnonce={$nonce}" ) ); ?>">Gizle</a>
                        <?php else : ?>
                            <a class="button button-small" href="<?php echo esc_url( admin_url( "admin.php?page=apex-cafe-reviews&rev_status=published&id={$rv->id}&_wpnonce={$nonce}" ) ); ?>">Yayınla</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

/**
 * Gmail SMTP, Hoş Geldin & Otomatik Hatırlatma E-posta Merkezi (v2.1.0)
 */
function apex_admin_email_center_page() {
    global $wpdb;
    $t_emails = $wpdb->prefix . 'apex_email_logs';
    $notice   = '';

    // 1. Gmail SMTP & Hatırlatma Ayarlarını Kaydet
    if ( isset( $_POST['apex_save_mail_settings'] ) && check_admin_referer( 'apex_mail_center_nonce' ) ) {
        $cur       = apex_get_settings();
        $raw_pass  = isset( $_POST['smtp_pass'] ) ? preg_replace( '/\s+/', '', (string) wp_unslash( $_POST['smtp_pass'] ) ) : '';
        $new_pass  = ! empty( $_POST['clear_smtp_pass'] ) ? '' : ( $raw_pass !== '' ? $raw_pass : ( $cur['smtp_pass'] ?? '' ) );
        $new_user  = sanitize_email( wp_unslash( $_POST['smtp_user'] ?? '' ) );
        // Yeni Gmail + Uygulama Şifresi ilk kez girildiyse canlı SMTP'yi otomatik etkinleştir
        $auto_enable = ( $raw_pass !== '' && is_email( $new_user ) );
        $smtp_on     = ( ! empty( $_POST['smtp_enabled'] ) || $auto_enable ) ? 1 : 0;

        $updated = array_merge( $cur, [
            'smtp_enabled'          => $smtp_on,
            'smtp_host'             => sanitize_text_field( wp_unslash( $_POST['smtp_host'] ?? 'smtp.gmail.com' ) ),
            'smtp_port'             => max( 1, absint( $_POST['smtp_port'] ?? 587 ) ),
            'smtp_encryption'       => in_array( $_POST['smtp_encryption'] ?? 'tls', [ 'tls', 'ssl', '' ], true ) ? $_POST['smtp_encryption'] : 'tls',
            'smtp_user'             => $new_user,
            'smtp_pass'             => $new_pass,
            'smtp_from_name'        => sanitize_text_field( wp_unslash( $_POST['smtp_from_name'] ?? 'APEX ARENA Spor Kompleksi' ) ),
            'mail_on_register'      => ! empty( $_POST['mail_on_register'] ) ? 1 : 0,
            'mail_on_booking'       => ! empty( $_POST['mail_on_booking'] ) ? 1 : 0,
            'mail_auto_reminders'   => ! empty( $_POST['mail_auto_reminders'] ) ? 1 : 0,
            'reminder_hours_before' => max( 1, min( 168, absint( $_POST['reminder_hours_before'] ?? 24 ) ) ),
        ] );
        update_option( 'apex_arena_settings', $updated );
        $notice = '<div class="notice notice-success"><p><strong>Gmail SMTP &amp; Otomatik Hatırlatma ayarları kaydedildi!</strong>'
            . ( $smtp_on && $new_user && $new_pass ? ' Canlı Gmail SMTP aktif.' : '' )
            . '</p></div>';
    }

    // 2. Test Maili Gönder
    if ( isset( $_POST['apex_send_test_mail'] ) && check_admin_referer( 'apex_mail_center_nonce' ) ) {
        $test_to = sanitize_email( wp_unslash( $_POST['test_email_to'] ?? '' ) );
        if ( is_email( $test_to ) && function_exists( 'apex_send_test_email' ) ) {
            $res = apex_send_test_email( $test_to );
            if ( $res['ok'] ) {
                $notice = '<div class="notice notice-success"><p>✅ <strong>' . esc_html( $res['message'] ) . '</strong></p></div>';
            } else {
                $notice = '<div class="notice notice-error"><p>⚠️ <strong>Gönderim Hatası:</strong> ' . esc_html( $res['error'] ?: $res['message'] ) . '</p></div>';
            }
        } else {
            $notice = '<div class="notice notice-error"><p>Lütfen geçerli bir alıcı Gmail/e-posta adresi girin.</p></div>';
        }
    }

    // 3. Otomatik Hatırlatma Tarayıcısını Manuel Çalıştır
    if ( isset( $_POST['apex_run_reminders_now'] ) && check_admin_referer( 'apex_mail_center_nonce' ) && function_exists( 'apex_run_scheduled_reminders' ) ) {
        $sum = apex_run_scheduled_reminders( true );
        $notice = '<div class="notice notice-info"><p>⏰ <strong>Hatırlatma taraması tamamlandı:</strong> '
            . (int) $sum['sent'] . ' yeni hatırlatma gönderildi, '
            . (int) $sum['skipped'] . ' kayıt atlandı, '
            . (int) $sum['failed'] . ' hata.</p></div>';
    }

    // 4. Seçili Üyeye Anında Antrenman veya Hoş Geldin Hatırlatması Gönder
    if ( isset( $_POST['apex_send_member_rem'] ) && check_admin_referer( 'apex_mail_center_nonce' ) ) {
        $target_uid  = absint( $_POST['target_member_id'] ?? 0 );
        $target_mode = in_array( $_POST['target_mail_mode'] ?? 'workout', [ 'workout', 'renewal', 'welcome' ], true ) ? $_POST['target_mail_mode'] : 'workout';
        if ( $target_uid > 0 ) {
            if ( $target_mode === 'welcome' && function_exists( 'apex_send_welcome_email' ) ) {
                $tu  = get_user_by( 'id', $target_uid );
                $res = $tu ? apex_send_welcome_email( [
                    'user_id'     => $tu->ID,
                    'full_name'   => $tu->display_name ?: $tu->user_login,
                    'email'       => $tu->user_email,
                    'member_code' => get_user_meta( $tu->ID, 'apex_member_code', true ) ?: 'APX-2026-' . str_pad( (string) $tu->ID, 4, '0', STR_PAD_LEFT ),
                    'plan_name'   => get_user_meta( $tu->ID, 'apex_membership_plan', true ) ?: 'APEX PRO',
                    'end_date'    => get_user_meta( $tu->ID, 'apex_membership_end', true ) ?: date( 'Y-m-d', strtotime( '+30 days' ) ),
                    'goal'        => get_user_meta( $tu->ID, 'apex_member_goal', true ) ?: 'Atletik Performans',
                ], true ) : [ 'ok' => false, 'message' => 'Üye bulunamadı.', 'error' => '' ];
            } elseif ( function_exists( 'apex_send_member_reminder_email' ) ) {
                $res = apex_send_member_reminder_email( $target_uid, $target_mode, true );
            } else {
                $res = [ 'ok' => false, 'message' => 'Mail modülü yüklenemedi.', 'error' => '' ];
            }
            $cls    = ! empty( $res['ok'] ) ? 'notice-success' : 'notice-error';
            $notice = '<div class="notice ' . $cls . '"><p>' . esc_html( ! empty( $res['ok'] ) ? $res['message'] : ( $res['error'] ?: $res['message'] ) ) . '</p></div>';
        }
    }

    $s            = apex_get_settings();
    $gmail_ready  = function_exists( 'apex_is_gmail_smtp_ready' ) && apex_is_gmail_smtp_ready();
    $total_mails  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$t_emails}" );
    $sent_gmail   = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$t_emails} WHERE status = 'sent'" );
    $rem_count    = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$t_emails} WHERE mail_type LIKE 'reminder_%'" );
    $welcome_cnt  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$t_emails} WHERE mail_type = 'welcome'" );
    $logs         = $wpdb->get_results( "SELECT * FROM {$t_emails} ORDER BY id DESC LIMIT 40" );
    $members      = get_users( [ 'number' => 40, 'orderby' => 'ID', 'order' => 'DESC' ] );
    $last_run     = get_option( 'apex_last_reminder_run', 'Henüz çalışmadı' );
    $csv_nonce    = wp_create_nonce( 'apex_csv_export' );
    $has_pass     = ! empty( $s['smtp_pass'] );
    ?>
    <style>
        .apex-mc-wrap { max-width: 1320px; margin: 16px 20px 44px 0; font-family: -apple-system, BlinkMacSystemFont, 'Plus Jakarta Sans', 'Segoe UI', sans-serif; color: #f5f5f7; }
        .apex-mc-wrap * { box-sizing: border-box; }
        .apex-mc-banner {
            background: radial-gradient(ellipse 46% 115% at 92% 0%, rgba(250,17,79,0.22), transparent 62%), radial-gradient(ellipse 40% 100% at 8% 100%, rgba(164,255,46,0.12), transparent 60%), #111217;
            border: 1px solid rgba(255,255,255,0.09); border-radius: 20px; padding: 22px 26px; display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; margin-bottom: 18px;
        }
        .apex-mc-kpis { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin-bottom: 18px; }
        .apex-mc-kpi { background: linear-gradient(180deg,#16171d,#101116); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 16px 18px; border-top: 2.5px solid var(--accent,#fa114f); }
        .apex-mc-kpi-lbl { font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #8c90a0; }
        .apex-mc-kpi-val { font-size: 25px; font-weight: 800; color: #fff; margin-top: 6px; }
        .apex-mc-grid { display: grid; grid-template-columns: minmax(0, 1.25fr) minmax(0, 0.95fr); gap: 18px; margin-bottom: 18px; }
        .apex-mc-card { background: linear-gradient(180deg,#16171d,#111216); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; padding: 20px 22px; }
        .apex-mc-card h2 { margin: 0 0 12px; font-size: 16px; font-weight: 800; color: #fff; }
        .apex-mc-field-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .apex-mc-field { display: flex; flex-direction: column; gap: 5px; }
        .apex-mc-field label { font-size: 12px; font-weight: 700; color: #d4d4d8; }
        .apex-mc-field input, .apex-mc-field select, .apex-mc-wrap select {
            background: #0c0d12 !important;
            border: 1px solid rgba(255,255,255,0.18) !important;
            border-radius: 10px;
            padding: 9px 12px;
            color: #ffffff !important;
            font-size: 13px;
            color-scheme: dark;
            max-width: 100%;
        }
        .apex-mc-field select option, .apex-mc-wrap select option {
            background-color: #151720 !important;
            color: #ffffff !important;
        }
        .apex-mc-field input::placeholder, .apex-mc-wrap input::placeholder { color: #8c90a0 !important; }
        .apex-mc-field select:focus, .apex-mc-field input:focus { border-color: #fa114f !important; box-shadow: 0 0 0 2px rgba(250,17,79,0.28) !important; outline: none; }
        .apex-mc-checks { display: grid; gap: 8px; margin: 14px 0; padding: 12px 14px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); border-radius: 12px; }
        .apex-mc-checks label { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #e4e4e7; cursor: pointer; }
        .apex-mc-btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 9px 16px; border-radius: 999px; font-size: 12.5px; font-weight: 700; border: 1px solid rgba(255,255,255,0.15); background: rgba(255,255,255,0.06); color: #fff; cursor: pointer; text-decoration: none; }
        .apex-mc-btn:hover { background: rgba(255,255,255,0.14); color: #fff; }
        .apex-mc-btn-red { background: linear-gradient(135deg,#fa114f,#d40f40); border-color: rgba(255,110,145,0.45); }
        .apex-mc-btn-lime { background: linear-gradient(135deg,#68c714,#96eb26); color: #0a0b0e; border-color: rgba(164,255,46,0.5); }
        .apex-mc-btn-lime:hover { color: #0a0b0e; }
        .apex-mc-guide { background: rgba(38,230,255,0.06); border: 1px solid rgba(38,230,255,0.22); border-radius: 12px; padding: 12px 14px; font-size: 12.5px; color: #cbd5e1; line-height: 1.55; margin-top: 14px; }
        .apex-mc-guide ol { margin: 6px 0 0 18px; padding: 0; }
        .apex-mc-table { width: 100%; border-collapse: collapse; }
        .apex-mc-table th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; color: #8c90a0; padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .apex-mc-table td { padding: 11px 10px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 13px; color: #e4e4e7; vertical-align: middle; }
        .apex-mc-pill { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; white-space: nowrap; }
        .apex-mc-modal-bg { position: fixed; inset: 0; z-index: 100000; background: rgba(5,6,10,0.82); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; padding: 20px; }
        .apex-mc-modal-bg[hidden] { display: none; }
        .apex-mc-modal { width: min(760px, 100%); max-height: 90vh; background: #111319; border: 1px solid rgba(255,255,255,0.16); border-radius: 18px; overflow: hidden; display: flex; flex-direction: column; }
        .apex-mc-modal-head { padding: 14px 18px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .apex-mc-modal iframe { width: 100%; height: 72vh; border: 0; background: #090a0d; }
        @media (max-width: 1080px) { .apex-mc-kpis { grid-template-columns: repeat(2, 1fr); } .apex-mc-grid, .apex-mc-field-grid { grid-template-columns: 1fr; } }
    </style>

    <div class="wrap apex-mc-wrap">
        <?php echo $notice; ?>

        <div class="apex-mc-banner">
            <div>
                <div style="font-size:11px;font-weight:800;letter-spacing:0.09em;text-transform:uppercase;color:#a4ff2e;margin-bottom:5px;">
                    ● GMAIL SMTP &amp; OTOMATİK HATIRLATMA MOTORU · v<?php echo esc_html( APEX_VERSION ); ?>
                </div>
                <h1 style="margin:0;font-size:22px;font-weight:800;color:#fff;">E-posta Bildirimleri &amp; Hatırlatma Merkezi</h1>
                <p style="margin:6px 0 0;font-size:13px;color:#a1a1aa;">
                    Kayıt sonrası Hoş Geldin + Dijital QR Kartı, salon/PT onayları ve 24 saat kala otomatik hatırlatmaları Gmail SMTP ile yönetin.
                </p>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <a href="<?php echo esc_url( admin_url( "admin.php?page=apex-email-center&apex_export_csv=emails&_wpnonce={$csv_nonce}" ) ); ?>" class="apex-mc-btn">⬇ CSV İndir</a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=apex-arena-dashboard' ) ); ?>" class="apex-mc-btn">← Operasyon Paneli</a>
            </div>
        </div>

        <div class="apex-mc-kpis">
            <div class="apex-mc-kpi" style="--accent:#26e6ff;">
                <div class="apex-mc-kpi-lbl">Toplam E-posta Kaydı</div>
                <div class="apex-mc-kpi-val"><?php echo esc_html( $total_mails ); ?></div>
                <div style="font-size:12px;color:#9ca3af;margin-top:4px;">Hoş Geldin: <?php echo esc_html( $welcome_cnt ); ?></div>
            </div>
            <div class="apex-mc-kpi" style="--accent:#a4ff2e;">
                <div class="apex-mc-kpi-lbl">Canlı Gmail İletimi</div>
                <div class="apex-mc-kpi-val"><?php echo esc_html( $sent_gmail ); ?></div>
                <div style="font-size:12px;color:#a4ff2e;margin-top:4px;">SMTP üzerinden ulaştı</div>
            </div>
            <div class="apex-mc-kpi" style="--accent:#ffbe0b;">
                <div class="apex-mc-kpi-lbl">Hatırlatma E-postaları</div>
                <div class="apex-mc-kpi-val"><?php echo esc_html( $rem_count ); ?></div>
                <div style="font-size:12px;color:#9ca3af;margin-top:4px;">Son Tarama: <?php echo esc_html( $last_run ); ?></div>
            </div>
            <div class="apex-mc-kpi" style="--accent:<?php echo $gmail_ready ? '#a4ff2e' : '#fa114f'; ?>;">
                <div class="apex-mc-kpi-lbl">Gmail SMTP Durumu</div>
                <div class="apex-mc-kpi-val" style="font-size:18px;color:<?php echo $gmail_ready ? '#a4ff2e' : '#ffbe0b'; ?>;">
                    <?php echo $gmail_ready ? '● CANLI GMAIL AKTİF' : '● YEREL ÖNİZLEME'; ?>
                </div>
                <div style="font-size:12px;color:#9ca3af;margin-top:4px;"><?php echo $gmail_ready ? esc_html( $s['smtp_user'] ) : 'Gmail &amp; App Password bekleniyor'; ?></div>
            </div>
        </div>

        <div class="apex-mc-grid">
            <!-- SOL: GMAIL SMTP & OTOMASYON AYARLARI -->
            <div class="apex-mc-card">
                <h2>1. Gmail SMTP &amp; Otomatik Gönderim Ayarları</h2>
                <form method="post">
                    <?php wp_nonce_field( 'apex_mail_center_nonce' ); ?>
                    <div class="apex-mc-field-grid">
                        <div class="apex-mc-field">
                            <label for="smtp_user">Gmail Adresiniz *</label>
                            <input type="email" id="smtp_user" name="smtp_user" value="<?php echo esc_attr( $s['smtp_user'] ); ?>" placeholder="ornek@gmail.com">
                        </div>
                        <div class="apex-mc-field">
                            <label for="smtp_pass">Google Uygulama Şifresi (16 Hane) <?php echo $has_pass ? '— Kayıtlı ✓' : '*'; ?></label>
                            <input type="password" id="smtp_pass" name="smtp_pass" value="" placeholder="<?php echo $has_pass ? 'Değiştirmek için yeni şifre yazın' : 'xxxx xxxx xxxx xxxx'; ?>" autocomplete="new-password">
                        </div>
                        <div class="apex-mc-field">
                            <label for="smtp_from_name">Gönderen Adı</label>
                            <input type="text" id="smtp_from_name" name="smtp_from_name" value="<?php echo esc_attr( $s['smtp_from_name'] ); ?>">
                        </div>
                        <div class="apex-mc-field">
                            <label for="reminder_hours_before">Hatırlatma Süresi (Randevudan Kaç Saat Önce)</label>
                            <input type="number" id="reminder_hours_before" name="reminder_hours_before" min="1" max="168" value="<?php echo esc_attr( $s['reminder_hours_before'] ); ?>">
                        </div>
                        <div class="apex-mc-field">
                            <label for="smtp_host">SMTP Sunucusu</label>
                            <input type="text" id="smtp_host" name="smtp_host" value="<?php echo esc_attr( $s['smtp_host'] ?: 'smtp.gmail.com' ); ?>">
                        </div>
                        <div class="apex-mc-field">
                            <label for="smtp_port">Port &amp; Şifreleme</label>
                            <div style="display:flex;gap:8px;">
                                <input type="number" id="smtp_port" name="smtp_port" value="<?php echo esc_attr( $s['smtp_port'] ?: 587 ); ?>" style="width:90px;">
                                <select name="smtp_encryption" style="flex:1;">
                                    <option value="tls" <?php selected( $s['smtp_encryption'], 'tls' ); ?>>TLS (587 — Önerilen)</option>
                                    <option value="ssl" <?php selected( $s['smtp_encryption'], 'ssl' ); ?>>SSL (465)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="apex-mc-checks">
                        <label><input type="checkbox" name="smtp_enabled" value="1" <?php checked( ! empty( $s['smtp_enabled'] ) ); ?>> <strong>Canlı Gmail SMTP Gönderimini Etkinleştir</strong> (Kapalıysa mailler sıfır hata ile aşağıda önizlenir)</label>
                        <label><input type="checkbox" name="mail_on_register" value="1" <?php checked( ! empty( $s['mail_on_register'] ) ); ?>> Yeni üye kayıt olunca <strong>Hoş Geldin &amp; Dijital QR Kart</strong> e-postası gönder</label>
                        <label><input type="checkbox" name="mail_on_booking" value="1" <?php checked( ! empty( $s['mail_on_booking'] ) ); ?>> Salon kiralama veya PT randevusu alındığında <strong>Onay E-postası</strong> gönder</label>
                        <label><input type="checkbox" name="mail_auto_reminders" value="1" <?php checked( ! empty( $s['mail_auto_reminders'] ) ); ?>> Saatlik WP-Cron ile <strong>Yaklaşan Randevu &amp; Üyelik Hatırlatmaları</strong> gönder</label>
                        <?php if ( $has_pass ) : ?>
                        <label style="color:#fca5a5;"><input type="checkbox" name="clear_smtp_pass" value="1"> Kayıtlı uygulama şifresini temizle</label>
                        <?php endif; ?>
                    </div>

                    <button type="submit" name="apex_save_mail_settings" class="apex-mc-btn apex-mc-btn-red">💾 Gmail &amp; Hatırlatma Ayarlarını Kaydet</button>
                </form>

                <div class="apex-mc-guide">
                    <strong style="color:#26e6ff;">📌 Gmail Uygulama Şifresi (App Password) Nasıl Alınır?</strong>
                    <ol>
                        <li>Google Hesabınızda (<strong>myaccount.google.com/security</strong>) <strong>2 Adımlı Doğrulama</strong>’yı açın.</li>
                        <li><strong>Uygulama Şifreleri (App Passwords)</strong> sayfasına gidip "APEX ARENA" adıyla yeni 16 haneli şifre oluşturun.</li>
                        <li>Gmail adresinizi ve 16 haneli şifreyi yukarıya girip <strong>Canlı Gmail SMTP</strong> kutusunu işaretleyin ve sağdan Test Maili gönderin!</li>
                    </ol>
                </div>
            </div>

            <!-- SAĞ: TEST MAİLİ & ANLIK HATIRLATMA TETİKLEYİCİ -->
            <div style="display:grid;gap:18px;align-content:start;">
                <div class="apex-mc-card">
                    <h2>2. Canlı Gmail Test Maili Gönder</h2>
                    <p style="font-size:12.5px;color:#a1a1aa;margin:0 0 12px;">Ayarlarınızı kaydettikten sonra hedef Gmail adresinize anında test e-postası gönderip teslimatı doğrulayın.</p>
                    <form method="post" style="display:flex;gap:8px;flex-wrap:wrap;">
                        <?php wp_nonce_field( 'apex_mail_center_nonce' ); ?>
                        <input type="email" name="test_email_to" value="<?php echo esc_attr( $s['smtp_user'] ?: wp_get_current_user()->user_email ); ?>" placeholder="hedef@gmail.com" style="flex:1;min-width:190px;background:#0c0d12;border:1px solid rgba(255,255,255,0.14);border-radius:10px;padding:9px 12px;color:#fff;" required>
                        <button type="submit" name="apex_send_test_mail" class="apex-mc-btn apex-mc-btn-lime">✉️ Test Maili Gönder</button>
                    </form>
                </div>

                <div class="apex-mc-card">
                    <h2>3. Anlık Hatırlatma Gönderici</h2>
                    <p style="font-size:12.5px;color:#a1a1aa;margin:0 0 12px;">Seçtiğiniz üyeye tek tıkla <strong>Antrenman Hatırlatması</strong>, <strong>Üyelik Yenileme</strong> veya <strong>Hoş Geldin &amp; QR</strong> e-postası gönderin.</p>
                    <form method="post" style="display:grid;gap:10px;">
                        <?php wp_nonce_field( 'apex_mail_center_nonce' ); ?>
                        <div class="apex-mc-field">
                            <label for="target_member_id">Üye Seçin</label>
                            <select id="target_member_id" name="target_member_id">
                                <?php foreach ( $members as $mu ) : ?>
                                    <option value="<?php echo esc_attr( $mu->ID ); ?>"><?php echo esc_html( $mu->display_name . ' (' . $mu->user_email . ')' ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="apex-mc-field">
                            <label for="target_mail_mode">E-posta Türü</label>
                            <select id="target_mail_mode" name="target_mail_mode">
                                <option value="workout">🔥 Antrenman &amp; Hedef Hatırlatması</option>
                                <option value="renewal">⏳ Üyelik Yenileme Hatırlatması</option>
                                <option value="welcome">🎉 Hoş Geldin &amp; Dijital QR Kartı</option>
                            </select>
                        </div>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:4px;">
                            <button type="submit" name="apex_send_member_rem" class="apex-mc-btn apex-mc-btn-red">📤 Seçili Üyeye Gönder</button>
                            <button type="submit" name="apex_run_reminders_now" class="apex-mc-btn">⏰ Yaklaşan Randevuları Şimdi Tara</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- GÖNDERİLEN E-POSTALAR & CANLI HTML ÖNİZLEME TABLOSU -->
        <div class="apex-mc-card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;flex-wrap:wrap;gap:10px;">
                <h2 style="margin:0;">4. E-posta Kayıt Defteri &amp; Canlı HTML Önizleme (Son 40 Gönderim)</h2>
                <span style="font-size:12px;color:#8c90a0;">Her gönderilen kayıt HTML şablonuyla birlikte güvenle saklanır.</span>
            </div>
            <div style="overflow-x:auto;">
                <table class="apex-mc-table">
                    <thead>
                        <tr><th>Tarih</th><th>Tür</th><th>Alıcı</th><th>Konu</th><th>Durum</th><th>Önizleme</th></tr>
                    </thead>
                    <tbody>
                    <?php if ( empty( $logs ) ) : ?>
                        <tr><td colspan="6" style="text-align:center;color:#8c90a0;padding:24px;">Henüz e-posta kaydı yok. Yeni üyelik kaydı veya test maili ile anında deneyebilirsiniz.</td></tr>
                    <?php else : foreach ( $logs as $lg ) :
                        $tm = function_exists( 'apex_mail_type_meta' ) ? apex_mail_type_meta( $lg->mail_type ) : [ 'label' => $lg->mail_type, 'color' => '#26e6ff', 'bg' => 'rgba(38,230,255,0.14)' ];
                        $st_map = [
                            'sent'         => [ 'txt' => '✓ Gmail İletildi', 'col' => '#a4ff2e', 'bg' => 'rgba(164,255,46,0.14)' ],
                            'queued_local' => [ 'txt' => '● Yerel Önizleme', 'col' => '#26e6ff', 'bg' => 'rgba(38,230,255,0.14)' ],
                            'failed'       => [ 'txt' => '✕ SMTP Hatası',    'col' => '#ff4b7c', 'bg' => 'rgba(250,17,79,0.18)' ],
                        ];
                        $st = $st_map[ $lg->status ] ?? [ 'txt' => $lg->status, 'col' => '#9ca3af', 'bg' => 'rgba(156,163,175,0.14)' ];
                    ?>
                        <tr>
                            <td style="white-space:nowrap;color:#a1a1aa;"><?php echo esc_html( date( 'd.m.Y H:i', strtotime( $lg->created_at ) ) ); ?></td>
                            <td><span class="apex-mc-pill" style="color:<?php echo esc_attr( $tm['color'] ); ?>;background:<?php echo esc_attr( $tm['bg'] ); ?>;"><?php echo esc_html( $tm['label'] ); ?></span></td>
                            <td><strong><?php echo esc_html( $lg->recipient_name ?: 'Üye' ); ?></strong><br><small style="color:#9ca3af;"><?php echo esc_html( $lg->recipient_email ); ?></small></td>
                            <td>
                                <?php echo esc_html( $lg->subject ); ?>
                                <?php if ( ! empty( $lg->error_message ) ) : ?>
                                    <div style="font-size:11px;color:#fca5a5;margin-top:3px;">Hata: <?php echo esc_html( $lg->error_message ); ?></div>
                                <?php endif; ?>
                            </td>
                            <td><span class="apex-mc-pill" style="color:<?php echo esc_attr( $st['col'] ); ?>;background:<?php echo esc_attr( $st['bg'] ); ?>;"><?php echo esc_html( $st['txt'] ); ?></span></td>
                            <td>
                                <button type="button" class="apex-mc-btn apex-mail-preview-btn"
                                    data-subject="<?php echo esc_attr( $lg->subject ); ?>"
                                    data-to="<?php echo esc_attr( $lg->recipient_email ); ?>"
                                    data-b64="<?php echo esc_attr( base64_encode( (string) $lg->body_html ) ); ?>">👁 Önizle</button>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- CANLI E-POSTA HTML ÖNİZLEME MODALI -->
    <div class="apex-mc-modal-bg" id="apex-mail-preview-modal" hidden>
        <div class="apex-mc-modal">
            <div class="apex-mc-modal-head">
                <div>
                    <strong id="apex-mp-subject" style="color:#fff;font-size:14px;display:block;">E-posta Önizleme</strong>
                    <small id="apex-mp-to" style="color:#9ca3af;"></small>
                </div>
                <button type="button" class="apex-mc-btn" id="apex-mp-close">✕ Kapat</button>
            </div>
            <iframe id="apex-mp-frame" sandbox="allow-same-origin" title="E-posta HTML Önizleme"></iframe>
        </div>
    </div>
    <script>
    (function(){
        const modal = document.getElementById('apex-mail-preview-modal');
        const frame = document.getElementById('apex-mp-frame');
        const subj  = document.getElementById('apex-mp-subject');
        const toEl  = document.getElementById('apex-mp-to');
        function decodeB64Utf8(b64){
            const bin = atob(b64);
            const bytes = Uint8Array.from(bin, c => c.charCodeAt(0));
            return new TextDecoder('utf-8').decode(bytes);
        }
        document.querySelectorAll('.apex-mail-preview-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                subj.textContent = btn.dataset.subject || 'E-posta Önizleme';
                toEl.textContent = 'Alıcı: ' + (btn.dataset.to || '');
                frame.srcdoc = decodeB64Utf8(btn.dataset.b64 || '');
                modal.hidden = false;
            });
        });
        document.getElementById('apex-mp-close')?.addEventListener('click', () => { modal.hidden = true; });
        modal?.addEventListener('click', (e) => { if (e.target === modal) modal.hidden = true; });
    })();
    </script>
    <?php
}
