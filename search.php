<?php
/**
 * Site İçi Arama Sonuçları Şablonu
 *
 * @package ApexArena
 */
get_header();
?>
<section class="apex-page-hero">
    <div class="apex-container">
        <?php apex_breadcrumbs(); ?>
        <span class="apex-eyebrow">SİTE İÇİ ARAMA</span>
        <h1 class="apex-h1">“<?php echo esc_html( get_search_query() ); ?>” ARAMA SONUÇLARI</h1>
    </div>
</section>
<section class="apex-section">
    <div class="apex-container">
        <?php if ( have_posts() ) : ?>
            <div class="apex-grid-3">
                <?php while ( have_posts() ) : the_post(); ?>
                <article class="apex-card">
                    <div class="apex-card-body">
                        <span class="apex-tag"><?php echo esc_html( get_post_type_object( get_post_type() )->labels->singular_name ?? 'İçerik' ); ?></span>
                        <h2 class="apex-h3"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <p class="apex-muted" style="margin:0;"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
                        <div class="apex-card-foot">
                            <a href="<?php the_permalink(); ?>" class="apex-btn apex-btn-outline apex-btn-sm">GÖRÜNTÜLE →</a>
                        </div>
                    </div>
                </article>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <div class="apex-panel" style="text-align:center;padding:48px 24px;">
                <h2 class="apex-h2">SONUÇ BULUNAMADI</h2>
                <p class="apex-lead" style="margin:12px auto 24px;">Aradığınız ifadeyle eşleşen bir kayıt bulamadık. Sağ alttaki APEX AI Spor Koçu’na doğrudan sorabilirsiniz!</p>
                <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="apex-btn apex-btn-primary">ANA SAYFAYA DÖN</a>
                    <button type="button" class="apex-btn apex-btn-volt" data-open-ai>🤖 AI SPOR KOÇU’NA SOR</button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php get_footer(); ?>
