<?php
/**
 * Template Name: Tesis Olanakları
 * Rubric Kapsamı (5 Puan): Soyunma Odası & Duş, Kapalı Otopark, Apex Protein Kafe
 *
 * @package ApexArena
 */
get_header();

$settings = apex_get_settings();
$menu     = apex_get_cafe_menu();
$park_pct = round( ( ( $settings['parking_total'] - $settings['parking_free'] ) / max( 1, $settings['parking_total'] ) ) * 100 );
?>

<section class="apex-page-hero apex-editorial-hero">
    <div class="apex-container">
        <?php apex_breadcrumbs(); ?>
        <span class="apex-eyebrow"><?php echo esc_html( apex_cms( 'page_fac_eyebrow' ) ); ?></span>
        <h1 class="apex-h1"><?php echo esc_html( apex_cms( 'page_fac_title' ) ); ?></h1>
        <p class="apex-lead"><?php echo esc_html( apex_cms( 'page_fac_desc' ) ); ?></p>
    </div>
</section>

<!-- 1. Soyunma Odası, Duş ve Sauna -->
<section class="apex-section" id="soyunma" aria-labelledby="fac-locker-h">
    <div class="apex-container">
        <div class="apex-grid-2" style="align-items:center;gap:40px;">
            <div class="apex-ed-photo-frame">
                <img src="<?php echo esc_url( apex_img_url( 'facility-locker.jpg' ) ); ?>" alt="APEX ARENA Akıllı Dolaplı Soyunma Odası ve Yağmur Duş Alanı">
                <span class="apex-ed-photo-cap">01 <i>/</i> <?php echo esc_html( apex_cms( 'fac1_stat' ) ); ?></span>
            </div>
            <div>
                <span class="apex-pt-rule" aria-hidden="true"></span>
                <span class="apex-eyebrow"><?php echo esc_html( apex_cms( 'fac1_role' ) ); ?></span>
                <h2 id="fac-locker-h" class="apex-h2"><?php echo esc_html( apex_cms( 'fac1_title' ) ); ?></h2>
                <p class="apex-lead"><?php echo esc_html( apex_cms( 'fac1_desc' ) ); ?></p>
                <ul class="apex-pt-list" style="margin-top:20px;margin-bottom:0;">
                    <li>240 adet bileklik/QR kod ile açılan akıllı RFID dolap (şifre ve anahtar derdi yok)</li>
                    <li>24 adet yüksek basınçlı termostatik yağmur duş kabini + organik duş jeli &amp; şampuan</li>
                    <li>85°C kuru Fin saunası ve soğuk şok duşları (kas toparlanması için)</li>
                    <li>Profesyonel Dyson/iyonik saç kurutma istasyonları, buharlı ütü ve havlu servisi</li>
                    <li>Tekerlekli sandalye uyumlu geniş engelli soyunma &amp; duş kabini (WCAG fiziksel erişim)</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- 2. Kapalı Otopark & EV Şarj -->
<section class="apex-section apex-section-alt" id="otopark" aria-labelledby="fac-park-h">
    <div class="apex-container">
        <div class="apex-grid-2" style="align-items:center;gap:40px;">
            <div>
                <span class="apex-pt-rule" aria-hidden="true"></span>
                <span class="apex-eyebrow"><?php echo esc_html( apex_cms( 'fac2_role' ) ); ?></span>
                <h2 id="fac-park-h" class="apex-h2"><?php echo esc_html( apex_cms( 'fac2_title' ) ); ?></h2>
                <p class="apex-lead"><?php echo esc_html( apex_cms( 'fac2_desc' ) ); ?></p>

                <!-- Canlı Otopark Doluluk Göstergesi -->
                <div class="apex-panel" style="margin:20px 0;padding:18px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                        <strong>Canlı Kapalı Otopark Durumu</strong>
                        <span style="color:var(--apex-crimson);font-weight:800;"><?php echo esc_html( $settings['parking_free'] ); ?> Boş Yer / <?php echo esc_html( $settings['parking_total'] ); ?> Toplam</span>
                    </div>
                    <div style="height:8px;background:var(--apex-bg);border-radius:99px;overflow:hidden;border:1px solid var(--apex-border);">
                        <div style="width:<?php echo esc_attr( $park_pct ); ?>%;height:100%;background:linear-gradient(90deg,#a4ff2e,var(--apex-crimson));"></div>
                    </div>
                    <small class="apex-muted" style="display:block;margin-top:6px;">Anlık doluluk oranı: %<?php echo esc_html( $park_pct ); ?> · 6 adet 22 kW AC EV Şarj Ünitesinden 4’ü şu an müsait.</small>

                    <!-- Canlı 6x 22kW EV Şarj İstasyonu Monitörü (v2.0.0) -->
                    <div class="apex-ev-grid" aria-label="EV Şarj Üniteleri Canlı Durumu">
                        <?php
                        $ev_bays = [
                            [ 'EV-01', true,  '22 kW · Müsait' ],
                            [ 'EV-02', false, 'Şarjda (%78)' ],
                            [ 'EV-03', true,  '22 kW · Müsait' ],
                            [ 'EV-04', true,  '22 kW · Müsait' ],
                            [ 'EV-05', false, 'Şarjda (%42)' ],
                            [ 'EV-06', true,  '22 kW · Müsait' ],
                        ];
                        foreach ( $ev_bays as $ev ) : ?>
                        <div class="apex-ev-bay <?php echo $ev[1] ? 'is-free' : 'is-busy'; ?>">
                            <strong>⚡ <?php echo esc_html( $ev[0] ); ?></strong>
                            <span><?php echo esc_html( $ev[2] ); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Saatlik Tesis Yoğunluk Tahmin Grafiği (v2.0.0) -->
                <div class="apex-panel" style="margin:0 0 20px;padding:18px;" id="apex-crowd-widget">
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:12px;">
                        <div>
                            <strong style="display:block;">Saatlik Tesis Yoğunluk Tahmini</strong>
                            <small class="apex-muted" id="apex-crowd-note">En sakin saatler: 09:00–12:00 ve 14:00–16:00 arası</small>
                        </div>
                        <div class="apex-pt-filter-bar" role="tablist" aria-label="Gün seçimi" style="gap:14px;">
                            <button type="button" class="apex-pt-tab is-active" data-crowd-day="weekday" style="padding:4px 0 8px;font-size:0.72rem;">Hafta İçi</button>
                            <button type="button" class="apex-pt-tab" data-crowd-day="sat" style="padding:4px 0 8px;font-size:0.72rem;">Cumartesi</button>
                            <button type="button" class="apex-pt-tab" data-crowd-day="sun" style="padding:4px 0 8px;font-size:0.72rem;">Pazar</button>
                        </div>
                    </div>
                    <div class="apex-crowd-bars" id="apex-crowd-bars" role="list" aria-label="Saatlik yoğunluk barları"></div>
                </div>

                <ul class="apex-pt-list" style="margin-bottom:0;">
                    <li>APEX Classic üyelere ilk 2 saat, APEX PRO üyelere ilk 3 saat, ELITE üyelere sınırsız ücretsiz</li>
                    <li>Salon kiralayan tüm maç oyuncularına 2 saat ücretsiz otopark tanımlaması</li>
                    <li>Asansör çıkışına 10 metre mesafede 8 adet ayrılmış engelli park alanı</li>
                    <li>7/24 CCTV kamera güvenliği, motosiklet ve bisiklet park istasyonları</li>
                </ul>
            </div>
            <div class="apex-ed-photo-frame">
                <img src="<?php echo esc_url( apex_img_url( 'facility-parking.jpg' ) ); ?>" alt="APEX ARENA Kapalı Otopark ve Elektrikli Araç Şarj Alanı">
                <span class="apex-ed-photo-cap">02 <i>/</i> PLAKA TANIMA · 6 EV ŞARJ</span>
            </div>
        </div>
    </div>
</section>

<!-- 3. Apex Protein Kafe & Makro Hesaplı Menü (İsviçre Tipi Menü Tablosu) -->
<section class="apex-section" id="kafe" aria-labelledby="fac-cafe-h">
    <div class="apex-container">
        <div class="apex-grid-2" style="align-items:center;gap:40px;margin-bottom:48px;">
            <div class="apex-ed-photo-frame">
                <img src="<?php echo esc_url( apex_img_url( 'facility-cafe.jpg' ) ); ?>" alt="Apex Protein Kafe ve Fit Bowl Lounge">
                <span class="apex-ed-photo-cap">03 <i>/</i> <?php echo esc_html( apex_cms( 'fac3_stat' ) ); ?></span>
            </div>
            <div>
                <span class="apex-pt-rule" aria-hidden="true"></span>
                <span class="apex-eyebrow"><?php echo esc_html( apex_cms( 'fac3_role' ) ); ?></span>
                <h2 id="fac-cafe-h" class="apex-h2"><?php echo esc_html( apex_cms( 'fac3_title' ) ); ?></h2>
                <p class="apex-lead"><?php echo esc_html( apex_cms( 'fac3_desc' ) ); ?></p>
                <div class="apex-ed-meta-strip" style="margin-top:20px;">
                    <div><span>SAATLER</span><strong>Her Gün 07:30 – 22:30</strong></div>
                    <div><span>LOUNGE</span><strong>1 Gbps Fiber Wi-Fi</strong></div>
                    <div><span>İÇERİK</span><strong>Şekersiz &amp; Katkısız</strong></div>
                </div>
            </div>
        </div>

        <div class="apex-section-head" style="margin-bottom:24px;">
            <h3 class="apex-h2">Kafe Menüsü &amp; Makro Değerleri</h3>
            <div class="apex-pt-filter-bar" role="tablist" aria-label="Kafe menü kategorileri">
                <button type="button" class="apex-pt-tab is-active" data-filter-group="cafe" data-filter="all" role="tab" aria-selected="true">Tümü</button>
                <button type="button" class="apex-pt-tab" data-filter-group="cafe" data-filter="shake" role="tab" aria-selected="false">Protein Shake</button>
                <button type="button" class="apex-pt-tab" data-filter-group="cafe" data-filter="bowl" role="tab" aria-selected="false">Fit Bowl</button>
                <button type="button" class="apex-pt-tab" data-filter-group="cafe" data-filter="coffee" role="tab" aria-selected="false">Kahve</button>
                <button type="button" class="apex-pt-tab" data-filter-group="cafe" data-filter="snack" role="tab" aria-selected="false">Atıştırmalık</button>
            </div>
        </div>

        <div class="apex-pt-ledger" data-filter-container="cafe">
            <?php foreach ( $menu as $m_i => $item ) :
                preg_match( '/(\d+)\s*kcal/i', $item['macros'], $mk );
                preg_match( '/(\d+)\s*g\s*Protein/i', $item['macros'], $mp );
                $kcal_val = isset( $mk[1] ) ? (int) $mk[1] : 220;
                $pro_val  = isset( $mp[1] ) ? (int) $mp[1] : 20;
                $num      = str_pad( (string) ( $m_i + 1 ), 2, '0', STR_PAD_LEFT );
            ?>
            <article class="apex-pt-cell" data-filter-item="<?php echo esc_attr( $item['category'] ); ?>">
                <div class="apex-pt-cell-top">
                    <span class="apex-pt-num"><?php echo esc_html( $num ); ?></span>
                    <span class="apex-pt-meta"><?php echo esc_html( strtoupper( $item['category'] ) ); ?></span>
                    <span class="apex-pt-total" style="margin-left:auto;font-size:1.55rem;color:var(--apex-crimson);"><?php echo esc_html( apex_format_tl( $item['price'] ) ); ?></span>
                </div>
                <div class="apex-pt-cell-head" style="margin-bottom:10px;">
                    <span class="apex-pt-rule" aria-hidden="true"></span>
                    <h4 class="apex-pt-title" style="min-height:auto;font-size:1.45rem;"><?php echo esc_html( $item['name'] ); ?></h4>
                </div>
                <div style="font-size:0.75rem;font-weight:700;letter-spacing:0.06em;color:#f4e268;margin-bottom:8px;"><?php echo esc_html( $item['macros'] ); ?></div>
                <p class="apex-pt-days" style="flex:1;margin-top:0;font-size:0.84rem;color:rgba(212,212,216,0.8);"><?php echo esc_html( $item['desc'] ); ?></p>
                <button
                    type="button"
                    class="apex-pt-cta apex-pt-cta-btn"
                    data-cafe-add
                    data-item-name="<?php echo esc_attr( $item['name'] ); ?>"
                    data-item-price="<?php echo esc_attr( $item['price'] ); ?>"
                    data-item-kcal="<?php echo esc_attr( $kcal_val ); ?>"
                    data-item-protein="<?php echo esc_attr( $pro_val ); ?>"
                >
                    <span>+ Sepete Ekle</span>
                    <span class="apex-pt-cta-arrow" aria-hidden="true">↗</span>
                </button>
            </article>
            <?php endforeach; ?>
        </div>

        <!-- Antrenman Çıkışı Kafe Ön Sipariş Sepeti (v2.0.0) -->
        <div class="apex-panel apex-cafe-cart" id="apex-cafe-cart" style="margin-top:30px;">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap;margin-bottom:16px;">
                <div>
                    <span class="apex-eyebrow" style="margin-bottom:4px;">ANTRENMAN ÇIKIŞI HAZIR BEKLESİN</span>
                    <h3 class="apex-h3" style="margin:0;">Kafe Ön Sipariş Sepeti</h3>
                </div>
                <div class="apex-tags" id="apex-cafe-macro-summary">
                    <span class="apex-tag">0 Ürün</span>
                    <span class="apex-tag apex-tag-volt">0 kcal · 0g Protein</span>
                    <span class="apex-tag" style="background:rgba(227,24,55,0.18);color:#fff;">Toplam: ₺0</span>
                </div>
            </div>
            <div id="apex-cafe-cart-items" class="apex-muted" style="font-size:0.9rem;margin-bottom:16px;">Yukarıdaki menüden dilediğiniz shake, fit bowl veya kahveyi sepete ekleyin.</div>
            <form id="apex-cafe-order-form" class="apex-form-grid" novalidate>
                <div class="apex-field"><label for="cafe-name">Ad Soyad *</label><input type="text" id="cafe-name" name="customer_name" placeholder="Adınız Soyadınız" required></div>
                <div class="apex-field"><label for="cafe-phone">Telefon *</label><input type="tel" id="cafe-phone" name="customer_phone" placeholder="0532 000 00 00" required></div>
                <div class="apex-field">
                    <label for="cafe-pickup">Teslim Zamanı</label>
                    <select id="cafe-pickup" name="pickup_time">
                        <option value="15 dk sonra">15 dk sonra (Hızlı Teslim)</option>
                        <option value="Antrenman Çıkışı (20 dk)" selected>Antrenman Çıkışı (20 dk)</option>
                        <option value="30 dk sonra">30 dk sonra</option>
                        <option value="45 dk sonra">45 dk sonra</option>
                    </select>
                </div>
                <div class="apex-field"><label for="cafe-note">Hazırlama Notu</label><input type="text" id="cafe-note" name="notes" placeholder="Örn. Badem sütüyle, buzsuz..."></div>
                <div class="apex-field apex-field-full">
                    <button type="submit" class="apex-btn apex-btn-primary">Ön Siparişi Baristaya Gönder</button>
                </div>
            </form>
            <div id="apex-cafe-order-result" role="status" aria-live="polite" style="margin-top:12px;"></div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
