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

<!-- 1. APPLE-TARZI 60 FPS SCROLL İLE OYNAYAN TAM EKRAN MİMARİ TUR VİDEOSU (240 HD WebP Kare · GPU ImageBitmap) -->
<section
    id="apex-cinema-runway"
    class="apex-cinema-runway"
    aria-labelledby="hero-title"
    data-frame-base="<?php echo esc_url( APEX_URI . '/assets/video-frames/' ); ?>"
    data-frame-ver="1.4.1"
    data-frame-count="240"
    data-duration="20.0"
    data-pad="4"
>
    <div class="apex-cinema-stage">
        <canvas
            id="apex-cinema-canvas"
            class="apex-cinema-canvas"
            style="background-image:url('<?php echo esc_url( APEX_URI . '/assets/video-frames/frame_0001.webp?v=1.4.1' ); ?>');background-size:cover;background-position:center;"
            aria-hidden="true"
        ></canvas>
        <div class="apex-cinema-vignette" aria-hidden="true"></div>

        <div class="apex-cinema-layers">
            <!-- BÖLÜM 0: DIŞ MİMARİ & GİRİŞ HERO (0% - 37%) -->
            <div class="apex-cinema-chapter" data-cinema-chapter="0" data-start="0.00" data-end="0.38">
                <div class="apex-container">
                    <div class="apex-hero-content">
                        <span class="apex-live-pill">
                            <span class="apex-live-dot" aria-hidden="true"></span>
                            Canlı Yoğunluk %<?php echo esc_html( $settings['live_occupancy'] ); ?> · Otopark <?php echo esc_html( $settings['parking_free'] ); ?>/<?php echo esc_html( $settings['parking_total'] ); ?> Boş
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
                            <div class="apex-kpi-label">Gemini 2.5 AI &amp; Erişilebilirlik Merkezi</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BÖLÜM 1: GRAND MİMARİ LOBİ (37% - 56%) -->
            <div class="apex-cinema-chapter" data-cinema-chapter="1" data-start="0.37" data-end="0.57" aria-hidden="true">
                <div class="apex-container">
                    <div class="apex-cinema-story-card">
                        <span class="apex-live-pill">01 / Mimari Karşılama Lobisi</span>
                        <h2 class="apex-h1" style="margin-top:16px;">Kapıdan İçeri, Yeni Bir Standart.</h2>
                        <p class="apex-lead">Doğal mermer, sıcak lineer mimari aydınlatma ve QR dijital üyelik geçişiyle saniyeler içinde antrenmana başla.</p>
                        <div class="apex-hero-ctas">
                            <a href="<?php echo esc_url( home_url( '/uyelik/#paketler' ) ); ?>" class="apex-btn apex-btn-outline">Üyelik Avantajlarını Gör →</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BÖLÜM 2: 180 ARAÇLIK AKILLI KAPALI OTOPARK & EV ŞARJ (56% - 70%) -->
            <div class="apex-cinema-chapter" data-cinema-chapter="2" data-start="0.56" data-end="0.70" aria-hidden="true">
                <div class="apex-container">
                    <div class="apex-cinema-story-card">
                        <span class="apex-live-pill">02 / 180 Araçlık Akıllı Kapalı Otopark &amp; 6 EV Şarj</span>
                        <h2 class="apex-h1" style="margin-top:16px;">Sıfır Bekleme, Doğrudan Giriş.</h2>
                        <p class="apex-lead">Plaka tanımalı otomatik bariyerler, üyelere ilk 3 saat ücretsiz park ayrıcalığı ve elektrikli araç hızlı şarj istasyonları.</p>
                        <div class="apex-hero-ctas">
                            <a href="<?php echo esc_url( home_url( '/tesis-olanaklari/' ) ); ?>" class="apex-btn apex-btn-primary">Tesis Olanaklarını İncele</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BÖLÜM 3: RFID SOYUNMA ODALARI, YAĞMUR DUŞ & FİN SAUNASI (70% - 85%) -->
            <div class="apex-cinema-chapter" data-cinema-chapter="3" data-start="0.69" data-end="0.85" aria-hidden="true">
                <div class="apex-container">
                    <div class="apex-cinema-story-card">
                        <span class="apex-live-pill">03 / 240 RFID Dolap · Yağmur Duş · Fin Saunası</span>
                        <h2 class="apex-h1" style="margin-top:16px;">Otel Konforunda Toparlanma.</h2>
                        <p class="apex-lead">Dijital akıllı dolaplar, özel yağmur duş kabinleri ve sedir ağacı Fin saunasıyla yoğun antrenman sonrası tam yenilenme.</p>
                        <div class="apex-hero-ctas">
                            <a href="<?php echo esc_url( home_url( '/pt-egitmenler/' ) ); ?>" class="apex-btn apex-btn-outline">PT Programlarını İncele →</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BÖLÜM 4: APEX ARENA PROTEİN KAFE & LOUNGE (85% - 100%) -->
            <div class="apex-cinema-chapter" data-cinema-chapter="4" data-start="0.84" data-end="1.00" aria-hidden="true">
                <div class="apex-container">
                    <div class="apex-cinema-story-card">
                        <span class="apex-live-pill">04 / Apex Arena Protein Kafe &amp; Lounge</span>
                        <h2 class="apex-h1" style="margin-top:16px;">Beslenme ve Sosyal Buluşma.</h2>
                        <p class="apex-lead">Makro hesaplı protein shake’ler, taze fit bowl menüleri ve 3. dalga kahve barıyla günün her saatinde enerji dolu mola.</p>
                        <div class="apex-hero-ctas">
                            <a href="<?php echo esc_url( home_url( '/salon-kiralama/#rezervasyon' ) ); ?>" class="apex-btn apex-btn-primary">Salon Kirala &amp; Başla</a>
                            <a href="<?php echo esc_url( home_url( '/tesis-olanaklari/#kafe' ) ); ?>" class="apex-btn apex-btn-outline">Kafe Menüsü</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. 3D PERSPEKTİF MONOLİT & 3D DUMBBELL SAHNESİ (WebGL 60 FPS) -->
<section id="apex-3d-runway" class="apex-3d-runway" aria-labelledby="motion-3d-title">
    <div class="apex-3d-sticky">
        <div class="apex-3d-monolith" data-3d-monolith>
            <!-- Katman 1: Dev Kinetik Tipografi (3D Heykelin Arkasında) -->
            <div class="apex-3d-typo-wrap">
                <h2 id="motion-3d-title" class="apex-3d-headline">
                    <span class="apex-3d-line" data-3d-line="top">HAREKETİN</span>
                    <span class="apex-3d-line" data-3d-line="bottom">YENİ MERKEZİ</span>
                </h2>
            </div>

            <!-- Katman 2: Canlı Three.js WebGL 3D Cast-Iron Dumbbell (Harflerin Önünde!) -->
            <canvas class="apex-3d-canvas" data-3d-canvas aria-hidden="true"></canvas>

            <!-- Katman 3: Sağ Alt Köşe Manifesto -->
            <div class="apex-3d-bottom-bar">
                <p class="apex-3d-manifesto">
                    Her hedef için yeni bir başlangıç,<br>her hareket için yeni bir güç.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- 3. SALON VE KİRALAMA — MİMARİ EDİTORYAL SALON GALERİSİ (v2.2.4) -->
<section class="apex-section apex-courts-section" aria-labelledby="home-courts-title">
    <div class="apex-container">
        <div class="apex-section-head">
            <div>
                <span class="apex-eyebrow">Salon ve Kiralama · FIBA &amp; CEV</span>
                <h2 id="home-courts-title" class="apex-h2">Profesyonel Maç Salonları</h2>
                <p class="apex-lead">FIBA standartlarında 2 basketbol ve CEV standartlarında 1 voleybol salonumuzu canlı saat seçimi ve lisanslı hakem opsiyonuyla rezerve edin.</p>
            </div>
            <a href="<?php echo esc_url( home_url( '/salon-kiralama/' ) ); ?>" class="apex-editorial-all-link">
                <span>Tüm Salonlar</span>
                <span class="apex-editorial-arrow" aria-hidden="true">↗</span>
            </a>
        </div>

        <div class="apex-court-gallery">
            <?php foreach ( $courts as $idx => $court ) :
                $sport = get_post_meta( $court->ID, '_apex_sport', true );
                $dim   = get_post_meta( $court->ID, '_apex_dimensions', true );
                $surf  = get_post_meta( $court->ID, '_apex_surface', true );
                $rate  = (int) get_post_meta( $court->ID, '_apex_hourly_rate', true );
                $ref   = (int) get_post_meta( $court->ID, '_apex_referee_fee', true );
                $photo = apex_get_post_photo( $court->ID, 'court-basketball-1.jpg' );
                $num   = str_pad( (string) ( $idx + 1 ), 2, '0', STR_PAD_LEFT );
            ?>
            <a href="<?php echo esc_url( home_url( '/salon-kiralama/?court=' . $court->ID . '#rezervasyon' ) ); ?>" class="apex-court-poster">
                <div class="apex-court-media">
                    <img src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( $court->post_title . ' - ' . $surf ); ?>" loading="lazy">
                    <span class="apex-court-scrim" aria-hidden="true"></span>
                </div>

                <div class="apex-court-top">
                    <span class="apex-coach-idx"><?php echo esc_html( $num ); ?></span>
                    <span class="apex-coach-stat"><?php echo esc_html( strtoupper( $sport ) ); ?> <i>/</i> <?php echo esc_html( $dim ); ?></span>
                </div>

                <div class="apex-court-body">
                    <span class="apex-coach-rule" aria-hidden="true"></span>
                    <span class="apex-coach-role"><?php echo esc_html( $surf ); ?> · HAKEM +<?php echo esc_html( apex_format_tl( $ref ) ); ?></span>
                    <h3 class="apex-court-title"><?php echo esc_html( $court->post_title ); ?></h3>
                    <p class="apex-court-desc"><?php echo esc_html( wp_trim_words( $court->post_excerpt ?: $court->post_content, 18 ) ); ?></p>
                    <div class="apex-court-foot">
                        <span class="apex-court-rate"><?php echo esc_html( apex_format_tl( $rate ) ); ?> <em>/ saat</em></span>
                        <span class="apex-court-action">
                            <span>Rezerve Et</span>
                            <span class="apex-coach-link-arrow" aria-hidden="true">↗</span>
                        </span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 3. PT EĞİTMENLER & PAKETLER — MİMARİ EDİTORYAL PROGRAM TABLOSU (v2.2.3) -->
<section class="apex-section apex-section-alt apex-pt-section" aria-labelledby="home-pt-title">
    <div class="apex-container">
        <div class="apex-section-head apex-pt-head">
            <div>
                <span class="apex-eyebrow">PT Eğitmenler · Bireysel &amp; Grupsal</span>
                <h2 id="home-pt-title" class="apex-h2">PT Paketleri ve Seans Ücretleri</h2>
                <p class="apex-lead">Hedefine uygun birebir özel antrenman veya butik grup dersini seç, eğitim günlerini incele ve anında online randevu oluştur.</p>
            </div>
            <div class="apex-pt-filter-bar" role="tablist" aria-label="PT paket filtresi">
                <button type="button" class="apex-pt-tab is-active" data-filter-group="home-pt" data-filter="all" role="tab" aria-selected="true">Tümü</button>
                <button type="button" class="apex-pt-tab" data-filter-group="home-pt" data-filter="bireysel" role="tab" aria-selected="false">Bireysel</button>
                <button type="button" class="apex-pt-tab" data-filter-group="home-pt" data-filter="grupsal" role="tab" aria-selected="false">Grupsal</button>
            </div>
        </div>

        <div class="apex-pt-ledger" data-filter-container="home-pt">
            <?php foreach ( $packages as $idx => $pkg ) :
                $type  = get_post_meta( $pkg->ID, '_apex_training_type', true ) ?: 'bireysel';
                $sess  = get_post_meta( $pkg->ID, '_apex_sessions', true );
                $per   = (int) get_post_meta( $pkg->ID, '_apex_per_session_fee', true );
                $total = (int) get_post_meta( $pkg->ID, '_apex_total_price', true );
                $days  = get_post_meta( $pkg->ID, '_apex_days', true );
                $scope = array_filter( array_map( 'trim', explode( "\n", (string) get_post_meta( $pkg->ID, '_apex_scope', true ) ) ) );
                $feat  = get_post_meta( $pkg->ID, '_apex_featured', true ) === '1';
                $num   = str_pad( (string) ( $idx + 1 ), 2, '0', STR_PAD_LEFT );
            ?>
            <article class="apex-pt-cell <?php echo $feat ? 'is-featured' : ''; ?>" data-filter-item="<?php echo esc_attr( $type ); ?>">
                <div class="apex-pt-cell-top">
                    <span class="apex-pt-num"><?php echo esc_html( $num ); ?></span>
                    <span class="apex-pt-meta">
                        <?php echo $type === 'grupsal' ? 'GRUPSAL' : 'BİREYSEL PT'; ?> <i>/</i> <?php echo esc_html( $sess ); ?> SEANS
                    </span>
                    <?php if ( $feat ) : ?>
                        <span class="apex-pt-pick"><i aria-hidden="true"></i>Önerilen</span>
                    <?php endif; ?>
                </div>

                <div class="apex-pt-cell-head">
                    <span class="apex-pt-rule" aria-hidden="true"></span>
                    <h3 class="apex-pt-title"><?php echo esc_html( $pkg->post_title ); ?></h3>
                </div>

                <div class="apex-pt-price-row">
                    <span class="apex-pt-total"><?php echo esc_html( apex_format_tl( $total ) ); ?></span>
                    <span class="apex-pt-unit"><?php echo esc_html( apex_format_tl( $per ) ); ?> <em>/ seans</em></span>
                </div>

                <p class="apex-pt-days"><?php echo esc_html( $days ); ?></p>

                <ul class="apex-pt-list">
                    <?php foreach ( array_slice( $scope, 0, 4 ) as $line ) : ?>
                        <li><?php echo esc_html( $line ); ?></li>
                    <?php endforeach; ?>
                </ul>

                <a href="<?php echo esc_url( home_url( '/pt-egitmenler/?package=' . $pkg->ID . '#randevu' ) ); ?>" class="apex-pt-cta">
                    <span>Randevu Oluştur</span>
                    <span class="apex-pt-cta-arrow" aria-hidden="true">↗</span>
                </a>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 4. ANTRENÖRLER — EDİTORYAL PORTRE GALERİSİ (v2.2.1) -->
<section class="apex-section apex-coaches-section" aria-labelledby="home-trainers-title">
    <div class="apex-container">
        <div class="apex-section-head apex-coaches-head">
            <div>
                <span class="apex-eyebrow">Uzman Kadro · Sertifika &amp; Online Takvim</span>
                <h2 id="home-trainers-title" class="apex-h2">Antrenörlerimizle Tanış</h2>
                <p class="apex-lead">Her eğitmenimizin özgeçmişini, uluslararası sertifikalarını inceleyin ve canlı online takviminden doğrudan randevu alın.</p>
            </div>
            <a href="<?php echo esc_url( home_url( '/antrenorler/' ) ); ?>" class="apex-editorial-all-link">
                <span>Tüm Kadroyu Gör</span>
                <span class="apex-editorial-arrow" aria-hidden="true">↗</span>
            </a>
        </div>

        <div class="apex-coach-gallery">
            <?php foreach ( $trainers as $idx => $tr ) :
                $role  = get_post_meta( $tr->ID, '_apex_role', true );
                $spec  = get_post_meta( $tr->ID, '_apex_specialty', true );
                $exp   = get_post_meta( $tr->ID, '_apex_experience', true );
                $rate  = get_post_meta( $tr->ID, '_apex_rating', true ) ?: '4.9';
                $photo = apex_get_post_photo( $tr->ID, 'trainer-kerem.jpg' );
                $num   = str_pad( (string) ( $idx + 1 ), 2, '0', STR_PAD_LEFT );
            ?>
            <a href="<?php echo esc_url( get_permalink( $tr->ID ) ); ?>" class="apex-coach-poster">
                <div class="apex-coach-poster-media">
                    <img src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( $tr->post_title . ' - ' . $role ); ?>" loading="lazy">
                    <span class="apex-coach-poster-scrim" aria-hidden="true"></span>
                </div>

                <div class="apex-coach-poster-top">
                    <span class="apex-coach-idx"><?php echo esc_html( $num ); ?></span>
                    <span class="apex-coach-stat"><?php echo esc_html( $exp ); ?> YIL <i>/</i> <?php echo esc_html( $rate ); ?></span>
                </div>

                <div class="apex-coach-poster-body">
                    <span class="apex-coach-rule" aria-hidden="true"></span>
                    <span class="apex-coach-role"><?php echo esc_html( $role ); ?></span>
                    <h3 class="apex-coach-name"><?php echo esc_html( $tr->post_title ); ?></h3>
                    <p class="apex-coach-spec"><?php echo esc_html( $spec ); ?></p>
                    <span class="apex-coach-link">
                        <span>Profil &amp; Takvim</span>
                        <span class="apex-coach-link-arrow" aria-hidden="true">↗</span>
                    </span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 5. TESİS OLANAKLARI — MİMARİ EDİTORYAL BENTO GALERİSİ (v2.2.2) -->
<section class="apex-section apex-section-alt apex-fac-section" aria-labelledby="home-fac-title">
    <div class="apex-container">
        <div class="apex-section-head">
            <div>
                <span class="apex-eyebrow">Tesis Olanakları · Mimari Konfor</span>
                <h2 id="home-fac-title" class="apex-h2">Soyunma Odası, Otopark ve Protein Kafe</h2>
            </div>
            <a href="<?php echo esc_url( home_url( '/tesis-olanaklari/' ) ); ?>" class="apex-editorial-all-link">
                <span>Olanaklar &amp; Menü</span>
                <span class="apex-editorial-arrow" aria-hidden="true">↗</span>
            </a>
        </div>

        <div class="apex-fac-bento">
            <a href="<?php echo esc_url( home_url( '/tesis-olanaklari/' ) ); ?>" class="apex-fac-tile apex-fac-tile-tall">
                <div class="apex-fac-media">
                    <img src="<?php echo esc_url( apex_img_url( 'facility-locker.jpg' ) ); ?>" alt="APEX ARENA Soyunma Odası, Yağmur Duş ve Sedir Fin Saunası" loading="lazy">
                    <span class="apex-fac-scrim" aria-hidden="true"></span>
                </div>
                <div class="apex-fac-top">
                    <span class="apex-coach-idx">01</span>
                    <span class="apex-coach-stat">240 RFID DOLAP <i>/</i> 85°C SAUNA</span>
                </div>
                <div class="apex-fac-body">
                    <span class="apex-coach-rule" aria-hidden="true"></span>
                    <span class="apex-coach-role">TOPARLANMA &amp; SPA KONFORU</span>
                    <h3 class="apex-fac-title">Soyunma Odası &amp; Fin Saunası</h3>
                    <p class="apex-fac-desc">Temassız RFID akıllı dolaplar, yüksek basınçlı yağmur duş kabinleri, sedir ağacı Fin saunası, hijyenik havlu servisi ve engelsiz erişimli özel kabinler.</p>
                    <span class="apex-coach-link">
                        <span>Tesis Detayları</span>
                        <span class="apex-coach-link-arrow" aria-hidden="true">↗</span>
                    </span>
                </div>
            </a>

            <a href="<?php echo esc_url( home_url( '/tesis-olanaklari/#otopark' ) ); ?>" class="apex-fac-tile">
                <div class="apex-fac-media">
                    <img src="<?php echo esc_url( apex_img_url( 'facility-parking.jpg' ) ); ?>" alt="APEX ARENA Kapalı Otopark ve 22 kW EV Şarj İstasyonları" loading="lazy">
                    <span class="apex-fac-scrim" aria-hidden="true"></span>
                </div>
                <div class="apex-fac-top">
                    <span class="apex-coach-idx">02</span>
                    <span class="apex-coach-stat"><?php echo esc_html( $settings['parking_free'] ); ?> BOŞ YER <i>/</i> 6× 22 KW EV</span>
                </div>
                <div class="apex-fac-body">
                    <span class="apex-coach-rule" aria-hidden="true"></span>
                    <span class="apex-coach-role">PLAKA TANIMALI · DİREKT ASANSÖR</span>
                    <h3 class="apex-fac-title">Akıllı Kapalı Otopark</h3>
                    <p class="apex-fac-desc">180 araçlık kapalı otopark, üyelere ilk 3 saat ücretsiz park ayrıcalığı ve 6 adet hızlı elektrikli araç şarj noktası.</p>
                    <span class="apex-coach-link">
                        <span>Canlı EV &amp; Doluluk</span>
                        <span class="apex-coach-link-arrow" aria-hidden="true">↗</span>
                    </span>
                </div>
            </a>

            <a href="<?php echo esc_url( home_url( '/tesis-olanaklari/#kafe' ) ); ?>" class="apex-fac-tile">
                <div class="apex-fac-media">
                    <img src="<?php echo esc_url( apex_img_url( 'facility-cafe.jpg' ) ); ?>" alt="Apex Protein Kafe ve Lounge Bar" loading="lazy">
                    <span class="apex-fac-scrim" aria-hidden="true"></span>
                </div>
                <div class="apex-fac-top">
                    <span class="apex-coach-idx">03</span>
                    <span class="apex-coach-stat">MAKRO MENÜ <i>/</i> PRO %15</span>
                </div>
                <div class="apex-fac-body">
                    <span class="apex-coach-rule" aria-hidden="true"></span>
                    <span class="apex-coach-role">BESLENME &amp; 3. DALGA KAHVE BARI</span>
                    <h3 class="apex-fac-title">Apex Protein Kafe</h3>
                    <p class="apex-fac-desc">İzole whey shake’ler, diyetisyen onaylı makro hesaplı fit bowl tabakları, cold brew bar ve antrenman çıkışı hızlı ön sipariş.</p>
                    <span class="apex-coach-link">
                        <span>Menü &amp; Ön Sipariş</span>
                        <span class="apex-coach-link-arrow" aria-hidden="true">↗</span>
                    </span>
                </div>
            </a>
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

<!-- 8. İNTERAKTİF 3D MİMARİ KESİT SPOR KOMPLEKSİ (Three.js Dollhouse Cutaway · v2.2.0) -->
<section class="apex-section" id="kat-plani" aria-labelledby="home-floorplan-title">
    <div class="apex-container">
        <div class="apex-section-head">
            <div>
                <span class="apex-eyebrow">3D MİMARİ KESİT MAKETİ · 14.500 M² · GERÇEK ZAMANLI WEBGL</span>
                <h2 id="home-floorplan-title" class="apex-h2">Kompleksi 3D Mimaride Keşfet</h2>
            </div>
            <p class="apex-muted" style="max-width:46ch;margin:0;">Farenizi 3D mimarideki herhangi bir salonun veya odanın üzerine getirin — kamera akıcı biçimde içeri yaklaşır. Tıklayarak odaya kilitlenebilir, sürükleyerek açıyı döndürebilirsiniz.</p>
        </div>

        <div class="apex-fp-layout apex-arch-layout">
            <div class="apex-arch-stage-card" id="apex-arch-3d-stage">
                <canvas class="apex-arch-canvas" data-arch-canvas aria-label="APEX ARENA 3D spor kompleksi mimari kesit maketi"></canvas>

                <!-- Üst Yüzen Liquid Glass Durum Çubuğu -->
                <div class="apex-arch-hud-top">
                    <div class="apex-arch-hud-pill" id="apex-arch-hud-pill">
                        <span class="apex-live-dot" aria-hidden="true"></span>
                        <div>
                            <strong id="apex-arch-hud-title">3D Kompleks Mimarisi · 6 Aktif Bölge</strong>
                            <span id="apex-arch-hud-hint">Mouse ile bir odanın üstüne gel — kamera 3D içine yaklaşır</span>
                        </div>
                    </div>
                    <div class="apex-arch-hud-actions">
                        <button type="button" class="apex-arch-ctrl-btn" id="apex-arch-reset-btn" hidden>← Genel Bakış</button>
                        <button type="button" class="apex-arch-ctrl-btn" id="apex-arch-orbit-btn" title="Otomatik 3D turu aç/kapat">⟳ 3D Tur</button>
                    </div>
                </div>

                <!-- Alt Hızlı Oda Seçici Çipler (Hover ile anında içeri yaklaşır · Mobil uyumlu) -->
                <div class="apex-arch-chips" role="tablist" aria-label="3D spor kompleksi alanları">
                    <button type="button" class="apex-arch-chip" data-arch-chip="basket1">🏀 Basketbol 1</button>
                    <button type="button" class="apex-arch-chip" data-arch-chip="lobby">🌀 Lobi &amp; EV</button>
                    <button type="button" class="apex-arch-chip" data-arch-chip="voleybol">🏐 Voleybol Pro</button>
                    <button type="button" class="apex-arch-chip" data-arch-chip="pt">🏋️ PT &amp; Kuvvet</button>
                    <button type="button" class="apex-arch-chip" data-arch-chip="spa">🔥 Sauna &amp; Duş</button>
                    <button type="button" class="apex-arch-chip" data-arch-chip="cafe">🥤 Protein Kafe</button>
                </div>
            </div>

            <aside class="apex-panel apex-fp-detail" id="apex-fp-detail" aria-live="polite">
                <span class="apex-eyebrow" id="fp-d-tag">FIBA STANDARTI · KUZEYBATI KANAT</span>
                <h3 class="apex-h2" id="fp-d-title" style="margin:6px 0 10px;">Arena Basketbol 1 (FIBA Parke)</h3>
                <p class="apex-muted" id="fp-d-desc" style="margin:0 0 18px;">28×15m uluslararası FIBA ölçülerinde esnek akçaağaç parke, çift cam panyalı profesyonel pota, LED skorboard ve isteğe bağlı lisanslı TBF hakem hizmeti.</p>
                <div class="apex-fp-specs" id="fp-d-specs">
                    <span>🏀 28×15m FIBA Parke</span>
                    <span>⚖️ Lisanslı Hakem (+₺450)</span>
                    <span>📺 4 Yönlü LED Skorboard</span>
                </div>
                <a href="<?php echo esc_url( home_url( '/salon-kiralama/#rezervasyon' ) ); ?>" id="fp-d-cta" class="apex-btn apex-btn-primary apex-btn-block" style="margin-top:20px;">Basketbol Salonunu Kirala →</a>
            </aside>
        </div>
    </div>
</section>

<!-- 9. ÜYE PUANLAMA & CANLI DEĞERLENDİRMELER (v2.0.0) -->
<?php
global $wpdb;
$apex_reviews = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}apex_reviews WHERE status = 'published' ORDER BY id DESC LIMIT 6" );
$apex_rev_avg = round( (float) $wpdb->get_var( "SELECT COALESCE(AVG(rating), 4.9) FROM {$wpdb->prefix}apex_reviews WHERE status = 'published'" ), 1 );
$apex_rev_cnt = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}apex_reviews WHERE status = 'published'" );
?>
<section class="apex-section apex-section-alt" id="yorumlar" aria-labelledby="home-reviews-title">
    <div class="apex-container">
        <div class="apex-section-head">
            <div>
                <span class="apex-eyebrow">DOĞRULANMIŞ ÜYE DENEYİMLERİ</span>
                <h2 id="home-reviews-title" class="apex-h2">Üyelerimiz Ne Diyor?</h2>
            </div>
            <div class="apex-rev-summary">
                <strong class="apex-rev-score" id="apex-rev-avg"><?php echo esc_html( number_format( $apex_rev_avg, 1 ) ); ?></strong>
                <div>
                    <div class="apex-stars" aria-hidden="true">★★★★★</div>
                    <small class="apex-muted"><span id="apex-rev-count"><?php echo esc_html( $apex_rev_cnt ); ?></span> üye değerlendirmesi</small>
                </div>
            </div>
        </div>

        <div class="apex-grid-3" id="apex-reviews-grid">
            <?php foreach ( $apex_reviews as $rv ) : ?>
            <article class="apex-card apex-rev-card">
                <div class="apex-card-body">
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;">
                        <span class="apex-stars" aria-label="<?php echo esc_attr( $rv->rating ); ?> üzerinden 5 yıldız"><?php echo str_repeat( '★', (int) $rv->rating ) . str_repeat( '☆', max( 0, 5 - (int) $rv->rating ) ); ?></span>
                        <span class="apex-tag"><?php echo esc_html( $rv->member_plan ); ?></span>
                    </div>
                    <p style="margin:6px 0 0;font-size:0.92rem;color:var(--apex-text);">“<?php echo esc_html( $rv->comment ); ?>”</p>
                    <div class="apex-card-foot">
                        <strong><?php echo esc_html( $rv->author_name ); ?></strong>
                        <span class="apex-muted" style="font-size:0.78rem;"><?php echo esc_html( date( 'd.m.Y', strtotime( $rv->created_at ) ) ); ?></span>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <!-- Canlı 5 Yıldızlı Yorum Ekleme Formu -->
        <div class="apex-panel" style="margin-top:28px;">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:16px;">
                <div>
                    <h3 class="apex-h3" style="margin:0;">Deneyimini Puanla</h3>
                    <p class="apex-muted" style="margin:4px 0 0;font-size:0.86rem;">Salonlar, PT koçluğu, kafe veya tesis olanakları hakkındaki görüşün anında yayınlanır.</p>
                </div>
                <div class="apex-star-picker" id="apex-star-picker" role="radiogroup" aria-label="Puanınız">
                    <?php for ( $st = 1; $st <= 5; $st++ ) : ?>
                        <button type="button" class="apex-star-btn is-active" data-star-val="<?php echo esc_attr( $st ); ?>" aria-label="<?php echo esc_attr( $st ); ?> yıldız">★</button>
                    <?php endfor; ?>
                </div>
            </div>
            <form id="apex-review-form" class="apex-form-grid" novalidate>
                <input type="hidden" id="rev-rating" name="rating" value="5">
                <div class="apex-field">
                    <label for="rev-name">Ad Soyad *</label>
                    <input type="text" id="rev-name" name="author_name" placeholder="Örn. Selin Demir" required>
                </div>
                <div class="apex-field">
                    <label for="rev-plan">Üyelik / Hizmet</label>
                    <select id="rev-plan" name="member_plan">
                        <option value="APEX PRO">APEX PRO Üyesi</option>
                        <option value="APEX ELITE + PT">APEX ELITE + PT</option>
                        <option value="APEX CLASSIC">APEX CLASSIC</option>
                        <option value="Salon Misafiri">Salon Kiralama Misafiri</option>
                    </select>
                </div>
                <div class="apex-field apex-field-full">
                    <label for="rev-comment">Yorumunuz *</label>
                    <textarea id="rev-comment" name="comment" rows="2" placeholder="APEX ARENA deneyiminizi birkaç cümleyle paylaşın..." required></textarea>
                </div>
                <div class="apex-field apex-field-full" style="flex-direction:row;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                    <button type="submit" class="apex-btn apex-btn-primary">Değerlendirmeyi Yayınla</button>
                    <div id="rev-result" role="status" aria-live="polite"></div>
                </div>
            </form>
        </div>
    </div>
</section>

<?php get_footer(); ?>
