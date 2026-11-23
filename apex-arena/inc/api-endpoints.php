<?php
/**
 * WordPress REST API Uç Noktaları — Canlı Müsaitlik, Salon & PT Rezervasyon, Üyelik, İletişim, AI Chatbot
 *
 * @package ApexArena
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'rest_api_init', function() {
    register_rest_route( 'apex/v1', '/availability', [
        'methods'             => 'GET',
        'callback'            => 'apex_api_get_availability',
        'permission_callback' => '__return_true',
    ] );

    register_rest_route( 'apex/v1', '/book-court', [
        'methods'             => 'POST',
        'callback'            => 'apex_api_book_court',
        'permission_callback' => '__return_true',
    ] );

    register_rest_route( 'apex/v1', '/book-pt', [
        'methods'             => 'POST',
        'callback'            => 'apex_api_book_pt',
        'permission_callback' => '__return_true',
    ] );

    register_rest_route( 'apex/v1', '/membership/register', [
        'methods'             => 'POST',
        'callback'            => 'apex_api_membership_register',
        'permission_callback' => '__return_true',
    ] );

    register_rest_route( 'apex/v1', '/membership/login', [
        'methods'             => 'POST',
        'callback'            => 'apex_api_membership_login',
        'permission_callback' => '__return_true',
    ] );

    register_rest_route( 'apex/v1', '/booking/cancel', [
        'methods'             => 'POST',
        'callback'            => 'apex_api_cancel_booking',
        'permission_callback' => function() {
            return is_user_logged_in();
        },
    ] );

    register_rest_route( 'apex/v1', '/contact', [
        'methods'             => 'POST',
        'callback'            => 'apex_api_contact_submit',
        'permission_callback' => '__return_true',
    ] );

    register_rest_route( 'apex/v1', '/chat', [
        'methods'             => 'POST',
        'callback'            => 'apex_api_chat',
        'permission_callback' => '__return_true',
    ] );
} );

/**
 * Canlı Salon veya Antrenör Müsaitlik Sorgusu
 */
function apex_api_get_availability( WP_REST_Request $req ) {
    global $wpdb;
    $type = sanitize_text_field( $req->get_param( 'type' ) ?: 'court' );
    $id   = absint( $req->get_param( 'id' ) );
    $date = sanitize_text_field( $req->get_param( 'date' ) ?: current_time( 'Y-m-d' ) );

    if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
        return new WP_REST_Response( [ 'error' => 'Geçersiz tarih formatı.' ], 400 );
    }

    $all_slots = apex_get_time_slots();
    if ( $type === 'trainer' ) {
        $taken = $wpdb->get_col( $wpdb->prepare(
            "SELECT time_slot FROM {$wpdb->prefix}apex_pt_appointments WHERE trainer_id = %d AND appointment_date = %s AND status != 'cancelled'",
            $id, $date
        ) );
    } else {
        $taken = $wpdb->get_col( $wpdb->prepare(
            "SELECT time_slot FROM {$wpdb->prefix}apex_court_bookings WHERE court_id = %d AND booking_date = %s AND status != 'cancelled'",
            $id, $date
        ) );
    }

    $slots = [];
    foreach ( $all_slots as $slot ) {
        $slots[] = [
            'time'      => $slot,
            'available' => ! in_array( $slot, $taken, true ),
        ];
    }

    return new WP_REST_Response( [
        'type'  => $type,
        'id'    => $id,
        'date'  => $date,
        'slots' => $slots,
    ], 200 );
}

/**
 * Salon Kiralama Rezervasyonu (Hakem Ayarlama Hizmeti dahil)
 */
function apex_api_book_court( WP_REST_Request $req ) {
    global $wpdb;
    $p = $req->get_json_params();

    $court_id = absint( $p['court_id'] ?? 0 );
    $date     = sanitize_text_field( $p['date'] ?? '' );
    $slot     = sanitize_text_field( $p['time_slot'] ?? '' );
    $duration = max( 1, min( 4, absint( $p['duration'] ?? 1 ) ) );
    $referee  = ! empty( $p['needs_referee'] ) ? 1 : 0;
    $name     = sanitize_text_field( $p['customer_name'] ?? '' );
    $email    = sanitize_email( $p['customer_email'] ?? '' );
    $phone    = sanitize_text_field( $p['customer_phone'] ?? '' );
    $notes    = sanitize_textarea_field( $p['notes'] ?? '' );

    if ( ! $court_id || ! $date || ! $slot || ! $name || ! is_email( $email ) || ! $phone ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'Lütfen tüm zorunlu rezervasyon alanlarını eksiksiz doldurun.' ], 400 );
    }

    $court = get_post( $court_id );
    if ( ! $court || $court->post_type !== 'apex_court' ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'Seçilen salon bulunamadı.' ], 404 );
    }

    // Çakışma kontrolü
    $exists = (int) $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}apex_court_bookings WHERE court_id = %d AND booking_date = %s AND time_slot = %s AND status != 'cancelled'",
        $court_id, $date, $slot
    ) );
    if ( $exists > 0 ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'Üzgünüz, seçtiğiniz saat dilimi az önce doldu. Lütfen başka bir saat seçin.' ], 409 );
    }

    $hourly   = (int) get_post_meta( $court_id, '_apex_hourly_rate', true ) ?: 1500;
    $ref_unit = (int) get_post_meta( $court_id, '_apex_referee_fee', true ) ?: (int) apex_get_settings()['referee_fee'];
    $sport    = get_post_meta( $court_id, '_apex_sport', true ) ?: 'basketbol';
    $base     = $hourly * $duration;
    $ref_fee  = $referee ? $ref_unit : 0;
    $total    = $base + $ref_fee;
    $code     = 'APX-SLN-' . strtoupper( substr( md5( uniqid( '', true ) ), 0, 6 ) );

    $inserted = $wpdb->insert(
        $wpdb->prefix . 'apex_court_bookings',
        [
            'booking_code'   => $code,
            'court_id'       => $court_id,
            'court_name'     => $court->post_title,
            'court_sport'    => $sport,
            'user_id'        => get_current_user_id(),
            'customer_name'  => $name,
            'customer_email' => $email,
            'customer_phone' => $phone,
            'booking_date'   => $date,
            'time_slot'      => $slot,
            'duration_hours' => $duration,
            'needs_referee'  => $referee,
            'referee_fee'    => $ref_fee,
            'base_price'     => $base,
            'total_price'    => $total,
            'notes'          => $notes,
            'status'         => 'confirmed',
        ],
        [ '%s', '%d', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%s', '%s' ]
    );

    if ( ! $inserted ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'Rezervasyon kaydedilirken bir veritabanı hatası oluştu.' ], 500 );
    }

    return new WP_REST_Response( [
        'success'      => true,
        'booking_code' => $code,
        'court_name'   => $court->post_title,
        'date'         => date( 'd.m.Y', strtotime( $date ) ),
        'time_slot'    => $slot,
        'referee'      => (bool) $referee,
        'total_price'  => $total,
        'total_label'  => apex_format_tl( $total ),
        'message'      => sprintf( 'Salon rezervasyonunuz onaylandı! Rezervasyon Kodunuz: %s', $code ),
    ], 201 );
}

/**
 * PT Eğitmen Online Randevu & Rezervasyon
 */
function apex_api_book_pt( WP_REST_Request $req ) {
    global $wpdb;
    $p = $req->get_json_params();

    $trainer_id = absint( $p['trainer_id'] ?? 0 );
    $package_id = absint( $p['package_id'] ?? 0 );
    $type       = in_array( $p['training_type'] ?? '', [ 'bireysel', 'grupsal' ], true ) ? $p['training_type'] : 'bireysel';
    $date       = sanitize_text_field( $p['date'] ?? '' );
    $slot       = sanitize_text_field( $p['time_slot'] ?? '' );
    $name       = sanitize_text_field( $p['customer_name'] ?? '' );
    $email      = sanitize_email( $p['customer_email'] ?? '' );
    $phone      = sanitize_text_field( $p['customer_phone'] ?? '' );
    $goal       = sanitize_text_field( $p['goal'] ?? '' );
    $notes      = sanitize_textarea_field( $p['notes'] ?? '' );

    if ( ! $trainer_id || ! $date || ! $slot || ! $name || ! is_email( $email ) || ! $phone ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'Lütfen antrenör, tarih, saat ve iletişim bilgilerini eksiksiz doldurun.' ], 400 );
    }

    $trainer = get_post( $trainer_id );
    if ( ! $trainer || $trainer->post_type !== 'apex_trainer' ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'Seçilen antrenör bulunamadı.' ], 404 );
    }

    $exists = (int) $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}apex_pt_appointments WHERE trainer_id = %d AND appointment_date = %s AND time_slot = %s AND status != 'cancelled'",
        $trainer_id, $date, $slot
    ) );
    if ( $exists > 0 ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'Seçilen antrenörün bu saat dilimi dolu. Lütfen farklı bir saat seçin.' ], 409 );
    }

    $pkg_name = 'Tek Seans Değerlendirme';
    $price    = (int) get_post_meta( $trainer_id, '_apex_session_fee', true ) ?: 950;
    if ( $package_id ) {
        $pkg = get_post( $package_id );
        if ( $pkg && $pkg->post_type === 'apex_pt_package' ) {
            $pkg_name = $pkg->post_title;
            $price    = (int) get_post_meta( $package_id, '_apex_total_price', true ) ?: $price;
            $type     = get_post_meta( $package_id, '_apex_training_type', true ) ?: $type;
        }
    }

    $code = 'APX-PT-' . strtoupper( substr( md5( uniqid( '', true ) ), 0, 6 ) );

    $inserted = $wpdb->insert(
        $wpdb->prefix . 'apex_pt_appointments',
        [
            'booking_code'     => $code,
            'trainer_id'       => $trainer_id,
            'trainer_name'     => $trainer->post_title,
            'training_type'    => $type,
            'package_id'       => $package_id,
            'package_name'     => $pkg_name,
            'user_id'          => get_current_user_id(),
            'customer_name'    => $name,
            'customer_email'   => $email,
            'customer_phone'   => $phone,
            'appointment_date' => $date,
            'time_slot'        => $slot,
            'price'            => $price,
            'goal'             => $goal,
            'notes'            => $notes,
            'status'           => 'confirmed',
        ],
        [ '%s', '%d', '%s', '%s', '%d', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s' ]
    );

    if ( ! $inserted ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'Randevu kaydedilirken hata oluştu.' ], 500 );
    }

    return new WP_REST_Response( [
        'success'      => true,
        'booking_code' => $code,
        'trainer_name' => $trainer->post_title,
        'package_name' => $pkg_name,
        'date'         => date( 'd.m.Y', strtotime( $date ) ),
        'time_slot'    => $slot,
        'price_label'  => apex_format_tl( $price ),
        'message'      => sprintf( 'PT randevunuz onaylandı! Randevu Kodunuz: %s', $code ),
    ], 201 );
}

/**
 * Üyelik Online Kayıt & Paket Başvurusu
 */
function apex_api_membership_register( WP_REST_Request $req ) {
    global $wpdb;
    $p = $req->get_json_params();

    $plans     = apex_get_membership_plans();
    $plan_slug = sanitize_key( $p['plan_slug'] ?? 'pro' );
    if ( ! isset( $plans[ $plan_slug ] ) ) {
        $plan_slug = 'pro';
    }
    $plan    = $plans[ $plan_slug ];
    $billing = ( ( $p['billing_cycle'] ?? 'monthly' ) === 'annual' ) ? 'annual' : 'monthly';
    $price   = ( $billing === 'annual' ) ? $plan['annual_price'] : $plan['monthly_price'];

    $full_name = sanitize_text_field( $p['full_name'] ?? '' );
    $email     = sanitize_email( $p['email'] ?? '' );
    $phone     = sanitize_text_field( $p['phone'] ?? '' );
    $password  = (string) ( $p['password'] ?? '' );
    $goal      = sanitize_text_field( $p['goal'] ?? '' );
    $emergency = sanitize_text_field( $p['emergency_contact'] ?? '' );
    $kvkk      = ! empty( $p['kvkk_consent'] );
    $terms     = ! empty( $p['terms_consent'] );

    if ( ! $full_name || ! is_email( $email ) || ! $phone ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'Ad Soyad, geçerli e-posta ve telefon alanları zorunludur.' ], 400 );
    }
    if ( ! $kvkk || ! $terms ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'Devam etmek için KVKK Aydınlatma Metni ve Üyelik Sözleşmesini onaylamalısınız.' ], 400 );
    }

    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        if ( email_exists( $email ) ) {
            return new WP_REST_Response( [ 'success' => false, 'message' => 'Bu e-posta adresi ile kayıtlı bir üye zaten var. Lütfen Üye Girişi sekmesinden giriş yapın.' ], 409 );
        }
        if ( strlen( $password ) < 6 ) {
            return new WP_REST_Response( [ 'success' => false, 'message' => 'Şifreniz en az 6 karakter olmalıdır.' ], 400 );
        }
        $username = sanitize_user( current( explode( '@', $email ) ), true );
        if ( username_exists( $username ) ) {
            $username .= wp_rand( 100, 999 );
        }
        $user_id = wp_create_user( $username, $password, $email );
        if ( is_wp_error( $user_id ) ) {
            return new WP_REST_Response( [ 'success' => false, 'message' => $user_id->get_error_message() ], 400 );
        }
        wp_update_user( [ 'ID' => $user_id, 'display_name' => $full_name ] );
        wp_set_current_user( $user_id );
        wp_set_auth_cookie( $user_id, true );
    }

    $member_code = 'APX-2026-' . strtoupper( substr( md5( $user_id . uniqid( '', true ) ), 0, 5 ) );
    $end_date    = ( $billing === 'annual' ) ? date( 'Y-m-d', strtotime( '+365 days' ) ) : date( 'Y-m-d', strtotime( '+30 days' ) );

    update_user_meta( $user_id, 'apex_membership_plan', $plan['name'] );
    update_user_meta( $user_id, 'apex_membership_status', 'active' );
    update_user_meta( $user_id, 'apex_membership_start', current_time( 'Y-m-d' ) );
    update_user_meta( $user_id, 'apex_membership_end', $end_date );
    update_user_meta( $user_id, 'apex_member_phone', $phone );
    update_user_meta( $user_id, 'apex_member_code', $member_code );
    update_user_meta( $user_id, 'apex_member_goal', $goal );

    $wpdb->insert(
        $wpdb->prefix . 'apex_membership_applications',
        [
            'member_code'       => $member_code,
            'user_id'           => $user_id,
            'plan_slug'         => $plan_slug,
            'plan_name'         => $plan['name'],
            'billing_cycle'     => $billing,
            'price'             => $price,
            'full_name'         => $full_name,
            'email'             => $email,
            'phone'             => $phone,
            'goal'              => $goal,
            'emergency_contact' => $emergency,
            'kvkk_consent'      => 1,
            'terms_consent'     => 1,
            'status'            => 'active',
        ],
        [ '%s', '%d', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s' ]
    );

    return new WP_REST_Response( [
        'success'     => true,
        'member_code' => $member_code,
        'plan_name'   => $plan['name'],
        'message'     => "Hoş geldin {$full_name}! {$plan['name']} üyeliğin ve Dijital QR Kartın ({$member_code}) oluşturuldu.",
        'redirect'    => home_url( '/uyelik/?panel=1' ),
    ], 201 );
}

/**
 * Üye Girişi
 */
function apex_api_membership_login( WP_REST_Request $req ) {
    $p = $req->get_json_params();
    $login    = sanitize_text_field( $p['username'] ?? '' );
    $password = (string) ( $p['password'] ?? '' );

    if ( ! $login || ! $password ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'Kullanıcı adı/e-posta ve şifre gereklidir.' ], 400 );
    }

    $user = wp_signon( [
        'user_login'    => $login,
        'user_password' => $password,
        'remember'      => true,
    ], is_ssl() );

    if ( is_wp_error( $user ) ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'Giriş bilgileri hatalı. Demo üye için uye_demo / ApexUye2026! kullanabilirsiniz.' ], 401 );
    }

    return new WP_REST_Response( [
        'success'  => true,
        'message'  => 'Giriş başarılı! Üye paneliniz yükleniyor...',
        'redirect' => home_url( '/uyelik/?panel=1' ),
    ], 200 );
}

/**
 * Üye Panelinden Rezervasyon İptali
 */
function apex_api_cancel_booking( WP_REST_Request $req ) {
    global $wpdb;
    $p    = $req->get_json_params();
    $kind = ( ( $p['kind'] ?? '' ) === 'pt' ) ? 'pt' : 'court';
    $id   = absint( $p['id'] ?? 0 );
    $uid  = get_current_user_id();

    $table = ( $kind === 'pt' ) ? $wpdb->prefix . 'apex_pt_appointments' : $wpdb->prefix . 'apex_court_bookings';
    $where = current_user_can( 'manage_options' ) ? [ 'id' => $id ] : [ 'id' => $id, 'user_id' => $uid ];
    $updated = $wpdb->update( $table, [ 'status' => 'cancelled' ], $where );

    if ( $updated === false ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'İptal işlemi başarısız oldu.' ], 400 );
    }
    return new WP_REST_Response( [ 'success' => true, 'message' => 'Rezervasyonunuz iptal edildi.' ], 200 );
}

/**
 * İletişim Formu Gönderimi
 */
function apex_api_contact_submit( WP_REST_Request $req ) {
    global $wpdb;
    $p = $req->get_json_params();

    $name    = sanitize_text_field( $p['full_name'] ?? '' );
    $email   = sanitize_email( $p['email'] ?? '' );
    $phone   = sanitize_text_field( $p['phone'] ?? '' );
    $subject = sanitize_text_field( $p['subject'] ?? 'Genel Bilgi Talebi' );
    $message = sanitize_textarea_field( $p['message'] ?? '' );
    $kvkk    = ! empty( $p['kvkk_consent'] );

    if ( ! $name || ! is_email( $email ) || ! $message ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'Ad Soyad, geçerli e-posta ve mesaj alanları zorunludur.' ], 400 );
    }
    if ( ! $kvkk ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'Lütfen KVKK Aydınlatma Metnini onaylayın.' ], 400 );
    }

    $wpdb->insert(
        $wpdb->prefix . 'apex_contact_messages',
        [
            'full_name'    => $name,
            'email'        => $email,
            'phone'        => $phone,
            'subject'      => $subject,
            'message'      => $message,
            'kvkk_consent' => 1,
        ],
        [ '%s', '%s', '%s', '%s', '%s', '%d' ]
    );

    return new WP_REST_Response( [ 'success' => true, 'message' => 'Mesajınız başarıyla iletildi! Uzman ekibimiz en kısa sürede sizinle iletişime geçecektir.' ], 201 );
}

/**
 * Hibrit AI Chatbot REST Endpoint
 */
function apex_api_chat( WP_REST_Request $req ) {
    $p       = $req->get_json_params();
    $message = sanitize_textarea_field( $p['message'] ?? '' );
    $history = is_array( $p['history'] ?? null ) ? $p['history'] : [];
    $session = sanitize_text_field( $p['session_id'] ?? '' );

    if ( ! $message ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'Lütfen bir soru yazın.' ], 400 );
    }

    $res = apex_ai_handle_message( $message, $history, $session );
    return new WP_REST_Response( array_merge( [ 'success' => true ], $res ), 200 );
}
