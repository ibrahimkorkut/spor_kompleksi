<?php
/**
 * Template Name: İletişim & Ulaşım
 *
 * @package ApexArena
 */
get_header();
$s = apex_get_settings();

$wa_digits = preg_replace( '/\D+/', '', (string) ( $s['phone'] ?? '02165552739' ) );
if ( strpos( $wa_digits, '0' ) === 0 ) {
    $wa_digits = '90' . substr( $wa_digits, 1 );
} elseif ( strpos( $wa_digits, '90' ) !== 0 ) {
    $wa_digits = '90' . $wa_digits;
}
$wa_url   = 'https://wa.me/' . $wa_digits . '?text=' . rawurlencode( 'Merhaba APEX ARENA, bilgi almak istiyorum.' );
$map_q    = rawurlencode( 'Barbaros Mahallesi, Ataşehir, İstanbul' );
$map_src  = 'https://maps.google.com/maps?q=' . $map_q . '&t=m&z=15&ie=UTF8&iwloc=B&output=embed';
$map_link = 'https://www.google.com/maps/search/?api=1&query=' . $map_q;
?>

<section class="apex-page-hero">
    <div class="apex-container">
        <?php apex_breadcrumbs(); ?>
        <span class="apex-eyebrow">İLETİŞİM · ULAŞIM &amp; KURUMSAL DESTEK</span>
        <h1 class="apex-h1">BİZE ULAŞIN</h1>
        <p class="apex-lead">Ücretsiz tesis turu, kurumsal turnuva organizasyonu, PT danışmanlığı veya üyelik sorularınız için ekibimiz haftanın 7 günü yanınızda.</p>
    </div>
</section>

<section class="apex-section">
    <div class="apex-container">
        <div class="apex-contact-layout">
            <!-- SOL SÜTUN: Bize Yazın Kartı (1. Fotoğraf Düzeni × APEX Koyu/Kırmızı Tema) -->
            <div class="apex-contact-form-card">
                <div class="apex-contact-head">
                    <div class="apex-contact-title-row">
                        <span class="apex-contact-title-icon" aria-hidden="true">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 4-8 5-8-5V6l8 5 8-5v2Z"/></svg>
                        </span>
                        <h2 class="apex-contact-heading">Bize Yazın</h2>
                    </div>
                    <p class="apex-contact-sub">Aşağıdaki formu doldurarak bize mesaj gönderebilirsiniz.</p>
                </div>

                <form id="apex-contact-form" class="apex-contact-grid" novalidate>
                    <div class="apex-contact-field">
                        <label for="c-name">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-4.33 0-8 2.17-8 5v1h16v-1c0-2.83-3.67-5-8-5Z"/></svg>
                            <span>Ad Soyad *</span>
                        </label>
                        <input type="text" id="c-name" name="full_name" placeholder="Adınız ve Soyadınız" required>
                    </div>

                    <div class="apex-contact-field">
                        <label for="c-email">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 4-8 5-8-5V6l8 5 8-5v2Z"/></svg>
                            <span>E-posta *</span>
                        </label>
                        <input type="email" id="c-email" name="email" placeholder="ornek@email.com" required>
                    </div>

                    <div class="apex-contact-field">
                        <label for="c-phone">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.62 10.79a15.05 15.05 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.01-.24c1.12.37 2.33.57 3.58.57a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C10.61 21 3 13.39 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.46.57 3.58a1 1 0 0 1-.25 1.01l-2.2 2.2Z"/></svg>
                            <span>Telefon *</span>
                        </label>
                        <input type="tel" id="c-phone" name="phone" placeholder="+90 (___) ___ __ __" required>
                    </div>

                    <div class="apex-contact-field">
                        <label for="c-subject">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21.41 11.58 12.41 2.58A2 2 0 0 0 11 2H4a2 2 0 0 0-2 2v7a2 2 0 0 0 .59 1.42l9 9a2 2 0 0 0 2.82 0l7-7a2 2 0 0 0 0-2.84ZM6.5 8A1.5 1.5 0 1 1 8 6.5 1.5 1.5 0 0 1 6.5 8Z"/></svg>
                            <span>Konu</span>
                        </label>
                        <input type="text" id="c-subject" name="subject" list="apex-contact-subjects" placeholder="Mesaj konusu">
                        <datalist id="apex-contact-subjects">
                            <option value="Üyelik & Ücretsiz Tesis Turu"></option>
                            <option value="Salon Kiralama & Hakem Hizmeti"></option>
                            <option value="PT & Grup Dersleri"></option>
                            <option value="Kurumsal Turnuva / Sponsorluk"></option>
                            <option value="Öneri & Şikayet"></option>
                        </datalist>
                    </div>

                    <div class="apex-contact-field apex-contact-field-full">
                        <label for="c-message">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20 2H4a2 2 0 0 0-2 2v18l4-4h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2Z"/></svg>
                            <span>Mesajınız *</span>
                        </label>
                        <textarea id="c-message" name="message" rows="5" placeholder="Mesajınızı buraya yazınız..." required></textarea>
                    </div>

                    <div class="apex-contact-field apex-contact-field-full">
                        <label class="apex-contact-kvkk">
                            <input type="checkbox" name="kvkk_consent" value="1" required>
                            <span><a href="<?php echo esc_url( home_url( '/kvkk-aydinlatma-metni/' ) ); ?>">KVKK Aydınlatma Metni</a> kapsamında iletişim verilerimin işlenmesini onaylıyorum. *</span>
                        </label>
                    </div>

                    <div class="apex-contact-field apex-contact-field-full">
                        <button type="submit" class="apex-contact-submit">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M2.01 21 23 12 2.01 3 2 10l15 2-15 2 .01 7Z"/></svg>
                            <span>Mesaj Gönder</span>
                        </button>
                    </div>
                </form>

                <div id="contact-result" role="status" aria-live="polite" style="margin-top:14px;"></div>
            </div>

            <!-- SAĞ SÜTUN: İletişim Bilgileri (5 Kartlı Liste) -->
            <div class="apex-contact-info-col">
                <div class="apex-contact-head">
                    <div class="apex-contact-title-row">
                        <span class="apex-contact-title-icon" aria-hidden="true">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm1 15h-2v-6h2Zm0-8h-2V7h2Z"/></svg>
                        </span>
                        <h2 class="apex-contact-heading">İletişim Bilgileri</h2>
                    </div>
                    <p class="apex-contact-sub">Bize aşağıdaki kanallardan da ulaşabilirsiniz.</p>
                </div>

                <div class="apex-contact-info-list">
                    <div class="apex-contact-info-item">
                        <span class="apex-contact-icon-badge" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 14.5 9 2.5 2.5 0 0 1 12 11.5Z"/></svg>
                        </span>
                        <div class="apex-contact-info-body">
                            <strong>Adresimiz</strong>
                            <span><?php echo esc_html( $s['address'] ); ?></span>
                        </div>
                    </div>

                    <a class="apex-contact-info-item apex-contact-info-link" href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $s['phone'] ) ); ?>">
                        <span class="apex-contact-icon-badge" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79a15.05 15.05 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.01-.24c1.12.37 2.33.57 3.58.57a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C10.61 21 3 13.39 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.46.57 3.58a1 1 0 0 1-.25 1.01l-2.2 2.2Z"/></svg>
                        </span>
                        <div class="apex-contact-info-body">
                            <strong>Telefon</strong>
                            <span><?php echo esc_html( $s['phone'] ); ?></span>
                        </div>
                    </a>

                    <a class="apex-contact-info-item apex-contact-info-link" href="mailto:<?php echo esc_attr( $s['email'] ); ?>">
                        <span class="apex-contact-icon-badge" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 4-8 5-8-5V6l8 5 8-5v2Z"/></svg>
                        </span>
                        <div class="apex-contact-info-body">
                            <strong>E-posta</strong>
                            <span><?php echo esc_html( $s['email'] ); ?></span>
                        </div>
                    </a>

                    <div class="apex-contact-info-item">
                        <span class="apex-contact-icon-badge" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm1 11h-5v-2h3V6h2Z"/></svg>
                        </span>
                        <div class="apex-contact-info-body">
                            <strong>Çalışma Saatleri</strong>
                            <span>Hafta İçi: <?php echo esc_html( $s['weekday_hours'] ); ?> · Hafta Sonu: <?php echo esc_html( $s['weekend_hours'] ); ?></span>
                        </div>
                    </div>

                    <a class="apex-contact-info-item apex-contact-info-link" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer">
                        <span class="apex-contact-icon-badge is-wa" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 32 32" fill="none">
                                <path d="M16.003 3.2C8.943 3.2 3.2 8.943 3.2 16.003c0 2.27.595 4.487 1.725 6.44L3.1 28.9l6.632-1.739a12.75 12.75 0 0 0 6.271 1.644h.005c7.06 0 12.802-5.743 12.802-12.802S23.063 3.2 16.003 3.2Z" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/>
                                <path d="M22.25 19.34c-.34-.17-2.01-.99-2.32-1.1-.31-.12-.54-.17-.77.17-.23.34-.88 1.1-1.08 1.33-.2.23-.4.26-.74.09-.34-.17-1.44-.53-2.74-1.69-1.01-.9-1.7-2.02-1.9-2.36-.2-.34-.02-.52.15-.69.15-.15.34-.4.51-.6.17-.2.23-.34.34-.57.11-.23.06-.43-.03-.6-.09-.17-.77-1.85-1.05-2.54-.28-.67-.56-.58-.77-.59-.2-.01-.43-.01-.66-.01-.23 0-.6.09-.91.43-.31.34-1.2 1.17-1.2 2.85 0 1.68 1.23 3.31 1.4 3.54.17.23 2.41 3.68 5.84 5.16.82.35 1.45.56 1.95.72.82.26 1.56.22 2.15.14.66-.1 2.01-.82 2.3-1.62.28-.8.28-1.48.2-1.62-.09-.14-.31-.23-.66-.4Z" fill="currentColor"/>
                            </svg>
                        </span>
                        <div class="apex-contact-info-body">
                            <strong>WhatsApp</strong>
                            <span>WhatsApp ile Yazın</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- GERÇEK ETKİLEŞİMLİ GOOGLE MAPS HARİTA ALANI -->
        <div class="apex-contact-map-card">
            <div class="apex-contact-map-head">
                <div>
                    <span class="apex-eyebrow" style="margin-bottom:4px;">KONUM &amp; ULAŞIM</span>
                    <h2 class="apex-h3" style="margin:0;">APEX ARENA · Ataşehir / İstanbul</h2>
                    <p class="apex-muted" style="margin:4px 0 0;font-size:0.88rem;">M8 Metro Küçükbakkalköy durağına 5 dk yürüme mesafesi · 180 araçlık kapalı otopark girişi Spor Bulvarı üzerinden.</p>
                </div>
                <a href="<?php echo esc_url( $map_link ); ?>" target="_blank" rel="noopener noreferrer" class="apex-btn apex-btn-outline apex-btn-sm">
                    Google Haritalar’da Aç ↗
                </a>
            </div>
            <div class="apex-contact-map-frame">
                <iframe
                    src="<?php echo esc_url( $map_src ); ?>"
                    title="APEX ARENA Ataşehir İstanbul Google Harita Konumu"
                    loading="lazy"
                    allowfullscreen
                    referrerpolicy="no-referrer-when-downgrade"
                ></iframe>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
