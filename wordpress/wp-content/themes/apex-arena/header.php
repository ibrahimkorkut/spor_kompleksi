<?php
/**
 * Header şablonu — Tek Satır Yüzen Apple Liquid Glass Navigasyon Barı + Scroll Edge Fade
 *
 * @package ApexArena
 */
$nav_raw   = function_exists( 'apex_get_nav_items' ) ? apex_get_nav_items() : [];
$nav_items = [];
foreach ( $nav_raw as $ni ) {
    if ( isset( $ni['enabled'] ) && ! $ni['enabled'] ) {
        continue;
    }
    $nav_items[] = [
        'label' => $ni['label'],
        'url'   => function_exists( 'apex_resolve_cms_url' ) ? apex_resolve_cms_url( $ni['url'] ) : home_url( $ni['url'] ),
        'slug'  => $ni['slug'] ?? '',
    ];
}
$brand_1 = apex_cms( 'brand_first', 'Apex' );
$brand_2 = apex_cms( 'brand_second', 'Arena' );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#000000">
    <?php if ( ! defined( 'WPSEO_VERSION' ) ) : ?>
    <meta name="description" content="<?php echo esc_attr( get_bloginfo( 'description' ) ); ?>">
    <?php endif; ?>
    <?php wp_head(); ?>
</head>
<body <?php body_class( 'apex-body' ); ?>>
<?php wp_body_open(); ?>

<!-- WCAG 2.2 AA Skip Link & Global GPU Scroll Progress Hairline -->
<a class="apex-skip-link" href="#main-content">Ana içeriğe atla</a>
<div class="apex-scroll-progress" aria-hidden="true"><div class="apex-scroll-progress-fill" id="apex-scroll-progress-fill"></div></div>
<div id="apex-toast" class="apex-toast" role="status" aria-live="polite" hidden></div>
<div id="apex-sr-announcer" class="sr-only" role="status" aria-live="polite" aria-atomic="true"></div>
<div id="apex-reading-guide" class="apex-reading-guide" aria-hidden="true"></div>

<!-- Yüzen Apple Liquid Glass Navigasyon Katmanı -->
<div class="apex-header-wrap">
    <header class="apex-header" role="banner">
        <div class="apex-header-inner">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="apex-brand" aria-label="APEX ARENA Ana Sayfa">
                <span class="apex-brand-mark" aria-hidden="true">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3 22 20H2L12 3Z"/></svg>
                </span>
                <span class="apex-brand-text"><span class="apex-brand-accent"><?php echo esc_html( $brand_1 ); ?></span> <?php echo esc_html( $brand_2 ); ?></span>
            </a>

            <nav class="apex-nav" aria-label="Ana menü">
                <ul class="apex-nav-list" id="apex-nav-list">
                    <li class="apex-nav-morph-slot" aria-hidden="true"><span class="apex-nav-morph" id="apex-nav-morph"></span></li>
                    <?php foreach ( $nav_items as $item ) :
                        $is_active = is_page( $item['slug'] ) || ( $item['slug'] === 'antrenorler' && is_singular( 'apex_trainer' ) ) || ( $item['slug'] === 'duyurular' && is_singular( 'apex_announcement' ) );
                    ?>
                    <li>
                        <a href="<?php echo esc_url( $item['url'] ); ?>" class="apex-nav-link <?php echo $is_active ? 'is-active' : ''; ?>" <?php echo $is_active ? 'aria-current="page"' : ''; ?>>
                            <?php echo esc_html( $item['label'] ); ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </nav>

            <div class="apex-header-actions">
                <div class="apex-search-inline" id="apex-search-bar">
                    <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="apex-search-inline-form">
                        <button type="button" class="apex-search-trigger" id="apex-search-toggle" title="Site içi arama (Ctrl+K)" aria-label="Site içi arama (Ctrl+K)" aria-expanded="false" aria-controls="apex-global-search">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7.5"/><path d="m20.5 20.5-4-4"/></svg>
                        </button>
                        <label for="apex-global-search" class="sr-only">Sitede ara</label>
                        <input type="search" id="apex-global-search" name="s" class="apex-search-inline-input" placeholder="Salon, PT, antrenör, kafe ara..." value="<?php echo esc_attr( get_search_query() ); ?>" autocomplete="off" tabindex="-1">
                        <button type="button" class="apex-search-inline-close" id="apex-search-close" aria-label="Aramayı kapat" tabindex="-1">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                        </button>
                    </form>
                </div>
                <?php if ( is_user_logged_in() ) :
                    $cu = wp_get_current_user();
                ?>
                    <a href="<?php echo esc_url( home_url( '/uyelik/#uye-paneli' ) ); ?>" class="apex-nav-util">
                        <span class="apex-avatar-dot" aria-hidden="true"><?php echo esc_html( mb_strtoupper( mb_substr( $cu->display_name, 0, 1 ) ) ); ?></span>
                        Üye Panelim
                    </a>
                    <a href="<?php echo esc_url( home_url( '/uyelik/#paketler' ) ); ?>" class="apex-btn apex-btn-primary apex-btn-sm">Paketler</a>
                <?php else : ?>
                    <a href="<?php echo esc_url( home_url( '/uyelik/#giris' ) ); ?>" class="apex-nav-util"><?php echo esc_html( apex_cms( 'nav_login_label', 'Üye Girişi' ) ); ?></a>
                    <a href="<?php echo esc_url( home_url( '/uyelik/#kayit' ) ); ?>" class="apex-btn apex-btn-primary apex-btn-sm"><?php echo esc_html( apex_cms( 'nav_join_label', 'Bize Katıl' ) ); ?></a>
                <?php endif; ?>
                <button type="button" class="apex-mobile-toggle" id="apex-mobile-open" aria-label="Menüyü aç" aria-controls="apex-mobile-drawer" aria-expanded="false">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><line x1="4" y1="7" x2="20" y2="7"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="17" x2="20" y2="17"/></svg>
                    <span>Menü</span>
                </button>
            </div>
        </div>
    </header>
</div>

<!-- Tam Ekran Mobil Liquid Glass Çekmece -->
<div class="apex-mobile-drawer" id="apex-mobile-drawer" role="dialog" aria-modal="true" aria-label="Mobil navigasyon menüsü" hidden>
    <div class="apex-mobile-drawer-head">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="apex-brand">
            <span class="apex-brand-mark" aria-hidden="true">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3 22 20H2L12 3Z"/></svg>
            </span>
            <span class="apex-brand-text"><span class="apex-brand-accent">Apex</span> Arena</span>
        </a>
        <button type="button" class="apex-mobile-close" id="apex-mobile-close" aria-label="Menüyü kapat">Kapat</button>
    </div>
    <nav class="apex-mobile-nav" aria-label="Mobil menü">
        <ul>
            <?php foreach ( $nav_items as $item ) : ?>
                <li><a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a></li>
            <?php endforeach; ?>
            <li><a href="<?php echo esc_url( home_url( '/sss/' ) ); ?>">Sıkça Sorulan Sorular</a></li>
            <li><a href="<?php echo esc_url( home_url( '/cerez-politikasi/' ) ); ?>">KVKK ve Çerez Politikası</a></li>
        </ul>
    </nav>
    <div class="apex-mobile-drawer-actions">
        <?php if ( is_user_logged_in() ) : ?>
            <a href="<?php echo esc_url( home_url( '/uyelik/#uye-paneli' ) ); ?>" class="apex-btn apex-btn-primary apex-btn-block">Üye Panelim</a>
            <a href="<?php echo esc_url( home_url( '/uyelik/#paketler' ) ); ?>" class="apex-btn apex-btn-outline apex-btn-block">Üyelik Paketleri</a>
        <?php else : ?>
            <a href="<?php echo esc_url( home_url( '/uyelik/#kayit' ) ); ?>" class="apex-btn apex-btn-primary apex-btn-block">Bize Katıl</a>
            <a href="<?php echo esc_url( home_url( '/uyelik/#giris' ) ); ?>" class="apex-btn apex-btn-outline apex-btn-block">Üye Girişi</a>
        <?php endif; ?>
        <button type="button" class="apex-btn apex-btn-ghost apex-btn-block" data-open-ai>Apex AI Koç’a Sor</button>
    </div>
</div>

<main id="main-content" tabindex="-1">
