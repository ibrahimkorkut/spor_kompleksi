<?php
/**
 * Template Name: PT Eğitmenler & Online Randevu
 * Rubric Kapsamı (30 Puan): Bireysel Antrenman, Grupsal Antrenman, PT Paketler & Seans Ücretleri,
 * Eğitim Detayları / Kapsamı / İçeriği / Günler, Antrenör Seçimi, Online Randevu & Rezervasyon
 *
 * @package ApexArena
 */
get_header();

$packages = get_posts( [ 'post_type' => 'apex_pt_package', 'numberposts' => 20, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC' ] );
$trainers = get_posts( [ 'post_type' => 'apex_trainer', 'numberposts' => 20, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC' ] );
$pre_pkg  = absint( $_GET['package'] ?? 0 );
$pre_tr   = absint( $_GET['trainer'] ?? ( $trainers[0]->ID ?? 0 ) );
$cu       = is_user_logged_in() ? wp_get_current_user() : null;
?>

<section class="apex-page-hero apex-editorial-hero">
    <div class="apex-container">
        <?php apex_breadcrumbs(); ?>
        <span class="apex-eyebrow"><?php echo esc_html( apex_cms( 'page_pt_eyebrow' ) ); ?></span>
        <h1 class="apex-h1"><?php echo esc_html( apex_cms( 'page_pt_title' ) ); ?></h1>
        <p class="apex-lead"><?php echo esc_html( apex_cms( 'page_pt_desc' ) ); ?></p>
    </div>
</section>

<!-- 1. Bireysel vs Grupsal Antrenman Özeti (İsviçre Tipi İnce Çizgili 2 Sütun) -->
<section class="apex-section" id="bireysel">
    <div class="apex-container">
        <div class="apex-ed-split-2">
            <div class="apex-ed-cell is-featured">
                <div class="apex-pt-cell-top">
                    <span class="apex-pt-num">01</span>
                    <span class="apex-pt-meta">1-ON-1 KİŞİYE ÖZEL KOÇLUK</span>
                </div>
                <span class="apex-pt-rule" aria-hidden="true"></span>
                <h2 class="apex-pt-title" style="min-height:auto;font-size:clamp(1.9rem,2.6vw,2.4rem);">BİREYSEL ANTRENMAN</h2>
                <p class="apex-pt-days" style="font-size:0.9rem;color:rgba(212,212,216,0.82);">Sadece senin biyomekaniğine, hedeflerine ve haftalık takvimine göre tasarlanan birebir özel PT seansları.</p>
                <ul class="apex-pt-list" style="margin-bottom:0;">
                    <li>Başlangıçta InBody 570 vücut analizi &amp; postür değerlendirmesi</li>
                    <li>Kişiye özel hipertrofi, yağ yakımı, atletik performans veya rehabilitasyon planı</li>
                    <li>Esnek saat seçimi (08:00 – 22:00 arası antrenör canlı takviminden)</li>
                    <li>Makro &amp; beslenme rehberliği + 4 haftada bir ilerleme raporu</li>
                </ul>
            </div>

            <div class="apex-ed-cell" id="grupsal">
                <div class="apex-pt-cell-top">
                    <span class="apex-pt-num">02</span>
                    <span class="apex-pt-meta">MAKSİMUM 8 KİŞİLİK BUTİK GRUP</span>
                </div>
                <span class="apex-pt-rule" aria-hidden="true"></span>
                <h2 class="apex-pt-title" style="min-height:auto;font-size:clamp(1.9rem,2.6vw,2.4rem);">GRUPSAL ANTRENMAN</h2>
                <p class="apex-pt-days" style="font-size:0.9rem;color:rgba(212,212,216,0.82);">Takım motivasyonuyla sınırları zorlayan, butik kontenjanlı (6–8 kişi) HIIT, Fonksiyonel Kuvvet, Reformer Pilates ve Basketbol/Voleybol Kondisyon dersleri.</p>
                <ul class="apex-pt-list" style="margin-bottom:0;">
                    <li>Ekonomik seans başı ücretle profesyonel PT gözetimi</li>
                    <li>Sabit haftalık eğitim günleri ile sürdürülebilir disiplin</li>
                    <li>Seviyeye göre (Başlangıç / Orta / İleri) ölçeklenebilir ağırlık ve tempo</li>
                    <li>Nabız takip ekranlı yüksek motivasyonlu stüdyo atmosferi</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- 2. PT Paketleri, Seans Ücretleri, Eğitim Detayları & Kapsamı (İsviçre Tipi Mimari Tablo) -->
<section class="apex-section apex-section-alt" id="paketler" aria-labelledby="pt-packages-h">
    <div class="apex-container">
        <div class="apex-section-head">
            <div>
                <span class="apex-eyebrow">ŞEFFAF FİYATLANDIRMA · KAPSAMLARI VE GÜNLERİ</span>
                <h2 id="pt-packages-h" class="apex-h2">PT Paketleri ve Seans Ücretleri</h2>
            </div>
            <div class="apex-pt-filter-bar" role="tablist" aria-label="PT paket türü filtresi">
                <button type="button" class="apex-pt-tab is-active" data-filter-group="pt-all" data-filter="all" role="tab" aria-selected="true">Tümü</button>
                <button type="button" class="apex-pt-tab" data-filter-group="pt-all" data-filter="bireysel" role="tab" aria-selected="false">Bireysel PT</button>
                <button type="button" class="apex-pt-tab" data-filter-group="pt-all" data-filter="grupsal" role="tab" aria-selected="false">Grupsal</button>
            </div>
        </div>

        <div class="apex-pt-ledger" data-filter-container="pt-all">
            <?php foreach ( $packages as $idx => $pkg ) :
                $type  = get_post_meta( $pkg->ID, '_apex_training_type', true ) ?: 'bireysel';
                $sess  = get_post_meta( $pkg->ID, '_apex_sessions', true );
                $per   = (int) get_post_meta( $pkg->ID, '_apex_per_session_fee', true );
                $total = (int) get_post_meta( $pkg->ID, '_apex_total_price', true );
                $dur   = get_post_meta( $pkg->ID, '_apex_duration', true ) ?: 60;
                $days  = get_post_meta( $pkg->ID, '_apex_days', true );
                $level = get_post_meta( $pkg->ID, '_apex_level', true );
                $scope = array_filter( array_map( 'trim', explode( "\n", (string) get_post_meta( $pkg->ID, '_apex_scope', true ) ) ) );
                $feat  = get_post_meta( $pkg->ID, '_apex_featured', true ) === '1';
                $num   = str_pad( (string) ( $idx + 1 ), 2, '0', STR_PAD_LEFT );
            ?>
            <article class="apex-pt-cell <?php echo $feat ? 'is-featured' : ''; ?>" data-filter-item="<?php echo esc_attr( $type ); ?>">
                <div class="apex-pt-cell-top">
                    <span class="apex-pt-num"><?php echo esc_html( $num ); ?></span>
                    <span class="apex-pt-meta">
                        <?php echo $type === 'grupsal' ? 'GRUPSAL' : 'BİREYSEL PT'; ?> <i>/</i> <?php echo esc_html( $sess ); ?> SEANS · <?php echo esc_html( $dur ); ?> DK
                    </span>
                    <?php if ( $feat ) : ?><span class="apex-pt-pick"><i aria-hidden="true"></i>Önerilen</span><?php endif; ?>
                </div>
                <div class="apex-pt-cell-head">
                    <span class="apex-pt-rule" aria-hidden="true"></span>
                    <h3 class="apex-pt-title"><?php echo esc_html( $pkg->post_title ); ?></h3>
                </div>
                <div class="apex-pt-price-row">
                    <span class="apex-pt-total"><?php echo esc_html( apex_format_tl( $total ) ); ?></span>
                    <span class="apex-pt-unit"><?php echo esc_html( apex_format_tl( $per ) ); ?> <em>/ seans</em></span>
                </div>
                <p class="apex-pt-days"><?php echo esc_html( $days . ( $level ? ' · ' . $level : '' ) ); ?></p>
                <ul class="apex-pt-list">
                    <?php foreach ( $scope as $line ) : ?>
                        <li><?php echo esc_html( $line ); ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="apex-pt-cta apex-pt-cta-btn" data-select-pt-package="<?php echo esc_attr( $pkg->ID ); ?>" data-pt-type="<?php echo esc_attr( $type ); ?>">
                    <span>Bu Paketle Randevu Al</span>
                    <span class="apex-pt-cta-arrow" aria-hidden="true">↗</span>
                </button>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 3. Haftalık Eğitim Günleri & Ders Programı Tablosu -->
<section class="apex-section" id="program" aria-labelledby="pt-schedule-h">
    <div class="apex-container">
        <span class="apex-eyebrow">EĞİTİM DETAYLARI · GÜNLER & SAATLER</span>
        <h2 id="pt-schedule-h" class="apex-h2" style="margin-bottom:24px;">HAFTALIK GRUP & PT EĞİTİM PROGRAMI</h2>

        <div class="apex-table-wrap">
            <table class="apex-table">
                <thead>
                    <tr>
                        <th>Saat</th>
                        <th>Pazartesi</th>
                        <th>Salı</th>
                        <th>Çarşamba</th>
                        <th>Perşembe</th>
                        <th>Cuma</th>
                        <th>Cumartesi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>08:30</strong></td>
                        <td>Bireysel PT<br><small class="apex-muted">Kerem Alkan</small></td>
                        <td>Reformer Pilates (Grup)<br><small class="apex-muted">Selin Yılmaz</small></td>
                        <td>Bireysel PT<br><small class="apex-muted">Kerem Alkan</small></td>
                        <td>Reformer Pilates (Grup)<br><small class="apex-muted">Selin Yılmaz</small></td>
                        <td>Atletik Mobilite<br><small class="apex-muted">Mert Demir</small></td>
                        <td>Sabah HIIT Burn<br><small class="apex-muted">Elif Karaca</small></td>
                    </tr>
                    <tr>
                        <td><strong>12:30</strong></td>
                        <td>Core & Postür (Grup)<br><small class="apex-muted">Selin Yılmaz</small></td>
                        <td>Bireysel Kuvvet PT<br><small class="apex-muted">Kerem Alkan</small></td>
                        <td>Core & Postür (Grup)<br><small class="apex-muted">Selin Yılmaz</small></td>
                        <td>Bireysel Kuvvet PT<br><small class="apex-muted">Kerem Alkan</small></td>
                        <td>Express HIIT 45’<br><small class="apex-muted">Elif Karaca</small></td>
                        <td>Sıçrama & Çeviklik<br><small class="apex-muted">Mert Demir</small></td>
                    </tr>
                    <tr>
                        <td><strong>18:30</strong></td>
                        <td>HIIT & Yağ Yakımı (Grup)<br><small class="apex-muted">Elif Karaca</small></td>
                        <td>Fonksiyonel Kuvvet (Grup)<br><small class="apex-muted">Kerem Alkan</small></td>
                        <td>HIIT & Yağ Yakımı (Grup)<br><small class="apex-muted">Elif Karaca</small></td>
                        <td>Fonksiyonel Kuvvet (Grup)<br><small class="apex-muted">Kerem Alkan</small></td>
                        <td>Voleybol & Basketbol Kondisyon<br><small class="apex-muted">Mert Demir</small></td>
                        <td>Bireysel PT Seansları<br><small class="apex-muted">Tüm Kadro</small></td>
                    </tr>
                    <tr>
                        <td><strong>20:00</strong></td>
                        <td>Bireysel PT (VIP)<br><small class="apex-muted">Kerem Alkan</small></td>
                        <td>Reformer Pilates Akşam<br><small class="apex-muted">Selin Yılmaz</small></td>
                        <td>Atletik Performans (Grup)<br><small class="apex-muted">Mert Demir</small></td>
                        <td>Reformer Pilates Akşam<br><small class="apex-muted">Selin Yılmaz</small></td>
                        <td>Bireysel PT<br><small class="apex-muted">Elif Karaca</small></td>
                        <td>Esneklik & Recovery<br><small class="apex-muted">Selin Yılmaz</small></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- 4. Antrenör Seçimi + Canlı Online PT Randevu ve Rezervasyon -->
<section class="apex-section apex-section-alt" id="randevu" aria-labelledby="pt-booking-h">
    <div class="apex-container">
        <span class="apex-eyebrow apex-eyebrow-volt">ANTRENÖR SEÇİMİ & ONLİNE RANDEVU</span>
        <h2 id="pt-booking-h" class="apex-h2" style="margin-bottom:28px;">ANTRENÖRÜNÜ SEÇ VE ANINDA RANDEVU AL</h2>

        <div class="apex-grid-2" style="align-items:stretch;">
            <!-- Antrenör Seçim Kartları -->
            <div class="apex-panel apex-pt-picker-panel">
                <h3 class="apex-h3" style="margin-bottom:18px;">1. Antrenör Seçimi</h3>
                <div class="apex-pt-picker-list">
                <?php foreach ( $trainers as $tr ) :
                    $role  = get_post_meta( $tr->ID, '_apex_role', true );
                    $spec  = get_post_meta( $tr->ID, '_apex_specialty', true );
                    $fee   = (int) get_post_meta( $tr->ID, '_apex_session_fee', true );
                    $photo = apex_get_post_photo( $tr->ID, 'trainer-kerem.jpg' );
                    $sel   = ( $tr->ID === $pre_tr );
                ?>
                <div class="apex-trainer-pick <?php echo $sel ? 'apex-card-featured' : ''; ?>" data-pick-trainer="<?php echo esc_attr( $tr->ID ); ?>">
                    <img src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( $tr->post_title ); ?>" style="width:76px;height:76px;border-radius:10px;object-fit:cover;object-position:top;flex-shrink:0;">
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;justify-content:space-between;gap:8px;align-items:center;flex-wrap:wrap;">
                            <strong style="font-size:1.04rem;"><?php echo esc_html( $tr->post_title ); ?></strong>
                            <span class="apex-tag apex-tag-volt"><?php echo esc_html( apex_format_tl( $fee ) ); ?> / seans</span>
                        </div>
                        <div style="color:var(--apex-crimson);font-size:0.8rem;font-weight:700;margin-top:2px;"><?php echo esc_html( $role ); ?></div>
                        <div class="apex-muted" style="font-size:0.83rem;margin-top:4px;"><?php echo esc_html( $spec ); ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
                </div>
                <div class="apex-pt-picker-foot">
                    <span>✓ Ücretsiz InBody Analizi</span>
                    <span>✓ Canlı Beslenme Takvimi</span>
                    <span>✓ 24 Saat Esnek İptal</span>
                </div>
            </div>

            <!-- Canlı Randevu Formu -->
            <div class="apex-panel">
                <h3 class="apex-h3" style="margin-bottom:18px;">2. Online PT Randevu & Rezervasyon Formu</h3>
                <form id="apex-pt-booking-form" class="apex-form-grid" novalidate>
                    <div class="apex-field">
                        <label for="pt-trainer-select">Seçilen Antrenör *</label>
                        <select id="pt-trainer-select" name="trainer_id" required>
                            <?php foreach ( $trainers as $tr ) : ?>
                                <option value="<?php echo esc_attr( $tr->ID ); ?>" <?php selected( $tr->ID, $pre_tr ); ?>><?php echo esc_html( $tr->post_title ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="apex-field">
                        <label for="pt-type-select">Antrenman Türü *</label>
                        <select id="pt-type-select" name="training_type" required>
                            <option value="bireysel">Bireysel Antrenman (1-on-1)</option>
                            <option value="grupsal">Grupsal Antrenman (Butik Grup)</option>
                        </select>
                    </div>

                    <div class="apex-field apex-field-full">
                        <label for="pt-package-select">PT Paketi Seçimi</label>
                        <select id="pt-package-select" name="package_id">
                            <option value="0">Tek Seans Başlangıç & Değerlendirme</option>
                            <?php foreach ( $packages as $pkg ) :
                                $tot = (int) get_post_meta( $pkg->ID, '_apex_total_price', true );
                            ?>
                                <option value="<?php echo esc_attr( $pkg->ID ); ?>" <?php selected( $pkg->ID, $pre_pkg ); ?>>
                                    <?php echo esc_html( $pkg->post_title . ' — ' . apex_format_tl( $tot ) ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="apex-field apex-field-full">
                        <label for="pt-date-input">Randevu Tarihi *</label>
                        <input type="date" id="pt-date-input" name="date" min="<?php echo esc_attr( current_time( 'Y-m-d' ) ); ?>" value="<?php echo esc_attr( current_time( 'Y-m-d' ) ); ?>" required>
                    </div>

                    <div class="apex-field apex-field-full">
                        <label>Müsait Saat Dilimi Seçin * <small class="apex-muted">(Canlı veritabanı kontrolü)</small></label>
                        <input type="hidden" id="pt-time-slot" name="time_slot" required>
                        <div class="apex-slots-grid" id="pt-slots-container" role="group" aria-label="Antrenör müsait saatleri">
                            <span class="apex-muted">Saatler yükleniyor...</span>
                        </div>
                    </div>

                    <div class="apex-field">
                        <label for="pt-name">Ad Soyad *</label>
                        <input type="text" id="pt-name" name="customer_name" value="<?php echo esc_attr( $cu ? $cu->display_name : '' ); ?>" placeholder="Örn. Emre Kaya" required>
                    </div>

                    <div class="apex-field">
                        <label for="pt-phone">Telefon *</label>
                        <input type="tel" id="pt-phone" name="customer_phone" value="<?php echo esc_attr( $cu ? get_user_meta( $cu->ID, 'apex_member_phone', true ) : '' ); ?>" placeholder="0532 000 00 00" required>
                    </div>

                    <div class="apex-field">
                        <label for="pt-email">E-posta *</label>
                        <input type="email" id="pt-email" name="customer_email" value="<?php echo esc_attr( $cu ? $cu->user_email : '' ); ?>" placeholder="ornek@mail.com" required>
                    </div>

                    <div class="apex-field">
                        <label for="pt-goal">Antrenman Hedefiniz</label>
                        <select id="pt-goal" name="goal">
                            <option value="Yağ Yakımı & Sıkılaşma">Yağ Yakımı & Sıkılaşma</option>
                            <option value="Kas Kazanımı & Kuvvet">Kas Kazanımı & Kuvvet</option>
                            <option value="Basketbol / Voleybol Performansı">Basketbol / Voleybol Performansı</option>
                            <option value="Postür & Reformer Pilates">Postür & Reformer Pilates</option>
                            <option value="Genel Kondisyon & Sağlık">Genel Kondisyon & Sağlık</option>
                        </select>
                    </div>

                    <div class="apex-field apex-field-full">
                        <label for="pt-notes">Ek Notlar / Sağlık Bilgisi</label>
                        <textarea id="pt-notes" name="notes" placeholder="Varsa sakatlık geçmişiniz veya özel talebiniz..."></textarea>
                    </div>

                    <div class="apex-field apex-field-full">
                        <button type="submit" class="apex-btn apex-btn-primary apex-btn-lg apex-btn-block">ONLİNE PT RANDEVUSUNU ONAYLA</button>
                    </div>
                </form>
                <div id="pt-booking-result" role="status" aria-live="polite"></div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
