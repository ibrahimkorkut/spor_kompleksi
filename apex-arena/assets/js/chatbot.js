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
  const chipsBox = document.getElementById('apex-ai-chips');
  const form = document.getElementById('apex-ai-form');
  const input = document.getElementById('apex-ai-input');

  if (!panel || !msgBox || !form) return;

  let sessionId = sessionStorage.getItem('apex_ai_sid');
  if (!sessionId) {
    sessionId = 'sid_' + Math.random().toString(36).slice(2, 11);
    sessionStorage.setItem('apex_ai_sid', sessionId);
  }
  const history = [];

  function setOpen(open) {
    panel.hidden = !open;
    if (launcher) launcher.setAttribute('aria-expanded', String(open));
    if (open && input) input.focus();
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
      history.length = 0;
      msgBox.innerHTML = '<div class="apex-msg apex-msg-bot">🔄 Sohbet temizlendi. Salon kiralama, hakem hizmeti, PT paketleri veya VKİ analizi hakkında ne sormak istersin?</div>';
    });
  }

  function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, (m) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]));
  }

  function renderRichText(text) {
    return escapeHtml(text)
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
    history.push({ role: 'user', text: clean });
    input.value = '';
    const typing = appendMsg('bot', '⏳ APEX AI yanıt hazırlıyor...');

    try {
      const res = await fetch(cfg.restUrl + 'chat', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': cfg.nonce || '' },
        body: JSON.stringify({ message: clean, history, session_id: sessionId }),
      });
      const data = await res.json();
      typing.remove();
      if (data && data.reply) {
        appendMsg('bot', data.reply, data.action || null);
        history.push({ role: 'bot', text: data.reply });
        if (data.suggestions) renderChips(data.suggestions);
      } else {
        appendMsg('bot', 'Şu an yanıt alınamadı, lütfen tekrar deneyin.');
      }
    } catch (_) {
      typing.remove();
      appendMsg('bot', 'Bağlantı sırasında geçici bir sorun oluştu. Lütfen tekrar deneyin.');
    }
  }

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    sendPrompt(input.value);
  });

  document.querySelectorAll('[data-ai-prompt]').forEach((chip) => {
    chip.addEventListener('click', () => sendPrompt(chip.getAttribute('data-ai-prompt')));
  });
})();
