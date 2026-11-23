<?php
/**
 * WCAG 2.2 AA / AAA Yüzen Erişilebilirlik Merkezi Bileşeni (10 Puan Modülü)
 *
 * @package ApexArena
 */
?>
<button type="button" class="apex-a11y-launcher" id="apex-a11y-launcher" aria-label="Erişilebilirlik araç çubuğunu aç" aria-controls="apex-a11y-panel" aria-expanded="false" data-open-a11y>
    <span aria-hidden="true">♿</span>
    <span>ERİŞİLEBİLİRLİK</span>
</button>

<aside class="apex-a11y-panel" id="apex-a11y-panel" role="dialog" aria-modal="false" aria-labelledby="apex-a11y-title" hidden>
    <div class="apex-a11y-head">
        <div>
            <span class="apex-ai-badge">WCAG 2.2 AA / AAA UYUMLU</span>
            <h2 id="apex-a11y-title" class="apex-h3" style="font-size:1.25rem;">ERİŞİLEBİLİRLİK MERKEZİ</h2>
        </div>
        <button type="button" class="apex-mobile-close" id="apex-a11y-close" aria-label="Erişilebilirlik panelini kapat">✕</button>
    </div>

    <div class="apex-a11y-font-row">
        <span style="font-size:0.85rem;font-weight:700;">Yazı Boyutu (<span id="a11y-scale-label">%100</span>)</span>
        <div class="apex-a11y-font-btns">
            <button type="button" id="a11y-font-dec" aria-label="Yazı boyutunu küçült">A-</button>
            <button type="button" id="a11y-font-reset" aria-label="Varsayılan yazı boyutu">A</button>
            <button type="button" id="a11y-font-inc" aria-label="Yazı boyutunu büyüt">A+</button>
        </div>
    </div>

    <div class="apex-a11y-grid">
        <button type="button" class="apex-a11y-opt" data-a11y-toggle="highContrast" aria-pressed="false">
            <span aria-hidden="true">◐</span> Yüksek Kontrast
        </button>
        <button type="button" class="apex-a11y-opt" data-a11y-toggle="lightMode" aria-pressed="false">
            <span aria-hidden="true">☀️</span> Aydınlık Mod
        </button>
        <button type="button" class="apex-a11y-opt" data-a11y-toggle="dyslexia" aria-pressed="false">
            <span aria-hidden="true">Aa</span> Disleksi Fontu
        </button>
        <button type="button" class="apex-a11y-opt" data-a11y-toggle="highlightLinks" aria-pressed="false">
            <span aria-hidden="true">🔗</span> Link Vurgula
        </button>
        <button type="button" class="apex-a11y-opt" data-a11y-toggle="reduceMotion" aria-pressed="false">
            <span aria-hidden="true">⏸️</span> Animasyon Durdur
        </button>
        <button type="button" class="apex-a11y-opt" data-a11y-toggle="grayscale" aria-pressed="false">
            <span aria-hidden="true">🎨</span> Gri Tonlama
        </button>
        <button type="button" class="apex-a11y-opt" data-a11y-toggle="readingGuide" aria-pressed="false">
            <span aria-hidden="true">📏</span> Okuma Kılavuzu
        </button>
        <button type="button" class="apex-a11y-opt" data-a11y-toggle="largeCursor" aria-pressed="false">
            <span aria-hidden="true">🖱️</span> Büyük İmleç
        </button>
    </div>

    <div style="display:grid;gap:8px;">
        <button type="button" class="apex-btn apex-btn-volt apex-btn-sm apex-btn-block" id="a11y-tts-btn">🔊 SAYFAYI SESLİ OKU (TR)</button>
        <div style="display:flex;gap:8px;">
            <button type="button" class="apex-btn apex-btn-outline apex-btn-sm" id="a11y-reset-all" style="flex:1;">SIFIRLA</button>
            <a href="<?php echo esc_url( home_url( '/erisilebilirlik-beyani/' ) ); ?>" class="apex-btn apex-btn-ghost apex-btn-sm" style="flex:1;">BEYAN</a>
        </div>
    </div>
</aside>
