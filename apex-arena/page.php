<?php
/**
 * Standart Sayfa Şablonu (Çerez Politikası, KVKK, Gizlilik, Sözleşme, Erişilebilirlik Beyanı vb.)
 *
 * @package ApexArena
 */
get_header();
while ( have_posts() ) : the_post();
?>
<section class="apex-page-hero">
    <div class="apex-container">
        <?php apex_breadcrumbs(); ?>
        <span class="apex-eyebrow">KURUMSAL & YASAL BİLGİLENDİRME</span>
        <h1 class="apex-h1"><?php the_title(); ?></h1>
        <p class="apex-lead">Son güncelleme: <?php echo esc_html( get_the_modified_date( 'd.m.Y' ) ); ?> · APEX ARENA Spor ve Performans Kompleksi</p>
    </div>
</section>

<section class="apex-section">
    <div class="apex-container">
        <article class="apex-prose">
            <?php the_content(); ?>

            <?php if ( is_page( 'cerez-politikasi' ) ) : ?>
                <div style="margin-top:28px;padding-top:20px;border-top:1px solid var(--apex-border);">
                    <button type="button" class="apex-btn apex-btn-primary" data-open-cookie-modal>🍪 ÇEREZ TERCİHLERİMİ ŞİMDİ GÜNCELLE</button>
                </div>
            <?php elseif ( is_page( 'erisilebilirlik-beyani' ) ) : ?>
                <div style="margin-top:28px;padding-top:20px;border-top:1px solid var(--apex-border);">
                    <button type="button" class="apex-btn apex-btn-volt" data-open-a11y>♿ ERİŞİLEBİLİRLİK MERKEZİNİ AÇ</button>
                </div>
            <?php endif; ?>
        </article>
    </div>
</section>
<?php endwhile; get_footer(); ?>
