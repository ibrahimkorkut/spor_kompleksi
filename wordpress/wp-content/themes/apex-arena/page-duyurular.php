<?php
/**
 * Template Name: Salon Duyuruları
 *
 * @package ApexArena
 */
get_header();

$anns = get_posts( [ 'post_type' => 'apex_announcement', 'numberposts' => 30, 'post_status' => 'publish' ] );
?>

<section class="apex-page-hero apex-editorial-hero">
    <div class="apex-container">
        <?php apex_breadcrumbs(); ?>
        <span class="apex-eyebrow"><?php echo esc_html( apex_cms( 'page_news_eyebrow' ) ); ?></span>
        <h1 class="apex-h1"><?php echo esc_html( apex_cms( 'page_news_title' ) ); ?></h1>
        <p class="apex-lead"><?php echo esc_html( apex_cms( 'page_news_desc' ) ); ?></p>
    </div>
</section>

<section class="apex-section">
    <div class="apex-container">
        <div class="apex-pt-filter-bar" style="margin-bottom:28px;" role="tablist" aria-label="Duyuru kategori filtresi">
            <button type="button" class="apex-pt-tab is-active" data-filter-group="anns" data-filter="all" role="tab" aria-selected="true">Tümü</button>
            <button type="button" class="apex-pt-tab" data-filter-group="anns" data-filter="Turnuva" role="tab" aria-selected="false">Turnuva</button>
            <button type="button" class="apex-pt-tab" data-filter-group="anns" data-filter="Salon Duyurusu" role="tab" aria-selected="false">Salon Duyurusu</button>
            <button type="button" class="apex-pt-tab" data-filter-group="anns" data-filter="Kampanya" role="tab" aria-selected="false">Kampanya</button>
            <button type="button" class="apex-pt-tab" data-filter-group="anns" data-filter="Bakım & Saatler" role="tab" aria-selected="false">Bakım &amp; Saatler</button>
        </div>

        <div class="apex-pt-ledger" data-filter-container="anns">
            <?php foreach ( $anns as $idx => $ann ) :
                $cat  = get_post_meta( $ann->ID, '_apex_ann_category', true ) ?: 'Salon Duyurusu';
                $prio = get_post_meta( $ann->ID, '_apex_ann_priority', true ) ?: 'normal';
                $loc  = get_post_meta( $ann->ID, '_apex_ann_location', true ) ?: 'APEX ARENA';
                $dt   = get_post_meta( $ann->ID, '_apex_ann_date', true ) ?: get_the_date( 'd.m.Y', $ann->ID );
                $num  = str_pad( (string) ( $idx + 1 ), 2, '0', STR_PAD_LEFT );
            ?>
            <article class="apex-pt-cell <?php echo $prio === 'urgent' ? 'is-featured' : ''; ?>" data-filter-item="<?php echo esc_attr( $cat ); ?>">
                <div class="apex-pt-cell-top">
                    <span class="apex-pt-num"><?php echo esc_html( $num ); ?></span>
                    <span class="apex-pt-meta"><?php echo esc_html( strtoupper( $cat ) ); ?> <i>/</i> <?php echo esc_html( $dt ); ?></span>
                    <?php if ( $prio === 'urgent' ) : ?><span class="apex-pt-pick"><i aria-hidden="true"></i>Önemli</span><?php endif; ?>
                </div>
                <div class="apex-pt-cell-head">
                    <span class="apex-pt-rule" aria-hidden="true"></span>
                    <h2 class="apex-pt-title" style="min-height:2.15em;"><?php echo esc_html( $ann->post_title ); ?></h2>
                </div>
                <p class="apex-pt-days" style="flex:1;margin-top:0;font-size:0.86rem;color:rgba(212,212,216,0.82);"><?php echo esc_html( wp_trim_words( $ann->post_excerpt ?: $ann->post_content, 26 ) ); ?></p>
                <a href="<?php echo esc_url( get_permalink( $ann->ID ) ); ?>" class="apex-pt-cta">
                    <span><?php echo esc_html( $loc ); ?> · Devamını Oku</span>
                    <span class="apex-pt-cta-arrow" aria-hidden="true">↗</span>
                </a>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
