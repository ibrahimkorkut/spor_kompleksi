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
        const waMsg = encodeURIComponent(
          `🏀 APEX ARENA Maç Daveti!\nSalon: ${data.court_name}\nTarih & Saat: ${data.date} · ${data.time_slot} (${payload.duration} saat)${data.referee ? '\n⚖️ Lisanslı Hakem Dahil' : ''}\nKod: ${data.booking_code}\nKonum: Barbaros Mah. Spor Bulvarı No: 42, Ataşehir / İstanbul`
        );
        const mailBadge = data.mail_status === 'sent'
          ? '<div style="margin-top:6px;font-size:0.84rem;color:#a4ff2e;">📧 Rezervasyon onay e-postası Gmail adresinize gönderildi.</div>'
          : (data.mail_status === 'queued_local' ? '<div style="margin-top:6px;font-size:0.84rem;opacity:0.88;">📧 Onay ve 24 saat hatırlatma e-postası oluşturuldu (Üye Panelinden önizlenebilir).</div>' : '');
        resBox.innerHTML = `<div class="apex-alert apex-alert-success">
          ✅ <strong>${data.message}</strong><br>${data.court_name} · ${data.date} ${data.time_slot} · ${data.referee ? '⚖️ Hakem Dahil · ' : ''}Toplam: <strong>${data.total_label}</strong>${mailBadge}
          <div class="apex-confirm-actions">
            <button type="button" class="apex-btn apex-btn-ghost apex-btn-sm" id="apex-court-ics-dl">📅 Takvime Ekle (.ics)</button>
            <a href="https://wa.me/?text=${waMsg}" target="_blank" rel="noopener noreferrer" class="apex-btn apex-btn-sm apex-btn-wa-share">💬 WhatsApp ile Maç Daveti Paylaş</a>
          </div>
        </div>`;
        document.getElementById('apex-court-ics-dl')?.addEventListener('click', () => {
          if (window.apexDownloadIcs) {
            window.apexDownloadIcs({
              title: `APEX ARENA — ${data.court_name}`,
              date: payload.date,
              time: data.time_slot,
              hours: payload.duration,
              desc: `Rezervasyon Kodu: ${data.booking_code} · ${data.referee ? 'Lisanslı Hakem Dahil · ' : ''}Toplam: ${data.total_label}`,
            });
          }
        });
        if (window.apexToast) window.apexToast(`✓ ${data.court_name} rezervasyonunuz onaylandı!`);
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
        const waPtMsg = encodeURIComponent(
          `🏋️ APEX ARENA PT Randevum Onaylandı!\nAntrenör: ${data.trainer_name}\nPaket: ${data.package_name}\nTarih & Saat: ${data.date} · ${data.time_slot}\nKod: ${data.booking_code}`
        );
        const ptMailBadge = data.mail_status === 'sent'
          ? '<div style="margin-top:6px;font-size:0.84rem;color:#a4ff2e;">📧 PT randevu onayı e-posta adresinize gönderildi.</div>'
          : (data.mail_status === 'queued_local' ? '<div style="margin-top:6px;font-size:0.84rem;opacity:0.88;">📧 PT onay ve hatırlatma e-postası oluşturuldu (Üye Panelinden önizlenebilir).</div>' : '');
        resBox.innerHTML = `<div class="apex-alert apex-alert-success">
          ✅ <strong>${data.message}</strong><br>${data.trainer_name} · ${data.package_name} · ${data.date} ${data.time_slot}${ptMailBadge}
          <div class="apex-confirm-actions">
            <button type="button" class="apex-btn apex-btn-ghost apex-btn-sm" id="apex-pt-ics-dl">📅 Takvime Ekle (.ics)</button>
            <a href="https://wa.me/?text=${waPtMsg}" target="_blank" rel="noopener noreferrer" class="apex-btn apex-btn-sm apex-btn-wa-share">💬 WhatsApp’ta Paylaş</a>
          </div>
        </div>`;
        document.getElementById('apex-pt-ics-dl')?.addEventListener('click', () => {
          if (window.apexDownloadIcs) {
            window.apexDownloadIcs({
              title: `APEX ARENA PT — ${data.trainer_name}`,
              date: payload.date,
              time: data.time_slot,
              hours: 1,
              desc: `PT Randevu Kodu: ${data.booking_code} · Paket: ${data.package_name}`,
            });
          }
        });
        if (window.apexToast) window.apexToast(`✓ ${data.trainer_name} PT randevunuz onaylandı!`);
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
      payload.reminder_opt_in = fd.get('reminder_opt_in') === '1';

      const { ok, data } = await apiFetch('membership/register', { method: 'POST', body: JSON.stringify(payload) });
      if (ok && data.success) {
        out.innerHTML = `<div class="apex-alert apex-alert-success">🎉 ${data.message}</div>`;
        setTimeout(() => { window.location.href = data.redirect || window.location.href; }, 1600);
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
        if (window.apexToast) window.apexToast('✓ Mesajınız APEX ARENA ekibine iletildi.');
        contactForm.reset();
      } else {
        out.innerHTML = `<div class="apex-alert apex-alert-error">⚠️ ${data.message || 'Gönderilemedi.'}</div>`;
      }
    });
  }

  // =========================================================================
  // 5. ÜYE PANELİ E-POSTA BİLDİRİMLERİ, HATIRLATMA & HTML ÖNİZLEME (v2.1.0)
  // =========================================================================
  const mailPanel = document.getElementById('eposta-hatirlatma');
  if (mailPanel) {
    const remToggle = document.getElementById('apex-member-rem-toggle');
    const mailStatus = document.getElementById('apex-member-mail-status');
    const mailList = document.getElementById('apex-member-email-list');
    const mailModal = document.getElementById('apex-member-mail-modal');
    const mailFrame = document.getElementById('apex-mm-frame');
    const mailTitle = document.getElementById('apex-mm-title');

    function decodeB64Utf8(b64) {
      try {
        const bin = atob(b64);
        const bytes = Uint8Array.from(bin, (c) => c.charCodeAt(0));
        return new TextDecoder('utf-8').decode(bytes);
      } catch (e) {
        return '';
      }
    }

    function openMailPreview(subject, html) {
      if (!mailModal || !mailFrame) return;
      if (mailTitle) mailTitle.textContent = subject || 'E-posta Önizleme';
      mailFrame.srcdoc = html || '';
      mailModal.hidden = false;
    }

    function bindPreviewButtons(scope) {
      (scope || mailPanel).querySelectorAll('[data-preview-mail]').forEach((btn) => {
        if (btn.dataset.bound) return;
        btn.dataset.bound = '1';
        btn.addEventListener('click', () => {
          const html = btn.dataset.mailHtml || decodeB64Utf8(btn.dataset.mailB64 || '');
          openMailPreview(btn.dataset.mailSubject || 'E-posta Önizleme', html);
        });
      });
    }
    bindPreviewButtons(mailPanel);

    mailModal?.querySelector('[data-close-modal]')?.addEventListener('click', () => { mailModal.hidden = true; });
    mailModal?.addEventListener('click', (e) => { if (e.target === mailModal) mailModal.hidden = true; });

    async function refreshMemberEmails() {
      const { ok, data } = await apiFetch('member/emails');
      if (!ok || !data.success || !mailList) return;
      if (!data.emails || !data.emails.length) return;
      mailList.innerHTML = '';
      data.emails.slice(0, 6).forEach((em) => {
        const row = document.createElement('div');
        row.className = 'apex-mail-row';
        const stLbl = em.status === 'sent' ? '✓ Gmail İletildi' : (em.status === 'failed' ? '✕ Hata' : '● Hazır');
        row.innerHTML = `<div class="apex-mail-row-info">
          <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:3px;">
            <span class="apex-tag" style="color:${em.type_color};">${em.type_label}</span>
            <small class="apex-muted">${em.created_at} · ${stLbl}</small>
          </div>
          <strong style="font-size:0.9rem;display:block;"></strong>
        </div>
        <button type="button" class="apex-btn apex-btn-outline apex-btn-sm" data-preview-mail>👁 Maili Gör</button>`;
        row.querySelector('strong').textContent = em.subject;
        const pBtn = row.querySelector('[data-preview-mail]');
        pBtn.dataset.mailSubject = em.subject;
        pBtn.dataset.mailHtml = em.body_html || '';
        mailList.appendChild(row);
      });
      bindPreviewButtons(mailList);
    }

    if (remToggle) {
      remToggle.addEventListener('change', async () => {
        const { ok, data } = await apiFetch('member/mail-action', {
          method: 'POST',
          body: JSON.stringify({ action: 'save_prefs', reminder_opt_in: remToggle.checked }),
        });
        if (mailStatus) {
          mailStatus.innerHTML = `<div class="apex-alert ${ok && data.success ? 'apex-alert-success' : 'apex-alert-error'}">${ok && data.success ? '✅' : '⚠️'} ${data.message || 'Güncellenemedi.'}</div>`;
        }
        if (ok && window.apexToast) window.apexToast(data.message);
      });
    }

    mailPanel.querySelectorAll('[data-member-mail-action]').forEach((btn) => {
      btn.addEventListener('click', async () => {
        const act = btn.getAttribute('data-member-mail-action');
        const orig = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'GÖNDERİLİYOR...';
        const { ok, data } = await apiFetch('member/mail-action', {
          method: 'POST',
          body: JSON.stringify({ action: act }),
        });
        btn.disabled = false;
        btn.textContent = orig;
        if (mailStatus) {
          mailStatus.innerHTML = `<div class="apex-alert ${ok && data.success ? 'apex-alert-success' : 'apex-alert-error'}">${ok && data.success ? '📧' : '⚠️'} ${data.message || 'İşlem tamamlanamadı.'}</div>`;
        }
        if (ok && data.success) {
          if (window.apexToast) window.apexToast('📧 E-posta oluşturuldu / gönderildi!');
          await refreshMemberEmails();
        }
      });
    });
  }
})();
