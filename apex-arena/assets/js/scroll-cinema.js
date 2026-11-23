/**
 * APEX ARENA — Apple-Style 60 FPS Scroll-Scrubbed Canvas Video Engine (v1.2.0)
 * 100% ücretsiz, yerel WebP kare dizisi ile sıfır gecikmeli ileri/geri video oynatma.
 *
 * @package ApexArena
 */
(function () {
  'use strict';

  var runway = document.getElementById('apex-cinema-runway');
  if (!runway) return;

  var canvas = document.getElementById('apex-cinema-canvas');
  if (!canvas || !canvas.getContext) return;
  var ctx = canvas.getContext('2d', { alpha: false });

  var frameBase = runway.getAttribute('data-frame-base') || '';
  var frameCount = parseInt(runway.getAttribute('data-frame-count') || '120', 10);
  var durationSec = parseFloat(runway.getAttribute('data-duration') || '6.0');
  var padDigits = parseInt(runway.getAttribute('data-pad') || '4', 10);

  var fillEl = runway.querySelector('[data-cinema-fill]');
  var timeEl = runway.querySelector('[data-cinema-time]');
  var pctEl = runway.querySelector('[data-cinema-pct]');
  var statusEl = runway.querySelector('[data-cinema-status]');
  var autoBtn = runway.querySelector('[data-cinema-autoplay]');
  var chapterDots = Array.prototype.slice.call(runway.querySelectorAll('[data-cinema-jump]'));
  var chapters = Array.prototype.slice.call(runway.querySelectorAll('[data-cinema-chapter]'));

  var frames = new Array(frameCount);
  var loadedCount = 0;
  var lastDrawnIdx = -1;
  var targetProgress = 0;
  var renderedProgress = 0;
  var rafId = null;
  var autoPlaying = false;
  var autoRafId = null;
  var canvasW = 0;
  var canvasH = 0;

  function isReducedMotion() {
    return (
      document.documentElement.classList.contains('a11y-reduce-motion') ||
      (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches)
    );
  }

  function pad(num, size) {
    var s = String(num);
    while (s.length < size) s = '0' + s;
    return s;
  }

  function frameUrl(idx) {
    return frameBase + 'frame_' + pad(idx + 1, padDigits) + '.webp';
  }

  function formatTime(sec) {
    var s = Math.max(0, Math.min(durationSec, sec));
    var whole = Math.floor(s);
    var tenths = Math.floor((s - whole) * 10);
    return '00:0' + whole + '.' + tenths;
  }

  function resizeCanvas() {
    var stage = canvas.parentElement;
    if (!stage) return;
    var rect = stage.getBoundingClientRect();
    var dpr = Math.min(window.devicePixelRatio || 1, 2);
    canvasW = Math.max(1, Math.round(rect.width * dpr));
    canvasH = Math.max(1, Math.round(rect.height * dpr));
    if (canvas.width !== canvasW || canvas.height !== canvasH) {
      canvas.width = canvasW;
      canvas.height = canvasH;
      lastDrawnIdx = -1;
      drawFrameForProgress(renderedProgress, true);
    }
  }

  function drawImageCover(img) {
    if (!img || !img.naturalWidth || !img.naturalHeight) return;
    var iw = img.naturalWidth;
    var ih = img.naturalHeight;
    var cw = canvas.width;
    var ch = canvas.height;
    var scale = Math.max(cw / iw, ch / ih);
    var dw = iw * scale;
    var dh = ih * scale;
    var dx = (cw - dw) * 0.5;
    var dy = (ch - dh) * 0.5;

    ctx.fillStyle = '#000000';
    ctx.fillRect(0, 0, cw, ch);
    ctx.drawImage(img, dx, dy, dw, dh);
  }

  function findNearestLoaded(idx) {
    if (frames[idx] && frames[idx].complete && frames[idx].naturalWidth > 0) {
      return idx;
    }
    for (var offset = 1; offset < frameCount; offset++) {
      var prev = idx - offset;
      if (prev >= 0 && frames[prev] && frames[prev].complete && frames[prev].naturalWidth > 0) {
        return prev;
      }
      var next = idx + offset;
      if (next < frameCount && frames[next] && frames[next].complete && frames[next].naturalWidth > 0) {
        return next;
      }
    }
    return -1;
  }

  function drawFrameForProgress(progress, force) {
    var desired = Math.max(0, Math.min(frameCount - 1, Math.round(progress * (frameCount - 1))));
    var readyIdx = findNearestLoaded(desired);
    if (readyIdx === -1) return;
    if (!force && readyIdx === lastDrawnIdx) return;
    drawImageCover(frames[readyIdx]);
    lastDrawnIdx = readyIdx;
  }

  function updateHUD(progress) {
    if (fillEl) {
      fillEl.style.transform = 'scaleX(' + Math.max(0.01, progress).toFixed(4) + ')';
    }
    if (timeEl) {
      timeEl.textContent = formatTime(progress * durationSec) + ' / ' + formatTime(durationSec);
    }
    if (pctEl) {
      pctEl.textContent = Math.round(progress * 100) + '%';
    }

    var activeChapterIdx = 0;
    chapters.forEach(function (el, idx) {
      var start = parseFloat(el.getAttribute('data-start') || '0');
      var end = parseFloat(el.getAttribute('data-end') || '1');
      var fade = 0.055;
      var opacity = 0;
      var translateY = 26;

      if (idx === 0 && progress <= end) {
        // Hero intro starts fully visible at scroll 0
        if (progress <= end - fade) {
          opacity = 1;
          translateY = -progress * 90;
        } else {
          var tOut = (progress - (end - fade)) / fade;
          opacity = Math.max(0, 1 - tOut);
          translateY = -28 - tOut * 28;
        }
      } else if (progress >= start && progress <= end) {
        if (progress < start + fade) {
          var tIn = (progress - start) / fade;
          opacity = tIn;
          translateY = (1 - tIn) * 28;
        } else if (idx < chapters.length - 1 && progress > end - fade) {
          var tExit = (progress - (end - fade)) / fade;
          opacity = Math.max(0, 1 - tExit);
          translateY = -tExit * 24;
        } else {
          opacity = 1;
          translateY = 0;
        }
      }

      el.style.opacity = opacity.toFixed(3);
      el.style.transform = 'translate3d(0,' + translateY.toFixed(1) + 'px,0)';
      el.style.pointerEvents = opacity > 0.45 ? 'auto' : 'none';
      el.setAttribute('aria-hidden', opacity < 0.15 ? 'true' : 'false');

      if (opacity > 0.35) {
        activeChapterIdx = idx;
      }
    });

    chapterDots.forEach(function (btn, idx) {
      var isAct = idx === activeChapterIdx;
      btn.classList.toggle('is-active', isAct);
      btn.setAttribute('aria-current', isAct ? 'step' : 'false');
    });
  }

  function computeScrollProgress() {
    var rect = runway.getBoundingClientRect();
    var scrollable = runway.offsetHeight - window.innerHeight;
    if (scrollable <= 1) return 0;
    var raw = -rect.top / scrollable;
    return Math.max(0, Math.min(1, raw));
  }

  function tick() {
    rafId = null;
    var reduced = isReducedMotion();
    if (reduced) {
      renderedProgress = targetProgress;
    } else {
      var diff = targetProgress - renderedProgress;
      if (Math.abs(diff) < 0.0006) {
        renderedProgress = targetProgress;
      } else {
        renderedProgress += diff * 0.16;
        rafId = window.requestAnimationFrame(tick);
      }
    }

    drawFrameForProgress(renderedProgress, false);
    updateHUD(renderedProgress);
  }

  function requestTick() {
    targetProgress = computeScrollProgress();
    if (!rafId) {
      rafId = window.requestAnimationFrame(tick);
    }
  }

  function stopAutoPlay() {
    autoPlaying = false;
    if (autoRafId) {
      window.cancelAnimationFrame(autoRafId);
      autoRafId = null;
    }
    if (autoBtn) {
      autoBtn.classList.remove('is-playing');
      autoBtn.setAttribute('aria-pressed', 'false');
      var lbl = autoBtn.querySelector('[data-play-label]');
      if (lbl) lbl.textContent = 'Oynat';
    }
  }

  function startAutoPlay() {
    if (autoPlaying) {
      stopAutoPlay();
      return;
    }
    autoPlaying = true;
    if (autoBtn) {
      autoBtn.classList.add('is-playing');
      autoBtn.setAttribute('aria-pressed', 'true');
      var lbl = autoBtn.querySelector('[data-play-label]');
      if (lbl) lbl.textContent = 'Duraklat';
    }

    var runwayTop = runway.getBoundingClientRect().top + window.scrollY;
    var scrollable = Math.max(1, runway.offsetHeight - window.innerHeight);
    var currentP = computeScrollProgress();
    if (currentP >= 0.98) {
      window.scrollTo(0, runwayTop);
      currentP = 0;
    }

    var startTime = performance.now();
    var remainingMs = Math.max(1800, (1 - currentP) * durationSec * 1550);
    var startScroll = runwayTop + currentP * scrollable;
    var endScroll = runwayTop + scrollable;

    function step(now) {
      if (!autoPlaying) return;
      var t = Math.min(1, (now - startTime) / remainingMs);
      window.scrollTo(0, startScroll + (endScroll - startScroll) * t);
      if (t < 1) {
        autoRafId = window.requestAnimationFrame(step);
      } else {
        stopAutoPlay();
      }
    }
    autoRafId = window.requestAnimationFrame(step);
  }

  // Stop auto-scroll immediately if user manually scrolls via wheel or touch
  window.addEventListener('wheel', stopAutoPlay, { passive: true });
  window.addEventListener('touchstart', stopAutoPlay, { passive: true });

  if (autoBtn) {
    autoBtn.addEventListener('click', startAutoPlay);
  }

  chapterDots.forEach(function (btn) {
    btn.addEventListener('click', function () {
      stopAutoPlay();
      var p = parseFloat(btn.getAttribute('data-cinema-jump') || '0');
      var runwayTop = runway.getBoundingClientRect().top + window.scrollY;
      var scrollable = Math.max(1, runway.offsetHeight - window.innerHeight);
      window.scrollTo({
        top: runwayTop + p * scrollable,
        behavior: isReducedMotion() ? 'auto' : 'smooth',
      });
    });
  });

  // Progressive Keyframe-First Preloader (Frame 0 -> Every 4th -> All remaining)
  function loadSingleFrame(idx, onDone) {
    if (frames[idx]) {
      if (onDone) onDone();
      return;
    }
    var img = new Image();
    img.decoding = 'async';
    img.onload = function () {
      loadedCount++;
      if (idx === 0) {
        drawFrameForProgress(renderedProgress, true);
      } else if (Math.abs(idx - Math.round(renderedProgress * (frameCount - 1))) <= 3) {
        drawFrameForProgress(renderedProgress, false);
      }
      if (statusEl && loadedCount >= frameCount) {
        statusEl.textContent = 'Scroll ile Oynat';
      }
      if (onDone) onDone();
    };
    img.onerror = function () {
      if (onDone) onDone();
    };
    img.src = frameUrl(idx);
    frames[idx] = img;
  }

  function preloadFrames() {
    // 1. Frame 0 immediately for instant LCP
    loadSingleFrame(0, function () {
      // 2. Keyframes pass (stride 4)
      var queue = [];
      for (var i = 4; i < frameCount; i += 4) queue.push(i);
      // 3. Fill-in pass (all remaining frames)
      for (var j = 1; j < frameCount; j++) {
        if (j % 4 !== 0) queue.push(j);
      }

      var concurrency = 6;
      var cursor = 0;
      function worker() {
        if (cursor >= queue.length) return;
        var nextIdx = queue[cursor++];
        loadSingleFrame(nextIdx, worker);
      }
      for (var c = 0; c < concurrency; c++) worker();
    });
  }

  // Initialize
  resizeCanvas();
  targetProgress = computeScrollProgress();
  renderedProgress = targetProgress;
  updateHUD(renderedProgress);
  preloadFrames();

  window.addEventListener('scroll', requestTick, { passive: true });
  window.addEventListener('resize', function () {
    resizeCanvas();
    requestTick();
  });
})();
