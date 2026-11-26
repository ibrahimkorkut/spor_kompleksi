<?php
/**
 * Ana Sayfa Şablonu — Modüler Tam Kontrol Mimari + Apple HIG & Liquid Glass Estetiği (v2.4.0)
 * Tüm bölümler ve parçalar WP Admin -> APEX ARENA -> Site İçerik CMS üzerinden eklenip çıkarılabilir ve sıralanabilir.
 *
 * @package ApexArena
 */
get_header();

$settings      = apex_get_settings();
$hidden_posts  = apex_get_hidden_home_posts();
$courts_limit  = (int) apex_cms( 'home_courts_limit', 0 );
$pt_limit      = (int) apex_cms( 'home_pt_limit', 0 );
$tr_limit      = (int) apex_cms( 'home_tr_limit', 0 );
$news_limit    = (int) apex_cms( 'home_news_limit', 3 );
$reviews_limit = max( 1, (int) apex_cms( 'home_reviews_limit', 6 ) );

$courts = get_posts( [
    'post_type'    => 'apex_court',
    'numberposts'  => $courts_limit > 0 ? $courts_limit : -1,
    'post_status'  => 'publish',
    'post__not_in' => $hidden_posts,
    'orderby'      => 'menu_order',
    'order'        => 'ASC',
] );
$trainers = get_posts( [
    'post_type'    => 'apex_trainer',
    'numberposts'  => $tr_limit > 0 ? $tr_limit : -1,
    'post_status'  => 'publish',
    'post__not_in' => $hidden_posts,
    'orderby'      => 'menu_order',
    'order'        => 'ASC',
] );
$packages = get_posts( [
    'post_type'    => 'apex_pt_package',
    'numberposts'  => $pt_limit > 0 ? $pt_limit : -1,
    'post_status'  => 'publish',
    'post__not_in' => $hidden_posts,
    'orderby'      => 'menu_order',
    'order'        => 'ASC',
] );
$anns = ( $news_limit === 0 ) ? [] : get_posts( [
    'post_type'    => 'apex_announcement',
    'numberposts'  => $news_limit,
    'post_status'  => 'publish',
    'post__not_in' => $hidden_posts,
] );

$plans       = apex_get_membership_plans();
$hero_kpis   = apex_get_hero_kpis();
$chapters    = array_values( array_filter( apex_get_hero_chapters(), fn( $ch ) => ! isset( $ch['enabled'] ) || ! empty( $ch['enabled'] ) ) );
$fac_tiles   = apex_get_facility_tiles();
$hero_title  = apex_cms( 'hero_title', get_theme_mod( 'apex_hero_title', 'Sınırlarını Arenada Aş.' ) );
$hero_sub    = apex_cms( 'hero_subtitle', get_theme_mod( 'apex_hero_subtitle', 'İki FIBA basketbol salonu, profesyonel voleybol sahası, lisanslı hakem hizmeti, sertifikalı PT kadrosu ve 7/24 AI Spor Koçu tek çatı altında.' ) );
$home_blocks = apex_get_home_blocks();

foreach ( $home_blocks as $block ) :
    if ( empty( $block['enabled'] ) ) {
        continue;
    }

    // ÖZEL CMS BÖLÜMÜ (Admin Panelden Yeni Eklenen Bloklar)
    if ( ( $block['type'] ?? '' ) === 'custom' ) :
        $layout   = $block['layout'] ?? 'cta_banner';
        $eyebrow  = $block['eyebrow'] ?? '';
        $title    = $block['title'] ?? '';
        $desc     = $block['desc'] ?? '';
        $cta_txt  = $block['cta_text'] ?? '';
        $cta_url  = apex_resolve_cms_url( $block['cta_url'] ?? '/uyelik/#paketler' );
        $img_url  = apex_img_url( $block['image'] ?? 'hero-arena.jpg' );
        $cards    = array_values( array_filter( array_map( 'trim', explode( "\n", (string) ( $block['cards_raw'] ?? '' ) ) ) ) );
        ?>
        <section class="apex-section apex-custom-sec" id="<?php echo esc_attr( $block['id'] ); ?>" aria-label="<?php echo esc_attr( $title ); ?>">
            <div class="apex-container">
                <?php if ( $layout === 'feature_cards' ) : ?>
                    <div class="apex-section-head">
                        <div>
                            <?php if ( $eyebrow ) : ?><span class="apex-eyebrow"><?php echo esc_html( $eyebrow ); ?></span><?php endif; ?>
                            <h2 class="apex-h2"><?php echo esc_html( $title ); ?></h2>
                            <?php if ( $desc ) : ?><p class="apex-lead"><?php echo esc_html( $desc ); ?></p><?php endif; ?>
                        </div>
                        <?php if ( $cta_txt ) : ?>
                            <a href="<?php echo esc_url( $cta_url ); ?>" class="apex-editorial-all-link">
                                <span><?php echo esc_html( $cta_txt ); ?></span>
                                <span class="apex-editorial-arrow" aria-hidden="true">↗</span>
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php if ( ! empty( $cards ) ) : ?>
                    <div class="apex-pt-ledger">
                        <?php foreach ( $cards as $ci => $crow ) :
                            $parts  = array_map( 'trim', explode( '|', $crow ) );
                            $ctitle = $parts[0] ?? '';
                            $cdesc  = $parts[1] ?? '';
                            $ctag   = $parts[2] ?? 'APEX';
                            $cnum   = str_pad( (string) ( $ci + 1 ), 2, '0', STR_PAD_LEFT );
                        ?>
                        <article class="apex-pt-cell">
                            <div class="apex-pt-cell-top">
                                <span class="apex-pt-num"><?php echo esc_html( $cnum ); ?></span>
                                <span class="apex-pt-meta"><?php echo esc_html( strtoupper( $ctag ) ); ?></span>
                            </div>
                            <div class="apex-pt-cell-head">
                                <span class="apex-pt-rule" aria-hidden="true"></span>
                                <h3 class="apex-pt-title"><?php echo esc_html( $ctitle ); ?></h3>
                            </div>
                            <?php if ( $cdesc ) : ?><p class="apex-pt-days"><?php echo esc_html( $cdesc ); ?></p><?php endif; ?>
                        </article>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                <?php elseif ( $layout === 'split_media' ) : ?>
                    <div class="apex-custom-split">
                        <div class="apex-ed-photo-frame">
                            <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
                        </div>
                        <div class="apex-custom-split-body">
                            <?php if ( $eyebrow ) : ?><span class="apex-eyebrow"><?php echo esc_html( $eyebrow ); ?></span><?php endif; ?>
                            <h2 class="apex-h2"><?php echo esc_html( $title ); ?></h2>
                            <?php if ( $desc ) : ?><p class="apex-lead"><?php echo nl2br( esc_html( $desc ) ); ?></p><?php endif; ?>
                            <?php if ( $cta_txt ) : ?>
                                <div style="margin-top:20px;">
                                    <a href="<?php echo esc_url( $cta_url ); ?>" class="apex-btn apex-btn-primary"><?php echo esc_html( $cta_txt ); ?> ↗</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php else : ?>
                    <div class="apex-custom-cta-box">
                        <div>
                            <?php if ( $eyebrow ) : ?><span class="apex-eyebrow"><?php echo esc_html( $eyebrow ); ?></span><?php endif; ?>
                            <h2 class="apex-h2" style="margin-bottom:8px;"><?php echo esc_html( $title ); ?></h2>
                            <?php if ( $desc ) : ?><p class="apex-lead" style="margin:0;"><?php echo nl2br( esc_html( $desc ) ); ?></p><?php endif; ?>
                        </div>
                        <?php if ( $cta_txt ) : ?>
                            <a href="<?php echo esc_url( $cta_url ); ?>" class="apex-btn apex-btn-primary apex-btn-lg"><?php echo esc_html( $cta_txt ); ?> ↗</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <?php
        continue;
    endif;

    switch ( $block['id'] ) :

        case 'hero':
            ?>
            <!-- 1. APPLE-TARZI 60 FPS SCROLL İLE OYNAYAN TAM EKRAN MİMARİ TUR VİDEOSU -->
            <section
                id="apex-cinema-runway"
                class="apex-cinema-runway"
                aria-labelledby="hero-title"
                data-frame-base="<?php echo esc_url( APEX_URI . '/assets/video-frames/' ); ?>"
                data-frame-ver="1.4.1"
                data-frame-count="240"
                data-duration="20.0"
                data-pad="4"
            >
                <div class="apex-cinema-stage">
                    <canvas
                        id="apex-cinema-canvas"
                        class="apex-cinema-canvas"
                        style="background-image:url('<?php echo esc_url( APEX_URI . '/assets/video-frames/frame_0001.webp?v=1.4.1' ); ?>');background-size:cover;background-position:center;"
                        aria-hidden="true"
                    ></canvas>
                    <div class="apex-cinema-vignette" aria-hidden="true"></div>

                    <div class="apex-cinema-layers">
                        <?php $has_ch = apex_cms_show( 'show_hero_chapters' ) && ! empty( $chapters ); ?>
                        <div class="apex-cinema-chapter" data-cinema-chapter="0" data-start="0.00" data-end="<?php echo $has_ch ? '0.38' : '1.00'; ?>">
                            <div class="apex-container">
                                <div class="apex-hero-content">
                                    <?php if ( apex_cms_show( 'show_hero_live_pill' ) ) : ?>
                                    <span class="apex-live-pill">
                                        <span class="apex-live-dot" aria-hidden="true"></span>
                                        Canlı Yoğunluk %<?php echo esc_html( $settings['live_occupancy'] ); ?> · Otopark <?php echo esc_html( $settings['parking_free'] ); ?>/<?php echo esc_html( $settings['parking_total'] ); ?> Boş
                                    </span>
                                    <?php endif; ?>
                                    <h1 id="hero-title" class="apex-h1" style="margin-top:18px;">
                                        <?php echo esc_html( $hero_title ); ?>
                                    </h1>
                                    <p class="apex-lead"><?php echo esc_html( $hero_sub ); ?></p>

                                    <?php if ( apex_cms_show( 'show_hero_cta1' ) || apex_cms_show( 'show_hero_cta2' ) || apex_cms_show( 'show_hero_ai_btn' ) ) : ?>
                                    <div class="apex-hero-ctas">
                                        <?php if ( apex_cms_show( 'show_hero_cta1' ) ) : ?>
                                            <a href="<?php echo esc_url( apex_resolve_cms_url( apex_cms( 'hero_cta1_url', '/uyelik/#paketler' ) ) ); ?>" class="apex-btn apex-btn-primary apex-btn-lg"><?php echo esc_html( apex_cms( 'hero_cta1', 'Üyeliği Başlat' ) ); ?></a>
                                        <?php endif; ?>
                                        <?php if ( apex_cms_show( 'show_hero_cta2' ) ) : ?>
                                            <a href="<?php echo esc_url( apex_resolve_cms_url( apex_cms( 'hero_cta2_url', '/salon-kiralama/' ) ) ); ?>" class="apex-btn apex-btn-outline apex-btn-lg"><?php echo esc_html( apex_cms( 'hero_cta2', 'Salon Kirala' ) ); ?></a>
                                        <?php endif; ?>
                                        <?php if ( apex_cms_show( 'show_hero_ai_btn' ) ) : ?>
                                            <button type="button" class="apex-btn apex-btn-outline apex-btn-lg" data-open-ai>
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 3a9 9 0 0 0-9 9c0 1.66.45 3.22 1.24 4.56L3 21l4.44-1.24A8.96 8.96 0 0 0 12 21a9 9 0 1 0 0-18Z"/></svg>
                                                <?php echo esc_html( apex_cms( 'hero_ai_label', 'AI Spor Koçu' ) ); ?>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <?php if ( apex_cms_show( 'show_hero_kpis' ) && ! empty( $hero_kpis ) ) : ?>
                                <div class="apex-kpi-grid apex-cinema-kpi-compact <?php echo count( $hero_kpis ) !== 4 ? 'apex-kpi-grid-auto' : ''; ?>" aria-label="Kompleks öne çıkan verileri">
                                    <?php foreach ( $hero_kpis as $kpi ) : ?>
                                    <div class="apex-kpi-card">
                                        <div class="apex-kpi-val"><?php echo esc_html( $kpi['val'] ); ?></div>
                                        <div class="apex-kpi-label"><?php echo esc_html( $kpi['lbl'] ); ?></div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ( $has_ch ) :
                            $ch_cnt   = count( $chapters );
                            $span     = ( 1.00 - 0.37 ) / max( 1, $ch_cnt );
                            foreach ( $chapters as $ci => $ch ) :
                                $st  = round( 0.37 + $ci * $span, 2 );
                                $end = ( $ci === $ch_cnt - 1 ) ? 1.00 : round( 0.37 + ( $ci + 1 ) * $span + 0.01, 2 );
                        ?>
                        <div class="apex-cinema-chapter" data-cinema-chapter="<?php echo esc_attr( $ci + 1 ); ?>" data-start="<?php echo esc_attr( number_format( $st, 2, '.', '' ) ); ?>" data-end="<?php echo esc_attr( number_format( $end, 2, '.', '' ) ); ?>" aria-hidden="true">
                            <div class="apex-container">
                                <div class="apex-cinema-story-card">
                                    <?php if ( ! empty( $ch['badge'] ) ) : ?><span class="apex-live-pill"><?php echo esc_html( $ch['badge'] ); ?></span><?php endif; ?>
                                    <h2 class="apex-h1" style="margin-top:16px;"><?php echo esc_html( $ch['title'] ); ?></h2>
                                    <?php if ( ! empty( $ch['desc'] ) ) : ?><p class="apex-lead"><?php echo esc_html( $ch['desc'] ); ?></p><?php endif; ?>
                                    <?php if ( ! empty( $ch['cta1_text'] ) || ! empty( $ch['cta2_text'] ) ) : ?>
                                    <div class="apex-hero-ctas">
                                        <?php if ( ! empty( $ch['cta1_text'] ) ) : ?>
                                            <a href="<?php echo esc_url( apex_resolve_cms_url( $ch['cta1_url'] ?? '/' ) ); ?>" class="apex-btn <?php echo ( $ci % 2 === 1 ) ? 'apex-btn-primary' : 'apex-btn-outline'; ?>"><?php echo esc_html( $ch['cta1_text'] ); ?></a>
                                        <?php endif; ?>
                                        <?php if ( ! empty( $ch['cta2_text'] ) ) : ?>
                                            <a href="<?php echo esc_url( apex_resolve_cms_url( $ch['cta2_url'] ?? '/' ) ); ?>" class="apex-btn apex-btn-outline"><?php echo esc_html( $ch['cta2_text'] ); ?></a>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; endif; ?>
                    </div>
                </div>
            </section>
            <?php
            break;

        case 'dumbbell':
            ?>
            <!-- 2. 3D PERSPEKTİF MONOLİT & 3D DUMBBELL SAHNESİ (WebGL 60 FPS) -->
            <section id="apex-3d-runway" class="apex-3d-runway" aria-labelledby="motion-3d-title">
                <div class="apex-3d-sticky">
                    <div class="apex-3d-monolith" data-3d-monolith>
                        <div class="apex-3d-typo-wrap">
                            <h2 id="motion-3d-title" class="apex-3d-headline">
                                <span class="apex-3d-line" data-3d-line="top"><?php echo esc_html( apex_cms( 'monolith_line1' ) ); ?></span>
                                <span class="apex-3d-line" data-3d-line="bottom"><?php echo esc_html( apex_cms( 'monolith_line2' ) ); ?></span>
                            </h2>
                        </div>
                        <canvas class="apex-3d-canvas" data-3d-canvas aria-hidden="true"></canvas>
                        <div class="apex-3d-bottom-bar">
                            <p class="apex-3d-manifesto"><?php echo nl2br( esc_html( apex_cms( 'monolith_manifesto' ) ) ); ?></p>
                        </div>
                    </div>
                </div>
            </section>
            <?php
            break;

        case 'courts':
            if ( empty( $courts ) ) {
                break;
            }
            ?>
            <!-- 3. SALON VE KİRALAMA — MİMARİ EDİTORYAL SALON GALERİSİ -->
            <section class="apex-section apex-courts-section" aria-labelledby="home-courts-title">
                <div class="apex-container">
                    <div class="apex-section-head">
                        <div>
                            <span class="apex-eyebrow"><?php echo esc_html( apex_cms( 'home_courts_eyebrow' ) ); ?></span>
                            <h2 id="home-courts-title" class="apex-h2"><?php echo esc_html( apex_cms( 'home_courts_title' ) ); ?></h2>
                            <p class="apex-lead"><?php echo esc_html( apex_cms( 'home_courts_desc' ) ); ?></p>
                        </div>
                        <a href="<?php echo esc_url( home_url( '/salon-kiralama/' ) ); ?>" class="apex-editorial-all-link">
                            <span>Tüm Salonlar</span>
                            <span class="apex-editorial-arrow" aria-hidden="true">↗</span>
                        </a>
                    </div>

                    <div class="apex-court-gallery">
                        <?php foreach ( $courts as $idx => $court ) :
                            $sport = get_post_meta( $court->ID, '_apex_sport', true );
                            $dim   = get_post_meta( $court->ID, '_apex_dimensions', true );
                            $surf  = get_post_meta( $court->ID, '_apex_surface', true );
                            $rate  = (int) get_post_meta( $court->ID, '_apex_hourly_rate', true );
                            $ref   = (int) get_post_meta( $court->ID, '_apex_referee_fee', true );
                            $photo = apex_get_post_photo( $court->ID, 'court-basketball-1.jpg' );
                            $num   = str_pad( (string) ( $idx + 1 ), 2, '0', STR_PAD_LEFT );
                        ?>
                        <a href="<?php echo esc_url( home_url( '/salon-kiralama/?court=' . $court->ID . '#rezervasyon' ) ); ?>" class="apex-court-poster">
                            <div class="apex-court-media">
                                <img src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( $court->post_title . ' - ' . $surf ); ?>" loading="lazy">
                                <span class="apex-court-scrim" aria-hidden="true"></span>
                            </div>
                            <div class="apex-court-top">
                                <span class="apex-coach-idx"><?php echo esc_html( $num ); ?></span>
                                <span class="apex-coach-stat"><?php echo esc_html( strtoupper( $sport ) ); ?> <i>/</i> <?php echo esc_html( $dim ); ?></span>
                            </div>
                            <div class="apex-court-body">
                                <span class="apex-coach-rule" aria-hidden="true"></span>
                                <span class="apex-coach-role"><?php echo esc_html( $surf ); ?> · HAKEM +<?php echo esc_html( apex_format_tl( $ref ) ); ?></span>
                                <h3 class="apex-court-title"><?php echo esc_html( $court->post_title ); ?></h3>
                                <p class="apex-court-desc"><?php echo esc_html( wp_trim_words( $court->post_excerpt ?: $court->post_content, 18 ) ); ?></p>
                                <div class="apex-court-foot">
                                    <span class="apex-court-rate"><?php echo esc_html( apex_format_tl( $rate ) ); ?> <em>/ saat</em></span>
                                    <span class="apex-court-action"><span>Rezerve Et</span><span class="apex-coach-link-arrow" aria-hidden="true">↗</span></span>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
            <?php
            break;

        case 'pt':
            if ( empty( $packages ) ) {
                break;
            }
            ?>
            <!-- 4. PT EĞİTMENLER & PAKETLER — MİMARİ EDİTORYAL PROGRAM TABLOSU -->
            <section class="apex-section apex-section-alt apex-pt-section" aria-labelledby="home-pt-title">
                <div class="apex-container">
                    <div class="apex-section-head apex-pt-head">
                        <div>
                            <span class="apex-eyebrow"><?php echo esc_html( apex_cms( 'home_pt_eyebrow' ) ); ?></span>
                            <h2 id="home-pt-title" class="apex-h2"><?php echo esc_html( apex_cms( 'home_pt_title' ) ); ?></h2>
                            <p class="apex-lead"><?php echo esc_html( apex_cms( 'home_pt_desc' ) ); ?></p>
                        </div>
                        <div class="apex-pt-filter-bar" role="tablist" aria-label="PT paket filtresi">
                            <button type="button" class="apex-pt-tab is-active" data-filter-group="home-pt" data-filter="all" role="tab" aria-selected="true">Tümü</button>
                            <button type="button" class="apex-pt-tab" data-filter-group="home-pt" data-filter="bireysel" role="tab" aria-selected="false">Bireysel</button>
                            <button type="button" class="apex-pt-tab" data-filter-group="home-pt" data-filter="grupsal" role="tab" aria-selected="false">Grupsal</button>
                        </div>
                    </div>

                    <div class="apex-pt-ledger" data-filter-container="home-pt">
                        <?php foreach ( $packages as $idx => $pkg ) :
                            $type  = get_post_meta( $pkg->ID, '_apex_training_type', true ) ?: 'bireysel';
                            $sess  = get_post_meta( $pkg->ID, '_apex_sessions', true );
                            $per   = (int) get_post_meta( $pkg->ID, '_apex_per_session_fee', true );
                            $total = (int) get_post_meta( $pkg->ID, '_apex_total_price', true );
                            $days  = get_post_meta( $pkg->ID, '_apex_days', true );
                            $scope = array_filter( array_map( 'trim', explode( "\n", (string) get_post_meta( $pkg->ID, '_apex_scope', true ) ) ) );
                            $feat  = get_post_meta( $pkg->ID, '_apex_featured', true ) === '1';
                            $num   = str_pad( (string) ( $idx + 1 ), 2, '0', STR_PAD_LEFT );
                        ?>
                        <article class="apex-pt-cell <?php echo $feat ? 'is-featured' : ''; ?>" data-filter-item="<?php echo esc_attr( $type ); ?>">
                            <div class="apex-pt-cell-top">
                                <span class="apex-pt-num"><?php echo esc_html( $num ); ?></span>
                                <span class="apex-pt-meta"><?php echo $type === 'grupsal' ? 'GRUPSAL' : 'BİREYSEL PT'; ?> <i>/</i> <?php echo esc_html( $sess ); ?> SEANS</span>
                                <?php if ( $feat ) : ?><span class="apex-pt-pick"><i aria-hidden="true"></i>Önerilen</span><?php endif; ?>
                            </div>
                            <div class="apex-pt-cell-head">
                                <span class="apex-pt-rule" aria-hidden="true"></span>
                                <h3 class="apex-pt-title"><?php echo esc_html( $pkg->post_title ); ?></h3>
                            </div>
                            <div class="apex-pt-price-row">
                                <span class="apex-pt-total"><?php echo esc_html( apex_format_tl( $total ) ); ?></span>
                                <span class="apex-pt-unit"><?php echo esc_html( apex_format_tl( $per ) ); ?> <em>/ seans</em></span>
                            </div>
                            <p class="apex-pt-days"><?php echo esc_html( $days ); ?></p>
                            <ul class="apex-pt-list">
                                <?php foreach ( array_slice( $scope, 0, 4 ) as $line ) : ?><li><?php echo esc_html( $line ); ?></li><?php endforeach; ?>
                            </ul>
                            <a href="<?php echo esc_url( home_url( '/pt-egitmenler/?package=' . $pkg->ID . '#randevu' ) ); ?>" class="apex-pt-cta">
                                <span>Randevu Oluştur</span><span class="apex-pt-cta-arrow" aria-hidden="true">↗</span>
                            </a>
                        </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
            <?php
            break;

        case 'trainers':
            if ( empty( $trainers ) ) {
                break;
            }
            ?>
            <!-- 5. ANTRENÖRLER — EDİTORYAL PORTRE GALERİSİ -->
            <section class="apex-section apex-coaches-section" aria-labelledby="home-trainers-title">
                <div class="apex-container">
                    <div class="apex-section-head apex-coaches-head">
                        <div>
                            <span class="apex-eyebrow"><?php echo esc_html( apex_cms( 'home_tr_eyebrow' ) ); ?></span>
                            <h2 id="home-trainers-title" class="apex-h2"><?php echo esc_html( apex_cms( 'home_tr_title' ) ); ?></h2>
                            <p class="apex-lead"><?php echo esc_html( apex_cms( 'home_tr_desc' ) ); ?></p>
                        </div>
                        <a href="<?php echo esc_url( home_url( '/antrenorler/' ) ); ?>" class="apex-editorial-all-link">
                            <span>Tüm Kadroyu Gör</span>
                            <span class="apex-editorial-arrow" aria-hidden="true">↗</span>
                        </a>
                    </div>

                    <div class="apex-coach-gallery">
                        <?php foreach ( $trainers as $idx => $tr ) :
                            $role  = get_post_meta( $tr->ID, '_apex_role', true );
                            $spec  = get_post_meta( $tr->ID, '_apex_specialty', true );
                            $exp   = get_post_meta( $tr->ID, '_apex_experience', true );
                            $rate  = get_post_meta( $tr->ID, '_apex_rating', true ) ?: '4.9';
                            $photo = apex_get_post_photo( $tr->ID, 'trainer-kerem.jpg' );
                            $num   = str_pad( (string) ( $idx + 1 ), 2, '0', STR_PAD_LEFT );
                        ?>
                        <a href="<?php echo esc_url( get_permalink( $tr->ID ) ); ?>" class="apex-coach-poster">
                            <div class="apex-coach-poster-media">
                                <img src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( $tr->post_title . ' - ' . $role ); ?>" loading="lazy">
                                <span class="apex-coach-poster-scrim" aria-hidden="true"></span>
                            </div>
                            <div class="apex-coach-poster-top">
                                <span class="apex-coach-idx"><?php echo esc_html( $num ); ?></span>
                                <span class="apex-coach-stat"><?php echo esc_html( $exp ); ?> YIL <i>/</i> <?php echo esc_html( $rate ); ?></span>
                            </div>
                            <div class="apex-coach-poster-body">
                                <span class="apex-coach-rule" aria-hidden="true"></span>
                                <span class="apex-coach-role"><?php echo esc_html( $role ); ?></span>
                                <h3 class="apex-coach-name"><?php echo esc_html( $tr->post_title ); ?></h3>
                                <p class="apex-coach-spec"><?php echo esc_html( $spec ); ?></p>
                                <span class="apex-coach-link"><span>Profil &amp; Takvim</span><span class="apex-coach-link-arrow" aria-hidden="true">↗</span></span>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
            <?php
            break;

        case 'facilities':
            if ( empty( $fac_tiles ) ) {
                break;
            }
            $is_three = ( count( $fac_tiles ) === 3 );
            ?>
            <!-- 6. TESİS OLANAKLARI — MİMARİ EDİTORYAL BENTO GALERİSİ -->
            <section class="apex-section apex-section-alt apex-fac-section" aria-labelledby="home-fac-title">
                <div class="apex-container">
                    <div class="apex-section-head">
                        <div>
                            <span class="apex-eyebrow"><?php echo esc_html( apex_cms( 'home_fac_eyebrow' ) ); ?></span>
                            <h2 id="home-fac-title" class="apex-h2"><?php echo esc_html( apex_cms( 'home_fac_title' ) ); ?></h2>
                        </div>
                        <a href="<?php echo esc_url( home_url( '/tesis-olanaklari/' ) ); ?>" class="apex-editorial-all-link">
                            <span>Olanaklar &amp; Menü</span>
                            <span class="apex-editorial-arrow" aria-hidden="true">↗</span>
                        </a>
                    </div>

                    <div class="<?php echo $is_three ? 'apex-fac-bento' : 'apex-fac-bento-grid'; ?>">
                        <?php foreach ( $fac_tiles as $fi => $ft ) :
                            $num  = str_pad( (string) ( $fi + 1 ), 2, '0', STR_PAD_LEFT );
                            $stat = ! empty( $ft['is_live_parking'] ) ? ( $settings['parking_free'] . ' BOŞ YER / 6× 22 KW EV' ) : ( $ft['stat'] ?? '' );
                            $tall = ( $is_three && $fi === 0 ) ? ' apex-fac-tile-tall' : '';
                        ?>
                        <a href="<?php echo esc_url( apex_resolve_cms_url( $ft['url'] ?? '/tesis-olanaklari/' ) ); ?>" class="apex-fac-tile<?php echo $tall; ?>">
                            <div class="apex-fac-media">
                                <img src="<?php echo esc_url( apex_img_url( $ft['image'] ?? 'facility-locker.jpg' ) ); ?>" alt="<?php echo esc_attr( $ft['title'] ?? 'Tesis' ); ?>" loading="lazy">
                                <span class="apex-fac-scrim" aria-hidden="true"></span>
                            </div>
                            <div class="apex-fac-top">
                                <span class="apex-coach-idx"><?php echo esc_html( $num ); ?></span>
                                <span class="apex-coach-stat"><?php echo esc_html( $stat ); ?></span>
                            </div>
                            <div class="apex-fac-body">
                                <span class="apex-coach-rule" aria-hidden="true"></span>
                                <span class="apex-coach-role"><?php echo esc_html( $ft['role'] ?? '' ); ?></span>
                                <h3 class="apex-fac-title"><?php echo esc_html( $ft['title'] ?? '' ); ?></h3>
                                <p class="apex-fac-desc"><?php echo esc_html( $ft['desc'] ?? '' ); ?></p>
                                <span class="apex-coach-link">
                                    <span><?php echo esc_html( $ft['cta'] ?? 'Tesis Detayları' ); ?></span>
                                    <span class="apex-coach-link-arrow" aria-hidden="true">↗</span>
                                </span>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
            <?php
            break;

        case 'membership':
            if ( empty( $plans ) ) {
                break;
            }
            ?>
            <!-- 7. ÜYELİK PAKETLERİ — MİMARİ EDİTORYAL ÜYELİK TABLOSU -->
            <section class="apex-section" aria-labelledby="home-mem-title">
                <div class="apex-container">
                    <div class="apex-section-head">
                        <div>
                            <span class="apex-eyebrow"><?php echo esc_html( apex_cms( 'home_mem_eyebrow' ) ); ?></span>
                            <h2 id="home-mem-title" class="apex-h2"><?php echo esc_html( apex_cms( 'home_mem_title' ) ); ?></h2>
                        </div>
                        <a href="<?php echo esc_url( home_url( '/uyelik/' ) ); ?>" class="apex-editorial-all-link">
                            <span>Karşılaştırma &amp; Panel</span>
                            <span class="apex-editorial-arrow" aria-hidden="true">↗</span>
                        </a>
                    </div>

                    <div class="apex-pt-ledger">
                        <?php $m_idx = 0; foreach ( $plans as $pl ) : $m_idx++; $num = str_pad( (string) $m_idx, 2, '0', STR_PAD_LEFT ); ?>
                        <article class="apex-pt-cell <?php echo ! empty( $pl['featured'] ) ? 'is-featured' : ''; ?>">
                            <div class="apex-pt-cell-top">
                                <span class="apex-pt-num"><?php echo esc_html( $num ); ?></span>
                                <span class="apex-pt-meta"><?php echo esc_html( $pl['badge'] ); ?></span>
                                <?php if ( ! empty( $pl['featured'] ) ) : ?><span class="apex-pt-pick"><i aria-hidden="true"></i>En Popüler</span><?php endif; ?>
                            </div>
                            <div class="apex-pt-cell-head">
                                <span class="apex-pt-rule" aria-hidden="true"></span>
                                <h3 class="apex-pt-title" style="min-height:1.1em;"><?php echo esc_html( $pl['name'] ); ?></h3>
                            </div>
                            <div class="apex-pt-price-row">
                                <span class="apex-pt-total"><?php echo esc_html( apex_format_tl( $pl['monthly_price'] ) ); ?></span>
                                <span class="apex-pt-unit"><?php echo esc_html( apex_format_tl( $pl['annual_price'] ) ); ?> <em>/ yıllık ay</em></span>
                            </div>
                            <p class="apex-pt-days"><?php echo esc_html( $pl['summary'] ); ?></p>
                            <ul class="apex-pt-list">
                                <?php foreach ( $pl['features'] as $f ) : ?><li><?php echo esc_html( $f ); ?></li><?php endforeach; ?>
                            </ul>
                            <a href="<?php echo esc_url( home_url( '/uyelik/?plan=' . $pl['slug'] . '#kayit' ) ); ?>" class="apex-pt-cta">
                                <span>Üyeliği Başlat</span><span class="apex-pt-cta-arrow" aria-hidden="true">↗</span>
                            </a>
                        </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
            <?php
            break;

        case 'announcements':
            if ( empty( $anns ) ) {
                break;
            }
            ?>
            <!-- 8. SALON DUYURULARI — MİMARİ EDİTORYAL BÜLTEN -->
            <section class="apex-section apex-section-alt" aria-labelledby="home-news-title">
                <div class="apex-container">
                    <div class="apex-section-head">
                        <div>
                            <span class="apex-eyebrow"><?php echo esc_html( apex_cms( 'home_news_eyebrow' ) ); ?></span>
                            <h2 id="home-news-title" class="apex-h2"><?php echo esc_html( apex_cms( 'home_news_title' ) ); ?></h2>
                        </div>
                        <a href="<?php echo esc_url( home_url( '/duyurular/' ) ); ?>" class="apex-editorial-all-link">
                            <span>Tüm Duyurular</span>
                            <span class="apex-editorial-arrow" aria-hidden="true">↗</span>
                        </a>
                    </div>

                    <div class="apex-pt-ledger">
                        <?php foreach ( $anns as $a_idx => $ann ) :
                            $cat  = get_post_meta( $ann->ID, '_apex_ann_category', true ) ?: 'Duyuru';
                            $loc  = get_post_meta( $ann->ID, '_apex_ann_location', true );
                            $dt   = get_post_meta( $ann->ID, '_apex_ann_date', true ) ?: get_the_date( 'd.m.Y', $ann->ID );
                            $num  = str_pad( (string) ( $a_idx + 1 ), 2, '0', STR_PAD_LEFT );
                        ?>
                        <article class="apex-pt-cell">
                            <div class="apex-pt-cell-top">
                                <span class="apex-pt-num"><?php echo esc_html( $num ); ?></span>
                                <span class="apex-pt-meta"><?php echo esc_html( strtoupper( $cat ) ); ?> <i>/</i> <?php echo esc_html( $dt ); ?></span>
                            </div>
                            <div class="apex-pt-cell-head">
                                <span class="apex-pt-rule" aria-hidden="true"></span>
                                <h3 class="apex-pt-title" style="min-height:2.15em;"><?php echo esc_html( $ann->post_title ); ?></h3>
                            </div>
                            <p class="apex-pt-days" style="flex:1;margin-top:0;font-size:0.86rem;color:rgba(212,212,216,0.82);"><?php echo esc_html( wp_trim_words( $ann->post_excerpt ?: $ann->post_content, 24 ) ); ?></p>
                            <a href="<?php echo esc_url( get_permalink( $ann->ID ) ); ?>" class="apex-pt-cta">
                                <span><?php echo esc_html( $loc ?: 'Apex Arena' ); ?> · İncele</span><span class="apex-pt-cta-arrow" aria-hidden="true">↗</span>
                            </a>
                        </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
            <?php
            break;

        case 'arch3d':
            ?>
            <!-- 9. İNTERAKTİF 3D MİMARİ KESİT SPOR KOMPLEKSİ -->
            <section class="apex-section" id="kat-plani" aria-labelledby="home-floorplan-title">
                <div class="apex-container">
                    <div class="apex-section-head">
                        <div>
                            <span class="apex-eyebrow"><?php echo esc_html( apex_cms( 'home_arch_eyebrow' ) ); ?></span>
                            <h2 id="home-floorplan-title" class="apex-h2"><?php echo esc_html( apex_cms( 'home_arch_title' ) ); ?></h2>
                        </div>
                        <p class="apex-muted" style="max-width:46ch;margin:0;"><?php echo esc_html( apex_cms( 'home_arch_desc' ) ); ?></p>
                    </div>

                    <div class="apex-fp-layout apex-arch-layout">
                        <div class="apex-arch-stage-card" id="apex-arch-3d-stage">
                            <canvas class="apex-arch-canvas" data-arch-canvas aria-label="APEX ARENA 3D spor kompleksi mimari kesit maketi"></canvas>
                            <div class="apex-arch-hud-top">
                                <div class="apex-arch-hud-pill" id="apex-arch-hud-pill">
                                    <span class="apex-live-dot" aria-hidden="true"></span>
                                    <div>
                                        <strong id="apex-arch-hud-title">3D Kompleks Mimarisi · 6 Aktif Bölge</strong>
                                        <span id="apex-arch-hud-hint">Mouse ile bir odanın üstüne gel — kamera 3D içine yaklaşır</span>
                                    </div>
                                </div>
                                <div class="apex-arch-hud-actions">
                                    <button type="button" class="apex-arch-ctrl-btn" id="apex-arch-reset-btn" hidden>← Genel Bakış</button>
                                    <button type="button" class="apex-arch-ctrl-btn" id="apex-arch-orbit-btn" title="Otomatik 3D turu aç/kapat">⟳ 3D Tur</button>
                                </div>
                            </div>
                            <div class="apex-arch-chips" role="tablist" aria-label="3D spor kompleksi alanları">
                                <button type="button" class="apex-arch-chip" data-arch-chip="basket1">🏀 Basketbol 1</button>
                                <button type="button" class="apex-arch-chip" data-arch-chip="lobby">🌀 Lobi &amp; EV</button>
                                <button type="button" class="apex-arch-chip" data-arch-chip="voleybol">🏐 Voleybol Pro</button>
                                <button type="button" class="apex-arch-chip" data-arch-chip="pt">🏋️ PT &amp; Kuvvet</button>
                                <button type="button" class="apex-arch-chip" data-arch-chip="spa">🔥 Sauna &amp; Duş</button>
                                <button type="button" class="apex-arch-chip" data-arch-chip="cafe">🥤 Protein Kafe</button>
                            </div>
                        </div>

                        <aside class="apex-panel apex-fp-detail" id="apex-fp-detail" aria-live="polite">
                            <span class="apex-eyebrow" id="fp-d-tag">FIBA STANDARTI · KUZEYBATI KANAT</span>
                            <h3 class="apex-h2" id="fp-d-title" style="margin:6px 0 10px;">Arena Basketbol 1 (FIBA Parke)</h3>
                            <p class="apex-muted" id="fp-d-desc" style="margin:0 0 18px;">28×15m uluslararası FIBA ölçülerinde esnek akçaağaç parke, çift cam panyalı profesyonel pota, LED skorboard ve isteğe bağlı lisanslı TBF hakem hizmeti.</p>
                            <div class="apex-fp-specs" id="fp-d-specs">
                                <span>🏀 28×15m FIBA Parke</span>
                                <span>⚖️ Lisanslı Hakem (+₺450)</span>
                                <span>📺 4 Yönlü LED Skorboard</span>
                            </div>
                            <a href="<?php echo esc_url( home_url( '/salon-kiralama/#rezervasyon' ) ); ?>" id="fp-d-cta" class="apex-btn apex-btn-primary apex-btn-block" style="margin-top:20px;">Basketbol Salonunu Kirala →</a>
                        </aside>
                    </div>
                </div>
            </section>
            <?php
            break;

        case 'reviews':
            global $wpdb;
            $apex_reviews = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}apex_reviews WHERE status = 'published' ORDER BY id DESC LIMIT %d", $reviews_limit ) );
            $apex_rev_avg = round( (float) $wpdb->get_var( "SELECT COALESCE(AVG(rating), 4.9) FROM {$wpdb->prefix}apex_reviews WHERE status = 'published'" ), 1 );
            $apex_rev_cnt = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}apex_reviews WHERE status = 'published'" );
            ?>
            <!-- 10. ÜYE PUANLAMA & CANLI DEĞERLENDİRMELER -->
            <section class="apex-section apex-section-alt" id="yorumlar" aria-labelledby="home-reviews-title">
                <div class="apex-container">
                    <div class="apex-section-head">
                        <div>
                            <span class="apex-eyebrow"><?php echo esc_html( apex_cms( 'home_rev_eyebrow' ) ); ?></span>
                            <h2 id="home-reviews-title" class="apex-h2"><?php echo esc_html( apex_cms( 'home_rev_title' ) ); ?></h2>
                        </div>
                        <div class="apex-rev-summary">
                            <strong class="apex-rev-score" id="apex-rev-avg"><?php echo esc_html( number_format( $apex_rev_avg, 1 ) ); ?></strong>
                            <div>
                                <div class="apex-stars" aria-hidden="true">★★★★★</div>
                                <small class="apex-muted"><span id="apex-rev-count"><?php echo esc_html( $apex_rev_cnt ); ?></span> üye değerlendirmesi</small>
                            </div>
                        </div>
                    </div>

                    <div class="apex-grid-3" id="apex-reviews-grid">
                        <?php foreach ( $apex_reviews as $rv ) : ?>
                        <article class="apex-card apex-rev-card">
                            <div class="apex-card-body">
                                <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;">
                                    <span class="apex-stars" aria-label="<?php echo esc_attr( $rv->rating ); ?> üzerinden 5 yıldız"><?php echo str_repeat( '★', (int) $rv->rating ) . str_repeat( '☆', max( 0, 5 - (int) $rv->rating ) ); ?></span>
                                    <span class="apex-tag"><?php echo esc_html( $rv->member_plan ); ?></span>
                                </div>
                                <p style="margin:6px 0 0;font-size:0.92rem;color:var(--apex-text);">“<?php echo esc_html( $rv->comment ); ?>”</p>
                                <div class="apex-card-foot">
                                    <strong><?php echo esc_html( $rv->author_name ); ?></strong>
                                    <span class="apex-muted" style="font-size:0.78rem;"><?php echo esc_html( date( 'd.m.Y', strtotime( $rv->created_at ) ) ); ?></span>
                                </div>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>

                    <?php if ( apex_cms_show( 'show_review_form' ) ) : ?>
                    <div class="apex-panel" style="margin-top:28px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:16px;">
                            <div>
                                <h3 class="apex-h3" style="margin:0;">Deneyimini Puanla</h3>
                                <p class="apex-muted" style="margin:4px 0 0;font-size:0.86rem;">Salonlar, PT koçluğu, kafe veya tesis olanakları hakkındaki görüşün anında yayınlanır.</p>
                            </div>
                            <div class="apex-star-picker" id="apex-star-picker" role="radiogroup" aria-label="Puanınız">
                                <?php for ( $st = 1; $st <= 5; $st++ ) : ?>
                                    <button type="button" class="apex-star-btn is-active" data-star-val="<?php echo esc_attr( $st ); ?>" aria-label="<?php echo esc_attr( $st ); ?> yıldız">★</button>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <form id="apex-review-form" class="apex-form-grid" novalidate>
                            <input type="hidden" id="rev-rating" name="rating" value="5">
                            <div class="apex-field">
                                <label for="rev-name">Ad Soyad *</label>
                                <input type="text" id="rev-name" name="author_name" placeholder="Örn. Selin Demir" required>
                            </div>
                            <div class="apex-field">
                                <label for="rev-plan">Üyelik / Hizmet</label>
                                <select id="rev-plan" name="member_plan">
                                    <?php foreach ( $plans as $pl ) : ?>
                                        <option value="<?php echo esc_attr( $pl['name'] ); ?>"><?php echo esc_html( $pl['name'] ); ?></option>
                                    <?php endforeach; ?>
                                    <option value="Salon Misafiri">Salon Kiralama Misafiri</option>
                                </select>
                            </div>
                            <div class="apex-field apex-field-full">
                                <label for="rev-comment">Yorumunuz *</label>
                                <textarea id="rev-comment" name="comment" rows="2" placeholder="APEX ARENA deneyiminizi birkaç cümleyle paylaşın..." required></textarea>
                            </div>
                            <div class="apex-field apex-field-full" style="flex-direction:row;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                                <button type="submit" class="apex-btn apex-btn-primary">Değerlendirmeyi Yayınla</button>
                                <div id="rev-result" role="status" aria-live="polite"></div>
                            </div>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            </section>
            <?php
            break;

    endswitch;
endforeach;
?>

<?php get_footer(); ?>
