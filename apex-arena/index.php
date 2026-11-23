<?php
/**
 * Ana Fallback Şablonu (WordPress zorunlu dosya)
 *
 * @package ApexArena
 */
get_header();
?>
<section class="apex-page-hero">
    <div class="apex-container">
        <span class="apex-eyebrow">APEX ARENA</span>
        <h1 class="apex-h1"><?php echo esc_html( is_home() ? 'HABERLER & DUYURULAR' : get_the_archive_title() ); ?></h1>
    </div>
</section>
<section class="apex-section">
    <div class="apex-container">
        <?php if ( have_posts() ) : ?>
            <div class="apex-grid-3">
                <?php while ( have_posts() ) : the_post(); ?>
                <article class="apex-card">
                    <div class="apex-card-body">
                        <span class="apex-muted" style="font-size:0.82rem;"><?php echo esc_html( get_the_date( 'd.m.Y' ) ); ?></span>
                        <h2 class="apex-h3"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <p class="apex-muted" style="margin:0;"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
                        <div class="apex-card-foot">
                            <a href="<?php the_permalink(); ?>" class="apex-btn apex-btn-outline apex-btn-sm">DEVAMINI OKU</a>
                        </div>
                    </div>
                </article>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <div class="apex-panel"><p class="apex-muted">Henüz içerik eklenmemiş.</p></div>
        <?php endif; ?>
    </div>
</section>
<?php get_footer(); ?>
