<?php
/**
 * Özel MySQL Tabloları — Salon Rezervasyonları, PT Randevuları, Üyelik Başvuruları, AI Chatbot Logları, İletişim
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

    // 1. Salon Kiralama Rezervasyonları (Basketbol 1, Basketbol 2, Voleybol + Hakem Opsiyonu)
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

    // 2. PT Eğitmen Randevuları (Bireysel & Grupsal)
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

    // 3. Üyelik Başvuruları & Kayıtları
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

    dbDelta( $sql_courts );
    dbDelta( $sql_pt );
    dbDelta( $sql_mem );
    dbDelta( $sql_chat );
    dbDelta( $sql_contact );
}
