<?php
/**
 * Ana Sayfa Şablonu — APEX ARENA (MACFit esintili yüksek enerjili editorial ritim)
 *
 * @package ApexArena
 */
get_header();

$settings = apex_get_settings();
$courts   = get_posts( [ 'post_type' => 'apex_court', 'numberposts' => 3, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC' ] );
$trainers = get_posts( [ 'post_type' => 'apex_trainer', 'numberposts' => 4, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC' ] );
$packages = get_posts( [ 'post_type' => 'apex_pt_package', 'numberposts' => 6, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC' ] );
$anns     = get_posts( [ 'post_type' => 'apex_announcement', 'numberposts' => 3, 'post_status' => 'publish' ] );
$plans    = apex_get_membership_plans();
$hero_title = get_theme_mod( 'apex_hero_title', 'SINIRLARINI ARENADA AŞ. PERFORMANSIN YENİ MERKEZİ.' );
$hero_sub   = get_theme_mod( 'apex_hero_subtitle', '2 FIBA standart basketbol salonu, profesyonel voleybol sahası, lisanslı hakem hizmeti, sertifikalı PT kadrosu, canlı online takvim ve 7/24 AI Spor Koçu tek komplekste.' );
?>

<!-- 1. HERO -->
<section class="apex-hero" aria-labelledby="hero-title">
    <div class="apex-hero-bg" aria-hidden="true">
        <img src="<?php echo esc_url( apex_img_url( 'hero-arena.jpg' ) ); ?>" alt="" width="1600" height="900">
    </div>
    <div class="apex-container apex-hero-inner">
        <div class="apex-hero-content">
            <span class="apex-live-pill">
                <span class="apex-live-dot" aria-hidden="true"></span>
                CANLI YOĞUNLUK: %<?php echo esc_html( $settings['live_occupancy'] ); ?> SAKİN · ŞU AN ANTRENMANA UYGUN
            </span>
            <h1 id="hero-title" class="apex-h1" style="margin-top:18px;">
                <?php echo esc_html( $hero_title ); ?>
            </h1>
            <p class="apex-lead"><?php echo esc_html( $hero_sub ); ?></p>

            <div class="apex-hero-ctas">
                <a href="<?php echo esc_url( home_url( '/uyelik/#paketler' ) ); ?>" class="apex-btn apex-btn-primary apex-btn-lg">BİZE KATIL</a>
                <a href="<?php echo esc_url( home_url( '/salon-kiralama/' ) ); ?>" class="apex-btn apex-btn-outline apex-btn-lg">SALON KİRALA</a>
                <button type="button" class="apex-btn apex-btn-ghost apex-btn-lg" data-open-ai>🤖 AI SPOR KOÇU</button>
            </div>
        </div>

        <div class="apex-kpi-grid" aria-label="Kompleks öne çıkan verileri">
            <div class="apex-kpi-card">
                <div class="apex-kpi-val">3 SALON</div>
                <div class="apex-kpi-label">2 Basketbol + 1 Voleybol · Hakem Hizmetli</div>
            </div>
            <div class="apex-kpi-card">
                <div class="apex-kpi-val">4+ PRO PT</div>
                <div class="apex-kpi-label">Sertifikalı Kadro · Canlı Online Takvim</div>
            </div>
            <div class="apex-kpi-card">
                <div class="apex-kpi-val">180 ARAÇ</div>
                <div class="apex-kpi-label">Kapalı Otopark, 6 EV Şarj, Duş & Protein Kafe</div>
            </div>
            <div class="apex-kpi-card">
                <div class="apex-kpi-val">7/24 AI + WCAG</div>
                <div class="apex-kpi-label">Hibrit AI Koç & Tam Erişilebilirlik Merkezi</div>
            </div>
        </div>
    </div>
</section>

<!-- 2. SALON VE KİRALAMA (2 Basketbol + 1 Voleybol + Hakem Ayarlama) -->
<section class="apex-section" aria-labelledby="home-courts-title">
    <div class="apex-container">
        <div class="apex-section-head">
            <div>
                <span class="apex-eyebrow">SALON VE KİRALAMA · LİSANSLI HAKEM AYARLAMA HİZMETİ</span>
                <h2 id="home-courts-title" class="apex-h2">PROFESYONEL MAÇ SALONLARI</h2>
                <p class="apex-lead">FIBA standartlarında 2 basketbol ve CEV standartlarında 1 voleybol salonumuzu canlı saat seçimi ve isteğe bağlı lisanslı hakem hizmetiyle anında kiralayın.</p>
            </div>
            <a href="<?php echo esc_url( home_url( '/salon-kiralama/' ) ); ?>" class="apex-btn apex-btn-outline">TÜM SALONLAR & REZERVASYON →</a>
        </div>

        <div class="apex-grid-3">
            <?php foreach ( $courts as $court ) :
                $sport = get_post_meta( $court->ID, '_apex_sport', true );
                $dim   = get_post_meta( $court->ID, '_apex_dimensions', true );
                $surf  = get_post_meta( $court->ID, '_apex_surface', true );
                $rate  = (int) get_post_meta( $court->ID, '_apex_hourly_rate', true );
                $ref   = (int) get_post_meta( $court->ID, '_apex_referee_fee', true );
                $photo = apex_get_post_photo( $court->ID, 'court-basketball-1.jpg' );
            ?>
            <article class="apex-card">
                <div class="apex-card-media">
                    <img src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( $court->post_title . ' - ' . $surf ); ?>" loading="lazy">
                    <span class="apex-card-badge"><?php echo esc_html( strtoupper( $sport ) . ' · ' . $dim ); ?></span>
                </div>
                <div class="apex-card-body">
                    <h3 class="apex-h3"><?php echo esc_html( $court->post_title ); ?></h3>
                    <p class="apex-muted" style="margin:0;"><?php echo esc_html( wp_trim_words( $court->post_excerpt ?: $court->post_content, 20 ) ); ?></p>
                    <div class="apex-tags">
                        <span class="apex-tag"><?php echo esc_html( $surf ); ?></span>
                        <span class="apex-tag apex-tag-volt">⚖️ + Hakem: <?php echo esc_html( apex_format_tl( $ref ) ); ?></span>
                    </div>
                    <div class="apex-card-foot">
                        <div class="apex-price"><?php echo esc_html( apex_format_tl( $rate ) ); ?> <small>/ saat</small></div>
                        <a href="<?php echo esc_url( home_url( '/salon-kiralama/?court=' . $court->ID . '#rezervasyon' ) ); ?>" class="apex-btn apex-btn-primary apex-btn-sm">REZERVE ET</a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 3. PT EĞİTMENLER (BİREYSEL & GRUPSAL ANTRENMAN + PAKETLER) -->
<section class="apex-section apex-section-alt" aria-labelledby="home-pt-title">
    <div class="apex-container">
        <div class="apex-section-head">
            <div>
                <span class="apex-eyebrow apex-eyebrow-volt">PT EĞİTMENLER · BİREYSEL & GRUPSAL ANTRENMAN</span>
                <h2 id="home-pt-title" class="apex-h2">PT PAKETLERİ & SEANS ÜCRETLERİ</h2>
                <p class="apex-lead">Hedefine göre birebir özel antrenman veya yüksek enerjili küçük grup derslerinden birini seç, günlerini gör ve online randevunu oluştur.</p>
            </div>
            <div class="apex-tabs" role="tablist" aria-label="PT paket filtresi">
                <button type="button" class="apex-tab-btn is-active" data-filter-group="home-pt" data-filter="all" role="tab" aria-selected="true">TÜMÜ</button>
                <button type="button" class="apex-tab-btn" data-filter-group="home-pt" data-filter="bireysel" role="tab" aria-selected="false">BİREYSEL</button>
                <button type="button" class="apex-tab-btn" data-filter-group="home-pt" data-filter="grupsal" role="tab" aria-selected="false">GRUPSAL</button>
            </div>
        </div>

        <div class="apex-grid-3" data-filter-container="home-pt">
            <?php foreach ( $packages as $pkg ) :
                $type  = get_post_meta( $pkg->ID, '_apex_training_type', true ) ?: 'bireysel';
                $sess  = get_post_meta( $pkg->ID, '_apex_sessions', true );
                $per   = (int) get_post_meta( $pkg->ID, '_apex_per_session_fee', true );
                $total = (int) get_post_meta( $pkg->ID, '_apex_total_price', true );
                $days  = get_post_meta( $pkg->ID, '_apex_days', true );
                $scope = array_filter( array_map( 'trim', explode( "\n", (string) get_post_meta( $pkg->ID, '_apex_scope', true ) ) ) );
                $feat  = get_post_meta( $pkg->ID, '_apex_featured', true ) === '1';
            ?>
            <article class="apex-card <?php echo $feat ? 'apex-card-featured' : ''; ?>" data-filter-item="<?php echo esc_attr( $type ); ?>">
                <div class="apex-card-body">
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap;">
                        <span class="apex-eyebrow <?php echo $type === 'grupsal' ? 'apex-eyebrow-volt' : ''; ?>" style="margin:0;">
                            <?php echo $type === 'grupsal' ? 'GRUPSAL ANTRENMAN' : 'BİREYSEL PT'; ?> · <?php echo esc_html( $sess ); ?> SEANS
                        </span>
                        <?php if ( $feat ) : ?><span class="apex-mini-badge">POPÜLER</span><?php endif; ?>
                    </div>
                    <h3 class="apex-h3"><?php echo esc_html( $pkg->post_title ); ?></h3>
                    <div class="apex-price"><?php echo esc_html( apex_format_tl( $total ) ); ?> <small>(Seans başı <?php echo esc_html( apex_format_tl( $per ) ); ?>)</small></div>
                    <p class="apex-muted" style="margin:0;font-size:0.88rem;">📅 <strong>Günler:</strong> <?php echo esc_html( $days ); ?></p>
                    <ul class="apex-checklist">
                        <?php foreach ( array_slice( $scope, 0, 4 ) as $line ) : ?>
                            <li><?php echo esc_html( $line ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="apex-card-foot">
                        <a href="<?php echo esc_url( home_url( '/pt-egitmenler/?package=' . $pkg->ID . '#randevu' ) ); ?>" class="apex-btn <?php echo $feat ? 'apex-btn-primary' : 'apex-btn-outline'; ?> apex-btn-block">RANDEVU AL</a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 4. ANTRENÖR KADROSU -->
<section class="apex-section" aria-labelledby="home-trainers-title">
    <div class="apex-container">
        <div class="apex-section-head">
            <div>
                <span class="apex-eyebrow">UZMAN KADRO · SERTİFİKA, ÖZGEÇMİŞ & CANLI TAKVİM</span>
                <h2 id="home-trainers-title" class="apex-h2">ANTRENÖRLERİMİZLE TANIŞ</h2>
                <p class="apex-lead">Her eğitmenimizin uzmanlık alanını, özgeçmişini, uluslararası sertifikalarını inceleyin ve canlı online takviminden doğrudan randevu alın.</p>
            </div>
            <a href="<?php echo esc_url( home_url( '/antrenorler/' ) ); ?>" class="apex-btn apex-btn-outline">TÜM ANTRENÖRLER & TAKVİM →</a>
        </div>

        <div class="apex-grid-4">
            <?php foreach ( $trainers as $tr ) :
                $role  = get_post_meta( $tr->ID, '_apex_role', true );
                $spec  = get_post_meta( $tr->ID, '_apex_specialty', true );
                $exp   = get_post_meta( $tr->ID, '_apex_experience', true );
                $rate  = get_post_meta( $tr->ID, '_apex_rating', true ) ?: '4.9';
                $photo = apex_get_post_photo( $tr->ID, 'trainer-kerem.jpg' );
            ?>
            <article class="apex-card">
                <div class="apex-card-media apex-card-media-portrait">
                    <img src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( $tr->post_title . ' - ' . $role ); ?>" loading="lazy">
                    <span class="apex-card-badge">★ <?php echo esc_html( $rate ); ?> · <?php echo esc_html( $exp ); ?> YIL</span>
                </div>
                <div class="apex-card-body">
                    <span class="apex-eyebrow" style="margin:0;"><?php echo esc_html( $role ); ?></span>
                    <h3 class="apex-h3"><?php echo esc_html( $tr->post_title ); ?></h3>
                    <p class="apex-muted" style="margin:0;font-size:0.88rem;"><?php echo esc_html( $spec ); ?></p>
                    <div class="apex-card-foot">
                        <a href="<?php echo esc_url( get_permalink( $tr->ID ) ); ?>" class="apex-btn apex-btn-outline apex-btn-sm apex-btn-block">PROFİL & TAKVİM</a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 5. TESİS OLANAKLARI (Soyunma Odası & Duş, Otopark, Kafe) -->
<section class="apex-section apex-section-alt" aria-labelledby="home-fac-title">
    <div class="apex-container">
        <div class="apex-section-head">
            <div>
                <span class="apex-eyebrow apex-eyebrow-volt">TESİS OLANAKLARI · 5 YILDIZLI KOMPLEKS</span>
                <h2 id="home-fac-title" class="apex-h2">SOYUNMA ODASI, OTOPARK VE PROTEİN KAFE</h2>
            </div>
            <a href="<?php echo esc_url( home_url( '/tesis-olanaklari/' ) ); ?>" class="apex-btn apex-btn-outline">TÜM OLANAKLAR & KAFE MENÜSÜ →</a>
        </div>

        <div class="apex-grid-3">
            <article class="apex-card">
                <div class="apex-card-media">
                    <img src="<?php echo esc_url( apex_img_url( 'facility-locker.jpg' ) ); ?>" alt="APEX ARENA Soyunma Odası, Yağmur Duş ve Sauna" loading="lazy">
                    <span class="apex-card-badge">240 DİJİTAL DOLAP & SAUNA</span>
                </div>
                <div class="apex-card-body">
                    <h3 class="apex-h3">SOYUNMA ODASI & DUŞ</h3>
                    <p class="apex-muted" style="margin:0;">RFID akıllı kilitli dolaplar, yüksek basınçlı yağmur duş kabinleri, Fin saunası, hijyenik havlu servisi ve tam engelli erişimli özel soyunma odası.</p>
                </div>
            </article>

            <article class="apex-card">
                <div class="apex-card-media">
                    <img src="<?php echo esc_url( apex_img_url( 'facility-parking.jpg' ) ); ?>" alt="APEX ARENA Kapalı Otopark ve EV Şarj İstasyonları" loading="lazy">
                    <span class="apex-card-badge"><?php echo esc_html( $settings['parking_free'] ); ?> BOŞ YER · 6 EV ŞARJ</span>
                </div>
                <div class="apex-card-body">
                    <h3 class="apex-h3">KAPALI OTOPARK</h3>
                    <p class="apex-muted" style="margin:0;">180 araçlık 7/24 güvenlikli kapalı otopark, üyelere ilk 3 saat ücretsiz kullanım, 6 elektrikli araç şarj ünitesi ve doğrudan asansörlü salon erişimi.</p>
                </div>
            </article>

            <article class="apex-card">
                <div class="apex-card-media">
                    <img src="<?php echo esc_url( apex_img_url( 'facility-cafe.jpg' ) ); ?>" alt="Apex Protein Kafe ve Lounge Bar" loading="lazy">
                    <span class="apex-card-badge apex-card-badge-crimson">PRO ÜYELERE %15 İNDİRİM</span>
                </div>
                <div class="apex-card-body">
                    <h3 class="apex-h3">APEX PROTEİN KAFE</h3>
                    <p class="apex-muted" style="margin:0;">Taze izole whey shake’ler, makro değerleri hesaplanmış fit bowl tabakları, 3. dalga cold brew kahveler ve maç sonrası sosyalleşme lounge alanı.</p>
                </div>
            </article>
        </div>
    </div>
</section>

<!-- 6. ÜYELİK PAKETLERİ ÖNİZLEME -->
<section class="apex-section" aria-labelledby="home-mem-title">
    <div class="apex-container">
        <div class="apex-section-head">
            <div>
                <span class="apex-eyebrow">ÜYELİK İŞLEMLERİ · ANINDA DİJİTAL QR KART</span>
                <h2 id="home-mem-title" class="apex-h2">HEDEFİNE UYGUN ÜYELİĞİ SEÇ</h2>
            </div>
            <a href="<?php echo esc_url( home_url( '/uyelik/' ) ); ?>" class="apex-btn apex-btn-outline">ÜYELİK KARŞILAŞTIRMA & PANEL →</a>
        </div>

        <div class="apex-grid-3">
            <?php foreach ( $plans as $pl ) : ?>
            <article class="apex-card <?php echo $pl['featured'] ? 'apex-card-featured' : ''; ?>">
                <div class="apex-card-body">
                    <span class="apex-eyebrow <?php echo $pl['featured'] ? 'apex-eyebrow-volt' : ''; ?>"><?php echo esc_html( $pl['badge'] ); ?></span>
                    <h3 class="apex-h2"><?php echo esc_html( $pl['name'] ); ?></h3>
                    <div class="apex-price" style="color:var(--apex-crimson);"><?php echo esc_html( apex_format_tl( $pl['monthly_price'] ) ); ?> <small>/ ay</small></div>
                    <p class="apex-muted" style="margin:0;font-size:0.9rem;"><?php echo esc_html( $pl['summary'] ); ?></p>
                    <ul class="apex-checklist">
                        <?php foreach ( $pl['features'] as $f ) : ?>
                            <li><?php echo esc_html( $f ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="apex-card-foot">
                        <a href="<?php echo esc_url( home_url( '/uyelik/?plan=' . $pl['slug'] . '#kayit' ) ); ?>" class="apex-btn <?php echo $pl['featured'] ? 'apex-btn-primary' : 'apex-btn-outline'; ?> apex-btn-block">HEMEN BAŞLA</a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 7. SALON DUYURULARI -->
<?php if ( ! empty( $anns ) ) : ?>
<section class="apex-section apex-section-alt" aria-labelledby="home-news-title">
    <div class="apex-container">
        <div class="apex-section-head">
            <div>
                <span class="apex-eyebrow apex-eyebrow-volt">SALON DUYURULARI & TURNUVA TAKVİMİ</span>
                <h2 id="home-news-title" class="apex-h2">GÜNCEL DUYURULAR</h2>
            </div>
            <a href="<?php echo esc_url( home_url( '/duyurular/' ) ); ?>" class="apex-btn apex-btn-outline">TÜM DUYURULAR →</a>
        </div>

        <div class="apex-grid-3">
            <?php foreach ( $anns as $ann ) :
                $cat  = get_post_meta( $ann->ID, '_apex_ann_category', true ) ?: 'Duyuru';
                $loc  = get_post_meta( $ann->ID, '_apex_ann_location', true );
                $dt   = get_post_meta( $ann->ID, '_apex_ann_date', true ) ?: get_the_date( 'd.m.Y', $ann->ID );
            ?>
            <article class="apex-card">
                <div class="apex-card-body">
                    <div style="display:flex;justify-content:space-between;gap:8px;align-items:center;">
                        <span class="apex-tag apex-tag-volt"><?php echo esc_html( $cat ); ?></span>
                        <span class="apex-muted" style="font-size:0.82rem;"><?php echo esc_html( $dt ); ?></span>
                    </div>
                    <h3 class="apex-h3"><?php echo esc_html( $ann->post_title ); ?></h3>
                    <p class="apex-muted" style="margin:0;font-size:0.9rem;"><?php echo esc_html( wp_trim_words( $ann->post_excerpt ?: $ann->post_content, 24 ) ); ?></p>
                    <div class="apex-card-foot">
                        <span class="apex-muted" style="font-size:0.82rem;">📍 <?php echo esc_html( $loc ?: 'APEX ARENA' ); ?></span>
                        <a href="<?php echo esc_url( get_permalink( $ann->ID ) ); ?>" style="color:var(--apex-crimson);font-weight:700;">Detay →</a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
