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

  // 1b. Üst Menü Liquid Glass Shape Morph Geçiş Kapsülü (Hover + Sayfalar Arası Akış)
  const navList = document.getElementById('apex-nav-list');
  const navMorph = document.getElementById('apex-nav-morph');
  if (navList && navMorph) {
    const navLinks = Array.from(navList.querySelectorAll('.apex-nav-link'));
    const activeLink = navList.querySelector('.apex-nav-link.is-active');
    let morphTimer = null;
    let currentTarget = activeLink || null;

    navList.classList.add('has-morph');

    function measureLink(el) {
      const listRect = navList.getBoundingClientRect();
      const r = el.getBoundingClientRect();
      return {
        left: r.left - listRect.left,
        top: r.top - listRect.top,
        width: r.width,
        height: r.height,
      };
    }

    function applyMorphRect(rect, opts) {
      const instant = opts && opts.instant;
      const animateStretch = opts && opts.stretch;
      if (instant) {
        navMorph.classList.add('is-instant');
      } else {
        navMorph.classList.remove('is-instant');
      }
      navMorph.style.width = rect.width.toFixed(1) + 'px';
      navMorph.style.height = rect.height.toFixed(1) + 'px';
      navMorph.style.transform = `translate3d(${rect.left.toFixed(1)}px, ${rect.top.toFixed(1)}px, 0) scaleY(${animateStretch ? 0.91 : 1})`;
      navMorph.classList.add('is-visible');

      if (animateStretch && !instant) {
        navMorph.classList.add('is-morphing');
        clearTimeout(morphTimer);
        morphTimer = setTimeout(() => {
          navMorph.classList.remove('is-morphing');
          navMorph.style.transform = `translate3d(${rect.left.toFixed(1)}px, ${rect.top.toFixed(1)}px, 0) scaleY(1)`;
        }, 170);
      }
      if (instant) {
        void navMorph.offsetWidth;
        navMorph.classList.remove('is-instant');
      }
    }

    function morphTo(el, instant) {
      if (!el) {
        navMorph.classList.remove('is-visible', 'is-morphing');
        currentTarget = null;
        return;
      }
      const changed = currentTarget && currentTarget !== el;
      currentTarget = el;
      applyMorphRect(measureLink(el), { instant: Boolean(instant), stretch: Boolean(changed && !instant) });
    }

    // Sayfa geçişi sonrası önceki menü öğesinden yeni aktif öğeye shape morph!
    let prevRect = null;
    try {
      const raw = sessionStorage.getItem('apex_nav_morph_from');
      sessionStorage.removeItem('apex_nav_morph_from');
      if (raw) {
        const parsed = JSON.parse(raw);
        if (parsed && Date.now() - parsed.ts < 2500) prevRect = parsed;
      }
    } catch (_) {}

    const initNavMorph = () => {
      if (activeLink) {
        if (prevRect && typeof prevRect.left === 'number') {
          applyMorphRect(prevRect, { instant: true });
          currentTarget = null;
          requestAnimationFrame(() => {
            requestAnimationFrame(() => {
              const dest = measureLink(activeLink);
              currentTarget = activeLink;
              applyMorphRect(dest, { instant: false, stretch: true });
            });
          });
        } else {
          morphTo(activeLink, true);
        }
      }
    };

    initNavMorph();
    if (document.fonts && document.fonts.ready) {
      document.fonts.ready.then(() => {
        if (currentTarget && !navMorph.classList.contains('is-morphing')) {
          morphTo(currentTarget, true);
        }
      });
    }

    navLinks.forEach((link) => {
      link.addEventListener('pointerenter', () => morphTo(link, false));
      link.addEventListener('focus', () => morphTo(link, false));
      link.addEventListener('click', () => {
        try {
          const fromEl = activeLink || link;
          const rect = measureLink(fromEl);
          sessionStorage.setItem('apex_nav_morph_from', JSON.stringify({ ...rect, ts: Date.now() }));
        } catch (_) {}
      });
    });

    navList.addEventListener('pointerleave', () => {
      if (activeLink) morphTo(activeLink, false);
      else morphTo(null, false);
    });

    window.addEventListener('resize', () => {
      if (currentTarget) morphTo(currentTarget, true);
    }, { passive: true });
    if (typeof ResizeObserver !== 'undefined') {
      new ResizeObserver(() => {
        if (currentTarget && !navMorph.classList.contains('is-morphing')) {
          morphTo(currentTarget, true);
        }
      }).observe(navList);
    }
  }

  // 2. Site İçi Satır İçi Açılır Arama Çubuğu
  const siteHeader = document.querySelector('.apex-header');
  const searchToggle = document.getElementById('apex-search-toggle');
  const searchBar = document.getElementById('apex-search-bar');
  const searchInput = document.getElementById('apex-global-search');
  const searchClose = document.getElementById('apex-search-close');
  const searchForm = searchBar ? searchBar.querySelector('form') : null;

  function setSearchOpen(open, selectText) {
    if (!searchBar) return;
    searchBar.classList.toggle('is-open', open);
    if (siteHeader) siteHeader.classList.toggle('is-search-open', open);
    if (searchToggle) searchToggle.setAttribute('aria-expanded', String(open));
    if (searchInput) searchInput.tabIndex = open ? 0 : -1;
    if (searchClose) searchClose.tabIndex = open ? 0 : -1;
    if (open && searchInput) {
      setTimeout(() => {
        searchInput.focus();
        if (selectText) searchInput.select();
      }, 60);
    }
  }

  if (searchToggle && searchBar) {
    searchToggle.addEventListener('click', () => {
      const isOpen = searchBar.classList.contains('is-open');
      if (isOpen && searchInput && searchInput.value.trim() && searchForm) {
        searchForm.submit();
        return;
      }
      setSearchOpen(!isOpen);
    });
  }
  if (searchClose) {
    searchClose.addEventListener('click', () => {
      if (searchInput) searchInput.value = '';
      setSearchOpen(false);
      if (searchToggle) searchToggle.focus();
    });
  }
  document.addEventListener('click', (e) => {
    if (searchBar && searchBar.classList.contains('is-open') && !searchBar.contains(e.target)) {
      setSearchOpen(false);
    }
  });

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

  // 5b. Dijital QR Kart Büyütme Pop-up
  const qrOpenBtn = document.getElementById('apex-qr-open');
  const qrModal = document.getElementById('apex-qr-modal');
  if (qrOpenBtn && qrModal) {
    qrOpenBtn.addEventListener('click', () => {
      qrModal.hidden = false;
      const closeBtn = qrModal.querySelector('[data-close-modal]');
      if (closeBtn) closeBtn.focus();
    });
  }

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
      setSearchOpen(true, true);
      return;
    }

    if (!inInput && e.key === '/' && !e.ctrlKey && !e.metaKey && !e.altKey) {
      if (searchBar) {
        e.preventDefault();
        setSearchOpen(true);
      }
      return;
    }

    if (e.key !== 'Escape') return;
    toggleMobileDrawer(false);
    if (searchBar && searchBar.classList.contains('is-open')) {
      setSearchOpen(false);
      if (searchToggle) searchToggle.focus();
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

  // =========================================================================
  // 13. RFC 5545 .ics TAKVİM DOSYASI OLUŞTURUCU (Google / Apple / Outlook)
  // =========================================================================
  function pad2(n) {
    return String(n).padStart(2, '0');
  }
  function escapeIcsText(str) {
    return String(str || '').replace(/\\/g, '\\\\').replace(/\n/g, '\\n').replace(/,/g, '\\,').replace(/;/g, '\\;');
  }
  function parseDateYmd(str) {
    if (!str) return null;
    const iso = /^(\d{4})-(\d{2})-(\d{2})$/.exec(str.trim());
    if (iso) return { y: +iso[1], m: +iso[2], d: +iso[3] };
    const tr = /^(\d{2})\.(\d{2})\.(\d{4})$/.exec(str.trim());
    if (tr) return { y: +tr[3], m: +tr[2], d: +tr[1] };
    return null;
  }
  function downloadIcs({ title, date, time, hours, desc, location }) {
    const dt = parseDateYmd(date) || (() => {
      const now = new Date();
      return { y: now.getFullYear(), m: now.getMonth() + 1, d: now.getDate() };
    })();
    const tm = /^(\d{1,2}):(\d{2})/.exec(String(time || '19:00'));
    const hh = tm ? +tm[1] : 19;
    const mm = tm ? +tm[2] : 0;
    const durH = Math.max(1, parseInt(hours || 1, 10));

    const start = new Date(dt.y, dt.m - 1, dt.d, hh, mm, 0);
    const end = new Date(start.getTime() + durH * 60 * 60 * 1000);
    const fmt = (d) => `${d.getFullYear()}${pad2(d.getMonth() + 1)}${pad2(d.getDate())}T${pad2(d.getHours())}${pad2(d.getMinutes())}00`;
    const nowStamp = fmt(new Date());
    const uid = `apex-${Date.now()}-${Math.random().toString(36).slice(2, 7)}@apexarena.com.tr`;

    const ics = [
      'BEGIN:VCALENDAR',
      'VERSION:2.0',
      'PRODID:-//APEX ARENA Spor Kompleksi//Rezervasyon v2.0//TR',
      'CALSCALE:GREGORIAN',
      'METHOD:PUBLISH',
      'BEGIN:VEVENT',
      `UID:${uid}`,
      `DTSTAMP:${nowStamp}`,
      `DTSTART:${fmt(start)}`,
      `DTEND:${fmt(end)}`,
      `SUMMARY:${escapeIcsText(title || 'APEX ARENA Rezervasyonu')}`,
      `LOCATION:${escapeIcsText(location || 'APEX ARENA Spor Kompleksi, Barbaros Mah. Spor Bulvarı No: 42, Ataşehir / İstanbul')}`,
      `DESCRIPTION:${escapeIcsText(desc || 'APEX ARENA online rezervasyon kaydı.')}`,
      'END:VEVENT',
      'END:VCALENDAR',
    ].join('\r\n');

    const blob = new Blob([ics], { type: 'text/calendar;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `apex-arena-${dt.y}${pad2(dt.m)}${pad2(dt.d)}.ics`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.setTimeout(() => URL.revokeObjectURL(url), 1200);
    showToast('📅 Takvim dosyası (.ics) indirildi!');
  }
  window.apexDownloadIcs = downloadIcs;

  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-ics-btn]');
    if (!btn) return;
    downloadIcs({
      title: btn.getAttribute('data-ics-title'),
      date: btn.getAttribute('data-ics-date'),
      time: btn.getAttribute('data-ics-time'),
      hours: btn.getAttribute('data-ics-hours'),
      desc: btn.getAttribute('data-ics-desc'),
    });
  });

  // Yardımcı REST çağrısı (v2.0.0 modülleri için)
  const apexCfg = window.APEX_CONFIG || { restUrl: '/wp-json/apex/v1/', nonce: '' };
  async function apexApi(endpoint, options = {}) {
    const res = await fetch(apexCfg.restUrl + endpoint, {
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': apexCfg.nonce || '',
        ...(options.headers || {}),
      },
      ...options,
    });
    const data = await res.json().catch(() => ({ success: false, message: 'Sunucu yanıtı okunamadı.' }));
    return { ok: res.ok, data };
  }
  function escHtml(s) {
    return String(s || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  // =========================================================================
  // 14. İNTERAKTİF TESİS KAT PLANI (#kat-plani)
  // =========================================================================
  const fpZones = document.querySelectorAll('[data-fp-zone]');
  const fpCta = document.getElementById('fp-d-cta');
  if (fpZones.length > 0 && fpCta) {
    const siteBase = (fpCta.getAttribute('href') || '').replace(/\/salon-kiralama\/.*$/, '');
    const ZONE_INFO = {
      basket1: {
        tag: 'FIBA STANDARTI · ZEMİN KAT',
        title: 'Arena Basketbol 1 (FIBA Parke)',
        desc: '28×15m uluslararası FIBA ölçülerinde şok emici akçaağaç parke, 4 yönlü LED skorboard, 120 kişilik tribün ve isteğe bağlı lisanslı TBF hakem hizmeti.',
        specs: ['🏀 28×15m FIBA', '⚖️ Hakem (+₺450)', '🚗 2 Saat Ücretsiz Park'],
        cta: 'Bu Salonu Rezerve Et →',
        url: siteBase + '/salon-kiralama/#rezervasyon',
      },
      basket2: {
        tag: 'PERFORMANS SAHASI · ZEMİN KAT',
        title: 'Arena Basketbol 2 (Performans)',
        desc: '5v5 kurumsal ligler, özel antrenmanlar ve şut makineli teknik çalışmalar için akustik yalıtımlı FIBA parke basketbol salonu.',
        specs: ['🏀 28×15m FIBA', '📺 4K Maç Kamerası', '⚖️ TBF Hakem Opsiyonu'],
        cta: 'Müsait Saatleri Gör →',
        url: siteBase + '/salon-kiralama/#rezervasyon',
      },
      voleybol: {
        tag: 'CEV STANDARTI · ZEMİN KAT',
        title: 'Arena Voleybol Pro (Taraflex)',
        desc: '18×9m CEV onaylı kaymaz Taraflex zemin, 11 metre yüksek tavan açıklığı, profesyonel file gergi sistemi ve TVF hakem opsiyonu.',
        specs: ['🏐 CEV Taraflex', '📏 11m Tavan Yüksekliği', '⚖️ TVF Hakem Opsiyonu'],
        cta: 'Voleybol Sahası Kirala →',
        url: siteBase + '/salon-kiralama/#rezervasyon',
      },
      pt: {
        tag: '1. KAT · KUVVET & REFORMER STÜDYOSU',
        title: 'PT & Fonksiyonel Performans Stüdyosu',
        desc: 'Eleiko olimpik ağırlık istasyonları, Technogym Artis kardiyo hattı, Reformer Pilates stüdyosu ve 4 uluslararası sertifikalı birebir koç.',
        specs: ['🏋️ 4 Sertifikalı Koç', '📊 InBody 770 Analizi', '🥗 Kişisel Beslenme Takvimi'],
        cta: 'PT Randevusu Al →',
        url: siteBase + '/pt-egitmenler/#randevu',
      },
      spa: {
        tag: 'ZEMİN KAT · HİJYEN & TOPARLANMA',
        title: 'RFID Soyunma Odaları, Yağmur Duş & Fin Saunası',
        desc: '240 adet QR/RFID bileklikli akıllı dolap, 24 yüksek basınçlı termostatik yağmur duşu, 85°C kuru Fin saunası, şok duşları ve engelsiz özel kabin.',
        specs: ['🔐 240 RFID Dolap', '🧖 85°C Fin Saunası', '♿ Engelsiz Kabin'],
        cta: 'Tesis Olanaklarını İncele →',
        url: siteBase + '/tesis-olanaklari/#soyunma',
      },
      cafe: {
        tag: 'LOBİ & -1. KAT · BESLENME & OTOPARK',
        title: 'Apex Protein Kafe & 180 Araçlık EV Otopark',
        desc: 'Diyetisyen onaylı makro hesaplı shake ve fit bowl ön siparişi, 1 Gbps fiber lounge, plaka tanımalı kapalı otopark ve 6 adet 22 kW EV şarj istasyonu.',
        specs: ['🥤 Makro Ön Sipariş', '⚡ 6× 22 kW EV Şarj', '🚗 180 Araçlık Otopark'],
        cta: 'Kafe Menüsü & EV Durumu →',
        url: siteBase + '/tesis-olanaklari/#kafe',
      },
    };

    const activateZone = (zoneKey) => {
      const info = ZONE_INFO[zoneKey];
      if (!info) return;
      fpZones.forEach((z) => z.classList.toggle('is-active', z.getAttribute('data-fp-zone') === zoneKey));
      const elTag = document.getElementById('fp-d-tag');
      const elTitle = document.getElementById('fp-d-title');
      const elDesc = document.getElementById('fp-d-desc');
      const elSpecs = document.getElementById('fp-d-specs');
      if (elTag) elTag.textContent = info.tag;
      if (elTitle) elTitle.textContent = info.title;
      if (elDesc) elDesc.textContent = info.desc;
      if (elSpecs) elSpecs.innerHTML = info.specs.map((sp) => `<span>${escHtml(sp)}</span>`).join('');
      fpCta.textContent = info.cta;
      fpCta.setAttribute('href', info.url);
      announce(`${info.title} seçildi.`);
    };

    fpZones.forEach((z) => {
      z.addEventListener('click', () => activateZone(z.getAttribute('data-fp-zone')));
      z.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          activateZone(z.getAttribute('data-fp-zone'));
        }
      });
    });
  }

  // =========================================================================
  // 15. CANLI 5 YILDIZLI ÜYE DEĞERLENDİRME FORMU (#yorumlar)
  // =========================================================================
  const starPicker = document.getElementById('apex-star-picker');
  const revRatingInp = document.getElementById('rev-rating');
  if (starPicker && revRatingInp) {
    const starBtns = starPicker.querySelectorAll('[data-star-val]');
    const paintStars = (val) => {
      revRatingInp.value = String(val);
      starBtns.forEach((b) => {
        const bv = parseInt(b.getAttribute('data-star-val'), 10);
        b.classList.toggle('is-active', bv <= val);
      });
    };
    starBtns.forEach((b) => {
      b.addEventListener('click', () => paintStars(parseInt(b.getAttribute('data-star-val'), 10)));
    });
  }

  const revForm = document.getElementById('apex-review-form');
  if (revForm) {
    const revOut = document.getElementById('rev-result');
    const revGrid = document.getElementById('apex-reviews-grid');
    revForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(revForm);
      const payload = {
        author_name: fd.get('author_name'),
        member_plan: fd.get('member_plan'),
        rating: parseInt(fd.get('rating') || '5', 10),
        comment: fd.get('comment'),
      };
      const btn = revForm.querySelector('button[type="submit"]');
      btn.disabled = true;
      btn.textContent = 'Yayınlanıyor...';
      const { ok, data } = await apexApi('reviews', { method: 'POST', body: JSON.stringify(payload) });
      btn.disabled = false;
      btn.textContent = 'Değerlendirmeyi Yayınla';

      if (ok && data.success && data.review) {
        if (revOut) revOut.innerHTML = `<span style="color:#30d158;font-weight:700;">✓ ${escHtml(data.message)}</span>`;
        const avgEl = document.getElementById('apex-rev-avg');
        const cntEl = document.getElementById('apex-rev-count');
        if (avgEl && data.average) avgEl.textContent = Number(data.average).toFixed(1);
        if (cntEl && data.count) cntEl.textContent = String(data.count);
        if (revGrid) {
          const r = data.review;
          const stars = '★'.repeat(r.rating) + '☆'.repeat(Math.max(0, 5 - r.rating));
          const card = document.createElement('article');
          card.className = 'apex-card apex-rev-card';
          card.innerHTML = `<div class="apex-card-body">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;">
              <span class="apex-stars">${stars}</span>
              <span class="apex-tag">${escHtml(r.member_plan)}</span>
            </div>
            <p style="margin:6px 0 0;font-size:0.92rem;color:var(--apex-text);">“${escHtml(r.comment)}”</p>
            <div class="apex-card-foot">
              <strong>${escHtml(r.author_name)}</strong>
              <span class="apex-muted" style="font-size:0.78rem;">${escHtml(r.created_at)}</span>
            </div>
          </div>`;
          revGrid.prepend(card);
        }
        revForm.reset();
        if (revRatingInp) revRatingInp.value = '5';
        if (starPicker) starPicker.querySelectorAll('[data-star-val]').forEach((b) => b.classList.add('is-active'));
        showToast('★ Değerlendirmeniz anında yayınlandı!');
      } else if (revOut) {
        revOut.innerHTML = `<span style="color:#ff5b73;font-weight:600;">⚠️ ${escHtml(data.message || 'Yorum gönderilemedi.')}</span>`;
      }
    });
  }

  // =========================================================================
  // 16. SAATLİK TESİS YOĞUNLUK TAHMİN GRAFİĞİ (#apex-crowd-bars)
  // =========================================================================
  const crowdBars = document.getElementById('apex-crowd-bars');
  if (crowdBars) {
    const CROWD_DATA = {
      weekday: {
        note: 'En sakin saatler: 09:00–12:00 ve 14:00–16:00 arası (%22–%38)',
        hours: [
          ['07', 42], ['08', 58], ['09', 34], ['10', 26], ['11', 28], ['12', 46],
          ['13', 44], ['14', 29], ['15', 32], ['16', 54], ['17', 74], ['18', 92],
          ['19', 96], ['20', 88], ['21', 64], ['22', 35],
        ],
      },
      sat: {
        note: 'Cumartesi öğleden sonra 15:00–18:00 arası salon ve fitness en sakin dönemdir.',
        hours: [
          ['08', 35], ['09', 56], ['10', 78], ['11', 86], ['12', 82], ['13', 68],
          ['14', 52], ['15', 40], ['16', 44], ['17', 58], ['18', 72], ['19', 64],
          ['20', 48], ['21', 30],
        ],
      },
      sun: {
        note: 'Pazar günleri özellikle 14:00 sonrası sauna, fitness ve basketbol salonları rahattır.',
        hours: [
          ['08', 24], ['09', 40], ['10', 64], ['11', 74], ['12', 70], ['13', 55],
          ['14', 42], ['15', 36], ['16', 38], ['17', 48], ['18', 52], ['19', 44],
          ['20', 30], ['21', 20],
        ],
      },
    };
    const nowHour = String(new Date().getHours()).padStart(2, '0');
    const renderCrowd = (dayKey) => {
      const cfg = CROWD_DATA[dayKey] || CROWD_DATA.weekday;
      const noteEl = document.getElementById('apex-crowd-note');
      if (noteEl) noteEl.textContent = cfg.note;
      crowdBars.innerHTML = cfg.hours.map(([h, pct]) => {
        const cls = pct >= 80 ? 'is-peak' : (pct >= 50 ? 'is-mid' : 'is-calm');
        const isNow = h === nowHour;
        return `<div class="apex-crowd-col ${isNow ? 'is-now' : ''}" role="listitem" title="${h}:00 — %${pct} tahmini doluluk">
          <div class="apex-crowd-bar-wrap">
            <div class="apex-crowd-bar ${cls}" style="height:${pct}%;"><span>%${pct}</span></div>
          </div>
          <span class="apex-crowd-lbl">${h}</span>
        </div>`;
      }).join('');
    };
    document.querySelectorAll('[data-crowd-day]').forEach((btn) => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('[data-crowd-day]').forEach((b) => b.classList.toggle('is-active', b === btn));
        renderCrowd(btn.getAttribute('data-crowd-day'));
      });
    });
    renderCrowd('weekday');
  }

  // =========================================================================
  // 17. APEX PROTEİN KAFE ANTRENMAN ÇIKIŞI ÖN SİPARİŞ SEPETİ (#apex-cafe-cart)
  // =========================================================================
  const cafeOrderForm = document.getElementById('apex-cafe-order-form');
  if (cafeOrderForm) {
    const cartItemsEl = document.getElementById('apex-cafe-cart-items');
    const summaryEl = document.getElementById('apex-cafe-macro-summary');
    const resultEl = document.getElementById('apex-cafe-order-result');
    let cart = [];

    const renderCart = () => {
      let totalQty = 0;
      let totalKcal = 0;
      let totalPro = 0;
      let totalPrice = 0;
      cart.forEach((it) => {
        totalQty += it.qty;
        totalKcal += it.kcal * it.qty;
        totalPro += it.protein * it.qty;
        totalPrice += it.price * it.qty;
      });
      if (summaryEl) {
        summaryEl.innerHTML = `
          <span class="apex-tag">${totalQty} Ürün</span>
          <span class="apex-tag apex-tag-volt">${totalKcal} kcal · ${totalPro}g Protein</span>
          <span class="apex-tag" style="background:rgba(227,24,55,0.2);color:#fff;">Toplam: ₺${totalPrice.toLocaleString('tr-TR')}</span>
        `;
      }
      if (!cartItemsEl) return;
      if (cart.length === 0) {
        cartItemsEl.innerHTML = '<span class="apex-muted">Yukarıdaki menüden dilediğiniz shake, fit bowl veya kahveyi sepete ekleyin.</span>';
        return;
      }
      cartItemsEl.innerHTML = `<div class="apex-cart-list">${cart.map((it, idx) => `
        <div class="apex-cart-row">
          <div>
            <strong>${escHtml(it.name)}</strong>
            <small class="apex-muted" style="display:block;">${it.kcal * it.qty} kcal · ${it.protein * it.qty}g protein · ₺${(it.price * it.qty).toLocaleString('tr-TR')}</small>
          </div>
          <div class="apex-cart-qty">
            <button type="button" class="apex-btn apex-btn-ghost apex-btn-sm" data-cart-dec="${idx}" aria-label="Adet azalt">−</button>
            <span>${it.qty}</span>
            <button type="button" class="apex-btn apex-btn-ghost apex-btn-sm" data-cart-inc="${idx}" aria-label="Adet artır">+</button>
            <button type="button" class="apex-btn apex-btn-outline apex-btn-sm" data-cart-del="${idx}" aria-label="Ürünü sil">✕</button>
          </div>
        </div>
      `).join('')}</div>`;
    };

    document.querySelectorAll('[data-cafe-add]').forEach((btn) => {
      btn.addEventListener('click', () => {
        const name = btn.getAttribute('data-item-name');
        const price = parseInt(btn.getAttribute('data-item-price') || '0', 10);
        const kcal = parseInt(btn.getAttribute('data-item-kcal') || '0', 10);
        const protein = parseInt(btn.getAttribute('data-item-protein') || '0', 10);
        const existing = cart.find((x) => x.name === name);
        if (existing) existing.qty = Math.min(10, existing.qty + 1);
        else cart.push({ name, price, kcal, protein, qty: 1 });
        renderCart();
        showToast(`🥤 ${name} sepete eklendi.`);
      });
    });

    if (cartItemsEl) {
      cartItemsEl.addEventListener('click', (e) => {
        const inc = e.target.closest('[data-cart-inc]');
        const dec = e.target.closest('[data-cart-dec]');
        const del = e.target.closest('[data-cart-del]');
        if (inc) {
          const i = +inc.getAttribute('data-cart-inc');
          if (cart[i]) cart[i].qty = Math.min(10, cart[i].qty + 1);
          renderCart();
        } else if (dec) {
          const i = +dec.getAttribute('data-cart-dec');
          if (cart[i]) {
            cart[i].qty -= 1;
            if (cart[i].qty <= 0) cart.splice(i, 1);
          }
          renderCart();
        } else if (del) {
          cart.splice(+del.getAttribute('data-cart-del'), 1);
          renderCart();
        }
      });
    }

    cafeOrderForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      if (cart.length === 0) {
        if (resultEl) resultEl.innerHTML = '<div class="apex-alert apex-alert-error">⚠️ Lütfen önce yukarıdaki menüden sepete en az 1 ürün ekleyin.</div>';
        return;
      }
      const fd = new FormData(cafeOrderForm);
      const btn = cafeOrderForm.querySelector('button[type="submit"]');
      btn.disabled = true;
      btn.textContent = 'Baristaya İletiliyor...';
      const { ok, data } = await apexApi('cafe-order', {
        method: 'POST',
        body: JSON.stringify({
          customer_name: fd.get('customer_name'),
          customer_phone: fd.get('customer_phone'),
          pickup_time: fd.get('pickup_time'),
          notes: fd.get('notes'),
          items: cart,
        }),
      });
      btn.disabled = false;
      btn.textContent = 'Ön Siparişi Baristaya Gönder';
      if (ok && data.success) {
        if (resultEl) {
          resultEl.innerHTML = `<div class="apex-alert apex-alert-success">✅ <strong>${escHtml(data.message)}</strong><br>Toplam: <strong>${escHtml(data.total_label)}</strong> · ${data.total_kcal} kcal · ${data.total_protein}g Protein</div>`;
        }
        cart = [];
        renderCart();
        cafeOrderForm.reset();
        showToast(`✓ Kafe siparişiniz alındı: ${data.order_code}`);
      } else if (resultEl) {
        resultEl.innerHTML = `<div class="apex-alert apex-alert-error">⚠️ ${escHtml(data.message || 'Sipariş iletilemedi.')}</div>`;
      }
    });
  }

  // =========================================================================
  // 18. 3 SORULUK AKILLI PAKET & PT BULUCU QUIZ (#paket-bulucu)
  // =========================================================================
  const quizBody = document.getElementById('apex-quiz-body');
  if (quizBody) {
    const stepLabel = document.getElementById('quiz-step-label');
    const resultBox = document.getElementById('apex-quiz-result');
    const answers = {};

    const showStep = (stepNum) => {
      quizBody.querySelectorAll('.apex-quiz-step').forEach((s) => {
        s.classList.toggle('is-active', Number(s.getAttribute('data-quiz-step')) === stepNum);
      });
      if (resultBox) resultBox.hidden = true;
      if (stepLabel) stepLabel.textContent = `Soru ${stepNum} / 3`;
    };

    const showQuizResult = () => {
      quizBody.querySelectorAll('.apex-quiz-step').forEach((s) => s.classList.remove('is-active'));
      if (stepLabel) stepLabel.textContent = 'Kişisel Önerin Hazır';

      let planSlug = 'pro';
      let planTitle = 'APEX PRO';
      let priceTxt = '₺2.490 / ay';
      let reason = 'Haftalık düzenli antrenman, sınırsız grup dersleri, %15 kafe ve %10 salon indirimi ile en dengeli paket.';
      let coach = 'Elif Karaca (Fonksiyonel & HIIT)';

      if (answers.pt === 'vip' || (answers.freq === '6' && answers.pt !== 'none')) {
        planSlug = 'elite';
        planTitle = 'APEX ELITE + PT';
        priceTxt = '₺4.990 / ay';
        reason = 'Ayda 8 birebir PT seansı, koçunun hazırladığı canlı beslenme takvimi, 1-1 koç sohbeti ve VIP otopark/sauna ayrıcalığı.';
        coach = answers.goal === 'court' ? 'Emre Demir (Basketbol & Voleybol Performans)' : (answers.goal === 'wellness' ? 'Selin Yılmaz (Reformer & Postür)' : 'Kerem Alkan (Kuvvet & Hipertrofi)');
      } else if (answers.pt === 'none' && answers.freq === '2') {
        planSlug = 'classic';
        planTitle = 'APEX CLASSIC';
        priceTxt = '₺1.490 / ay';
        reason = 'Haftada 2 gün 7/24 fitness, akıllı RFID dolap, yağmur duş, Fin saunası ve dijital QR kartı için en ekonomik başlangıç.';
        coach = 'Başlangıç InBody Değerlendirmesi + Apex AI Koç';
      } else {
        if (answers.goal === 'court') coach = 'Emre Demir (Atletik Patlayıcılık & Salon Performansı)';
        else if (answers.goal === 'muscle') coach = 'Kerem Alkan (Kuvvet & Hipertrofi)';
        else if (answers.goal === 'wellness') coach = 'Selin Yılmaz (Reformer Pilates & Mobilite)';
      }

      if (resultBox) {
        resultBox.hidden = false;
        resultBox.innerHTML = `
          <div class="apex-quiz-res-inner">
            <div>
              <span class="apex-eyebrow" style="margin-bottom:4px;">SANA EN UYGUN PAKET</span>
              <h4 class="apex-h2" style="margin:0 0 6px;">${planTitle} <span style="color:var(--apex-crimson);font-size:1.15rem;">(${priceTxt})</span></h4>
              <p class="apex-muted" style="margin:0 0 10px;">${reason}</p>
              <div class="apex-tags"><span class="apex-tag apex-tag-volt">🏅 Önerilen Koç: ${coach}</span></div>
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
              <button type="button" class="apex-btn apex-btn-primary" id="apex-quiz-apply-plan" data-slug="${planSlug}">Bu Paketi Seç &amp; Kayıt Ol →</button>
              <button type="button" class="apex-btn apex-btn-outline apex-btn-sm" id="apex-quiz-reset">Testi Tekrarla</button>
            </div>
          </div>
        `;
        document.getElementById('apex-quiz-apply-plan')?.addEventListener('click', (ev) => {
          const slug = ev.currentTarget.getAttribute('data-slug');
          const sel = document.getElementById('mem-plan-select');
          if (sel && slug) sel.value = slug;
          setAuthMode('register');
          document.getElementById('kayit')?.scrollIntoView({ behavior: 'smooth' });
          showToast(`🎯 ${planTitle} kayıt formunda seçildi!`);
        });
        document.getElementById('apex-quiz-reset')?.addEventListener('click', () => showStep(1));
      }
    };

    quizBody.querySelectorAll('[data-quiz-pick]').forEach((btn) => {
      btn.addEventListener('click', () => {
        const key = btn.getAttribute('data-quiz-pick');
        answers[key] = btn.getAttribute('data-val');
        if (key === 'goal') showStep(2);
        else if (key === 'freq') showStep(3);
        else showQuizResult();
      });
    });
  }

  // =========================================================================
  // 19. ÜYE PANELİ HAFTALIK ANTRENMAN KAYDI (#apex-workout-log-form)
  // =========================================================================
  const wkForm = document.getElementById('apex-workout-log-form');
  if (wkForm) {
    wkForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(wkForm);
      const btn = wkForm.querySelector('button[type="submit"]');
      btn.disabled = true;
      btn.textContent = 'Kaydediliyor...';
      const { ok, data } = await apexApi('workout-log', {
        method: 'POST',
        body: JSON.stringify({
          workout_type: fd.get('workout_type'),
          duration_min: parseInt(fd.get('duration_min') || '60', 10),
          calories: parseInt(fd.get('calories') || '480', 10),
          notes: fd.get('notes'),
        }),
      });
      btn.disabled = false;
      btn.textContent = '+ Bugünkü Antrenmanı Kaydet';
      if (ok && data.success && data.entry) {
        const list = document.getElementById('apex-wk-list');
        if (list) {
          const pct = Math.max(12, Math.min(100, Math.round((data.entry.calories / 750) * 100)));
          const row = document.createElement('div');
          row.className = 'apex-wk-row';
          row.innerHTML = `
            <div style="display:flex;justify-content:space-between;font-size:0.86rem;margin-bottom:5px;">
              <strong>${escHtml(data.entry.workout_type)} <small class="apex-muted">(Bugün)</small></strong>
              <span>${data.entry.duration_min} dk · <strong style="color:var(--apex-crimson);">${data.entry.calories} kcal</strong></span>
            </div>
            <div style="height:7px;background:var(--apex-bg);border-radius:99px;overflow:hidden;">
              <div style="width:${pct}%;height:100%;background:linear-gradient(90deg,#e31837,#f4e268);border-radius:99px;"></div>
            </div>
          `;
          list.prepend(row);
        }
        const kpis = document.getElementById('apex-wk-kpis');
        if (kpis) {
          const ns = parseInt(kpis.dataset.sessions || '0', 10) + 1;
          const nm = parseInt(kpis.dataset.minutes || '0', 10) + parseInt(data.entry.duration_min || '0', 10);
          const nk = parseInt(kpis.dataset.kcal || '0', 10) + parseInt(data.entry.calories || '0', 10);
          kpis.dataset.sessions = String(ns);
          kpis.dataset.minutes = String(nm);
          kpis.dataset.kcal = String(nk);
          const eS = document.getElementById('apex-wk-kpi-sessions');
          const eM = document.getElementById('apex-wk-kpi-min');
          const eK = document.getElementById('apex-wk-kpi-kcal');
          if (eS) eS.textContent = `${ns} Seans`;
          if (eM) eM.textContent = `⏱ ${nm} dk`;
          if (eK) eK.textContent = `🔥 ${nk.toLocaleString('tr-TR')} kcal`;
          const gTxt = document.getElementById('apex-wk-goal-txt');
          const gFill = document.getElementById('apex-wk-goal-fill');
          const avgM = document.getElementById('apex-wk-avg-min');
          const avgK = document.getElementById('apex-wk-avg-kcal');
          const gPct = Math.min(100, Math.round((nk / 3500) * 100));
          if (gTxt) gTxt.textContent = `${nk.toLocaleString('tr-TR')} / 3.500 kcal (%${gPct})`;
          if (gFill) gFill.style.width = Math.max(6, gPct) + '%';
          if (avgM) avgM.textContent = `${Math.round(nm / Math.max(1, ns))} dk`;
          if (avgK) avgK.textContent = `${Math.round(nk / Math.max(1, ns))} kcal`;
        }
        wkForm.reset();
        showToast('🔥 ' + data.message);
      } else {
        showToast('⚠️ ' + (data.message || 'Antrenman kaydedilemedi.'));
      }
    });

    wkForm.querySelectorAll('[data-wk-preset]').forEach((pb) => {
      pb.addEventListener('click', () => {
        const [t, d, k] = (pb.getAttribute('data-wk-preset') || '').split('|');
        const sel = document.getElementById('wk-type');
        const dur = document.getElementById('wk-dur');
        const kcal = document.getElementById('wk-kcal');
        if (sel && t) sel.value = t;
        if (dur && d) dur.value = d;
        if (kcal && k) kcal.value = k;
        wkForm.querySelectorAll('[data-wk-preset]').forEach((x) => x.classList.toggle('is-active', x === pb));
      });
    });
  }

  // =========================================================================
  // 20. ÜYE ↔ KOÇ BİREBİR CANLI MESAJLAŞMA (#apex-member-coach-chat-form)
  // =========================================================================
  const memberChatForm = document.getElementById('apex-member-coach-chat-form');
  if (memberChatForm) {
    const chatBox = document.getElementById('apex-member-chat-box');
    const chatRail = document.getElementById('apex-member-chat-scrollbar');
    const chatThumb = document.getElementById('apex-member-chat-thumb');
    const chatInput = document.getElementById('member-coach-msg-input');

    // Özel Kırmızı Neon Glow Kaydırma Çubuğu (sürükle & tıkla destekli)
    function syncMemberChatScroll() {
      if (!chatBox || !chatRail || !chatThumb) return;
      const viewH = chatBox.clientHeight;
      const totalH = chatBox.scrollHeight;
      const maxScroll = totalH - viewH;
      if (maxScroll <= 2 || viewH <= 0) {
        chatRail.classList.add('is-hidden');
        return;
      }
      chatRail.classList.remove('is-hidden');
      const track = chatThumb.parentElement;
      const trackH = track ? track.clientHeight : Math.max(0, viewH - 20);
      if (trackH <= 0) return;
      const thumbH = Math.max(36, Math.round((viewH / totalH) * trackH));
      const maxThumbTop = Math.max(0, trackH - thumbH);
      const ratio = Math.min(1, Math.max(0, chatBox.scrollTop / maxScroll));
      chatThumb.style.height = thumbH + 'px';
      chatThumb.style.transform = `translate3d(0, ${(ratio * maxThumbTop).toFixed(1)}px, 0)`;
      chatRail.setAttribute('aria-valuenow', String(Math.round(ratio * 100)));
    }

    if (chatBox) {
      let mcRaf = null;
      const scheduleMcSync = () => {
        if (mcRaf) return;
        mcRaf = requestAnimationFrame(() => {
          mcRaf = null;
          syncMemberChatScroll();
        });
      };
      chatBox.scrollTop = chatBox.scrollHeight;
      scheduleMcSync();
      chatBox.addEventListener('scroll', scheduleMcSync, { passive: true });
      window.addEventListener('resize', scheduleMcSync, { passive: true });
      if (typeof ResizeObserver !== 'undefined') new ResizeObserver(scheduleMcSync).observe(chatBox);
      if (typeof MutationObserver !== 'undefined') new MutationObserver(scheduleMcSync).observe(chatBox, { childList: true, subtree: true });

      if (chatRail && chatThumb) {
        let dragging = false;
        let dragStartY = 0;
        let dragStartScroll = 0;
        chatThumb.addEventListener('pointerdown', (e) => {
          e.preventDefault();
          e.stopPropagation();
          dragging = true;
          dragStartY = e.clientY;
          dragStartScroll = chatBox.scrollTop;
          chatRail.classList.add('is-dragging');
          if (chatThumb.setPointerCapture) chatThumb.setPointerCapture(e.pointerId);
        });
        chatThumb.addEventListener('pointermove', (e) => {
          if (!dragging) return;
          const track = chatThumb.parentElement;
          const trackH = track ? track.clientHeight : 0;
          const maxThumbTop = Math.max(1, trackH - chatThumb.offsetHeight);
          const maxScroll = Math.max(0, chatBox.scrollHeight - chatBox.clientHeight);
          chatBox.scrollTop = dragStartScroll + ((e.clientY - dragStartY) / maxThumbTop) * maxScroll;
        });
        const endDrag = (e) => {
          if (!dragging) return;
          dragging = false;
          chatRail.classList.remove('is-dragging');
          if (e && chatThumb.releasePointerCapture && chatThumb.hasPointerCapture && chatThumb.hasPointerCapture(e.pointerId)) {
            chatThumb.releasePointerCapture(e.pointerId);
          }
        };
        chatThumb.addEventListener('pointerup', endDrag);
        chatThumb.addEventListener('pointercancel', endDrag);
        chatRail.addEventListener('pointerdown', (e) => {
          if (e.target === chatThumb) return;
          const track = chatThumb.parentElement;
          if (!track) return;
          const rect = track.getBoundingClientRect();
          const thumbH = chatThumb.offsetHeight;
          const maxThumbTop = Math.max(1, rect.height - thumbH);
          const clickPos = Math.min(maxThumbTop, Math.max(0, e.clientY - rect.top - thumbH / 2));
          const maxScroll = Math.max(0, chatBox.scrollHeight - chatBox.clientHeight);
          chatBox.scrollTo({ top: (clickPos / maxThumbTop) * maxScroll, behavior: 'smooth' });
        });
      }
    }

    memberChatForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const text = (chatInput?.value || '').trim();
      if (!text) return;
      const btn = memberChatForm.querySelector('button[type="submit"]');
      btn.disabled = true;
      const { ok, data } = await apexApi('member/message', {
        method: 'POST',
        body: JSON.stringify({ message: text }),
      });
      btn.disabled = false;
      if (ok && data.success && data.item) {
        if (chatBox) {
          const b = document.createElement('div');
          b.className = 'apex-mchat-bubble is-me';
          b.innerHTML = `${escHtml(data.item.message)}<small>${escHtml(data.item.sender_name)} · ${escHtml(data.item.time)}</small>`;
          chatBox.appendChild(b);
          chatBox.scrollTop = chatBox.scrollHeight;
        }
        if (chatInput) chatInput.value = '';
        showToast('💬 Mesajınız koçunuza iletildi!');
      } else {
        showToast('⚠️ ' + (data.message || 'Mesaj gönderilemedi.'));
      }
    });
  }
})();
