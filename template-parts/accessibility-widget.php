<?php
/**
 * WCAG 2.2 AA / AAA Yüzen Erişilebilirlik Merkezi — Apple Liquid Glass + SF Symbols Vektör İkonlar
 *
 * @package ApexArena
 */
?>
<button type="button" class="apex-a11y-launcher" id="apex-a11y-launcher" aria-label="Erişilebilirlik araç çubuğunu aç" aria-controls="apex-a11y-panel" aria-expanded="false" data-open-a11y>
    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <circle cx="12" cy="4.5" r="2"/>
        <path d="M4.5 8.5h15M12 8.5v5.5m0 0-3.5 6m3.5-6 3.5 6"/>
    </svg>
    <span>Erişilebilirlik</span>
</button>

<aside class="apex-a11y-panel" id="apex-a11y-panel" role="dialog" aria-modal="false" aria-labelledby="apex-a11y-title" hidden>
    <div class="apex-a11y-head">
        <div>
            <span class="apex-ai-badge">WCAG 2.2 AA / AAA</span>
            <h2 id="apex-a11y-title" class="apex-h3" style="font-size:1.12rem;margin-top:2px;">Erişilebilirlik Merkezi</h2>
        </div>
        <button type="button" class="apex-mobile-close" id="apex-a11y-close" aria-label="Erişilebilirlik panelini kapat">✕</button>
    </div>

    <div class="apex-a11y-font-row">
        <span style="font-size:0.84rem;font-weight:600;">Yazı Boyutu (<span id="a11y-scale-label">%100</span>)</span>
        <div class="apex-a11y-font-btns">
            <button type="button" id="a11y-font-dec" aria-label="Yazı boyutunu küçült">A-</button>
            <button type="button" id="a11y-font-reset" aria-label="Varsayılan yazı boyutu">A</button>
            <button type="button" id="a11y-font-inc" aria-label="Yazı boyutunu büyüt">A+</button>
        </div>
    </div>

    <div class="apex-a11y-grid">
        <button type="button" class="apex-a11y-opt" data-a11y-toggle="highContrast" aria-pressed="false">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 3v18a9 9 0 0 0 0-18Z" fill="currentColor"/></svg>
            Yüksek Kontrast
        </button>
        <button type="button" class="apex-a11y-opt" data-a11y-toggle="lightMode" aria-pressed="false">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="4.5"/><path d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32 1.41 1.41M2 12h2m16 0h2M4.93 19.07l1.41-1.41m11.32-11.32 1.41-1.41"/></svg>
            Aydınlık Mod
        </button>
        <button type="button" class="apex-a11y-opt" data-a11y-toggle="dyslexia" aria-pressed="false">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 20 10.5 4h3L20 20M7 14h10"/></svg>
            Disleksi Fontu
        </button>
        <button type="button" class="apex-a11y-opt" data-a11y-toggle="highlightLinks" aria-pressed="false">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
            Link Vurgula
        </button>
        <button type="button" class="apex-a11y-opt" data-a11y-toggle="reduceMotion" aria-pressed="false">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="5" width="4" height="14" rx="1"/></svg>
            Animasyon Durdur
        </button>
        <button type="button" class="apex-a11y-opt" data-a11y-toggle="grayscale" aria-pressed="false">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 3v18"/></svg>
            Gri Tonlama
        </button>
        <button type="button" class="apex-a11y-opt" data-a11y-toggle="readingGuide" aria-pressed="false">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 12h18M3 6h12M3 18h15"/></svg>
            Okuma Kılavuzu
        </button>
        <button type="button" class="apex-a11y-opt" data-a11y-toggle="largeCursor" aria-pressed="false">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m4 4 7 17 2.5-6.5L20 12 4 4Z"/></svg>
            Büyük İmleç
        </button>
    </div>

    <div style="display:grid;gap:8px;">
        <button type="button" class="apex-btn apex-btn-volt apex-btn-sm apex-btn-block" id="a11y-tts-btn">Sayfayı Sesli Oku (TR)</button>
        <div style="display:flex;gap:8px;">
            <button type="button" class="apex-btn apex-btn-outline apex-btn-sm" id="a11y-reset-all" style="flex:1;">Sıfırla</button>
            <a href="<?php echo esc_url( home_url( '/erisilebilirlik-beyani/' ) ); ?>" class="apex-btn apex-btn-ghost apex-btn-sm" style="flex:1;">Beyan</a>
        </div>
    </div>
</aside>
