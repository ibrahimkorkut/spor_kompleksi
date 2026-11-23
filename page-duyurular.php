<?php
/**
 * Template Name: Salon Duyuruları
 *
 * @package ApexArena
 */
get_header();

$anns = get_posts( [ 'post_type' => 'apex_announcement', 'numberposts' => 30, 'post_status' => 'publish' ] );
?>

<section class="apex-page-hero">
    <div class="apex-container">
        <?php apex_breadcrumbs(); ?>
        <span class="apex-eyebrow apex-eyebrow-volt">SALON DUYURULARI & ETKİNLİK TAKVİMİ</span>
        <h1 class="apex-h1">SALON DUYURULARI VE TURNUVALAR</h1>
        <p class="apex-lead">Basketbol ve voleybol turnuvaları, salon bakım duyuruları, yeni grup dersleri ve üye kampanyalarını buradan takip edebilirsiniz.</p>
    </div>
</section>

<section class="apex-section">
    <div class="apex-container">
        <div class="apex-tabs" style="margin-bottom:28px;" role="tablist" aria-label="Duyuru kategori filtresi">
            <button type="button" class="apex-tab-btn is-active" data-filter-group="anns" data-filter="all" role="tab" aria-selected="true">TÜMÜ</button>
            <button type="button" class="apex-tab-btn" data-filter-group="anns" data-filter="Turnuva" role="tab" aria-selected="false">TURNUVA</button>
            <button type="button" class="apex-tab-btn" data-filter-group="anns" data-filter="Salon Duyurusu" role="tab" aria-selected="false">SALON DUYURUSU</button>
            <button type="button" class="apex-tab-btn" data-filter-group="anns" data-filter="Kampanya" role="tab" aria-selected="false">KAMPANYA</button>
            <button type="button" class="apex-tab-btn" data-filter-group="anns" data-filter="Bakım & Saatler" role="tab" aria-selected="false">BAKIM & SAATLER</button>
        </div>

        <div class="apex-grid-3" data-filter-container="anns">
            <?php foreach ( $anns as $ann ) :
                $cat  = get_post_meta( $ann->ID, '_apex_ann_category', true ) ?: 'Salon Duyurusu';
                $prio = get_post_meta( $ann->ID, '_apex_ann_priority', true ) ?: 'normal';
                $loc  = get_post_meta( $ann->ID, '_apex_ann_location', true ) ?: 'APEX ARENA';
                $dt   = get_post_meta( $ann->ID, '_apex_ann_date', true ) ?: get_the_date( 'd.m.Y', $ann->ID );
            ?>
            <article class="apex-card <?php echo $prio === 'urgent' ? 'apex-card-featured' : ''; ?>" data-filter-item="<?php echo esc_attr( $cat ); ?>">
                <div class="apex-card-body">
                    <div style="display:flex;justify-content:space-between;gap:8px;align-items:center;flex-wrap:wrap;">
                        <span class="apex-tag apex-tag-volt"><?php echo esc_html( $cat ); ?></span>
                        <span class="apex-muted" style="font-size:0.82rem;">📅 <?php echo esc_html( $dt ); ?></span>
                    </div>
                    <h2 class="apex-h3"><?php echo esc_html( $ann->post_title ); ?></h2>
                    <p class="apex-muted" style="margin:0;"><?php echo esc_html( wp_trim_words( $ann->post_excerpt ?: $ann->post_content, 28 ) ); ?></p>
                    <div class="apex-card-foot">
                        <span class="apex-muted" style="font-size:0.84rem;">📍 <?php echo esc_html( $loc ); ?></span>
                        <a href="<?php echo esc_url( get_permalink( $ann->ID ) ); ?>" class="apex-btn apex-btn-outline apex-btn-sm">DEVAMINI OKU</a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
