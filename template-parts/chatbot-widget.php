<?php
/**
 * APEX AI Spor Koçu — Yüzen Hibrit Chatbot Bileşeni
 *
 * @package ApexArena
 */
$ai_mode = apex_get_effective_ai_mode();
$mode_label = $ai_mode === 'gemini-live' ? 'GEMINI LLM + CANLI WP DB' : ( $ai_mode === 'openai-live' ? 'OPENAI LLM + CANLI WP DB' : 'HİBRİT AKILLI WP DB MOTORU' );
?>
<button type="button" class="apex-ai-launcher" id="apex-ai-launcher" aria-label="APEX AI Spor Koçu sohbetini aç" aria-controls="apex-ai-panel" aria-expanded="false" data-open-ai>
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 3a9 9 0 0 0-9 9c0 1.66.45 3.22 1.24 4.56L3 21l4.44-1.24A8.96 8.96 0 0 0 12 21a9 9 0 1 0 0-18Z"/><circle cx="9" cy="12" r="1" fill="currentColor"/><circle cx="12" cy="12" r="1" fill="currentColor"/><circle cx="15" cy="12" r="1" fill="currentColor"/></svg>
    <span>APEX AI KOÇ</span>
</button>

<section class="apex-ai-panel" id="apex-ai-panel" role="dialog" aria-modal="false" aria-labelledby="apex-ai-title" hidden>
    <header class="apex-ai-head">
        <div>
            <span class="apex-ai-badge">● ÇEVRİMİÇİ · <?php echo esc_html( $mode_label ); ?></span>
            <h2 id="apex-ai-title" class="apex-h3" style="font-size:1.25rem;margin-top:2px;">APEX AI SPOR KOÇU</h2>
        </div>
        <div style="display:flex;gap:6px;">
            <button type="button" class="apex-mobile-close" id="apex-ai-clear" title="Sohbeti temizle" aria-label="Sohbeti temizle" style="padding:6px 10px;">↺</button>
            <button type="button" class="apex-mobile-close" id="apex-ai-close" aria-label="Sohbeti kapat" style="padding:6px 12px;">✕</button>
        </div>
    </header>

    <div class="apex-ai-messages" id="apex-ai-messages" aria-live="polite">
        <div class="apex-msg apex-msg-bot">
            👋 Merhaba! Ben <strong>APEX AI Spor Koçu</strong>.<br><br>
            Canlı tesis veritabanımıza bağlı olarak sana şunlarda anında yardım edebilirim:<br>
            • Basketbol (2 adet) & Voleybol salonu müsaitliği ve <strong>hakem hizmeti</strong><br>
            • Bireysel / Grupsal PT paketleri ve antrenör önerisi<br>
            • Boy/kilo yazarak anlık <strong>VKİ & program analizi</strong> (örn. <code>180 cm 82 kg</code>)
        </div>
    </div>

    <div class="apex-ai-chips" id="apex-ai-chips" aria-label="Hızlı sorular">
        <button type="button" class="apex-chip" data-ai-prompt="Bugün hangi basketbol ve voleybol salonları boş, hakem ücreti nedir?">🏀 Boş salonlar & hakem</button>
        <button type="button" class="apex-chip" data-ai-prompt="PT paketleri, seans ücretleri ve eğitim günleri neler?">🏋️ PT ücret & günler</button>
        <button type="button" class="apex-chip" data-ai-prompt="180 cm 82 kg için VKİ analizi ve program öner">📊 180 cm 82 kg VKİ</button>
        <button type="button" class="apex-chip" data-ai-prompt="Üyelik paketlerini ve tesis olanaklarını karşılaştır">💳 Üyelik & olanaklar</button>
    </div>

    <form class="apex-ai-form" id="apex-ai-form">
        <label for="apex-ai-input" class="sr-only">AI Spor Koçu’na mesajınız</label>
        <input type="text" id="apex-ai-input" placeholder="Salon, PT, VKİ veya üyelik sor..." autocomplete="off" required>
        <button type="submit" class="apex-btn apex-btn-primary apex-btn-sm">GÖNDER</button>
    </form>
</section>
