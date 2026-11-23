<?php
/**
 * Tekil Yazı Şablonu
 *
 * @package ApexArena
 */
get_header();
while ( have_posts() ) : the_post();
?>
<section class="apex-page-hero">
    <div class="apex-container">
        <?php apex_breadcrumbs(); ?>
        <span class="apex-eyebrow"><?php echo esc_html( get_the_date( 'd.m.Y' ) ); ?></span>
        <h1 class="apex-h1"><?php the_title(); ?></h1>
    </div>
</section>
<section class="apex-section">
    <div class="apex-container">
        <article class="apex-prose">
            <?php the_content(); ?>
        </article>
    </div>
</section>
<?php endwhile; get_footer(); ?>
