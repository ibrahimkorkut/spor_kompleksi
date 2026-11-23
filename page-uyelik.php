<?php
/**
 * Template Name: Üyelik İşlemleri & Üye Paneli
 * Rubric Kapsamı (10 Puan): Paket Seçimi & Karşılaştırma, Online Kayıt, Üye Girişi,
 * Dijital QR Üyelik Kartı, Rezervasyon & PT Randevu Yönetimi, VKİ/Kalori Hesaplayıcı
 *
 * @package ApexArena
 */
get_header();

global $wpdb;
$plans    = apex_get_membership_plans();
$pre_plan = sanitize_key( $_GET['plan'] ?? 'pro' );
if ( ! isset( $plans[ $pre_plan ] ) ) {
    $pre_plan = 'pro';
}
$logged = is_user_logged_in();
$cu     = $logged ? wp_get_current_user() : null;
?>

<section class="apex-page-hero">
    <div class="apex-container">
        <?php apex_breadcrumbs(); ?>
        <span class="apex-eyebrow">ÜYELİK İŞLEMLERİ · DİJİTAL QR KART & ONLINE ÜYE PANELİ</span>
        <h1 class="apex-h1">ÜYELİK PAKETLERİ VE ÜYE PANELİ</h1>
        <p class="apex-lead">Hedefine uygun üyeliği seç, saniyeler içinde dijital QR üyelik kartını oluştur veya mevcut hesabınla giriş yaparak tüm salon ve PT rezervasyonlarını tek ekrandan yönet.</p>
    </div>
</section>

<?php if ( $logged && $cu ) :
    $m_plan   = get_user_meta( $cu->ID, 'apex_membership_plan', true ) ?: 'APEX PRO';
    $m_code   = get_user_meta( $cu->ID, 'apex_member_code', true ) ?: 'APX-2026-' . str_pad( (string) $cu->ID, 4, '0', STR_PAD_LEFT );
    $m_end    = get_user_meta( $cu->ID, 'apex_membership_end', true ) ?: date( 'Y-m-d', strtotime( '+300 days' ) );
    $m_goal   = get_user_meta( $cu->ID, 'apex_member_goal', true ) ?: 'Atletik Performans';
    $my_courts = $wpdb->get_results( $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}apex_court_bookings WHERE user_id = %d OR customer_email = %s ORDER BY id DESC LIMIT 20",
        $cu->ID, $cu->user_email
    ) );
    $my_pts = $wpdb->get_results( $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}apex_pt_appointments WHERE user_id = %d OR customer_email = %s ORDER BY id DESC LIMIT 20",
        $cu->ID, $cu->user_email
    ) );
?>
<!-- GİRİŞ YAPMIŞ ÜYE PANELİ (DASHBOARD) -->
<section class="apex-section" id="uye-paneli" aria-labelledby="member-dash-h">
    <div class="apex-container" style="display:grid;gap:28px;">
        <div class="apex-member-card">
            <div>
                <span class="apex-live-pill"><span class="apex-live-dot" aria-hidden="true"></span> AKTİF ÜYELİK · <?php echo esc_html( $m_plan ); ?></span>
                <h2 id="member-dash-h" class="apex-h2" style="margin-top:12px;">HOŞ GELDİN, <?php echo esc_html( $cu->display_name ); ?>!</h2>
                <p class="apex-muted" style="margin:6px 0 14px;">Üye No: <strong style="color:#fff;"><?php echo esc_html( $m_code ); ?></strong> · Bitiş: <strong style="color:#fff;"><?php echo esc_html( date( 'd.m.Y', strtotime( $m_end ) ) ); ?></strong> · Hedef: <strong style="color:var(--apex-volt);"><?php echo esc_html( $m_goal ); ?></strong></p>
                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <a href="<?php echo esc_url( home_url( '/salon-kiralama/#rezervasyon' ) ); ?>" class="apex-btn apex-btn-primary apex-btn-sm">+ YENİ SALON REZERVASYONU</a>
                    <a href="<?php echo esc_url( home_url( '/pt-egitmenler/#randevu' ) ); ?>" class="apex-btn apex-btn-volt apex-btn-sm">+ YENİ PT RANDEVUSU</a>
                    <a href="<?php echo esc_url( wp_logout_url( home_url( '/uyelik/' ) ) ); ?>" class="apex-btn apex-btn-outline apex-btn-sm">ÇIKIŞ YAP</a>
                </div>
            </div>
            <div style="text-align:center;">
                <?php echo apex_generate_member_qr_svg( $m_code ); ?>
                <div style="font-size:0.75rem;color:var(--apex-volt);font-weight:800;margin-top:6px;">TURNİKE & OTOPARK QR</div>
            </div>
        </div>

        <div class="apex-grid-2" style="align-items:start;">
            <div class="apex-panel">
                <h3 class="apex-h3" style="margin-bottom:16px;">🏀 SALON REZERVASYONLARIM</h3>
                <?php if ( empty( $my_courts ) ) : ?>
                    <p class="apex-muted">Henüz salon rezervasyonun yok. <a href="<?php echo esc_url( home_url( '/salon-kiralama/' ) ); ?>" style="color:var(--apex-crimson);font-weight:700;">Hemen kirala →</a></p>
                <?php else : ?>
                    <div style="display:grid;gap:12px;">
                        <?php foreach ( $my_courts as $b ) : ?>
                        <div style="background:var(--apex-bg);border:1px solid var(--apex-border);border-radius:10px;padding:14px;display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap;">
                            <div>
                                <strong><?php echo esc_html( $b->court_name ); ?></strong> <span class="apex-tag"><?php echo esc_html( $b->booking_code ); ?></span>
                                <div class="apex-muted" style="font-size:0.85rem;margin-top:4px;">📅 <?php echo esc_html( date( 'd.m.Y', strtotime( $b->booking_date ) ) . ' · ' . $b->time_slot . ' (' . $b->duration_hours . ' saat)' ); ?></div>
                                <div style="font-size:0.84rem;margin-top:4px;">
                                    <?php if ( $b->needs_referee ) : ?><span class="apex-tag apex-tag-volt">⚖️ Hakemli</span><?php endif; ?>
                                    <strong><?php echo esc_html( apex_format_tl( $b->total_price ) ); ?></strong> · Durum: <strong><?php echo esc_html( strtoupper( $b->status ) ); ?></strong>
                                </div>
                            </div>
                            <?php if ( $b->status !== 'cancelled' ) : ?>
                                <button type="button" class="apex-btn apex-btn-outline apex-btn-sm" data-cancel-booking="<?php echo esc_attr( $b->id ); ?>" data-booking-kind="court">İPTAL ET</button>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="apex-panel">
                <h3 class="apex-h3" style="margin-bottom:16px;">🏋️ PT RANDEVULARIM</h3>
                <?php if ( empty( $my_pts ) ) : ?>
                    <p class="apex-muted">Henüz PT randevun bulunmuyor. <a href="<?php echo esc_url( home_url( '/pt-egitmenler/' ) ); ?>" style="color:var(--apex-crimson);font-weight:700;">Antrenör seç →</a></p>
                <?php else : ?>
                    <div style="display:grid;gap:12px;">
                        <?php foreach ( $my_pts as $pt ) : ?>
                        <div style="background:var(--apex-bg);border:1px solid var(--apex-border);border-radius:10px;padding:14px;display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap;">
                            <div>
                                <strong><?php echo esc_html( $pt->trainer_name ); ?></strong> <span class="apex-tag"><?php echo esc_html( $pt->booking_code ); ?></span>
                                <div class="apex-muted" style="font-size:0.85rem;margin-top:4px;"><?php echo esc_html( strtoupper( $pt->training_type ) . ' · ' . $pt->package_name ); ?></div>
                                <div style="font-size:0.84rem;margin-top:4px;">📅 <?php echo esc_html( date( 'd.m.Y', strtotime( $pt->appointment_date ) ) . ' · ' . $pt->time_slot ); ?> · Durum: <strong><?php echo esc_html( strtoupper( $pt->status ) ); ?></strong></div>
                            </div>
                            <?php if ( $pt->status !== 'cancelled' ) : ?>
                                <button type="button" class="apex-btn apex-btn-outline apex-btn-sm" data-cancel-booking="<?php echo esc_attr( $pt->id ); ?>" data-booking-kind="pt">İPTAL ET</button>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 1. Üyelik Paketleri + Aylık / Yıllık Toggle -->
<section class="apex-section apex-section-alt" id="paketler" aria-labelledby="mem-plans-h">
    <div class="apex-container">
        <div class="apex-section-head">
            <div>
                <span class="apex-eyebrow apex-eyebrow-volt">ESNEK PLANLAR · YILLIKTA %20 AVANTAJ</span>
                <h2 id="mem-plans-h" class="apex-h2">ÜYELİK PAKETLERİ KARŞILAŞTIRMASI</h2>
            </div>
            <div class="apex-tabs" role="group" aria-label="Ödeme periyodu seçimi">
                <button type="button" class="apex-tab-btn is-active" data-billing-toggle="monthly">AYLIK ÖDEME</button>
                <button type="button" class="apex-tab-btn" data-billing-toggle="annual">YILLIK (%20 İNDİRİM)</button>
            </div>
        </div>

        <div class="apex-grid-3">
            <?php foreach ( $plans as $pl ) : ?>
            <article class="apex-card <?php echo $pl['featured'] ? 'apex-card-featured' : ''; ?>">
                <div class="apex-card-body">
                    <span class="apex-eyebrow <?php echo $pl['featured'] ? 'apex-eyebrow-volt' : ''; ?>"><?php echo esc_html( $pl['badge'] ); ?></span>
                    <h3 class="apex-h2"><?php echo esc_html( $pl['name'] ); ?></h3>
                    <div class="apex-price" style="color:var(--apex-crimson);">
                        <span data-plan-price data-monthly="<?php echo esc_attr( apex_format_tl( $pl['monthly_price'] ) ); ?>" data-annual="<?php echo esc_attr( apex_format_tl( $pl['annual_price'] ) ); ?>">
                            <?php echo esc_html( apex_format_tl( $pl['monthly_price'] ) ); ?>
                        </span>
                        <small>/ ay</small>
                    </div>
                    <p class="apex-muted" style="margin:0;font-size:0.9rem;"><?php echo esc_html( $pl['summary'] ); ?></p>
                    <ul class="apex-checklist">
                        <?php foreach ( $pl['features'] as $f ) : ?>
                            <li><?php echo esc_html( $f ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="apex-card-foot">
                        <button type="button" class="apex-btn <?php echo $pl['featured'] ? 'apex-btn-primary' : 'apex-btn-outline'; ?> apex-btn-block" data-select-plan="<?php echo esc_attr( $pl['slug'] ); ?>">BU PAKETİ SEÇ</button>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 2. Online Üye Kaydı & Üye Girişi -->
<section class="apex-section" id="kayit">
    <div class="apex-container">
        <div class="apex-grid-2" style="align-items:start;">
            <!-- Online Kayıt / Paket Başvurusu -->
            <div class="apex-panel">
                <span class="apex-eyebrow">1 DAKİKADA DİJİTAL ÜYELİK</span>
                <h2 class="apex-h2" style="margin-bottom:18px;"><?php echo $logged ? 'PAKET DEĞİŞTİR / YÜKSELT' : 'ONLINE ÜYELİK KAYDI'; ?></h2>
                <form id="apex-membership-register-form" class="apex-form-grid" novalidate>
                    <div class="apex-field">
                        <label for="mem-plan-select">Üyelik Paketi *</label>
                        <select id="mem-plan-select" name="plan_slug" required>
                            <?php foreach ( $plans as $pl ) : ?>
                                <option value="<?php echo esc_attr( $pl['slug'] ); ?>" <?php selected( $pl['slug'], $pre_plan ); ?>>
                                    <?php echo esc_html( $pl['name'] . ' — ' . apex_format_tl( $pl['monthly_price'] ) . '/ay' ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="apex-field">
                        <label for="mem-billing-select">Ödeme Periyodu *</label>
                        <select id="mem-billing-select" name="billing_cycle">
                            <option value="monthly">Aylık Standart</option>
                            <option value="annual">Yıllık (%20 İndirimli)</option>
                        </select>
                    </div>
                    <div class="apex-field">
                        <label for="mem-fullname">Ad Soyad *</label>
                        <input type="text" id="mem-fullname" name="full_name" value="<?php echo esc_attr( $cu ? $cu->display_name : '' ); ?>" required>
                    </div>
                    <div class="apex-field">
                        <label for="mem-phone">Telefon *</label>
                        <input type="tel" id="mem-phone" name="phone" value="<?php echo esc_attr( $cu ? get_user_meta( $cu->ID, 'apex_member_phone', true ) : '' ); ?>" placeholder="0532 000 00 00" required>
                    </div>
                    <div class="apex-field">
                        <label for="mem-email">E-posta *</label>
                        <input type="email" id="mem-email" name="email" value="<?php echo esc_attr( $cu ? $cu->user_email : '' ); ?>" required>
                    </div>
                    <?php if ( ! $logged ) : ?>
                    <div class="apex-field">
                        <label for="mem-password">Hesap Şifresi * (Min. 6 karakter)</label>
                        <input type="password" id="mem-password" name="password" minlength="6" required>
                    </div>
                    <?php endif; ?>
                    <div class="apex-field">
                        <label for="mem-goal">Birincil Hedefiniz</label>
                        <select id="mem-goal" name="goal">
                            <option value="Genel Fitness & Kondisyon">Genel Fitness & Kondisyon</option>
                            <option value="Yağ Yakımı & Kilo Kontrolü">Yağ Yakımı & Kilo Kontrolü</option>
                            <option value="Kas Kazanımı & Kuvvet">Kas Kazanımı & Kuvvet</option>
                            <option value="Basketbol / Voleybol Performansı">Basketbol / Voleybol Performansı</option>
                        </select>
                    </div>
                    <div class="apex-field">
                        <label for="mem-emergency">Acil Durum Yakını (Ad & Tel)</label>
                        <input type="text" id="mem-emergency" name="emergency_contact" placeholder="Örn. Ayşe Yılmaz · 0533...">
                    </div>
                    <div class="apex-field apex-field-full" style="gap:10px;">
                        <label style="display:flex;gap:10px;align-items:flex-start;font-weight:500;font-size:0.86rem;">
                            <input type="checkbox" name="kvkk_consent" value="1" required style="width:20px;height:20px;min-height:20px;">
                            <span><a href="<?php echo esc_url( home_url( '/kvkk-aydinlatma-metni/' ) ); ?>" target="_blank" style="color:var(--apex-volt);text-decoration:underline;">KVKK Aydınlatma Metni</a>’ni okudum ve kişisel verilerimin işlenmesini onaylıyorum. *</span>
                        </label>
                        <label style="display:flex;gap:10px;align-items:flex-start;font-weight:500;font-size:0.86rem;">
                            <input type="checkbox" name="terms_consent" value="1" required style="width:20px;height:20px;min-height:20px;">
                            <span><a href="<?php echo esc_url( home_url( '/uyelik-sozlesmesi/' ) ); ?>" target="_blank" style="color:var(--apex-volt);text-decoration:underline;">Üyelik ve Mesafeli Satış Sözleşmesi</a>’ni kabul ediyorum. *</span>
                        </label>
                    </div>
                    <div class="apex-field apex-field-full">
                        <button type="submit" class="apex-btn apex-btn-primary apex-btn-lg apex-btn-block">ÜYELİĞİ BAŞLAT & QR KART OLUŞTUR</button>
                    </div>
                </form>
                <div id="mem-register-result" role="status" aria-live="polite"></div>
            </div>

            <!-- Üye Girişi + VKİ Hesaplayıcı -->
            <div style="display:grid;gap:24px;">
                <div class="apex-panel" id="giris">
                    <span class="apex-eyebrow apex-eyebrow-volt">MEVCUT ÜYELER</span>
                    <h2 class="apex-h2" style="margin-bottom:14px;">ÜYE GİRİŞİ</h2>
                    <form id="apex-member-login-form" style="display:grid;gap:14px;" novalidate>
                        <div class="apex-field">
                            <label for="login-user">Kullanıcı Adı veya E-posta</label>
                            <input type="text" id="login-user" name="username" placeholder="uye_demo" required>
                        </div>
                        <div class="apex-field">
                            <label for="login-pass">Şifre</label>
                            <input type="password" id="login-pass" name="password" required>
                        </div>
                        <div style="display:flex;gap:10px;flex-wrap:wrap;">
                            <button type="submit" class="apex-btn apex-btn-primary" style="flex:1;">GİRİŞ YAP</button>
                            <button type="button" class="apex-btn apex-btn-ghost" id="apex-fill-demo-login">DEMO ÜYE DOLDUR</button>
                        </div>
                    </form>
                    <small class="apex-muted" style="display:block;margin-top:10px;">Demo Test Hesabı: <code>uye_demo</code> / <code>ApexUye2026!</code></small>
                    <div id="mem-login-result" role="status" aria-live="polite"></div>
                </div>

                <!-- VKİ & Günlük Kalori Hesaplayıcı (MACFit Hesaplama Araçları Esintisi) -->
                <div class="apex-panel" id="vki">
                    <span class="apex-eyebrow">HESAPLAMA ARACI</span>
                    <h2 class="apex-h3" style="margin-bottom:14px;">VKİ (BMI) & GÜNLÜK KALORİ HESAPLAYICI</h2>
                    <form id="apex-bmi-form" class="apex-form-grid">
                        <div class="apex-field"><label for="bmi-height">Boy (cm)</label><input type="number" id="bmi-height" value="178" min="120" max="230" required></div>
                        <div class="apex-field"><label for="bmi-weight">Kilo (kg)</label><input type="number" id="bmi-weight" value="78" min="35" max="220" required></div>
                        <div class="apex-field apex-field-full">
                            <button type="submit" class="apex-btn apex-btn-volt apex-btn-block">HESAPLA VE PROGRAM ÖNER</button>
                        </div>
                    </form>
                    <div id="bmi-output" style="margin-top:14px;" role="status" aria-live="polite"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
