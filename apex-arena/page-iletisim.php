<?php
/**
 * Template Name: İletişim & Ulaşım
 *
 * @package ApexArena
 */
get_header();
$s = apex_get_settings();
?>

<section class="apex-page-hero">
    <div class="apex-container">
        <?php apex_breadcrumbs(); ?>
        <span class="apex-eyebrow">İLETİŞİM · ULAŞIM & KURUMSAL DESTEK</span>
        <h1 class="apex-h1">BİZE ULAŞIN</h1>
        <p class="apex-lead">Ücretsiz tesis turu, kurumsal turnuva organizasyonu, PT danışmanlığı veya üyelik sorularınız için ekibimiz haftanın 7 günü yanınızda.</p>
    </div>
</section>

<section class="apex-section">
    <div class="apex-container">
        <div class="apex-grid-2" style="align-items:start;">
            <div style="display:grid;gap:20px;">
                <div class="apex-panel" style="display:grid;gap:14px;">
                    <h2 class="apex-h3">TESİS BİLGİLERİ</h2>
                    <div><strong style="color:var(--apex-volt);">📍 Adres:</strong><br><span class="apex-muted"><?php echo esc_html( $s['address'] ); ?></span></div>
                    <div><strong style="color:var(--apex-volt);">📞 Telefon:</strong><br><a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $s['phone'] ) ); ?>"><?php echo esc_html( $s['phone'] ); ?></a></div>
                    <div><strong style="color:var(--apex-volt);">✉️ E-posta:</strong><br><a href="mailto:<?php echo esc_attr( $s['email'] ); ?>"><?php echo esc_html( $s['email'] ); ?></a></div>
                    <div><strong style="color:var(--apex-volt);">🕒 Çalışma Saatleri:</strong><br><span class="apex-muted">Hafta İçi: <?php echo esc_html( $s['weekday_hours'] ); ?><br>Hafta Sonu: <?php echo esc_html( $s['weekend_hours'] ); ?></span></div>
                </div>

                <!-- Etkileşimli Harita Alanı (Offline-safe SVG + Harita Linki) -->
                <div class="apex-panel" style="padding:20px;">
                    <h3 class="apex-h4" style="margin-bottom:12px;">KONUM & ULAŞIM</h3>
                    <div style="background:linear-gradient(135deg,#131824,#0B0E14);border:1px solid var(--apex-border-strong);border-radius:12px;padding:24px;text-align:center;">
                        <div style="font-size:2rem;" aria-hidden="true">📍</div>
                        <strong style="display:block;margin:6px 0;">APEX ARENA · Ataşehir / İstanbul</strong>
                        <p class="apex-muted" style="font-size:0.86rem;margin:0 0 14px;">M8 Metro Küçükbakkalköy durağına 5 dk yürüme mesafesi · 180 araçlık kapalı otopark girişi Spor Bulvarı üzerinden.</p>
                        <a href="https://maps.google.com/?q=Atasehir+Istanbul" target="_blank" rel="noopener noreferrer" class="apex-btn apex-btn-outline apex-btn-sm">GOOGLE HARİTALAR’DA AÇ ↗</a>
                    </div>
                </div>
            </div>

            <div class="apex-panel">
                <h2 class="apex-h3" style="margin-bottom:16px;">İLETİŞİM FORMU</h2>
                <form id="apex-contact-form" class="apex-form-grid" novalidate>
                    <div class="apex-field"><label for="c-name">Ad Soyad *</label><input type="text" id="c-name" name="full_name" required></div>
                    <div class="apex-field"><label for="c-phone">Telefon</label><input type="tel" id="c-phone" name="phone" placeholder="0532 000 00 00"></div>
                    <div class="apex-field apex-field-full"><label for="c-email">E-posta *</label><input type="email" id="c-email" name="email" required></div>
                    <div class="apex-field apex-field-full">
                        <label for="c-subject">Konu</label>
                        <select id="c-subject" name="subject">
                            <option value="Üyelik & Tesis Turu">Üyelik & Ücretsiz Tesis Turu</option>
                            <option value="Salon Kiralama & Hakem Hizmeti">Salon Kiralama & Hakem Hizmeti</option>
                            <option value="PT & Grup Dersleri">PT & Grup Dersleri</option>
                            <option value="Kurumsal Turnuva / Sponsorluk">Kurumsal Turnuva / Sponsorluk</option>
                            <option value="Öneri & Şikayet">Öneri & Şikayet</option>
                        </select>
                    </div>
                    <div class="apex-field apex-field-full"><label for="c-message">Mesajınız *</label><textarea id="c-message" name="message" required></textarea></div>
                    <div class="apex-field apex-field-full">
                        <label style="display:flex;gap:10px;align-items:flex-start;font-weight:500;font-size:0.86rem;">
                            <input type="checkbox" name="kvkk_consent" value="1" required style="width:20px;height:20px;min-height:20px;">
                            <span><a href="<?php echo esc_url( home_url( '/kvkk-aydinlatma-metni/' ) ); ?>" style="color:var(--apex-volt);text-decoration:underline;">KVKK Aydınlatma Metni</a> kapsamında iletişim verilerimin işlenmesini onaylıyorum. *</span>
                        </label>
                    </div>
                    <div class="apex-field apex-field-full">
                        <button type="submit" class="apex-btn apex-btn-primary apex-btn-lg apex-btn-block">MESAJI GÖNDER</button>
                    </div>
                </form>
                <div id="contact-result" role="status" aria-live="polite"></div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
