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

<section class="apex-page-hero">
    <div class="apex-container">
        <?php apex_breadcrumbs(); ?>
        <span class="apex-eyebrow">UZMAN ANTRENÖR KADROSU · SERTİFİKALAR & CANLI TAKVİM</span>
        <h1 class="apex-h1">ANTRENÖRLER VE ONLİNE TAKVİM</h1>
        <p class="apex-lead">Her antrenörümüzün kimliğini, uzmanlık alanını, özgeçmişini, uluslararası sertifikalarını inceleyin ve canlı müsaitlik takviminden doğrudan randevu alın.</p>
    </div>
</section>

<section class="apex-section">
    <div class="apex-container" style="display:grid;gap:36px;">
        <?php foreach ( $trainers as $tr ) :
            $role   = get_post_meta( $tr->ID, '_apex_role', true );
            $spec   = get_post_meta( $tr->ID, '_apex_specialty', true );
            $exp    = get_post_meta( $tr->ID, '_apex_experience', true );
            $edu    = get_post_meta( $tr->ID, '_apex_education', true );
            $fee    = (int) get_post_meta( $tr->ID, '_apex_session_fee', true );
            $rating = get_post_meta( $tr->ID, '_apex_rating', true ) ?: '4.9';
            $photo  = apex_get_post_photo( $tr->ID, 'trainer-kerem.jpg' );
            $certs  = json_decode( (string) get_post_meta( $tr->ID, '_apex_certificates', true ), true ) ?: [];
            $sched  = json_decode( (string) get_post_meta( $tr->ID, '_apex_schedule', true ), true ) ?: [];
        ?>
        <article class="apex-panel" id="trainer-<?php echo esc_attr( $tr->ID ); ?>">
            <div class="apex-split-profile">
                <div>
                    <div class="apex-card-media apex-card-media-portrait" style="border-radius:14px;overflow:hidden;border:1px solid var(--apex-border);">
                        <img src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( $tr->post_title . ' - ' . $role ); ?>" loading="lazy">
                        <span class="apex-card-badge">★ <?php echo esc_html( $rating ); ?> / 5.0 · <?php echo esc_html( $exp ); ?> YIL DENEYİM</span>
                    </div>
                    <div style="margin-top:16px;display:grid;gap:10px;">
                        <a href="<?php echo esc_url( get_permalink( $tr->ID ) ); ?>" class="apex-btn apex-btn-primary apex-btn-block">DETAYLI PROFİL & CANLI TAKVİM</a>
                        <a href="<?php echo esc_url( home_url( '/pt-egitmenler/?trainer=' . $tr->ID . '#randevu' ) ); ?>" class="apex-btn apex-btn-outline apex-btn-block">HEMEN PT RANDEVUSU AL</a>
                    </div>
                </div>

                <div style="display:grid;gap:18px;">
                    <div>
                        <span class="apex-eyebrow" style="margin:0;"><?php echo esc_html( $role ); ?></span>
                        <h2 class="apex-h2" style="margin-top:4px;"><?php echo esc_html( $tr->post_title ); ?></h2>
                        <div class="apex-tags" style="margin-top:10px;">
                            <span class="apex-tag apex-tag-volt">🎯 Uzmanlık: <?php echo esc_html( $spec ); ?></span>
                            <span class="apex-tag">🎓 <?php echo esc_html( $edu ); ?></span>
                            <span class="apex-tag">💳 Tek Seans: <?php echo esc_html( apex_format_tl( $fee ) ); ?></span>
                        </div>
                    </div>

                    <div>
                        <h3 class="apex-h4" style="color:var(--apex-volt);margin-bottom:6px;">ÖZGEÇMİŞ (CV)</h3>
                        <p class="apex-muted" style="margin:0;"><?php echo esc_html( wp_strip_all_tags( $tr->post_content ) ); ?></p>
                    </div>

                    <!-- Sertifikalar ve Belgeler -->
                    <div>
                        <h3 class="apex-h4" style="color:var(--apex-volt);margin-bottom:10px;">SERTİFİKA VE BELGELER (İncelemek için tıklayın)</h3>
                        <div style="display:flex;flex-wrap:wrap;gap:10px;">
                            <?php foreach ( $certs as $cert ) : ?>
                                <button type="button" class="apex-btn apex-btn-ghost apex-btn-sm"
                                    data-cert-modal
                                    data-cert-trainer="<?php echo esc_attr( $tr->post_title ); ?>"
                                    data-cert-name="<?php echo esc_attr( $cert['name'] ?? '' ); ?>"
                                    data-cert-issuer="<?php echo esc_attr( $cert['issuer'] ?? '' ); ?>"
                                    data-cert-year="<?php echo esc_attr( $cert['year'] ?? '' ); ?>"
                                    data-cert-code="<?php echo esc_attr( $cert['code'] ?? '' ); ?>">
                                    🏅 <?php echo esc_html( $cert['name'] ?? '' ); ?> (<?php echo esc_html( $cert['year'] ?? '' ); ?>)
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Haftalık Online Müsaitlik Takvimi Önizleme -->
                    <div>
                        <h3 class="apex-h4" style="color:var(--apex-volt);margin-bottom:10px;">ONLİNE MÜSAİTLİK TAKVİMİ (HAFTALIK)</h3>
                        <div class="apex-table-wrap">
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
                                                            <a href="<?php echo esc_url( home_url( '/pt-egitmenler/?trainer=' . $tr->ID . '#randevu' ) ); ?>" class="apex-tag apex-tag-volt" style="text-align:center;"><?php echo esc_html( $sl ); ?></a>
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
