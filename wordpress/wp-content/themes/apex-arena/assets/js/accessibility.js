/**
 * APEX ARENA — WCAG 2.2 AA / AAA Erişilebilirlik Merkezi (10 Puan Modülü)
 */
(function () {
  'use strict';

  const STORAGE_KEY = 'apex_a11y_prefs_v1';
  const defaultState = {
    fontScale: 1,
    highContrast: false,
    lightMode: false,
    dyslexia: false,
    highlightLinks: false,
    reduceMotion: false,
    grayscale: false,
    readingGuide: false,
    largeCursor: false,
  };

  let state = { ...defaultState };
  try {
    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved) state = { ...defaultState, ...JSON.parse(saved) };
  } catch (_) {}

  const classMap = {
    highContrast: 'a11y-high-contrast',
    lightMode: 'a11y-light-mode',
    dyslexia: 'a11y-dyslexia',
    highlightLinks: 'a11y-highlight-links',
    reduceMotion: 'a11y-reduce-motion',
    grayscale: 'a11y-grayscale',
    readingGuide: 'a11y-reading-guide',
    largeCursor: 'a11y-large-cursor',
  };

  function applyState() {
    const root = document.documentElement;
    root.style.setProperty('--a11y-font-scale', String(state.fontScale));
    const scaleLbl = document.getElementById('a11y-scale-label');
    if (scaleLbl) scaleLbl.textContent = '%' + Math.round(state.fontScale * 100);

    Object.entries(classMap).forEach(([key, cls]) => {
      root.classList.toggle(cls, !!state[key]);
      const btn = document.querySelector(`[data-a11y-toggle="${key}"]`);
      if (btn) {
        btn.classList.toggle('is-active', !!state[key]);
        btn.setAttribute('aria-pressed', String(!!state[key]));
      }
    });

    try { localStorage.setItem(STORAGE_KEY, JSON.stringify(state)); } catch (_) {}
  }

  applyState();

  const panel = document.getElementById('apex-a11y-panel');
  const launcher = document.getElementById('apex-a11y-launcher');
  const closeBtn = document.getElementById('apex-a11y-close');

  function setOpen(open) {
    if (!panel) return;
    panel.hidden = !open;
    if (launcher) launcher.setAttribute('aria-expanded', String(open));
    if (open && closeBtn) closeBtn.focus();
  }

  document.querySelectorAll('[data-open-a11y]').forEach((btn) => {
    btn.addEventListener('click', () => setOpen(panel ? panel.hidden : true));
  });
  if (closeBtn) closeBtn.addEventListener('click', () => setOpen(false));

  // Font boyut kontrolleri
  document.getElementById('a11y-font-inc')?.addEventListener('click', () => {
    state.fontScale = Math.min(1.45, +(state.fontScale + 0.1).toFixed(2));
    applyState();
  });
  document.getElementById('a11y-font-dec')?.addEventListener('click', () => {
    state.fontScale = Math.max(0.85, +(state.fontScale - 0.1).toFixed(2));
    applyState();
  });
  document.getElementById('a11y-font-reset')?.addEventListener('click', () => {
    state.fontScale = 1;
    applyState();
  });

  // Mod butonları
  document.querySelectorAll('[data-a11y-toggle]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const k = btn.getAttribute('data-a11y-toggle');
      state[k] = !state[k];
      if (k === 'highContrast' && state.highContrast) state.lightMode = false;
      if (k === 'lightMode' && state.lightMode) state.highContrast = false;
      applyState();
    });
  });

  // Tümünü Sıfırla
  document.getElementById('a11y-reset-all')?.addEventListener('click', () => {
    state = { ...defaultState };
    if ('speechSynthesis' in window) window.speechSynthesis.cancel();
    applyState();
  });

  // Okuma Kılavuzu Fare Takibi
  const guide = document.getElementById('apex-reading-guide');
  document.addEventListener('mousemove', (e) => {
    if (state.readingGuide && guide) {
      guide.style.top = e.clientY + 'px';
    }
  }, { passive: true });

  // Web Speech API — Türkçe Sesli Okuma
  const ttsBtn = document.getElementById('a11y-tts-btn');
  if (ttsBtn) {
    ttsBtn.addEventListener('click', () => {
      if (!('speechSynthesis' in window)) {
        window.alert('Tarayıcınız sesli okuma özelliğini desteklemiyor.');
        return;
      }
      if (window.speechSynthesis.speaking) {
        window.speechSynthesis.cancel();
        ttsBtn.textContent = '🔊 SAYFAYI SESLİ OKU (TR)';
        return;
      }
      const selected = window.getSelection()?.toString().trim();
      const mainEl = document.getElementById('main-content');
      const text = selected || (mainEl ? mainEl.innerText.replace(/\s+/g, ' ').slice(0, 750) : document.title);
      const u = new SpeechSynthesisUtterance(text);
      u.lang = 'tr-TR';
      u.rate = 0.98;
      u.onend = () => { ttsBtn.textContent = '🔊 SAYFAYI SESLİ OKU (TR)'; };
      ttsBtn.textContent = '⏹️ SESLİ OKUMAYI DURDUR';
      window.speechSynthesis.speak(u);
    });
  }
})();
