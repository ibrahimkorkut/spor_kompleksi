<?php
/**
 * Tekil Antrenör Profili — Özgeçmiş, Sertifikalar & Canlı Online Takvim ile Doğrudan Randevu
 *
 * @package ApexArena
 */
get_header();
while ( have_posts() ) : the_post();
    $tid    = get_the_ID();
    $role   = get_post_meta( $tid, '_apex_role', true );
    $spec   = get_post_meta( $tid, '_apex_specialty', true );
    $exp    = get_post_meta( $tid, '_apex_experience', true );
    $edu    = get_post_meta( $tid, '_apex_education', true );
    $fee    = (int) get_post_meta( $tid, '_apex_session_fee', true );
    $rating = get_post_meta( $tid, '_apex_rating', true ) ?: '4.9';
    $photo  = apex_get_post_photo( $tid, 'trainer-kerem.jpg' );
    $certs  = json_decode( (string) get_post_meta( $tid, '_apex_certificates', true ), true ) ?: [];
    $cu     = is_user_logged_in() ? wp_get_current_user() : null;
?>
<section class="apex-page-hero">
    <div class="apex-container">
        <?php apex_breadcrumbs(); ?>
        <span class="apex-eyebrow"><?php echo esc_html( $role ); ?></span>
        <h1 class="apex-h1"><?php the_title(); ?></h1>
        <p class="apex-lead"><?php echo esc_html( $spec ); ?> · <?php echo esc_html( $exp ); ?> Yıl Deneyim · ★ <?php echo esc_html( $rating ); ?>/5.0</p>
    </div>
</section>

<section class="apex-section">
    <div class="apex-container">
        <div class="apex-split-profile">
            <div style="display:grid;gap:20px;">
                <div class="apex-card-media apex-card-media-portrait" style="border-radius:16px;border:1px solid var(--apex-border);">
                    <img src="<?php echo esc_url( $photo ); ?>" alt="<?php the_title_attribute(); ?>">
                </div>
                <div class="apex-panel" style="padding:20px;display:grid;gap:10px;">
                    <div><span class="apex-muted">Eğitim:</span><br><strong><?php echo esc_html( $edu ); ?></strong></div>
                    <div><span class="apex-muted">Tek Seans PT Ücreti:</span><br><strong style="font-size:1.4rem;color:var(--apex-volt);"><?php echo esc_html( apex_format_tl( $fee ) ); ?></strong></div>
                </div>
            </div>

            <div style="display:grid;gap:28px;">
                <div class="apex-panel">
                    <h2 class="apex-h3" style="margin-bottom:12px;">ÖZGEÇMİŞ & UZMANLIK</h2>
                    <div class="apex-muted"><?php the_content(); ?></div>
                </div>

                <div class="apex-panel">
                    <h2 class="apex-h3" style="margin-bottom:14px;">SERTİFİKA VE BELGELER</h2>
                    <div class="apex-grid-2">
                        <?php foreach ( $certs as $c ) : ?>
                        <div style="background:var(--apex-bg);border:1px solid var(--apex-border-strong);border-radius:10px;padding:16px;">
                            <div style="color:var(--apex-volt);font-weight:800;font-size:0.8rem;">🏅 <?php echo esc_html( $c['year'] ?? '' ); ?> · <?php echo esc_html( $c['code'] ?? '' ); ?></div>
                            <strong style="display:block;margin:6px 0 4px;"><?php echo esc_html( $c['name'] ?? '' ); ?></strong>
                            <small class="apex-muted"><?php echo esc_html( $c['issuer'] ?? '' ); ?></small>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Canlı Online Takvim & Direkt Randevu -->
                <div class="apex-panel">
                    <span class="apex-eyebrow apex-eyebrow-volt">CANLI ONLİNE TAKVİM</span>
                    <h2 class="apex-h3" style="margin-bottom:18px;"><?php the_title(); ?> İLE ANINDA RANDEVU AL</h2>
                    <form id="apex-pt-booking-form" class="apex-form-grid" novalidate>
                        <input type="hidden" id="pt-trainer-select" name="trainer_id" value="<?php echo esc_attr( $tid ); ?>">
                        <input type="hidden" id="pt-package-select" name="package_id" value="0">
                        <div class="apex-field">
                            <label for="pt-date-input">Tarih Seçin *</label>
                            <input type="date" id="pt-date-input" name="date" min="<?php echo esc_attr( current_time( 'Y-m-d' ) ); ?>" value="<?php echo esc_attr( current_time( 'Y-m-d' ) ); ?>" required>
                        </div>
                        <div class="apex-field">
                            <label for="pt-type-select">Antrenman Türü</label>
                            <select id="pt-type-select" name="training_type">
                                <option value="bireysel">Bireysel Antrenman</option>
                                <option value="grupsal">Grupsal Antrenman</option>
                            </select>
                        </div>
                        <div class="apex-field apex-field-full">
                            <label>Canlı Müsait Saatler *</label>
                            <input type="hidden" id="pt-time-slot" name="time_slot" required>
                            <div class="apex-slots-grid" id="pt-slots-container"></div>
                        </div>
                        <div class="apex-field"><label for="pt-name">Ad Soyad *</label><input type="text" id="pt-name" name="customer_name" value="<?php echo esc_attr( $cu ? $cu->display_name : '' ); ?>" required></div>
                        <div class="apex-field"><label for="pt-phone">Telefon *</label><input type="tel" id="pt-phone" name="customer_phone" required></div>
                        <div class="apex-field apex-field-full"><label for="pt-email">E-posta *</label><input type="email" id="pt-email" name="customer_email" value="<?php echo esc_attr( $cu ? $cu->user_email : '' ); ?>" required></div>
                        <div class="apex-field apex-field-full">
                            <button type="submit" class="apex-btn apex-btn-primary apex-btn-lg apex-btn-block">RANDEVUYU ONAYLA</button>
                        </div>
                    </form>
                    <div id="pt-booking-result" role="status" aria-live="polite"></div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endwhile; get_footer(); ?>
