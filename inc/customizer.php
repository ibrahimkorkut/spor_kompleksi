<?php
/**
 * WordPress Theme Customizer (Görünüm → Özelleştir) — Envato Tema Standardı
 *
 * @package ApexArena
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'customize_register', function( WP_Customize_Manager $wp_customize ) {
    $wp_customize->add_section( 'apex_hero_section', [
        'title'    => __( 'APEX ARENA — Hero & Marka', 'apex-arena' ),
        'priority' => 25,
    ] );

    $wp_customize->add_setting( 'apex_hero_title', [
        'default'           => 'SINIRLARINI ARENADA AŞ. PERFORMANSIN YENİ MERKEZİ.',
        'sanitize_callback' => 'sanitize_text_field',
    ] );
    $wp_customize->add_control( 'apex_hero_title', [
        'label'   => __( 'Ana Sayfa Hero Başlığı', 'apex-arena' ),
        'section' => 'apex_hero_section',
        'type'    => 'text',
    ] );

    $wp_customize->add_setting( 'apex_hero_subtitle', [
        'default'           => '2 FIBA standart basketbol salonu, profesyonel voleybol sahası, lisanslı hakem hizmeti, sertifikalı PT kadrosu, canlı online takvim ve 7/24 AI Spor Koçu tek komplekste.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ] );
    $wp_customize->add_control( 'apex_hero_subtitle', [
        'label'   => __( 'Hero Alt Açıklama', 'apex-arena' ),
        'section' => 'apex_hero_section',
        'type'    => 'textarea',
    ] );

    $wp_customize->add_setting( 'apex_accent_color', [
        'default'           => '#E31837',
        'sanitize_callback' => 'sanitize_hex_color',
    ] );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'apex_accent_color', [
        'label'   => __( 'Ana Vurgu Rengi (Accent)', 'apex-arena' ),
        'section' => 'apex_hero_section',
    ] ) );
} );

add_action( 'wp_head', function() {
    $accent = get_theme_mod( 'apex_accent_color', '#E31837' );
    if ( $accent && $accent !== '#E31837' ) {
        echo '<style>:root{--apex-crimson:' . esc_attr( $accent ) . ';}</style>';
    }
}, 25 );
