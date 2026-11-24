<?php
/**
 * Footer şablonu — 6 Sütunlu Kurumsal Dizin + Yasal/KVKK/Çerez + GitHub/Envato + Yüzen AI & Erişilebilirlik Bileşenleri
 *
 * @package ApexArena
 */
$settings = apex_get_settings();
?>
</main>

<!-- Pre-Footer CTA Bandı (MACFit esintili yüksek etki) -->
<section class="apex-prefooter" aria-labelledby="prefooter-heading">
    <div class="apex-container apex-prefooter-inner">
        <div>
            <span class="apex-eyebrow">HEDEFİNİ ERTELEME · İLK ADIMI BUGÜN AT</span>
            <h2 id="prefooter-heading" class="apex-h2">ARENADA YERİNİ ALMA ZAMANI!</h2>
            <p class="apex-lead">Ücretsiz tesis turu, InBody vücut analizi veya profesyonel maç salonu rezervasyonun tek tık uzağında.</p>
        </div>
        <div class="apex-prefooter-btns">
            <a href="<?php echo esc_url( home_url( '/uyelik/#paketler' ) ); ?>" class="apex-btn apex-btn-primary apex-btn-lg">HEMEN ÜYE OL</a>
            <a href="<?php echo esc_url( home_url( '/salon-kiralama/' ) ); ?>" class="apex-btn apex-btn-outline apex-btn-lg">SALON KİRALA</a>
        </div>
    </div>
</section>

<footer class="apex-footer" role="contentinfo">
    <div class="apex-container">
        <div class="apex-footer-grid">
            <div class="apex-footer-col apex-footer-brand-col">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="apex-brand">
                    <span class="apex-brand-mark" aria-hidden="true">▲</span>
                    <span class="apex-brand-text"><span class="apex-brand-accent">APEX</span> ARENA</span>
                </a>
                <p class="apex-footer-desc">2 FIBA Basketbol Salonu, CEV Voleybol Sahası, lisanslı hakem hizmeti, sertifikalı PT kadrosu, 5 yıldızlı tesis olanakları ve 7/24 Hibrit AI Spor Koçu ile yeni nesil spor kompleksi.</p>
                <div class="apex-footer-badges">
                    <span class="apex-mini-badge">WCAG 2.2 AA</span>
                    <span class="apex-mini-badge">KVKK UYUMLU</span>
                    <span class="apex-mini-badge">ENVATO READY</span>
                </div>
            </div>

            <div class="apex-footer-col">
                <h3 class="apex-footer-title">PT & ANTRENMAN</h3>
                <ul class="apex-footer-links">
                    <li><a href="<?php echo esc_url( home_url( '/pt-egitmenler/#bireysel' ) ); ?>">Bireysel PT Antrenman</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/pt-egitmenler/#grupsal' ) ); ?>">Grupsal Antrenmanlar</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/pt-egitmenler/#paketler' ) ); ?>">PT Paketler & Seans Ücretleri</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/pt-egitmenler/#program' ) ); ?>">Haftalık Eğitim Günleri</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/antrenorler/' ) ); ?>">Antrenörler & Online Takvim</a></li>
                </ul>
            </div>

            <div class="apex-footer-col">
                <h3 class="apex-footer-title">SALON & TESİS</h3>
                <ul class="apex-footer-links">
                    <li><a href="<?php echo esc_url( home_url( '/salon-kiralama/' ) ); ?>">Basketbol Salonları (2 Adet)</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/salon-kiralama/' ) ); ?>">Voleybol Salonu (1 Adet)</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/salon-kiralama/#rezervasyon' ) ); ?>">Hakem Ayarlama Hizmeti</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/tesis-olanaklari/#soyunma' ) ); ?>">Soyunma Odası, Duş & Sauna</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/tesis-olanaklari/#otopark' ) ); ?>">Kapalı Otopark & EV Şarj</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/tesis-olanaklari/#kafe' ) ); ?>">Apex Protein Kafe Menüsü</a></li>
                </ul>
            </div>

            <div class="apex-footer-col">
                <h3 class="apex-footer-title">ÜYELİK & ARAÇLAR</h3>
                <ul class="apex-footer-links">
                    <li><a href="<?php echo esc_url( home_url( '/uyelik/#paketler' ) ); ?>">Üyelik Paketleri</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/uyelik/#giris' ) ); ?>">Üye Girişi & QR Kart</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/uyelik/#vki' ) ); ?>">VKİ & Kalori Hesaplayıcı</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/duyurular/' ) ); ?>">Salon Duyuruları</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/sss/' ) ); ?>">Sıkça Sorulan Sorular</a></li>
                </ul>
            </div>

            <div class="apex-footer-col">
                <h3 class="apex-footer-title">YASAL & KVKK</h3>
                <ul class="apex-footer-links">
                    <li><a href="<?php echo esc_url( home_url( '/cerez-politikasi/' ) ); ?>">Çerez Politikası</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/kvkk-aydinlatma-metni/' ) ); ?>">KVKK Aydınlatma Metni</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/gizlilik-politikasi/' ) ); ?>">Gizlilik Politikası</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/uyelik-sozlesmesi/' ) ); ?>">Üyelik & İptal Sözleşmesi</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/erisilebilirlik-beyani/' ) ); ?>">Erişilebilirlik Beyanı</a></li>
                    <li><button type="button" class="apex-footer-btn-link" data-open-cookie-modal>Çerez Tercihlerini Yönet</button></li>
                </ul>
            </div>

            <div class="apex-footer-col">
                <h3 class="apex-footer-title">İletişim &amp; Kaynak</h3>
                <ul class="apex-footer-links apex-footer-contact">
                    <li><?php echo esc_html( $settings['address'] ); ?></li>
                    <li><a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $settings['phone'] ) ); ?>"><?php echo esc_html( $settings['phone'] ); ?></a></li>
                    <li><a href="mailto:<?php echo esc_attr( $settings['email'] ); ?>"><?php echo esc_html( $settings['email'] ); ?></a></li>
                    <li>Hafta İçi: <?php echo esc_html( $settings['weekday_hours'] ); ?></li>
                    <li><a href="<?php echo esc_url( $settings['github_url'] ); ?>" target="_blank" rel="noopener noreferrer">GitHub Tema Deposu ↗</a></li>
                    <li><a href="<?php echo esc_url( $settings['envato_url'] ); ?>" target="_blank" rel="noopener noreferrer">Envato Market Profili ↗</a></li>
                </ul>
            </div>
        </div>

        <div class="apex-footer-bottom">
            <p>© <?php echo esc_html( date( 'Y' ) ); ?> <strong>APEX ARENA Spor ve Performans Kompleksi</strong>. Tüm hakları saklıdır. Özel WordPress Teması (v<?php echo esc_html( APEX_VERSION ); ?>).</p>
            <div class="apex-footer-bottom-links">
                <?php
                $apex_admin_target = admin_url( 'admin.php?page=apex-arena-dashboard' );
                $apex_panel_url    = current_user_can( 'manage_options' ) ? $apex_admin_target : wp_login_url( $apex_admin_target, true );
                ?>
                <a href="<?php echo esc_url( $apex_panel_url ); ?>">Yönetim Paneli</a>
                <span aria-hidden="true">·</span>
                <button type="button" class="apex-footer-btn-link" data-open-a11y>Erişilebilirlik Merkezi</button>
                <span aria-hidden="true">·</span>
                <button type="button" class="apex-footer-btn-link" data-open-cookie-modal>Çerez Ayarları</button>
            </div>
        </div>
    </div>
</footer>

<?php
// Yüzen Bileşenler: WCAG 2.2 Erişilebilirlik Merkezi, Hibrit AI Spor Koçu, Çerez Tercih Banner & Modalı
get_template_part( 'template-parts/accessibility-widget' );
get_template_part( 'template-parts/chatbot-widget' );
get_template_part( 'template-parts/cookie-banner' );
?>

<?php wp_footer(); ?>
</body>
</html>
