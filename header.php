<?php
/**
 * Header şablonu — Sticky Masaüstü Menü + Tam Ekran Mobil Çekmece + Skip Link + Canlı Yoğunluk
 *
 * @package ApexArena
 */
$settings = apex_get_settings();
$current_url = ( is_ssl() ? 'https://' : 'http://' ) . ( $_SERVER['HTTP_HOST'] ?? 'localhost' ) . ( $_SERVER['REQUEST_URI'] ?? '/' );
$nav_items = [
    [ 'label' => 'PT EĞİTMENLER', 'url' => home_url( '/pt-egitmenler/' ), 'slug' => 'pt-egitmenler' ],
    [ 'label' => 'ANTRENÖRLER', 'url' => home_url( '/antrenorler/' ), 'slug' => 'antrenorler' ],
    [ 'label' => 'SALON KİRALAMA', 'url' => home_url( '/salon-kiralama/' ), 'slug' => 'salon-kiralama' ],
    [ 'label' => 'TESİS OLANAKLARI', 'url' => home_url( '/tesis-olanaklari/' ), 'slug' => 'tesis-olanaklari' ],
    [ 'label' => 'ÜYELİK', 'url' => home_url( '/uyelik/' ), 'slug' => 'uyelik' ],
    [ 'label' => 'DUYURULAR', 'url' => home_url( '/duyurular/' ), 'slug' => 'duyurular' ],
    [ 'label' => 'İLETİŞİM', 'url' => home_url( '/iletisim/' ), 'slug' => 'iletisim' ],
];
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0A0B0E">
    <meta name="description" content="<?php echo esc_attr( get_bloginfo( 'description' ) ); ?>">
    <?php wp_head(); ?>
</head>
<body <?php body_class( 'apex-body' ); ?>>
<?php wp_body_open(); ?>

<!-- WCAG 2.2 AA Skip to Main Content -->
<a class="apex-skip-link" href="#main-content">Ana içeriğe atla</a>

<!-- Canlı Ekran Okuyucu Duyuru Bölgesi -->
<div id="apex-sr-announcer" class="sr-only" role="status" aria-live="polite" aria-atomic="true"></div>

<!-- Okuma Kılavuzu (Erişilebilirlik özelliği) -->
<div id="apex-reading-guide" class="apex-reading-guide" aria-hidden="true"></div>

<!-- Üst Bilgi Şeridi (Canlı Yoğunluk & Hızlı Erişim) -->
<div class="apex-topbar" role="region" aria-label="Canlı tesis durumu">
    <div class="apex-container apex-topbar-inner">
        <div class="apex-topbar-left">
            <span class="apex-live-pill">
                <span class="apex-live-dot" aria-hidden="true"></span>
                CANLI YOĞUNLUK: %<?php echo esc_html( $settings['live_occupancy'] ); ?> SAKİN
            </span>
            <span class="apex-topbar-meta">Otopark: <strong><?php echo esc_html( $settings['parking_free'] ); ?>/<?php echo esc_html( $settings['parking_total'] ); ?></strong> Boş</span>
            <span class="apex-topbar-meta apex-hide-sm">Saatler: <?php echo esc_html( $settings['weekday_hours'] ); ?></span>
        </div>
        <div class="apex-topbar-right">
            <a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $settings['phone'] ) ); ?>" class="apex-topbar-link"><?php echo esc_html( $settings['phone'] ); ?></a>
            <button type="button" class="apex-topbar-a11y-btn" data-open-a11y aria-label="Erişilebilirlik araç çubuğunu aç">♿ Erişilebilirlik</button>
        </div>
    </div>
</div>

<!-- Sticky Ana Header -->
<header class="apex-header" role="banner">
    <div class="apex-container apex-header-inner">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="apex-brand" aria-label="APEX ARENA Ana Sayfa">
            <span class="apex-brand-mark" aria-hidden="true">▲</span>
            <span class="apex-brand-text"><span class="apex-brand-accent">APEX</span> ARENA</span>
        </a>

        <nav class="apex-nav" aria-label="Ana menü">
            <ul class="apex-nav-list">
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
            <button type="button" class="apex-icon-btn" id="apex-search-toggle" aria-label="Site içi arama" aria-expanded="false">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            </button>
            <?php if ( is_user_logged_in() ) :
                $cu = wp_get_current_user();
            ?>
                <a href="<?php echo esc_url( home_url( '/uyelik/?panel=1' ) ); ?>" class="apex-nav-util">
                    <span class="apex-avatar-dot" aria-hidden="true"><?php echo esc_html( mb_strtoupper( mb_substr( $cu->display_name, 0, 1 ) ) ); ?></span>
                    ÜYE PANELİM
                </a>
            <?php else : ?>
                <a href="<?php echo esc_url( home_url( '/uyelik/#giris' ) ); ?>" class="apex-nav-util">ÜYE GİRİŞİ</a>
            <?php endif; ?>
            <a href="<?php echo esc_url( home_url( '/uyelik/#paketler' ) ); ?>" class="apex-btn apex-btn-primary apex-btn-sm">BİZE KATIL</a>
            <button type="button" class="apex-mobile-toggle" id="apex-mobile-open" aria-label="Menüyü aç" aria-controls="apex-mobile-drawer" aria-expanded="false">
                <span>MENÜ</span>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
        </div>
    </div>

    <!-- Açılır Site İçi Arama Çubuğu -->
    <div class="apex-search-bar" id="apex-search-bar" hidden>
        <div class="apex-container">
            <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="apex-search-form">
                <label for="apex-global-search" class="sr-only">Sitede ara</label>
                <input type="search" id="apex-global-search" name="s" placeholder="Antrenör, basketbol salonu, PT paketi, kafe veya duyuru ara..." value="<?php echo esc_attr( get_search_query() ); ?>">
                <button type="submit" class="apex-btn apex-btn-primary apex-btn-sm">ARA</button>
            </form>
        </div>
    </div>
</header>

<!-- Tam Ekran Mobil Çekmece (MACFit esintili responsive mobil menü) -->
<div class="apex-mobile-drawer" id="apex-mobile-drawer" role="dialog" aria-modal="true" aria-label="Mobil navigasyon menüsü" hidden>
    <div class="apex-mobile-drawer-head">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="apex-brand">
            <span class="apex-brand-mark" aria-hidden="true">▲</span>
            <span class="apex-brand-text"><span class="apex-brand-accent">APEX</span> ARENA</span>
        </a>
        <button type="button" class="apex-mobile-close" id="apex-mobile-close" aria-label="Menüyü kapat">✕ KAPAT</button>
    </div>
    <nav class="apex-mobile-nav" aria-label="Mobil menü">
        <ul>
            <?php foreach ( $nav_items as $item ) : ?>
                <li><a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a></li>
            <?php endforeach; ?>
            <li><a href="<?php echo esc_url( home_url( '/sss/' ) ); ?>">SIKÇA SORULAN SORULAR</a></li>
            <li><a href="<?php echo esc_url( home_url( '/cerez-politikasi/' ) ); ?>">KVKK & ÇEREZ POLİTİKASI</a></li>
        </ul>
    </nav>
    <div class="apex-mobile-drawer-actions">
        <a href="<?php echo esc_url( home_url( '/uyelik/#paketler' ) ); ?>" class="apex-btn apex-btn-primary apex-btn-block">BİZE KATIL</a>
        <a href="<?php echo esc_url( home_url( '/uyelik/#giris' ) ); ?>" class="apex-btn apex-btn-outline apex-btn-block"><?php echo is_user_logged_in() ? 'ÜYE PANELİM' : 'ÜYE GİRİŞİ'; ?></a>
        <button type="button" class="apex-btn apex-btn-ghost apex-btn-block" data-open-ai>🤖 APEX AI SPOR KOÇU’NA SOR</button>
    </div>
</div>

<main id="main-content" tabindex="-1">
