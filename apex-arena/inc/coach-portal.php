<?php
/**
 * APEX ARENA — Dinamik Neon Glow Koç Yönetim Paneli (v2.0.2)
 * Sadece koçların kendi öğrencilerini (ve yöneticinin tüm koçları) görebildiği
 * Apple Fitness esintili canlı aktivite halkaları, etkileşimli neon grafikler,
 * 5 öğünlük Beslenme Takvimi, Kişisel Öneri ve Birebir Mesajlaşma Stüdyosu.
 *
 * @package ApexArena
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Koç rolündeki kullanıcı giriş yapınca doğrudan Koç Paneline yönlendirilsin
 */
add_filter( 'login_redirect', function( $redirect_to, $requested, $user ) {
    if ( $user instanceof WP_User && ! is_wp_error( $user ) ) {
        if ( in_array( 'apex_coach', (array) $user->roles, true ) && ! user_can( $user, 'manage_options' ) ) {
            return admin_url( 'admin.php?page=apex-coach-portal' );
        }
    }
    return $redirect_to;
}, 20, 3 );

/**
 * Koç kullanıcısı için gereksiz WP Admin menülerini temizle; sadece Koç Paneli kalsın
 */
add_action( 'admin_menu', function() {
    add_menu_page(
        'APEX Koç Grafik Paneli',
        'Koç Paneli',
        'apex_coach_access',
        'apex-coach-portal',
        'apex_render_coach_portal_page',
        'dashicons-chart-area',
        2
    );

    if ( current_user_can( 'apex_coach_access' ) && ! current_user_can( 'manage_options' ) ) {
        remove_menu_page( 'index.php' );
        remove_menu_page( 'edit.php' );
        remove_menu_page( 'edit-comments.php' );
        remove_menu_page( 'tools.php' );
        remove_menu_page( 'upload.php' );
    }
}, 99 );

/**
 * Durum etiketi meta bilgileri (Apple Fitness neon uyumlu palet)
 */
function apex_coach_status_meta( $status ) {
    $map = [
        'hedefte' => [ 'label' => 'Hedefte',       'color' => '#a4ff2e', 'bg' => 'rgba(164,255,46,0.14)' ],
        'dikkat'  => [ 'label' => 'Dikkat',        'color' => '#ffbe0b', 'bg' => 'rgba(255,190,11,0.15)' ],
        'yeni'    => [ 'label' => 'Yeni Başladı',  'color' => '#26e6ff', 'bg' => 'rgba(38,230,255,0.14)' ],
        'riskli'  => [ 'label' => 'Yoğun Takip',   'color' => '#fa114f', 'bg' => 'rgba(250,17,79,0.16)' ],
    ];
    return $map[ $status ] ?? $map['hedefte'];
}

/**
 * Grafiksel Koç Yönetim Paneli Render
 */
function apex_render_coach_portal_page() {
    if ( ! current_user_can( 'apex_coach_access' ) && ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Bu panele yalnızca yetkili APEX ARENA koçları erişebilir.' );
    }

    global $wpdb;
    $cu       = wp_get_current_user();
    $is_admin = current_user_can( 'manage_options' );
    $t_st     = $wpdb->prefix . 'apex_coach_students';
    $t_nut    = $wpdb->prefix . 'apex_nutrition_plans';
    $t_msg    = $wpdb->prefix . 'apex_coach_messages';

    // Koç rolündeki kullanıcı SADECE kendi öğrencilerini görür; Admin tüm koçlar arasında geçiş yapabilir
    $coach_filter = $is_admin ? absint( $_GET['coach_id'] ?? 0 ) : (int) $cu->ID;
    $coaches      = $is_admin ? get_users( [ 'role' => 'apex_coach', 'orderby' => 'display_name' ] ) : [];
    if ( $is_admin && count( $coaches ) < 7 && function_exists( 'apex_setup_coach_roles_and_seed' ) ) {
        apex_setup_coach_roles_and_seed();
        $coaches = get_users( [ 'role' => 'apex_coach', 'orderby' => 'display_name' ] );
    }
    $coach_student_counts = [];
    $total_all_students   = 0;
    if ( $is_admin ) {
        $cnt_rows = $wpdb->get_results( "SELECT coach_user_id, COUNT(*) AS cnt FROM {$t_st} GROUP BY coach_user_id" );
        foreach ( (array) $cnt_rows as $cr ) {
            $coach_student_counts[ (int) $cr->coach_user_id ] = (int) $cr->cnt;
            $total_all_students += (int) $cr->cnt;
        }
    }

    if ( $coach_filter > 0 ) {
        $students = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$t_st} WHERE coach_user_id = %d ORDER BY id ASC", $coach_filter ) );
    } else {
        $students = $wpdb->get_results( "SELECT * FROM {$t_st} ORDER BY id ASC" );
    }

    $payload_map  = [];
    $student_list = [];
    foreach ( $students as $st ) {
        $nut   = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t_nut} WHERE student_id = %d LIMIT 1", $st->id ) );
        $meals = $nut ? ( json_decode( (string) $nut->meals_json, true ) ?: [] ) : [];
        $msgs  = $wpdb->get_results( $wpdb->prepare( "SELECT sender_role, sender_name, message, DATE_FORMAT(created_at, '%%d.%%m %%H:%%i') AS time FROM {$t_msg} WHERE student_id = %d ORDER BY id ASC LIMIT 50", $st->id ) );

        $entry = [
            'id'               => (int) $st->id,
            'student_name'     => $st->student_name,
            'student_email'    => $st->student_email,
            'coach_name'       => $st->coach_name,
            'goal'             => $st->goal,
            'status'           => $st->status,
            'progress_pct'     => (int) $st->progress_pct,
            'progress_trend'   => $st->progress_trend,
            'adherence_pct'    => (int) $st->adherence_pct,
            'current_weight'   => (float) $st->current_weight,
            'target_weight'    => (float) $st->target_weight,
            'sessions_planned' => (int) $st->sessions_planned,
            'sessions_done'    => (int) $st->sessions_done,
            'coach_advice'     => (string) $st->coach_advice,
            'nutrition'        => [
                'daily_kcal'   => $nut ? (int) $nut->daily_kcal : 2250,
                'protein_g'    => $nut ? (int) $nut->protein_g : 165,
                'carbs_g'      => $nut ? (int) $nut->carbs_g : 220,
                'fat_g'        => $nut ? (int) $nut->fat_g : 65,
                'water_l'      => $nut ? (float) $nut->water_l : 3.0,
                'sabah'        => $meals['sabah'] ?? '',
                'ara1'         => $meals['ara1'] ?? '',
                'ogle'         => $meals['ogle'] ?? '',
                'ant_sonra'    => $meals['ant_sonra'] ?? '',
                'aksam'        => $meals['aksam'] ?? '',
                'weekly_notes' => $nut ? (string) $nut->weekly_notes : '',
            ],
            'messages'         => $msgs,
        ];
        $payload_map[ $st->id ] = $entry;
        $student_list[]         = (int) $st->id;
    }

    $avg_prog = count( $students ) ? round( array_sum( array_map( fn( $x ) => (int) $x->progress_pct, $students ) ) / count( $students ) ) : 0;
    $avg_adh  = count( $students ) ? round( array_sum( array_map( fn( $x ) => (int) $x->adherence_pct, $students ) ) / count( $students ) ) : 0;

    $rest_url = esc_url_raw( rest_url( 'apex/v1/' ) );
    $nonce    = wp_create_nonce( 'wp_rest' );
    $first_id = ! empty( $students ) ? (int) $students[0]->id : 0;
    ?>
    <style>
        .apex-cp-wrap {
            --cp-bg: #0b0c10;
            --cp-card: #131419;
            --cp-card-elev: #181a21;
            --cp-border: rgba(255, 255, 255, 0.08);
            --cp-pink: #fa114f;
            --cp-pink-hi: #ff4b7c;
            --cp-lime: #a4ff2e;
            --cp-lime-lo: #7bd615;
            --cp-cyan: #26e6ff;
            --cp-cyan-lo: #00b8d9;
            --cp-amber: #ffbe0b;
            max-width: 1360px;
            margin: 16px 20px 44px 0;
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
            color: #f5f5f7;
            font-variant-numeric: tabular-nums;
            position: relative;
        }
        .apex-cp-wrap * { box-sizing: border-box; }

        /* Üst Hero Bar */
        .apex-cp-banner {
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(ellipse 45% 110% at 92% 0%, rgba(250, 17, 79, 0.22), transparent 62%),
                radial-gradient(ellipse 40% 100% at 10% 100%, rgba(38, 230, 255, 0.09), transparent 60%),
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
        .apex-cp-eyebrow {
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
        .apex-cp-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--cp-lime);
            box-shadow: 0 0 10px rgba(164, 255, 46, 0.9);
            animation: apexCpPulseDot 2.4s ease-in-out infinite;
        }
        @keyframes apexCpPulseDot {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.25); opacity: 0.65; }
        }
        .apex-cp-h1 { margin: 0; font-size: 23px; font-weight: 800; color: #fff; letter-spacing: -0.025em; }
        .apex-cp-sub { margin: 6px 0 0; color: #a1a1aa; font-size: 13.5px; display: flex; flex-wrap: wrap; gap: 14px; align-items: center; }
        .apex-cp-sub strong { color: #fff; }
        .apex-cp-pills { display: flex; align-items: center; gap: 9px; flex-wrap: wrap; }
        .apex-cp-btn {
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
        .apex-cp-btn:hover { background: rgba(255, 255, 255, 0.12); border-color: rgba(255, 255, 255, 0.24); color: #fff; transform: translateY(-1px); }
        .apex-cp-btn-red {
            background: linear-gradient(135deg, #fa114f 0%, #d40f40 100%);
            border-color: rgba(255, 110, 145, 0.45);
            box-shadow: 0 8px 24px rgba(250, 17, 79, 0.35);
        }
        .apex-cp-btn-red:hover { background: linear-gradient(135deg, #ff2761 0%, #e51348 100%); box-shadow: 0 10px 28px rgba(250, 17, 79, 0.5); }
        .apex-cp-select {
            background: #171921;
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 999px;
            padding: 7px 14px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
        }

        /* 5 Kartlı Obsidian + Glow Analiz Grid'i */
        .apex-cp-top3 { display: grid; grid-template-columns: minmax(0, 1.05fr) minmax(0, 1.15fr) minmax(0, 0.95fr); gap: 18px; margin-bottom: 18px; }
        .apex-cp-bot2 { display: grid; grid-template-columns: minmax(0, 0.88fr) minmax(0, 1.42fr); gap: 18px; margin-bottom: 20px; }
        .apex-cp-card {
            position: relative;
            overflow: hidden;
            background: linear-gradient(180deg, #16171d 0%, #111216 100%);
            border: 1px solid var(--cp-border);
            border-radius: 22px;
            padding: 20px 22px;
            box-shadow: 0 18px 46px rgba(0, 0, 0, 0.32), inset 0 1px 0 rgba(255, 255, 255, 0.08);
            min-width: 0;
            transition: border-color 0.25s ease, box-shadow 0.25s ease;
        }
        .apex-cp-card:hover {
            border-color: rgba(255, 255, 255, 0.14);
        }
        .apex-cp-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            pointer-events: none;
            opacity: 0;
            background: radial-gradient(300px circle at var(--mx, 50%) var(--my, 30%), rgba(255, 255, 255, 0.055), transparent 70%);
            transition: opacity 0.25s ease;
        }
        .apex-cp-card:hover::before { opacity: 1; }

        .apex-cp-card-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }
        .apex-cp-card-title {
            font-size: 14.5px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.015em;
            margin: 0;
        }
        .apex-cp-card-sub {
            font-size: 11.5px;
            font-weight: 600;
            color: #8c90a0;
        }

        /* Segmented Pill Toggle (Aktivite Halkaları Öğrenci / Genel + Tablo Filtresi) */
        .apex-cp-seg {
            display: inline-flex;
            gap: 3px;
            padding: 3px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .apex-cp-seg-btn {
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
        .apex-cp-seg-btn.is-active {
            background: rgba(255, 255, 255, 0.14);
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.28);
        }

        /* Apple Fitness 3-Ring Neon Glow Card */
        .apex-cp-rings-card {
            background:
                radial-gradient(circle at 50% 42%, rgba(164, 255, 46, 0.08) 0%, rgba(250, 17, 79, 0.08) 38%, transparent 72%),
                linear-gradient(180deg, #15161b 0%, #0f1014 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .apex-cp-rings-stage {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2px 0 6px;
        }
        .apex-cp-rings-svg {
            width: 196px;
            height: 196px;
            display: block;
            overflow: visible;
            filter: drop-shadow(0 0 24px rgba(250, 17, 79, 0.14));
        }
        .apex-cp-ring-metrics {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
            padding-top: 10px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            text-align: center;
        }
        .apex-cp-rm-lbl {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 600;
            color: #a1a1aa;
            margin-bottom: 3px;
        }
        .apex-cp-rm-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            display: inline-block;
        }
        .apex-cp-rm-val {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -0.02em;
            line-height: 1.1;
        }
        .apex-cp-rm-val small {
            font-size: 12px;
            font-weight: 600;
            opacity: 0.58;
        }
        .apex-cp-rm-unit {
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: 0.08em;
            color: #71717a;
            margin-top: 2px;
        }

        /* Interactive SVG Charts (Bar & Dual-Glow Area) */
        .apex-cp-svg { width: 100%; height: auto; display: block; overflow: visible; }
        .apex-cp-bar-group { cursor: pointer; transition: opacity 0.22s ease; }
        .apex-cp-bar-group.is-dimmed { opacity: 0.45; }
        .apex-cp-bar-group:hover { opacity: 1 !important; }
        .apex-cp-bar-rect {
            transform-box: fill-box;
            transform-origin: center bottom;
            transition: filter 0.2s ease, opacity 0.2s ease;
        }
        .apex-cp-bar-group.is-active .apex-cp-bar-rect-done,
        .apex-cp-bar-group:hover .apex-cp-bar-rect-done {
            filter: drop-shadow(0 0 10px rgba(250, 17, 79, 0.72));
        }
        .apex-cp-area-node { cursor: pointer; }
        .apex-cp-legend-inline {
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
        .apex-cp-legend-dots { display: flex; gap: 14px; align-items: center; }
        .apex-cp-legend-dots span { display: inline-flex; align-items: center; gap: 6px; }
        .apex-cp-legend-dots i { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }

        /* Floating Liquid Glass Chart Tooltip */
        .apex-cp-tooltip {
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
        .apex-cp-tooltip.is-visible { opacity: 1; }
        .apex-cp-tooltip strong { display: block; color: #fff; font-size: 12.5px; margin-bottom: 2px; }

        /* Glowing Program Adherence Bars */
        .apex-cp-bars { display: grid; gap: 11px; }
        .apex-cp-bar-item {
            display: grid;
            grid-template-columns: 116px minmax(0, 1fr) 44px;
            align-items: center;
            gap: 12px;
            font-size: 12.5px;
            padding: 7px 10px;
            border-radius: 12px;
            border: 1px solid transparent;
            cursor: pointer;
            transition: background 0.2s ease, border-color 0.2s ease;
        }
        .apex-cp-bar-item:hover { background: rgba(255, 255, 255, 0.04); }
        .apex-cp-bar-item.is-selected {
            background: rgba(250, 17, 79, 0.1);
            border-color: rgba(250, 17, 79, 0.32);
        }
        .apex-cp-bar-track {
            height: 9px;
            background: rgba(255, 255, 255, 0.07);
            border-radius: 99px;
            position: relative;
            overflow: visible;
        }
        .apex-cp-bar-fill {
            height: 100%;
            border-radius: 99px;
            position: relative;
            transition: width 0.65s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .apex-cp-bar-fill::after {
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

        /* Student Status Table */
        .apex-cp-table-wrap { overflow-x: auto; }
        .apex-cp-table { width: 100%; border-collapse: collapse; min-width: 520px; }
        .apex-cp-table th {
            text-align: left;
            font-size: 10.5px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #7c8194;
            padding: 9px 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .apex-cp-table td {
            padding: 11px 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 13px;
            color: #e4e4e7;
            vertical-align: middle;
        }
        .apex-cp-table tbody tr { transition: background 0.18s ease; }
        .apex-cp-table tbody tr.is-selected {
            background: linear-gradient(90deg, rgba(250, 17, 79, 0.16) 0%, rgba(250, 17, 79, 0.04) 100%);
        }
        .apex-cp-table tbody tr:hover { background: rgba(255, 255, 255, 0.045); cursor: pointer; }
        .apex-cp-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid transparent;
        }
        .apex-cp-trend-up { color: var(--cp-lime); font-weight: 800; text-shadow: 0 0 12px rgba(164, 255, 46, 0.35); }
        .apex-cp-trend-down { color: var(--cp-pink-hi); font-weight: 800; text-shadow: 0 0 12px rgba(250, 17, 79, 0.4); }

        /* Coach Action Workspace */
        .apex-cp-studio {
            background: linear-gradient(180deg, #16171e 0%, #111217 100%);
            border: 1px solid rgba(250, 17, 79, 0.3);
            border-radius: 22px;
            padding: 24px 26px;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.38), inset 0 1px 0 rgba(255, 255, 255, 0.08);
        }
        .apex-cp-studio-head { display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; padding-bottom: 16px; border-bottom: 1px solid rgba(255, 255, 255, 0.08); margin-bottom: 18px; }
        .apex-cp-tabs { display: flex; gap: 8px; flex-wrap: wrap; }
        .apex-cp-tab {
            padding: 9px 16px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.04);
            color: #d4d4d8;
            font-weight: 700;
            font-size: 12.5px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .apex-cp-tab:hover { background: rgba(255, 255, 255, 0.09); color: #fff; }
        .apex-cp-tab.is-active {
            background: linear-gradient(135deg, #fa114f 0%, #d40f40 100%);
            border-color: rgba(255, 110, 145, 0.45);
            color: #fff;
            box-shadow: 0 8px 22px rgba(250, 17, 79, 0.35);
        }
        .apex-cp-pane { display: none; }
        .apex-cp-pane.is-active { display: block; }
        .apex-cp-form-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
        .apex-cp-field { display: flex; flex-direction: column; gap: 6px; }
        .apex-cp-field-full { grid-column: 1 / -1; }
        .apex-cp-field label { font-size: 12px; font-weight: 700; color: #d4d4d8; }
        .apex-cp-field input, .apex-cp-field select, .apex-cp-field textarea {
            width: 100%;
            background: #0a0b0f;
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 12px;
            padding: 10px 13px;
            color: #fff;
            font-size: 13px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .apex-cp-field input:focus, .apex-cp-field select:focus, .apex-cp-field textarea:focus {
            outline: none;
            border-color: var(--cp-pink);
            box-shadow: 0 0 0 3px rgba(250, 17, 79, 0.18);
        }
        .apex-cp-field textarea { min-height: 76px; resize: vertical; }
        .apex-cp-chat-box { background: #0a0b0f; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 16px; padding: 16px; max-height: 290px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; margin-bottom: 12px; }
        .apex-cp-msg { max-width: 78%; padding: 10px 14px; border-radius: 16px; font-size: 13px; line-height: 1.5; }
        .apex-cp-msg-coach { align-self: flex-end; background: linear-gradient(135deg, #fa114f, #c80d3f); color: #fff; border-bottom-right-radius: 4px; box-shadow: 0 6px 18px rgba(250, 17, 79, 0.24); }
        .apex-cp-msg-student { align-self: flex-start; background: #1c1e27; color: #f5f5f7; border: 1px solid rgba(255, 255, 255, 0.08); border-bottom-left-radius: 4px; }
        .apex-cp-msg small { display: block; opacity: 0.72; font-size: 11px; margin-top: 4px; }
        .apex-cp-chat-form { display: flex; gap: 10px; }
        .apex-cp-chat-form input { flex: 1; background: #0a0b0f; border: 1px solid rgba(255, 255, 255, 0.16); border-radius: 999px; padding: 10px 16px; color: #fff; font-size: 13px; }
        .apex-cp-alert { margin-top: 12px; padding: 11px 15px; border-radius: 12px; font-size: 13px; font-weight: 600; }
        .apex-cp-alert-ok { background: rgba(164, 255, 46, 0.12); border: 1px solid rgba(164, 255, 46, 0.42); color: #ccff77; }
        .apex-cp-alert-err { background: rgba(250, 17, 79, 0.15); border: 1px solid rgba(250, 17, 79, 0.45); color: #fda4af; }

        @media (max-width: 1180px) {
            .apex-cp-top3, .apex-cp-bot2 { grid-template-columns: 1fr; }
            .apex-cp-form-grid { grid-template-columns: 1fr; }
        }
    </style>

    <div class="apex-cp-wrap" id="apex-cp-root">
        <div class="apex-cp-tooltip" id="apex-cp-tooltip" role="tooltip"></div>

        <div class="apex-cp-banner">
            <div>
                <div class="apex-cp-eyebrow">
                    <span class="apex-cp-dot"></span>
                    <span><?php echo $is_admin ? 'YÖNETİCİ DENETİM GÖRÜNÜMÜ · TÜM KOÇLAR' : 'ÖZEL KOÇ PORTALI · SADECE KENDİ ÖĞRENCİLERİNİZ'; ?></span>
                </div>
                <h1 class="apex-cp-h1">APEX Koç Performans &amp; Öğrenci Stüdyosu</h1>
                <div class="apex-cp-sub">
                    <span>Hoş geldin <strong><?php echo esc_html( $cu->display_name ); ?></strong></span>
                    <span>Aktif Öğrenci: <strong id="cp-kpi-count"><?php echo count( $students ); ?></strong></span>
                    <span>Ort. İlerleme: <strong id="cp-kpi-prog" style="color:#fa114f;">%<?php echo esc_html( $avg_prog ); ?></strong></span>
                    <span>Ort. Program Uyumu: <strong id="cp-kpi-adh" style="color:#26e6ff;">%<?php echo esc_html( $avg_adh ); ?></strong></span>
                </div>
            </div>
            <div class="apex-cp-pills">
                <?php if ( $is_admin && ! empty( $coaches ) ) : ?>
                <form method="get" style="display:inline-flex;gap:8px;align-items:center;">
                    <input type="hidden" name="page" value="apex-coach-portal">
                    <select name="coach_id" class="apex-cp-select" onchange="this.form.submit()">
                        <option value="0" <?php selected( $coach_filter, 0 ); ?>>Tüm Koçlar (<?php echo count( $coaches ); ?> Koç · <?php echo (int) $total_all_students; ?> Öğrenci)</option>
                        <?php foreach ( $coaches as $co ) :
                            $co_cnt = (int) ( $coach_student_counts[ (int) $co->ID ] ?? 0 );
                        ?>
                            <option value="<?php echo esc_attr( $co->ID ); ?>" <?php selected( $coach_filter, (int) $co->ID ); ?>>Koç: <?php echo esc_html( $co->display_name ); ?> (<?php echo $co_cnt; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <?php endif; ?>
                <a href="<?php echo esc_url( home_url( '/uyelik/?panel=1' ) ); ?>" class="apex-cp-btn" target="_blank">Üye Görünümü ↗</a>
                <a href="<?php echo esc_url( wp_logout_url( wp_login_url( admin_url( 'admin.php?page=apex-coach-portal' ) ) ) ); ?>" class="apex-cp-btn apex-cp-btn-red">Güvenli Çıkış</a>
            </div>
        </div>

        <!-- ÜST SATIR: 3 DİNAMİK NEON GLOW ANALİZ KARTI -->
        <div class="apex-cp-top3">
            <!-- 1. Dinamik Etkileşimli Seans Performansı -->
            <div class="apex-cp-card">
                <div class="apex-cp-card-head">
                    <div>
                        <h2 class="apex-cp-card-title">Seans Performansı</h2>
                        <span class="apex-cp-card-sub">Planlanan vs Tamamlanan · Seçmek için tıkla</span>
                    </div>
                    <span class="apex-cp-card-sub" id="cp-bar-focus-pill" style="color:#ff4b7c;"></span>
                </div>
                <svg class="apex-cp-svg" id="cp-bar-svg" viewBox="0 0 340 190" role="img" aria-label="Öğrenci bazlı etkileşimli seans grafiği"></svg>
                <div class="apex-cp-legend-inline">
                    <div class="apex-cp-legend-dots">
                        <span><i style="background:#4b5268;"></i>Planlanan</span>
                        <span><i style="background:#fa114f;box-shadow:0 0 8px #fa114f;"></i>Tamamlanan</span>
                    </div>
                    <span style="font-size:11px;color:#71717a;">Canlı senkron</span>
                </div>
            </div>

            <!-- 2. Dinamik Neon Çift Eğri Trend Grafiği -->
            <div class="apex-cp-card">
                <div class="apex-cp-card-head">
                    <div>
                        <h2 class="apex-cp-card-title">Gelişim &amp; Beslenme Trendi</h2>
                        <span class="apex-cp-card-sub">Eğri üzerinde gezinerek detaylı ölçüm gör</span>
                    </div>
                    <span class="apex-cp-card-sub" id="cp-area-focus-pill" style="color:#a4ff2e;"></span>
                </div>
                <svg class="apex-cp-svg" id="cp-area-svg" viewBox="0 0 350 190" role="img" aria-label="Öğrenci gelişim ve beslenme uyumu neon trend grafiği"></svg>
                <div class="apex-cp-legend-inline">
                    <div class="apex-cp-legend-dots">
                        <span><i style="background:#a4ff2e;box-shadow:0 0 8px #a4ff2e;"></i>Beslenme Uyumu</span>
                        <span><i style="background:#26e6ff;box-shadow:0 0 8px #26e6ff;"></i>Hedef İlerleme</span>
                    </div>
                    <span style="font-size:11px;color:#71717a;">Neon telemetri</span>
                </div>
            </div>

            <!-- 3. Apple Fitness Tarzı İç İçe 3 Glow Aktivite Halkası -->
            <div class="apex-cp-card apex-cp-rings-card">
                <div class="apex-cp-card-head" style="margin-bottom:6px;">
                    <div>
                        <h2 class="apex-cp-card-title">Aktivite Halkaları</h2>
                        <span class="apex-cp-card-sub" id="cp-ring-subject">Seçili Öğrenci</span>
                    </div>
                    <div class="apex-cp-seg" role="tablist" aria-label="Halka görünüm modu">
                        <button type="button" class="apex-cp-seg-btn is-active" data-ring-mode="student">Öğrenci</button>
                        <button type="button" class="apex-cp-seg-btn" data-ring-mode="team">Genel</button>
                    </div>
                </div>

                <div class="apex-cp-rings-stage">
                    <svg class="apex-cp-rings-svg" id="cp-rings-svg" viewBox="0 0 220 220" role="img" aria-label="İlerleme, seans ve beslenme aktivite halkaları">
                        <defs>
                            <linearGradient id="ringGradPink" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#ff2d68" />
                                <stop offset="100%" stop-color="#fa114f" />
                            </linearGradient>
                            <linearGradient id="ringGradLime" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#c9ff33" />
                                <stop offset="100%" stop-color="#87e817" />
                            </linearGradient>
                            <linearGradient id="ringGradCyan" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#5ff5ff" />
                                <stop offset="100%" stop-color="#00c4e6" />
                            </linearGradient>

                            <radialGradient id="orbPink" cx="35%" cy="35%" r="65%">
                                <stop offset="0%" stop-color="#ffffff" />
                                <stop offset="42%" stop-color="#ff7ba3" />
                                <stop offset="100%" stop-color="#fa114f" />
                            </radialGradient>
                            <radialGradient id="orbLime" cx="35%" cy="35%" r="65%">
                                <stop offset="0%" stop-color="#ffffff" />
                                <stop offset="45%" stop-color="#dbff6e" />
                                <stop offset="100%" stop-color="#8de81c" />
                            </radialGradient>
                            <radialGradient id="orbCyan" cx="35%" cy="35%" r="65%">
                                <stop offset="0%" stop-color="#ffffff" />
                                <stop offset="45%" stop-color="#9cfaff" />
                                <stop offset="100%" stop-color="#00cce8" />
                            </radialGradient>

                            <filter id="glowPink" x="-45%" y="-45%" width="190%" height="190%">
                                <feGaussianBlur stdDeviation="4.8" result="blur" />
                                <feMerge><feMergeNode in="blur" /><feMergeNode in="SourceGraphic" /></feMerge>
                            </filter>
                            <filter id="glowLime" x="-45%" y="-45%" width="190%" height="190%">
                                <feGaussianBlur stdDeviation="4.5" result="blur" />
                                <feMerge><feMergeNode in="blur" /><feMergeNode in="SourceGraphic" /></feMerge>
                            </filter>
                            <filter id="glowCyan" x="-45%" y="-45%" width="190%" height="190%">
                                <feGaussianBlur stdDeviation="4.5" result="blur" />
                                <feMerge><feMergeNode in="blur" /><feMergeNode in="SourceGraphic" /></feMerge>
                            </filter>
                            <filter id="tipShadow" x="-60%" y="-60%" width="220%" height="220%">
                                <feGaussianBlur stdDeviation="2.6" />
                            </filter>
                        </defs>

                        <!-- Koyu arka iz halkaları -->
                        <circle cx="110" cy="110" r="86" fill="none" stroke="rgba(250, 17, 79, 0.17)" stroke-width="19" />
                        <circle cx="110" cy="110" r="63" fill="none" stroke="rgba(164, 255, 46, 0.16)" stroke-width="19" />
                        <circle cx="110" cy="110" r="40" fill="none" stroke="rgba(38, 230, 255, 0.16)" stroke-width="19" />

                        <!-- Parlayan neon ilerleme yayları (-90° saat 12 başlangıç) -->
                        <g transform="rotate(-90 110 110)">
                            <circle id="cp-ring-pink" cx="110" cy="110" r="86" fill="none" stroke="url(#ringGradPink)" stroke-width="19" stroke-linecap="round" filter="url(#glowPink)" />
                            <circle id="cp-ring-lime" cx="110" cy="110" r="63" fill="none" stroke="url(#ringGradLime)" stroke-width="19" stroke-linecap="round" filter="url(#glowLime)" />
                            <circle id="cp-ring-cyan" cx="110" cy="110" r="40" fill="none" stroke="url(#ringGradCyan)" stroke-width="19" stroke-linecap="round" filter="url(#glowCyan)" />
                        </g>

                        <!-- Halka uçlarında derinlik gölgeli 3D parlak glow küreler -->
                        <circle id="cp-orb-pink-sh" cx="110" cy="24" r="9.5" fill="rgba(0,0,0,0.52)" filter="url(#tipShadow)" />
                        <circle id="cp-orb-pink" cx="110" cy="24" r="9.2" fill="url(#orbPink)" filter="url(#glowPink)" />

                        <circle id="cp-orb-lime-sh" cx="110" cy="47" r="9.5" fill="rgba(0,0,0,0.52)" filter="url(#tipShadow)" />
                        <circle id="cp-orb-lime" cx="110" cy="47" r="9.2" fill="url(#orbLime)" filter="url(#glowLime)" />

                        <circle id="cp-orb-cyan-sh" cx="110" cy="70" r="9.5" fill="rgba(0,0,0,0.52)" filter="url(#tipShadow)" />
                        <circle id="cp-orb-cyan" cx="110" cy="70" r="9.2" fill="url(#orbCyan)" filter="url(#glowCyan)" />

                        <!-- Merkez koyu boşluk -->
                        <circle cx="110" cy="110" r="24" fill="#111216" />
                    </svg>
                </div>

                <div class="apex-cp-ring-metrics">
                    <div>
                        <div class="apex-cp-rm-lbl"><span class="apex-cp-rm-dot" style="background:#fa114f;box-shadow:0 0 8px #fa114f;"></span>İlerleme</div>
                        <div class="apex-cp-rm-val" style="color:#ff3b72;" id="cp-rm-prog">0<small>/100</small></div>
                        <div class="apex-cp-rm-unit">HEDEF %</div>
                    </div>
                    <div>
                        <div class="apex-cp-rm-lbl"><span class="apex-cp-rm-dot" style="background:#a4ff2e;box-shadow:0 0 8px #a4ff2e;"></span>Seans</div>
                        <div class="apex-cp-rm-val" style="color:#a4ff2e;" id="cp-rm-sess">0<small>/12</small></div>
                        <div class="apex-cp-rm-unit">SEANS</div>
                    </div>
                    <div>
                        <div class="apex-cp-rm-lbl"><span class="apex-cp-rm-dot" style="background:#26e6ff;box-shadow:0 0 8px #26e6ff;"></span>Beslenme</div>
                        <div class="apex-cp-rm-val" style="color:#26e6ff;" id="cp-rm-adh">0<small>/100</small></div>
                        <div class="apex-cp-rm-unit">UYUM %</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ALT SATIR: SOL GLOW PROGRAM UYUMU + SAĞ FİLTRELENEBİLİR ÖĞRENCİ TABLOSU -->
        <div class="apex-cp-bot2">
            <div class="apex-cp-card">
                <div class="apex-cp-card-head">
                    <div>
                        <h2 class="apex-cp-card-title">Program Uyumu</h2>
                        <span class="apex-cp-card-sub">Diyet &amp; antrenman disiplin endeksi</span>
                    </div>
                    <span class="apex-cp-card-sub" style="color:#26e6ff;">Canlı</span>
                </div>
                <div class="apex-cp-bars" id="cp-adherence-bars"></div>
            </div>

            <div class="apex-cp-card">
                <div class="apex-cp-card-head">
                    <div>
                        <h2 class="apex-cp-card-title">Öğrenci Takip Listesi</h2>
                        <span class="apex-cp-card-sub">Satıra tıkla · tüm grafikler ve halkalar anında güncellensin</span>
                    </div>
                    <div class="apex-cp-seg" id="cp-status-filters" role="tablist" aria-label="Duruma göre filtrele">
                        <button type="button" class="apex-cp-seg-btn is-active" data-status-filter="all">Tümü</button>
                        <button type="button" class="apex-cp-seg-btn" data-status-filter="hedefte">Hedefte</button>
                        <button type="button" class="apex-cp-seg-btn" data-status-filter="dikkat">Dikkat</button>
                        <button type="button" class="apex-cp-seg-btn" data-status-filter="yeni">Yeni</button>
                        <button type="button" class="apex-cp-seg-btn" data-status-filter="riskli">Takip</button>
                    </div>
                </div>
                <div class="apex-cp-table-wrap">
                    <table class="apex-cp-table" id="apex-cp-student-table">
                        <thead>
                            <tr><th>#</th><th>Öğrenci &amp; Hedef</th><th>Durum</th><th>İlerleme</th><th>İşlem</th></tr>
                        </thead>
                        <tbody id="cp-student-tbody"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ETKİLEŞİMLİ KOÇ ÇALIŞMA ALANI: DURUM/ÖNERİ + BESLENME TAKVİMİ + 1-1 MESAJLAŞMA -->
        <?php if ( $first_id > 0 ) : ?>
        <div class="apex-cp-studio" id="apex-cp-studio">
            <div class="apex-cp-studio-head">
                <div>
                    <div class="apex-cp-eyebrow" style="margin-bottom:4px;">SEÇİLİ ÖĞRENCİ KOÇLUK DOSYASI</div>
                    <h2 style="margin:0;color:#fff;font-size:20px;font-weight:800;" id="cp-active-name"></h2>
                    <div style="font-size:12.5px;color:#9ca3af;margin-top:3px;" id="cp-active-meta"></div>
                </div>
                <div class="apex-cp-tabs" role="tablist">
                    <button type="button" class="apex-cp-tab is-active" data-cp-tab="status">Durum &amp; Koç Önerisi</button>
                    <button type="button" class="apex-cp-tab" data-cp-tab="nutrition">Beslenme Takvimi</button>
                    <button type="button" class="apex-cp-tab" data-cp-tab="chat">Birebir Mesajlaşma</button>
                </div>
            </div>

            <form id="apex-cp-save-form">
                <input type="hidden" id="cp-student-id" value="<?php echo esc_attr( $first_id ); ?>">

                <!-- TAB 1: DURUM & ÖNERİ -->
                <div class="apex-cp-pane is-active" data-cp-pane="status">
                    <div class="apex-cp-form-grid">
                        <div class="apex-cp-field">
                            <label for="cp-status">Öğrenci Durumu</label>
                            <select id="cp-status">
                                <option value="hedefte">Hedefte (Neon Yeşil)</option>
                                <option value="dikkat">Dikkat Gerekiyor (Kehribar)</option>
                                <option value="yeni">Yeni Başladı (Turkuaz)</option>
                                <option value="riskli">Yoğun Takip / Riskli (Neon Pembe)</option>
                            </select>
                        </div>
                        <div class="apex-cp-field">
                            <label for="cp-progress">Hedef İlerleme (%)</label>
                            <input type="number" id="cp-progress" min="0" max="100">
                        </div>
                        <div class="apex-cp-field">
                            <label for="cp-adherence">Program &amp; Diyet Uyumu (%)</label>
                            <input type="number" id="cp-adherence" min="0" max="100">
                        </div>
                        <div class="apex-cp-field">
                            <label for="cp-trend">Haftalık İvme Yönü</label>
                            <select id="cp-trend">
                                <option value="up">Yükselişte (↑)</option>
                                <option value="down">Düşüşte (↓)</option>
                            </select>
                        </div>
                        <div class="apex-cp-field">
                            <label for="cp-cur-weight">Güncel Kilo (kg)</label>
                            <input type="number" step="0.1" id="cp-cur-weight">
                        </div>
                        <div class="apex-cp-field">
                            <label for="cp-tar-weight">Hedef Kilo (kg)</label>
                            <input type="number" step="0.1" id="cp-tar-weight">
                        </div>
                        <div class="apex-cp-field apex-cp-field-full">
                            <label for="cp-advice">Koç Kişisel Önerisi &amp; Antrenman Yönlendirmesi (Öğrencinin Üye Panelinde Canlı Görünür)</label>
                            <textarea id="cp-advice" rows="3"></textarea>
                        </div>
                    </div>
                    <p style="margin:16px 0 0;"><button type="submit" class="apex-cp-btn apex-cp-btn-red">Durum, Öneri &amp; Beslenme Planını Kaydet</button></p>
                </div>

                <!-- TAB 2: BESLENME TAKVİMİ -->
                <div class="apex-cp-pane" data-cp-pane="nutrition">
                    <div class="apex-cp-form-grid">
                        <div class="apex-cp-field"><label for="cp-kcal">Günlük Kalori (kcal)</label><input type="number" id="cp-kcal"></div>
                        <div class="apex-cp-field"><label for="cp-pro">Protein (g)</label><input type="number" id="cp-pro"></div>
                        <div class="apex-cp-field"><label for="cp-carb">Karbonhidrat (g)</label><input type="number" id="cp-carb"></div>
                        <div class="apex-cp-field"><label for="cp-fat">Sağlıklı Yağ (g)</label><input type="number" id="cp-fat"></div>
                        <div class="apex-cp-field"><label for="cp-water">Günlük Su (Litre)</label><input type="number" step="0.1" id="cp-water"></div>
                        <div class="apex-cp-field"><label>&nbsp;</label><button type="submit" class="apex-cp-btn apex-cp-btn-red" style="justify-content:center;">Beslenme Takvimini Yayınla</button></div>

                        <div class="apex-cp-field apex-cp-field-full"><label for="cp-m-sabah">08:00 · Sabah Kahvaltı</label><textarea id="cp-m-sabah" rows="2"></textarea></div>
                        <div class="apex-cp-field"><label for="cp-m-ara1">11:00 · Ara Öğün</label><textarea id="cp-m-ara1" rows="2"></textarea></div>
                        <div class="apex-cp-field"><label for="cp-m-ogle">13:30 · Öğle Yemeği</label><textarea id="cp-m-ogle" rows="2"></textarea></div>
                        <div class="apex-cp-field"><label for="cp-m-ant">Antrenman Öncesi / Sonrası</label><textarea id="cp-m-ant" rows="2"></textarea></div>
                        <div class="apex-cp-field apex-cp-field-full"><label for="cp-m-aksam">19:30 · Akşam Yemeği</label><textarea id="cp-m-aksam" rows="2"></textarea></div>
                        <div class="apex-cp-field apex-cp-field-full"><label for="cp-m-notes">Haftalık Rotasyon &amp; Takviye Notları</label><textarea id="cp-m-notes" rows="2"></textarea></div>
                    </div>
                    <p style="margin:14px 0 0;"><button type="submit" class="apex-cp-btn apex-cp-btn-red">Beslenme Takvimini Kaydet &amp; Öğrenciye Gönder</button></p>
                </div>
            </form>

            <!-- TAB 3: BİREBİR MESAJLAŞMA -->
            <div class="apex-cp-pane" data-cp-pane="chat">
                <div class="apex-cp-chat-box" id="cp-chat-list" aria-live="polite"></div>
                <form id="apex-cp-chat-form" class="apex-cp-chat-form">
                    <input type="text" id="cp-chat-input" placeholder="Öğrencinize birebir mesaj, antrenman hatırlatması veya beslenme notu yazın..." required>
                    <button type="submit" class="apex-cp-btn apex-cp-btn-red">Mesaj Gönder</button>
                </form>
            </div>

            <div id="cp-feedback" role="status" aria-live="polite"></div>
        </div>
        <?php endif; ?>
    </div>

    <script>
    (function(){
        const DATA = <?php echo wp_json_encode( $payload_map, JSON_UNESCAPED_UNICODE ); ?>;
        const ORDER = <?php echo wp_json_encode( $student_list ); ?>;
        const REST = <?php echo wp_json_encode( $rest_url ); ?>;
        const NONCE = <?php echo wp_json_encode( $nonce ); ?>;
        let currentId = <?php echo (int) $first_id; ?>;
        let ringMode = 'student';
        let statusFilter = 'all';

        const STATUS_META = {
            hedefte: { label: 'Hedefte',      color: '#a4ff2e', bg: 'rgba(164,255,46,0.13)', border: 'rgba(164,255,46,0.34)' },
            dikkat:  { label: 'Dikkat',       color: '#ffbe0b', bg: 'rgba(255,190,11,0.14)', border: 'rgba(255,190,11,0.34)' },
            yeni:    { label: 'Yeni Başladı', color: '#26e6ff', bg: 'rgba(38,230,255,0.13)', border: 'rgba(38,230,255,0.34)' },
            riskli:  { label: 'Yoğun Takip',  color: '#fa114f', bg: 'rgba(250,17,79,0.16)',  border: 'rgba(250,17,79,0.38)' },
        };

        const $ = (id) => document.getElementById(id);
        const fb = $('cp-feedback');
        const tip = $('apex-cp-tooltip');

        function getList() {
            return ORDER.map((id) => DATA[id]).filter(Boolean);
        }
        function firstName(full) {
            return String(full || '').trim().split(/\s+/)[0] || '';
        }
        function esc(str) {
            return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
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
        document.querySelectorAll('.apex-cp-card').forEach((card) => {
            card.addEventListener('pointermove', (e) => {
                const r = card.getBoundingClientRect();
                card.style.setProperty('--mx', (e.clientX - r.left).toFixed(0) + 'px');
                card.style.setProperty('--my', (e.clientY - r.top).toFixed(0) + 'px');
            }, { passive: true });
        });

        // =====================================================================
        // 1. APPLE FITNESS 3 GLOW AKTİVİTE HALKASI (rAF Yumuşak Animasyonlu!)
        // =====================================================================
        const RINGS = [
            { r: 86, el: $('cp-ring-pink'), orb: $('cp-orb-pink'), sh: $('cp-orb-pink-sh'), cur: 0 },
            { r: 63, el: $('cp-ring-lime'), orb: $('cp-orb-lime'), sh: $('cp-orb-lime-sh'), cur: 0 },
            { r: 40, el: $('cp-ring-cyan'), orb: $('cp-orb-cyan'), sh: $('cp-orb-cyan-sh'), cur: 0 },
        ];
        RINGS.forEach((rg) => {
            rg.circ = 2 * Math.PI * rg.r;
            if (rg.el) {
                rg.el.setAttribute('stroke-dasharray', rg.circ.toFixed(2));
                rg.el.setAttribute('stroke-dashoffset', rg.circ.toFixed(2));
            }
        });

        let ringAnimRaf = null;
        function setRingVisual(rg, pct) {
            const clamped = Math.max(0.02, Math.min(1, pct / 100));
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

        function animateRingsTo(targets, labels) {
            if (ringAnimRaf) cancelAnimationFrame(ringAnimRaf);
            const starts = RINGS.map((rg) => rg.cur);
            const t0 = performance.now();
            const dur = 760;

            const step = (now) => {
                const p = Math.min(1, (now - t0) / dur);
                const ease = 1 - Math.pow(1 - p, 3);
                RINGS.forEach((rg, i) => {
                    const val = starts[i] + (targets[i] - starts[i]) * ease;
                    rg.cur = val;
                    setRingVisual(rg, val);
                });
                const pProg = $('cp-rm-prog');
                const pSess = $('cp-rm-sess');
                const pAdh  = $('cp-rm-adh');
                if (pProg) pProg.innerHTML = Math.round(starts[0] + (targets[0] - starts[0]) * ease) + '<small>/100</small>';
                if (pSess) pSess.innerHTML = Math.round(labels.sessDone * ease) + '<small>/' + labels.sessPlan + '</small>';
                if (pAdh)  pAdh.innerHTML  = Math.round(starts[2] + (targets[2] - starts[2]) * ease) + '<small>/100</small>';
                if (p < 1) ringAnimRaf = requestAnimationFrame(step);
                else {
                    if (pProg) pProg.innerHTML = Math.round(targets[0]) + '<small>/100</small>';
                    if (pSess) pSess.innerHTML = labels.sessDone + '<small>/' + labels.sessPlan + '</small>';
                    if (pAdh)  pAdh.innerHTML  = Math.round(targets[2]) + '<small>/100</small>';
                }
            };
            ringAnimRaf = requestAnimationFrame(step);
        }

        function renderRings() {
            const list = getList();
            if (!list.length) return;
            const subj = $('cp-ring-subject');
            if (ringMode === 'team' || !DATA[currentId]) {
                const avgProg = Math.round(list.reduce((s, x) => s + x.progress_pct, 0) / list.length);
                const totDone = list.reduce((s, x) => s + x.sessions_done, 0);
                const totPlan = Math.max(1, list.reduce((s, x) => s + x.sessions_planned, 0));
                const sessPct = Math.round((totDone / totPlan) * 100);
                const avgAdh  = Math.round(list.reduce((s, x) => s + x.adherence_pct, 0) / list.length);
                if (subj) subj.textContent = `Tüm Öğrenciler (${list.length} Kişi)`;
                animateRingsTo([avgProg, sessPct, avgAdh], { sessDone: totDone, sessPlan: totPlan });
            } else {
                const st = DATA[currentId];
                const sessPct = Math.round((st.sessions_done / Math.max(1, st.sessions_planned)) * 100);
                if (subj) subj.textContent = st.student_name;
                animateRingsTo([st.progress_pct, sessPct, st.adherence_pct], { sessDone: st.sessions_done, sessPlan: st.sessions_planned });
            }
        }

        // =====================================================================
        // 2. DİNAMİK ETKİLEŞİMLİ SEANS BAR GRAFİĞİ (Tam İsim + Glow + Tooltip)
        // =====================================================================
        function renderBarChart() {
            const svg = $('cp-bar-svg');
            const list = getList().slice(0, 7);
            if (!svg || !list.length) return;
            const maxS = Math.max(12, ...list.map((s) => Math.max(s.sessions_planned, s.sessions_done)));
            const baseY = 158;
            const chartH = 128;
            const leftX = 32;
            const usableW = 296;
            const slotW = usableW / list.length;
            const barW = Math.min(13, Math.max(9, slotW * 0.27));

            let html = `<defs>
                <linearGradient id="cpBarDoneGrad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#ff4b7c" />
                    <stop offset="100%" stop-color="#c40b3e" />
                </linearGradient>
                <linearGradient id="cpBarActiveGrad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#c9ff33" />
                    <stop offset="100%" stop-color="#6ac90f" />
                </linearGradient>
                <linearGradient id="cpBarPlanGrad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#434a62" />
                    <stop offset="100%" stop-color="#262a38" />
                </linearGradient>
            </defs>`;

            [0, 0.5, 1].forEach((frac) => {
                const gy = baseY - frac * chartH;
                const val = Math.round(maxS * frac);
                html += `<line x1="${leftX}" y1="${gy.toFixed(1)}" x2="332" y2="${gy.toFixed(1)}" stroke="rgba(255,255,255,${frac === 0 ? 0.14 : 0.05})" ${frac > 0 ? 'stroke-dasharray="3 4"' : ''} />`;
                html += `<text x="24" y="${(gy + 3).toFixed(1)}" text-anchor="end" fill="#6b7280" font-size="9.5">${val}</text>`;
            });

            list.forEach((st, i) => {
                const cx = leftX + slotW * (i + 0.5);
                const hPlan = Math.max(8, (st.sessions_planned / maxS) * chartH);
                const hDone = Math.max(6, (st.sessions_done / maxS) * chartH);
                const active = st.id === currentId;
                const doneFill = active ? 'url(#cpBarActiveGrad)' : 'url(#cpBarDoneGrad)';
                const glowStyle = active ? 'filter:drop-shadow(0 0 9px rgba(164,255,46,0.58));' : '';
                html += `<g class="apex-cp-bar-group ${active ? 'is-active' : ''}" data-chart-id="${st.id}">
                    <rect x="${(cx - slotW * 0.45).toFixed(1)}" y="24" width="${(slotW * 0.9).toFixed(1)}" height="${chartH + 14}" fill="transparent" />
                    <rect class="apex-cp-bar-rect" x="${(cx - barW - 1.5).toFixed(1)}" y="${(baseY - hPlan).toFixed(1)}" width="${barW.toFixed(1)}" height="${hPlan.toFixed(1)}" rx="4" fill="url(#cpBarPlanGrad)" />
                    <rect class="apex-cp-bar-rect apex-cp-bar-rect-done" x="${(cx + 1.5).toFixed(1)}" y="${(baseY - hDone).toFixed(1)}" width="${barW.toFixed(1)}" height="${hDone.toFixed(1)}" rx="4" fill="${doneFill}" style="${glowStyle}" />
                    <text x="${cx.toFixed(1)}" y="176" text-anchor="middle" fill="${active ? '#ffffff' : '#8c90a0'}" font-weight="${active ? '800' : '600'}" font-size="10">${esc(firstName(st.student_name))}</text>
                </g>`;
            });

            svg.innerHTML = html;
            const focusEl = $('cp-bar-focus-pill');
            if (focusEl && DATA[currentId]) {
                const st = DATA[currentId];
                focusEl.textContent = `${firstName(st.student_name)}: ${st.sessions_done}/${st.sessions_planned} seans`;
            }

            svg.querySelectorAll('[data-chart-id]').forEach((g) => {
                const id = Number(g.getAttribute('data-chart-id'));
                g.addEventListener('pointermove', (ev) => {
                    const st = DATA[id];
                    if (!st) return;
                    const pct = Math.round((st.sessions_done / Math.max(1, st.sessions_planned)) * 100);
                    showTip(ev, `<strong>${esc(st.student_name)}</strong>Tamamlanan: <span style="color:#a4ff2e;font-weight:700;">${st.sessions_done}/${st.sessions_planned} seans</span> (%${pct})`);
                });
                g.addEventListener('pointerleave', hideTip);
                g.addEventListener('click', () => selectStudent(id));
            });
        }

        // =====================================================================
        // 3. DİNAMİK NEON ÇİFT EĞRİ TREND GRAFİĞİ (Lime & Cyan Glow)
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
            const svg = $('cp-area-svg');
            const list = getList().slice(0, 7);
            if (!svg || list.length < 2) return;
            const baseY = 158;
            const topY = 26;
            const chartH = baseY - topY;
            const leftX = 32;
            const rightX = 330;
            const stepX = (rightX - leftX) / Math.max(1, list.length - 1);

            const adhPts = list.map((st, i) => ({
                id: st.id, st,
                x: leftX + i * stepX,
                y: baseY - (st.adherence_pct / 100) * chartH,
            }));
            const progPts = list.map((st, i) => ({
                id: st.id, st,
                x: leftX + i * stepX,
                y: baseY - (st.progress_pct / 100) * chartH,
            }));

            let html = `<defs>
                <linearGradient id="cpAreaLime" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#a4ff2e" stop-opacity="0.34" />
                    <stop offset="100%" stop-color="#a4ff2e" stop-opacity="0.01" />
                </linearGradient>
                <linearGradient id="cpAreaCyan" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#26e6ff" stop-opacity="0.36" />
                    <stop offset="100%" stop-color="#26e6ff" stop-opacity="0.02" />
                </linearGradient>
                <filter id="cpLineGlow" x="-20%" y="-20%" width="140%" height="140%">
                    <feGaussianBlur stdDeviation="2.6" result="b" />
                    <feMerge><feMergeNode in="b" /><feMergeNode in="SourceGraphic" /></feMerge>
                </filter>
            </defs>`;

            [0, 50, 100].forEach((val) => {
                const gy = baseY - (val / 100) * chartH;
                html += `<line x1="${leftX}" y1="${gy.toFixed(1)}" x2="${rightX}" y2="${gy.toFixed(1)}" stroke="rgba(255,255,255,${val === 0 ? 0.14 : 0.05})" ${val > 0 ? 'stroke-dasharray="3 4"' : ''} />`;
                html += `<text x="24" y="${(gy + 3).toFixed(1)}" text-anchor="end" fill="#6b7280" font-size="9">${val}</text>`;
            });

            html += `<path d="${buildSmoothPath(adhPts, true, baseY)}" fill="url(#cpAreaLime)" />`;
            html += `<path d="${buildSmoothPath(progPts, true, baseY)}" fill="url(#cpAreaCyan)" />`;
            html += `<path d="${buildSmoothPath(adhPts, false, baseY)}" fill="none" stroke="#a4ff2e" stroke-width="2.4" filter="url(#cpLineGlow)" />`;
            html += `<path d="${buildSmoothPath(progPts, false, baseY)}" fill="none" stroke="#26e6ff" stroke-width="2.4" filter="url(#cpLineGlow)" />`;

            list.forEach((st, i) => {
                const ax = adhPts[i].x, ay = adhPts[i].y;
                const py = progPts[i].y;
                const active = st.id === currentId;
                html += `<g class="apex-cp-area-node" data-area-id="${st.id}">
                    <rect x="${(ax - stepX * 0.45).toFixed(1)}" y="${topY}" width="${(stepX * 0.9).toFixed(1)}" height="${chartH + 22}" fill="transparent" />
                    ${active ? `<line x1="${ax.toFixed(1)}" y1="${topY}" x2="${ax.toFixed(1)}" y2="${baseY}" stroke="rgba(255,255,255,0.22)" stroke-dasharray="3 3" />` : ''}
                    <circle cx="${ax.toFixed(1)}" cy="${ay.toFixed(1)}" r="${active ? 5 : 3.2}" fill="${active ? '#ffffff' : '#a4ff2e'}" stroke="#a4ff2e" stroke-width="2" ${active ? 'filter="url(#cpLineGlow)"' : ''} />
                    <circle cx="${ax.toFixed(1)}" cy="${py.toFixed(1)}" r="${active ? 5 : 3.2}" fill="${active ? '#ffffff' : '#26e6ff'}" stroke="#26e6ff" stroke-width="2" ${active ? 'filter="url(#cpLineGlow)"' : ''} />
                    <text x="${ax.toFixed(1)}" y="176" text-anchor="middle" fill="${active ? '#ffffff' : '#8c90a0'}" font-weight="${active ? '800' : '600'}" font-size="10">${esc(firstName(st.student_name))}</text>
                </g>`;
            });

            svg.innerHTML = html;
            const focusEl = $('cp-area-focus-pill');
            if (focusEl && DATA[currentId]) {
                const st = DATA[currentId];
                focusEl.textContent = `${firstName(st.student_name)}: %${st.progress_pct} / %${st.adherence_pct}`;
            }

            svg.querySelectorAll('[data-area-id]').forEach((g) => {
                const id = Number(g.getAttribute('data-area-id'));
                g.addEventListener('pointermove', (ev) => {
                    const st = DATA[id];
                    if (!st) return;
                    showTip(ev, `<strong>${esc(st.student_name)}</strong>Beslenme Uyumu: <span style="color:#a4ff2e;font-weight:700;">%${st.adherence_pct}</span><br>Hedef İlerleme: <span style="color:#26e6ff;font-weight:700;">%${st.progress_pct}</span>`);
                });
                g.addEventListener('pointerleave', hideTip);
                g.addEventListener('click', () => selectStudent(id));
            });
        }

        // =====================================================================
        // 4. GLOW PROGRAM UYUM BARLARI & FİLTRELENEBİLİR ÖĞRENCİ TABLOSU
        // =====================================================================
        function renderAdherenceBars() {
            const box = $('cp-adherence-bars');
            if (!box) return;
            const list = getList();
            box.innerHTML = list.map((st) => {
                const c = st.adherence_pct >= 80 ? '#a4ff2e' : (st.adherence_pct >= 65 ? '#26e6ff' : (st.adherence_pct >= 50 ? '#ffbe0b' : '#fa114f'));
                const grad = st.adherence_pct >= 80 ? 'linear-gradient(90deg,#68c714,#c9ff33)' :
                             (st.adherence_pct >= 65 ? 'linear-gradient(90deg,#00a6cc,#5ff5ff)' :
                             (st.adherence_pct >= 50 ? 'linear-gradient(90deg,#d97706,#ffd13b)' : 'linear-gradient(90deg,#b80e3b,#ff4b7c)'));
                return `<div class="apex-cp-bar-item ${st.id === currentId ? 'is-selected' : ''}" data-select-student="${st.id}">
                    <strong style="color:#f5f5f7;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${esc(st.student_name)}</strong>
                    <div class="apex-cp-bar-track">
                        <div class="apex-cp-bar-fill" style="width:${st.adherence_pct}%;background:${grad};color:${c};box-shadow:0 0 14px ${c}44;"></div>
                    </div>
                    <span style="text-align:right;font-weight:800;color:${c};">%${st.adherence_pct}</span>
                </div>`;
            }).join('');

            box.querySelectorAll('[data-select-student]').forEach((el) => {
                el.addEventListener('click', () => selectStudent(Number(el.getAttribute('data-select-student'))));
            });
        }

        function renderStudentTable() {
            const tbody = $('cp-student-tbody');
            if (!tbody) return;
            const list = getList().filter((st) => statusFilter === 'all' || st.status === statusFilter);
            if (!list.length) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:#8c90a0;padding:24px;">Bu durum filtresinde öğrenci bulunamadı.</td></tr>';
                return;
            }
            tbody.innerHTML = list.map((st, idx) => {
                const sm = STATUS_META[st.status] || STATUS_META.hedefte;
                const isDown = st.progress_trend === 'down';
                return `<tr data-select-student="${st.id}" class="${st.id === currentId ? 'is-selected' : ''}">
                    <td><strong>${idx + 1}</strong></td>
                    <td>
                        <strong style="color:#fff;">${esc(st.student_name)}</strong>
                        <div style="font-size:11.5px;color:#8c90a0;">${esc(st.goal)} · ${st.current_weight}→${st.target_weight} kg</div>
                    </td>
                    <td>
                        <span class="apex-cp-badge" style="background:${sm.bg};color:${sm.color};border-color:${sm.border};">
                            ${esc(sm.label)}
                        </span>
                    </td>
                    <td>
                        <span class="${isDown ? 'apex-cp-trend-down' : 'apex-cp-trend-up'}">
                            %${st.progress_pct} ${isDown ? '↓' : '↑'}
                        </span>
                    </td>
                    <td>
                        <button type="button" class="apex-cp-btn" style="padding:5px 12px;font-size:11.5px;" data-select-student="${st.id}">Beslenme &amp; Mesaj</button>
                    </td>
                </tr>`;
            }).join('');

            tbody.querySelectorAll('[data-select-student]').forEach((el) => {
                el.addEventListener('click', (e) => {
                    e.stopPropagation();
                    selectStudent(Number(el.getAttribute('data-select-student')));
                });
            });
        }

        function updateHeaderKpis() {
            const list = getList();
            if (!list.length) return;
            const avgP = Math.round(list.reduce((s, x) => s + x.progress_pct, 0) / list.length);
            const avgA = Math.round(list.reduce((s, x) => s + x.adherence_pct, 0) / list.length);
            if ($('cp-kpi-count')) $('cp-kpi-count').textContent = String(list.length);
            if ($('cp-kpi-prog'))  $('cp-kpi-prog').textContent  = '%' + avgP;
            if ($('cp-kpi-adh'))   $('cp-kpi-adh').textContent   = '%' + avgA;
        }

        function renderChat(msgs) {
            const box = $('cp-chat-list');
            if (!box) return;
            box.innerHTML = '';
            if (!msgs || !msgs.length) {
                box.innerHTML = '<div style="color:#8c90a0;font-size:13px;text-align:center;padding:24px;">Henüz birebir mesaj yok. İlk mesajı aşağıdan gönderebilirsiniz.</div>';
                return;
            }
            msgs.forEach((m) => {
                const div = document.createElement('div');
                div.className = 'apex-cp-msg ' + (m.sender_role === 'coach' ? 'apex-cp-msg-coach' : 'apex-cp-msg-student');
                div.textContent = m.message;
                const sm = document.createElement('small');
                sm.textContent = (m.sender_name || '') + (m.time ? ' · ' + m.time : '');
                div.appendChild(sm);
                box.appendChild(div);
            });
            box.scrollTop = box.scrollHeight;
        }

        function renderAllVisuals() {
            updateHeaderKpis();
            renderBarChart();
            renderAreaChart();
            renderRings();
            renderAdherenceBars();
            renderStudentTable();
        }

        function selectStudent(id) {
            const st = DATA[id];
            if (!st) return;
            currentId = id;
            if ($('cp-student-id')) $('cp-student-id').value = id;
            if ($('cp-active-name')) $('cp-active-name').textContent = st.student_name;
            if ($('cp-active-meta')) $('cp-active-meta').textContent = `Koç: ${st.coach_name} · Hedef: ${st.goal} · Seans: ${st.sessions_done}/${st.sessions_planned} · E-posta: ${st.student_email}`;
            if ($('cp-status')) $('cp-status').value = st.status;
            if ($('cp-progress')) $('cp-progress').value = st.progress_pct;
            if ($('cp-adherence')) $('cp-adherence').value = st.adherence_pct;
            if ($('cp-trend')) $('cp-trend').value = st.progress_trend;
            if ($('cp-cur-weight')) $('cp-cur-weight').value = st.current_weight;
            if ($('cp-tar-weight')) $('cp-tar-weight').value = st.target_weight;
            if ($('cp-advice')) $('cp-advice').value = st.coach_advice;

            if ($('cp-kcal')) $('cp-kcal').value = st.nutrition.daily_kcal;
            if ($('cp-pro')) $('cp-pro').value = st.nutrition.protein_g;
            if ($('cp-carb')) $('cp-carb').value = st.nutrition.carbs_g;
            if ($('cp-fat')) $('cp-fat').value = st.nutrition.fat_g;
            if ($('cp-water')) $('cp-water').value = st.nutrition.water_l;
            if ($('cp-m-sabah')) $('cp-m-sabah').value = st.nutrition.sabah;
            if ($('cp-m-ara1')) $('cp-m-ara1').value = st.nutrition.ara1;
            if ($('cp-m-ogle')) $('cp-m-ogle').value = st.nutrition.ogle;
            if ($('cp-m-ant')) $('cp-m-ant').value = st.nutrition.ant_sonra;
            if ($('cp-m-aksam')) $('cp-m-aksam').value = st.nutrition.aksam;
            if ($('cp-m-notes')) $('cp-m-notes').value = st.nutrition.weekly_notes;

            renderChat(st.messages);
            renderAllVisuals();
            if (fb) fb.innerHTML = '';
        }

        // Halka Öğrenci / Genel geçişi
        document.querySelectorAll('[data-ring-mode]').forEach((btn) => {
            btn.addEventListener('click', () => {
                ringMode = btn.getAttribute('data-ring-mode');
                document.querySelectorAll('[data-ring-mode]').forEach((b) => b.classList.toggle('is-active', b === btn));
                renderRings();
            });
        });

        // Durum filtresi
        document.querySelectorAll('[data-status-filter]').forEach((btn) => {
            btn.addEventListener('click', () => {
                statusFilter = btn.getAttribute('data-status-filter');
                document.querySelectorAll('[data-status-filter]').forEach((b) => b.classList.toggle('is-active', b === btn));
                renderStudentTable();
            });
        });

        // Stüdyo sekmeleri
        document.querySelectorAll('[data-cp-tab]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const target = btn.getAttribute('data-cp-tab');
                document.querySelectorAll('[data-cp-tab]').forEach((b) => b.classList.toggle('is-active', b === btn));
                document.querySelectorAll('[data-cp-pane]').forEach((p) => p.classList.toggle('is-active', p.getAttribute('data-cp-pane') === target));
            });
        });

        // Kaydet → Tüm 5 grafik + Apple Fitness halkaları canlı yeniden canlanır!
        const saveForm = $('apex-cp-save-form');
        if (saveForm) {
            saveForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const payload = {
                    student_id: Number($('cp-student-id').value),
                    status: $('cp-status').value,
                    progress_pct: Number($('cp-progress').value),
                    adherence_pct: Number($('cp-adherence').value),
                    progress_trend: $('cp-trend').value,
                    current_weight: parseFloat($('cp-cur-weight').value),
                    target_weight: parseFloat($('cp-tar-weight').value),
                    coach_advice: $('cp-advice').value,
                    nutrition: {
                        daily_kcal: Number($('cp-kcal').value),
                        protein_g: Number($('cp-pro').value),
                        carbs_g: Number($('cp-carb').value),
                        fat_g: Number($('cp-fat').value),
                        water_l: parseFloat($('cp-water').value),
                        sabah: $('cp-m-sabah').value,
                        ara1: $('cp-m-ara1').value,
                        ogle: $('cp-m-ogle').value,
                        ant_sonra: $('cp-m-ant').value,
                        aksam: $('cp-m-aksam').value,
                        weekly_notes: $('cp-m-notes').value,
                    },
                };
                const r = await fetch(REST + 'coach/save-student', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': NONCE },
                    body: JSON.stringify(payload),
                });
                const j = await r.json().catch(() => ({}));
                if (r.ok && j.success) {
                    Object.assign(DATA[currentId], payload);
                    renderAllVisuals();
                    fb.innerHTML = '<div class="apex-cp-alert apex-cp-alert-ok">✓ ' + esc(j.message) + '</div>';
                } else {
                    fb.innerHTML = '<div class="apex-cp-alert apex-cp-alert-err">⚠ ' + esc(j.message || 'Kaydedilemedi.') + '</div>';
                }
            });
        }

        // Birebir mesaj gönder
        const chatForm = $('apex-cp-chat-form');
        if (chatForm) {
            chatForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const inp = $('cp-chat-input');
                const text = (inp.value || '').trim();
                if (!text) return;
                inp.disabled = true;
                const r = await fetch(REST + 'coach/message', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': NONCE },
                    body: JSON.stringify({ student_id: currentId, message: text }),
                });
                const j = await r.json().catch(() => ({}));
                inp.disabled = false;
                if (r.ok && j.success && j.item) {
                    DATA[currentId].messages.push(j.item);
                    renderChat(DATA[currentId].messages);
                    inp.value = '';
                    fb.innerHTML = '<div class="apex-cp-alert apex-cp-alert-ok">✓ Mesaj öğrencinin üye paneline iletildi.</div>';
                } else {
                    fb.innerHTML = '<div class="apex-cp-alert apex-cp-alert-err">⚠ ' + esc(j.message || 'Gönderilemedi.') + '</div>';
                }
            });
        }

        if (currentId && DATA[currentId]) {
            selectStudent(currentId);
        } else {
            renderAllVisuals();
        }
    })();
    </script>
    <?php
}
