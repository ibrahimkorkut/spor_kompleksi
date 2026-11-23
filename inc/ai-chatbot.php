<?php
/**
 * APEX AI Spor Koçu — Hibrit Yapay Zeka Asistan Motoru
 * - WP Yönetim Panelinden Google Gemini veya OpenAI API anahtarı girildiğinde canlı LLM + RAG (gerçek WP veritabanı verileriyle) çalışır.
 * - API anahtarı girilmediğinde veya offline test ortamında gelişmiş Türkçe NLP + canlı WordPress veritabanı motoru %100 sorunsuz yanıt verir.
 *
 * @package ApexArena
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Canlı WordPress verilerinden RAG bağlamı oluştur (LLM ve yerel motor için)
 */
function apex_ai_build_live_context() {
    global $wpdb;
    $settings = apex_get_settings();

    $courts = get_posts( [ 'post_type' => 'apex_court', 'numberposts' => 10, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC' ] );
    $court_lines = [];
    foreach ( $courts as $c ) {
        $sport = get_post_meta( $c->ID, '_apex_sport', true );
        $rate  = (int) get_post_meta( $c->ID, '_apex_hourly_rate', true );
        $ref   = (int) get_post_meta( $c->ID, '_apex_referee_fee', true );
        $dim   = get_post_meta( $c->ID, '_apex_dimensions', true );
        $court_lines[] = sprintf( '- %s (%s, %s): Saatlik %s, Hakem Hizmeti +%s', $c->post_title, ucfirst( $sport ), $dim, apex_format_tl( $rate ), apex_format_tl( $ref ) );
    }

    $trainers = get_posts( [ 'post_type' => 'apex_trainer', 'numberposts' => 10, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC' ] );
    $trainer_lines = [];
    foreach ( $trainers as $t ) {
        $role = get_post_meta( $t->ID, '_apex_role', true );
        $spec = get_post_meta( $t->ID, '_apex_specialty', true );
        $fee  = (int) get_post_meta( $t->ID, '_apex_session_fee', true );
        $exp  = get_post_meta( $t->ID, '_apex_experience', true );
        $trainer_lines[] = sprintf( '- %s (%s | %s | %s yıl deneyim | Seans: %s)', $t->post_title, $role, $spec, $exp, apex_format_tl( $fee ) );
    }

    $packages = get_posts( [ 'post_type' => 'apex_pt_package', 'numberposts' => 12, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC' ] );
    $pkg_lines = [];
    foreach ( $packages as $p ) {
        $type  = get_post_meta( $p->ID, '_apex_training_type', true );
        $sess  = get_post_meta( $p->ID, '_apex_sessions', true );
        $total = (int) get_post_meta( $p->ID, '_apex_total_price', true );
        $days  = get_post_meta( $p->ID, '_apex_days', true );
        $pkg_lines[] = sprintf( '- %s [%s]: %s seans, Toplam %s (%s)', $p->post_title, strtoupper( $type ), $sess, apex_format_tl( $total ), $days );
    }

    $plans = apex_get_membership_plans();
    $plan_lines = [];
    foreach ( $plans as $pl ) {
        $plan_lines[] = sprintf( '- %s: Aylık %s / Yıllık taahhütle aylık %s', $pl['name'], apex_format_tl( $pl['monthly_price'] ), apex_format_tl( $pl['annual_price'] ) );
    }

    $today = current_time( 'Y-m-d' );
    $today_count = (int) $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}apex_court_bookings WHERE booking_date = %s AND status != 'cancelled'",
        $today
    ) );

    return "Sen APEX ARENA Spor ve Performans Kompleksi'nin 7/24 çalışan profesyonel Türkçe AI Spor Koçu ve Müşteri Asistanısın.\n"
        . "Kısa, samimi, enerjik, doğru ve aksiyon odaklı Türkçe yanıtlar ver. Uydurma fiyat verme; sadece aşağıdaki canlı tesis verilerini kullan:\n\n"
        . "TESİS BİLGİLERİ:\n"
        . "- Adres: {$settings['address']} | Tel: {$settings['phone']} | E-posta: {$settings['email']}\n"
        . "- Çalışma Saatleri: Hafta içi {$settings['weekday_hours']}, Hafta sonu {$settings['weekend_hours']}\n"
        . "- Anlık Tesis Yoğunluğu: %{$settings['live_occupancy']} (Sakin) | Kapalı Otopark Boş Yer: {$settings['parking_free']}/{$settings['parking_total']} (6 EV şarj ünitesi)\n"
        . "- Tesis Olanakları: 240 dijital dolaplı Soyunma Odası & Yağmur Duş & Fin Saunası, 180 araçlık Kapalı Otopark (üyelere ilk 3 saat ücretsiz), Apex Protein Kafe.\n"
        . "- Bugünkü Salon Rezervasyon Sayısı: {$today_count}\n\n"
        . "SALONLAR (2 Basketbol + 1 Voleybol + Hakem Ayarlama Hizmeti):\n" . implode( "\n", $court_lines ) . "\n\n"
        . "ANTRENÖRLER:\n" . implode( "\n", $trainer_lines ) . "\n\n"
        . "PT PAKETLERİ (BİREYSEL & GRUPSAL):\n" . implode( "\n", $pkg_lines ) . "\n\n"
        . "ÜYELİK PAKETLERİ:\n" . implode( "\n", $plan_lines );
}

/**
 * Google Gemini API çağrısı
 */
function apex_ai_call_gemini( $message, $history, $system_prompt, $settings ) {
    $model = ! empty( $settings['gemini_model'] ) ? $settings['gemini_model'] : 'gemini-2.5-flash';
    $url   = 'https://generativelanguage.googleapis.com/v1beta/models/' . rawurlencode( $model ) . ':generateContent?key=' . rawurlencode( $settings['gemini_api_key'] );

    $contents = [];
    if ( is_array( $history ) ) {
        foreach ( array_slice( $history, -6 ) as $h ) {
            if ( empty( $h['text'] ) ) {
                continue;
            }
            $contents[] = [
                'role'  => ( isset( $h['role'] ) && $h['role'] === 'bot' ) ? 'model' : 'user',
                'parts' => [ [ 'text' => sanitize_text_field( $h['text'] ) ] ],
            ];
        }
    }
    $contents[] = [
        'role'  => 'user',
        'parts' => [ [ 'text' => $message ] ],
    ];

    $response = wp_remote_post( $url, [
        'timeout' => 15,
        'headers' => [ 'Content-Type' => 'application/json' ],
        'body'    => wp_json_encode( [
            'systemInstruction' => [ 'parts' => [ [ 'text' => $system_prompt ] ] ],
            'contents'          => $contents,
            'generationConfig'  => [ 'temperature' => 0.6, 'maxOutputTokens' => 450 ],
        ] ),
    ] );

    if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
        return false;
    }
    $data = json_decode( wp_remote_retrieve_body( $response ), true );
    return $data['candidates'][0]['content']['parts'][0]['text'] ?? false;
}

/**
 * OpenAI API çağrısı
 */
function apex_ai_call_openai( $message, $history, $system_prompt, $settings ) {
    $model = ! empty( $settings['openai_model'] ) ? $settings['openai_model'] : 'gpt-4o-mini';
    $msgs  = [ [ 'role' => 'system', 'content' => $system_prompt ] ];
    if ( is_array( $history ) ) {
        foreach ( array_slice( $history, -6 ) as $h ) {
            if ( empty( $h['text'] ) ) {
                continue;
            }
            $msgs[] = [
                'role'    => ( isset( $h['role'] ) && $h['role'] === 'bot' ) ? 'assistant' : 'user',
                'content' => sanitize_text_field( $h['text'] ),
            ];
        }
    }
    $msgs[] = [ 'role' => 'user', 'content' => $message ];

    $response = wp_remote_post( 'https://api.openai.com/v1/chat/completions', [
        'timeout' => 15,
        'headers' => [
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $settings['openai_api_key'],
        ],
        'body' => wp_json_encode( [
            'model'       => $model,
            'messages'    => $msgs,
            'temperature' => 0.6,
            'max_tokens'  => 450,
        ] ),
    ] );

    if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
        return false;
    }
    $data = json_decode( wp_remote_retrieve_body( $response ), true );
    return $data['choices'][0]['message']['content'] ?? false;
}

/**
 * Gelişmiş Yerel Hibrit NLP + Canlı WordPress Veritabanı Motoru (Offline & Anahtarsız %100 Çalışır)
 */
function apex_ai_local_hybrid_reply( $message ) {
    global $wpdb;
    $settings = apex_get_settings();
    $q = mb_strtolower( $message, 'UTF-8' );
    $q_ascii = str_replace( [ 'ç', 'ğ', 'ı', 'İ', 'ö', 'ş', 'ü' ], [ 'c', 'g', 'i', 'i', 'o', 's', 'u' ], $q );

    $suggestions = [ 'Salon fiyatları ve hakem hizmeti', 'PT paketleri ve seans ücretleri', 'Bana antrenör öner', 'Üyelik paketlerini karşılaştır' ];
    $action      = null;

    // 1. VKİ / Boy-Kilo Analizi (örn: "180 cm 82 kg" veya "boyum 175 kilom 70")
    if ( preg_match( '/(1[4-9]\d|2[0-1]\d)\s*(cm)?.*?(4\d|[5-9]\d|1\d\d)\s*(kg|kilo)/u', $q, $m ) || preg_match( '/(4\d|[5-9]\d|1\d\d)\s*(kg|kilo).*?(1[4-9]\d|2[0-1]\d)\s*(cm)?/u', $q, $m2 ) ) {
        if ( ! empty( $m ) ) {
            $h_cm = (int) $m[1];
            $w_kg = (int) $m[3];
        } else {
            $w_kg = (int) $m2[1];
            $h_cm = (int) $m2[3];
        }
        $h_m = $h_cm / 100;
        $bmi = round( $w_kg / ( $h_m * $h_m ), 1 );
        if ( $bmi < 18.5 ) {
            $cat = 'Zayıf';
            $rec = 'Kas kütlesi kazanımı için **Kerem Alkan** ile 12 Seans Bireysel Kuvvet PT paketini ve Protein Kafe’deki *Apex Peanut Power Shake* (38g protein) desteğini öneririm.';
        } elseif ( $bmi < 25 ) {
            $cat = 'İdeal Aralık';
            $rec = 'Harika bir temelin var! Atletik performans ve sıkılaşma için **APEX PRO** üyeliği + haftalık Fonksiyonel HIIT grup dersleri tam sana göre.';
        } elseif ( $bmi < 30 ) {
            $cat = 'Hafif Kilolu';
            $rec = 'Yağ yakımı ve metabolizma hızlandırma için **Elif Karaca** ile Grupsal HIIT + **Selin Yılmaz** ile 8 Seans Fonksiyonel PT paketini öneriyorum.';
        } else {
            $cat = 'Yüksek VKİ';
            $rec = 'Eklem dostu ve güvenli başlangıç için **Selin Yılmaz** (Reformer Pilates & Postür) ile birebir ölçüm + InBody analiziyle kişiselleştirilmiş program başlatmanı öneririm.';
        }
        return [
            'reply'       => "📊 **Anlık VKİ Analizin ({$h_cm} cm / {$w_kg} kg):**\nVücut Kitle İndeksin: **{$bmi}** (*{$cat}*)\n\n🎯 **APEX AI Önerisi:** {$rec}",
            'action'      => [ 'label' => 'PT Paketlerini İncele', 'url' => home_url( '/pt-egitmenler/' ) ],
            'suggestions' => [ 'PT seans ücretleri nedir?', 'Kerem Alkan müsait günler', 'Üyelik paketleri' ],
        ];
    }

    // 2. Salon Kiralama / Basketbol / Voleybol / Hakem / Müsaitlik
    if ( preg_match( '/(salon|basketbol|voleybol|hakem|saha|kirala|mac|turnuva|bos saat|musait)/', $q_ascii ) ) {
        $courts = get_posts( [ 'post_type' => 'apex_court', 'numberposts' => 5, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC' ] );
        $target_date = ( strpos( $q_ascii, 'yarin' ) !== false ) ? date( 'Y-m-d', strtotime( '+1 day', current_time( 'timestamp' ) ) ) : current_time( 'Y-m-d' );
        $date_label  = ( strpos( $q_ascii, 'yarin' ) !== false ) ? 'Yarın (' . date( 'd.m.Y', strtotime( $target_date ) ) . ')' : 'Bugün (' . date( 'd.m.Y', strtotime( $target_date ) ) . ')';

        $lines = [ "🏀🏐 **APEX ARENA Salon Kiralama & Canlı Müsaitlik ({$date_label}):**" ];
        foreach ( $courts as $c ) {
            $rate = (int) get_post_meta( $c->ID, '_apex_hourly_rate', true );
            $ref  = (int) get_post_meta( $c->ID, '_apex_referee_fee', true );
            $taken = $wpdb->get_col( $wpdb->prepare(
                "SELECT time_slot FROM {$wpdb->prefix}apex_court_bookings WHERE court_id = %d AND booking_date = %s AND status != 'cancelled'",
                $c->ID, $target_date
            ) );
            $free_slots = array_values( array_diff( [ '10:00', '14:00', '18:00', '19:00', '20:00', '21:00' ], $taken ) );
            $sample = ! empty( $free_slots ) ? implode( ', ', array_slice( $free_slots, 0, 4 ) ) : 'Tamamı dolu';
            $lines[] = "• **{$c->post_title}** — " . apex_format_tl( $rate ) . "/saat *(+Lisanslı Hakem: " . apex_format_tl( $ref ) . ")*\n  ↳ Öne çıkan boş saatler: `{$sample}`";
        }
        $lines[] = "\n⚖️ **Hakem Ayarlama Hizmeti:** Rezervasyon formunda tek tıkla TBF/TVF lisanslı hakem ekleyebilirsin!";
        return [
            'reply'       => implode( "\n", $lines ),
            'action'      => [ 'label' => 'Hemen Salon Kirala', 'url' => home_url( '/salon-kiralama/' ) ],
            'suggestions' => [ 'Yarın akşam boş salonlar', 'APEX PRO salon indirimi', 'Soyunma odası ve otopark var mı?' ],
        ];
    }

    // 3. PT Paketleri, Bireysel & Grupsal Antrenman, Seans Ücretleri, Günler
    if ( preg_match( '/(pt|paket|seans|bireysel|grupsal|grup ders|egitim|ucret|fiyat|gunler|program|pilates|hiit|kilo|yag|kas)/', $q_ascii ) && ! preg_match( '/(uyelik|classic|elite)/', $q_ascii ) ) {
        $packages = get_posts( [ 'post_type' => 'apex_pt_package', 'numberposts' => 8, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC' ] );
        $lines = [ "🏋️ **PT Paketleri, Seans Ücretleri ve Eğitim Günleri:**" ];
        foreach ( $packages as $p ) {
            $type  = get_post_meta( $p->ID, '_apex_training_type', true ) === 'grupsal' ? 'Grupsal' : 'Bireysel';
            $sess  = get_post_meta( $p->ID, '_apex_sessions', true );
            $per   = (int) get_post_meta( $p->ID, '_apex_per_session_fee', true );
            $total = (int) get_post_meta( $p->ID, '_apex_total_price', true );
            $days  = get_post_meta( $p->ID, '_apex_days', true );
            $lines[] = "• **{$p->post_title}** ({$type} · {$sess} Seans): **" . apex_format_tl( $total ) . "** *(Seans başı " . apex_format_tl( $per ) . ")*\n  ↳ Günler: {$days}";
        }
        $lines[] = "\n💡 İpucu: Boy ve kilonu yazarsan (örn. `178 cm 80 kg`) sana özel VKİ analizi ve PT programı önerebilirim!";
        return [
            'reply'       => implode( "\n", $lines ),
            'action'      => [ 'label' => 'PT Paketleri & Online Randevu', 'url' => home_url( '/pt-egitmenler/' ) ],
            'suggestions' => [ 'Antrenör kadrosunu göster', '180 cm 84 kg analiz et', 'Online randevu nasıl alınır?' ],
        ];
    }

    // 4. Antrenörler, Sertifikalar, Özgeçmiş, Online Takvim
    if ( preg_match( '/(antrenor|egitmen|hoca|koc|kerem|selin|mert|elif|sertifika|ozgecmis|takvim|randevu)/', $q_ascii ) ) {
        $trainers = get_posts( [ 'post_type' => 'apex_trainer', 'numberposts' => 6, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC' ] );
        $lines = [ "🥇 **Sertifikalı Antrenör Kadromuz ve Canlı Takvimleri:**" ];
        foreach ( $trainers as $t ) {
            $role = get_post_meta( $t->ID, '_apex_role', true );
            $spec = get_post_meta( $t->ID, '_apex_specialty', true );
            $fee  = (int) get_post_meta( $t->ID, '_apex_session_fee', true );
            $exp  = get_post_meta( $t->ID, '_apex_experience', true );
            $lines[] = "• **{$t->post_title}** — *{$role}* ({$exp} yıl deneyim, " . apex_format_tl( $fee ) . "/seans)\n  ↳ Uzmanlık: {$spec}";
        }
        $lines[] = "\n📅 Her antrenörün profilinde **sertifika belgelerini** görüntüleyebilir ve **canlı takviminden** boş saate tıklayıp anında randevu alabilirsin.";
        return [
            'reply'       => implode( "\n", $lines ),
            'action'      => [ 'label' => 'Antrenörler & Canlı Takvim', 'url' => home_url( '/antrenorler/' ) ],
            'suggestions' => [ 'PT paket ücretleri', 'Salon kiralamak istiyorum', 'Üyelik paketleri' ],
        ];
    }

    // 5. Tesis Olanakları (Soyunma Odası, Duş, Otopark, Kafe)
    if ( preg_match( '/(tesis|olanak|soyunma|dus|sauna|dolap|otopark|park|sarj|kafe|cafe|protein|kahve|yemek|menu)/', $q_ascii ) ) {
        $reply = "✨ **APEX ARENA 5 Yıldızlı Tesis Olanakları:**\n"
            . "• **Soyunma Odası & Duş:** 240 adet dijital RFID akıllı dolap, yağmur duş kabinleri, Fin saunası, hijyenik havlu servisi ve engelli erişimli özel soyunma odası.\n"
            . "• **Kapalı Otopark:** 180 araçlık kapalı otopark (şu an **{$settings['parking_free']} boş yer**), 6 adet EV elektrikli araç şarj ünitesi, üyelere ilk 3 saat **ücretsiz**.\n"
            . "• **Apex Protein Kafe:** Makro hesaplı protein shake'ler (₺155–₺175), fit bowl menüleri (₺255–₺340) ve 3. dalga cold brew kahveler. APEX PRO üyelerine **%15 indirim**!";
        return [
            'reply'       => $reply,
            'action'      => [ 'label' => 'Tesis Olanakları & Kafe Menüsü', 'url' => home_url( '/tesis-olanaklari/' ) ],
            'suggestions' => [ 'Üyelik paketleri neler?', 'Salon kiralama ücretleri', 'Çalışma saatleriniz nedir?' ],
        ];
    }

    // 6. Üyelik İşlemleri / Dijital Kart / Paketler
    if ( preg_match( '/(uyelik|uye|kayit|katil|abonelik|classic|pro|elite|qr|kart|panel|giris)/', $q_ascii ) ) {
        $plans = apex_get_membership_plans();
        $lines = [ "💳 **APEX ARENA Üyelik Paketleri (Yıllıkta %20 İndirim):**" ];
        foreach ( $plans as $pl ) {
            $tag = $pl['featured'] ? ' ⭐ *EN POPÜLER*' : '';
            $lines[] = "• **{$pl['name']}**{$tag}: Aylık **" . apex_format_tl( $pl['monthly_price'] ) . "** *(Yıllıkta " . apex_format_tl( $pl['annual_price'] ) . "/ay)* — {$pl['summary']}";
        }
        $lines[] = "\n📲 Online kayıt olduğunda **Dijital QR Üyelik Kartın** anında aktif olur; randevularını ve salon rezervasyonlarını Üye Panelinden yönetebilirsin.";
        return [
            'reply'       => implode( "\n", $lines ),
            'action'      => [ 'label' => 'Üyelik Paketleri & Kayıt Ol', 'url' => home_url( '/uyelik/' ) ],
            'suggestions' => [ 'PT paketleri nelerdir?', 'Salon kiralamada üye indirimi var mı?', 'Tesis olanakları' ],
        ];
    }

    // 7. Erişilebilirlik, KVKK, Çerez Politikası, Duyurular, İletişim, Çalışma Saatleri
    if ( preg_match( '/(cerez|cookie|kvkk|gizlilik|sozlesme|iade|iptal)/', $q_ascii ) ) {
        return [
            'reply'       => "🛡️ **KVKK, Gizlilik ve Çerez Yönetimi:**\nAPEX ARENA, 6698 sayılı KVKK kapsamına tam uyumludur. Sayfanın alt kısmındaki **Çerez Tercihleri** bağlantısından Zorunlu, Analitik, Tercih ve Pazarlama çerezlerini dilediğin zaman yönetebilir; Çerez Politikası ve KVKK Aydınlatma Metni sayfalarımızı inceleyebilirsin.",
            'action'      => [ 'label' => 'Çerez Politikası Sayfası', 'url' => home_url( '/cerez-politikasi/' ) ],
            'suggestions' => [ 'Erişilebilirlik özellikleri neler?', 'İletişim ve adres', 'Üyelik paketleri' ],
        ];
    }

    if ( preg_match( '/(erisilebilirlik|wcag|kontrast|disleksi|engelli|sesli|buyut)/', $q_ascii ) ) {
        return [
            'reply'       => "♿ **WCAG 2.2 AA Erişilebilirlik Özellikleri:**\nSol alttaki **Erişilebilirlik** butonuna tıklayarak; yazı boyutunu büyütebilir, **Yüksek Kontrast**, **Disleksi Dostu Lexend Fontu**, **Okuma Kılavuzu**, **Animasyon Durdurma**, **Bağlantı Vurgulama** ve **Türkçe Sesli Okuma** özelliklerini anında etkinleştirebilirsin. Ayrıca tesisimizde engelli otoparkı, rampa ve özel soyunma odası bulunur.",
            'action'      => [ 'label' => 'Erişilebilirlik Beyanı', 'url' => home_url( '/erisilebilirlik-beyani/' ) ],
            'suggestions' => [ 'Tesis olanakları', 'İletişim bilgileri', 'Salon kiralama' ],
        ];
    }

    if ( preg_match( '/(duyuru|etkinlik|kampanya|haber|bakim)/', $q_ascii ) ) {
        $anns = get_posts( [ 'post_type' => 'apex_announcement', 'numberposts' => 3, 'post_status' => 'publish' ] );
        $lines = [ "📢 **Güncel Salon Duyuruları:**" ];
        foreach ( $anns as $a ) {
            $cat = get_post_meta( $a->ID, '_apex_ann_category', true );
            $lines[] = "• **[{$cat}] {$a->post_title}** — " . wp_trim_words( wp_strip_all_tags( $a->post_excerpt ?: $a->post_content ), 14 );
        }
        return [
            'reply'       => implode( "\n", $lines ),
            'action'      => [ 'label' => 'Tüm Duyurular', 'url' => home_url( '/duyurular/' ) ],
            'suggestions' => [ 'Salon kiralama ücretleri', 'PT paketleri', 'İletişim' ],
        ];
    }

    if ( preg_match( '/(saat|acik|kapali|adres|nerede|konum|telefon|iletisim|yogunluk)/', $q_ascii ) ) {
        return [
            'reply'       => "📍 **APEX ARENA İletişim & Canlı Durum:**\n• **Adres:** {$settings['address']}\n• **Telefon:** {$settings['phone']} | **E-posta:** {$settings['email']}\n• **Çalışma Saatleri:** Hafta içi **{$settings['weekday_hours']}**, Hafta sonu **{$settings['weekend_hours']}**\n• **Anlık Yoğunluk:** %{$settings['live_occupancy']} (Sakin) · Otopark: {$settings['parking_free']}/{$settings['parking_total']} boş yer.",
            'action'      => [ 'label' => 'İletişim & Harita', 'url' => home_url( '/iletisim/' ) ],
            'suggestions' => [ 'Salon kiralamak istiyorum', 'Üyelik paketleri', 'Tesis olanakları' ],
        ];
    }

    // 8. Selamlama & Genel Yardım
    return [
        'reply'       => "👋 Merhaba! Ben **APEX AI Spor Koçu**.\nSana şu konularda canlı tesis verileriyle anında yardımcı olabilirim:\n• 🏀 **Basketbol (2 adet) & Voleybol (1 adet)** salon kiralama ve **hakem hizmeti**\n• 🏋️ **Bireysel / Grupsal PT paketleri**, seans ücretleri ve antrenör seçimi\n• 📊 **VKİ analizi** (ör. `180 cm 82 kg` yazman yeterli!)\n• 💳 **Üyelik paketleri**, tesis olanakları (duş, otopark, kafe) ve canlı yoğunluk\n\nHangi konuda yardımcı olayım?",
        'action'      => $action,
        'suggestions' => $suggestions,
    ];
}

/**
 * Ana Hibrit Chatbot Yanıt Yöneticisi
 */
function apex_ai_handle_message( $message, $history = [], $session_id = '' ) {
    global $wpdb;
    $settings = apex_get_settings();
    $mode     = apex_get_effective_ai_mode();
    $reply    = false;
    $used     = 'local-hybrid';
    $action   = null;
    $sugg     = [ 'Salon kiralama & hakem', 'PT paket ücretleri', 'Antrenörler & takvim', '180 cm 82 kg analiz et' ];

    if ( $mode === 'gemini-live' || $mode === 'openai-live' ) {
        $sys_prompt = apex_ai_build_live_context();
        if ( $mode === 'gemini-live' ) {
            $reply = apex_ai_call_gemini( $message, $history, $sys_prompt, $settings );
            if ( $reply ) {
                $used = 'gemini-live';
            }
        } elseif ( $mode === 'openai-live' ) {
            $reply = apex_ai_call_openai( $message, $history, $sys_prompt, $settings );
            if ( $reply ) {
                $used = 'openai-live';
            }
        }
    }

    // API anahtarı yoksa veya ağ/kota hatası oluşursa kesintisiz Yerel Hibrit WP DB motoruna geç!
    if ( ! $reply ) {
        $local  = apex_ai_local_hybrid_reply( $message );
        $reply  = $local['reply'];
        $action = $local['action'];
        $sugg   = $local['suggestions'];
        $used   = 'local-hybrid';
    }

    // Konuşmayı veritabanına kaydet
    $wpdb->insert(
        $wpdb->prefix . 'apex_chatbot_logs',
        [
            'session_id'   => sanitize_text_field( $session_id ?: wp_generate_uuid4() ),
            'user_id'      => get_current_user_id(),
            'user_message' => sanitize_textarea_field( $message ),
            'bot_reply'    => sanitize_textarea_field( $reply ),
            'ai_mode'      => $used,
        ],
        [ '%s', '%d', '%s', '%s', '%s' ]
    );

    return [
        'reply'       => $reply,
        'mode'        => $used,
        'action'      => $action,
        'suggestions' => $sugg,
    ];
}
