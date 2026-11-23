<?php
/**
 * Ana Sayfa Şablonu — Apple HIG & Liquid Glass Estetiği
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
$hero_title = get_theme_mod( 'apex_hero_title', 'Sınırlarını Arenada Aş.' );
$hero_sub   = get_theme_mod( 'apex_hero_subtitle', 'İki FIBA basketbol salonu, profesyonel voleybol sahası, lisanslı hakem hizmeti, sertifikalı PT kadrosu ve 7/24 AI Spor Koçu tek çatı altında.' );
?>

<!-- 1. APPLE-TARZI SCROLL İLE OYNAYAN TAM EKRAN SİNEMATİK VİDEO HERO (119 HD WebP Kare · 60 FPS) -->
<section
    id="apex-cinema-runway"
    class="apex-cinema-runway"
    aria-labelledby="hero-title"
    data-frame-base="<?php echo esc_url( APEX_URI . '/assets/video-frames/' ); ?>"
    data-frame-count="119"
    data-duration="7.9"
    data-pad="4"
>
    <div class="apex-cinema-stage">
        <canvas
            id="apex-cinema-canvas"
            class="apex-cinema-canvas"
            style="background-image:url('<?php echo esc_url( APEX_URI . '/assets/video-frames/frame_0001.webp' ); ?>');background-size:cover;background-position:center;"
            aria-hidden="true"
        ></canvas>
        <div class="apex-cinema-vignette" aria-hidden="true"></div>

        <div class="apex-cinema-layers">
            <!-- BÖLÜM 0: GİRİŞ HERO & CANLI TELEMETRİ -->
            <div class="apex-cinema-chapter" data-cinema-chapter="0" data-start="0.00" data-end="0.25">
                <div class="apex-container">
                    <div class="apex-hero-content">
                        <span class="apex-live-pill">
                            <span class="apex-live-dot" aria-hidden="true"></span>
                            Canlı Yoğunluk %<?php echo esc_html( $settings['live_occupancy'] ); ?> · Otopark <?php echo esc_html( $settings['parking_free'] ); ?>/<?php echo esc_html( $settings['parking_total'] ); ?> Boş · Aşağı Kaydırarak Videoyu Oynat
                        </span>
                        <h1 id="hero-title" class="apex-h1" style="margin-top:18px;">
                            <?php echo esc_html( $hero_title ); ?>
                        </h1>
                        <p class="apex-lead"><?php echo esc_html( $hero_sub ); ?></p>

                        <div class="apex-hero-ctas">
                            <a href="<?php echo esc_url( home_url( '/uyelik/#paketler' ) ); ?>" class="apex-btn apex-btn-primary apex-btn-lg">Üyeliği Başlat</a>
                            <a href="<?php echo esc_url( home_url( '/salon-kiralama/' ) ); ?>" class="apex-btn apex-btn-outline apex-btn-lg">Salon Kirala</a>
                            <button type="button" class="apex-btn apex-btn-outline apex-btn-lg" data-open-ai>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 3a9 9 0 0 0-9 9c0 1.66.45 3.22 1.24 4.56L3 21l4.44-1.24A8.96 8.96 0 0 0 12 21a9 9 0 1 0 0-18Z"/></svg>
                                AI Spor Koçu
                            </button>
                        </div>
                    </div>

                    <div class="apex-kpi-grid apex-cinema-kpi-compact" aria-label="Kompleks öne çıkan verileri">
                        <div class="apex-kpi-card">
                            <div class="apex-kpi-val">3 Salon</div>
                            <div class="apex-kpi-label">2 Basketbol · 1 Voleybol · Hakem Hizmeti</div>
                        </div>
                        <div class="apex-kpi-card">
                            <div class="apex-kpi-val">Sertifikalı PT</div>
                            <div class="apex-kpi-label">Bireysel &amp; Grupsal · Canlı Takvim</div>
                        </div>
                        <div class="apex-kpi-card">
                            <div class="apex-kpi-val">180 Araç</div>
                            <div class="apex-kpi-label">Kapalı Otopark, 6 EV Şarj &amp; Protein Kafe</div>
                        </div>
                        <div class="apex-kpi-card">
                            <div class="apex-kpi-val">7/24 AI + WCAG</div>
                            <div class="apex-kpi-label">Hibrit Asistan &amp; Erişilebilirlik Merkezi</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BÖLÜM 1: PERFORMANS & KONDİSYON ALANI -->
            <div class="apex-cinema-chapter" data-cinema-chapter="1" data-start="0.22" data-end="0.49" aria-hidden="true">
                <div class="apex-container">
                    <div class="apex-cinema-story-card">
                        <span class="apex-live-pill">01 / Performans &amp; Kondisyon Katı</span>
                        <h2 class="apex-h1" style="margin-top:16px;">Her Adımda Maksimum Güç.</h2>
                        <p class="apex-lead">Olimpik kaldırma platformları, fonksiyonel hız kulvarı ve akustik zemin kaplamasıyla kesintisiz performans odaklı antrenman deneyimi.</p>
                        <div class="apex-hero-ctas">
                            <a href="<?php echo esc_url( home_url( '/tesis-olanaklari/' ) ); ?>" class="apex-btn apex-btn-outline">Tesis Olanaklarını Keşfet →</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BÖLÜM 2: FIBA BASKETBOL & VOLEYBOL SALONLARI -->
            <div class="apex-cinema-chapter" data-cinema-chapter="2" data-start="0.46" data-end="0.78" aria-hidden="true">
                <div class="apex-container">
                    <div class="apex-cinema-story-card">
                        <span class="apex-live-pill">02 / 2 FIBA Basketbol · 1 CEV Voleybol</span>
                        <h2 class="apex-h1" style="margin-top:16px;">Maç Atmosferi, Anında Rezervasyon.</h2>
                        <p class="apex-lead">Şok emici parke zemin, LED skorbord ve tek tıkla eklenebilen lisanslı hakem ayarlama hizmetiyle turnuva standardında salon kiralama.</p>
                        <div class="apex-hero-ctas">
                            <a href="<?php echo esc_url( home_url( '/salon-kiralama/#rezervasyon' ) ); ?>" class="apex-btn apex-btn-primary">Canlı Saat Seç &amp; Kirala</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BÖLÜM 3: BİREBİR PT & HİBRİT AI KOÇ -->
            <div class="apex-cinema-chapter" data-cinema-chapter="3" data-start="0.75" data-end="1.00" aria-hidden="true">
                <div class="apex-container">
                    <div class="apex-cinema-story-card">
                        <span class="apex-live-pill">03 / Bireysel &amp; Grupsal PT · Online Takvim</span>
                        <h2 class="apex-h1" style="margin-top:16px;">Hedefine Özel Koçluk.</h2>
                        <p class="apex-lead">TBF/TVF/NASM sertifikalı antrenör seçimi, şeffaf seans paketleri ve canlı takvim üzerinden saniyeler içinde onaylanan PT randevuları.</p>
                        <div class="apex-hero-ctas">
                            <a href="<?php echo esc_url( home_url( '/pt-egitmenler/' ) ); ?>" class="apex-btn apex-btn-primary">PT Paketlerini İncele</a>
                            <a href="<?php echo esc_url( home_url( '/antrenorler/' ) ); ?>" class="apex-btn apex-btn-outline">Antrenör Kadrosu</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Apple Clear Liquid Glass Video Scrubbing HUD -->
        <div class="apex-cinema-hud-wrap">
            <div class="apex-cinema-hud" role="region" aria-label="Kaydırma tabanlı video kontrol çubuğu">
                <button type="button" class="apex-cinema-play-btn" data-cinema-autoplay aria-pressed="false">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="6,4 20,12 6,20"/></svg>
                    <span data-play-label>Oynat</span>
                </button>

                <div class="apex-cinema-track-wrap">
                    <div class="apex-cinema-track-meta">
                        <span data-cinema-status>Scroll ile Oynat</span>
                        <span data-cinema-time>00:00.0 / 00:07.9</span>
                        <span data-cinema-pct>0%</span>
                    </div>
                    <div class="apex-cinema-track" aria-hidden="true">
                        <div class="apex-cinema-track-fill" data-cinema-fill></div>
                    </div>
                </div>

                <div class="apex-cinema-dots" aria-label="Video sahneleri">
                    <button type="button" class="apex-cinema-dot is-active" data-cinema-jump="0.00" aria-label="Sahne 1: Giriş">01</button>
                    <button type="button" class="apex-cinema-dot" data-cinema-jump="0.33" aria-label="Sahne 2: Performans">02</button>
                    <button type="button" class="apex-cinema-dot" data-cinema-jump="0.62" aria-label="Sahne 3: Basketbol Salonu">03</button>
                    <button type="button" class="apex-cinema-dot" data-cinema-jump="0.90" aria-label="Sahne 4: PT Eğitimi">04</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. SALON VE KİRALAMA -->
<section class="apex-section" aria-labelledby="home-courts-title">
    <div class="apex-container">
        <div class="apex-section-head">
            <div>
                <span class="apex-eyebrow">Salon ve Kiralama</span>
                <h2 id="home-courts-title" class="apex-h2">Profesyonel Maç Salonları</h2>
                <p class="apex-lead">FIBA standartlarında 2 basketbol ve CEV standartlarında 1 voleybol salonumuzu canlı saat seçimi ve lisanslı hakem opsiyonuyla rezerve edin.</p>
            </div>
            <a href="<?php echo esc_url( home_url( '/salon-kiralama/' ) ); ?>" class="apex-btn apex-btn-outline apex-btn-sm">Tüm Salonlar →</a>
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
                    <span class="apex-card-badge"><?php echo esc_html( ucfirst( $sport ) . ' · ' . $dim ); ?></span>
                </div>
                <div class="apex-card-body">
                    <h3 class="apex-h3"><?php echo esc_html( $court->post_title ); ?></h3>
                    <p class="apex-muted" style="margin:0;font-size:0.9rem;"><?php echo esc_html( wp_trim_words( $court->post_excerpt ?: $court->post_content, 19 ) ); ?></p>
                    <div class="apex-tags">
                        <span class="apex-tag"><?php echo esc_html( $surf ); ?></span>
                        <span class="apex-tag apex-tag-volt">Hakem Opsiyonu +<?php echo esc_html( apex_format_tl( $ref ) ); ?></span>
                    </div>
                    <div class="apex-card-foot">
                        <div class="apex-price"><?php echo esc_html( apex_format_tl( $rate ) ); ?> <small>/ saat</small></div>
                        <a href="<?php echo esc_url( home_url( '/salon-kiralama/?court=' . $court->ID . '#rezervasyon' ) ); ?>" class="apex-btn apex-btn-outline apex-btn-sm">Rezerve Et</a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 3. PT EĞİTMENLER & PAKETLER -->
<section class="apex-section apex-section-alt" aria-labelledby="home-pt-title">
    <div class="apex-container">
        <div class="apex-section-head">
            <div>
                <span class="apex-eyebrow">PT Eğitmenler · Bireysel &amp; Grupsal</span>
                <h2 id="home-pt-title" class="apex-h2">PT Paketleri ve Seans Ücretleri</h2>
                <p class="apex-lead">Hedefine uygun birebir özel antrenman veya butik grup dersini seç, eğitim günlerini incele ve anında online randevu oluştur.</p>
            </div>
            <div class="apex-tabs" role="tablist" aria-label="PT paket filtresi">
                <button type="button" class="apex-tab-btn is-active" data-filter-group="home-pt" data-filter="all" role="tab" aria-selected="true">Tümü</button>
                <button type="button" class="apex-tab-btn" data-filter-group="home-pt" data-filter="bireysel" role="tab" aria-selected="false">Bireysel</button>
                <button type="button" class="apex-tab-btn" data-filter-group="home-pt" data-filter="grupsal" role="tab" aria-selected="false">Grupsal</button>
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
                        <span class="apex-eyebrow" style="margin:0;">
                            <?php echo $type === 'grupsal' ? 'Grupsal Antrenman' : 'Bireysel PT'; ?> · <?php echo esc_html( $sess ); ?> Seans
                        </span>
                        <?php if ( $feat ) : ?><span class="apex-mini-badge" style="color:#fff;border-color:rgba(227,24,55,0.6);">Önerilen</span><?php endif; ?>
                    </div>
                    <h3 class="apex-h3"><?php echo esc_html( $pkg->post_title ); ?></h3>
                    <div class="apex-price"><?php echo esc_html( apex_format_tl( $total ) ); ?> <small>· Seans başı <?php echo esc_html( apex_format_tl( $per ) ); ?></small></div>
                    <p class="apex-muted" style="margin:0;font-size:0.86rem;">Günler: <?php echo esc_html( $days ); ?></p>
                    <ul class="apex-checklist">
                        <?php foreach ( array_slice( $scope, 0, 4 ) as $line ) : ?>
                            <li><?php echo esc_html( $line ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="apex-card-foot">
                        <a href="<?php echo esc_url( home_url( '/pt-egitmenler/?package=' . $pkg->ID . '#randevu' ) ); ?>" class="apex-btn <?php echo $feat ? 'apex-btn-primary' : 'apex-btn-outline'; ?> apex-btn-block">Randevu Al</a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 4. ANTRENÖRLER -->
<section class="apex-section" aria-labelledby="home-trainers-title">
    <div class="apex-container">
        <div class="apex-section-head">
            <div>
                <span class="apex-eyebrow">Uzman Kadro · Sertifika &amp; Online Takvim</span>
                <h2 id="home-trainers-title" class="apex-h2">Antrenörlerimizle Tanış</h2>
                <p class="apex-lead">Her eğitmenimizin özgeçmişini, uluslararası sertifikalarını inceleyin ve canlı online takviminden doğrudan randevu alın.</p>
            </div>
            <a href="<?php echo esc_url( home_url( '/antrenorler/' ) ); ?>" class="apex-btn apex-btn-outline apex-btn-sm">Tüm Kadro →</a>
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
                    <span class="apex-card-badge">★ <?php echo esc_html( $rate ); ?> · <?php echo esc_html( $exp ); ?> Yıl</span>
                </div>
                <div class="apex-card-body">
                    <span class="apex-eyebrow" style="margin:0;font-size:0.72rem;"><?php echo esc_html( $role ); ?></span>
                    <h3 class="apex-h3"><?php echo esc_html( $tr->post_title ); ?></h3>
                    <p class="apex-muted" style="margin:0;font-size:0.86rem;"><?php echo esc_html( $spec ); ?></p>
                    <div class="apex-card-foot">
                        <a href="<?php echo esc_url( get_permalink( $tr->ID ) ); ?>" class="apex-btn apex-btn-outline apex-btn-sm apex-btn-block">Profil ve Takvim</a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 5. TESİS OLANAKLARI -->
<section class="apex-section apex-section-alt" aria-labelledby="home-fac-title">
    <div class="apex-container">
        <div class="apex-section-head">
            <div>
                <span class="apex-eyebrow">Tesis Olanakları</span>
                <h2 id="home-fac-title" class="apex-h2">Soyunma Odası, Otopark ve Protein Kafe</h2>
            </div>
            <a href="<?php echo esc_url( home_url( '/tesis-olanaklari/' ) ); ?>" class="apex-btn apex-btn-outline apex-btn-sm">Olanaklar &amp; Menü →</a>
        </div>

        <div class="apex-grid-3">
            <article class="apex-card">
                <div class="apex-card-media">
                    <img src="<?php echo esc_url( apex_img_url( 'facility-locker.jpg' ) ); ?>" alt="APEX ARENA Soyunma Odası, Yağmur Duş ve Sauna" loading="lazy">
                    <span class="apex-card-badge">240 Akıllı Dolap · Sauna</span>
                </div>
                <div class="apex-card-body">
                    <h3 class="apex-h3">Soyunma Odası &amp; Duş</h3>
                    <p class="apex-muted" style="margin:0;font-size:0.9rem;">RFID kilitli dolaplar, yüksek basınçlı yağmur duşları, Fin saunası, hijyenik havlu servisi ve engelsiz erişimli özel soyunma odası.</p>
                </div>
            </article>

            <article class="apex-card">
                <div class="apex-card-media">
                    <img src="<?php echo esc_url( apex_img_url( 'facility-parking.jpg' ) ); ?>" alt="APEX ARENA Kapalı Otopark ve EV Şarj İstasyonları" loading="lazy">
                    <span class="apex-card-badge"><?php echo esc_html( $settings['parking_free'] ); ?> Boş Yer · 6 EV Şarj</span>
                </div>
                <div class="apex-card-body">
                    <h3 class="apex-h3">Kapalı Otopark</h3>
                    <p class="apex-muted" style="margin:0;font-size:0.9rem;">180 araçlık plaka tanımalı kapalı otopark, üyelere ilk 3 saat ücretsiz kullanım, 6 adet 22 kW EV şarj istasyonu ve asansörlü direkt erişim.</p>
                </div>
            </article>

            <article class="apex-card">
                <div class="apex-card-media">
                    <img src="<?php echo esc_url( apex_img_url( 'facility-cafe.jpg' ) ); ?>" alt="Apex Protein Kafe ve Lounge Bar" loading="lazy">
                    <span class="apex-card-badge apex-card-badge-crimson">PRO Üyelere %15 İndirim</span>
                </div>
                <div class="apex-card-body">
                    <h3 class="apex-h3">Apex Protein Kafe</h3>
                    <p class="apex-muted" style="margin:0;font-size:0.9rem;">Taze izole whey shake’ler, diyetisyen onaylı makro hesaplı fit bowl tabakları, 3. dalga cold brew kahveler ve dinlenme lounge alanı.</p>
                </div>
            </article>
        </div>
    </div>
</section>

<!-- 6. ÜYELİK PAKETLERİ -->
<section class="apex-section" aria-labelledby="home-mem-title">
    <div class="apex-container">
        <div class="apex-section-head">
            <div>
                <span class="apex-eyebrow">Üyelik İşlemleri · Dijital QR Kart</span>
                <h2 id="home-mem-title" class="apex-h2">Hedefine Uygun Üyeliği Seç</h2>
            </div>
            <a href="<?php echo esc_url( home_url( '/uyelik/' ) ); ?>" class="apex-btn apex-btn-outline apex-btn-sm">Karşılaştırma &amp; Panel →</a>
        </div>

        <div class="apex-grid-3">
            <?php foreach ( $plans as $pl ) : ?>
            <article class="apex-card <?php echo $pl['featured'] ? 'apex-card-featured' : ''; ?>">
                <div class="apex-card-body">
                    <span class="apex-eyebrow"><?php echo esc_html( $pl['badge'] ); ?></span>
                    <h3 class="apex-h2" style="font-size:1.75rem;"><?php echo esc_html( $pl['name'] ); ?></h3>
                    <div class="apex-price"><?php echo esc_html( apex_format_tl( $pl['monthly_price'] ) ); ?> <small>/ ay</small></div>
                    <p class="apex-muted" style="margin:0;font-size:0.88rem;"><?php echo esc_html( $pl['summary'] ); ?></p>
                    <ul class="apex-checklist">
                        <?php foreach ( $pl['features'] as $f ) : ?>
                            <li><?php echo esc_html( $f ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="apex-card-foot">
                        <a href="<?php echo esc_url( home_url( '/uyelik/?plan=' . $pl['slug'] . '#kayit' ) ); ?>" class="apex-btn <?php echo $pl['featured'] ? 'apex-btn-primary' : 'apex-btn-outline'; ?> apex-btn-block">Hemen Başla</a>
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
                <span class="apex-eyebrow">Salon Duyuruları &amp; Turnuvalar</span>
                <h2 id="home-news-title" class="apex-h2">Güncel Duyurular</h2>
            </div>
            <a href="<?php echo esc_url( home_url( '/duyurular/' ) ); ?>" class="apex-btn apex-btn-outline apex-btn-sm">Tüm Duyurular →</a>
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
                        <span class="apex-muted" style="font-size:0.8rem;"><?php echo esc_html( $dt ); ?></span>
                    </div>
                    <h3 class="apex-h3"><?php echo esc_html( $ann->post_title ); ?></h3>
                    <p class="apex-muted" style="margin:0;font-size:0.88rem;"><?php echo esc_html( wp_trim_words( $ann->post_excerpt ?: $ann->post_content, 22 ) ); ?></p>
                    <div class="apex-card-foot">
                        <span class="apex-muted" style="font-size:0.82rem;"><?php echo esc_html( $loc ?: 'Apex Arena' ); ?></span>
                        <a href="<?php echo esc_url( get_permalink( $ann->ID ) ); ?>" style="color:var(--apex-text);font-weight:600;font-size:0.86rem;">İncele →</a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
