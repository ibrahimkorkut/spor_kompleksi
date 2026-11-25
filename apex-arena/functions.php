<?php
/**
 * APEX ARENA — Spor Kompleksi Özel WordPress Teması
 * Ana tema bootstrap dosyası
 *
 * @package ApexArena
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'APEX_VERSION', '2.2.0' );
define( 'APEX_DIR', get_template_directory() );
define( 'APEX_URI', get_template_directory_uri() );

/**
 * Modüler tema bileşenlerini yükle
 */
$apex_modules = [
    'inc/database.php',
    'inc/post-types.php',
    'inc/helpers.php',
    'inc/mailer.php',
    'inc/ai-chatbot.php',
    'inc/api-endpoints.php',
    'inc/admin-panel.php',
    'inc/coach-portal.php',
    'inc/admin-login-theme.php',
    'inc/customizer.php',
];

foreach ( $apex_modules as $module ) {
    $path = APEX_DIR . '/' . $module;
    if ( file_exists( $path ) ) {
        require_once $path;
    }
}

/**
 * Tema destekleri ve menü kayıtları
 */
function apex_theme_setup() {
    load_theme_textdomain( 'apex-arena', APEX_DIR . '/languages' );

    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'custom-logo', [
        'height'      => 60,
        'width'       => 220,
        'flex-height' => true,
        'flex-width'  => true,
    ] );
    add_theme_support( 'html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
        'navigation-widgets',
    ] );

    add_image_size( 'apex-hero', 1600, 900, true );
    add_image_size( 'apex-card', 800, 520, true );
    add_image_size( 'apex-portrait', 600, 750, true );

    register_nav_menus( [
        'primary'         => __( 'Ana Üst Menü', 'apex-arena' ),
        'footer_services' => __( 'Footer Hizmetler Menüsü', 'apex-arena' ),
        'footer_legal'    => __( 'Footer Kurumsal & Yasal Menü', 'apex-arena' ),
    ] );
}
add_action( 'after_setup_theme', 'apex_theme_setup' );

/**
 * Widget alanları (Envato standartları)
 */
function apex_widgets_init() {
    register_sidebar( [
        'name'          => __( 'Kenar Çubuğu', 'apex-arena' ),
        'id'            => 'apex-sidebar',
        'description'   => __( 'Blog ve duyuru detay sayfaları için bileşen alanı.', 'apex-arena' ),
        'before_widget' => '<section id="%1$s" class="apex-widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="apex-widget-title">',
        'after_title'   => '</h3>',
    ] );
}
add_action( 'widgets_init', 'apex_widgets_init' );

/**
 * Stil ve script dosyalarını yükle
 */
function apex_enqueue_assets() {
    // Google Fonts: Barlow Condensed (Display), Plus Jakarta Sans (Body), Lexend (Disleksi Dostu Erişilebilirlik)
    wp_enqueue_style(
        'apex-google-fonts',
        'https://fonts.googleapis.com/css2?family=Barlow+Condensed:ital,wght@0,600;0,700;0,800;0,900;1,700&family=Lexend:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap',
        [],
        null
    );

    wp_enqueue_style( 'apex-main', APEX_URI . '/assets/css/main.css', [ 'apex-google-fonts' ], APEX_VERSION . '.' . filemtime( APEX_DIR . '/assets/css/main.css' ) );
    wp_enqueue_style( 'apex-accessibility', APEX_URI . '/assets/css/accessibility.css', [ 'apex-main' ], APEX_VERSION . '.' . filemtime( APEX_DIR . '/assets/css/accessibility.css' ) );

    wp_enqueue_script( 'apex-main', APEX_URI . '/assets/js/main.js', [], APEX_VERSION . '.' . filemtime( APEX_DIR . '/assets/js/main.js' ), true );
    wp_enqueue_script( 'apex-booking', APEX_URI . '/assets/js/booking.js', [ 'apex-main' ], APEX_VERSION, true );
    wp_enqueue_script( 'apex-accessibility', APEX_URI . '/assets/js/accessibility.js', [ 'apex-main' ], APEX_VERSION, true );
    wp_enqueue_script( 'apex-chatbot', APEX_URI . '/assets/js/chatbot.js', [ 'apex-main' ], APEX_VERSION . '.' . filemtime( APEX_DIR . '/assets/js/chatbot.js' ), true );
    if ( is_front_page() ) {
        wp_enqueue_script( 'apex-scroll-cinema', APEX_URI . '/assets/js/scroll-cinema.js', [], APEX_VERSION, true );
        wp_enqueue_script( 'apex-motion-3d', APEX_URI . '/assets/js/motion-3d.js', [], APEX_VERSION, true );
        wp_enqueue_script( 'apex-complex-3d', APEX_URI . '/assets/js/complex-3d.js', [], APEX_VERSION . '.' . filemtime( APEX_DIR . '/assets/js/complex-3d.js' ), true );
    }

    $settings = apex_get_settings();
    wp_localize_script( 'apex-main', 'APEX_CONFIG', [
        'restUrl'      => esc_url_raw( rest_url( 'apex/v1/' ) ),
        'nonce'        => wp_create_nonce( 'wp_rest' ),
        'homeUrl'      => esc_url_raw( home_url( '/' ) ),
        'themeUri'     => esc_url_raw( APEX_URI ),
        'isLoggedIn'   => is_user_logged_in(),
        'refereeFee'   => (int) $settings['referee_fee'],
        'occupancy'    => (int) $settings['live_occupancy'],
        'aiMode'       => apex_get_effective_ai_mode(),
        'pages'        => [
            'pt'         => esc_url_raw( home_url( '/pt-egitmenler/' ) ),
            'trainers'   => esc_url_raw( home_url( '/antrenorler/' ) ),
            'courts'     => esc_url_raw( home_url( '/salon-kiralama/' ) ),
            'facilities' => esc_url_raw( home_url( '/tesis-olanaklari/' ) ),
            'membership' => esc_url_raw( home_url( '/uyelik/' ) ),
            'news'       => esc_url_raw( home_url( '/duyurular/' ) ),
            'contact'    => esc_url_raw( home_url( '/iletisim/' ) ),
            'cookies'    => esc_url_raw( home_url( '/cerez-politikasi/' ) ),
            'kvkk'       => esc_url_raw( home_url( '/kvkk-aydinlatma-metni/' ) ),
        ],
    ] );
}
add_action( 'wp_enqueue_scripts', 'apex_enqueue_assets' );

/**
 * ES Module desteği (Three.js 3D Kintsugi sahnesi için)
 */
add_filter( 'script_loader_tag', function( $tag, $handle, $src ) {
    if ( in_array( $handle, [ 'apex-motion-3d', 'apex-complex-3d' ], true ) ) {
        return '<script type="module" src="' . esc_url( $src ) . '" id="' . esc_attr( $handle ) . '-js"></script>' . "\n";
    }
    return $tag;
}, 10, 3 );

/**
 * Schema.org JSON-LD Yapısal Veri (SEO)
 */
function apex_output_schema_jsonld() {
    $settings = apex_get_settings();
    $schema   = [
        '@context'    => 'https://schema.org',
        '@type'       => 'SportsActivityLocation',
        'name'        => 'APEX ARENA Spor ve Performans Kompleksi',
        'description' => get_bloginfo( 'description' ),
        'url'         => home_url( '/' ),
        'telephone'   => $settings['phone'],
        'email'       => $settings['email'],
        'address'     => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => $settings['address'],
            'addressLocality' => 'Ataşehir',
            'addressRegion'   => 'İstanbul',
            'addressCountry'  => 'TR',
        ],
        'openingHoursSpecification' => [
            [
                '@type'     => 'OpeningHoursSpecification',
                'dayOfWeek' => [ 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday' ],
                'opens'     => '06:30',
                'closes'    => '23:30',
            ],
            [
                '@type'     => 'OpeningHoursSpecification',
                'dayOfWeek' => [ 'Saturday', 'Sunday' ],
                'opens'     => '08:00',
                'closes'    => '22:00',
            ],
        ],
        'amenityFeature' => [
            [ '@type' => 'LocationFeatureSpecification', 'name' => '2x FIBA Basketbol Salonu', 'value' => true ],
            [ '@type' => 'LocationFeatureSpecification', 'name' => '1x CEV Voleybol Salonu', 'value' => true ],
            [ '@type' => 'LocationFeatureSpecification', 'name' => 'Lisanslı Hakem Hizmeti', 'value' => true ],
            [ '@type' => 'LocationFeatureSpecification', 'name' => 'Soyunma Odası, Yağmur Duş ve Sauna', 'value' => true ],
            [ '@type' => 'LocationFeatureSpecification', 'name' => 'Kapalı Otopark & EV Şarj', 'value' => true ],
            [ '@type' => 'LocationFeatureSpecification', 'name' => 'Apex Protein Kafe', 'value' => true ],
        ],
    ];
    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'apex_output_schema_jsonld', 20 );

/**
 * LCP İlk Video Karesi Öncelikli Preload (Scroll Cinema)
 */
function apex_preload_hero_frame() {
    if ( ! is_front_page() ) {
        return;
    }
    $frame0 = esc_url( APEX_URI . '/assets/video-frames/frame_0001.webp?v=1.4.1' );
    echo '<link rel="preload" as="image" type="image/webp" href="' . $frame0 . '" fetchpriority="high">' . "\n";
}
add_action( 'wp_head', 'apex_preload_hero_frame', 2 );

/**
 * Tema etkinleştirildiğinde veritabanı tablolarını oluştur ve permalink kurallarını yenile
 */
function apex_on_theme_activate() {
    apex_create_custom_tables();
    apex_register_post_types();
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'apex_on_theme_activate' );

/**
 * Standart üyeler (subscriber) için ön yüzde WP Admin Bar'ı gizle (kurumsal üye paneli deneyimi)
 */
add_filter( 'show_admin_bar', function( $show ) {
    return current_user_can( 'edit_posts' ) ? $show : false;
} );

/**
 * Güvenli şekilde tabloların varlığını garanti et
 */
add_action( 'init', function() {
    if ( get_option( 'apex_db_version' ) !== APEX_VERSION ) {
        apex_create_custom_tables();
        update_option( 'apex_db_version', APEX_VERSION );
    }
}, 5 );
