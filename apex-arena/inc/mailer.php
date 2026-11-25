<?php
/**
 * APEX ARENA — Gmail SMTP, E-posta Bildirimleri ve Otomatik Hatırlatma Motoru (v2.1.0)
 *
 * Sıfır eklenti bağımlılığı ile WordPress'in dahili PHPMailer motorunu Gmail SMTP'ye bağlar,
 * kayıt (Hoş Geldin + Dijital QR Kart), salon/PT rezervasyon onayı ve 24 saat kala otomatik
 * hatırlatma e-postalarını HTML şablonla gönderir ve tüm gönderimleri MySQL'de kaydeder.
 *
 * @package ApexArena
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Son wp_mail() hatasını geçici olarak yakala (tanı ve loglama için)
 */
$GLOBALS['apex_last_mail_error'] = '';

add_action( 'wp_mail_failed', function( $wp_error ) {
    if ( is_wp_error( $wp_error ) ) {
        $GLOBALS['apex_last_mail_error'] = $wp_error->get_error_message();
    }
} );

/**
 * Gmail SMTP aktifken WordPress'in varsayılan wordpress@localhost gönderen adresini Gmail ile değiştir
 */
add_filter( 'wp_mail_from', function( $from ) {
    if ( function_exists( 'apex_is_gmail_smtp_ready' ) && apex_is_gmail_smtp_ready() ) {
        $s = apex_get_settings();
        if ( ! empty( $s['smtp_user'] ) && is_email( $s['smtp_user'] ) ) {
            return trim( (string) $s['smtp_user'] );
        }
    }
    return $from;
}, 20 );

add_filter( 'wp_mail_from_name', function( $name ) {
    if ( function_exists( 'apex_is_gmail_smtp_ready' ) && apex_is_gmail_smtp_ready() ) {
        $s = apex_get_settings();
        if ( ! empty( $s['smtp_from_name'] ) ) {
            return (string) $s['smtp_from_name'];
        }
    }
    return $name;
}, 20 );

/**
 * Gmail SMTP Aktifse WordPress PHPMailer'ı Gmail'e Yönlendir
 */
add_action( 'phpmailer_init', function( $phpmailer ) {
    $s = apex_get_settings();
    if ( empty( $s['smtp_enabled'] ) || empty( $s['smtp_user'] ) || empty( $s['smtp_pass'] ) ) {
        return;
    }

    $host = ! empty( $s['smtp_host'] ) ? $s['smtp_host'] : 'smtp.gmail.com';
    $port = ! empty( $s['smtp_port'] ) ? (int) $s['smtp_port'] : 587;
    $enc  = in_array( $s['smtp_encryption'] ?? 'tls', [ 'tls', 'ssl', '' ], true ) ? $s['smtp_encryption'] : 'tls';

    $phpmailer->isSMTP();
    $phpmailer->Host        = $host;
    $phpmailer->SMTPAuth    = true;
    $phpmailer->SMTPAutoTLS = true;
    $phpmailer->Port        = $port;
    $phpmailer->Username    = trim( (string) $s['smtp_user'] );
    $phpmailer->Password    = preg_replace( '/\s+/', '', (string) $s['smtp_pass'] );
    $phpmailer->SMTPSecure  = $enc;
    $phpmailer->CharSet     = 'UTF-8';
    $phpmailer->Timeout     = 15;

    $from_email = trim( (string) $s['smtp_user'] );
    $from_name  = ! empty( $s['smtp_from_name'] ) ? $s['smtp_from_name'] : 'APEX ARENA Spor Kompleksi';
    try {
        $phpmailer->setFrom( $from_email, $from_name, false );
        $phpmailer->Sender = $from_email;
    } catch ( Exception $e ) {
        // PHPMailer varsayılan adresle devam eder
    }
}, 20 );

/**
 * Gmail SMTP canlı yapılandırılmış mı?
 */
function apex_is_gmail_smtp_ready() {
    $s = apex_get_settings();
    return ! empty( $s['smtp_enabled'] ) && ! empty( $s['smtp_user'] ) && ! empty( $s['smtp_pass'] );
}

/**
 * E-posta şablon sarmalayıcı (Gmail, Apple Mail ve Outlook uyumlu karanlık APEX tasarımı)
 */
function apex_render_email_template( array $args ) {
    $s        = apex_get_settings();
    $badge    = $args['badge'] ?? 'APEX ARENA BİLDİRİM';
    $title    = $args['title'] ?? 'APEX ARENA';
    $intro    = $args['intro'] ?? '';
    $rows     = is_array( $args['rows'] ?? null ) ? $args['rows'] : [];
    $highlight= $args['highlight'] ?? '';
    $tips     = is_array( $args['tips'] ?? null ) ? $args['tips'] : [];
    $cta_text = $args['cta_text'] ?? 'Üye Paneline Git';
    $cta_url  = $args['cta_url'] ?? home_url( '/uyelik/?panel=1' );
    $footer_n = $args['footer_note'] ?? 'Bu e-posta APEX ARENA Spor Kompleksi otomasyon sistemi tarafından gönderilmiştir.';

    $rows_html = '';
    foreach ( $rows as $lbl => $val ) {
        $rows_html .= '<tr>'
            . '<td style="padding:10px 14px;border-bottom:1px solid #222530;color:#9CA3AF;font-size:13px;width:42%;">' . esc_html( $lbl ) . '</td>'
            . '<td style="padding:10px 14px;border-bottom:1px solid #222530;color:#FFFFFF;font-size:13.5px;font-weight:700;text-align:right;">' . esc_html( (string) $val ) . '</td>'
            . '</tr>';
    }

    $highlight_html = '';
    if ( $highlight ) {
        $highlight_html = '<div style="margin:20px 0;padding:16px 18px;border-radius:12px;background:#1A1419;border:1px solid #E31837;color:#F8FAFC;font-size:14px;line-height:1.55;">'
            . wp_kses_post( $highlight )
            . '</div>';
    }

    $tips_html = '';
    if ( ! empty( $tips ) ) {
        $items = '';
        foreach ( $tips as $t ) {
            $items .= '<li style="margin:6px 0;color:#D1D5DB;font-size:13.5px;line-height:1.5;">' . esc_html( $t ) . '</li>';
        }
        $tips_html = '<div style="margin:20px 0 6px;padding:16px 18px;border-radius:12px;background:#141720;border:1px solid #242938;">'
            . '<div style="font-size:11px;font-weight:800;letter-spacing:1.2px;text-transform:uppercase;color:#F4E268;margin-bottom:8px;">TESİS HATIRLATMALARI</div>'
            . '<ul style="margin:0;padding-left:18px;">' . $items . '</ul>'
            . '</div>';
    }

    $cta_html = '';
    if ( $cta_text && $cta_url ) {
        $cta_html = '<div style="text-align:center;margin:26px 0 8px;">'
            . '<a href="' . esc_url( $cta_url ) . '" style="display:inline-block;padding:13px 28px;border-radius:999px;background:#E31837;color:#FFFFFF;text-decoration:none;font-weight:800;font-size:13.5px;letter-spacing:0.6px;">'
            . esc_html( $cta_text ) . ' →</a>'
            . '</div>';
    }

    return '<!DOCTYPE html><html lang="tr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">'
        . '<title>' . esc_html( $title ) . '</title></head>'
        . '<body style="margin:0;padding:0;background-color:#090A0D;font-family:\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif;color:#F3F4F6;">'
        . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#090A0D;padding:28px 12px;">'
        . '<tr><td align="center">'
        . '<table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width:600px;width:100%;background-color:#111319;border:1px solid #222633;border-radius:18px;overflow:hidden;">'
        // Header
        . '<tr><td style="padding:26px 30px;background:linear-gradient(135deg,#190A10 0%,#111319 65%,#141A24 100%);border-bottom:2px solid #E31837;">'
        . '<div style="font-size:11px;font-weight:800;letter-spacing:1.6px;text-transform:uppercase;color:#F4E268;margin-bottom:8px;">▲ ' . esc_html( $badge ) . '</div>'
        . '<div style="font-size:24px;font-weight:900;letter-spacing:0.4px;color:#FFFFFF;line-height:1.2;">APEX <span style="color:#E31837;">ARENA</span></div>'
        . '<div style="font-size:17px;font-weight:700;color:#E5E7EB;margin-top:10px;">' . esc_html( $title ) . '</div>'
        . '</td></tr>'
        // Body
        . '<tr><td style="padding:26px 30px;">'
        . '<p style="margin:0 0 18px;font-size:14.5px;line-height:1.65;color:#D1D5DB;">' . wp_kses_post( $intro ) . '</p>'
        . $highlight_html
        . ( $rows_html ? '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#0D0F14;border:1px solid #222530;border-radius:12px;overflow:hidden;margin:16px 0;">' . $rows_html . '</table>' : '' )
        . $tips_html
        . $cta_html
        . '</td></tr>'
        // Footer
        . '<tr><td style="padding:20px 30px;background:#0C0D12;border-top:1px solid #1E222D;font-size:12px;line-height:1.6;color:#9CA3AF;">'
        . '<div style="font-weight:700;color:#E5E7EB;margin-bottom:4px;">APEX ARENA Spor ve Performans Kompleksi</div>'
        . '<div>' . esc_html( $s['address'] ) . '</div>'
        . '<div>Tel: ' . esc_html( $s['phone'] ) . ' · Hafta İçi: ' . esc_html( $s['weekday_hours'] ) . ' · Hafta Sonu: ' . esc_html( $s['weekend_hours'] ) . '</div>'
        . '<div style="margin-top:10px;font-size:11px;color:#6B7280;">' . esc_html( $footer_n ) . '</div>'
        . '</td></tr>'
        . '</table>'
        . '</td></tr></table></body></html>';
}

/**
 * Merkezi E-posta Gönderici + MySQL Kayıt Defteri (wp_apex_email_logs)
 *
 * @return array{ok:bool,status:string,delivery_mode:string,log_id:int,message:string,error:string}
 */
function apex_send_mail( array $params ) {
    global $wpdb;

    $to         = sanitize_email( $params['to'] ?? '' );
    $to_name    = sanitize_text_field( $params['to_name'] ?? '' );
    $subject    = sanitize_text_field( $params['subject'] ?? 'APEX ARENA Bildirimi' );
    $html       = (string) ( $params['html'] ?? '' );
    $mail_type  = sanitize_key( $params['mail_type'] ?? 'general' );
    $ref_key    = sanitize_text_field( $params['ref_key'] ?? '' );
    $user_id    = absint( $params['user_id'] ?? 0 );
    $force_send = ! empty( $params['force_send'] );

    if ( ! is_email( $to ) ) {
        return [
            'ok'            => false,
            'status'        => 'failed',
            'delivery_mode' => 'none',
            'log_id'        => 0,
            'message'       => 'Geçersiz alıcı e-posta adresi.',
            'error'         => 'invalid_email',
        ];
    }

    $table = $wpdb->prefix . 'apex_email_logs';

    // Tekrarlı otomatik hatırlatmaları önle
    if ( $ref_key && ! $force_send ) {
        $already = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE ref_key = %s", $ref_key ) );
        if ( $already > 0 ) {
            return [
                'ok'            => true,
                'status'        => 'skipped_duplicate',
                'delivery_mode' => 'none',
                'log_id'        => 0,
                'message'       => 'Bu hatırlatma daha önce gönderildi.',
                'error'         => '',
            ];
        }
    }

    $s            = apex_get_settings();
    $gmail_ready  = apex_is_gmail_smtp_ready();
    $status       = 'queued_local';
    $mode         = 'local_preview';
    $error_msg    = '';
    $user_message = '';

    if ( $gmail_ready ) {
        $GLOBALS['apex_last_mail_error'] = '';
        $headers = [ 'Content-Type: text/html; charset=UTF-8' ];
        $from_e  = trim( (string) $s['smtp_user'] );
        $from_n  = ! empty( $s['smtp_from_name'] ) ? $s['smtp_from_name'] : 'APEX ARENA Spor Kompleksi';
        if ( is_email( $from_e ) ) {
            $headers[] = sprintf( 'From: %s <%s>', $from_n, $from_e );
            $headers[] = sprintf( 'Reply-To: %s <%s>', $from_n, $from_e );
        }

        $sent = wp_mail( $to, $subject, $html, $headers );
        if ( $sent ) {
            $status       = 'sent';
            $mode         = 'gmail_smtp';
            $user_message = sprintf( 'E-posta Gmail SMTP üzerinden %s adresine başarıyla gönderildi.', $to );
        } else {
            $status       = 'failed';
            $mode         = 'gmail_smtp';
            $error_msg    = $GLOBALS['apex_last_mail_error'] ?: 'SMTP bağlantısı kurulamadı. Gmail adresi ve 16 haneli Google Uygulama Şifresini kontrol edin.';
            $user_message = 'Gmail SMTP gönderimi başarısız oldu ancak e-posta kayıt defterinde saklandı.';
        }
    } else {
        // Yerel XAMPP ortamında SMTP henüz girilmemişse kayıt/rezervasyon akışını bloklamadan logla
        $status       = 'queued_local';
        $mode         = 'local_preview';
        $user_message = sprintf( 'Hoş geldin/bildirim e-postası %s için oluşturuldu ve E-posta Merkezi’ne kaydedildi (canlı Gmail iletimi için WP Admin → E-posta & Hatırlatma bölümünden Gmail SMTP tanımlayabilirsiniz).', $to );
    }

    $wpdb->insert(
        $table,
        [
            'user_id'         => $user_id,
            'recipient_email' => $to,
            'recipient_name'  => $to_name,
            'mail_type'       => $mail_type,
            'ref_key'         => $ref_key,
            'subject'         => $subject,
            'body_html'       => $html,
            'delivery_mode'   => $mode,
            'status'          => $status,
            'error_message'   => $error_msg,
        ],
        [ '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ]
    );

    return [
        'ok'            => ( $status !== 'failed' ),
        'status'        => $status,
        'delivery_mode' => $mode,
        'log_id'        => (int) $wpdb->insert_id,
        'message'       => $user_message,
        'error'         => $error_msg,
    ];
}

/**
 * 1. Yeni Üye Kaydı — Hoş Geldin & Dijital QR Kart E-postası
 */
function apex_send_welcome_email( array $data, $force = false ) {
    $s = apex_get_settings();
    if ( ! $force && empty( $s['mail_on_register'] ) ) {
        return [ 'ok' => true, 'status' => 'disabled', 'delivery_mode' => 'none', 'log_id' => 0, 'message' => 'Kayıt e-postası ayarlarda kapalı.', 'error' => '' ];
    }

    $name       = $data['full_name'] ?? 'Değerli Sporcumuz';
    $email      = $data['email'] ?? '';
    $code       = $data['member_code'] ?? 'APX-2026-0001';
    $plan       = $data['plan_name'] ?? 'APEX PRO';
    $billing    = ( ( $data['billing_cycle'] ?? 'monthly' ) === 'annual' ) ? 'Yıllık (%20 İndirimli)' : 'Aylık Standart';
    $price      = isset( $data['price'] ) ? apex_format_tl( $data['price'] ) : '₺2.490';
    $end_date   = ! empty( $data['end_date'] ) ? date( 'd.m.Y', strtotime( $data['end_date'] ) ) : date( 'd.m.Y', strtotime( '+30 days' ) );
    $goal       = ! empty( $data['goal'] ) ? $data['goal'] : 'Atletik Performans';
    $user_id    = absint( $data['user_id'] ?? 0 );

    $html = apex_render_email_template( [
        'badge'       => 'HOŞ GELDİNİZ · DİJİTAL ÜYELİK AKTİF',
        'title'       => "APEX ARENA Ailesine Hoş Geldin, {$name}!",
        'intro'       => "Üyeliğin başarıyla oluşturuldu ve <strong>Dijital QR Geçiş Kartın</strong> aktif edildi. Artık turnikelerden ve kapalı otoparktan telefonundaki QR kod ile saniyeler içinde geçiş yapabilir, salon kiralayabilir ve PT randevusu alabilirsin.",
        'highlight'   => "<div style=\"font-size:11px;letter-spacing:1.2px;text-transform:uppercase;color:#F4E268;font-weight:800;margin-bottom:4px;\">DİJİTAL QR ÜYE KODUN</div>"
                       . "<div style=\"font-size:24px;font-weight:900;letter-spacing:2px;color:#FFFFFF;\">" . esc_html( $code ) . "</div>"
                       . "<div style=\"font-size:12.5px;color:#D1D5DB;margin-top:6px;\">Üye paneline giriş yaparak QR kodunu tam ekran büyütebilirsin.</div>",
        'rows'        => [
            'Üye Adı Soyadı'    => $name,
            'Seçilen Paket'     => $plan,
            'Ödeme Periyodu'    => $billing,
            'Paket Tutarı'      => $price,
            'Üyelik Bitiş'      => $end_date,
            'Birincil Hedef'    => $goal,
        ],
        'tips'        => [
            'İlk ziyaretinde resepsiyondan ücretsiz InBody 570 vücut analizi randevunu oluşturabilirsin.',
            'Hafta içi 06:30–23:30, hafta sonu 08:00–22:00 saatleri arasında tesisimiz açıktır.',
            'Randevularından 24 saat önce otomatik hatırlatma e-postaları göndereceğiz (Üye Panelinden yönetebilirsin).',
        ],
        'cta_text'    => 'Dijital QR Kartım & Üye Panelim',
        'cta_url'     => home_url( '/uyelik/?panel=1' ),
    ] );

    return apex_send_mail( [
        'to'         => $email,
        'to_name'    => $name,
        'subject'    => "APEX ARENA'ya Hoş Geldin {$name}! Dijital QR Kartın Hazır ({$code})",
        'html'       => $html,
        'mail_type'  => 'welcome',
        'ref_key'    => $force ? '' : 'welcome_' . $code,
        'user_id'    => $user_id,
        'force_send' => $force,
    ] );
}

/**
 * 2. Salon Kiralama Rezervasyon Onay E-postası
 */
function apex_send_court_booking_email( array $data ) {
    $s = apex_get_settings();
    if ( empty( $s['mail_on_booking'] ) ) {
        return [ 'ok' => true, 'status' => 'disabled', 'delivery_mode' => 'none', 'log_id' => 0, 'message' => '', 'error' => '' ];
    }

    $name     = $data['customer_name'] ?? 'Sporcu';
    $email    = $data['customer_email'] ?? '';
    $code     = $data['booking_code'] ?? '';
    $court    = $data['court_name'] ?? 'Salon';
    $date_lbl = ! empty( $data['booking_date'] ) ? date( 'd.m.Y', strtotime( $data['booking_date'] ) ) : '';
    $slot     = $data['time_slot'] ?? '';
    $dur      = (int) ( $data['duration_hours'] ?? 1 );
    $referee  = ! empty( $data['needs_referee'] ) ? 'Evet — Lisanslı Hakem Dahil' : 'Hayır';
    $total    = apex_format_tl( $data['total_price'] ?? 0 );

    $html = apex_render_email_template( [
        'badge'     => 'SALON REZERVASYONU ONAYLANDI',
        'title'     => "{$court} Rezervasyonun Hazır!",
        'intro'     => "Merhaba <strong>" . esc_html( $name ) . "</strong>, salon rezervasyonun onaylandı. Maç saatinden 24 saat önce sana otomatik bir hatırlatma e-postası daha göndereceğiz.",
        'highlight' => "<strong>Rezervasyon Kodu:</strong> <span style=\"color:#F4E268;font-weight:900;letter-spacing:1px;\">" . esc_html( $code ) . "</span><br>"
                     . "📅 " . esc_html( "{$date_lbl} · {$slot} ({$dur} Saat)" ),
        'rows'      => [
            'Salon'            => $court,
            'Tarih & Saat'     => "{$date_lbl} · {$slot}",
            'Süre'             => "{$dur} Saat",
            'Hakem Hizmeti'    => $referee,
            'Toplam Tutar'     => $total,
        ],
        'tips'      => [
            'Parke koruması için temiz tabanlı salon ayakkabısı getirmenizi rica ederiz.',
            'Maçtan 15 dakika önce gelerek RFID akıllı soyunma dolapları ve ısınma alanını kullanabilirsiniz.',
            'İlk 2 saat ücretsiz kapalı otoparkımızdan faydalanabilirsiniz.',
        ],
        'cta_text'  => 'Rezervasyonlarımı Görüntüle',
        'cta_url'   => home_url( '/uyelik/?panel=1' ),
    ] );

    return apex_send_mail( [
        'to'        => $email,
        'to_name'   => $name,
        'subject'   => "Salon Rezervasyon Onayı: {$court} ({$date_lbl} {$slot}) — {$code}",
        'html'      => $html,
        'mail_type' => 'court_booking',
        'ref_key'   => 'book_court_' . $code,
        'user_id'   => absint( $data['user_id'] ?? 0 ),
    ] );
}

/**
 * 3. PT Randevu Onay E-postası
 */
function apex_send_pt_booking_email( array $data ) {
    $s = apex_get_settings();
    if ( empty( $s['mail_on_booking'] ) ) {
        return [ 'ok' => true, 'status' => 'disabled', 'delivery_mode' => 'none', 'log_id' => 0, 'message' => '', 'error' => '' ];
    }

    $name     = $data['customer_name'] ?? 'Sporcu';
    $email    = $data['customer_email'] ?? '';
    $code     = $data['booking_code'] ?? '';
    $trainer  = $data['trainer_name'] ?? 'APEX Koçu';
    $pkg      = $data['package_name'] ?? 'PT Seansı';
    $type     = ( ( $data['training_type'] ?? 'bireysel' ) === 'grupsal' ) ? 'Grupsal PT' : 'Bireysel PT';
    $date_lbl = ! empty( $data['appointment_date'] ) ? date( 'd.m.Y', strtotime( $data['appointment_date'] ) ) : '';
    $slot     = $data['time_slot'] ?? '';
    $price    = apex_format_tl( $data['price'] ?? 0 );

    $html = apex_render_email_template( [
        'badge'     => 'PT RANDEVUSU ONAYLANDI',
        'title'     => "{$trainer} ile Antrenman Randevun Onaylandı!",
        'intro'     => "Merhaba <strong>" . esc_html( $name ) . "</strong>, kişisel antrenörlük randevun takvime işlendi. Seansından önce otomatik hatırlatma e-postası da alacaksın.",
        'highlight' => "<strong>Randevu Kodu:</strong> <span style=\"color:#F4E268;font-weight:900;letter-spacing:1px;\">" . esc_html( $code ) . "</span><br>"
                     . "🏋️ " . esc_html( "{$trainer} · {$date_lbl} · {$slot}" ),
        'rows'      => [
            'Antrenör'       => $trainer,
            'Eğitim Türü'    => $type,
            'Paket'          => $pkg,
            'Tarih & Saat'   => "{$date_lbl} · {$slot}",
            'Ücret'          => $price,
        ],
        'tips'      => [
            'Seans öncesi 60–90 dakika içinde hafif karbonhidrat + protein ağırlıklı bir ara öğün önerilir.',
            'Antrenman çıkışı için Apex Protein Kafe’den ön sipariş vererek shake’ini hazır bulabilirsin.',
        ],
        'cta_text'  => 'PT Randevularımı Gör',
        'cta_url'   => home_url( '/uyelik/?panel=1' ),
    ] );

    return apex_send_mail( [
        'to'        => $email,
        'to_name'   => $name,
        'subject'   => "PT Randevu Onayı: {$trainer} ({$date_lbl} {$slot}) — {$code}",
        'html'      => $html,
        'mail_type' => 'pt_booking',
        'ref_key'   => 'book_pt_' . $code,
        'user_id'   => absint( $data['user_id'] ?? 0 ),
    ] );
}

/**
 * 4. Yaklaşan Salon veya PT Randevusu Hatırlatma E-postası
 */
function apex_send_booking_reminder_email( $kind, $row, $force = false ) {
    $is_pt    = ( $kind === 'pt' );
    $name     = $row->customer_name ?? 'Sporcu';
    $email    = $row->customer_email ?? '';
    $code     = $row->booking_code ?? '';
    $date_raw = $is_pt ? ( $row->appointment_date ?? '' ) : ( $row->booking_date ?? '' );
    $date_lbl = $date_raw ? date( 'd.m.Y', strtotime( $date_raw ) ) : '';
    $slot     = $row->time_slot ?? '';
    $title_it = $is_pt ? ( $row->trainer_name . ' ile PT Seansı' ) : ( $row->court_name . ' Maçı' );
    $ref_key  = ( $is_pt ? 'rem_pt_' : 'rem_court_' ) . (int) ( $row->id ?? 0 );

    $rows = $is_pt
        ? [
            'Antrenör'      => $row->trainer_name,
            'Paket'         => $row->package_name,
            'Tarih & Saat'  => "{$date_lbl} · {$slot}",
            'Randevu Kodu'  => $code,
        ]
        : [
            'Salon'         => $row->court_name,
            'Tarih & Saat'  => "{$date_lbl} · {$slot} ({$row->duration_hours} Saat)",
            'Hakem Durumu'  => ! empty( $row->needs_referee ) ? 'Lisanslı Hakem Hazır' : 'Hakemsiz',
            'Rezervasyon'   => $code,
        ];

    $html = apex_render_email_template( [
        'badge'     => 'OTOMATİK RANDEVU HATIRLATMASI',
        'title'     => "⏰ Hatırlatma: {$title_it} Yaklaşıyor!",
        'intro'     => "Merhaba <strong>" . esc_html( $name ) . "</strong>, APEX ARENA'daki yaklaşan randevunu hatırlatmak istedik. Enerjin hazırsa sahada görüşürüz!",
        'highlight' => "📅 <strong>" . esc_html( $date_lbl ) . "</strong> saat <strong>" . esc_html( $slot ) . "</strong> — " . esc_html( $title_it ),
        'rows'      => $rows,
        'tips'      => [
            'QR dijital kartınla turnikeden hızlı geçiş yapabilirsin.',
            'Seansından 15 dakika önce ısınma alanında olman performansı artırır.',
            'Planın değiştiyse Üye Paneli üzerinden randevunu tek tıkla iptal edebilirsin.',
        ],
        'cta_text'  => 'Üye Panelimi & QR Kartımı Aç',
        'cta_url'   => home_url( '/uyelik/?panel=1' ),
    ] );

    return apex_send_mail( [
        'to'         => $email,
        'to_name'    => $name,
        'subject'    => "⏰ Hatırlatma: {$title_it} ({$date_lbl} {$slot}) — APEX ARENA",
        'html'       => $html,
        'mail_type'  => $is_pt ? 'reminder_pt' : 'reminder_court',
        'ref_key'    => $force ? '' : $ref_key,
        'user_id'    => absint( $row->user_id ?? 0 ),
        'force_send' => $force,
    ] );
}

/**
 * 5. Kişisel Antrenman & Üyelik Hatırlatma E-postası (Üyeye Özel / Haftalık veya Yenileme)
 */
function apex_send_member_reminder_email( $user_id, $mode = 'workout', $force = false ) {
    global $wpdb;
    $user = get_user_by( 'id', absint( $user_id ) );
    if ( ! $user || ! is_email( $user->user_email ) ) {
        return [ 'ok' => false, 'status' => 'failed', 'delivery_mode' => 'none', 'log_id' => 0, 'message' => 'Üye bulunamadı.', 'error' => 'no_user' ];
    }

    $name   = $user->display_name ?: $user->user_login;
    $plan   = get_user_meta( $user->ID, 'apex_membership_plan', true ) ?: 'APEX PRO';
    $code   = get_user_meta( $user->ID, 'apex_member_code', true ) ?: 'APX-2026-' . str_pad( (string) $user->ID, 4, '0', STR_PAD_LEFT );
    $end    = get_user_meta( $user->ID, 'apex_membership_end', true ) ?: date( 'Y-m-d', strtotime( '+30 days' ) );
    $goal   = get_user_meta( $user->ID, 'apex_member_goal', true ) ?: 'Atletik Performans';
    $days_l = max( 0, (int) ceil( ( strtotime( $end ) - time() ) / DAY_IN_SECONDS ) );

    $coach_rec = $wpdb->get_row( $wpdb->prepare(
        "SELECT coach_name, coach_advice, progress_pct FROM {$wpdb->prefix}apex_coach_students WHERE student_user_id = %d OR student_email = %s LIMIT 1",
        $user->ID, $user->user_email
    ) );

    $is_renewal = ( $mode === 'renewal' );
    $subject    = $is_renewal
        ? "⏳ Üyelik Hatırlatması: {$plan} Paketinizin Bitimine {$days_l} Gün Kaldı — APEX ARENA"
        : "🔥 Antrenman & Hedef Hatırlatması: {$name}, Bugün Sahada Buluşalım! — APEX ARENA";

    $highlight = $is_renewal
        ? "<strong>{$plan}</strong> üyeliğinin geçerlilik tarihi <strong>" . esc_html( date( 'd.m.Y', strtotime( $end ) ) ) . "</strong> ({$days_l} gün kaldı). Kesintisiz QR geçiş ve koç takibi için üyeliğini panelden yenileyebilirsin."
        : ( $coach_rec && ! empty( $coach_rec->coach_advice )
            ? "<strong>💡 Koçun " . esc_html( $coach_rec->coach_name ) . "’ın Notu:</strong><br>" . esc_html( $coach_rec->coach_advice )
            : "Bu haftaki hedefin: <strong>" . esc_html( $goal ) . "</strong>. Antrenman sonrası verilerini Üye Panelindeki performans günlüğüne kaydetmeyi unutma!" );

    $rows = [
        'Üye Kodu (QR)'     => $code,
        'Aktif Paket'       => $plan,
        'Birincil Hedef'    => $goal,
        'Üyelik Bitiş'      => date( 'd.m.Y', strtotime( $end ) ) . " ({$days_l} gün)",
    ];
    if ( $coach_rec ) {
        $rows['Atanmış Koç']    = $coach_rec->coach_name;
        $rows['Program İlerleme'] = '%' . (int) $coach_rec->progress_pct;
    }

    $html = apex_render_email_template( [
        'badge'     => $is_renewal ? 'ÜYELİK YENİLEME HATIRLATMASI' : 'ANTRENMAN & BESLENME HATIRLATMASI',
        'title'     => $is_renewal ? "Üyeliğinin Bitimine {$days_l} Gün Kaldı!" : "Hareket Zamanı, {$name}!",
        'intro'     => $is_renewal
            ? "Merhaba <strong>" . esc_html( $name ) . "</strong>, APEX ARENA üyeliğinin bitiş tarihini hatırlatmak istedik. Yıllık yenilemede %20 indirim avantajından yararlanabilirsin."
            : "Merhaba <strong>" . esc_html( $name ) . "</strong>, düzenli ritmini koruman için kısa bir APEX ARENA hatırlatması! Bugünkü antrenmanını ve beslenme hedeflerini panelinden takip edebilirsin.",
        'highlight' => $highlight,
        'rows'      => $rows,
        'tips'      => [
            'Antrenman öncesi su tüketimini ihmal etme ve 10 dk dinamik ısınma yap.',
            'Çıkışta Fin saunası ve Apex Protein Kafe ön siparişiyle toparlanmanı hızlandır.',
        ],
        'cta_text'  => $is_renewal ? 'Üyeliğimi Yenile / Yükselt' : 'Antrenman Günlüğüme Git',
        'cta_url'   => home_url( $is_renewal ? '/uyelik/#kayit' : '/uyelik/?panel=1#antrenman-takip' ),
    ] );

    $ref_key = $is_renewal
        ? 'rem_mem_' . $user->ID . '_' . date( 'Y-m' )
        : 'rem_wk_' . $user->ID . '_' . date( 'Y-W' );

    return apex_send_mail( [
        'to'         => $user->user_email,
        'to_name'    => $name,
        'subject'    => $subject,
        'html'       => $html,
        'mail_type'  => $is_renewal ? 'reminder_membership' : 'reminder_workout',
        'ref_key'    => $force ? '' : $ref_key,
        'user_id'    => $user->ID,
        'force_send' => $force,
    ] );
}

/**
 * 6. Gmail SMTP Bağlantı Test Maili
 */
function apex_send_test_email( $to_email ) {
    $s   = apex_get_settings();
    $now = current_time( 'd.m.Y H:i:s' );
    $html = apex_render_email_template( [
        'badge'     => 'GMAIL SMTP BAĞLANTI TESTİ',
        'title'     => 'APEX ARENA E-posta Sistemi Başarıyla Çalışıyor!',
        'intro'     => 'Tebrikler! Bu e-postayı görüyorsan <strong>APEX ARENA Gmail SMTP &amp; Otomatik Hatırlatma Motoru</strong> doğru şekilde yapılandırılmış demektir.',
        'highlight' => '✅ <strong>Durum:</strong> ' . ( apex_is_gmail_smtp_ready() ? 'Canlı Gmail SMTP Aktif (' . esc_html( $s['smtp_user'] ) . ')' : 'Yerel Önizleme Modu (Gmail SMTP henüz etkin değil)' ),
        'rows'      => [
            'SMTP Sunucusu'     => ( $s['smtp_host'] ?: 'smtp.gmail.com' ) . ':' . ( $s['smtp_port'] ?: 587 ),
            'Şifreleme'         => strtoupper( $s['smtp_encryption'] ?: 'TLS' ),
            'Gönderim Zamanı'   => $now,
            'Tema Sürümü'       => 'APEX ARENA v' . APEX_VERSION,
        ],
        'cta_text'  => 'Yönetim E-posta Merkezine Dön',
        'cta_url'   => admin_url( 'admin.php?page=apex-email-center' ),
    ] );

    return apex_send_mail( [
        'to'         => $to_email,
        'to_name'    => 'APEX Yönetici',
        'subject'    => "✅ APEX ARENA Gmail SMTP Test Maili ({$now})",
        'html'       => $html,
        'mail_type'  => 'test',
        'user_id'    => get_current_user_id(),
        'force_send' => true,
    ] );
}

/**
 * Otomatik Hatırlatma Tarayıcısı (WP-Cron Saatlik + Admin Manuel Tetikleme)
 * - Önümüzdeki X saat içindeki Salon Rezervasyonları
 * - Önümüzdeki X saat içindeki PT Randevuları
 * - Bitişine <= 7 gün kalan aktif üyelikler
 */
function apex_run_scheduled_reminders( $manual = false ) {
    global $wpdb;
    $s = apex_get_settings();
    if ( ! $manual && empty( $s['mail_auto_reminders'] ) ) {
        return [ 'sent' => 0, 'skipped' => 0, 'failed' => 0, 'details' => [] ];
    }

    $hours   = max( 1, min( 168, (int) ( $s['reminder_hours_before'] ?? 24 ) ) );
    $now_ts  = current_time( 'timestamp' );
    $end_ts  = $now_ts + ( $hours * HOUR_IN_SECONDS );
    $today   = date( 'Y-m-d', $now_ts );
    $max_day = date( 'Y-m-d', $end_ts );

    $summary = [ 'sent' => 0, 'skipped' => 0, 'failed' => 0, 'details' => [] ];

    // 1. Yaklaşan Salon Rezervasyonları
    $courts = $wpdb->get_results( $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}apex_court_bookings WHERE status = 'confirmed' AND booking_date BETWEEN %s AND %s ORDER BY booking_date ASC, time_slot ASC",
        $today, $max_day
    ) );
    foreach ( (array) $courts as $c ) {
        if ( $c->user_id && get_user_meta( (int) $c->user_id, 'apex_mail_reminders_opt_out', true ) === '1' ) {
            $summary['skipped']++;
            continue;
        }
        $slot_ts = strtotime( $c->booking_date . ' ' . $c->time_slot );
        if ( $slot_ts && $slot_ts >= $now_ts && $slot_ts <= $end_ts ) {
            $res = apex_send_booking_reminder_email( 'court', $c );
            if ( $res['status'] === 'skipped_duplicate' ) {
                $summary['skipped']++;
            } elseif ( $res['ok'] ) {
                $summary['sent']++;
                $summary['details'][] = "Salon Hatırlatma → {$c->customer_email} ({$c->court_name})";
            } else {
                $summary['failed']++;
            }
        }
    }

    // 2. Yaklaşan PT Randevuları
    $pts = $wpdb->get_results( $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}apex_pt_appointments WHERE status = 'confirmed' AND appointment_date BETWEEN %s AND %s ORDER BY appointment_date ASC, time_slot ASC",
        $today, $max_day
    ) );
    foreach ( (array) $pts as $pt ) {
        if ( $pt->user_id && get_user_meta( (int) $pt->user_id, 'apex_mail_reminders_opt_out', true ) === '1' ) {
            $summary['skipped']++;
            continue;
        }
        $slot_ts = strtotime( $pt->appointment_date . ' ' . $pt->time_slot );
        if ( $slot_ts && $slot_ts >= $now_ts && $slot_ts <= $end_ts ) {
            $res = apex_send_booking_reminder_email( 'pt', $pt );
            if ( $res['status'] === 'skipped_duplicate' ) {
                $summary['skipped']++;
            } elseif ( $res['ok'] ) {
                $summary['sent']++;
                $summary['details'][] = "PT Hatırlatma → {$pt->customer_email} ({$pt->trainer_name})";
            } else {
                $summary['failed']++;
            }
        }
    }

    // 3. Bitimine 7 Gün veya Daha Az Kalan Aktif Üyelikler
    $users = get_users( [
        'meta_key'   => 'apex_membership_status',
        'meta_value' => 'active',
        'fields'     => [ 'ID', 'user_email' ],
    ] );
    foreach ( (array) $users as $u ) {
        if ( get_user_meta( (int) $u->ID, 'apex_mail_reminders_opt_out', true ) === '1' ) {
            $summary['skipped']++;
            continue;
        }
        $end_str = get_user_meta( (int) $u->ID, 'apex_membership_end', true );
        if ( ! $end_str ) {
            continue;
        }
        $days_left = (int) ceil( ( strtotime( $end_str ) - $now_ts ) / DAY_IN_SECONDS );
        if ( $days_left >= 0 && $days_left <= 7 ) {
            $res = apex_send_member_reminder_email( (int) $u->ID, 'renewal' );
            if ( $res['status'] === 'skipped_duplicate' ) {
                $summary['skipped']++;
            } elseif ( $res['ok'] ) {
                $summary['sent']++;
                $summary['details'][] = "Üyelik Yenileme Hatırlatma → {$u->user_email} ({$days_left} gün)";
            } else {
                $summary['failed']++;
            }
        }
    }

    update_option( 'apex_last_reminder_run', current_time( 'mysql' ) );
    return $summary;
}

/**
 * WP-Cron Saatlik Hatırlatma Görevini Kaydet
 */
add_action( 'apex_hourly_reminder_event', function() {
    apex_run_scheduled_reminders( false );
} );

add_action( 'init', function() {
    if ( ! wp_next_scheduled( 'apex_hourly_reminder_event' ) ) {
        wp_schedule_event( time() + 300, 'hourly', 'apex_hourly_reminder_event' );
    }
} );

/**
 * E-posta türü insan okunabilir etiket + renk metadatası
 */
function apex_mail_type_meta( $type ) {
    $map = [
        'welcome'             => [ 'label' => 'Hoş Geldin & QR',     'color' => '#a4ff2e', 'bg' => 'rgba(164,255,46,0.14)' ],
        'court_booking'       => [ 'label' => 'Salon Onayı',         'color' => '#26e6ff', 'bg' => 'rgba(38,230,255,0.14)' ],
        'pt_booking'          => [ 'label' => 'PT Randevu Onayı',    'color' => '#26e6ff', 'bg' => 'rgba(38,230,255,0.14)' ],
        'reminder_court'      => [ 'label' => 'Salon Hatırlatma',    'color' => '#ffbe0b', 'bg' => 'rgba(255,190,11,0.16)' ],
        'reminder_pt'         => [ 'label' => 'PT Hatırlatma',       'color' => '#ffbe0b', 'bg' => 'rgba(255,190,11,0.16)' ],
        'reminder_membership' => [ 'label' => 'Üyelik Hatırlatma',   'color' => '#ff4b7c', 'bg' => 'rgba(250,17,79,0.16)' ],
        'reminder_workout'    => [ 'label' => 'Antrenman Hatırlatma','color' => '#ffbe0b', 'bg' => 'rgba(255,190,11,0.16)' ],
        'test'                => [ 'label' => 'SMTP Test',           'color' => '#c084fc', 'bg' => 'rgba(192,132,252,0.16)' ],
    ];
    return $map[ $type ] ?? [ 'label' => 'Bildirim', 'color' => '#9ca3af', 'bg' => 'rgba(156,163,175,0.14)' ];
}
