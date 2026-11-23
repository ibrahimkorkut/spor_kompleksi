/**
 * APEX ARENA — Canlı Rezervasyon, PT Randevu, Üyelik & İletişim REST İstemcisi
 */
(function () {
  'use strict';
  const cfg = window.APEX_CONFIG || { restUrl: '/wp-json/apex/v1/', nonce: '', refereeFee: 450 };

  function formatTL(num) {
    return '₺' + Number(num || 0).toLocaleString('tr-TR');
  }

  async function apiFetch(endpoint, options = {}) {
    const res = await fetch(cfg.restUrl + endpoint, {
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': cfg.nonce || '',
        ...(options.headers || {}),
      },
      ...options,
    });
    const data = await res.json().catch(() => ({ success: false, message: 'Sunucu yanıtı okunamadı.' }));
    return { ok: res.ok, status: res.status, data };
  }

  // =========================================================================
  // 1. SALON KİRALAMA SİSTEMİ (Basketbol x2, Voleybol x1 + Hakem Opsiyonu)
  // =========================================================================
  const courtForm = document.getElementById('apex-court-booking-form');
  if (courtForm) {
    const selCourt = document.getElementById('court-select');
    const inpDate = document.getElementById('court-date');
    const selDur = document.getElementById('court-duration');
    const chkRef = document.getElementById('court-referee');
    const inpSlot = document.getElementById('court-time-slot');
    const slotsBox = document.getElementById('court-slots-container');
    const resBox = document.getElementById('court-booking-result');

    function updateSummary() {
      const opt = selCourt.options[selCourt.selectedIndex];
      const rate = parseInt(opt?.dataset.rate || '1500', 10);
      const refUnit = parseInt(opt?.dataset.referee || cfg.refereeFee, 10);
      const dur = parseInt(selDur.value || '1', 10);
      const needsRef = chkRef.checked;
      const base = rate * dur;
      const refFee = needsRef ? refUnit : 0;
      const total = base + refFee;

      document.getElementById('ref-fee-label').textContent = formatTL(refUnit);
      document.getElementById('sum-court').textContent = (opt?.textContent || '').split('—')[0].trim();
      document.getElementById('sum-datetime').textContent = inpSlot.value ? `${inpDate.value} · ${inpSlot.value}` : `${inpDate.value} · Saat seçiniz`;
      document.getElementById('sum-duration').textContent = `${dur} Saat`;
      document.getElementById('sum-base').textContent = formatTL(base);
      document.getElementById('sum-referee').textContent = needsRef ? `Dahil (+${formatTL(refFee)})` : 'Dahil Değil (₺0)';
      document.getElementById('sum-total').textContent = formatTL(total);
    }

    async function loadCourtSlots() {
      slotsBox.innerHTML = '<span class="apex-muted">Canlı saatler kontrol ediliyor...</span>';
      inpSlot.value = '';
      const { ok, data } = await apiFetch(`availability?type=court&id=${encodeURIComponent(selCourt.value)}&date=${encodeURIComponent(inpDate.value)}`);
      if (!ok || !data.slots) {
        slotsBox.innerHTML = '<span class="apex-muted">Saatler alınamadı.</span>';
        return;
      }
      slotsBox.innerHTML = '';
      data.slots.forEach((s) => {
        const b = document.createElement('button');
        b.type = 'button';
        b.className = 'apex-slot-btn';
        b.textContent = s.available ? s.time : `${s.time} DOLU`;
        b.disabled = !s.available;
        if (s.available) {
          b.addEventListener('click', () => {
            slotsBox.querySelectorAll('.apex-slot-btn').forEach((x) => x.classList.remove('is-selected'));
            b.classList.add('is-selected');
            inpSlot.value = s.time;
            updateSummary();
          });
        }
        slotsBox.appendChild(b);
      });
      updateSummary();
    }

    document.querySelectorAll('[data-select-court]').forEach((btn) => {
      btn.addEventListener('click', () => {
        selCourt.value = btn.getAttribute('data-select-court');
        loadCourtSlots();
        document.getElementById('rezervasyon')?.scrollIntoView({ behavior: 'smooth' });
      });
    });

    selCourt.addEventListener('change', loadCourtSlots);
    inpDate.addEventListener('change', loadCourtSlots);
    selDur.addEventListener('change', updateSummary);
    chkRef.addEventListener('change', updateSummary);
    loadCourtSlots();

    courtForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      if (!inpSlot.value) {
        resBox.innerHTML = '<div class="apex-alert apex-alert-error">Lütfen yukarıdan müsait bir saat dilimi seçin.</div>';
        return;
      }
      const fd = new FormData(courtForm);
      const payload = {
        court_id: parseInt(fd.get('court_id'), 10),
        date: fd.get('date'),
        duration: parseInt(fd.get('duration'), 10),
        time_slot: fd.get('time_slot'),
        needs_referee: chkRef.checked,
        customer_name: fd.get('customer_name'),
        customer_email: fd.get('customer_email'),
        customer_phone: fd.get('customer_phone'),
        notes: fd.get('notes'),
      };
      const btn = courtForm.querySelector('button[type="submit"]');
      btn.disabled = true;
      btn.textContent = 'KAYDEDİLİYOR...';

      const { ok, data } = await apiFetch('book-court', { method: 'POST', body: JSON.stringify(payload) });
      btn.disabled = false;
      btn.textContent = 'SALON REZERVASYONUNU ONAYLA';

      if (ok && data.success) {
        resBox.innerHTML = `<div class="apex-alert apex-alert-success">✅ <strong>${data.message}</strong><br>${data.court_name} · ${data.date} ${data.time_slot} · ${data.referee ? '⚖️ Hakem Dahil · ' : ''}Toplam: <strong>${data.total_label}</strong></div>`;
        loadCourtSlots();
      } else {
        resBox.innerHTML = `<div class="apex-alert apex-alert-error">⚠️ ${data.message || 'Rezervasyon tamamlanamadı.'}</div>`;
      }
    });
  }

  // =========================================================================
  // 2. PT EĞİTMEN ONLİNE RANDEVU & ANTRENÖR CANLI TAKVİMİ
  // =========================================================================
  const ptForm = document.getElementById('apex-pt-booking-form');
  if (ptForm) {
    const trSel = document.getElementById('pt-trainer-select');
    const pkgSel = document.getElementById('pt-package-select');
    const typeSel = document.getElementById('pt-type-select');
    const dtInp = document.getElementById('pt-date-input');
    const slotInp = document.getElementById('pt-time-slot');
    const slotsBox = document.getElementById('pt-slots-container');
    const resBox = document.getElementById('pt-booking-result');

    async function loadPtSlots() {
      if (!slotsBox || !trSel || !dtInp) return;
      slotsBox.innerHTML = '<span class="apex-muted">Antrenör takvimi kontrol ediliyor...</span>';
      slotInp.value = '';
      const { ok, data } = await apiFetch(`availability?type=trainer&id=${encodeURIComponent(trSel.value)}&date=${encodeURIComponent(dtInp.value)}`);
      if (!ok || !data.slots) return;
      slotsBox.innerHTML = '';
      data.slots.forEach((s) => {
        const b = document.createElement('button');
        b.type = 'button';
        b.className = 'apex-slot-btn';
        b.textContent = s.available ? s.time : `${s.time} DOLU`;
        b.disabled = !s.available;
        if (s.available) {
          b.addEventListener('click', () => {
            slotsBox.querySelectorAll('.apex-slot-btn').forEach((x) => x.classList.remove('is-selected'));
            b.classList.add('is-selected');
            slotInp.value = s.time;
          });
        }
        slotsBox.appendChild(b);
      });
    }

    document.querySelectorAll('[data-pick-trainer]').forEach((card) => {
      card.addEventListener('click', () => {
        const id = card.getAttribute('data-pick-trainer');
        document.querySelectorAll('[data-pick-trainer]').forEach((c) => c.classList.remove('apex-card-featured'));
        card.classList.add('apex-card-featured');
        if (trSel) { trSel.value = id; loadPtSlots(); }
      });
    });

    document.querySelectorAll('[data-select-pt-package]').forEach((btn) => {
      btn.addEventListener('click', () => {
        if (pkgSel) pkgSel.value = btn.getAttribute('data-select-pt-package');
        if (typeSel && btn.getAttribute('data-pt-type')) typeSel.value = btn.getAttribute('data-pt-type');
        document.getElementById('randevu')?.scrollIntoView({ behavior: 'smooth' });
      });
    });

    if (trSel && trSel.tagName === 'SELECT') trSel.addEventListener('change', loadPtSlots);
    if (dtInp) dtInp.addEventListener('change', loadPtSlots);
    loadPtSlots();

    ptForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      if (!slotInp.value) {
        resBox.innerHTML = '<div class="apex-alert apex-alert-error">Lütfen antrenör takviminden müsait bir saat seçin.</div>';
        return;
      }
      const fd = new FormData(ptForm);
      const payload = Object.fromEntries(fd.entries());
      const btn = ptForm.querySelector('button[type="submit"]');
      btn.disabled = true;
      btn.textContent = 'KAYDEDİLİYOR...';

      const { ok, data } = await apiFetch('book-pt', { method: 'POST', body: JSON.stringify(payload) });
      btn.disabled = false;
      btn.textContent = 'ONLİNE PT RANDEVUSUNU ONAYLA';

      if (ok && data.success) {
        resBox.innerHTML = `<div class="apex-alert apex-alert-success">✅ <strong>${data.message}</strong><br>${data.trainer_name} · ${data.package_name} · ${data.date} ${data.time_slot}</div>`;
        loadPtSlots();
      } else {
        resBox.innerHTML = `<div class="apex-alert apex-alert-error">⚠️ ${data.message || 'Randevu oluşturulamadı.'}</div>`;
      }
    });
  }

  // =========================================================================
  // 3. ÜYELİK KAYIT, GİRİŞ & REZERVASYON İPTAL
  // =========================================================================
  const memRegForm = document.getElementById('apex-membership-register-form');
  if (memRegForm) {
    const out = document.getElementById('mem-register-result');
    memRegForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(memRegForm);
      const payload = Object.fromEntries(fd.entries());
      payload.kvkk_consent = fd.get('kvkk_consent') === '1';
      payload.terms_consent = fd.get('terms_consent') === '1';

      const { ok, data } = await apiFetch('membership/register', { method: 'POST', body: JSON.stringify(payload) });
      if (ok && data.success) {
        out.innerHTML = `<div class="apex-alert apex-alert-success">🎉 ${data.message}</div>`;
        setTimeout(() => { window.location.href = data.redirect || window.location.href; }, 1200);
      } else {
        out.innerHTML = `<div class="apex-alert apex-alert-error">⚠️ ${data.message || 'Kayıt başarısız.'}</div>`;
      }
    });
  }

  const loginForm = document.getElementById('apex-member-login-form');
  const demoFillBtn = document.getElementById('apex-fill-demo-login');
  if (demoFillBtn) {
    demoFillBtn.addEventListener('click', () => {
      document.getElementById('login-user').value = 'uye_demo';
      document.getElementById('login-pass').value = 'ApexUye2026!';
    });
  }
  if (loginForm) {
    const out = document.getElementById('mem-login-result');
    loginForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(loginForm);
      const { ok, data } = await apiFetch('membership/login', {
        method: 'POST',
        body: JSON.stringify(Object.fromEntries(fd.entries())),
      });
      if (ok && data.success) {
        out.innerHTML = `<div class="apex-alert apex-alert-success">✅ ${data.message}</div>`;
        setTimeout(() => { window.location.href = data.redirect || window.location.href; }, 700);
      } else {
        out.innerHTML = `<div class="apex-alert apex-alert-error">⚠️ ${data.message || 'Giriş başarısız.'}</div>`;
      }
    });
  }

  document.querySelectorAll('[data-cancel-booking]').forEach((btn) => {
    btn.addEventListener('click', async () => {
      if (!window.confirm('Bu rezervasyonu iptal etmek istediğinize emin misiniz?')) return;
      const { ok } = await apiFetch('booking/cancel', {
        method: 'POST',
        body: JSON.stringify({
          id: parseInt(btn.getAttribute('data-cancel-booking'), 10),
          kind: btn.getAttribute('data-booking-kind'),
        }),
      });
      if (ok) window.location.reload();
    });
  });

  // =========================================================================
  // 4. İLETİŞİM FORMU
  // =========================================================================
  const contactForm = document.getElementById('apex-contact-form');
  if (contactForm) {
    const out = document.getElementById('contact-result');
    contactForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(contactForm);
      const payload = Object.fromEntries(fd.entries());
      payload.kvkk_consent = fd.get('kvkk_consent') === '1';
      const { ok, data } = await apiFetch('contact', { method: 'POST', body: JSON.stringify(payload) });
      if (ok && data.success) {
        out.innerHTML = `<div class="apex-alert apex-alert-success">✅ ${data.message}</div>`;
        contactForm.reset();
      } else {
        out.innerHTML = `<div class="apex-alert apex-alert-error">⚠️ ${data.message || 'Gönderilemedi.'}</div>`;
      }
    });
  }
})();
