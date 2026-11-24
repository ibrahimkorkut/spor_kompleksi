<?php
/**
 * APEX ARENA — Grafiksel Koç Yönetim Paneli (v2.0.0)
 * Sadece koçların kendi öğrencilerini (ve yöneticinin tüm koçları) görebildiği
 * 5 kartlı analitik grafik dashboard + Beslenme Takvimi + Kişisel Öneri + Birebir Mesajlaşma
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
 * Durum etiketi meta bilgileri
 */
function apex_coach_status_meta( $status ) {
    $map = [
        'hedefte' => [ 'label' => 'Hedefte',       'color' => '#30d158', 'bg' => 'rgba(48,209,88,0.16)' ],
        'dikkat'  => [ 'label' => 'Dikkat',        'color' => '#f59e0b', 'bg' => 'rgba(245,158,11,0.16)' ],
        'yeni'    => [ 'label' => 'Yeni Başladı',  'color' => '#38bdf8', 'bg' => 'rgba(56,189,248,0.16)' ],
        'riskli'  => [ 'label' => 'Yoğun Takip',   'color' => '#e31837', 'bg' => 'rgba(227,24,55,0.18)' ],
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

    if ( $coach_filter > 0 ) {
        $students = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$t_st} WHERE coach_user_id = %d ORDER BY id ASC", $coach_filter ) );
    } else {
        $students = $wpdb->get_results( "SELECT * FROM {$t_st} ORDER BY id ASC" );
    }

    // Her öğrenci için beslenme planı ve mesaj geçmişini yükle (tek seferde JS çalışma alanına aktar)
    $payload_map = [];
    $status_counts = [ 'hedefte' => 0, 'dikkat' => 0, 'yeni' => 0, 'riskli' => 0 ];
    foreach ( $students as $st ) {
        if ( isset( $status_counts[ $st->status ] ) ) {
            $status_counts[ $st->status ]++;
        }
        $nut   = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t_nut} WHERE student_id = %d LIMIT 1", $st->id ) );
        $meals = $nut ? ( json_decode( (string) $nut->meals_json, true ) ?: [] ) : [];
        $msgs  = $wpdb->get_results( $wpdb->prepare( "SELECT sender_role, sender_name, message, DATE_FORMAT(created_at, '%%d.%%m %%H:%%i') AS time FROM {$t_msg} WHERE student_id = %d ORDER BY id ASC LIMIT 50", $st->id ) );

        $payload_map[ $st->id ] = [
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
    }

    $total_st = max( 1, count( $students ) );
    $avg_prog = count( $students ) ? round( array_sum( array_map( fn( $x ) => (int) $x->progress_pct, $students ) ) / count( $students ) ) : 0;
    $avg_adh  = count( $students ) ? round( array_sum( array_map( fn( $x ) => (int) $x->adherence_pct, $students ) ) / count( $students ) ) : 0;

    // SVG Pie Chart dilimleri (saf çevrimdışı vektör)
    $pie_slices = [
        [ 'key' => 'hedefte', 'label' => 'Hedefte',      'color' => '#30d158', 'val' => $status_counts['hedefte'] ],
        [ 'key' => 'dikkat',  'label' => 'Dikkat',       'color' => '#f59e0b', 'val' => $status_counts['dikkat'] ],
        [ 'key' => 'yeni',    'label' => 'Yeni Başladı', 'color' => '#6366f1', 'val' => $status_counts['yeni'] ],
        [ 'key' => 'riskli',  'label' => 'Yoğun Takip',  'color' => '#e31837', 'val' => $status_counts['riskli'] ],
    ];
    $pie_paths = [];
    $acc_angle = -90.0;
    $cx = 86; $cy = 86; $r = 74;
    foreach ( $pie_slices as $sl ) {
        if ( $sl['val'] <= 0 ) continue;
        $sweep = ( $sl['val'] / $total_st ) * 360.0;
        if ( $sweep >= 359.9 ) {
            $pie_paths[] = '<circle cx="' . $cx . '" cy="' . $cy . '" r="' . $r . '" fill="' . esc_attr( $sl['color'] ) . '" />';
            break;
        }
        $a1 = deg2rad( $acc_angle );
        $a2 = deg2rad( $acc_angle + $sweep );
        $x1 = $cx + $r * cos( $a1 );
        $y1 = $cy + $r * sin( $a1 );
        $x2 = $cx + $r * cos( $a2 );
        $y2 = $cy + $r * sin( $a2 );
        $large = $sweep > 180 ? 1 : 0;
        $d = sprintf( 'M %.1f %.1f L %.1f %.1f A %d %d 0 %d 1 %.1f %.1f Z', $cx, $cy, $x1, $y1, $r, $r, $large, $x2, $y2 );
        $pie_paths[] = '<path d="' . $d . '" fill="' . esc_attr( $sl['color'] ) . '" stroke="#14161d" stroke-width="2" />';
        $acc_angle += $sweep;
    }

    // Area Chart verisi (öğrenci ilerleme & uyum eğrisi)
    $chart_pts = array_slice( $students, 0, 7 );
    $n_pts     = max( 2, count( $chart_pts ) );
    $step_x    = 260 / max( 1, $n_pts - 1 );
    $prog_poly = [];
    $adh_poly  = [];
    foreach ( $chart_pts as $i => $st ) {
        $x = 24 + $i * $step_x;
        $yp = 138 - ( (int) $st->progress_pct * 1.15 );
        $ya = 138 - ( (int) $st->adherence_pct * 1.15 );
        $prog_poly[] = [ $x, max( 18, $yp ), mb_substr( $st->student_name, 0, 5 ) ];
        $adh_poly[]  = [ $x, max( 18, $ya ) ];
    }
    $make_smooth_area = function( $pts ) {
        if ( count( $pts ) < 2 ) return '';
        $d = sprintf( 'M %.1f 140 L %.1f %.1f', $pts[0][0], $pts[0][0], $pts[0][1] );
        for ( $i = 0; $i < count( $pts ) - 1; $i++ ) {
            $p0 = $pts[ $i ];
            $p1 = $pts[ $i + 1 ];
            $mx = ( $p0[0] + $p1[0] ) / 2;
            $d .= sprintf( ' C %.1f %.1f, %.1f %.1f, %.1f %.1f', $mx, $p0[1], $mx, $p1[1], $p1[0], $p1[1] );
        }
        $last = end( $pts );
        $d .= sprintf( ' L %.1f 140 Z', $last[0] );
        return $d;
    };

    $rest_url = esc_url_raw( rest_url( 'apex/v1/' ) );
    $nonce    = wp_create_nonce( 'wp_rest' );
    $first_id = ! empty( $students ) ? (int) $students[0]->id : 0;
    ?>
    <style>
        .apex-cp-wrap { max-width: 1320px; margin: 16px 20px 40px 0; font-family: -apple-system, BlinkMacSystemFont, 'Plus Jakarta Sans', 'Segoe UI', sans-serif; color: #f5f5f7; }
        .apex-cp-wrap * { box-sizing: border-box; }
        .apex-cp-banner { background: linear-gradient(135deg, #101218 0%, #19131c 60%, #240c14 100%); border: 1px solid rgba(255,255,255,0.1); border-radius: 18px; padding: 24px 28px; display: flex; align-items: center; justify-content: space-between; gap: 18px; flex-wrap: wrap; margin-bottom: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.28); }
        .apex-cp-eyebrow { display: inline-flex; align-items: center; gap: 8px; font-size: 11px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: #a1a1aa; margin-bottom: 6px; }
        .apex-cp-dot { width: 8px; height: 8px; border-radius: 50%; background: #30d158; box-shadow: 0 0 8px #30d158; }
        .apex-cp-h1 { margin: 0; font-size: 24px; font-weight: 800; color: #fff; letter-spacing: -0.02em; }
        .apex-cp-sub { margin: 5px 0 0; color: #a1a1aa; font-size: 13.5px; }
        .apex-cp-pills { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .apex-cp-btn { display: inline-flex; align-items: center; gap: 7px; padding: 8px 16px; border-radius: 999px; font-size: 12.5px; font-weight: 700; text-decoration: none; border: 1px solid rgba(255,255,255,0.16); background: rgba(255,255,255,0.06); color: #fff; cursor: pointer; transition: 0.2s ease; }
        .apex-cp-btn:hover { background: rgba(255,255,255,0.14); color: #fff; }
        .apex-cp-btn-red { background: #e31837; border-color: #e31837; }
        .apex-cp-btn-red:hover { background: #f22947; }
        .apex-cp-select { background: #14161d; color: #fff; border: 1px solid rgba(255,255,255,0.2); border-radius: 999px; padding: 6px 14px; font-size: 12.5px; font-weight: 600; }

        /* 5-Card Reference Analytics Grid (Top 3 Charts + Bottom 2 Progress/Table) */
        .apex-cp-top3 { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; margin-bottom: 18px; }
        .apex-cp-bot2 { display: grid; grid-template-columns: minmax(0, 0.85fr) minmax(0, 1.45fr); gap: 18px; margin-bottom: 22px; }
        .apex-cp-card { background: #14161d; border: 1px solid rgba(255,255,255,0.09); border-radius: 16px; padding: 20px 22px; box-shadow: 0 14px 36px rgba(0,0,0,0.22); min-width: 0; }
        .apex-cp-card-title { font-size: 11.5px; font-weight: 800; letter-spacing: 0.09em; text-transform: uppercase; color: #9ca3af; margin: 0 0 14px; display: flex; justify-content: space-between; align-items: center; }
        .apex-cp-card-title strong { color: #f5f5f7; font-size: 13px; letter-spacing: 0; text-transform: none; }
        .apex-cp-svg { width: 100%; height: auto; display: block; }
        .apex-cp-pie-wrap { display: flex; align-items: center; justify-content: space-around; gap: 14px; flex-wrap: wrap; }
        .apex-cp-legend { display: grid; gap: 8px; font-size: 12.5px; }
        .apex-cp-legend-row { display: flex; align-items: center; gap: 8px; color: #d4d4d8; }
        .apex-cp-swatch { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }

        /* Progress Bars Card */
        .apex-cp-bars { display: grid; gap: 14px; }
        .apex-cp-bar-item { display: grid; grid-template-columns: 112px minmax(0, 1fr) 40px; align-items: center; gap: 10px; font-size: 12.5px; }
        .apex-cp-bar-track { height: 8px; background: rgba(255,255,255,0.08); border-radius: 99px; overflow: hidden; }
        .apex-cp-bar-fill { height: 100%; border-radius: 99px; }

        /* Student Status Table (Reference Photo Style) */
        .apex-cp-table-wrap { overflow-x: auto; }
        .apex-cp-table { width: 100%; border-collapse: collapse; min-width: 520px; }
        .apex-cp-table th { text-align: left; font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase; color: #9ca3af; padding: 10px 10px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .apex-cp-table td { padding: 11px 10px; border-bottom: 1px solid rgba(255,255,255,0.06); font-size: 13px; color: #e4e4e7; vertical-align: middle; }
        .apex-cp-table tr.is-selected { background: rgba(227,24,55,0.12); }
        .apex-cp-table tbody tr:hover { background: rgba(255,255,255,0.04); cursor: pointer; }
        .apex-cp-badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; }
        .apex-cp-trend-up { color: #30d158; font-weight: 800; }
        .apex-cp-trend-down { color: #e31837; font-weight: 800; }

        /* Coach Action Workspace */
        .apex-cp-studio { background: #14161d; border: 1px solid rgba(227,24,55,0.34); border-radius: 18px; padding: 24px 26px; box-shadow: 0 22px 54px rgba(0,0,0,0.34); }
        .apex-cp-studio-head { display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; padding-bottom: 16px; border-bottom: 1px solid rgba(255,255,255,0.09); margin-bottom: 18px; }
        .apex-cp-tabs { display: flex; gap: 8px; flex-wrap: wrap; }
        .apex-cp-tab { padding: 9px 16px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.14); background: rgba(255,255,255,0.04); color: #d4d4d8; font-weight: 700; font-size: 13px; cursor: pointer; }
        .apex-cp-tab.is-active { background: #e31837; border-color: #e31837; color: #fff; }
        .apex-cp-pane { display: none; }
        .apex-cp-pane.is-active { display: block; }
        .apex-cp-form-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
        .apex-cp-field { display: flex; flex-direction: column; gap: 6px; }
        .apex-cp-field-full { grid-column: 1 / -1; }
        .apex-cp-field label { font-size: 12px; font-weight: 700; color: #d4d4d8; }
        .apex-cp-field input, .apex-cp-field select, .apex-cp-field textarea { width: 100%; background: #0a0b0f; border: 1px solid rgba(255,255,255,0.16); border-radius: 10px; padding: 10px 12px; color: #fff; font-size: 13px; }
        .apex-cp-field textarea { min-height: 76px; resize: vertical; }
        .apex-cp-chat-box { background: #0a0b0f; border: 1px solid rgba(255,255,255,0.1); border-radius: 14px; padding: 16px; max-height: 290px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; margin-bottom: 12px; }
        .apex-cp-msg { max-width: 78%; padding: 10px 14px; border-radius: 14px; font-size: 13px; line-height: 1.5; }
        .apex-cp-msg-coach { align-self: flex-end; background: #e31837; color: #fff; border-bottom-right-radius: 4px; }
        .apex-cp-msg-student { align-self: flex-start; background: #1d202a; color: #f5f5f7; border: 1px solid rgba(255,255,255,0.08); border-bottom-left-radius: 4px; }
        .apex-cp-msg small { display: block; opacity: 0.72; font-size: 11px; margin-top: 4px; }
        .apex-cp-chat-form { display: flex; gap: 10px; }
        .apex-cp-chat-form input { flex: 1; background: #0a0b0f; border: 1px solid rgba(255,255,255,0.18); border-radius: 999px; padding: 10px 16px; color: #fff; font-size: 13px; }
        .apex-cp-alert { margin-top: 12px; padding: 10px 14px; border-radius: 10px; font-size: 13px; font-weight: 600; }
        .apex-cp-alert-ok { background: rgba(48,209,88,0.15); border: 1px solid rgba(48,209,88,0.45); color: #86efac; }
        .apex-cp-alert-err { background: rgba(227,24,55,0.16); border: 1px solid rgba(227,24,55,0.45); color: #fda4af; }

        @media (max-width: 1120px) {
            .apex-cp-top3, .apex-cp-bot2 { grid-template-columns: 1fr; }
            .apex-cp-form-grid { grid-template-columns: 1fr; }
        }
    </style>

    <div class="apex-cp-wrap">
        <div class="apex-cp-banner">
            <div>
                <div class="apex-cp-eyebrow">
                    <span class="apex-cp-dot"></span>
                    <span><?php echo $is_admin ? 'YÖNETİCİ DENETİM GÖRÜNÜMÜ · TÜM KOÇLAR' : 'ÖZEL KOÇ PORTALI · SADECE KENDİ ÖĞRENCİLERİNİZ'; ?></span>
                </div>
                <h1 class="apex-cp-h1">APEX Koç Grafik &amp; Öğrenci Yönetim Paneli</h1>
                <p class="apex-cp-sub">Hoş geldin <strong><?php echo esc_html( $cu->display_name ); ?></strong> · Aktif Öğrenci: <strong><?php echo count( $students ); ?></strong> · Ort. İlerleme: <strong>%<?php echo esc_html( $avg_prog ); ?></strong> · Ort. Program Uyumu: <strong>%<?php echo esc_html( $avg_adh ); ?></strong></p>
            </div>
            <div class="apex-cp-pills">
                <?php if ( $is_admin && ! empty( $coaches ) ) : ?>
                <form method="get" style="display:inline-flex;gap:8px;align-items:center;">
                    <input type="hidden" name="page" value="apex-coach-portal">
                    <select name="coach_id" class="apex-cp-select" onchange="this.form.submit()">
                        <option value="0" <?php selected( $coach_filter, 0 ); ?>>Tüm Koçlar (<?php echo count( $students ); ?>)</option>
                        <?php foreach ( $coaches as $co ) : ?>
                            <option value="<?php echo esc_attr( $co->ID ); ?>" <?php selected( $coach_filter, (int) $co->ID ); ?>>Koç: <?php echo esc_html( $co->display_name ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <?php endif; ?>
                <a href="<?php echo esc_url( home_url( '/uyelik/?panel=1' ) ); ?>" class="apex-cp-btn" target="_blank">Üye Görünümü ↗</a>
                <a href="<?php echo esc_url( wp_logout_url( wp_login_url( admin_url( 'admin.php?page=apex-coach-portal' ) ) ) ); ?>" class="apex-cp-btn apex-cp-btn-red">Güvenli Çıkış</a>
            </div>
        </div>

        <!-- ÜST SATIR: 3 GRAFİK KARTI (BAR · AREA/LINE · PIE) -->
        <div class="apex-cp-top3">
            <!-- 1. Grouped Bar Chart -->
            <div class="apex-cp-card">
                <div class="apex-cp-card-title">
                    <span>SEANS KARŞILAŞTIRMASI (BAR)</span>
                    <strong>Planlanan vs Tamamlanan</strong>
                </div>
                <svg class="apex-cp-svg" viewBox="0 0 320 175" role="img" aria-label="Öğrenci bazlı planlanan ve tamamlanan seans sütun grafiği">
                    <line x1="26" y1="20" x2="26" y2="144" stroke="rgba(255,255,255,0.12)" />
                    <line x1="26" y1="144" x2="306" y2="144" stroke="rgba(255,255,255,0.15)" />
                    <?php
                    $bar_items = array_slice( $students, 0, 6 );
                    $max_s     = max( 12, ...array_map( fn( $x ) => max( (int) $x->sessions_planned, (int) $x->sessions_done ), $bar_items ?: [ (object) [ 'sessions_planned' => 12, 'sessions_done' => 8 ] ] ) );
                    $slot_w    = 270 / max( 1, count( $bar_items ) );
                    foreach ( $bar_items as $bi => $bst ) :
                        $bx = 34 + $bi * $slot_w;
                        $h1 = round( ( (int) $bst->sessions_planned / $max_s ) * 114 );
                        $h2 = round( ( (int) $bst->sessions_done / $max_s ) * 114 );
                    ?>
                        <rect x="<?php echo esc_attr( $bx ); ?>" y="<?php echo esc_attr( 144 - $h1 ); ?>" width="13" height="<?php echo esc_attr( $h1 ); ?>" rx="3" fill="#6366f1" />
                        <rect x="<?php echo esc_attr( $bx + 15 ); ?>" y="<?php echo esc_attr( 144 - $h2 ); ?>" width="13" height="<?php echo esc_attr( $h2 ); ?>" rx="3" fill="#e31837" />
                        <text x="<?php echo esc_attr( $bx + 14 ); ?>" y="160" text-anchor="middle" fill="#9ca3af" font-size="9.5"><?php echo esc_html( mb_substr( $bst->student_name, 0, 5 ) ); ?></text>
                    <?php endforeach; ?>
                </svg>
                <div style="display:flex;gap:14px;font-size:11px;color:#9ca3af;margin-top:4px;">
                    <span><i style="display:inline-block;width:9px;height:9px;background:#6366f1;border-radius:2px;margin-right:4px;"></i>Planlanan</span>
                    <span><i style="display:inline-block;width:9px;height:9px;background:#e31837;border-radius:2px;margin-right:4px;"></i>Tamamlanan</span>
                </div>
            </div>

            <!-- 2. Smooth Area / Line Chart -->
            <div class="apex-cp-card">
                <div class="apex-cp-card-title">
                    <span>GELİŞİM &amp; UYUM TRENDİ (AREA)</span>
                    <strong>Ort. %<?php echo esc_html( $avg_prog ); ?></strong>
                </div>
                <svg class="apex-cp-svg" viewBox="0 0 310 175" role="img" aria-label="Öğrenci gelişim ve program uyumu alan grafiği">
                    <defs>
                        <linearGradient id="apexGradGold" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#f59e0b" stop-opacity="0.72" />
                            <stop offset="100%" stop-color="#f59e0b" stop-opacity="0.06" />
                        </linearGradient>
                        <linearGradient id="apexGradIndigo" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#6366f1" stop-opacity="0.82" />
                            <stop offset="100%" stop-color="#6366f1" stop-opacity="0.1" />
                        </linearGradient>
                    </defs>
                    <line x1="24" y1="25" x2="294" y2="25" stroke="rgba(255,255,255,0.06)" stroke-dasharray="3 3" />
                    <line x1="24" y1="82" x2="294" y2="82" stroke="rgba(255,255,255,0.06)" stroke-dasharray="3 3" />
                    <line x1="24" y1="140" x2="294" y2="140" stroke="rgba(255,255,255,0.16)" />
                    <?php if ( count( $adh_poly ) >= 2 ) : ?>
                        <path d="<?php echo esc_attr( $make_smooth_area( $adh_poly ) ); ?>" fill="url(#apexGradGold)" stroke="#f59e0b" stroke-width="2" />
                        <path d="<?php echo esc_attr( $make_smooth_area( $prog_poly ) ); ?>" fill="url(#apexGradIndigo)" stroke="#818cf8" stroke-width="2.2" />
                        <?php foreach ( $prog_poly as $pp ) : ?>
                            <circle cx="<?php echo esc_attr( round( $pp[0], 1 ) ); ?>" cy="<?php echo esc_attr( round( $pp[1], 1 ) ); ?>" r="3.4" fill="#fff" stroke="#6366f1" stroke-width="2" />
                            <text x="<?php echo esc_attr( round( $pp[0], 1 ) ); ?>" y="158" text-anchor="middle" fill="#9ca3af" font-size="9"><?php echo esc_html( $pp[2] ); ?></text>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </svg>
                <div style="display:flex;gap:14px;font-size:11px;color:#9ca3af;margin-top:4px;">
                    <span><i style="display:inline-block;width:9px;height:9px;background:#f59e0b;border-radius:2px;margin-right:4px;"></i>Beslenme/Program Uyumu</span>
                    <span><i style="display:inline-block;width:9px;height:9px;background:#6366f1;border-radius:2px;margin-right:4px;"></i>Hedef İlerleme</span>
                </div>
            </div>

            <!-- 3. Pie Chart -->
            <div class="apex-cp-card">
                <div class="apex-cp-card-title">
                    <span>ÖĞRENCİ DURUM DAĞILIMI (PIE)</span>
                    <strong><?php echo count( $students ); ?> Öğrenci</strong>
                </div>
                <div class="apex-cp-pie-wrap">
                    <svg width="172" height="172" viewBox="0 0 172 172" role="img" aria-label="Öğrenci durum dağılımı pasta grafiği">
                        <?php echo implode( '', $pie_paths ); ?>
                        <circle cx="86" cy="86" r="32" fill="#14161d" />
                        <text x="86" y="83" text-anchor="middle" fill="#fff" font-size="16" font-weight="800"><?php echo count( $students ); ?></text>
                        <text x="86" y="98" text-anchor="middle" fill="#9ca3af" font-size="9">ÖĞRENCİ</text>
                    </svg>
                    <div class="apex-cp-legend">
                        <?php foreach ( $pie_slices as $sl ) : ?>
                        <div class="apex-cp-legend-row">
                            <span class="apex-cp-swatch" style="background:<?php echo esc_attr( $sl['color'] ); ?>;"></span>
                            <span><?php echo esc_html( $sl['label'] ); ?>: <strong><?php echo esc_html( $sl['val'] ); ?></strong></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ALT SATIR: SOL İLERLEME BARLARI + SAĞ ÖĞRENCİ DURUM TABLOSU -->
        <div class="apex-cp-bot2">
            <div class="apex-cp-card">
                <div class="apex-cp-card-title">
                    <span>PROGRAM UYUM ORANLARI</span>
                    <strong>Anlık Takip</strong>
                </div>
                <div class="apex-cp-bars">
                    <?php foreach ( $students as $st ) :
                        $sm = apex_coach_status_meta( $st->status );
                    ?>
                    <div class="apex-cp-bar-item">
                        <strong style="color:#f5f5f7;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo esc_html( $st->student_name ); ?></strong>
                        <div class="apex-cp-bar-track">
                            <div class="apex-cp-bar-fill" style="width:<?php echo esc_attr( $st->adherence_pct ); ?>%;background:<?php echo esc_attr( $sm['color'] ); ?>;"></div>
                        </div>
                        <span style="text-align:right;font-weight:700;color:<?php echo esc_attr( $sm['color'] ); ?>;">%<?php echo esc_html( $st->adherence_pct ); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="apex-cp-card">
                <div class="apex-cp-card-title">
                    <span>ÖĞRENCİ DURUM TABLOSU</span>
                    <strong>Düzenlemek için bir öğrenci seçin</strong>
                </div>
                <div class="apex-cp-table-wrap">
                    <table class="apex-cp-table" id="apex-cp-student-table">
                        <thead>
                            <tr><th>#</th><th>Öğrenci &amp; Hedef</th><th>Durum</th><th>İlerleme</th><th>İşlem</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ( $students as $idx => $st ) :
                            $sm = apex_coach_status_meta( $st->status );
                            $is_first = ( (int) $st->id === $first_id );
                        ?>
                            <tr data-select-student="<?php echo esc_attr( $st->id ); ?>" class="<?php echo $is_first ? 'is-selected' : ''; ?>">
                                <td><strong><?php echo esc_html( $idx + 1 ); ?></strong></td>
                                <td>
                                    <strong style="color:#fff;"><?php echo esc_html( $st->student_name ); ?></strong>
                                    <div style="font-size:11.5px;color:#9ca3af;"><?php echo esc_html( $st->goal ); ?> · <?php echo esc_html( $st->current_weight ); ?>→<?php echo esc_html( $st->target_weight ); ?> kg</div>
                                </td>
                                <td>
                                    <span class="apex-cp-badge" style="background:<?php echo esc_attr( $sm['bg'] ); ?>;color:<?php echo esc_attr( $sm['color'] ); ?>;">
                                        <?php echo esc_html( $sm['label'] ); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="<?php echo $st->progress_trend === 'down' ? 'apex-cp-trend-down' : 'apex-cp-trend-up'; ?>">
                                        %<?php echo esc_html( $st->progress_pct ); ?> <?php echo $st->progress_trend === 'down' ? '↓' : '↑'; ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="apex-cp-btn" style="padding:5px 12px;font-size:11.5px;" data-select-student="<?php echo esc_attr( $st->id ); ?>">Beslenme &amp; Mesaj</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
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
                                <option value="hedefte">Hedefte (Yeşil)</option>
                                <option value="dikkat">Dikkat Gerekiyor (Turuncu)</option>
                                <option value="yeni">Yeni Başladı (Mavi)</option>
                                <option value="riskli">Yoğun Takip / Riskli (Kırmızı)</option>
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
        const REST = <?php echo wp_json_encode( $rest_url ); ?>;
        const NONCE = <?php echo wp_json_encode( $nonce ); ?>;
        let currentId = <?php echo (int) $first_id; ?>;

        const $ = (id) => document.getElementById(id);
        const fb = $('cp-feedback');

        function renderChat(msgs) {
            const box = $('cp-chat-list');
            if (!box) return;
            box.innerHTML = '';
            if (!msgs || !msgs.length) {
                box.innerHTML = '<div style="color:#9ca3af;font-size:13px;text-align:center;padding:24px;">Henüz birebir mesaj yok. İlk mesajı aşağıdan gönderebilirsiniz.</div>';
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

        function loadStudent(id) {
            const st = DATA[id];
            if (!st) return;
            currentId = id;
            $('cp-student-id').value = id;
            $('cp-active-name').textContent = st.student_name;
            $('cp-active-meta').textContent = `Koç: ${st.coach_name} · Hedef: ${st.goal} · Seans: ${st.sessions_done}/${st.sessions_planned} · E-posta: ${st.student_email}`;
            $('cp-status').value = st.status;
            $('cp-progress').value = st.progress_pct;
            $('cp-adherence').value = st.adherence_pct;
            $('cp-trend').value = st.progress_trend;
            $('cp-cur-weight').value = st.current_weight;
            $('cp-tar-weight').value = st.target_weight;
            $('cp-advice').value = st.coach_advice;

            $('cp-kcal').value = st.nutrition.daily_kcal;
            $('cp-pro').value = st.nutrition.protein_g;
            $('cp-carb').value = st.nutrition.carbs_g;
            $('cp-fat').value = st.nutrition.fat_g;
            $('cp-water').value = st.nutrition.water_l;
            $('cp-m-sabah').value = st.nutrition.sabah;
            $('cp-m-ara1').value = st.nutrition.ara1;
            $('cp-m-ogle').value = st.nutrition.ogle;
            $('cp-m-ant').value = st.nutrition.ant_sonra;
            $('cp-m-aksam').value = st.nutrition.aksam;
            $('cp-m-notes').value = st.nutrition.weekly_notes;

            renderChat(st.messages);
            document.querySelectorAll('#apex-cp-student-table tbody tr').forEach((tr) => {
                tr.classList.toggle('is-selected', Number(tr.getAttribute('data-select-student')) === Number(id));
            });
            if (fb) fb.innerHTML = '';
        }

        document.querySelectorAll('[data-select-student]').forEach((el) => {
            el.addEventListener('click', () => {
                loadStudent(Number(el.getAttribute('data-select-student')));
            });
        });

        document.querySelectorAll('[data-cp-tab]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const target = btn.getAttribute('data-cp-tab');
                document.querySelectorAll('[data-cp-tab]').forEach((b) => b.classList.toggle('is-active', b === btn));
                document.querySelectorAll('[data-cp-pane]').forEach((p) => p.classList.toggle('is-active', p.getAttribute('data-cp-pane') === target));
            });
        });

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
                    fb.innerHTML = '<div class="apex-cp-alert apex-cp-alert-ok">✓ ' + j.message + '</div>';
                } else {
                    fb.innerHTML = '<div class="apex-cp-alert apex-cp-alert-err">⚠ ' + (j.message || 'Kaydedilemedi.') + '</div>';
                }
            });
        }

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
                    fb.innerHTML = '<div class="apex-cp-alert apex-cp-alert-err">⚠ ' + (j.message || 'Gönderilemedi.') + '</div>';
                }
            });
        }

        if (currentId && DATA[currentId]) loadStudent(currentId);
    })();
    </script>
    <?php
}
