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

    // v2.0.0 — Üye Yorumları, Kafe Ön Siparişi, Antrenman Kaydı & Koç-Öğrenci Sistemi
    register_rest_route( 'apex/v1', '/reviews', [
        [
            'methods'             => 'GET',
            'callback'            => 'apex_api_get_reviews',
            'permission_callback' => '__return_true',
        ],
        [
            'methods'             => 'POST',
            'callback'            => 'apex_api_submit_review',
            'permission_callback' => '__return_true',
        ],
    ] );

    register_rest_route( 'apex/v1', '/cafe-order', [
        'methods'             => 'POST',
        'callback'            => 'apex_api_submit_cafe_order',
        'permission_callback' => '__return_true',
    ] );

    register_rest_route( 'apex/v1', '/workout-log', [
        'methods'             => 'POST',
        'callback'            => 'apex_api_add_workout_log',
        'permission_callback' => function() {
            return is_user_logged_in();
        },
    ] );

    register_rest_route( 'apex/v1', '/member/message', [
        'methods'             => 'POST',
        'callback'            => 'apex_api_member_send_message',
        'permission_callback' => function() {
            return is_user_logged_in();
        },
    ] );

    register_rest_route( 'apex/v1', '/coach/save-student', [
        'methods'             => 'POST',
        'callback'            => 'apex_api_coach_save_student',
        'permission_callback' => function() {
            return current_user_can( 'apex_coach_access' ) || current_user_can( 'manage_options' );
        },
    ] );

    register_rest_route( 'apex/v1', '/coach/message', [
        'methods'             => 'POST',
        'callback'            => 'apex_api_coach_send_message',
        'permission_callback' => function() {
            return current_user_can( 'apex_coach_access' ) || current_user_can( 'manage_options' );
        },
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

/**
 * v2.0.0 — Üye Yorumları & Puanlama (GET / POST)
 */
function apex_api_get_reviews() {
    global $wpdb;
    $rows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}apex_reviews WHERE status = 'published' ORDER BY id DESC LIMIT 30" );
    $avg  = (float) $wpdb->get_var( "SELECT COALESCE(AVG(rating), 5) FROM {$wpdb->prefix}apex_reviews WHERE status = 'published'" );
    return new WP_REST_Response( [
        'success' => true,
        'count'   => count( $rows ),
        'average' => round( $avg, 1 ),
        'reviews' => $rows,
    ], 200 );
}

function apex_api_submit_review( WP_REST_Request $req ) {
    global $wpdb;
    $p       = $req->get_json_params();
    $name    = sanitize_text_field( $p['author_name'] ?? '' );
    $plan    = sanitize_text_field( $p['member_plan'] ?? 'APEX PRO' );
    $rating  = max( 1, min( 5, absint( $p['rating'] ?? 5 ) ) );
    $comment = sanitize_textarea_field( $p['comment'] ?? '' );

    if ( ! $name || strlen( $comment ) < 8 ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'Lütfen adınızı ve en az 8 karakterlik yorumunuzu yazın.' ], 400 );
    }

    $wpdb->insert(
        $wpdb->prefix . 'apex_reviews',
        [
            'author_name' => $name,
            'member_plan' => $plan,
            'rating'      => $rating,
            'comment'     => $comment,
            'status'      => 'published',
        ],
        [ '%s', '%s', '%d', '%s', '%s' ]
    );

    $avg   = (float) $wpdb->get_var( "SELECT COALESCE(AVG(rating), 5) FROM {$wpdb->prefix}apex_reviews WHERE status = 'published'" );
    $count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}apex_reviews WHERE status = 'published'" );

    return new WP_REST_Response( [
        'success' => true,
        'message' => 'Değerlendirmeniz yayınlandı! Görüşünüz için teşekkür ederiz.',
        'review'  => [
            'id'          => (int) $wpdb->insert_id,
            'author_name' => $name,
            'member_plan' => $plan,
            'rating'      => $rating,
            'comment'     => $comment,
            'created_at'  => current_time( 'd.m.Y' ),
        ],
        'average' => round( $avg, 1 ),
        'count'   => $count,
    ], 201 );
}

/**
 * v2.0.0 — Apex Protein Kafe Antrenman Çıkışı Ön Siparişi
 */
function apex_api_submit_cafe_order( WP_REST_Request $req ) {
    global $wpdb;
    $p      = $req->get_json_params();
    $name   = sanitize_text_field( $p['customer_name'] ?? '' );
    $phone  = sanitize_text_field( $p['customer_phone'] ?? '' );
    $pickup = sanitize_text_field( $p['pickup_time'] ?? 'Antrenman Çıkışı (20 dk)' );
    $notes  = sanitize_textarea_field( $p['notes'] ?? '' );
    $items  = is_array( $p['items'] ?? null ) ? $p['items'] : [];

    if ( ! $name || ! $phone || empty( $items ) ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'Sepetinizde en az 1 ürün olmalı ve ad/telefon bilgileri girilmelidir.' ], 400 );
    }

    $clean_items   = [];
    $total_price   = 0;
    $total_kcal    = 0;
    $total_protein = 0;
    foreach ( $items as $it ) {
        $qty = max( 1, min( 10, absint( $it['qty'] ?? 1 ) ) );
        $pr  = max( 0, absint( $it['price'] ?? 0 ) );
        $kc  = max( 0, absint( $it['kcal'] ?? 0 ) );
        $pro = max( 0, absint( $it['protein'] ?? 0 ) );
        $clean_items[] = [
            'name'    => sanitize_text_field( $it['name'] ?? '' ),
            'qty'     => $qty,
            'price'   => $pr,
            'kcal'    => $kc,
            'protein' => $pro,
        ];
        $total_price   += $pr * $qty;
        $total_kcal    += $kc * $qty;
        $total_protein += $pro * $qty;
    }

    $code = 'APX-KFE-' . strtoupper( substr( md5( uniqid( '', true ) ), 0, 5 ) );
    $wpdb->insert(
        $wpdb->prefix . 'apex_cafe_orders',
        [
            'order_code'    => $code,
            'user_id'       => get_current_user_id(),
            'customer_name' => $name,
            'customer_phone'=> $phone,
            'pickup_time'   => $pickup,
            'items_json'    => wp_json_encode( $clean_items, JSON_UNESCAPED_UNICODE ),
            'total_kcal'    => $total_kcal,
            'total_protein' => $total_protein,
            'total_price'   => $total_price,
            'notes'         => $notes,
            'status'        => 'preparing',
        ],
        [ '%s', '%d', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%s', '%s' ]
    );

    return new WP_REST_Response( [
        'success'       => true,
        'order_code'    => $code,
        'total_label'   => apex_format_tl( $total_price ),
        'total_kcal'    => $total_kcal,
        'total_protein' => $total_protein,
        'pickup_time'   => $pickup,
        'message'       => "Siparişiniz alındı ({$code})! {$pickup} teslim alınmak üzere baristalarımız hazırlıyor.",
    ], 201 );
}

/**
 * v2.0.0 — Üye Paneli Antrenman Kaydı Ekleme
 */
function apex_api_add_workout_log( WP_REST_Request $req ) {
    global $wpdb;
    $p    = $req->get_json_params();
    $uid  = get_current_user_id();
    $type = sanitize_text_field( $p['workout_type'] ?? 'Fonksiyonel Antrenman' );
    $dur  = max( 10, min( 240, absint( $p['duration_min'] ?? 60 ) ) );
    $kcal = max( 40, min( 2500, absint( $p['calories'] ?? ( $dur * 8 ) ) ) );
    $note = sanitize_text_field( $p['notes'] ?? '' );
    $date = sanitize_text_field( $p['workout_date'] ?? current_time( 'Y-m-d' ) );

    $wpdb->insert(
        $wpdb->prefix . 'apex_workout_logs',
        [
            'user_id'      => $uid,
            'workout_date' => $date,
            'workout_type' => $type,
            'duration_min' => $dur,
            'calories'     => $kcal,
            'notes'        => $note,
        ],
        [ '%d', '%s', '%s', '%d', '%d', '%s' ]
    );

    // İlgili koç-öğrenci ilerlemesini de +2% güncel tut
    $wpdb->query( $wpdb->prepare(
        "UPDATE {$wpdb->prefix}apex_coach_students SET sessions_done = LEAST(sessions_planned, sessions_done + 1), progress_pct = LEAST(100, progress_pct + 2), progress_trend = 'up', updated_at = NOW() WHERE student_user_id = %d",
        $uid
    ) );

    return new WP_REST_Response( [
        'success' => true,
        'message' => "Harika! {$type} ({$dur} dk · {$kcal} kcal) antrenman günlüğüne eklendi.",
        'entry'   => [
            'workout_date' => date( 'd.m.Y', strtotime( $date ) ),
            'workout_type' => $type,
            'duration_min' => $dur,
            'calories'     => $kcal,
            'notes'        => $note,
        ],
    ], 201 );
}

/**
 * v2.0.0 — Öğrenci → Koç Birebir Mesaj Gönderimi (Üye Panelinden)
 */
function apex_api_member_send_message( WP_REST_Request $req ) {
    global $wpdb;
    $p   = $req->get_json_params();
    $uid = get_current_user_id();
    $cu  = wp_get_current_user();
    $msg = sanitize_textarea_field( $p['message'] ?? '' );

    if ( ! $msg ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'Lütfen mesajınızı yazın.' ], 400 );
    }

    $student = $wpdb->get_row( $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}apex_coach_students WHERE student_user_id = %d OR student_email = %s LIMIT 1",
        $uid, $cu->user_email
    ) );

    if ( ! $student ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'Henüz atanmış bir koçunuz bulunmuyor.' ], 404 );
    }

    $wpdb->insert(
        $wpdb->prefix . 'apex_coach_messages',
        [
            'student_id'    => $student->id,
            'coach_user_id' => $student->coach_user_id,
            'sender_role'   => 'student',
            'sender_name'   => $cu->display_name,
            'message'       => $msg,
            'is_read'       => 0,
        ],
        [ '%d', '%d', '%s', '%s', '%s', '%d' ]
    );

    return new WP_REST_Response( [
        'success' => true,
        'message' => 'Mesajınız koçunuza iletildi.',
        'item'    => [
            'sender_role' => 'student',
            'sender_name' => $cu->display_name,
            'message'     => $msg,
            'time'        => current_time( 'd.m H:i' ),
        ],
    ], 201 );
}

/**
 * v2.0.0 — Koç Panelinden Öğrenci Durumu + Öneri + 7 Günlük Beslenme Takvimi Kaydetme
 * Sadece ilgili koçun kendi öğrencisi (veya Admin) güncellenebilir!
 */
function apex_api_coach_save_student( WP_REST_Request $req ) {
    global $wpdb;
    $p          = $req->get_json_params();
    $student_id = absint( $p['student_id'] ?? 0 );
    $uid        = get_current_user_id();
    $is_admin   = current_user_can( 'manage_options' );

    $student = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}apex_coach_students WHERE id = %d", $student_id ) );
    if ( ! $student ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'Öğrenci kaydı bulunamadı.' ], 404 );
    }
    if ( ! $is_admin && (int) $student->coach_user_id !== $uid ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'Yetkisiz işlem: Yalnızca kendi öğrencilerinizi düzenleyebilirsiniz.' ], 403 );
    }

    $allowed_st = [ 'hedefte', 'dikkat', 'yeni', 'riskli' ];
    $status     = in_array( $p['status'] ?? '', $allowed_st, true ) ? $p['status'] : $student->status;
    $prog       = max( 0, min( 100, absint( $p['progress_pct'] ?? $student->progress_pct ) ) );
    $adh        = max( 0, min( 100, absint( $p['adherence_pct'] ?? $student->adherence_pct ) ) );
    $trend      = ( ( $p['progress_trend'] ?? 'up' ) === 'down' ) ? 'down' : 'up';
    $cur_w      = (float) ( $p['current_weight'] ?? $student->current_weight );
    $tar_w      = (float) ( $p['target_weight'] ?? $student->target_weight );
    $advice     = sanitize_textarea_field( $p['coach_advice'] ?? $student->coach_advice );

    $wpdb->update(
        $wpdb->prefix . 'apex_coach_students',
        [
            'status'         => $status,
            'progress_pct'   => $prog,
            'progress_trend' => $trend,
            'adherence_pct'  => $adh,
            'current_weight' => $cur_w,
            'target_weight'  => $tar_w,
            'coach_advice'   => $advice,
            'updated_at'     => current_time( 'mysql' ),
        ],
        [ 'id' => $student_id ]
    );

    // Beslenme takvimini de kaydet/güncelle
    if ( isset( $p['nutrition'] ) && is_array( $p['nutrition'] ) ) {
        $n     = $p['nutrition'];
        $meals = [
            'sabah'     => sanitize_textarea_field( $n['sabah'] ?? '' ),
            'ara1'      => sanitize_textarea_field( $n['ara1'] ?? '' ),
            'ogle'      => sanitize_textarea_field( $n['ogle'] ?? '' ),
            'ant_sonra' => sanitize_textarea_field( $n['ant_sonra'] ?? '' ),
            'aksam'     => sanitize_textarea_field( $n['aksam'] ?? '' ),
        ];
        $t_nut = $wpdb->prefix . 'apex_nutrition_plans';
        $has   = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$t_nut} WHERE student_id = %d", $student_id ) );
        $data  = [
            'student_id'    => $student_id,
            'coach_user_id' => (int) $student->coach_user_id,
            'daily_kcal'    => max( 1000, min( 6000, absint( $n['daily_kcal'] ?? 2200 ) ) ),
            'protein_g'     => max( 40, min( 350, absint( $n['protein_g'] ?? 160 ) ) ),
            'carbs_g'       => max( 40, min( 600, absint( $n['carbs_g'] ?? 220 ) ) ),
            'fat_g'         => max( 20, min( 200, absint( $n['fat_g'] ?? 65 ) ) ),
            'water_l'       => max( 1.0, min( 7.0, (float) ( $n['water_l'] ?? 3.0 ) ) ),
            'meals_json'    => wp_json_encode( $meals, JSON_UNESCAPED_UNICODE ),
            'weekly_notes'  => sanitize_textarea_field( $n['weekly_notes'] ?? '' ),
            'updated_at'    => current_time( 'mysql' ),
        ];
        if ( $has > 0 ) {
            $wpdb->update( $t_nut, $data, [ 'student_id' => $student_id ] );
        } else {
            $wpdb->insert( $t_nut, $data );
        }
    }

    return new WP_REST_Response( [
        'success' => true,
        'message' => "{$student->student_name} için durum, koç önerisi ve beslenme takvimi güncellendi (Üye paneline anında yansıdı).",
    ], 200 );
}

/**
 * v2.0.0 — Koç → Öğrenci Birebir Mesaj Gönderimi (Koç Panelinden)
 */
function apex_api_coach_send_message( WP_REST_Request $req ) {
    global $wpdb;
    $p          = $req->get_json_params();
    $student_id = absint( $p['student_id'] ?? 0 );
    $msg        = sanitize_textarea_field( $p['message'] ?? '' );
    $uid        = get_current_user_id();
    $cu         = wp_get_current_user();
    $is_admin   = current_user_can( 'manage_options' );

    if ( ! $student_id || ! $msg ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'Öğrenci ve mesaj alanı zorunludur.' ], 400 );
    }

    $student = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}apex_coach_students WHERE id = %d", $student_id ) );
    if ( ! $student ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'Öğrenci bulunamadı.' ], 404 );
    }
    if ( ! $is_admin && (int) $student->coach_user_id !== $uid ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'Yetkisiz işlem: Yalnızca kendi öğrencilerinize mesaj gönderebilirsiniz.' ], 403 );
    }

    $sender = $is_admin && (int) $student->coach_user_id !== $uid ? ( $student->coach_name . ' (Yönetim)' ) : $cu->display_name;
    $wpdb->insert(
        $wpdb->prefix . 'apex_coach_messages',
        [
            'student_id'    => $student_id,
            'coach_user_id' => (int) $student->coach_user_id,
            'sender_role'   => 'coach',
            'sender_name'   => $sender,
            'message'       => $msg,
            'is_read'       => 0,
        ],
        [ '%d', '%d', '%s', '%s', '%s', '%d' ]
    );

    return new WP_REST_Response( [
        'success' => true,
        'message' => 'Mesaj öğrenciye iletildi.',
        'item'    => [
            'sender_role' => 'coach',
            'sender_name' => $sender,
            'message'     => $msg,
            'time'        => current_time( 'd.m H:i' ),
        ],
    ], 201 );
}
