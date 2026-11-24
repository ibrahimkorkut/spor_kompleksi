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

  // 4b. Çift Yönlü Kavisli Üyelik / Giriş Kayar Kartı (#apex-auth-stage)
  const authStage = document.getElementById('apex-auth-stage');
  function setAuthMode(mode) {
    if (!authStage) return;
    const isReg = mode === 'register';
    authStage.classList.toggle('is-register', isReg);
    authStage.querySelectorAll('[data-auth-mode]').forEach((b) => {
      if (b.classList.contains('apex-auth-m-btn')) {
        const active = b.getAttribute('data-auth-mode') === mode;
        b.classList.toggle('is-active', active);
        b.setAttribute('aria-selected', String(active));
      }
    });
  }
  if (authStage) {
    authStage.querySelectorAll('[data-auth-mode]').forEach((btn) => {
      btn.addEventListener('click', () => setAuthMode(btn.getAttribute('data-auth-mode')));
    });
    const syncAuthHash = () => {
      if (window.location.hash === '#giris') setAuthMode('login');
      else if (window.location.hash === '#kayit') setAuthMode('register');
    };
    syncAuthHash();
    window.addEventListener('hashchange', syncAuthHash);
  }

  document.querySelectorAll('[data-select-plan]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const slug = btn.getAttribute('data-select-plan');
      const sel = document.getElementById('mem-plan-select');
      if (sel) sel.value = slug;
      setAuthMode('register');
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

  // ESC tuşu tüm açık modalları/çekmeceyi/aramayı kapatır (WCAG 2.2) + Ctrl+K / '/' arama kısayolu
  document.addEventListener('keydown', (e) => {
    const tag = (e.target && e.target.tagName) || '';
    const inInput = tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || (e.target && e.target.isContentEditable);

    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
      e.preventDefault();
      if (searchBar) {
        searchBar.hidden = false;
        if (searchToggle) searchToggle.setAttribute('aria-expanded', 'true');
        if (searchInput) {
          searchInput.focus();
          searchInput.select();
        }
      }
      return;
    }

    if (!inInput && e.key === '/' && !e.ctrlKey && !e.metaKey && !e.altKey) {
      if (searchBar) {
        e.preventDefault();
        searchBar.hidden = false;
        if (searchToggle) searchToggle.setAttribute('aria-expanded', 'true');
        if (searchInput) searchInput.focus();
      }
      return;
    }

    if (e.key !== 'Escape') return;
    toggleMobileDrawer(false);
    if (searchBar && !searchBar.hidden) {
      searchBar.hidden = true;
      if (searchToggle) searchToggle.setAttribute('aria-expanded', 'false');
    }
    document.querySelectorAll('.apex-modal-backdrop').forEach((m) => { m.hidden = true; });
    const a11yP = document.getElementById('apex-a11y-panel');
    const aiP = document.getElementById('apex-ai-panel');
    if (a11yP) a11yP.hidden = true;
    if (aiP) aiP.hidden = true;
  });

  // Mobil çekmece içi bağlantılara tıklanınca çekmeceyi kapat
  if (mobileDrawer) {
    mobileDrawer.querySelectorAll('a, button[data-open-ai]').forEach((el) => {
      el.addEventListener('click', () => toggleMobileDrawer(false));
    });
  }

  // 8. Apple Liquid Glass Toast Bildirim Sistemi
  const toastEl = document.getElementById('apex-toast');
  let toastTimer = null;
  function showToast(msg) {
    if (!toastEl || !msg) return;
    toastEl.textContent = msg;
    toastEl.hidden = false;
    toastEl.classList.remove('is-visible');
    void toastEl.offsetWidth;
    toastEl.classList.add('is-visible');
    announce(msg);
    if (toastTimer) window.clearTimeout(toastTimer);
    toastTimer = window.setTimeout(() => {
      toastEl.classList.remove('is-visible');
      toastTimer = window.setTimeout(() => { toastEl.hidden = true; }, 260);
    }, 3200);
  }
  window.apexToast = showToast;

  // 9. Tek Tıkla Panoya Kopyalama (Telefon, E-posta, Adres)
  document.querySelectorAll('[data-copy]').forEach((btn) => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      const val = btn.getAttribute('data-copy') || '';
      const label = btn.getAttribute('data-copy-label') || 'Bilgi';
      if (!val) return;
      const done = () => {
        btn.classList.add('is-copied');
        showToast(`✓ ${label} kopyalandı: ${val}`);
        window.setTimeout(() => btn.classList.remove('is-copied'), 1600);
      };
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(val).then(done).catch(() => {
          const ta = document.createElement('textarea');
          ta.value = val;
          ta.style.position = 'fixed';
          ta.style.opacity = '0';
          document.body.appendChild(ta);
          ta.select();
          try { document.execCommand('copy'); } catch (_) {}
          document.body.removeChild(ta);
          done();
        });
      } else {
        done();
      }
    });
  });

  // 10. Canlı Tesis Açık / Kapalı Durum Rozeti (Hafta İçi 06:30–23:30 · Hafta Sonu 08:00–22:00)
  const openBadges = document.querySelectorAll('[data-live-open-badge]');
  if (openBadges.length > 0) {
    const now = new Date();
    const day = now.getDay(); // 0 = Paz, 6 = Cmt
    const mins = now.getHours() * 60 + now.getMinutes();
    const isWeekend = day === 0 || day === 6;
    const openMins = isWeekend ? 8 * 60 : 6 * 60 + 30;
    const closeMins = isWeekend ? 22 * 60 : 23 * 60 + 30;
    const isOpen = mins >= openMins && mins < closeMins;
    const closeStr = isWeekend ? '22:00' : '23:30';
    const nextOpenStr = isWeekend ? '08:00' : '06:30';
    const text = isOpen ? `Şu An Açık · ${closeStr}’a kadar` : `Şu An Kapalı · ${nextOpenStr}’da açılır`;
    openBadges.forEach((b) => {
      b.textContent = text;
      b.classList.toggle('is-open', isOpen);
      b.classList.toggle('is-closed', !isOpen);
    });
  }

  // 11. Global GPU Scroll-Progress Hairline + Akıllı Yukarı Çık Halkası (0 Forced Layout!)
  const progressFill = document.getElementById('apex-scroll-progress-fill');
  const backTopBtn = document.getElementById('apex-back-top');
  const backTopArc = document.getElementById('apex-back-top-arc');
  const RING_CIRC = 2 * Math.PI * 21; // r = 21 => ~131.95
  if (backTopArc) {
    backTopArc.style.strokeDasharray = RING_CIRC.toFixed(2);
    backTopArc.style.strokeDashoffset = RING_CIRC.toFixed(2);
  }
  let cachedMaxScroll = Math.max(1, document.documentElement.scrollHeight - window.innerHeight);
  let scrollUiRaf = null;
  let lastRatioStr = '';
  let backTopShown = false;

  const measureDocHeight = () => {
    cachedMaxScroll = Math.max(1, document.documentElement.scrollHeight - window.innerHeight);
  };
  const paintScrollUi = () => {
    scrollUiRaf = null;
    const sy = window.scrollY;
    const ratio = Math.min(1, Math.max(0, sy / cachedMaxScroll));
    const rStr = ratio.toFixed(4);
    if (rStr !== lastRatioStr) {
      lastRatioStr = rStr;
      if (progressFill) progressFill.style.transform = `scaleX(${rStr})`;
      if (backTopArc) backTopArc.style.strokeDashoffset = (RING_CIRC * (1 - ratio)).toFixed(2);
    }
    const shouldShow = sy > 420;
    if (backTopBtn && shouldShow !== backTopShown) {
      backTopShown = shouldShow;
      backTopBtn.classList.toggle('is-visible', shouldShow);
    }
  };
  const scheduleScrollUi = () => {
    if (!scrollUiRaf) scrollUiRaf = window.requestAnimationFrame(paintScrollUi);
  };
  if ('ResizeObserver' in window) {
    const docRo = new ResizeObserver(measureDocHeight);
    docRo.observe(document.documentElement);
  }
  window.addEventListener('scroll', scheduleScrollUi, { passive: true });
  window.addEventListener('resize', () => { measureDocHeight(); scheduleScrollUi(); }, { passive: true });
  measureDocHeight();
  paintScrollUi();

  if (backTopBtn) {
    backTopBtn.addEventListener('click', () => {
      const reduced = document.documentElement.classList.contains('a11y-reduce-motion');
      window.scrollTo({ top: 0, behavior: reduced ? 'auto' : 'smooth' });
      announce('Sayfanın başına dönüldü.');
    });
  }

  // 12. Masaüstü Kart İmleç Işığı (Zero-Layout Pointer Spotlight) + GPU Scroll-Reveal
  const finePointer = window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches;
  if (finePointer) {
    document.querySelectorAll('.apex-card, .apex-contact-info-item').forEach((card) => {
      let rect = null;
      card.addEventListener('pointerenter', () => { rect = card.getBoundingClientRect(); }, { passive: true });
      card.addEventListener('pointermove', (e) => {
        if (!rect || rect.width < 1) return;
        card.style.setProperty('--mx', `${(e.clientX - rect.left).toFixed(0)}px`);
        card.style.setProperty('--my', `${(e.clientY - rect.top).toFixed(0)}px`);
      }, { passive: true });
    });
  }

  const reducedPref =
    document.documentElement.classList.contains('a11y-reduce-motion') ||
    (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
  if (!reducedPref && 'IntersectionObserver' in window) {
    const revealTargets = document.querySelectorAll('.apex-section .apex-card, .apex-contact-info-item, .apex-contact-map-card');
    if (revealTargets.length > 0) {
      const revIo = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-revealed');
            revIo.unobserve(entry.target);
          }
        });
      }, { rootMargin: '0px 0px -40px 0px', threshold: 0.06 });

      revealTargets.forEach((el) => {
        const r = el.getBoundingClientRect();
        if (r.top > window.innerHeight * 0.96) {
          el.classList.add('apex-reveal');
          revIo.observe(el);
        }
      });
    }
  }
})();
