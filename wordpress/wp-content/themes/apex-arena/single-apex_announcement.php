<?php
/**
 * Tekil Salon Duyurusu Şablonu
 *
 * @package ApexArena
 */
get_header();
while ( have_posts() ) : the_post();
    $cat = get_post_meta( get_the_ID(), '_apex_ann_category', true ) ?: 'Salon Duyurusu';
    $loc = get_post_meta( get_the_ID(), '_apex_ann_location', true ) ?: 'APEX ARENA';
    $dt  = get_post_meta( get_the_ID(), '_apex_ann_date', true ) ?: get_the_date( 'd.m.Y' );
?>
<section class="apex-page-hero">
    <div class="apex-container">
        <?php apex_breadcrumbs(); ?>
        <span class="apex-eyebrow apex-eyebrow-volt"><?php echo esc_html( $cat . ' · ' . $dt ); ?></span>
        <h1 class="apex-h1"><?php the_title(); ?></h1>
        <p class="apex-lead">📍 <?php echo esc_html( $loc ); ?></p>
    </div>
</section>
<section class="apex-section">
    <div class="apex-container">
        <article class="apex-prose">
            <?php the_content(); ?>
            <div style="margin-top:28px;display:flex;gap:12px;flex-wrap:wrap;">
                <a href="<?php echo esc_url( home_url( '/salon-kiralama/' ) ); ?>" class="apex-btn apex-btn-primary">SALON REZERVASYONU YAP</a>
                <a href="<?php echo esc_url( home_url( '/duyurular/' ) ); ?>" class="apex-btn apex-btn-outline">← TÜM DUYURULARA DÖN</a>
            </div>
        </article>
    </div>
</section>
<?php endwhile; get_footer(); ?>
