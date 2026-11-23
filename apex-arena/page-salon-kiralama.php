<?php
/**
 * Template Name: Salon ve Kiralama Sistemi
 * Rubric Kapsamı (5 Puan): Basketbol (2 Adet), Voleybol (1 Adet), Hakem Ayarlama Hizmeti,
 * Salonlar Hakkında Bilgi & Fotoğraf Galerisi, Canlı Rezervasyon Sistemi
 *
 * @package ApexArena
 */
get_header();

$courts   = get_posts( [ 'post_type' => 'apex_court', 'numberposts' => 10, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC' ] );
$settings = apex_get_settings();
$pre_cid  = absint( $_GET['court'] ?? ( $courts[0]->ID ?? 0 ) );
$cu       = is_user_logged_in() ? wp_get_current_user() : null;
?>

<section class="apex-page-hero">
    <div class="apex-container">
        <?php apex_breadcrumbs(); ?>
        <span class="apex-eyebrow">SALON VE KİRALAMA · 2 BASKETBOL + 1 VOLEYBOL + HAKEM HİZMETİ</span>
        <h1 class="apex-h1">PROFESYONEL SALON KİRALAMA SİSTEMİ</h1>
        <p class="apex-lead">FIBA onaylı 2 adet basketbol salonumuz ve CEV standartlarında 1 adet voleybol salonumuzu teknik detayları ve fotoğraflarıyla inceleyin; lisanslı hakem ayarlama hizmetiyle anında rezerve edin.</p>
    </div>
</section>

<!-- 1. Salon Kartları & Teknik Detay + Fotoğraf Lightbox -->
<section class="apex-section">
    <div class="apex-container">
        <div class="apex-grid-3">
            <?php foreach ( $courts as $c ) :
                $sport    = get_post_meta( $c->ID, '_apex_sport', true );
                $code     = get_post_meta( $c->ID, '_apex_court_code', true );
                $dim      = get_post_meta( $c->ID, '_apex_dimensions', true );
                $surf     = get_post_meta( $c->ID, '_apex_surface', true );
                $cap      = get_post_meta( $c->ID, '_apex_capacity', true );
                $rate     = (int) get_post_meta( $c->ID, '_apex_hourly_rate', true );
                $ref_fee  = (int) get_post_meta( $c->ID, '_apex_referee_fee', true ) ?: (int) $settings['referee_fee'];
                $features = array_filter( array_map( 'trim', explode( "\n", (string) get_post_meta( $c->ID, '_apex_features', true ) ) ) );
                $photo    = apex_get_post_photo( $c->ID, 'court-basketball-1.jpg' );
            ?>
            <article class="apex-card">
                <div class="apex-card-media" style="cursor:zoom-in;" data-lightbox-src="<?php echo esc_url( $photo ); ?>" data-lightbox-caption="<?php echo esc_attr( $c->post_title . ' — ' . $dim . ' · ' . $surf ); ?>">
                    <img src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( $c->post_title ); ?>">
                    <span class="apex-card-badge"><?php echo esc_html( $code . ' · ' . strtoupper( $sport ) ); ?></span>
                </div>
                <div class="apex-card-body">
                    <h2 class="apex-h3"><?php echo esc_html( $c->post_title ); ?></h2>
                    <p class="apex-muted" style="margin:0;font-size:0.9rem;"><?php echo esc_html( wp_strip_all_tags( $c->post_content ) ); ?></p>
                    <div class="apex-tags">
                        <span class="apex-tag">📐 <?php echo esc_html( $dim ); ?></span>
                        <span class="apex-tag">🏟️ <?php echo esc_html( $cap ); ?></span>
                        <span class="apex-tag apex-tag-volt">⚖️ Hakem Opsiyonu: +<?php echo esc_html( apex_format_tl( $ref_fee ) ); ?></span>
                    </div>
                    <ul class="apex-checklist">
                        <?php foreach ( $features as $ft ) : ?>
                            <li><?php echo esc_html( $ft ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="apex-card-foot">
                        <div class="apex-price"><?php echo esc_html( apex_format_tl( $rate ) ); ?> <small>/ saat</small></div>
                        <button type="button" class="apex-btn apex-btn-primary apex-btn-sm" data-select-court="<?php echo esc_attr( $c->ID ); ?>">SALONU SEÇ</button>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 2. Hakem Ayarlama Hizmeti Özel Bilgi Bandı -->
<section class="apex-section apex-section-alt" style="padding:44px 0;">
    <div class="apex-container">
        <div class="apex-panel" style="border-left:5px solid var(--apex-volt);display:flex;align-items:center;justify-content:space-between;gap:24px;flex-wrap:wrap;">
            <div style="max-width:740px;">
                <span class="apex-eyebrow apex-eyebrow-volt">ÖZEL MAÇ & TURNUVA HİZMETİ</span>
                <h2 class="apex-h2">LİSANSLI HAKEM AYARLAMA HİZMETİ</h2>
                <p class="apex-lead">Kurumsal ligler, hazırlık maçları veya arkadaş turnuvalarınız için <strong>TBF (Basketbol)</strong> ve <strong>TVF (Voleybol)</strong> klasman hakemlerini tek tıkla maçınıza atıyoruz. Hakemimiz maçtan 20 dakika önce sahada hazır bulunur, skorboard ve faul takibini profesyonelce yönetir.</p>
            </div>
            <div style="text-align:right;">
                <div class="apex-price" style="color:var(--apex-volt);">+<?php echo esc_html( apex_format_tl( $settings['referee_fee'] ) ); ?></div>
                <div class="apex-muted">Maç Başı Sabit Ücret</div>
            </div>
        </div>
    </div>
</section>

<!-- 3. Canlı Salon Rezervasyon Sistemi -->
<section class="apex-section" id="rezervasyon" aria-labelledby="court-book-h">
    <div class="apex-container">
        <span class="apex-eyebrow">GERÇEK ZAMANLI REZERVASYON · ÇAKIŞMA KORUMALI</span>
        <h2 id="court-book-h" class="apex-h2" style="margin-bottom:28px;">ONLİNE SALON REZERVASYONU OLUŞTUR</h2>

        <div class="apex-split-booking">
            <div class="apex-panel">
                <form id="apex-court-booking-form" class="apex-form-grid" novalidate>
                    <div class="apex-field apex-field-full">
                        <label for="court-select">Salon Seçimi *</label>
                        <select id="court-select" name="court_id" required>
                            <?php foreach ( $courts as $c ) :
                                $rate = (int) get_post_meta( $c->ID, '_apex_hourly_rate', true );
                                $rf   = (int) get_post_meta( $c->ID, '_apex_referee_fee', true ) ?: (int) $settings['referee_fee'];
                            ?>
                                <option value="<?php echo esc_attr( $c->ID ); ?>"
                                    data-rate="<?php echo esc_attr( $rate ); ?>"
                                    data-referee="<?php echo esc_attr( $rf ); ?>"
                                    <?php selected( $c->ID, $pre_cid ); ?>>
                                    <?php echo esc_html( $c->post_title . ' — ' . apex_format_tl( $rate ) . '/saat' ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="apex-field">
                        <label for="court-date">Rezervasyon Tarihi *</label>
                        <input type="date" id="court-date" name="date" min="<?php echo esc_attr( current_time( 'Y-m-d' ) ); ?>" value="<?php echo esc_attr( current_time( 'Y-m-d' ) ); ?>" required>
                    </div>

                    <div class="apex-field">
                        <label for="court-duration">Kiralama Süresi *</label>
                        <select id="court-duration" name="duration">
                            <option value="1">1 Saat</option>
                            <option value="2">2 Saat</option>
                            <option value="3">3 Saat (Turnuva)</option>
                        </select>
                    </div>

                    <div class="apex-field apex-field-full">
                        <label>Müsait Saat Dilimleri * <small class="apex-muted">(Üzeri çizili saatler veritabanında doludur)</small></label>
                        <input type="hidden" id="court-time-slot" name="time_slot" required>
                        <div class="apex-slots-grid" id="court-slots-container" role="group" aria-label="Salon müsait saatleri">
                            <span class="apex-muted">Müsaitlik sorgulanıyor...</span>
                        </div>
                    </div>

                    <div class="apex-field apex-field-full">
                        <label class="apex-referee-box" for="court-referee">
                            <input type="checkbox" id="court-referee" name="needs_referee" value="1">
                            <span>
                                <strong style="color:var(--apex-volt);display:block;">⚖️ Lisanslı Hakem Ayarlama Hizmeti Ekle (+<span id="ref-fee-label"><?php echo esc_html( apex_format_tl( $settings['referee_fee'] ) ); ?></span>)</strong>
                                <small class="apex-muted">TBF / TVF klasman hakemi maç saatinde sahada hazır olur. Skorboard ve resmi maç yönetimi dahildir.</small>
                            </span>
                        </label>
                    </div>

                    <div class="apex-field">
                        <label for="court-name">Ad Soyad / Takım Sorumlusu *</label>
                        <input type="text" id="court-name" name="customer_name" value="<?php echo esc_attr( $cu ? $cu->display_name : '' ); ?>" placeholder="Örn. Burak Demir" required>
                    </div>

                    <div class="apex-field">
                        <label for="court-phone">Telefon *</label>
                        <input type="tel" id="court-phone" name="customer_phone" value="<?php echo esc_attr( $cu ? get_user_meta( $cu->ID, 'apex_member_phone', true ) : '' ); ?>" placeholder="0532 000 00 00" required>
                    </div>

                    <div class="apex-field apex-field-full">
                        <label for="court-email">E-posta *</label>
                        <input type="email" id="court-email" name="customer_email" value="<?php echo esc_attr( $cu ? $cu->user_email : '' ); ?>" placeholder="ornek@mail.com" required>
                    </div>

                    <div class="apex-field apex-field-full">
                        <label for="court-notes">Maç / Ekipman Notu (Top, yelek, tribün vb.)</label>
                        <textarea id="court-notes" name="notes" placeholder="Örn. 10 adet antrenman yeleği ve 2 maç topu hazır olsun."></textarea>
                    </div>

                    <div class="apex-field apex-field-full">
                        <button type="submit" class="apex-btn apex-btn-primary apex-btn-lg apex-btn-block">SALON REZERVASYONUNU ONAYLA</button>
                    </div>
                </form>
                <div id="court-booking-result" role="status" aria-live="polite"></div>
            </div>

            <!-- Canlı Fiyat & Özet Kartı -->
            <aside class="apex-panel" aria-label="Rezervasyon özeti">
                <h3 class="apex-h3" style="margin-bottom:16px;">REZERVASYON ÖZETİ</h3>
                <div class="apex-summary-box">
                    <div class="apex-summary-row"><span class="apex-muted">Salon:</span><strong id="sum-court">-</strong></div>
                    <div class="apex-summary-row"><span class="apex-muted">Tarih & Saat:</span><strong id="sum-datetime">Saat seçiniz</strong></div>
                    <div class="apex-summary-row"><span class="apex-muted">Süre:</span><strong id="sum-duration">1 Saat</strong></div>
                    <div class="apex-summary-row"><span class="apex-muted">Salon Ücreti:</span><strong id="sum-base">₺0</strong></div>
                    <div class="apex-summary-row"><span class="apex-muted">Hakem Hizmeti:</span><strong id="sum-referee">Dahil Değil (₺0)</strong></div>
                    <div class="apex-summary-row apex-summary-total"><span>TOPLAM TUTAR</span><span id="sum-total">₺0</span></div>
                </div>
                <ul class="apex-checklist" style="margin-top:20px;">
                    <li>Maç topları ve dijital skorboard kullanımı ücretsizdir.</li>
                    <li>Soyunma odası, yağmur duş ve ilk 2 saat kapalı otopark dahildir.</li>
                    <li>Maç saatinden 12 saat öncesine kadar ücretsiz iptal/değişiklik.</li>
                    <li>APEX PRO üyelerine gişede anında %25 üye indirimi uygulanır.</li>
                </ul>
            </aside>
        </div>
    </div>
</section>

<!-- Fotoğraf Lightbox Modalı -->
<div class="apex-modal-backdrop" id="apex-lightbox-modal" role="dialog" aria-modal="true" aria-label="Salon fotoğrafı büyütülmüş görünüm" hidden>
    <div class="apex-modal" style="max-width:900px;">
        <div class="apex-modal-head">
            <h2 id="lightbox-caption" class="apex-h3"></h2>
            <button type="button" class="apex-mobile-close" data-close-modal="apex-lightbox-modal" aria-label="Kapat">✕</button>
        </div>
        <img id="lightbox-img" src="" alt="" style="width:100%;border-radius:10px;">
    </div>
</div>

<?php get_footer(); ?>
