<?php
/**
 * Template Name: Sıkça Sorulan Sorular
 *
 * @package ApexArena
 */
get_header();
$faqs = [
    [ 'q' => 'Basketbol ve Voleybol salonlarını üye olmadan da kiralayabilir miyim?', 'a' => 'Evet! APEX ARENA üyesi olmayan takımlar ve gruplar da online rezervasyon sistemimiz üzerinden 2 adet Basketbol ve 1 adet Voleybol salonumuzu saatlik olarak kiralayabilir. APEX PRO üyelerimiz salon kiralamalarında %25 indirimden yararlanır.' ],
    [ 'q' => 'Hakem ayarlama hizmeti nasıl çalışır?', 'a' => 'Salon kiralama formunda "Lisanslı Hakem Ayarlama Hizmeti Ekle (+₺450)" kutucuğunu işaretlemeniz yeterlidir. TBF (Basketbol) veya TVF (Voleybol) klasman hakemimiz maç saatinden 20 dakika önce sahada hazır bulunur.' ],
    [ 'q' => 'Bireysel PT ile Grupsal Antrenman arasındaki fark nedir?', 'a' => 'Bireysel PT tamamen size özel saat ve programla 1-on-1 uygulanır. Grupsal antrenmanlar ise 6–8 kişilik butik kontenjanla sabit haftalık günlerde yapılır ve seans başı maliyeti çok daha ekonomiktir.' ],
    [ 'q' => 'Antrenörlerin sertifikalarını ve boş saatlerini nereden görebilirim?', 'a' => 'Antrenörler sayfasında her eğitmenin eğitim geçmişi, özgeçmişi ve doğrulanmış sertifika belgeleri yer alır. Profil sayfasındaki canlı online takvimden boş saatlere tıklayarak anında randevu oluşturabilirsiniz.' ],
    [ 'q' => 'Kapalı otopark ve soyunma odası kullanımı ücretli mi?', 'a' => 'Soyunma odası, akıllı RFID dolaplar, yağmur duşları ve Fin saunası tüm üye ve salon kiracılarına ücretsizdir. Kapalı otopark Classic üyelere ilk 2 saat, PRO üyelere ilk 3 saat, ELITE üyelere sınırsız ücretsizdir.' ],
    [ 'q' => 'APEX AI Spor Koçu nasıl çalışıyor?', 'a' => 'Sağ alttaki APEX AI Koç butonuna tıklayarak salon müsaitliği, hakem hizmeti, PT paketleri, antrenör önerisi veya boy/kilonuza göre anlık VKİ analizi sorabilirsiniz. Sistem canlı WordPress veritabanımızla entegre çalışır.' ],
    [ 'q' => 'Erişilebilirlik (WCAG 2.2) ve Çerez tercihlerimi nasıl yönetirim?', 'a' => 'Sol alttaki Erişilebilirlik butonundan yazı boyutu, yüksek kontrast, aydınlık mod, disleksi dostu Lexend fontu, okuma kılavuzu ve sesli okumayı açabilirsiniz. Çerez tercihlerinizi sayfa altındaki "Çerez Tercihlerini Yönet" bağlantısından dilediğiniz an değiştirebilirsiniz.' ],
];
?>
<section class="apex-page-hero">
    <div class="apex-container">
        <?php apex_breadcrumbs(); ?>
        <span class="apex-eyebrow">YARDIM MERKEZİ</span>
        <h1 class="apex-h1">SIKÇA SORULAN SORULAR</h1>
        <p class="apex-lead">Salon kiralama, hakem hizmeti, PT paketleri, üyelik, otopark, erişilebilirlik ve gizlilik hakkında merak ettikleriniz.</p>
    </div>
</section>

<section class="apex-section">
    <div class="apex-container" style="max-width:900px;">
        <div class="apex-accordion">
            <?php foreach ( $faqs as $idx => $f ) : ?>
            <details <?php echo $idx === 0 ? 'open' : ''; ?>>
                <summary><?php echo esc_html( $f['q'] ); ?></summary>
                <p><?php echo esc_html( $f['a'] ); ?></p>
            </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php get_footer(); ?>
