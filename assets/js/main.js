/**
 * APEX ARENA — Ana Etkileşim Modülü
 * Mobil Çekmece, Arama, Sekme Filtreleri, Sertifika & Fotoğraf Modalları, VKİ Hesaplayıcı, Çerez Yönetimi
 */
(function () {
  'use strict';

  function announce(msg) {
    const el = document.getElementById('apex-sr-announcer');
    if (el) el.textContent = msg;
  }

  // 1. Mobil Navigasyon Çekmecesi
  const mobileOpen = document.getElementById('apex-mobile-open');
  const mobileClose = document.getElementById('apex-mobile-close');
  const mobileDrawer = document.getElementById('apex-mobile-drawer');

  function toggleMobileDrawer(open) {
    if (!mobileDrawer) return;
    mobileDrawer.hidden = !open;
    document.body.style.overflow = open ? 'hidden' : '';
    if (mobileOpen) mobileOpen.setAttribute('aria-expanded', String(open));
    if (open && mobileClose) mobileClose.focus();
    if (!open && mobileOpen) mobileOpen.focus();
  }
  if (mobileOpen) mobileOpen.addEventListener('click', () => toggleMobileDrawer(true));
  if (mobileClose) mobileClose.addEventListener('click', () => toggleMobileDrawer(false));

  // 2. Site İçi Arama Çubuğu
  const searchToggle = document.getElementById('apex-search-toggle');
  const searchBar = document.getElementById('apex-search-bar');
  const searchInput = document.getElementById('apex-global-search');
  if (searchToggle && searchBar) {
    searchToggle.addEventListener('click', () => {
      const opening = searchBar.hidden;
      searchBar.hidden = !opening;
      searchToggle.setAttribute('aria-expanded', String(opening));
      if (opening && searchInput) searchInput.focus();
    });
  }

  // 3. Genel Sekme / Kategori Filtreleri (PT, Kafe, Duyurular)
  document.querySelectorAll('[data-filter-group]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const group = btn.getAttribute('data-filter-group');
      const val = btn.getAttribute('data-filter');
      document.querySelectorAll(`[data-filter-group="${group}"]`).forEach((b) => {
        const active = b === btn;
        b.classList.toggle('is-active', active);
        b.setAttribute('aria-selected', String(active));
      });
      const container = document.querySelector(`[data-filter-container="${group}"]`);
      if (!container) return;
      container.querySelectorAll('[data-filter-item]').forEach((item) => {
        const itemCat = item.getAttribute('data-filter-item');
        item.style.display = (val === 'all' || itemCat === val) ? '' : 'none';
      });
      announce(`${val} filtresi uygulandı.`);
    });
  });

  // 4. Üyelik Aylık / Yıllık Fiyat Geçişi
  document.querySelectorAll('[data-billing-toggle]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const mode = btn.getAttribute('data-billing-toggle');
      document.querySelectorAll('[data-billing-toggle]').forEach((b) => b.classList.toggle('is-active', b === btn));
      document.querySelectorAll('[data-plan-price]').forEach((el) => {
        el.textContent = el.getAttribute(mode === 'annual' ? 'data-annual' : 'data-monthly');
      });
      const sel = document.getElementById('mem-billing-select');
      if (sel) sel.value = mode;
    });
  });

  document.querySelectorAll('[data-select-plan]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const slug = btn.getAttribute('data-select-plan');
      const sel = document.getElementById('mem-plan-select');
      if (sel) sel.value = slug;
      const target = document.getElementById('kayit');
      if (target) target.scrollIntoView({ behavior: 'smooth' });
    });
  });

  // 5. Sertifika Belge Modalı & Salon Fotoğraf Lightbox
  function closeModal(id) {
    const m = document.getElementById(id);
    if (m) m.hidden = true;
  }
  document.querySelectorAll('[data-close-modal]').forEach((btn) => {
    btn.addEventListener('click', () => closeModal(btn.getAttribute('data-close-modal')));
  });
  document.querySelectorAll('.apex-modal-backdrop').forEach((bg) => {
    bg.addEventListener('click', (e) => {
      if (e.target === bg) bg.hidden = true;
    });
  });

  document.querySelectorAll('[data-cert-modal]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const modal = document.getElementById('apex-cert-modal');
      if (!modal) return;
      document.getElementById('cert-modal-trainer').textContent = btn.dataset.certTrainer || '';
      document.getElementById('cert-modal-name').textContent = btn.dataset.certName || '';
      document.getElementById('cert-modal-issuer').textContent = btn.dataset.certIssuer || '';
      document.getElementById('cert-modal-year').textContent = btn.dataset.certYear || '';
      document.getElementById('cert-modal-code').textContent = btn.dataset.certCode || '';
      modal.hidden = false;
    });
  });

  document.querySelectorAll('[data-lightbox-src]').forEach((el) => {
    el.addEventListener('click', () => {
      const modal = document.getElementById('apex-lightbox-modal');
      if (!modal) return;
      document.getElementById('lightbox-img').src = el.dataset.lightboxSrc;
      document.getElementById('lightbox-img').alt = el.dataset.lightboxCaption || '';
      document.getElementById('lightbox-caption').textContent = el.dataset.lightboxCaption || '';
      modal.hidden = false;
    });
  });

  // 6. VKİ & Günlük Kalori Hesaplayıcı
  const bmiForm = document.getElementById('apex-bmi-form');
  if (bmiForm) {
    bmiForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const h = parseFloat(document.getElementById('bmi-height').value);
      const w = parseFloat(document.getElementById('bmi-weight').value);
      const out = document.getElementById('bmi-output');
      if (!h || !w) return;
      const bmi = +(w / ((h / 100) ** 2)).toFixed(1);
      const cal = Math.round(w * 28.5);
      let cat = 'İdeal Aralık';
      let tip = 'APEX PRO üyeliği + haftada 3 fonksiyonel antrenman tam sana göre.';
      if (bmi < 18.5) {
        cat = 'Zayıf';
        tip = 'Kas kütlesi kazanımı için Kerem Alkan ile 12 Seans Kuvvet PT + günlük ~' + (cal + 350) + ' kcal önerilir.';
      } else if (bmi >= 25 && bmi < 30) {
        cat = 'Hafif Kilolu';
        tip = 'Yağ yakımı için Elif Karaca ile HIIT Grup + günlük ~' + (cal - 350) + ' kcal makro planı önerilir.';
      } else if (bmi >= 30) {
        cat = 'Yüksek VKİ';
        tip = 'Eklem dostu başlangıç için Selin Yılmaz ile Reformer Pilates + InBody analizi önerilir.';
      }
      out.innerHTML = `<div class="apex-alert apex-alert-success"><strong>VKİ: ${bmi} (${cat})</strong> · Tahmini Günlük İhtiyaç: ~${cal} kcal<br><small>${tip}</small></div>`;
    });
  }

  // 7. KVKK & Çerez Tercih Yönetimi
  const COOKIE_KEY = 'apex_cookie_consent_v1';
  const banner = document.getElementById('apex-cookie-banner');
  const cModal = document.getElementById('apex-cookie-modal');

  function saveConsent(prefs) {
    const payload = { ...prefs, essential: true, updatedAt: new Date().toISOString() };
    localStorage.setItem(COOKIE_KEY, JSON.stringify(payload));
    document.cookie = `apex_cookie_consent=1; path=/; max-age=${60 * 60 * 24 * 365}; SameSite=Lax`;
    if (banner) banner.hidden = true;
    if (cModal) cModal.hidden = true;
    announce('Çerez tercihleriniz kaydedildi.');
  }

  const stored = localStorage.getItem(COOKIE_KEY);
  if (!stored && banner) {
    banner.hidden = false;
  } else if (stored) {
    try {
      const p = JSON.parse(stored);
      const f = document.getElementById('cookie-pref-functional');
      const a = document.getElementById('cookie-pref-analytics');
      const m = document.getElementById('cookie-pref-marketing');
      if (f) f.checked = !!p.functional;
      if (a) a.checked = !!p.analytics;
      if (m) m.checked = !!p.marketing;
    } catch (_) {}
  }

  document.querySelectorAll('[data-open-cookie-modal]').forEach((btn) => {
    btn.addEventListener('click', () => {
      if (cModal) cModal.hidden = false;
    });
  });

  const acceptAllBtn = document.getElementById('apex-cookie-accept-all');
  const modalAcceptAll = document.getElementById('apex-cookie-modal-accept-all');
  const essentialBtn = document.getElementById('apex-cookie-essential');
  const savePrefsBtn = document.getElementById('apex-cookie-save-prefs');

  if (acceptAllBtn) acceptAllBtn.addEventListener('click', () => saveConsent({ functional: true, analytics: true, marketing: true }));
  if (modalAcceptAll) modalAcceptAll.addEventListener('click', () => saveConsent({ functional: true, analytics: true, marketing: true }));
  if (essentialBtn) essentialBtn.addEventListener('click', () => saveConsent({ functional: false, analytics: false, marketing: false }));
  if (savePrefsBtn) {
    savePrefsBtn.addEventListener('click', () => {
      saveConsent({
        functional: !!document.getElementById('cookie-pref-functional')?.checked,
        analytics: !!document.getElementById('cookie-pref-analytics')?.checked,
        marketing: !!document.getElementById('cookie-pref-marketing')?.checked,
      });
    });
  }

  // ESC tuşu tüm açık modalları/çekmeceyi kapatır (WCAG 2.2)
  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    toggleMobileDrawer(false);
    document.querySelectorAll('.apex-modal-backdrop').forEach((m) => { m.hidden = true; });
    const a11yP = document.getElementById('apex-a11y-panel');
    const aiP = document.getElementById('apex-ai-panel');
    if (a11yP) a11yP.hidden = true;
    if (aiP) aiP.hidden = true;
  });
})();
