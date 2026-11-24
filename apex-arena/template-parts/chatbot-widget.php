<?php
/**
 * APEX AI Spor Koçu — Apple Liquid Glass Yüzen Asistan Bileşeni
 *
 * @package ApexArena
 */
$ai_mode = apex_get_effective_ai_mode();
$mode_label = $ai_mode === 'gemini-live' ? 'Gemini 2.5 Flash · Canlı AI' : ( $ai_mode === 'openai-live' ? 'OpenAI LLM · Canlı AI' : 'Hibrit WP Veritabanı Motoru' );
$wa_settings = apex_get_settings();
$wa_digits   = preg_replace( '/\D+/', '', (string) ( $wa_settings['phone'] ?? '02165552739' ) );
if ( strpos( $wa_digits, '0' ) === 0 ) {
    $wa_digits = '90' . substr( $wa_digits, 1 );
} elseif ( strpos( $wa_digits, '90' ) !== 0 ) {
    $wa_digits = '90' . $wa_digits;
}
$wa_text = rawurlencode( 'Merhaba APEX ARENA, üyelik, PT ve salon rezervasyonu hakkında bilgi almak istiyorum.' );
$wa_url  = 'https://wa.me/' . $wa_digits . '?text=' . $wa_text;
?>
<a
    href="<?php echo esc_url( $wa_url ); ?>"
    class="apex-wa-launcher"
    id="apex-wa-launcher"
    target="_blank"
    rel="noopener noreferrer"
    aria-label="APEX ARENA WhatsApp Destek Hattı (yeni sekmede açılır)"
>
    <span class="apex-wa-ring apex-wa-ring-1" aria-hidden="true"></span>
    <span class="apex-wa-ring apex-wa-ring-2" aria-hidden="true"></span>
    <span class="apex-wa-ring apex-wa-ring-3" aria-hidden="true"></span>
    <span class="apex-wa-tooltip" aria-hidden="true">
        <span class="apex-wa-tooltip-dot"></span>WhatsApp Destek Hattı
    </span>
    <span class="apex-wa-orb" aria-hidden="true">
        <span class="apex-wa-sheen"></span>
        <svg class="apex-wa-icon" width="28" height="28" viewBox="0 0 32 32" fill="none">
            <path d="M16.003 3.2C8.943 3.2 3.2 8.943 3.2 16.003c0 2.27.595 4.487 1.725 6.44L3.1 28.9l6.632-1.739a12.75 12.75 0 0 0 6.271 1.644h.005c7.06 0 12.802-5.743 12.802-12.802S23.063 3.2 16.003 3.2Z" stroke="#FFFFFF" stroke-width="2.4" stroke-linejoin="round"/>
            <path d="M22.25 19.34c-.34-.17-2.01-.99-2.32-1.1-.31-.12-.54-.17-.77.17-.23.34-.88 1.1-1.08 1.33-.2.23-.4.26-.74.09-.34-.17-1.44-.53-2.74-1.69-1.01-.9-1.7-2.02-1.9-2.36-.2-.34-.02-.52.15-.69.15-.15.34-.4.51-.6.17-.2.23-.34.34-.57.11-.23.06-.43-.03-.6-.09-.17-.77-1.85-1.05-2.54-.28-.67-.56-.58-.77-.59-.2-.01-.43-.01-.66-.01-.23 0-.6.09-.91.43-.31.34-1.2 1.17-1.2 2.85 0 1.68 1.23 3.31 1.4 3.54.17.23 2.41 3.68 5.84 5.16.82.35 1.45.56 1.95.72.82.26 1.56.22 2.15.14.66-.1 2.01-.82 2.3-1.62.28-.8.28-1.48.2-1.62-.09-.14-.31-.23-.66-.4Z" fill="#FFFFFF"/>
        </svg>
    </span>
    <span class="apex-wa-badge" aria-hidden="true">1</span>
</a>

<button type="button" class="apex-ai-launcher" id="apex-ai-launcher" aria-label="APEX AI Spor Koçu sohbetini aç" aria-controls="apex-ai-panel" aria-expanded="false" data-open-ai>
    <span class="apex-ai-launcher-dot" aria-hidden="true"></span>
    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round" aria-hidden="true"><path d="M12 3a9 9 0 0 0-9 9c0 1.66.45 3.22 1.24 4.56L3 21l4.44-1.24A8.96 8.96 0 0 0 12 21a9 9 0 1 0 0-18Z"/></svg>
    <span>Apex AI Koç</span>
</button>

<section class="apex-ai-panel" id="apex-ai-panel" role="dialog" aria-modal="false" aria-labelledby="apex-ai-title" hidden>
    <header class="apex-ai-head">
        <div>
            <span class="apex-ai-badge"><span class="apex-live-dot" style="width:6px;height:6px;" aria-hidden="true"></span> <?php echo esc_html( $mode_label ); ?></span>
            <h2 id="apex-ai-title" class="apex-h3" style="font-size:1.12rem;margin-top:2px;">Apex AI Spor Koçu</h2>
        </div>
        <div style="display:flex;gap:6px;">
            <button type="button" class="apex-mobile-close" id="apex-ai-clear" title="Sohbeti temizle" aria-label="Sohbeti temizle" style="padding:5px 10px;">↺</button>
            <button type="button" class="apex-mobile-close" id="apex-ai-close" aria-label="Sohbeti kapat" style="padding:5px 11px;">✕</button>
        </div>
    </header>

    <div class="apex-ai-messages" id="apex-ai-messages" aria-live="polite">
        <div class="apex-msg apex-msg-bot" style="white-space:normal;">Selam, ben <strong>Apex AI Spor Koçu</strong>. Seninle her konuda doğal şekilde sohbet edebilir, antrenman ya da beslenme tavsiyesi verebilir veya salon müsaitliği, PT paketleri ve üyelikler hakkında canlı bilgi paylaşabilirim. Nasıl gidiyor, ne konuşalım?</div>
    </div>

    <div class="apex-ai-chips" id="apex-ai-chips" aria-label="Hızlı sorular">
        <button type="button" class="apex-chip" data-ai-prompt="Bugün hangi basketbol ve voleybol salonları boş, hakem ücreti nedir?">Boş salonlar &amp; hakem</button>
        <button type="button" class="apex-chip" data-ai-prompt="PT paketleri, seans ücretleri ve eğitim günleri neler?">PT ücret &amp; günler</button>
        <button type="button" class="apex-chip" data-ai-prompt="180 cm 82 kg için VKİ analizi ve program öner">180 cm 82 kg VKİ</button>
        <button type="button" class="apex-chip" data-ai-prompt="Üyelik paketlerini ve tesis olanaklarını karşılaştır">Üyelik &amp; olanaklar</button>
    </div>

    <form class="apex-ai-form" id="apex-ai-form">
        <label for="apex-ai-input" class="sr-only">AI Spor Koçu’na mesajınız</label>
        <input type="text" id="apex-ai-input" placeholder="Salon, PT, VKİ veya üyelik sor..." autocomplete="off" required>
        <button type="submit" class="apex-btn apex-btn-primary apex-btn-sm">Gönder</button>
    </form>
</section>
