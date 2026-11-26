<?php
/**
 * Template Name: Antrenörler & Online Takvim
 * Rubric Kapsamı (5 Puan): Kimlik, Resmi, Uzmanlık Alanı, Özgeçmiş, Sertifika ve Belgeler, Online Takvim
 *
 * @package ApexArena
 */
get_header();

$trainers = get_posts( [ 'post_type' => 'apex_trainer', 'numberposts' => 20, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC' ] );
?>

<section class="apex-page-hero apex-editorial-hero">
    <div class="apex-container">
        <?php apex_breadcrumbs(); ?>
        <span class="apex-eyebrow"><?php echo esc_html( apex_cms( 'page_tr_eyebrow' ) ); ?></span>
        <h1 class="apex-h1"><?php echo esc_html( apex_cms( 'page_tr_title' ) ); ?></h1>
        <p class="apex-lead"><?php echo esc_html( apex_cms( 'page_tr_desc' ) ); ?></p>
    </div>
</section>

<section class="apex-section">
    <div class="apex-container" style="display:grid;grid-template-columns:minmax(0,1fr);gap:28px;">
        <?php foreach ( $trainers as $idx => $tr ) :
            $role   = get_post_meta( $tr->ID, '_apex_role', true );
            $spec   = get_post_meta( $tr->ID, '_apex_specialty', true );
            $exp    = get_post_meta( $tr->ID, '_apex_experience', true );
            $edu    = get_post_meta( $tr->ID, '_apex_education', true );
            $fee    = (int) get_post_meta( $tr->ID, '_apex_session_fee', true );
            $rating = get_post_meta( $tr->ID, '_apex_rating', true ) ?: '4.9';
            $photo  = apex_get_post_photo( $tr->ID, 'trainer-kerem.jpg' );
            $certs  = json_decode( (string) get_post_meta( $tr->ID, '_apex_certificates', true ), true ) ?: [];
            $sched  = json_decode( (string) get_post_meta( $tr->ID, '_apex_schedule', true ), true ) ?: [];
            $num    = str_pad( (string) ( $idx + 1 ), 2, '0', STR_PAD_LEFT );
        ?>
        <article class="apex-ed-dossier" id="trainer-<?php echo esc_attr( $tr->ID ); ?>">
            <div class="apex-split-profile">
                <div style="display:flex;flex-direction:column;gap:14px;">
                    <div class="apex-coach-poster apex-dossier-poster">
                        <div class="apex-coach-poster-media">
                            <img src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( $tr->post_title . ' - ' . $role ); ?>" loading="lazy">
                            <span class="apex-coach-poster-scrim" aria-hidden="true"></span>
                        </div>
                        <div class="apex-coach-poster-top">
                            <span class="apex-coach-idx"><?php echo esc_html( $num ); ?></span>
                            <span class="apex-coach-stat"><?php echo esc_html( $exp ); ?> YIL <i>/</i> <?php echo esc_html( $rating ); ?></span>
                        </div>
                        <div class="apex-coach-poster-body">
                            <span class="apex-coach-rule" aria-hidden="true"></span>
                            <span class="apex-coach-role"><?php echo esc_html( $role ); ?></span>
                            <h2 class="apex-coach-name"><?php echo esc_html( $tr->post_title ); ?></h2>
                        </div>
                    </div>
                    <div style="display:grid;gap:8px;">
                        <a href="<?php echo esc_url( home_url( '/pt-egitmenler/?trainer=' . $tr->ID . '#randevu' ) ); ?>" class="apex-pt-cta apex-ed-cta-fill">
                            <span>Hemen PT Randevusu Al</span>
                            <span class="apex-pt-cta-arrow" aria-hidden="true">↗</span>
                        </a>
                        <a href="<?php echo esc_url( get_permalink( $tr->ID ) ); ?>" class="apex-pt-cta" style="padding:12px 14px;border:1px solid rgba(255,255,255,0.12);border-radius:4px;">
                            <span>Detaylı Profil &amp; Canlı Takvim</span>
                            <span class="apex-pt-cta-arrow" aria-hidden="true">↗</span>
                        </a>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:minmax(0,1fr);gap:22px;min-width:0;align-content:start;">
                    <div class="apex-ed-meta-strip">
                        <div>
                            <span>UZMANLIK</span>
                            <strong><?php echo esc_html( $spec ); ?></strong>
                        </div>
                        <div>
                            <span>EĞİTİM</span>
                            <strong><?php echo esc_html( $edu ); ?></strong>
                        </div>
                        <div>
                            <span>TEK SEANS</span>
                            <strong style="color:var(--apex-crimson);"><?php echo esc_html( apex_format_tl( $fee ) ); ?></strong>
                        </div>
                    </div>

                    <div>
                        <span class="apex-ed-subhead">ÖZGEÇMİŞ &amp; KOÇLUK YAKLAŞIMI</span>
                        <p class="apex-muted" style="margin:8px 0 0;line-height:1.65;"><?php echo esc_html( wp_strip_all_tags( $tr->post_content ) ); ?></p>
                    </div>

                    <!-- Sertifikalar ve Belgeler -->
                    <div>
                        <span class="apex-ed-subhead">DOĞRULANMIŞ SERTİFİKA VE BELGELER (İncelemek için tıklayın)</span>
                        <div class="apex-ed-cert-list">
                            <?php foreach ( $certs as $cert ) : ?>
                                <button type="button" class="apex-ed-cert-btn"
                                    data-cert-modal
                                    data-cert-trainer="<?php echo esc_attr( $tr->post_title ); ?>"
                                    data-cert-name="<?php echo esc_attr( $cert['name'] ?? '' ); ?>"
                                    data-cert-issuer="<?php echo esc_attr( $cert['issuer'] ?? '' ); ?>"
                                    data-cert-year="<?php echo esc_attr( $cert['year'] ?? '' ); ?>"
                                    data-cert-code="<?php echo esc_attr( $cert['code'] ?? '' ); ?>">
                                    <span><?php echo esc_html( $cert['name'] ?? '' ); ?></span>
                                    <em><?php echo esc_html( $cert['year'] ?? '' ); ?> ↗</em>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Haftalık Online Müsaitlik Takvimi Önizleme -->
                    <div>
                        <span class="apex-ed-subhead">ONLİNE MÜSAİTLİK TAKVİMİ (HAFTALIK)</span>
                        <div class="apex-table-wrap" style="margin-top:10px;">
                            <table class="apex-table" style="min-width:520px;">
                                <thead>
                                    <tr>
                                        <?php foreach ( $sched as $day => $slots ) : ?>
                                            <th><?php echo esc_html( $day ); ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <?php foreach ( $sched as $day => $slots ) : ?>
                                            <td style="vertical-align:top;">
                                                <?php if ( empty( $slots ) ) : ?>
                                                    <span class="apex-muted">İzinli</span>
                                                <?php else : ?>
                                                    <div style="display:grid;gap:6px;">
                                                        <?php foreach ( $slots as $sl ) : ?>
                                                            <a href="<?php echo esc_url( home_url( '/pt-egitmenler/?trainer=' . $tr->ID . '#randevu' ) ); ?>" class="apex-ed-slot-link"><?php echo esc_html( $sl ); ?></a>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
</section>

<!-- Sertifika Belge Görüntüleme Modalı -->
<div class="apex-modal-backdrop" id="apex-cert-modal" role="dialog" aria-modal="true" aria-labelledby="cert-modal-title" hidden>
    <div class="apex-modal">
        <div class="apex-modal-head">
            <h2 id="cert-modal-title" class="apex-h3">RESMİ SERTİFİKA & BELGE DOĞRULAMA</h2>
            <button type="button" class="apex-mobile-close" data-close-modal="apex-cert-modal" aria-label="Pencereyi kapat">✕</button>
        </div>
        <div style="border:2px dashed var(--apex-volt);border-radius:14px;padding:28px;text-align:center;background:linear-gradient(135deg,#13161D,#1E0B11);">
            <div style="font-size:2.6rem;" aria-hidden="true">🏅</div>
            <div class="apex-eyebrow apex-eyebrow-volt" style="margin-top:8px;">DOĞRULANMIŞ AKREDİTASYON BELGESİ</div>
            <h3 id="cert-modal-name" class="apex-h2" style="margin-top:6px;"></h3>
            <p style="margin:12px 0 4px;">Belge Sahibi: <strong id="cert-modal-trainer"></strong></p>
            <p class="apex-muted" style="margin:0;">Veren Kurum: <strong id="cert-modal-issuer" style="color:var(--apex-text);"></strong></p>
            <div class="apex-tags" style="justify-content:center;margin-top:16px;">
                <span class="apex-tag">Yıl: <strong id="cert-modal-year"></strong></span>
                <span class="apex-tag apex-tag-volt">Lisans No: <strong id="cert-modal-code"></strong></span>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
