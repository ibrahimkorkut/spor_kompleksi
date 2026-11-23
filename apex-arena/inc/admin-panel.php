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

add_action( 'admin_menu', function() {
    add_menu_page(
        'APEX ARENA Yönetim',
        'APEX ARENA',
        'manage_options',
        'apex-arena-dashboard',
        'apex_admin_dashboard_page',
        'dashicons-superhero-alt',
        3
    );
    add_submenu_page( 'apex-arena-dashboard', 'Genel Bakış', 'Genel Bakış', 'manage_options', 'apex-arena-dashboard', 'apex_admin_dashboard_page' );
    add_submenu_page( 'apex-arena-dashboard', 'Salon Rezervasyonları', 'Salon Rezervasyonları', 'manage_options', 'apex-court-bookings', 'apex_admin_court_bookings_page' );
    add_submenu_page( 'apex-arena-dashboard', 'PT Randevuları', 'PT Randevuları', 'manage_options', 'apex-pt-appointments', 'apex_admin_pt_appointments_page' );
    add_submenu_page( 'apex-arena-dashboard', 'Üyelik Başvuruları', 'Üyelikler', 'manage_options', 'apex-memberships', 'apex_admin_memberships_page' );
    add_submenu_page( 'apex-arena-dashboard', 'AI Chatbot & Tesis Ayarları', 'AI & Ayarlar', 'manage_options', 'apex-settings', 'apex_admin_settings_page' );
} );

/**
 * Genel Bakış Paneli
 */
function apex_admin_dashboard_page() {
    global $wpdb;
    $court_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}apex_court_bookings WHERE status != 'cancelled'" );
    $ref_count   = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}apex_court_bookings WHERE needs_referee = 1 AND status != 'cancelled'" );
    $pt_count    = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}apex_pt_appointments WHERE status != 'cancelled'" );
    $mem_count   = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}apex_membership_applications WHERE status = 'active'" );
    $chat_count  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}apex_chatbot_logs" );
    $revenue     = (int) $wpdb->get_var( "SELECT COALESCE(SUM(total_price),0) FROM {$wpdb->prefix}apex_court_bookings WHERE status != 'cancelled'" )
                 + (int) $wpdb->get_var( "SELECT COALESCE(SUM(price),0) FROM {$wpdb->prefix}apex_pt_appointments WHERE status != 'cancelled'" )
                 + (int) $wpdb->get_var( "SELECT COALESCE(SUM(price),0) FROM {$wpdb->prefix}apex_membership_applications WHERE status = 'active'" );
    $ai_mode = apex_get_effective_ai_mode();
    ?>
    <div class="wrap">
        <h1 style="font-weight:800;">APEX ARENA — Spor Kompleksi Yönetim Merkezi</h1>
        <p>Tüm salon rezervasyonları (hakem talepleri dahil), PT randevuları, üyelikler ve Hibrit AI Spor Koçu canlı metrikleri.</p>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin:20px 0;">
            <div style="background:#13161D;color:#fff;padding:20px;border-radius:10px;border-left:4px solid #E31837;">
                <div style="font-size:12px;color:#94A3B8;text-transform:uppercase;">Aktif Salon Rezervasyonu</div>
                <div style="font-size:32px;font-weight:800;margin-top:6px;"><?php echo esc_html( $court_count ); ?></div>
                <div style="font-size:12px;color:#B8FF00;">Hakemli Maç Talebi: <?php echo esc_html( $ref_count ); ?></div>
            </div>
            <div style="background:#13161D;color:#fff;padding:20px;border-radius:10px;border-left:4px solid #B8FF00;">
                <div style="font-size:12px;color:#94A3B8;text-transform:uppercase;">Aktif PT Randevusu</div>
                <div style="font-size:32px;font-weight:800;margin-top:6px;"><?php echo esc_html( $pt_count ); ?></div>
                <div style="font-size:12px;color:#94A3B8;">Bireysel & Grupsal</div>
            </div>
            <div style="background:#13161D;color:#fff;padding:20px;border-radius:10px;border-left:4px solid #38BDF8;">
                <div style="font-size:12px;color:#94A3B8;text-transform:uppercase;">Aktif Üyelikler</div>
                <div style="font-size:32px;font-weight:800;margin-top:6px;"><?php echo esc_html( $mem_count ); ?></div>
                <div style="font-size:12px;color:#94A3B8;">Dijital QR Kartlı</div>
            </div>
            <div style="background:#13161D;color:#fff;padding:20px;border-radius:10px;border-left:4px solid #F59E0B;">
                <div style="font-size:12px;color:#94A3B8;text-transform:uppercase;">Toplam İşlem Hacmi</div>
                <div style="font-size:28px;font-weight:800;margin-top:6px;"><?php echo esc_html( apex_format_tl( $revenue ) ); ?></div>
                <div style="font-size:12px;color:#B8FF00;">AI Sohbet: <?php echo esc_html( $chat_count ); ?> · Mod: <?php echo esc_html( $ai_mode ); ?></div>
            </div>
        </div>

        <p>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=apex-court-bookings' ) ); ?>" class="button button-primary">Salon Rezervasyonlarını Yönet</a>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=apex-pt-appointments' ) ); ?>" class="button">PT Randevuları</a>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=apex-settings' ) ); ?>" class="button">AI Chatbot API & Tesis Ayarları</a>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="button" target="_blank">Canlı Siteyi Aç ↗</a>
        </p>
    </div>
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
        $new = [
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
        ];
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
