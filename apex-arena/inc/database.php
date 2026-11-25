<?php
/**
 * Özel MySQL Tabloları — v2.0.0
 * Salon Rezervasyonları, PT Randevuları, Üyelikler, Chatbot, İletişim,
 * Koç-Öğrenci Takibi, Beslenme Takvimleri, Birebir Koç Mesajlaşması, Üye Yorumları, Kafe Siparişleri ve Antrenman Kayıtları
 *
 * @package ApexArena
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function apex_create_custom_tables() {
    global $wpdb;
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $charset = $wpdb->get_charset_collate();

    // 1. Salon Kiralama Rezervasyonları
    $t_courts = $wpdb->prefix . 'apex_court_bookings';
    $sql_courts = "CREATE TABLE {$t_courts} (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        booking_code VARCHAR(32) NOT NULL,
        court_id BIGINT(20) UNSIGNED NOT NULL,
        court_name VARCHAR(150) NOT NULL,
        court_sport VARCHAR(40) NOT NULL DEFAULT 'basketbol',
        user_id BIGINT(20) UNSIGNED DEFAULT 0,
        customer_name VARCHAR(120) NOT NULL,
        customer_email VARCHAR(150) NOT NULL,
        customer_phone VARCHAR(40) NOT NULL,
        booking_date DATE NOT NULL,
        time_slot VARCHAR(20) NOT NULL,
        duration_hours TINYINT(2) NOT NULL DEFAULT 1,
        needs_referee TINYINT(1) NOT NULL DEFAULT 0,
        referee_fee INT(11) NOT NULL DEFAULT 0,
        base_price INT(11) NOT NULL DEFAULT 0,
        total_price INT(11) NOT NULL DEFAULT 0,
        notes TEXT NULL,
        status VARCHAR(30) NOT NULL DEFAULT 'confirmed',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY booking_code (booking_code),
        KEY court_date_slot (court_id, booking_date, time_slot),
        KEY user_idx (user_id)
    ) {$charset};";

    // 2. PT Eğitmen Randevuları
    $t_pt = $wpdb->prefix . 'apex_pt_appointments';
    $sql_pt = "CREATE TABLE {$t_pt} (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        booking_code VARCHAR(32) NOT NULL,
        trainer_id BIGINT(20) UNSIGNED NOT NULL,
        trainer_name VARCHAR(120) NOT NULL,
        training_type VARCHAR(30) NOT NULL DEFAULT 'bireysel',
        package_id BIGINT(20) UNSIGNED DEFAULT 0,
        package_name VARCHAR(150) NOT NULL,
        user_id BIGINT(20) UNSIGNED DEFAULT 0,
        customer_name VARCHAR(120) NOT NULL,
        customer_email VARCHAR(150) NOT NULL,
        customer_phone VARCHAR(40) NOT NULL,
        appointment_date DATE NOT NULL,
        time_slot VARCHAR(20) NOT NULL,
        price INT(11) NOT NULL DEFAULT 0,
        goal VARCHAR(120) DEFAULT '',
        notes TEXT NULL,
        status VARCHAR(30) NOT NULL DEFAULT 'confirmed',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY booking_code (booking_code),
        KEY trainer_date_slot (trainer_id, appointment_date, time_slot),
        KEY user_idx (user_id)
    ) {$charset};";

    // 3. Üyelik Başvuruları
    $t_mem = $wpdb->prefix . 'apex_membership_applications';
    $sql_mem = "CREATE TABLE {$t_mem} (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        member_code VARCHAR(32) NOT NULL,
        user_id BIGINT(20) UNSIGNED DEFAULT 0,
        plan_slug VARCHAR(40) NOT NULL,
        plan_name VARCHAR(120) NOT NULL,
        billing_cycle VARCHAR(20) NOT NULL DEFAULT 'monthly',
        price INT(11) NOT NULL DEFAULT 0,
        full_name VARCHAR(120) NOT NULL,
        email VARCHAR(150) NOT NULL,
        phone VARCHAR(40) NOT NULL,
        goal VARCHAR(150) DEFAULT '',
        emergency_contact VARCHAR(150) DEFAULT '',
        kvkk_consent TINYINT(1) NOT NULL DEFAULT 1,
        terms_consent TINYINT(1) NOT NULL DEFAULT 1,
        status VARCHAR(30) NOT NULL DEFAULT 'active',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY member_code (member_code),
        KEY user_idx (user_id)
    ) {$charset};";

    // 4. AI Chatbot Konuşma Geçmişi
    $t_chat = $wpdb->prefix . 'apex_chatbot_logs';
    $sql_chat = "CREATE TABLE {$t_chat} (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        session_id VARCHAR(64) NOT NULL,
        user_id BIGINT(20) UNSIGNED DEFAULT 0,
        user_message TEXT NOT NULL,
        bot_reply TEXT NOT NULL,
        ai_mode VARCHAR(30) NOT NULL DEFAULT 'hybrid-local',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY session_idx (session_id)
    ) {$charset};";

    // 5. İletişim Formu Mesajları
    $t_contact = $wpdb->prefix . 'apex_contact_messages';
    $sql_contact = "CREATE TABLE {$t_contact} (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        full_name VARCHAR(120) NOT NULL,
        email VARCHAR(150) NOT NULL,
        phone VARCHAR(40) DEFAULT '',
        subject VARCHAR(160) NOT NULL,
        message TEXT NOT NULL,
        kvkk_consent TINYINT(1) NOT NULL DEFAULT 1,
        status VARCHAR(30) NOT NULL DEFAULT 'new',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) {$charset};";

    // 6. Koç-Öğrenci Atama & Durum Takibi (v2.0.0)
    $t_students = $wpdb->prefix . 'apex_coach_students';
    $sql_students = "CREATE TABLE {$t_students} (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        coach_user_id BIGINT(20) UNSIGNED NOT NULL,
        coach_name VARCHAR(120) NOT NULL,
        student_user_id BIGINT(20) UNSIGNED DEFAULT 0,
        student_name VARCHAR(120) NOT NULL,
        student_email VARCHAR(150) NOT NULL,
        goal VARCHAR(140) NOT NULL DEFAULT 'Genel Kondisyon',
        status VARCHAR(30) NOT NULL DEFAULT 'hedefte',
        progress_pct TINYINT(3) NOT NULL DEFAULT 70,
        progress_trend VARCHAR(10) NOT NULL DEFAULT 'up',
        adherence_pct TINYINT(3) NOT NULL DEFAULT 82,
        current_weight DECIMAL(5,1) DEFAULT 78.0,
        target_weight DECIMAL(5,1) DEFAULT 74.0,
        sessions_planned INT(11) NOT NULL DEFAULT 12,
        sessions_done INT(11) NOT NULL DEFAULT 9,
        coach_advice TEXT NULL,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY coach_idx (coach_user_id),
        KEY student_user_idx (student_user_id)
    ) {$charset};";

    // 7. Koç Tarafından Hazırlanan Beslenme Takvimleri (v2.0.0)
    $t_nutrition = $wpdb->prefix . 'apex_nutrition_plans';
    $sql_nutrition = "CREATE TABLE {$t_nutrition} (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        student_id BIGINT(20) UNSIGNED NOT NULL,
        coach_user_id BIGINT(20) UNSIGNED NOT NULL,
        daily_kcal INT(11) NOT NULL DEFAULT 2250,
        protein_g INT(11) NOT NULL DEFAULT 165,
        carbs_g INT(11) NOT NULL DEFAULT 220,
        fat_g INT(11) NOT NULL DEFAULT 65,
        water_l DECIMAL(3,1) NOT NULL DEFAULT 3.0,
        meals_json LONGTEXT NOT NULL,
        weekly_notes TEXT NULL,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY student_unique (student_id),
        KEY coach_idx (coach_user_id)
    ) {$charset};";

    // 8. Koç ↔ Öğrenci Birebir Mesajlaşma (v2.0.0)
    $t_cmsgs = $wpdb->prefix . 'apex_coach_messages';
    $sql_cmsgs = "CREATE TABLE {$t_cmsgs} (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        student_id BIGINT(20) UNSIGNED NOT NULL,
        coach_user_id BIGINT(20) UNSIGNED NOT NULL,
        sender_role VARCHAR(20) NOT NULL DEFAULT 'coach',
        sender_name VARCHAR(120) NOT NULL,
        message TEXT NOT NULL,
        is_read TINYINT(1) NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY student_idx (student_id),
        KEY coach_idx (coach_user_id)
    ) {$charset};";

    // 9. Üye Yorumları & Puanlama (v2.0.0)
    $t_reviews = $wpdb->prefix . 'apex_reviews';
    $sql_reviews = "CREATE TABLE {$t_reviews} (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        author_name VARCHAR(120) NOT NULL,
        member_plan VARCHAR(60) NOT NULL DEFAULT 'APEX PRO',
        rating TINYINT(1) NOT NULL DEFAULT 5,
        comment TEXT NOT NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'published',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY status_idx (status)
    ) {$charset};";

    // 10. Apex Protein Kafe Ön Siparişleri (v2.0.0)
    $t_cafe = $wpdb->prefix . 'apex_cafe_orders';
    $sql_cafe = "CREATE TABLE {$t_cafe} (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        order_code VARCHAR(32) NOT NULL,
        user_id BIGINT(20) UNSIGNED DEFAULT 0,
        customer_name VARCHAR(120) NOT NULL,
        customer_phone VARCHAR(40) NOT NULL,
        pickup_time VARCHAR(40) NOT NULL DEFAULT 'Antrenman Çıkışı (20 dk)',
        items_json LONGTEXT NOT NULL,
        total_kcal INT(11) NOT NULL DEFAULT 0,
        total_protein INT(11) NOT NULL DEFAULT 0,
        total_price INT(11) NOT NULL DEFAULT 0,
        notes TEXT NULL,
        status VARCHAR(30) NOT NULL DEFAULT 'preparing',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY order_code (order_code),
        KEY user_idx (user_id)
    ) {$charset};";

    // 11. Üye Paneli Antrenman & Hedef Kayıtları (v2.0.0)
    $t_workouts = $wpdb->prefix . 'apex_workout_logs';
    $sql_workouts = "CREATE TABLE {$t_workouts} (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        workout_date DATE NOT NULL,
        workout_type VARCHAR(80) NOT NULL,
        duration_min INT(11) NOT NULL DEFAULT 60,
        calories INT(11) NOT NULL DEFAULT 480,
        notes VARCHAR(255) DEFAULT '',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY user_date_idx (user_id, workout_date)
    ) {$charset};";

    // 12. Gmail SMTP & Otomatik Hatırlatma E-posta Kayıt Defteri (v2.1.0)
    $t_emails = $wpdb->prefix . 'apex_email_logs';
    $sql_emails = "CREATE TABLE {$t_emails} (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) UNSIGNED DEFAULT 0,
        recipient_email VARCHAR(150) NOT NULL,
        recipient_name VARCHAR(120) DEFAULT '',
        mail_type VARCHAR(40) NOT NULL DEFAULT 'welcome',
        ref_key VARCHAR(100) DEFAULT '',
        subject VARCHAR(255) NOT NULL,
        body_html LONGTEXT NOT NULL,
        delivery_mode VARCHAR(30) NOT NULL DEFAULT 'local_preview',
        status VARCHAR(30) NOT NULL DEFAULT 'queued_local',
        error_message TEXT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY user_idx (user_id),
        KEY type_idx (mail_type),
        KEY ref_idx (ref_key)
    ) {$charset};";

    dbDelta( $sql_courts );
    dbDelta( $sql_pt );
    dbDelta( $sql_mem );
    dbDelta( $sql_chat );
    dbDelta( $sql_contact );
    dbDelta( $sql_students );
    dbDelta( $sql_nutrition );
    dbDelta( $sql_cmsgs );
    dbDelta( $sql_reviews );
    dbDelta( $sql_cafe );
    dbDelta( $sql_workouts );
    dbDelta( $sql_emails );

    apex_setup_coach_roles_and_seed();
}

/**
 * Özel Koç Rolü, Demo Koç Hesapları (koc_kerem / koc_elif) ve v2.0.0 Gerçekçi Demo Verileri
 */
function apex_setup_coach_roles_and_seed() {
    global $wpdb;

    // 1. Özel 'apex_coach' WordPress rolü (sadece kendi koç paneline erişim)
    if ( ! get_role( 'apex_coach' ) ) {
        add_role( 'apex_coach', 'APEX Koç', [
            'read'              => true,
            'apex_coach_access' => true,
        ] );
    } else {
        $coach_role = get_role( 'apex_coach' );
        if ( $coach_role && ! $coach_role->has_cap( 'apex_coach_access' ) ) {
            $coach_role->add_cap( 'apex_coach_access' );
        }
    }
    $admin_role = get_role( 'administrator' );
    if ( $admin_role && ! $admin_role->has_cap( 'apex_coach_access' ) ) {
        $admin_role->add_cap( 'apex_coach_access' );
    }

    // 2. Demo Koç Hesaplarını Garanti Et (Toplam 7 Uzman Koç — Şifre: ApexKoc2026!)
    $coach_defs = [
        'koc_kerem'   => [ 'name' => 'Kerem Alkan',   'email' => 'kerem.alkan@apexarena.com.tr',   'title' => 'Baş Performans & Kuvvet Koçu (CSCS)' ],
        'koc_elif'    => [ 'name' => 'Elif Karaca',   'email' => 'elif.karaca@apexarena.com.tr',   'title' => 'HIIT, Metabolik Kondisyon & Beslenme Koçu' ],
        'koc_selin'   => [ 'name' => 'Selin Yılmaz',  'email' => 'selin.yilmaz@apexarena.com.tr',  'title' => 'Reformer Pilates, Mobilite & Core Koçu' ],
        'koc_mert'    => [ 'name' => 'Mert Demir',    'email' => 'mert.demir@apexarena.com.tr',    'title' => 'Basketbol & Atletik Patlayıcılık Koçu' ],
        'koc_caner'   => [ 'name' => 'Caner Kaya',    'email' => 'caner.kaya@apexarena.com.tr',    'title' => 'Voleybol & Dikey Sıçrama Performans Koçu' ],
        'koc_buse'    => [ 'name' => 'Buse Aydın',    'email' => 'buse.aydin@apexarena.com.tr',    'title' => 'Fonksiyonel Fitness & Yağ Yakımı Koçu' ],
        'koc_emirhan' => [ 'name' => 'Emirhan Çelik', 'email' => 'emirhan.celik@apexarena.com.tr', 'title' => 'Hipertrofi & Sporcu Beslenmesi Koçu' ],
    ];
    $coach_users = [];
    foreach ( $coach_defs as $login => $cdef ) {
        $u = get_user_by( 'login', $login );
        if ( ! $u ) {
            $uid = wp_create_user( $login, 'ApexKoc2026!', $cdef['email'] );
            if ( ! is_wp_error( $uid ) ) {
                wp_update_user( [ 'ID' => $uid, 'display_name' => $cdef['name'], 'role' => 'apex_coach' ] );
                update_user_meta( $uid, 'apex_coach_title', $cdef['title'] );
                $u = get_user_by( 'id', $uid );
            }
        }
        if ( $u ) {
            $coach_users[ $login ] = $u;
        }
    }

    $kerem    = $coach_users['koc_kerem'] ?? null;
    $elif     = $coach_users['koc_elif'] ?? null;
    $kerem_id = $kerem ? (int) $kerem->ID : 1;
    $elif_id  = $elif ? (int) $elif->ID : $kerem_id;
    $demo_u   = get_user_by( 'login', 'uye_demo' );
    $demo_uid = $demo_u ? (int) $demo_u->ID : 0;

    // 3. Koç-Öğrenci Takip Seed (Yalnızca tablo boşsa)
    $t_students = $wpdb->prefix . 'apex_coach_students';
    if ( (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$t_students}" ) === 0 ) {
        $seed_students = [
            [
                'coach_user_id'    => $kerem_id,
                'coach_name'       => 'Kerem Alkan',
                'student_user_id'  => $demo_uid,
                'student_name'     => $demo_u ? $demo_u->display_name : 'Demo Üye',
                'student_email'    => $demo_u ? $demo_u->user_email : 'uye@apexarena.com.tr',
                'goal'             => 'Kas Kütlesi & Atletik Kuvvet',
                'status'           => 'hedefte',
                'progress_pct'     => 86,
                'progress_trend'   => 'up',
                'adherence_pct'    => 91,
                'current_weight'   => 78.4,
                'target_weight'    => 76.0,
                'sessions_planned' => 12,
                'sessions_done'    => 10,
                'coach_advice'     => 'Bench press ve squat kuvvetinde harika ivme yakaladın! Bu hafta antrenman sonrası 35g protein + kompleks karbonhidrat alımını aksatma ve Çarşamba günü 15 dk mobilite ekle.',
            ],
            [
                'coach_user_id'    => $kerem_id,
                'coach_name'       => 'Kerem Alkan',
                'student_user_id'  => 0,
                'student_name'     => 'Caner Öztürk',
                'student_email'    => 'caner.ozturk@example.com',
                'goal'             => 'Basketbol Dikey Sıçrama',
                'status'           => 'hedefte',
                'progress_pct'     => 78,
                'progress_trend'   => 'up',
                'adherence_pct'    => 85,
                'current_weight'   => 84.0,
                'target_weight'    => 82.0,
                'sessions_planned' => 12,
                'sessions_done'    => 9,
                'coach_advice'     => 'Pliometrik kutu sıçrama serilerinde iniş kontrolün belirgin gelişti. Maç günlerinden önce bacak hacmini %20 hafifletelim.',
            ],
            [
                'coach_user_id'    => $kerem_id,
                'coach_name'       => 'Kerem Alkan',
                'student_user_id'  => 0,
                'student_name'     => 'Zeynep Kaya',
                'student_email'    => 'zeynep.kaya@example.com',
                'goal'             => 'Yağ Yakımı & Sıkılaşma',
                'status'           => 'dikkat',
                'progress_pct'     => 54,
                'progress_trend'   => 'down',
                'adherence_pct'    => 62,
                'current_weight'   => 66.5,
                'target_weight'    => 60.0,
                'sessions_planned' => 12,
                'sessions_done'    => 6,
                'coach_advice'     => 'Son 10 günde akşam karbonhidratı ve uyku düzeni biraz sarkmış. Günlük su hedefini 2.8L seviyesine sabitleyip bu hafta 2 ek kardiyo seansı planlayalım.',
            ],
            [
                'coach_user_id'    => $kerem_id,
                'coach_name'       => 'Kerem Alkan',
                'student_user_id'  => 0,
                'student_name'     => 'Burak Aydın',
                'student_email'    => 'burak.aydin@example.com',
                'goal'             => 'Omuz Rehabilitasyon & Postür',
                'status'           => 'yeni',
                'progress_pct'     => 38,
                'progress_trend'   => 'up',
                'adherence_pct'    => 79,
                'current_weight'   => 81.2,
                'target_weight'    => 79.0,
                'sessions_planned' => 8,
                'sessions_done'    => 3,
                'coach_advice'     => 'Rotator cuff ısınmalarını her antrenmandan önce bant ile 8 dakika uyguluyoruz. Ağrısız hareket açıklığı %90 seviyesine ulaştı.',
            ],
            [
                'coach_user_id'    => $kerem_id,
                'coach_name'       => 'Kerem Alkan',
                'student_user_id'  => 0,
                'student_name'     => 'Emre Şahin',
                'student_email'    => 'emre.sahin@example.com',
                'goal'             => 'Hipertrofi & 10K Kondisyon',
                'status'           => 'riskli',
                'progress_pct'     => 28,
                'progress_trend'   => 'down',
                'adherence_pct'    => 45,
                'current_weight'   => 92.4,
                'target_weight'    => 85.0,
                'sessions_planned' => 12,
                'sessions_done'    => 3,
                'coach_advice'     => 'Son 2 haftadır randevu katılımın düştü. İş yoğunluğuna göre seans saatlerini 07:30 sabah dilimine çekip daha sürdürülebilir bir tempo kuralım.',
            ],
            [
                'coach_user_id'    => $elif_id,
                'coach_name'       => 'Elif Karaca',
                'student_user_id'  => 0,
                'student_name'     => 'Selin Arslan',
                'student_email'    => 'selin.arslan@example.com',
                'goal'             => 'HIIT & Metabolik Yağ Yakımı',
                'status'           => 'hedefte',
                'progress_pct'     => 92,
                'progress_trend'   => 'up',
                'adherence_pct'    => 95,
                'current_weight'   => 59.8,
                'target_weight'    => 58.0,
                'sessions_planned' => 16,
                'sessions_done'    => 14,
                'coach_advice'     => 'Nabız toparlanma süren muhteşem! Hedef kiloya 1.8 kg kaldı, protein alımını koruyarak yüksek yoğunluklu interval bloklarını sürdürüyoruz.',
            ],
            [
                'coach_user_id'    => $elif_id,
                'coach_name'       => 'Elif Karaca',
                'student_user_id'  => 0,
                'student_name'     => 'Deniz Yıldız',
                'student_email'    => 'deniz.yildiz@example.com',
                'goal'             => 'Maraton Dayanıklılık',
                'status'           => 'dikkat',
                'progress_pct'     => 61,
                'progress_trend'   => 'up',
                'adherence_pct'    => 70,
                'current_weight'   => 71.0,
                'target_weight'    => 69.0,
                'sessions_planned' => 12,
                'sessions_done'    => 7,
                'coach_advice'     => 'Uzun koşular öncesi elektrolit ve kompleks karbonhidrat yüklemesini 2 saat önceye çekelim.',
            ],
        ];

        $first_student_id = 0;
        foreach ( $seed_students as $idx => $st ) {
            $wpdb->insert( $t_students, $st );
            $sid = (int) $wpdb->insert_id;
            if ( $idx === 0 ) {
                $first_student_id = $sid;
            }

            // Her öğrenci için varsayılan beslenme takvimi ekle
            $meals = [
                'sabah'    => '3 tam yumurta omlet, 60g yulaf ezmesi, yaban mersini, şekersiz filtre kahve',
                'ara1'     => '1 avuç çiğ badem (25g) + 1 yeşil elma veya 200ml kefir',
                'ogle'     => '180g ızgara tavuk göğsü, 150g basmati pirinç, bol zeytinyağlı yeşil salata',
                'ant_sonra'=> 'Apex Protein Kafe Whey İzolat Shake (32g protein) + 1 muz',
                'aksam'    => '200g ızgara somon veya hindi, buharda brokoli & kuşkonmaz, 1 kase yoğurt',
            ];
            $wpdb->insert( $wpdb->prefix . 'apex_nutrition_plans', [
                'student_id'    => $sid,
                'coach_user_id' => $st['coach_user_id'],
                'daily_kcal'    => ( $idx === 2 || $idx === 5 ) ? 1780 : 2420,
                'protein_g'     => ( $idx === 2 || $idx === 5 ) ? 130 : 178,
                'carbs_g'       => ( $idx === 2 || $idx === 5 ) ? 165 : 255,
                'fat_g'         => 68,
                'water_l'       => 3.2,
                'meals_json'    => wp_json_encode( $meals, JSON_UNESCAPED_UNICODE ),
                'weekly_notes'  => 'Pzt–Cum antrenman günlerinde öğle karbonhidratı +30g artırılır; Pazar dinlenme gününde düşük glisemik indeksli sebze ağırlıklı beslenme uygulanır.',
            ] );
        }

        // İlk öğrenci (uye_demo) için başlangıç koç-öğrenci mesajlaşması
        if ( $first_student_id > 0 ) {
            $t_cmsgs = $wpdb->prefix . 'apex_coach_messages';
            $wpdb->insert( $t_cmsgs, [
                'student_id'    => $first_student_id,
                'coach_user_id' => $kerem_id,
                'sender_role'   => 'coach',
                'sender_name'   => 'Kerem Alkan',
                'message'       => 'Merhaba! Bu haftaki InBody ölçümünde kas kütlen +0.6 kg artmış, tebrikler. Yeni 7 günlük beslenme takvimini güncelledim, antrenman sonrası protein shake saatine dikkat edelim.',
                'is_read'       => 1,
            ] );
            $wpdb->insert( $t_cmsgs, [
                'student_id'    => $first_student_id,
                'coach_user_id' => $kerem_id,
                'sender_role'   => 'student',
                'sender_name'   => $demo_u ? $demo_u->display_name : 'Demo Üye',
                'message'       => 'Teşekkürler hocam! Yeni beslenme planını panelden gördüm, Perşembe 19:00 seansına kadar birebir uygulayacağım.',
                'is_read'       => 0,
            ] );
        }
    }

    // 3b. Ek 5 Koç İçin Öğrenci, Beslenme Planı & Mesaj Seed (Sadece ilgili koçun öğrencisi yoksa)
    $extra_coach_students = [
        'koc_selin' => [
            [
                'student_name'     => 'Ece Demirtaş',
                'student_email'    => 'ece.demirtas@example.com',
                'goal'             => 'Reformer Pilates & Postür Düzeltme',
                'status'           => 'hedefte',
                'progress_pct'     => 89,
                'progress_trend'   => 'up',
                'adherence_pct'    => 93,
                'current_weight'   => 57.2,
                'target_weight'    => 56.0,
                'sessions_planned' => 12,
                'sessions_done'    => 11,
                'coach_advice'     => 'Torasik mobilite ve core stabilizasyonunda harika ilerleme var. Bu hafta Cadillac serisine orta direnç yay ile geçiyoruz.',
                'kcal'             => 1820,
                'pro'              => 125,
                'carb'             => 175,
                'fat'              => 62,
            ],
            [
                'student_name'     => 'Merve Aksu',
                'student_email'    => 'merve.aksu@example.com',
                'goal'             => 'Bel Mobilite & Esneklik',
                'status'           => 'yeni',
                'progress_pct'     => 46,
                'progress_trend'   => 'up',
                'adherence_pct'    => 82,
                'current_weight'   => 62.5,
                'target_weight'    => 60.0,
                'sessions_planned' => 10,
                'sessions_done'    => 5,
                'coach_advice'     => 'Nefes koordinasyonu oturdu; seans dışı günlerde 10 dk kalça fleksör açma rutini bel rahatlığını hızlandıracak.',
                'kcal'             => 1750,
                'pro'              => 118,
                'carb'             => 170,
                'fat'              => 58,
            ],
        ],
        'koc_mert' => [
            [
                'student_name'     => 'Kaan Yıldırım',
                'student_email'    => 'kaan.yildirim@example.com',
                'goal'             => 'Basketbol Çeviklik & Patlayıcı Kuvvet',
                'status'           => 'hedefte',
                'progress_pct'     => 84,
                'progress_trend'   => 'up',
                'adherence_pct'    => 88,
                'current_weight'   => 86.0,
                'target_weight'    => 84.5,
                'sessions_planned' => 14,
                'sessions_done'    => 12,
                'coach_advice'     => 'Dikey sıçramada +4 cm artış kaydedildi. FIBA salon antrenmanları öncesi ayak bileği stabilite bloğunu 6 dk tutalım.',
                'kcal'             => 2680,
                'pro'              => 192,
                'carb'             => 310,
                'fat'              => 72,
            ],
            [
                'student_name'     => 'Arda Koç',
                'student_email'    => 'arda.koc@example.com',
                'goal'             => 'Şut Mekaniği & Maç Kondisyonu',
                'status'           => 'dikkat',
                'progress_pct'     => 63,
                'progress_trend'   => 'up',
                'adherence_pct'    => 71,
                'current_weight'   => 79.4,
                'target_weight'    => 78.0,
                'sessions_planned' => 12,
                'sessions_done'    => 7,
                'coach_advice'     => 'Yüksek tempo sprint geçişlerinde nabız toparlanmasını artırmak için Salı günleri 15 dk interval ekliyoruz.',
                'kcal'             => 2520,
                'pro'              => 180,
                'carb'             => 285,
                'fat'              => 68,
            ],
        ],
        'koc_caner' => [
            [
                'student_name'     => 'İrem Şenol',
                'student_email'    => 'irem.senol@example.com',
                'goal'             => 'Voleybol Smaç & Blok Sıçrama',
                'status'           => 'hedefte',
                'progress_pct'     => 91,
                'progress_trend'   => 'up',
                'adherence_pct'    => 94,
                'current_weight'   => 64.0,
                'target_weight'    => 63.0,
                'sessions_planned' => 16,
                'sessions_done'    => 15,
                'coach_advice'     => 'Blok zamanlaması ve iniş mekaniği kusursuz. Turnuva haftasına girerken yüklemeyi %15 düşürüp patlayıcılığı koruyoruz.',
                'kcal'             => 2180,
                'pro'              => 152,
                'carb'             => 245,
                'fat'              => 64,
            ],
            [
                'student_name'     => 'Oğuzhan Tekin',
                'student_email'    => 'oguzhan.tekin@example.com',
                'goal'             => 'Omuz Kuvveti & Servis Hızı',
                'status'           => 'yeni',
                'progress_pct'     => 52,
                'progress_trend'   => 'up',
                'adherence_pct'    => 80,
                'current_weight'   => 83.5,
                'target_weight'    => 81.0,
                'sessions_planned' => 10,
                'sessions_done'    => 5,
                'coach_advice'     => 'Skapular kontrol testlerinde güçlü başlangıç yaptık; haftada 3 gün rotator cuff protokolü devam ediyor.',
                'kcal'             => 2460,
                'pro'              => 176,
                'carb'             => 270,
                'fat'              => 68,
            ],
        ],
        'koc_buse' => [
            [
                'student_name'     => 'Dilara Şen',
                'student_email'    => 'dilara.sen@example.com',
                'goal'             => 'Fonksiyonel Sıkılaşma & Yağ Yakımı',
                'status'           => 'hedefte',
                'progress_pct'     => 83,
                'progress_trend'   => 'up',
                'adherence_pct'    => 89,
                'current_weight'   => 61.4,
                'target_weight'    => 58.0,
                'sessions_planned' => 12,
                'sessions_done'    => 10,
                'coach_advice'     => 'Kettlebell komplekslerinde tempo çok iyi. Günlük 10.000 adım ve 2.8L su hedefini bozmadan devam edelim.',
                'kcal'             => 1720,
                'pro'              => 132,
                'carb'             => 155,
                'fat'              => 58,
            ],
            [
                'student_name'     => 'Onur Aksoy',
                'student_email'    => 'onur.aksoy@example.com',
                'goal'             => 'Metabolik Kondisyon & Kilo Kontrolü',
                'status'           => 'dikkat',
                'progress_pct'     => 58,
                'progress_trend'   => 'down',
                'adherence_pct'    => 66,
                'current_weight'   => 89.2,
                'target_weight'    => 82.0,
                'sessions_planned' => 12,
                'sessions_done'    => 7,
                'coach_advice'     => 'Hafta sonu kaçamaklarını dengelemek için Cumartesi sabahı 40 dk tempolu yürüyüş + protein kahvaltı ekleyelim.',
                'kcal'             => 2150,
                'pro'              => 168,
                'carb'             => 205,
                'fat'              => 65,
            ],
        ],
        'koc_emirhan' => [
            [
                'student_name'     => 'Berkay Tunç',
                'student_email'    => 'berkay.tunc@example.com',
                'goal'             => 'Temiz Hacim & Hipertrofi',
                'status'           => 'hedefte',
                'progress_pct'     => 88,
                'progress_trend'   => 'up',
                'adherence_pct'    => 92,
                'current_weight'   => 77.8,
                'target_weight'    => 81.0,
                'sessions_planned' => 14,
                'sessions_done'    => 13,
                'coach_advice'     => 'Haftalık +0.35 kg temiz kas artışı hedef bandında. Kreatin 5g ve antrenman sonrası 45g hızlı karbonhidratı koruyoruz.',
                'kcal'             => 2950,
                'pro'              => 205,
                'carb'             => 360,
                'fat'              => 78,
            ],
            [
                'student_name'     => 'Seda Karahan',
                'student_email'    => 'seda.karahan@example.com',
                'goal'             => 'Alt Vücut Kuvvet & Makro Dengesi',
                'status'           => 'hedefte',
                'progress_pct'     => 76,
                'progress_trend'   => 'up',
                'adherence_pct'    => 84,
                'current_weight'   => 58.6,
                'target_weight'    => 57.5,
                'sessions_planned' => 12,
                'sessions_done'    => 9,
                'coach_advice'     => 'Hip thrust ve RDL ağırlıklarında istikrarlı artış var. Akşam öğününde protein kaynağını 160g üzerine sabitliyoruz.',
                'kcal'             => 1940,
                'pro'              => 140,
                'carb'             => 195,
                'fat'              => 62,
            ],
        ],
    ];

    $t_nut   = $wpdb->prefix . 'apex_nutrition_plans';
    $t_cmsgs = $wpdb->prefix . 'apex_coach_messages';
    foreach ( $extra_coach_students as $login => $st_rows ) {
        if ( empty( $coach_users[ $login ] ) ) {
            continue;
        }
        $cu_obj = $coach_users[ $login ];
        $cid    = (int) $cu_obj->ID;
        $cname  = $cu_obj->display_name;
        if ( (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$t_students} WHERE coach_user_id = %d", $cid ) ) > 0 ) {
            continue;
        }
        foreach ( $st_rows as $sr ) {
            $wpdb->insert( $t_students, [
                'coach_user_id'    => $cid,
                'coach_name'       => $cname,
                'student_user_id'  => 0,
                'student_name'     => $sr['student_name'],
                'student_email'    => $sr['student_email'],
                'goal'             => $sr['goal'],
                'status'           => $sr['status'],
                'progress_pct'     => $sr['progress_pct'],
                'progress_trend'   => $sr['progress_trend'],
                'adherence_pct'    => $sr['adherence_pct'],
                'current_weight'   => $sr['current_weight'],
                'target_weight'    => $sr['target_weight'],
                'sessions_planned' => $sr['sessions_planned'],
                'sessions_done'    => $sr['sessions_done'],
                'coach_advice'     => $sr['coach_advice'],
            ] );
            $sid = (int) $wpdb->insert_id;
            if ( $sid > 0 ) {
                $meals = [
                    'sabah'     => '3 yumurta, 55g yulaf veya tam buğday ekşi maya tost, avokado, yeşil çay',
                    'ara1'      => '25g çiğ badem/ceviz + 1 porsiyon mevsim meyvesi',
                    'ogle'      => '180g ızgara tavuk veya hindi, 140g kinoa/basmati, zeytinyağlı yeşil salata',
                    'ant_sonra' => 'Apex Protein Kafe Whey İzolat Shake (32g protein) + muz',
                    'aksam'     => '190g ızgara somon veya yağsız dana, fırınlanmış sebze, 1 kase yoğurt',
                ];
                $wpdb->insert( $t_nut, [
                    'student_id'    => $sid,
                    'coach_user_id' => $cid,
                    'daily_kcal'    => $sr['kcal'],
                    'protein_g'     => $sr['pro'],
                    'carbs_g'       => $sr['carb'],
                    'fat_g'         => $sr['fat'],
                    'water_l'       => 3.0,
                    'meals_json'    => wp_json_encode( $meals, JSON_UNESCAPED_UNICODE ),
                    'weekly_notes'  => 'Antrenman günlerinde kompleks karbonhidrat zamanlaması seans öncesi 90 dk ve sonrası 45 dk içine yerleştirilir.',
                ] );
                $wpdb->insert( $t_cmsgs, [
                    'student_id'    => $sid,
                    'coach_user_id' => $cid,
                    'sender_role'   => 'coach',
                    'sender_name'   => $cname,
                    'message'       => 'Merhaba ' . explode( ' ', $sr['student_name'] )[0] . '! Haftalık antrenman ve beslenme takibini güncelledim, bir sonraki seans öncesi sorularını buradan yazabilirsin.',
                    'is_read'       => 1,
                ] );
            }
        }
    }

    // 4. Üye Yorumları Seed
    $t_reviews = $wpdb->prefix . 'apex_reviews';
    if ( (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$t_reviews}" ) === 0 ) {
        $seed_reviews = [
            [ 'author_name' => 'Mertcan Kılıç', 'member_plan' => 'APEX ELITE + PT', 'rating' => 5, 'comment' => 'FIBA parkeli basketbol salonu ve lisanslı hakem hizmeti kurumsal maçlarımız için kusursuz. Kapalı otopark ve EV şarj istasyonu büyük konfor.' ],
            [ 'author_name' => 'Ece Demirtaş', 'member_plan' => 'APEX PRO', 'rating' => 5, 'comment' => 'Selin Hoca ile Reformer Pilates seansları duruşumu tamamen değiştirdi. Soyunma odaları ve Fin saunası gerçekten otel standartlarında.' ],
            [ 'author_name' => 'Kaan Yıldırım', 'member_plan' => 'APEX ELITE + PT', 'rating' => 5, 'comment' => 'Koçum Kerem Alkan panelden haftalık beslenme takvimimi güncelleyip birebir mesajla takip ediyor. 2 ayda 6 kg yağ verip kas kütlemi artırdım.' ],
            [ 'author_name' => 'Dilara Şen', 'member_plan' => 'APEX PRO', 'rating' => 5, 'comment' => 'Antrenman bitmeden Protein Kafe’den fit bowl ve whey shake ön siparişi veriyorum, duştan çıkınca hazır bekliyor. Makroların yazması harika.' ],
            [ 'author_name' => 'Onur Aksoy', 'member_plan' => 'APEX CLASSIC', 'rating' => 4, 'comment' => 'Dijital QR kartla turnikeden ve otoparktan saniyeler içinde geçmek çok pratik. Akşam 19:00 yoğunluğunu grafikten kontrol edip sakin saatte geliyorum.' ],
        ];
        foreach ( $seed_reviews as $rev ) {
            $wpdb->insert( $t_reviews, $rev );
        }
    }

    // 5. Kafe Ön Siparişleri Seed
    $t_cafe = $wpdb->prefix . 'apex_cafe_orders';
    if ( (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$t_cafe}" ) === 0 ) {
        $wpdb->insert( $t_cafe, [
            'order_code'    => 'APX-KFE-1042',
            'user_id'       => $demo_uid,
            'customer_name' => $demo_u ? $demo_u->display_name : 'Demo Üye',
            'customer_phone'=> '0532 555 27 39',
            'pickup_time'   => 'Antrenman Çıkışı (20 dk)',
            'items_json'    => wp_json_encode( [ [ 'name' => 'Apex Whey İzolat Shake', 'qty' => 1, 'price' => 165 ], [ 'name' => 'Izgara Tavuklu Kinoa Bowl', 'qty' => 1, 'price' => 285 ] ], JSON_UNESCAPED_UNICODE ),
            'total_kcal'    => 735,
            'total_protein' => 76,
            'total_price'   => 450,
            'status'        => 'ready',
        ] );
        $wpdb->insert( $t_cafe, [
            'order_code'    => 'APX-KFE-1043',
            'user_id'       => 0,
            'customer_name' => 'Selin Arslan',
            'customer_phone'=> '0533 412 88 10',
            'pickup_time'   => '15 dk sonra',
            'items_json'    => wp_json_encode( [ [ 'name' => 'Buzlu Americano & Protein Shot', 'qty' => 1, 'price' => 135 ] ], JSON_UNESCAPED_UNICODE ),
            'total_kcal'    => 110,
            'total_protein' => 20,
            'total_price'   => 135,
            'status'        => 'preparing',
        ] );
    }

    // 6. Demo Üye Haftalık Antrenman Geçmişi Seed
    $t_workouts = $wpdb->prefix . 'apex_workout_logs';
    if ( $demo_uid > 0 && (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$t_workouts} WHERE user_id = %d", $demo_uid ) ) === 0 ) {
        $days = [
            [ -5, 'Üst Vücut Kuvvet PT', 65, 540, 'Bench press yeni rekor 85kg' ],
            [ -4, 'Basketbol Maç (Salon 1)', 60, 620, '5v5 tam saha yüksek tempo' ],
            [ -3, 'Alt Vücut & Core', 55, 490, 'Squat 4x8 + plank serisi' ],
            [ -1, 'HIIT & Mobilite', 45, 460, 'Fin saunası 15 dk toparlanma' ],
            [ 0,  'Fonksiyonel Kuvvet', 70, 580, 'Kerem Hoca ile birebir seans' ],
        ];
        foreach ( $days as $d ) {
            $wpdb->insert( $t_workouts, [
                'user_id'      => $demo_uid,
                'workout_date' => date( 'Y-m-d', strtotime( $d[0] . ' days' ) ),
                'workout_type' => $d[1],
                'duration_min' => $d[2],
                'calories'     => $d[3],
                'notes'        => $d[4],
            ] );
        }
    }

    // 7. Demo Üye E-posta & Hatırlatma Örnek Kayıtları (v2.1.0 — yalnızca tablo boşsa)
    $t_emails = $wpdb->prefix . 'apex_email_logs';
    if ( $demo_uid > 0 && (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$t_emails}" ) === 0 && function_exists( 'apex_render_email_template' ) ) {
        $demo_name  = $demo_u ? $demo_u->display_name : 'Demo Üye';
        $demo_email = $demo_u ? $demo_u->user_email : 'uye@apexarena.com.tr';
        $demo_code  = get_user_meta( $demo_uid, 'apex_member_code', true ) ?: 'APX-2026-DEMO';
        $welcome_html = apex_render_email_template( [
            'badge'     => 'HOŞ GELDİNİZ · DİJİTAL ÜYELİK AKTİF',
            'title'     => "APEX ARENA Ailesine Hoş Geldin, {$demo_name}!",
            'intro'     => 'Üyeliğin başarıyla oluşturuldu ve <strong>Dijital QR Geçiş Kartın</strong> aktif edildi. Turnikelerden ve kapalı otoparktan QR kodunla saniyeler içinde geçiş yapabilirsin.',
            'highlight' => "<div style=\"font-size:11px;letter-spacing:1.2px;text-transform:uppercase;color:#F4E268;font-weight:800;margin-bottom:4px;\">DİJİTAL QR ÜYE KODUN</div><div style=\"font-size:24px;font-weight:900;letter-spacing:2px;color:#FFFFFF;\">{$demo_code}</div>",
            'rows'      => [
                'Üye Adı Soyadı' => $demo_name,
                'Seçilen Paket'  => 'APEX PRO',
                'Durum'          => 'Aktif · Dijital QR Hazır',
            ],
        ] );
        $rem_html = apex_render_email_template( [
            'badge'     => 'ANTRENMAN & BESLENME HATIRLATMASI',
            'title'     => "Hareket Zamanı, {$demo_name}!",
            'intro'     => 'Bu haftaki hedeflerini ve koçun <strong>Kerem Alkan</strong> tarafından güncellenen beslenme takvimini hatırlatmak istedik.',
            'highlight' => '💡 <strong>Koç Notu:</strong> Antrenman sonrası 35g protein + kompleks karbonhidrat alımını aksatma ve Çarşamba günü 15 dk mobilite ekle.',
            'rows'      => [
                'Üye Kodu (QR)'   => $demo_code,
                'Atanmış Koç'     => 'Kerem Alkan',
                'Program İlerleme'=> '%86',
            ],
        ] );
        $wpdb->insert( $t_emails, [
            'user_id'         => $demo_uid,
            'recipient_email' => $demo_email,
            'recipient_name'  => $demo_name,
            'mail_type'       => 'welcome',
            'ref_key'         => 'seed_welcome_' . $demo_uid,
            'subject'         => "APEX ARENA'ya Hoş Geldin {$demo_name}! Dijital QR Kartın Hazır ({$demo_code})",
            'body_html'       => $welcome_html,
            'delivery_mode'   => 'local_preview',
            'status'          => 'queued_local',
            'error_message'   => '',
        ] );
        $wpdb->insert( $t_emails, [
            'user_id'         => $demo_uid,
            'recipient_email' => $demo_email,
            'recipient_name'  => $demo_name,
            'mail_type'       => 'reminder_workout',
            'ref_key'         => 'seed_rem_' . $demo_uid,
            'subject'         => "🔥 Antrenman & Hedef Hatırlatması: {$demo_name}, Bugün Sahada Buluşalım! — APEX ARENA",
            'body_html'       => $rem_html,
            'delivery_mode'   => 'local_preview',
            'status'          => 'queued_local',
            'error_message'   => '',
        ] );
    }
}
