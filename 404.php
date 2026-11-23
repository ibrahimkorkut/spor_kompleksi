<?php
/**
 * 404 Sayfa Bulunamadı Şablonu
 *
 * @package ApexArena
 */
get_header();
?>
<section class="apex-section" style="padding:110px 0;text-align:center;">
    <div class="apex-container" style="max-width:700px;">
        <span class="apex-eyebrow">HATA 404</span>
        <h1 class="apex-h1" style="font-size:clamp(4rem,10vw,7rem);color:var(--apex-crimson);">404</h1>
        <h2 class="apex-h2" style="margin-top:8px;">ARADIĞINIZ SAHA BULUNAMADI</h2>
        <p class="apex-lead" style="margin:14px auto 28px;">Sayfa taşınmış veya kaldırılmış olabilir. Ana sayfaya dönebilir ya da AI Spor Koçu’ndan yardım alabilirsiniz.</p>
        <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="apex-btn apex-btn-primary apex-btn-lg">ANA SAYFAYA DÖN</a>
            <a href="<?php echo esc_url( home_url( '/salon-kiralama/' ) ); ?>" class="apex-btn apex-btn-outline apex-btn-lg">SALON KİRALA</a>
        </div>
    </div>
</section>
<?php get_footer(); ?>
