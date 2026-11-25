/**
 * APEX ARENA — Hibrit AI Spor Koçu Sohbet İstemcisi
 */
(function () {
  'use strict';
  const cfg = window.APEX_CONFIG || { restUrl: '/wp-json/apex/v1/', nonce: '' };
  const panel = document.getElementById('apex-ai-panel');
  const launcher = document.getElementById('apex-ai-launcher');
  const closeBtn = document.getElementById('apex-ai-close');
  const clearBtn = document.getElementById('apex-ai-clear');
  const msgBox = document.getElementById('apex-ai-messages');
  const scrollRail = document.getElementById('apex-ai-scrollbar');
  const scrollThumb = document.getElementById('apex-ai-scrollbar-thumb');
  const chipsBox = document.getElementById('apex-ai-chips');
  const form = document.getElementById('apex-ai-form');
  const input = document.getElementById('apex-ai-input');

  if (!panel || !msgBox || !form) return;

  // Özel Neon Glow Kaydırma Çubuğu (sürükle & tıkla destekli)
  function syncScrollbar() {
    if (!scrollRail || !scrollThumb || panel.hidden) return;
    const viewH = msgBox.clientHeight;
    const totalH = msgBox.scrollHeight;
    const maxScroll = totalH - viewH;

    if (maxScroll <= 2 || viewH <= 0) {
      scrollRail.classList.add('is-hidden');
      return;
    }
    scrollRail.classList.remove('is-hidden');

    const track = scrollThumb.parentElement;
    const trackH = track ? track.clientHeight : Math.max(0, viewH - 20);
    if (trackH <= 0) return;

    const thumbH = Math.max(36, Math.round((viewH / totalH) * trackH));
    const maxThumbTop = Math.max(0, trackH - thumbH);
    const ratio = Math.min(1, Math.max(0, msgBox.scrollTop / maxScroll));
    const thumbTop = ratio * maxThumbTop;

    scrollThumb.style.height = thumbH + 'px';
    scrollThumb.style.transform = `translate3d(0, ${thumbTop.toFixed(1)}px, 0)`;
    scrollRail.setAttribute('aria-valuenow', String(Math.round(ratio * 100)));
  }

  let syncRaf = null;
  function scheduleSync() {
    if (syncRaf) return;
    syncRaf = requestAnimationFrame(() => {
      syncRaf = null;
      syncScrollbar();
    });
  }

  msgBox.addEventListener('scroll', scheduleSync, { passive: true });
  window.addEventListener('resize', scheduleSync, { passive: true });

  if (typeof ResizeObserver !== 'undefined') {
    new ResizeObserver(scheduleSync).observe(msgBox);
  }
  if (typeof MutationObserver !== 'undefined') {
    new MutationObserver(scheduleSync).observe(msgBox, { childList: true, subtree: true, characterData: true });
  }

  if (scrollRail && scrollThumb) {
    let dragging = false;
    let dragStartY = 0;
    let dragStartScroll = 0;

    scrollThumb.addEventListener('pointerdown', (e) => {
      e.preventDefault();
      e.stopPropagation();
      dragging = true;
      dragStartY = e.clientY;
      dragStartScroll = msgBox.scrollTop;
      scrollRail.classList.add('is-dragging');
      if (scrollThumb.setPointerCapture) scrollThumb.setPointerCapture(e.pointerId);
    });

    scrollThumb.addEventListener('pointermove', (e) => {
      if (!dragging) return;
      const track = scrollThumb.parentElement;
      const trackH = track ? track.clientHeight : 0;
      const thumbH = scrollThumb.offsetHeight;
      const maxThumbTop = Math.max(1, trackH - thumbH);
      const maxScroll = Math.max(0, msgBox.scrollHeight - msgBox.clientHeight);
      const deltaY = e.clientY - dragStartY;
      msgBox.scrollTop = dragStartScroll + (deltaY / maxThumbTop) * maxScroll;
    });

    const endDrag = (e) => {
      if (!dragging) return;
      dragging = false;
      scrollRail.classList.remove('is-dragging');
      if (e && scrollThumb.releasePointerCapture && scrollThumb.hasPointerCapture && scrollThumb.hasPointerCapture(e.pointerId)) {
        scrollThumb.releasePointerCapture(e.pointerId);
      }
    };
    scrollThumb.addEventListener('pointerup', endDrag);
    scrollThumb.addEventListener('pointercancel', endDrag);

    scrollRail.addEventListener('pointerdown', (e) => {
      if (e.target === scrollThumb) return;
      const track = scrollThumb.parentElement;
      if (!track) return;
      const rect = track.getBoundingClientRect();
      const thumbH = scrollThumb.offsetHeight;
      const maxThumbTop = Math.max(1, rect.height - thumbH);
      const clickPos = Math.min(maxThumbTop, Math.max(0, e.clientY - rect.top - thumbH / 2));
      const maxScroll = Math.max(0, msgBox.scrollHeight - msgBox.clientHeight);
      msgBox.scrollTo({ top: (clickPos / maxThumbTop) * maxScroll, behavior: 'smooth' });
    });
  }

  let sessionId = sessionStorage.getItem('apex_ai_sid');
  if (!sessionId) {
    sessionId = 'sid_' + Math.random().toString(36).slice(2, 11);
    sessionStorage.setItem('apex_ai_sid', sessionId);
  }
  let history = [];
  try {
    const saved = JSON.parse(sessionStorage.getItem('apex_ai_history') || '[]');
    if (Array.isArray(saved)) history = saved.slice(-14);
  } catch (_) {
    history = [];
  }

  function saveHistory() {
    try {
      sessionStorage.setItem('apex_ai_history', JSON.stringify(history.slice(-14)));
    } catch (_) {}
  }

  function setOpen(open) {
    panel.hidden = !open;
    if (launcher) launcher.setAttribute('aria-expanded', String(open));
    if (open) {
      if (input) input.focus();
      requestAnimationFrame(() => {
        msgBox.scrollTop = msgBox.scrollHeight;
        syncScrollbar();
      });
    }
  }

  document.querySelectorAll('[data-open-ai]').forEach((b) => {
    b.addEventListener('click', () => {
      const mDrawer = document.getElementById('apex-mobile-drawer');
      if (mDrawer) mDrawer.hidden = true;
      document.body.style.overflow = '';
      setOpen(panel.hidden);
    });
  });
  if (closeBtn) closeBtn.addEventListener('click', () => setOpen(false));
  if (clearBtn) {
    clearBtn.addEventListener('click', () => {
      history = [];
      saveHistory();
      msgBox.innerHTML = '<div class="apex-msg apex-msg-bot" style="white-space:normal;">Sohbet sıfırlandı. Yeni bir konuya geçelim, nasıl yardımcı olabilirim?</div>';
    });
  }

  function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, (m) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]));
  }

  function renderRichText(text) {
    return escapeHtml(text)
      .replace(/^\s*[\*\-]\s+/gm, '• ')
      .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
      .replace(/\*(.+?)\*/g, '<em>$1</em>')
      .replace(/`(.+?)`/g, '<code style="background:rgba(255,255,255,0.1);padding:1px 5px;border-radius:4px;">$1</code>');
  }

  function appendMsg(role, text, action) {
    const div = document.createElement('div');
    div.className = `apex-msg apex-msg-${role}`;
    div.innerHTML = role === 'bot' ? renderRichText(text) : escapeHtml(text);
    if (action && action.url && action.label) {
      const a = document.createElement('a');
      a.href = action.url;
      a.className = 'apex-msg-action';
      a.textContent = action.label + ' →';
      div.appendChild(document.createElement('br'));
      div.appendChild(a);
    }
    msgBox.appendChild(div);
    msgBox.scrollTop = msgBox.scrollHeight;
    return div;
  }

  function renderChips(list) {
    if (!chipsBox || !Array.isArray(list)) return;
    chipsBox.innerHTML = '';
    list.slice(0, 4).forEach((q) => {
      const b = document.createElement('button');
      b.type = 'button';
      b.className = 'apex-chip';
      b.textContent = q;
      b.addEventListener('click', () => sendPrompt(q));
      chipsBox.appendChild(b);
    });
  }

  async function sendPrompt(text) {
    const clean = text.trim();
    if (!clean) return;
    appendMsg('user', clean);
    const prevHistory = history.slice(-14);
    history.push({ role: 'user', text: clean });
    saveHistory();
    input.value = '';
    const typing = appendMsg('bot', 'Apex AI yazıyor...');

    try {
      const res = await fetch(cfg.restUrl + 'chat', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': cfg.nonce || '' },
        body: JSON.stringify({ message: clean, history: prevHistory, session_id: sessionId }),
      });
      const data = await res.json();
      typing.remove();
      if (data && data.reply) {
        appendMsg('bot', data.reply, data.action || null);
        history.push({ role: 'bot', text: data.reply });
        saveHistory();
        if (data.suggestions) renderChips(data.suggestions);
      } else {
        appendMsg('bot', 'Şu an yanıt alınamadı, lütfen tekrar deneyin.');
      }
    } catch (_) {
      typing.remove();
      appendMsg('bot', 'Bağlantı sırasında geçici bir sorun oluştu. Lütfen tekrar deneyin.');
    }
  }

  // Sayfa geçişlerinde mevcut oturum sohbetini koru
  if (history.length > 0) {
    history.forEach((item) => {
      if (item && item.text) appendMsg(item.role === 'user' ? 'user' : 'bot', item.text);
    });
  }

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    sendPrompt(input.value);
  });

  document.querySelectorAll('[data-ai-prompt]').forEach((chip) => {
    chip.addEventListener('click', () => sendPrompt(chip.getAttribute('data-ai-prompt')));
  });

  // WhatsApp Liquid Glass Butonu — İnce Manyetik İmleç Takibi
  const waBtn = document.getElementById('apex-wa-launcher');
  if (waBtn && window.matchMedia && window.matchMedia('(hover: hover)').matches) {
    waBtn.addEventListener('pointermove', (e) => {
      const r = waBtn.getBoundingClientRect();
      const dx = ((e.clientX - (r.left + r.width / 2)) / (r.width / 2)) * 4;
      const dy = ((e.clientY - (r.top + r.height / 2)) / (r.height / 2)) * 4;
      waBtn.style.setProperty('--wa-mx', dx.toFixed(1) + 'px');
      waBtn.style.setProperty('--wa-my', dy.toFixed(1) + 'px');
    }, { passive: true });
    waBtn.addEventListener('pointerleave', () => {
      waBtn.style.setProperty('--wa-mx', '0px');
      waBtn.style.setProperty('--wa-my', '0px');
    });
  }
})();
