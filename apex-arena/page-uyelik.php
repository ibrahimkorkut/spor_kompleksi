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

<section class="apex-page-hero apex-editorial-hero">
    <div class="apex-container">
        <?php apex_breadcrumbs(); ?>
        <span class="apex-eyebrow"><?php echo esc_html( apex_cms( 'page_mem_eyebrow' ) ); ?></span>
        <h1 class="apex-h1"><?php echo esc_html( apex_cms( 'page_mem_title' ) ); ?></h1>
        <p class="apex-lead"><?php echo esc_html( apex_cms( 'page_mem_desc' ) ); ?></p>
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
                    <?php if ( current_user_can( 'apex_access_coach_portal' ) || current_user_can( 'manage_options' ) ) : ?>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=apex-coach-portal' ) ); ?>" class="apex-btn apex-btn-ghost apex-btn-sm">📊 KOÇ PANELİ</a>
                    <?php endif; ?>
                    <a href="<?php echo esc_url( wp_logout_url( home_url( '/uyelik/' ) ) ); ?>" class="apex-btn apex-btn-outline apex-btn-sm">ÇIKIŞ YAP</a>
                </div>
            </div>
            <div style="text-align:center;">
                <button type="button" class="apex-qr-trigger" id="apex-qr-open" title="QR kodu büyüt" aria-label="Dijital QR kartını büyüt" aria-controls="apex-qr-modal">
                    <?php echo apex_generate_member_qr_svg( $m_code ); ?>
                    <span class="apex-qr-trigger-lbl">TURNİKE &amp; OTOPARK QR</span>
                    <span class="apex-qr-trigger-hint">Büyütmek için tıkla ↗</span>
                </button>
            </div>
        </div>

        <!-- Dijital QR Kart Büyütme Pop-up Modalı -->
        <div class="apex-modal-backdrop apex-qr-backdrop" id="apex-qr-modal" role="dialog" aria-modal="true" aria-labelledby="apex-qr-modal-title" hidden>
            <div class="apex-modal apex-qr-modal">
                <div class="apex-qr-modal-head">
                    <span class="apex-live-pill" style="font-size:0.72rem;padding:4px 11px;">
                        <span class="apex-live-dot" aria-hidden="true"></span>
                        AKTİF GEÇİŞ KARTI · <?php echo esc_html( $m_plan ); ?>
                    </span>
                    <button type="button" class="apex-mobile-close" data-close-modal="apex-qr-modal" aria-label="Kapat" style="padding:5px 11px;">✕</button>
                </div>
                <h3 id="apex-qr-modal-title" class="apex-h3" style="margin:10px 0 4px;text-align:center;"><?php echo esc_html( $cu->display_name ); ?></h3>
                <p class="apex-muted" style="margin:0 0 18px;text-align:center;font-size:0.84rem;">Turnike veya kapalı otopark okuyucusuna yaklaştırın.</p>
                <div class="apex-qr-stage">
                    <span class="apex-qr-scanline" aria-hidden="true"></span>
                    <?php echo apex_generate_member_qr_svg( $m_code ); ?>
                </div>
                <div class="apex-qr-code-pill">
                    <span>Üye Kodu</span>
                    <strong><?php echo esc_html( $m_code ); ?></strong>
                </div>
                <div class="apex-muted" style="font-size:0.78rem;text-align:center;margin-top:10px;">
                    Geçerlilik: <strong style="color:var(--apex-text);"><?php echo esc_html( date( 'd.m.Y', strtotime( $m_end ) ) ); ?></strong>
                </div>
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
                                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                    <button type="button" class="apex-btn apex-btn-ghost apex-btn-sm"
                                        data-ics-btn
                                        data-ics-title="<?php echo esc_attr( 'APEX ARENA — ' . $b->court_name ); ?>"
                                        data-ics-date="<?php echo esc_attr( $b->booking_date ); ?>"
                                        data-ics-time="<?php echo esc_attr( $b->time_slot ); ?>"
                                        data-ics-hours="<?php echo esc_attr( $b->duration_hours ); ?>"
                                        data-ics-desc="<?php echo esc_attr( 'Rezervasyon Kodu: ' . $b->booking_code . ( $b->needs_referee ? ' · Lisanslı Hakem Dahil' : '' ) ); ?>">📅 .ics</button>
                                    <button type="button" class="apex-btn apex-btn-outline apex-btn-sm" data-cancel-booking="<?php echo esc_attr( $b->id ); ?>" data-booking-kind="court">İPTAL ET</button>
                                </div>
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
                                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                    <button type="button" class="apex-btn apex-btn-ghost apex-btn-sm"
                                        data-ics-btn
                                        data-ics-title="<?php echo esc_attr( 'APEX ARENA PT — ' . $pt->trainer_name ); ?>"
                                        data-ics-date="<?php echo esc_attr( $pt->appointment_date ); ?>"
                                        data-ics-time="<?php echo esc_attr( $pt->time_slot ); ?>"
                                        data-ics-hours="1"
                                        data-ics-desc="<?php echo esc_attr( 'PT Randevu Kodu: ' . $pt->booking_code . ' · ' . $pt->package_name ); ?>">📅 .ics</button>
                                    <button type="button" class="apex-btn apex-btn-outline apex-btn-sm" data-cancel-booking="<?php echo esc_attr( $pt->id ); ?>" data-booking-kind="pt">İPTAL ET</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php
        // v2.0.0 — Canlı Koç Takibi, Beslenme Takvimi, 1-1 Koç Mesajlaşması & Haftalık Antrenman Günlüğü
        $my_coach_rec = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}apex_coach_students WHERE student_user_id = %d OR student_email = %s LIMIT 1",
            $cu->ID, $cu->user_email
        ) );
        $my_nut = $my_coach_rec ? $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}apex_nutrition_plans WHERE student_id = %d LIMIT 1",
            $my_coach_rec->id
        ) ) : null;
        $my_meals = $my_nut ? ( json_decode( (string) $my_nut->meals_json, true ) ?: [] ) : [];
        $my_cmsgs = $my_coach_rec ? $wpdb->get_results( $wpdb->prepare(
            "SELECT sender_role, sender_name, message, DATE_FORMAT(created_at, '%%d.%%m %%H:%%i') AS time FROM {$wpdb->prefix}apex_coach_messages WHERE student_id = %d ORDER BY id ASC LIMIT 40",
            $my_coach_rec->id
        ) ) : [];
        $my_workouts = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}apex_workout_logs WHERE user_id = %d ORDER BY workout_date DESC, id DESC LIMIT 7",
            $cu->ID
        ) );
        $total_wk_min  = array_sum( array_map( fn( $w ) => (int) $w->duration_min, $my_workouts ) );
        $total_wk_kcal = array_sum( array_map( fn( $w ) => (int) $w->calories, $my_workouts ) );
        ?>

        <!-- KOÇUMUN BESLENME TAKVİMİ, ÖNERİSİ & BİREBİR MESAJLAŞMA (CANLI SENKRON) -->
        <?php if ( $my_coach_rec ) :
            $sm = function_exists( 'apex_coach_status_meta' ) ? apex_coach_status_meta( $my_coach_rec->status ) : [ 'label' => 'Hedefte', 'color' => '#30d158', 'bg' => 'rgba(48,209,88,0.16)' ];
        ?>
        <div class="apex-grid-2" style="align-items:stretch;" id="koc-takip">
            <div class="apex-panel">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;margin-bottom:14px;">
                    <div>
                        <span class="apex-eyebrow" style="margin-bottom:4px;">KOÇUM: <?php echo esc_html( strtoupper( $my_coach_rec->coach_name ) ); ?></span>
                        <h3 class="apex-h3" style="margin:0;">🥗 Beslenme Takvimim &amp; Koç Önerisi</h3>
                    </div>
                    <span class="apex-tag" style="background:<?php echo esc_attr( $sm['bg'] ); ?>;color:<?php echo esc_attr( $sm['color'] ); ?>;border:1px solid <?php echo esc_attr( $sm['color'] ); ?>;">
                        <?php echo esc_html( $sm['label'] ); ?> · %<?php echo esc_html( $my_coach_rec->progress_pct ); ?>
                    </span>
                </div>

                <?php if ( ! empty( $my_coach_rec->coach_advice ) ) : ?>
                <div class="apex-coach-advice-box">
                    <strong>💡 <?php echo esc_html( $my_coach_rec->coach_name ); ?>’ın Güncel Önerisi:</strong>
                    <p style="margin:6px 0 0;"><?php echo esc_html( $my_coach_rec->coach_advice ); ?></p>
                </div>
                <?php endif; ?>

                <?php if ( $my_nut ) : ?>
                <div class="apex-tags" style="margin:14px 0;">
                    <span class="apex-tag apex-tag-volt">🔥 <?php echo esc_html( $my_nut->daily_kcal ); ?> kcal</span>
                    <span class="apex-tag">🥩 <?php echo esc_html( $my_nut->protein_g ); ?>g Protein</span>
                    <span class="apex-tag">🍚 <?php echo esc_html( $my_nut->carbs_g ); ?>g Karb</span>
                    <span class="apex-tag">🥑 <?php echo esc_html( $my_nut->fat_g ); ?>g Yağ</span>
                    <span class="apex-tag">💧 <?php echo esc_html( $my_nut->water_l ); ?>L Su</span>
                </div>

                <div class="apex-meal-list">
                    <?php
                    $meal_labels = [
                        'sabah'     => '08:00 · Kahvaltı',
                        'ara1'      => '11:00 · Ara Öğün',
                        'ogle'      => '13:30 · Öğle Yemeği',
                        'ant_sonra' => 'Antrenman Öncesi / Sonrası',
                        'aksam'     => '19:30 · Akşam Yemeği',
                    ];
                    foreach ( $meal_labels as $mk => $ml ) :
                        if ( empty( $my_meals[ $mk ] ) ) continue;
                    ?>
                    <div class="apex-meal-row">
                        <strong><?php echo esc_html( $ml ); ?></strong>
                        <span><?php echo esc_html( $my_meals[ $mk ] ); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php if ( ! empty( $my_nut->weekly_notes ) ) : ?>
                    <small class="apex-muted" style="display:block;margin-top:10px;">📌 <?php echo esc_html( $my_nut->weekly_notes ); ?></small>
                <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="apex-panel apex-member-chat-panel">
                <span class="apex-eyebrow" style="margin-bottom:4px;">BİREBİR CANLI İLETİŞİM</span>
                <h3 class="apex-h3" style="margin:0 0 14px;">💬 Koçumla Mesajlaş (<?php echo esc_html( $my_coach_rec->coach_name ); ?>)</h3>
                <div class="apex-member-chat-wrap">
                    <div class="apex-member-chat-box" id="apex-member-chat-box" aria-live="polite">
                        <?php foreach ( $my_cmsgs as $cm ) : ?>
                        <div class="apex-mchat-bubble <?php echo $cm->sender_role === 'student' ? 'is-me' : 'is-coach'; ?>">
                            <?php echo esc_html( $cm->message ); ?>
                            <small><?php echo esc_html( $cm->sender_name . ' · ' . $cm->time ); ?></small>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="apex-ai-scrollbar is-hidden" id="apex-member-chat-scrollbar" role="scrollbar" aria-controls="apex-member-chat-box" aria-orientation="vertical" aria-valuemin="0" aria-valuemax="100" aria-valuenow="100" aria-label="Koç sohbeti kaydırma çubuğu">
                        <div class="apex-ai-scrollbar-track">
                            <div class="apex-ai-scrollbar-thumb" id="apex-member-chat-thumb"></div>
                        </div>
                    </div>
                </div>
                <form id="apex-member-coach-chat-form" style="display:flex;gap:8px;margin-top:12px;" novalidate>
                    <input type="text" id="member-coach-msg-input" placeholder="Koçuna beslenme veya antrenman hakkında mesaj yaz..." style="flex:1;background:var(--apex-bg);border:1px solid var(--apex-border-strong);border-radius:999px;padding:10px 16px;color:var(--apex-text);font-size:0.88rem;" required>
                    <button type="submit" class="apex-btn apex-btn-primary apex-btn-sm">Gönder</button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- HAFTALIK ANTRENMAN & HEDEF TAKİBİ (v2.0.0) -->
        <div class="apex-panel" id="antrenman-takip">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap;margin-bottom:18px;">
                <div>
                    <span class="apex-eyebrow" style="margin-bottom:4px;">KİŞİSEL PERFORMANS GÜNLÜĞÜ</span>
                    <h3 class="apex-h3" style="margin:0;">📈 Haftalık Antrenman &amp; Kalori Takibi</h3>
                </div>
                <div class="apex-tags" id="apex-wk-kpis" data-sessions="<?php echo count( $my_workouts ); ?>" data-minutes="<?php echo esc_attr( $total_wk_min ); ?>" data-kcal="<?php echo esc_attr( $total_wk_kcal ); ?>">
                    <span class="apex-tag apex-tag-volt" id="apex-wk-kpi-sessions"><?php echo count( $my_workouts ); ?> Seans</span>
                    <span class="apex-tag" id="apex-wk-kpi-min">⏱ <?php echo esc_html( $total_wk_min ); ?> dk</span>
                    <span class="apex-tag" id="apex-wk-kpi-kcal">🔥 <?php echo esc_html( number_format( $total_wk_kcal, 0, ',', '.' ) ); ?> kcal</span>
                </div>
            </div>

            <?php
            $wk_goal_kcal = 3500;
            $wk_goal_pct  = min( 100, (int) round( ( $total_wk_kcal / $wk_goal_kcal ) * 100 ) );
            $wk_avg_min   = count( $my_workouts ) > 0 ? (int) round( $total_wk_min / count( $my_workouts ) ) : 0;
            $wk_avg_kcal  = count( $my_workouts ) > 0 ? (int) round( $total_wk_kcal / count( $my_workouts ) ) : 0;
            ?>
            <div class="apex-grid-2" style="align-items:stretch;">
                <div>
                    <div class="apex-wk-bars" id="apex-wk-list">
                        <?php foreach ( $my_workouts as $wk ) :
                            $pct = min( 100, round( ( (int) $wk->calories / 750 ) * 100 ) );
                        ?>
                        <div class="apex-wk-row">
                            <div style="display:flex;justify-content:space-between;font-size:0.86rem;margin-bottom:5px;">
                                <strong><?php echo esc_html( $wk->workout_type ); ?> <small class="apex-muted">(<?php echo esc_html( date( 'd.m', strtotime( $wk->workout_date ) ) ); ?>)</small></strong>
                                <span><?php echo esc_html( $wk->duration_min ); ?> dk · <strong style="color:var(--apex-crimson);"><?php echo esc_html( $wk->calories ); ?> kcal</strong></span>
                            </div>
                            <div style="height:7px;background:var(--apex-bg);border-radius:99px;overflow:hidden;">
                                <div style="width:<?php echo esc_attr( max( 12, $pct ) ); ?>%;height:100%;background:linear-gradient(90deg,#e31837,#f4e268);border-radius:99px;"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <form id="apex-workout-log-form" class="apex-wk-form-card" novalidate>
                    <div class="apex-wk-form-head">
                        <span class="apex-wk-form-title">Yeni Antrenman Kaydı</span>
                        <div class="apex-wk-presets" aria-label="Hızlı antrenman şablonları">
                            <button type="button" class="apex-wk-preset" data-wk-preset="Kuvvet & Hipertrofi|60|520">Kuvvet</button>
                            <button type="button" class="apex-wk-preset" data-wk-preset="HIIT & Kardiyo|45|480">HIIT</button>
                            <button type="button" class="apex-wk-preset" data-wk-preset="Basketbol / Voleybol Maç|60|620">Maç</button>
                            <button type="button" class="apex-wk-preset" data-wk-preset="Reformer Pilates & Mobilite|50|340">Pilates</button>
                        </div>
                    </div>

                    <div class="apex-form-grid">
                        <div class="apex-field">
                            <label for="wk-type">Antrenman Türü</label>
                            <select id="wk-type" name="workout_type">
                                <option value="Kuvvet & Hipertrofi">Kuvvet &amp; Hipertrofi</option>
                                <option value="Basketbol / Voleybol Maç">Basketbol / Voleybol Maç</option>
                                <option value="HIIT & Kardiyo">HIIT &amp; Kardiyo</option>
                                <option value="Reformer Pilates & Mobilite">Reformer Pilates &amp; Mobilite</option>
                            </select>
                        </div>
                        <div class="apex-field"><label for="wk-dur">Süre (dk)</label><input type="number" id="wk-dur" name="duration_min" value="60" min="15" max="240"></div>
                        <div class="apex-field"><label for="wk-kcal">Yakılan Kalori</label><input type="number" id="wk-kcal" name="calories" value="520" min="50" max="2000"></div>
                        <div class="apex-field"><label for="wk-note">Kısa Not</label><input type="text" id="wk-note" name="notes" placeholder="Örn. Yeni PR!"></div>
                    </div>

                    <div class="apex-wk-goal-box">
                        <div class="apex-wk-goal-top">
                            <span>Haftalık Kalori Hedefi</span>
                            <strong id="apex-wk-goal-txt"><?php echo esc_html( number_format( $total_wk_kcal, 0, ',', '.' ) ); ?> / 3.500 kcal (%<?php echo esc_html( $wk_goal_pct ); ?>)</strong>
                        </div>
                        <div class="apex-wk-goal-track">
                            <div class="apex-wk-goal-fill" id="apex-wk-goal-fill" style="width:<?php echo esc_attr( max( 6, $wk_goal_pct ) ); ?>%;"></div>
                        </div>
                        <div class="apex-wk-goal-meta">
                            <span>Ort. Süre: <strong id="apex-wk-avg-min"><?php echo esc_html( $wk_avg_min ); ?> dk</strong></span>
                            <span>Ort. Yakım: <strong id="apex-wk-avg-kcal"><?php echo esc_html( $wk_avg_kcal ); ?> kcal</strong></span>
                        </div>
                    </div>

                    <button type="submit" class="apex-btn apex-btn-primary apex-btn-block">+ Bugünkü Antrenmanı Kaydet</button>
                </form>
            </div>
        </div>

        <?php
        // v2.1.0 — Üye E-posta Bildirimleri & Otomatik Hatırlatma Merkezi
        $rem_opt_in  = get_user_meta( $cu->ID, 'apex_mail_reminders_opt_out', true ) !== '1';
        $gmail_ready = function_exists( 'apex_is_gmail_smtp_ready' ) && apex_is_gmail_smtp_ready();
        $my_emails   = $wpdb->get_results( $wpdb->prepare(
            "SELECT id, mail_type, subject, body_html, status, created_at FROM {$wpdb->prefix}apex_email_logs WHERE user_id = %d OR recipient_email = %s ORDER BY id DESC LIMIT 6",
            $cu->ID, $cu->user_email
        ) );
        ?>
        <!-- E-POSTA BİLDİRİMLERİ & HATIRLATMA MERKEZİ (v2.1.0) -->
        <div class="apex-panel apex-mail-panel" id="eposta-hatirlatma">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:14px;flex-wrap:wrap;margin-bottom:16px;">
                <div>
                    <span class="apex-eyebrow" style="margin-bottom:4px;">GMAIL &amp; E-POSTA OTOMASYONU</span>
                    <h3 class="apex-h3" style="margin:0;">📧 E-posta Bildirimlerim &amp; Hatırlatma Merkezi</h3>
                </div>
                <span class="apex-tag <?php echo $gmail_ready ? 'apex-tag-volt' : ''; ?>">
                    <?php echo $gmail_ready ? '● Canlı Gmail SMTP Aktif' : '● E-posta Önizleme Hazır'; ?>
                </span>
            </div>

            <div class="apex-mail-ctrl-bar">
                <div class="apex-mail-pref-box">
                    <div style="font-size:0.86rem;color:var(--apex-muted);margin-bottom:6px;">
                        Kayıtlı E-posta: <strong style="color:var(--apex-text);"><?php echo esc_html( $cu->user_email ); ?></strong>
                    </div>
                    <label class="apex-mail-switch">
                        <input type="checkbox" id="apex-member-rem-toggle" <?php checked( $rem_opt_in ); ?>>
                        <span>Salon/PT randevularımdan 24 saat önce ve haftalık antrenman <strong>hatırlatma e-postası</strong> al</span>
                    </label>
                </div>
                <div class="apex-mail-actions">
                    <button type="button" class="apex-btn apex-btn-ghost apex-btn-sm" data-member-mail-action="resend_welcome">✉️ Hoş Geldin &amp; QR Maili Gönder</button>
                    <button type="button" class="apex-btn apex-btn-primary apex-btn-sm" data-member-mail-action="send_reminder">⏰ Hatırlatma Maili Gönder</button>
                </div>
            </div>

            <div id="apex-member-mail-status" role="status" aria-live="polite" style="margin-top:12px;"></div>

            <div class="apex-mail-list" id="apex-member-email-list" style="margin-top:16px;">
                <?php if ( empty( $my_emails ) ) : ?>
                    <p class="apex-muted" style="margin:0;font-size:0.88rem;">Henüz hesabına ait e-posta kaydı yok. Yukarıdaki butonlarla anında Hoş Geldin veya Hatırlatma e-postası gönderebilirsin.</p>
                <?php else : foreach ( $my_emails as $em ) :
                    $tm = function_exists( 'apex_mail_type_meta' ) ? apex_mail_type_meta( $em->mail_type ) : [ 'label' => $em->mail_type, 'color' => '#a4ff2e', 'bg' => 'rgba(164,255,46,0.14)' ];
                    $st_lbl = $em->status === 'sent' ? '✓ Gmail İletildi' : ( $em->status === 'failed' ? '✕ Hata' : '● Hazır' );
                ?>
                <div class="apex-mail-row">
                    <div class="apex-mail-row-info">
                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:3px;">
                            <span class="apex-tag" style="background:<?php echo esc_attr( $tm['bg'] ); ?>;color:<?php echo esc_attr( $tm['color'] ); ?>;"><?php echo esc_html( $tm['label'] ); ?></span>
                            <small class="apex-muted"><?php echo esc_html( date( 'd.m.Y H:i', strtotime( $em->created_at ) ) ); ?> · <?php echo esc_html( $st_lbl ); ?></small>
                        </div>
                        <strong style="font-size:0.9rem;display:block;"><?php echo esc_html( $em->subject ); ?></strong>
                    </div>
                    <button type="button" class="apex-btn apex-btn-outline apex-btn-sm"
                        data-preview-mail
                        data-mail-subject="<?php echo esc_attr( $em->subject ); ?>"
                        data-mail-b64="<?php echo esc_attr( base64_encode( (string) $em->body_html ) ); ?>">👁 Maili Gör</button>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>

        <!-- Üye E-posta HTML Önizleme Modalı -->
        <div class="apex-modal-backdrop" id="apex-member-mail-modal" role="dialog" aria-modal="true" aria-labelledby="apex-mm-title" hidden>
            <div class="apex-modal apex-mail-modal">
                <div class="apex-qr-modal-head" style="margin-bottom:12px;">
                    <strong id="apex-mm-title" style="font-size:0.95rem;">E-posta Önizleme</strong>
                    <button type="button" class="apex-mobile-close" data-close-modal="apex-member-mail-modal" aria-label="Kapat" style="padding:5px 11px;">✕</button>
                </div>
                <iframe id="apex-mm-frame" class="apex-mail-iframe" sandbox="allow-same-origin" title="E-posta içeriği önizleme"></iframe>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 1. Üyelik Paketleri + Aylık / Yıllık Toggle (İsviçre Tipi Mimari Tablo) -->
<section class="apex-section apex-section-alt" id="paketler" aria-labelledby="mem-plans-h">
    <div class="apex-container">
        <div class="apex-section-head">
            <div>
                <span class="apex-eyebrow">ESNEK PLANLAR · YILLIKTA %20 AVANTAJ</span>
                <h2 id="mem-plans-h" class="apex-h2">Üyelik Paketleri Karşılaştırması</h2>
            </div>
            <div class="apex-pt-filter-bar" role="group" aria-label="Ödeme periyodu seçimi">
                <button type="button" class="apex-pt-tab is-active" data-billing-toggle="monthly">Aylık Ödeme</button>
                <button type="button" class="apex-pt-tab" data-billing-toggle="annual">Yıllık (%20 İndirim)</button>
            </div>
        </div>

        <div class="apex-pt-ledger">
            <?php $p_idx = 0; foreach ( $plans as $pl ) : $p_idx++; $num = str_pad( (string) $p_idx, 2, '0', STR_PAD_LEFT ); ?>
            <article class="apex-pt-cell <?php echo ! empty( $pl['featured'] ) ? 'is-featured' : ''; ?>">
                <div class="apex-pt-cell-top">
                    <span class="apex-pt-num"><?php echo esc_html( $num ); ?></span>
                    <span class="apex-pt-meta"><?php echo esc_html( $pl['badge'] ); ?></span>
                    <?php if ( ! empty( $pl['featured'] ) ) : ?><span class="apex-pt-pick"><i aria-hidden="true"></i>En Popüler</span><?php endif; ?>
                </div>
                <div class="apex-pt-cell-head">
                    <span class="apex-pt-rule" aria-hidden="true"></span>
                    <h3 class="apex-pt-title" style="min-height:1.1em;"><?php echo esc_html( $pl['name'] ); ?></h3>
                </div>
                <div class="apex-pt-price-row">
                    <span class="apex-pt-total" data-plan-price data-monthly="<?php echo esc_attr( apex_format_tl( $pl['monthly_price'] ) ); ?>" data-annual="<?php echo esc_attr( apex_format_tl( $pl['annual_price'] ) ); ?>">
                        <?php echo esc_html( apex_format_tl( $pl['monthly_price'] ) ); ?>
                    </span>
                    <span class="apex-pt-unit"><em>/ ay</em></span>
                </div>
                <p class="apex-pt-days"><?php echo esc_html( $pl['summary'] ); ?></p>
                <ul class="apex-pt-list">
                    <?php foreach ( $pl['features'] as $f ) : ?>
                        <li><?php echo esc_html( $f ); ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="apex-pt-cta apex-pt-cta-btn" data-select-plan="<?php echo esc_attr( $pl['slug'] ); ?>">
                    <span>Bu Paketi Seç</span>
                    <span class="apex-pt-cta-arrow" aria-hidden="true">↗</span>
                </button>
            </article>
            <?php endforeach; ?>
        </div>

        <!-- 1B. 3 Soruluk Akıllı Paket & PT Bulucu Quiz (v2.0.0) -->
        <div class="apex-panel apex-quiz-card" id="paket-bulucu" style="margin-top:36px;">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap;margin-bottom:18px;">
                <div>
                    <span class="apex-eyebrow" style="margin-bottom:4px;">30 SANİYEDE KİŞİSEL ÖNERİ</span>
                    <h3 class="apex-h3" style="margin:0;">🎯 Akıllı Üyelik &amp; PT Paketi Bulucu</h3>
                </div>
                <span class="apex-tag apex-tag-volt" id="quiz-step-label">Soru 1 / 3</span>
            </div>

            <div id="apex-quiz-body">
                <div class="apex-quiz-step is-active" data-quiz-step="1">
                    <p style="margin:0 0 12px;font-weight:700;">1. Birincil spor hedefin hangisi?</p>
                    <div class="apex-quiz-opts">
                        <button type="button" class="apex-quiz-opt" data-quiz-pick="goal" data-val="muscle">💪 Kas Kütlesi &amp; Kuvvet Kazanımı</button>
                        <button type="button" class="apex-quiz-opt" data-quiz-pick="goal" data-val="fatloss">🔥 Yağ Yakımı &amp; Sıkılaşma</button>
                        <button type="button" class="apex-quiz-opt" data-quiz-pick="goal" data-val="court">🏀 Basketbol / Voleybol Performansı</button>
                        <button type="button" class="apex-quiz-opt" data-quiz-pick="goal" data-val="wellness">🧘 Genel Sağlık, Postür &amp; Sauna</button>
                    </div>
                </div>
                <div class="apex-quiz-step" data-quiz-step="2">
                    <p style="margin:0 0 12px;font-weight:700;">2. Haftada kaç gün tesise gelmeyi planlıyorsun?</p>
                    <div class="apex-quiz-opts">
                        <button type="button" class="apex-quiz-opt" data-quiz-pick="freq" data-val="2">2 Gün / Hafta (Başlangıç)</button>
                        <button type="button" class="apex-quiz-opt" data-quiz-pick="freq" data-val="4">3–4 Gün / Hafta (Düzenli)</button>
                        <button type="button" class="apex-quiz-opt" data-quiz-pick="freq" data-val="6">5+ Gün / Hafta (Yoğun Performans)</button>
                    </div>
                </div>
                <div class="apex-quiz-step" data-quiz-step="3">
                    <p style="margin:0 0 12px;font-weight:700;">3. Birebir koç, beslenme takvimi ve salon ayrıcalığı istiyor musun?</p>
                    <div class="apex-quiz-opts">
                        <button type="button" class="apex-quiz-opt" data-quiz-pick="pt" data-val="none">Sadece kendi programımla çalışacağım</button>
                        <button type="button" class="apex-quiz-opt" data-quiz-pick="pt" data-val="group">Grup dersleri + ara sıra salon/kafe avantajı</button>
                        <button type="button" class="apex-quiz-opt" data-quiz-pick="pt" data-val="vip">Birebir PT koç, özel beslenme takvimi &amp; VIP</button>
                    </div>
                </div>
                <div class="apex-quiz-result" id="apex-quiz-result" hidden></div>
            </div>
        </div>

        <!-- 1C. Detaylı Paket Karşılaştırma Matrisi (v2.0.0) -->
        <div style="margin-top:32px;">
            <h3 class="apex-h3" style="margin-bottom:14px;">Detaylı Paket Karşılaştırma Tablosu</h3>
            <div class="apex-table-wrap">
                <table class="apex-table">
                    <thead>
                        <tr><th>Özellik / Ayrıcalık</th><th>APEX CLASSIC</th><th>APEX PRO</th><th>APEX ELITE + PT</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>Fitness, Kardiyo &amp; Fonksiyonel Alan</td><td>✓ 7/24</td><td>✓ 7/24</td><td>✓ 7/24 VIP</td></tr>
                        <tr><td>RFID Akıllı Dolap, Yağmur Duş &amp; Fin Saunası</td><td>✓ Dahil</td><td>✓ Dahil</td><td>✓ Özel Dolap + Havlu</td></tr>
                        <tr><td>Dijital QR Giriş Kartı &amp; Online Üye Paneli</td><td>✓</td><td>✓</td><td>✓</td></tr>
                        <tr><td>Grup Dersleri (HIIT, Reformer, Core)</td><td>Ek Ücretli</td><td>✓ Sınırsız</td><td>✓ Sınırsız Öncelikli</td></tr>
                        <tr><td>Kapalı Otopark &amp; EV Şarj İndirimi</td><td>İlk 2 Saat</td><td>İlk 3 Saat</td><td>Sınırsız Ücretsiz</td></tr>
                        <tr><td>Apex Protein Kafe İndirimi</td><td>%5</td><td>%15</td><td>%25</td></tr>
                        <tr><td>Birebir PT Seansı &amp; Koç Beslenme Takvimi</td><td>Opsiyonel</td><td>Aylık InBody</td><td>✓ Ayda 8 PT + Canlı Koç Paneli</td></tr>
                        <tr><td>Basketbol / Voleybol Salon Kiralama Önceliği</td><td>Standart</td><td>%10 İndirimli</td><td>%20 İndirim + 1 Ücretsiz Maç</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- 2. Üye Girişi & Online Kayıt Kartı (Yalnızca Giriş Yapmamış Ziyaretçilere) / Giriş Yapmışsa Paket Yükseltme -->
<section class="apex-section" id="kayit" aria-label="<?php echo $logged ? 'Üyelik paketi değiştirme' : 'Üyelik kaydı ve üye girişi'; ?>">
    <span id="giris" class="apex-anchor-target" aria-hidden="true"></span>
    <div class="apex-container">
        <?php if ( ! $logged ) :
            $start_register = isset( $_GET['plan'] ) && ! empty( $_GET['plan'] );
        ?>
        <div class="apex-auth-stage <?php echo $start_register ? 'is-register' : ''; ?>" id="apex-auth-stage">
            <!-- Mobil Hızlı Geçiş Sekmesi (<= 900px) -->
            <div class="apex-auth-mobile-switch" role="tablist" aria-label="Giriş veya kayıt seçimi">
                <button type="button" class="apex-auth-m-btn <?php echo $start_register ? '' : 'is-active'; ?>" data-auth-mode="login" role="tab" aria-selected="<?php echo $start_register ? 'false' : 'true'; ?>">Üye Girişi</button>
                <button type="button" class="apex-auth-m-btn <?php echo $start_register ? 'is-active' : ''; ?>" data-auth-mode="register" role="tab" aria-selected="<?php echo $start_register ? 'true' : 'false'; ?>">Kayıt Ol</button>
            </div>

            <!-- SOL PANEL: ÜYE GİRİŞİ -->
            <div class="apex-auth-pane apex-auth-pane-login">
                <div class="apex-auth-pane-inner">
                    <div class="apex-auth-brand">
                        <span class="apex-brand-mark" aria-hidden="true">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3 22 20H2L12 3Z"/></svg>
                        </span>
                        <span><strong>APEX</strong> ARENA</span>
                    </div>
                    <h2 class="apex-auth-title">Giriş Yap</h2>
                    <p class="apex-auth-sub">Kullanıcı adın veya e-posta şifrenle dijital QR paneline bağlan.</p>

                    <form id="apex-member-login-form" class="apex-auth-form" novalidate>
                        <div class="apex-field">
                            <label for="login-user">Kullanıcı Adı veya E-posta</label>
                            <input type="text" id="login-user" name="username" placeholder="uye_demo" required>
                        </div>
                        <div class="apex-field">
                            <label for="login-pass">Şifre</label>
                            <input type="password" id="login-pass" name="password" placeholder="••••••••" required>
                        </div>
                        <div class="apex-auth-aux">
                            <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="apex-auth-forgot">Şifreni mi unuttun?</a>
                            <button type="button" class="apex-auth-demo-link" id="apex-fill-demo-login">Demo Üye Doldur</button>
                        </div>
                        <button type="submit" class="apex-btn apex-btn-primary apex-btn-lg apex-btn-block">GİRİŞ YAP</button>
                    </form>

                    <div id="mem-login-result" role="status" aria-live="polite" style="margin-top:10px;"></div>

                    <div class="apex-auth-perks" aria-label="Üyelik avantajları">
                        <span class="apex-auth-perk-chip" title="Dijital QR Giriş Kartı">QR</span>
                        <span class="apex-auth-perk-chip" title="2 FIBA Basketbol & 1 Voleybol Salonu">3 Salon</span>
                        <span class="apex-auth-perk-chip" title="Sertifikalı PT Takvimi">PT</span>
                        <span class="apex-auth-perk-chip" title="7/24 Gemini AI Spor Koçu">AI Koç</span>
                    </div>
                    <small class="apex-muted apex-auth-demo-note">Demo Test Hesabı: <code>uye_demo</code> / <code>ApexUye2026!</code></small>
                </div>
            </div>

            <!-- SAĞ PANEL: ONLINE ÜYELİK KAYDI -->
            <div class="apex-auth-pane apex-auth-pane-register">
                <div class="apex-auth-pane-inner apex-auth-pane-wide">
                    <span class="apex-eyebrow" style="margin-bottom:4px;">1 DAKİKADA DİJİTAL ÜYELİK</span>
                    <h2 class="apex-auth-title" style="font-size:clamp(1.5rem,2.4vw,2rem);margin-bottom:14px;">Online Üyelik Kaydı</h2>

                    <form id="apex-membership-register-form" class="apex-form-grid apex-auth-reg-grid" novalidate>
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
                            <input type="text" id="mem-fullname" name="full_name" value="" placeholder="Ahmet Yılmaz" required>
                        </div>
                        <div class="apex-field">
                            <label for="mem-phone">Telefon *</label>
                            <input type="tel" id="mem-phone" name="phone" value="" placeholder="0532 000 00 00" required>
                        </div>
                        <div class="apex-field">
                            <label for="mem-email">E-posta *</label>
                            <input type="email" id="mem-email" name="email" value="" placeholder="ornek@eposta.com" required>
                        </div>
                        <div class="apex-field">
                            <label for="mem-password">Hesap Şifresi * (Min. 6)</label>
                            <input type="password" id="mem-password" name="password" minlength="6" placeholder="••••••••" required>
                        </div>
                        <div class="apex-field">
                            <label for="mem-goal">Birincil Hedefiniz</label>
                            <select id="mem-goal" name="goal">
                                <option value="Genel Fitness & Kondisyon">Genel Fitness &amp; Kondisyon</option>
                                <option value="Yağ Yakımı & Kilo Kontrolü">Yağ Yakımı &amp; Kilo Kontrolü</option>
                                <option value="Kas Kazanımı & Kuvvet">Kas Kazanımı &amp; Kuvvet</option>
                                <option value="Basketbol / Voleybol Performansı">Basketbol / Voleybol Performansı</option>
                            </select>
                        </div>
                        <div class="apex-field">
                            <label for="mem-emergency">Acil Durum Yakını</label>
                            <input type="text" id="mem-emergency" name="emergency_contact" placeholder="Örn. Ayşe Yılmaz · 0533...">
                        </div>
                        <div class="apex-field apex-field-full apex-auth-consents">
                            <label>
                                <input type="checkbox" name="kvkk_consent" value="1" required>
                                <span><a href="<?php echo esc_url( home_url( '/kvkk-aydinlatma-metni/' ) ); ?>" target="_blank">KVKK Aydınlatma Metni</a>’ni okudum, onaylıyorum. *</span>
                            </label>
                            <label>
                                <input type="checkbox" name="terms_consent" value="1" required>
                                <span><a href="<?php echo esc_url( home_url( '/uyelik-sozlesmesi/' ) ); ?>" target="_blank">Üyelik ve Mesafeli Satış Sözleşmesi</a>’ni kabul ediyorum. *</span>
                            </label>
                            <label>
                                <input type="checkbox" name="reminder_opt_in" value="1" checked>
                                <span>Kayıt sonrası <strong>Hoş Geldin &amp; Dijital QR Kart</strong> ve yaklaşan randevu hatırlatma e-postalarını almak istiyorum.</span>
                            </label>
                        </div>
                        <div class="apex-field apex-field-full">
                            <button type="submit" class="apex-btn apex-btn-primary apex-btn-block">ÜYELİĞİ BAŞLAT &amp; QR KART OLUŞTUR</button>
                        </div>
                    </form>
                    <div id="mem-register-result" role="status" aria-live="polite" style="margin-top:8px;"></div>
                </div>
            </div>

            <!-- KAYAR KAVİSLİ APEX KIRMIZI ÖRTÜ PANELİ -->
            <div class="apex-auth-overlay-wrap" aria-hidden="false">
                <div class="apex-auth-overlay">
                    <div class="apex-auth-overlay-panel apex-auth-overlay-left">
                        <span class="apex-auth-ov-eyebrow">MEVCUT ÜYELER</span>
                        <h3 class="apex-auth-ov-title">Tekrar Hoş Geldin!</h3>
                        <p class="apex-auth-ov-text">Zaten APEX ARENA üyesiysen hesabına giriş yap; dijital QR kartını, salon rezervasyonlarını ve PT takvimini hemen yönet.</p>
                        <button type="button" class="apex-auth-ov-btn" data-auth-mode="login">GİRİŞ YAP</button>
                    </div>
                    <div class="apex-auth-overlay-panel apex-auth-overlay-right">
                        <span class="apex-auth-ov-eyebrow">YENİ ÜYELİK</span>
                        <h3 class="apex-auth-ov-title">Merhaba, Sporcu!</h3>
                        <p class="apex-auth-ov-text">Sitenin tüm özelliklerini kullanmak, dijital QR kartını oluşturmak ve salon/PT rezervasyonu yapmak için hemen kayıt ol.</p>
                        <button type="button" class="apex-auth-ov-btn" data-auth-mode="register">KAYIT OL</button>
                    </div>
                </div>
            </div>
        </div>
        <?php else : ?>
        <!-- GİRİŞ YAPMIŞ ÜYE İÇİN SADECE PAKET DEĞİŞTİRME / YÜKSELTME PANELİ (GİRİŞ KARTI GİZLİ) -->
        <div class="apex-panel" style="max-width:1080px;margin:0 auto;">
            <span class="apex-eyebrow" style="margin-bottom:4px;">MEVCUT HESABINLA ANINDA GÜNCELLE</span>
            <h2 class="apex-h3" style="margin:0 0 6px;">Paket Değiştir / Yükselt</h2>
            <p class="apex-muted" style="margin:0 0 18px;font-size:0.9rem;">Giriş yapmış hesabın (<strong><?php echo esc_html( $cu->display_name ); ?></strong>) üzerinden yeni paket veya ödeme periyodu seçebilirsin.</p>

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
                    <input type="text" id="mem-fullname" name="full_name" value="<?php echo esc_attr( $cu->display_name ); ?>" required>
                </div>
                <div class="apex-field">
                    <label for="mem-phone">Telefon *</label>
                    <input type="tel" id="mem-phone" name="phone" value="<?php echo esc_attr( get_user_meta( $cu->ID, 'apex_member_phone', true ) ); ?>" placeholder="0532 000 00 00" required>
                </div>
                <div class="apex-field">
                    <label for="mem-email">E-posta *</label>
                    <input type="email" id="mem-email" name="email" value="<?php echo esc_attr( $cu->user_email ); ?>" required>
                </div>
                <div class="apex-field">
                    <label for="mem-goal">Birincil Hedefiniz</label>
                    <select id="mem-goal" name="goal">
                        <option value="Genel Fitness & Kondisyon">Genel Fitness &amp; Kondisyon</option>
                        <option value="Yağ Yakımı & Kilo Kontrolü">Yağ Yakımı &amp; Kilo Kontrolü</option>
                        <option value="Kas Kazanımı & Kuvvet">Kas Kazanımı &amp; Kuvvet</option>
                        <option value="Basketbol / Voleybol Performansı">Basketbol / Voleybol Performansı</option>
                    </select>
                </div>
                <div class="apex-field apex-field-full">
                    <label for="mem-emergency">Acil Durum Yakını</label>
                    <input type="text" id="mem-emergency" name="emergency_contact" placeholder="Örn. Ayşe Yılmaz · 0533...">
                </div>
                <div class="apex-field apex-field-full apex-auth-consents">
                    <label>
                        <input type="checkbox" name="kvkk_consent" value="1" required>
                        <span><a href="<?php echo esc_url( home_url( '/kvkk-aydinlatma-metni/' ) ); ?>" target="_blank">KVKK Aydınlatma Metni</a>’ni okudum, onaylıyorum. *</span>
                    </label>
                    <label>
                        <input type="checkbox" name="terms_consent" value="1" required>
                        <span><a href="<?php echo esc_url( home_url( '/uyelik-sozlesmesi/' ) ); ?>" target="_blank">Üyelik ve Mesafeli Satış Sözleşmesi</a>’ni kabul ediyorum. *</span>
                    </label>
                </div>
                <div class="apex-field apex-field-full">
                    <button type="submit" class="apex-btn apex-btn-primary apex-btn-block">PAKETİMİ GÜNCELLE</button>
                </div>
            </form>
            <div id="mem-register-result" role="status" aria-live="polite" style="margin-top:10px;"></div>
        </div>
        <?php endif; ?>

        <!-- 3. Geniş Yatay VKİ & Günlük Kalori Hesaplayıcı Barı -->
        <div class="apex-panel apex-bmi-bar" id="vki">
            <div class="apex-bmi-bar-head">
                <span class="apex-eyebrow" style="margin-bottom:4px;">HESAPLAMA ARACI</span>
                <h2 class="apex-h3">VKİ (BMI) &amp; Günlük Kalori Hesaplayıcı</h2>
            </div>
            <form id="apex-bmi-form" class="apex-bmi-inline-form">
                <div class="apex-field">
                    <label for="bmi-height">Boy (cm)</label>
                    <input type="number" id="bmi-height" value="178" min="120" max="230" required>
                </div>
                <div class="apex-field">
                    <label for="bmi-weight">Kilo (kg)</label>
                    <input type="number" id="bmi-weight" value="78" min="35" max="220" required>
                </div>
                <button type="submit" class="apex-btn apex-btn-volt apex-bmi-submit">HESAPLA VE PROGRAM ÖNER</button>
            </form>
            <div id="bmi-output" style="margin-top:14px;" role="status" aria-live="polite"></div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
