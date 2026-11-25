/**
 * APEX ARENA — 3D Architectural Cutaway Sports Complex (Dollhouse / Isometric WebGL Engine v2.2.0)
 *
 * Features:
 * - Procedural architectural cutaway sports complex inspired by isometric architectural floorplan models:
 *   thick exterior & interior walls with dark architectural cut-caps, central travertine atrium with
 *   sculptural 3D spiral staircase, QR turnstiles & EV charging bay, FIBA Basketball Court 1 (maple parke,
 *   3D hoops, LED scoreboard), CEV Volleyball Court (Taraflex, 3D net, ref stand), PT & Strength Studio
 *   (power squat racks, barbells, sprint turf, dumbbell rack, treadmills), RFID Lockers & Cedar Fin Sauna
 *   + Glass Rain Showers, and Apex Protein Cafe & Lounge (barista bar, stools, tables, indoor plants).
 * - Smooth 3D Hover Dive-In Camera: Hovering any room smoothly flies the camera down and INTO that room
 *   with perimeter glow rim highlight, subtle pointer parallax, and live inspector sync.
 * - Click to lock camera inside a room, drag to gently orbit, Escape / Reset to return to isometric overview.
 * - Zero CDN (uses local ./vendor/three.module.js), IntersectionObserver lazy init, off-screen rAF sleep.
 *
 * @package ApexArena
 */
import * as THREE from './vendor/three.module.js';

const root = document.getElementById('apex-arch-3d-stage');
if (root) {
  initArch3D(root);
}

/* ============================================================================
   Zone Definitions (World Bounds, Camera Dive-In Pose & Inspector Metadata)
   Complex Footprint: X in [-12, +12] (24m scaled), Z in [-7.5, +7.5] (15m scaled)
   ============================================================================ */
function getZones(siteBase) {
  return {
    basket1: {
      key: 'basket1',
      name: 'Arena Basketbol 1',
      short: 'Basketbol 1',
      status: '● Müsait',
      tag: 'FIBA STANDARTI · KUZEYBATI KANAT',
      title: 'Arena Basketbol 1 (FIBA Parke)',
      desc: '28×15m uluslararası FIBA ölçülerinde esnek akçaağaç parke, çift cam panyalı profesyonel pota, LED skorboard ve isteğe bağlı lisanslı TBF hakem hizmeti.',
      specs: ['🏀 28×15m FIBA Parke', '⚖️ Lisanslı Hakem (+₺450)', '📺 4 Yönlü LED Skorboard'],
      cta: 'Basketbol Salonunu Kirala →',
      url: `${siteBase}/salon-kiralama/#rezervasyon`,
      color: 0xe31837,
      bounds: { x: -7.5, z: -3.75, w: 8.8, d: 7.2 },
      // Camera dives down right inside/over the corner of the basketball court looking across the court & hoop
      camPos: new THREE.Vector3(-4.2, 4.8, 1.8),
      camLook: new THREE.Vector3(-8.0, 0.85, -4.2),
    },
    voleybol: {
      key: 'voleybol',
      name: 'Voleybol Pro',
      short: 'Voleybol Pro',
      status: '● Hakemli',
      tag: 'CEV TARAFLEX · KUZEYDOĞU KANAT',
      title: 'Arena Voleybol Pro (CEV Taraflex)',
      desc: 'Eklem dostu 7mm CEV onaylı Taraflex zemin, gerdirmeli profesyonel file & anten sistemi, hakem kürsüsü ve yüksek tavanlı LED aydınlatma.',
      specs: ['🏐 18×9m CEV Taraflex', '📏 12m Tavan Yüksekliği', '⚖️ TVF Hakem Opsiyonu'],
      cta: 'Voleybol Salonunu Kirala →',
      url: `${siteBase}/salon-kiralama/#rezervasyon`,
      color: 0x26e6ff,
      bounds: { x: 7.5, z: -3.75, w: 8.8, d: 7.2 },
      camPos: new THREE.Vector3(4.2, 4.8, 1.8),
      camLook: new THREE.Vector3(8.0, 0.9, -4.1),
    },
    lobby: {
      key: 'lobby',
      name: 'Merkez Lobi & Sarmal Merdiven',
      short: 'Lobi & EV',
      status: '● Açık',
      tag: 'ATRIYUM · QR TURNİKE & EV OTOPARK',
      title: 'Merkez Lobi, Sarmal Merdiven & EV Girişi',
      desc: 'Traverten mermer atriyum, üst galeriye çıkan heykelsi sarmal merdiven, temassız Dijital QR turnikeleri ve 22 kW hızlı elektrikli araç şarj girişi.',
      specs: ['🌀 Mimari Sarmal Merdiven', '📱 Dijital QR Turnike', '⚡ 6× 22 kW EV Şarj'],
      cta: 'Üyelik & Dijital QR Kart Al →',
      url: `${siteBase}/uyelik/#kayit`,
      color: 0xf4e268,
      bounds: { x: 0.0, z: -3.75, w: 6.0, d: 7.2 },
      camPos: new THREE.Vector3(1.6, 5.1, 2.4),
      camLook: new THREE.Vector3(0.1, 0.95, -4.2),
    },
    pt: {
      key: 'pt',
      name: 'PT & Kuvvet Stüdyosu',
      short: 'PT & Kuvvet',
      status: '● 7 Koç',
      tag: 'OLİMPİK KUVVET · GÜNEYBATI KANAT',
      title: 'PT, Olimpik Kuvvet & Sprint Stüdyosu',
      desc: 'Çift olimpik power squat rack, bumper plakalar, 15m yeşil sprint kızak turf pisti, serbest ağırlık istasyonu ve son teknoloji kardiyo koşu bantları.',
      specs: ['🏋️ Olimpik Squat Rack', '🌿 Sprint Sled Turf', '👤 7 Uzman PT Koçu'],
      cta: 'PT Randevusu Oluştur →',
      url: `${siteBase}/pt-egitmenler/#randevu`,
      color: 0xa4ff2e,
      bounds: { x: -7.5, z: 3.75, w: 8.8, d: 7.2 },
      camPos: new THREE.Vector3(-4.0, 4.7, 9.6),
      camLook: new THREE.Vector3(-8.0, 0.8, 3.2),
    },
    spa: {
      key: 'spa',
      name: 'Soyunma, Duş & Fin Saunası',
      short: 'Sauna & Spa',
      status: '● 85°C',
      tag: 'TOPARLANMA · GÜNEY ORTA KANAT',
      title: 'RFID Soyunma Odası, Yağmur Duş & Fin Saunası',
      desc: '240 adet temassız RFID akıllı dolap, cam bölmeli yüksek basınçlı yağmur duş kabinleri ve sıcak sedir ağacından 85°C Fin saunası.',
      specs: ['🔐 240 RFID Dolap', '🔥 85°C Sedir Fin Saunası', '🚿 Cam Yağmur Duşlar'],
      cta: 'Tesis Olanaklarını İncele →',
      url: `${siteBase}/tesis-olanaklari/`,
      color: 0xff9f1c,
      bounds: { x: 0.0, z: 3.75, w: 6.0, d: 7.2 },
      camPos: new THREE.Vector3(1.1, 5.0, 10.2),
      camLook: new THREE.Vector3(-0.2, 0.8, 3.6),
    },
    cafe: {
      key: 'cafe',
      name: 'Apex Protein Kafe & Lounge',
      short: 'Protein Kafe',
      status: '● Ön Sipariş',
      tag: 'BESLENME & LOUNGE · GÜNEYDOĞU KANAT',
      title: 'Apex Protein Kafe & Dinlenme Lounge',
      desc: 'Mermer barista tezgahı, makro hesaplı izole whey shake ve fit bowl menüsü, sıcak ahşap oturma alanları ve antrenman çıkışı hızlı teslimat.',
      specs: ['🥤 Makro Hesaplı Shake', '🥗 Fit Bowl Menüsü', '🌿 Lounge & Wi-Fi 6'],
      cta: 'Kafe Menüsü & Ön Sipariş →',
      url: `${siteBase}/tesis-olanaklari/#kafe`,
      color: 0x38bdf8,
      bounds: { x: 7.5, z: 3.75, w: 8.8, d: 7.2 },
      camPos: new THREE.Vector3(4.0, 4.7, 9.6),
      camLook: new THREE.Vector3(8.0, 0.85, 3.3),
    },
  };
}

/* ============================================================================
   Procedural Architectural Floor & Surface CanvasTextures
   ============================================================================ */
function makeBasketballTexture() {
  const c = document.createElement('canvas');
  c.width = 1024;
  c.height = 768;
  const ctx = c.getContext('2d');

  // Warm maple hardwood planks
  ctx.fillStyle = '#d7a86e';
  ctx.fillRect(0, 0, c.width, c.height);
  for (let y = 0; y < c.height; y += 16) {
    const shade = (Math.sin(y * 0.37) * 8) | 0;
    ctx.fillStyle = `rgb(${214 + shade},${166 + shade},${106 + shade})`;
    ctx.fillRect(0, y, c.width, 15);
    ctx.fillStyle = 'rgba(92,58,24,0.14)';
    ctx.fillRect(0, y + 15, c.width, 1);
  }

  // Outer crimson apron border
  ctx.strokeStyle = '#991b1b';
  ctx.lineWidth = 44;
  ctx.strokeRect(22, 22, c.width - 44, c.height - 44);

  // Court crisp white boundary
  const padX = 76;
  const padY = 70;
  const cw = c.width - padX * 2;
  const ch = c.height - padY * 2;
  ctx.strokeStyle = '#ffffff';
  ctx.lineWidth = 7;
  ctx.strokeRect(padX, padY, cw, ch);

  // Half-court line & center circle
  ctx.beginPath();
  ctx.moveTo(c.width / 2, padY);
  ctx.lineTo(c.width / 2, padY + ch);
  ctx.stroke();

  ctx.fillStyle = 'rgba(227,24,55,0.88)';
  ctx.beginPath();
  ctx.arc(c.width / 2, c.height / 2, 74, 0, Math.PI * 2);
  ctx.fill();
  ctx.stroke();

  ctx.fillStyle = '#ffffff';
  ctx.font = '900 34px "Segoe UI", sans-serif';
  ctx.textAlign = 'center';
  ctx.textBaseline = 'middle';
  ctx.fillText('APEX', c.width / 2, c.height / 2);

  // Paint / keys on left & right
  const keyW = 195;
  const keyH = 196;
  ctx.fillStyle = 'rgba(185,28,28,0.86)';
  ctx.fillRect(padX, c.height / 2 - keyH / 2, keyW, keyH);
  ctx.fillRect(padX + cw - keyW, c.height / 2 - keyH / 2, keyW, keyH);
  ctx.strokeRect(padX, c.height / 2 - keyH / 2, keyW, keyH);
  ctx.strokeRect(padX + cw - keyW, c.height / 2 - keyH / 2, keyW, keyH);

  // Free throw circles
  ctx.beginPath();
  ctx.arc(padX + keyW, c.height / 2, 62, -Math.PI / 2, Math.PI / 2);
  ctx.stroke();
  ctx.beginPath();
  ctx.arc(padX + cw - keyW, c.height / 2, 62, Math.PI / 2, (3 * Math.PI) / 2);
  ctx.stroke();

  // 3-point arcs
  ctx.beginPath();
  ctx.arc(padX + 54, c.height / 2, 240, -1.24, 1.24);
  ctx.stroke();
  ctx.beginPath();
  ctx.arc(padX + cw - 54, c.height / 2, 240, Math.PI - 1.24, Math.PI + 1.24);
  ctx.stroke();

  const tex = new THREE.CanvasTexture(c);
  tex.colorSpace = THREE.SRGBColorSpace;
  tex.anisotropy = 4;
  return tex;
}

function makeVolleyballTexture() {
  const c = document.createElement('canvas');
  c.width = 1024;
  c.height = 768;
  const ctx = c.getContext('2d');

  // CEV outer teal-blue apron
  ctx.fillStyle = '#1d6fa3';
  ctx.fillRect(0, 0, c.width, c.height);

  // Inner CEV warm coral-orange court
  const padX = 110;
  const padY = 105;
  const cw = c.width - padX * 2;
  const ch = c.height - padY * 2;
  ctx.fillStyle = '#e07a45';
  ctx.fillRect(padX, padY, cw, ch);

  // Subtle matte grain lines
  ctx.fillStyle = 'rgba(255,255,255,0.035)';
  for (let i = 0; i < c.width; i += 24) {
    ctx.fillRect(i, 0, 2, c.height);
  }

  // White court markings
  ctx.strokeStyle = '#ffffff';
  ctx.lineWidth = 8;
  ctx.strokeRect(padX, padY, cw, ch);

  // Center line
  ctx.beginPath();
  ctx.moveTo(c.width / 2, padY);
  ctx.lineTo(c.width / 2, padY + ch);
  ctx.stroke();

  // 3m attack lines
  ctx.lineWidth = 6;
  ctx.beginPath();
  ctx.moveTo(c.width / 2 - cw * 0.17, padY);
  ctx.lineTo(c.width / 2 - cw * 0.17, padY + ch);
  ctx.moveTo(c.width / 2 + cw * 0.17, padY);
  ctx.lineTo(c.width / 2 + cw * 0.17, padY + ch);
  ctx.stroke();

  const tex = new THREE.CanvasTexture(c);
  tex.colorSpace = THREE.SRGBColorSpace;
  tex.anisotropy = 4;
  return tex;
}

function makeGymRubberTexture() {
  const c = document.createElement('canvas');
  c.width = 512;
  c.height = 512;
  const ctx = c.getContext('2d');
  ctx.fillStyle = '#252830';
  ctx.fillRect(0, 0, 512, 512);
  // Subtle rubber tile grid
  ctx.strokeStyle = 'rgba(255,255,255,0.05)';
  ctx.lineWidth = 2;
  for (let i = 0; i <= 512; i += 64) {
    ctx.beginPath(); ctx.moveTo(i, 0); ctx.lineTo(i, 512); ctx.stroke();
    ctx.beginPath(); ctx.moveTo(0, i); ctx.lineTo(512, i); ctx.stroke();
  }
  // Flecks
  for (let k = 0; k < 1400; k++) {
    const x = (Math.sin(k * 12.9898) * 43758.5453) % 1;
    const y = (Math.sin(k * 78.233) * 23421.631) % 1;
    ctx.fillStyle = k % 9 === 0 ? 'rgba(227,24,55,0.35)' : 'rgba(200,210,225,0.12)';
    ctx.fillRect(Math.abs(x) * 512, Math.abs(y) * 512, 2, 2);
  }
  const tex = new THREE.CanvasTexture(c);
  tex.colorSpace = THREE.SRGBColorSpace;
  tex.wrapS = tex.wrapT = THREE.RepeatWrapping;
  tex.repeat.set(2, 2);
  return tex;
}

function makeTileTexture(baseHex, lineHex, repeatX = 4, repeatY = 4) {
  const c = document.createElement('canvas');
  c.width = 256;
  c.height = 256;
  const ctx = c.getContext('2d');
  ctx.fillStyle = baseHex;
  ctx.fillRect(0, 0, 256, 256);
  ctx.strokeStyle = lineHex;
  ctx.lineWidth = 3;
  for (let i = 0; i <= 256; i += 64) {
    ctx.beginPath(); ctx.moveTo(i, 0); ctx.lineTo(i, 256); ctx.stroke();
    ctx.beginPath(); ctx.moveTo(0, i); ctx.lineTo(256, i); ctx.stroke();
  }
  const tex = new THREE.CanvasTexture(c);
  tex.colorSpace = THREE.SRGBColorSpace;
  tex.wrapS = tex.wrapT = THREE.RepeatWrapping;
  tex.repeat.set(repeatX, repeatY);
  return tex;
}

function makeWoodTexture(baseA = '#c9a679', baseB = '#b99363') {
  const c = document.createElement('canvas');
  c.width = 256;
  c.height = 256;
  const ctx = c.getContext('2d');
  for (let y = 0; y < 256; y += 16) {
    ctx.fillStyle = (y / 16) % 2 === 0 ? baseA : baseB;
    ctx.fillRect(0, y, 256, 15);
    ctx.fillStyle = 'rgba(70,42,16,0.15)';
    ctx.fillRect(0, y + 15, 256, 1);
  }
  const tex = new THREE.CanvasTexture(c);
  tex.colorSpace = THREE.SRGBColorSpace;
  tex.wrapS = tex.wrapT = THREE.RepeatWrapping;
  tex.repeat.set(3, 3);
  return tex;
}

function makeScoreboardTexture() {
  const c = document.createElement('canvas');
  c.width = 256;
  c.height = 96;
  const ctx = c.getContext('2d');
  ctx.fillStyle = '#090b10';
  ctx.fillRect(0, 0, 256, 96);
  ctx.strokeStyle = '#e31837';
  ctx.lineWidth = 4;
  ctx.strokeRect(4, 4, 248, 88);
  ctx.fillStyle = '#a4ff2e';
  ctx.font = 'bold 14px monospace';
  ctx.textAlign = 'center';
  ctx.fillText('APEX ARENA · Q4 02:14', 128, 26);
  ctx.fillStyle = '#ff3b5c';
  ctx.font = '900 36px monospace';
  ctx.fillText('84  :  79', 128, 70);
  const tex = new THREE.CanvasTexture(c);
  tex.colorSpace = THREE.SRGBColorSpace;
  return tex;
}

/* ============================================================================
   Main 3D Architectural Complex Builder & Interactive Camera Controller
   ============================================================================ */
function initArch3D(stageEl) {
  const canvas = stageEl.querySelector('[data-arch-canvas]');
  if (!canvas) return;

  const hudPill = document.getElementById('apex-arch-hud-pill');
  const hudTitle = document.getElementById('apex-arch-hud-title');
  const hudHint = document.getElementById('apex-arch-hud-hint');
  const resetBtn = document.getElementById('apex-arch-reset-btn');
  const orbitBtn = document.getElementById('apex-arch-orbit-btn');
  const zoneChips = Array.from(document.querySelectorAll('[data-arch-chip]'));
  const fpCta = document.getElementById('fp-d-cta');
  const siteBase = (fpCta?.getAttribute('href') || '').replace(/\/(salon-kiralama|pt-egitmenler|tesis-olanaklari|uyelik)\/.*$/, '');
  const ZONES = getZones(siteBase);

  let initialized = false;
  let isVisible = false;
  let rafId = 0;

  // Lazy-start WebGL only when scrolled near the section
  const io = new IntersectionObserver(
    (entries) => {
      const entry = entries[0];
      isVisible = !!(entry && entry.isIntersecting);
      if (isVisible && !initialized) {
        initialized = true;
        bootScene();
      }
      if (isVisible && initialized && !rafId && startLoopFn) {
        startLoopFn();
      }
    },
    { rootMargin: '280px 0px' }
  );
  io.observe(stageEl);

  let startLoopFn = null;

  function bootScene() {
    const isMobile = window.innerWidth < 768;
    const renderer = new THREE.WebGLRenderer({
      canvas,
      antialias: true,
      alpha: true,
      powerPreference: 'high-performance',
    });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, isMobile ? 1.5 : 2));
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.12;
    renderer.outputColorSpace = THREE.SRGBColorSpace;

    const scene = new THREE.Scene();

    // Camera — Classic diagonal 3/4 isometric cutaway angle showing all 6 rooms cleanly framed
    const camera = new THREE.PerspectiveCamera(38, 16 / 10, 0.2, 140);
    const OVERVIEW_POS = new THREE.Vector3(10.5, 22.8, 25.2);
    const OVERVIEW_LOOK = new THREE.Vector3(0.0, 0.0, 0.2);
    camera.position.copy(OVERVIEW_POS);
    const currentLook = OVERVIEW_LOOK.clone();
    camera.lookAt(currentLook);

    // Studio Architectural Lighting (Clean daylight + soft warm bounce + directional shadows)
    const hemi = new THREE.HemisphereLight(0xf6f9ff, 0x262936, 1.05);
    scene.add(hemi);

    const sun = new THREE.DirectionalLight(0xfffaf0, 1.95);
    sun.position.set(14, 26, 16);
    sun.castShadow = true;
    sun.shadow.mapSize.width = isMobile ? 1024 : 2048;
    sun.shadow.mapSize.height = isMobile ? 1024 : 2048;
    sun.shadow.camera.near = 4;
    sun.shadow.camera.far = 65;
    sun.shadow.camera.left = -18;
    sun.shadow.camera.right = 18;
    sun.shadow.camera.top = 16;
    sun.shadow.camera.bottom = -16;
    sun.shadow.bias = -0.0005;
    scene.add(sun);

    const fillLight = new THREE.DirectionalLight(0x9ec5ff, 0.55);
    fillLight.position.set(-16, 12, -10);
    scene.add(fillLight);

    // Dynamic Hover Spotlight that follows the active room
    const hoverLight = new THREE.PointLight(0xfff1d6, 0.0, 14, 1.5);
    hoverLight.position.set(0, 5.2, 0);
    scene.add(hoverLight);

    // Master Complex Group (allows gentle architectural rotation/tilt)
    const complexGroup = new THREE.Group();
    scene.add(complexGroup);

    // Architectural Materials (inspired by the clean cutaway reference image!)
    const matSlab = new THREE.MeshStandardMaterial({ color: 0x1a1d24, roughness: 0.72, metalness: 0.12 });
    const matWallBody = new THREE.MeshStandardMaterial({ color: 0xe6e7ec, roughness: 0.78, metalness: 0.04 });
    const matWallCap = new THREE.MeshStandardMaterial({ color: 0x2c303a, roughness: 0.52, metalness: 0.22 });
    const matGlass = new THREE.MeshPhysicalMaterial({
      color: 0xaad8ff,
      transparent: true,
      opacity: 0.34,
      roughness: 0.12,
      metalness: 0.1,
    });
    const matDarkMetal = new THREE.MeshStandardMaterial({ color: 0x22252e, roughness: 0.38, metalness: 0.82 });
    const matChrome = new THREE.MeshStandardMaterial({ color: 0xd8dee9, roughness: 0.22, metalness: 0.92 });
    const matCrimson = new THREE.MeshStandardMaterial({ color: 0xe31837, roughness: 0.4, metalness: 0.25 });
    const matOak = new THREE.MeshStandardMaterial({ color: 0x8f5d38, roughness: 0.62, metalness: 0.05 });
    const matCedar = new THREE.MeshStandardMaterial({ color: 0xc98242, roughness: 0.58, metalness: 0.04 });
    const matPlantLeaf = new THREE.MeshStandardMaterial({ color: 0x2d8a4e, roughness: 0.65 });
    const matPot = new THREE.MeshStandardMaterial({ color: 0xd4d4d8, roughness: 0.55 });

    // Base Architectural Plinth / Foundation Slab
    const baseSlab = new THREE.Mesh(new THREE.BoxGeometry(24.8, 0.65, 15.8), matSlab);
    baseSlab.position.set(0, -0.34, 0);
    baseSlab.receiveShadow = true;
    complexGroup.add(baseSlab);

    // Subtle ground shadow pedestal
    const shadowPlane = new THREE.Mesh(
      new THREE.PlaneGeometry(42, 32),
      new THREE.ShadowMaterial({ opacity: 0.32 })
    );
    shadowPlane.rotation.x = -Math.PI / 2;
    shadowPlane.position.y = -0.68;
    shadowPlane.receiveShadow = true;
    complexGroup.add(shadowPlane);

    // Helper: Cutaway Architectural Wall Segment (white body + crisp dark top cut-cap)
    const WALL_H = 2.45;
    function addWall(x, z, w, d, h = WALL_H) {
      const body = new THREE.Mesh(new THREE.BoxGeometry(w, h - 0.08, d), matWallBody);
      body.position.set(x, (h - 0.08) / 2, z);
      body.castShadow = true;
      body.receiveShadow = true;
      complexGroup.add(body);

      const cap = new THREE.Mesh(new THREE.BoxGeometry(w + 0.015, 0.09, d + 0.015), matWallCap);
      cap.position.set(x, h - 0.04, z);
      cap.castShadow = true;
      complexGroup.add(cap);
    }

    // Helper: Potted Architectural Plant (like the reference image!)
    function addPlant(x, z, scale = 1) {
      const g = new THREE.Group();
      const pot = new THREE.Mesh(new THREE.CylinderGeometry(0.24 * scale, 0.18 * scale, 0.38 * scale, 12), matPot);
      pot.position.y = 0.19 * scale;
      pot.castShadow = true;
      g.add(pot);
      for (let i = 0; i < 4; i++) {
        const leaf = new THREE.Mesh(new THREE.DodecahedronGeometry(0.24 * scale, 0), matPlantLeaf);
        const a = (i / 4) * Math.PI * 2;
        leaf.position.set(Math.cos(a) * 0.12 * scale, (0.52 + (i % 2) * 0.14) * scale, Math.sin(a) * 0.12 * scale);
        leaf.scale.set(1, 1.35, 1);
        leaf.castShadow = true;
        g.add(leaf);
      }
      g.position.set(x, 0, z);
      complexGroup.add(g);
    }

    /* ------------------------------------------------------------------------
       1. EXTERIOR & INTERIOR CUTAWAY WALLS (with doorways connecting zones!)
       Footprint: X = [-12, +12], Z = [-7.5, +7.5]
       Dividers at X = -3.0, X = +3.0, Z = 0.0
       ------------------------------------------------------------------------ */
    const T = 0.28;
    // North Back Wall (full)
    addWall(0, -7.5, 24.28, T, 2.75);
    // West Outer Wall
    addWall(-12, 0, T, 15.28, 2.55);
    // East Outer Wall
    addWall(12, 0, T, 15.28, 2.55);
    // South Front Wall (slightly lower cutaway front lip so you see directly into bottom rooms!)
    addWall(-7.5, 7.5, 9.2, T, 1.05);
    addWall(7.5, 7.5, 9.2, T, 1.05);
    // South Center Glass Facade Frame (Lobby / Spa front)
    addWall(-1.8, 7.5, 2.4, T, 1.05);
    addWall(1.8, 7.5, 2.4, T, 1.05);

    // Interior Vertical Partition X = -3.0 (with doorways)
    addWall(-3.0, -5.2, T, 4.4);
    addWall(-3.0, -0.5, T, 2.6);
    addWall(-3.0, 5.2, T, 4.4);

    // Interior Vertical Partition X = +3.0 (with doorways)
    addWall(3.0, -5.2, T, 4.4);
    addWall(3.0, -0.5, T, 2.6);
    addWall(3.0, 5.2, T, 4.4);

    // Interior Horizontal Partition Z = 0.0 (with doorway corridors)
    addWall(-9.3, 0, 5.2, T);
    addWall(-4.5, 0, 1.8, T);
    addWall(9.3, 0, 5.2, T);
    addWall(4.5, 0, 1.8, T);
    // Center Lobby → Spa divider with open arch gap
    addWall(-1.85, 0, 2.1, T);
    addWall(1.85, 0, 2.1, T);

    // Front South Glass Curtain accents
    const frontGlass = new THREE.Mesh(new THREE.BoxGeometry(2.4, 1.35, 0.08), matGlass);
    frontGlass.position.set(0, 0.72, 7.5);
    complexGroup.add(frontGlass);

    /* ------------------------------------------------------------------------
       2. FLOORS + INVISIBLE RAYCAST HITBOXES + GLOW PERIMETER RIMS PER ZONE
       ------------------------------------------------------------------------ */
    const hitMeshes = [];
    const zoneRims = {};

    const floorMats = {
      basket1: new THREE.MeshStandardMaterial({ map: makeBasketballTexture(), roughness: 0.36, metalness: 0.06 }),
      voleybol: new THREE.MeshStandardMaterial({ map: makeVolleyballTexture(), roughness: 0.46, metalness: 0.04 }),
      lobby: new THREE.MeshStandardMaterial({ map: makeTileTexture('#e9e5dc', 'rgba(130,120,105,0.22)', 4, 5), roughness: 0.28, metalness: 0.08 }),
      pt: new THREE.MeshStandardMaterial({ map: makeGymRubberTexture(), roughness: 0.82, metalness: 0.05 }),
      spa: new THREE.MeshStandardMaterial({ map: makeTileTexture('#d8dde6', 'rgba(90,105,125,0.24)', 4, 5), roughness: 0.32, metalness: 0.05 }),
      cafe: new THREE.MeshStandardMaterial({ map: makeWoodTexture('#c79f70', '#b88e5e'), roughness: 0.55, metalness: 0.05 }),
    };

    Object.values(ZONES).forEach((z) => {
      // Floor Surface
      const fl = new THREE.Mesh(new THREE.PlaneGeometry(z.bounds.w, z.bounds.d), floorMats[z.key]);
      fl.rotation.x = -Math.PI / 2;
      fl.position.set(z.bounds.x, 0.015, z.bounds.z);
      fl.receiveShadow = true;
      complexGroup.add(fl);

      // Glowing Perimeter Highlight Frame (shown when hovered/selected)
      const rimGroup = new THREE.Group();
      const rimMat = new THREE.MeshBasicMaterial({ color: z.color, transparent: true, opacity: 0.0 });
      const rw = z.bounds.w - 0.25;
      const rd = z.bounds.d - 0.25;
      const barT = 0.12;
      const bN = new THREE.Mesh(new THREE.BoxGeometry(rw, 0.04, barT), rimMat);
      bN.position.set(z.bounds.x, 0.04, z.bounds.z - rd / 2);
      const bS = bN.clone(); bS.position.z = z.bounds.z + rd / 2;
      const bW = new THREE.Mesh(new THREE.BoxGeometry(barT, 0.04, rd), rimMat);
      bW.position.set(z.bounds.x - rw / 2, 0.04, z.bounds.z);
      const bE = bW.clone(); bE.position.x = z.bounds.x + rw / 2;
      rimGroup.add(bN, bS, bW, bE);
      complexGroup.add(rimGroup);
      zoneRims[z.key] = { mat: rimMat, targetOpacity: 0 };

      // Invisible Raycast Volume per room (makes hover detection silky-smooth & accurate!)
      const hit = new THREE.Mesh(
        new THREE.BoxGeometry(z.bounds.w - 0.1, 2.2, z.bounds.d - 0.1),
        new THREE.MeshBasicMaterial({ visible: false })
      );
      hit.position.set(z.bounds.x, 1.1, z.bounds.z);
      hit.userData.zoneKey = z.key;
      complexGroup.add(hit);
      hitMeshes.push(hit);
    });

    /* ------------------------------------------------------------------------
       3. DETAILED 3D INTERIOR FURNISHINGS PER ZONE
       ------------------------------------------------------------------------ */

    // === ZONE A: BASKETBALL COURT 1 (Hoops, Benches, Scoreboard, Basketballs) ===
    function buildBasketballHoop(x, z, rotY) {
      const g = new THREE.Group();
      const base = new THREE.Mesh(new THREE.BoxGeometry(0.65, 0.42, 0.55), matCrimson);
      base.position.set(-0.35, 0.21, 0);
      base.castShadow = true;
      g.add(base);

      const pole = new THREE.Mesh(new THREE.BoxGeometry(0.14, 1.85, 0.14), matDarkMetal);
      pole.position.set(-0.22, 1.0, 0);
      pole.castShadow = true;
      g.add(pole);

      const arm = new THREE.Mesh(new THREE.BoxGeometry(0.55, 0.1, 0.1), matDarkMetal);
      arm.position.set(0.05, 1.82, 0);
      g.add(arm);

      const board = new THREE.Mesh(new THREE.BoxGeometry(0.06, 0.62, 0.96), matGlass);
      board.position.set(0.32, 1.85, 0);
      g.add(board);

      // Backboard crimson frame
      const bFrame = new THREE.Mesh(new THREE.BoxGeometry(0.07, 0.05, 0.98), matCrimson);
      bFrame.position.set(0.32, 1.56, 0);
      g.add(bFrame);

      const rim = new THREE.Mesh(
        new THREE.TorusGeometry(0.16, 0.022, 8, 20),
        new THREE.MeshStandardMaterial({ color: 0xff5a1f, roughness: 0.35, metalness: 0.6 })
      );
      rim.rotation.x = Math.PI / 2;
      rim.position.set(0.5, 1.66, 0);
      g.add(rim);

      g.position.set(x, 0, z);
      g.rotation.y = rotY;
      complexGroup.add(g);
    }
    buildBasketballHoop(-11.15, -3.75, 0);
    buildBasketballHoop(-3.85, -3.75, Math.PI);

    // Wall LED Scoreboard on North Wall
    const sbMesh = new THREE.Mesh(
      new THREE.BoxGeometry(2.1, 0.72, 0.12),
      new THREE.MeshBasicMaterial({ map: makeScoreboardTexture() })
    );
    sbMesh.position.set(-7.5, 1.95, -7.28);
    complexGroup.add(sbMesh);

    // Team benches & basketball on court
    const benchMat = new THREE.MeshStandardMaterial({ color: 0x3b3f4a, roughness: 0.5 });
    for (let i = 0; i < 2; i++) {
      const bn = new THREE.Mesh(new THREE.BoxGeometry(2.2, 0.32, 0.42), benchMat);
      bn.position.set(-9.1 + i * 3.2, 0.16, -6.95);
      bn.castShadow = true;
      complexGroup.add(bn);
    }
    const bBall = new THREE.Mesh(
      new THREE.SphereGeometry(0.16, 16, 16),
      new THREE.MeshStandardMaterial({ color: 0xe8611a, roughness: 0.65 })
    );
    bBall.position.set(-7.5, 0.17, -3.75);
    bBall.castShadow = true;
    complexGroup.add(bBall);

    // === ZONE B: VOLLEYBALL PRO (Net, Poles, Referee Stand, Ball Cart) ===
    const netPostL = new THREE.Mesh(new THREE.CylinderGeometry(0.06, 0.06, 1.85, 10), matCrimson);
    netPostL.position.set(7.5, 0.925, -5.85);
    netPostL.castShadow = true;
    const netPostR = netPostL.clone();
    netPostR.position.z = -1.65;
    complexGroup.add(netPostL, netPostR);

    const vNet = new THREE.Mesh(
      new THREE.PlaneGeometry(4.2, 0.68),
      new THREE.MeshStandardMaterial({ color: 0xf8fafc, transparent: true, opacity: 0.58, side: THREE.DoubleSide, wireframe: false })
    );
    vNet.rotation.y = Math.PI / 2;
    vNet.position.set(7.5, 1.48, -3.75);
    complexGroup.add(vNet);

    const netTape = new THREE.Mesh(new THREE.BoxGeometry(0.06, 0.07, 4.24), new THREE.MeshBasicMaterial({ color: 0xffffff }));
    netTape.position.set(7.5, 1.82, -3.75);
    complexGroup.add(netTape);

    // Referee Stand
    const refStand = new THREE.Mesh(new THREE.BoxGeometry(0.45, 1.15, 0.45), matDarkMetal);
    refStand.position.set(7.5, 0.575, -6.35);
    refStand.castShadow = true;
    complexGroup.add(refStand);

    // Volleyball ball cart
    const cart = new THREE.Mesh(new THREE.BoxGeometry(0.65, 0.55, 0.55), matDarkMetal);
    cart.position.set(10.8, 0.275, -6.5);
    cart.castShadow = true;
    complexGroup.add(cart);
    const vBallMat = new THREE.MeshStandardMaterial({ color: 0xfacc15, roughness: 0.45 });
    for (let i = 0; i < 3; i++) {
      const vb = new THREE.Mesh(new THREE.SphereGeometry(0.13, 12, 12), vBallMat);
      vb.position.set(10.65 + i * 0.16, 0.62, -6.45);
      complexGroup.add(vb);
    }

    // === ZONE C: CENTRAL LOBBY, SCULPTURAL SPIRAL STAIRCASE, QR TURNSTILES & EV BAY ===
    // Signature Sculptural 3D Spiral Staircase (direct architectural nod to reference image!)
    const stairGroup = new THREE.Group();
    const stairCore = new THREE.Mesh(new THREE.CylinderGeometry(0.22, 0.22, 2.55, 18), matDarkMetal);
    stairCore.position.y = 1.275;
    stairCore.castShadow = true;
    stairGroup.add(stairCore);

    const stepMat = new THREE.MeshStandardMaterial({ color: 0xd6d1c7, roughness: 0.45, metalness: 0.06 });
    const stepCount = 15;
    for (let i = 0; i < stepCount; i++) {
      const angle = (i / stepCount) * Math.PI * 1.75;
      const step = new THREE.Mesh(new THREE.BoxGeometry(1.18, 0.11, 0.36), stepMat);
      step.position.set(Math.cos(angle) * 0.66, 0.14 + i * 0.155, Math.sin(angle) * 0.66);
      step.rotation.y = -angle;
      step.castShadow = true;
      step.receiveShadow = true;
      stairGroup.add(step);
    }
    // Curved architectural half-cylinder stairwell wall wrap
    const stairWrap = new THREE.Mesh(
      new THREE.CylinderGeometry(1.38, 1.38, 2.38, 28, 1, true, Math.PI * 0.55, Math.PI * 1.15),
      new THREE.MeshStandardMaterial({ color: 0xe2e4ea, roughness: 0.7, side: THREE.DoubleSide })
    );
    stairWrap.position.y = 1.19;
    stairGroup.add(stairWrap);
    stairGroup.position.set(-0.95, 0, -5.25);
    complexGroup.add(stairGroup);

    // Reception Desk with crimson accent strip
    const desk = new THREE.Mesh(new THREE.BoxGeometry(2.2, 0.82, 0.62), matDarkMetal);
    desk.position.set(1.25, 0.41, -6.2);
    desk.castShadow = true;
    complexGroup.add(desk);
    const deskNeon = new THREE.Mesh(new THREE.BoxGeometry(2.22, 0.08, 0.64), new THREE.MeshBasicMaterial({ color: 0xe31837 }));
    deskNeon.position.set(1.25, 0.82, -6.2);
    complexGroup.add(deskNeon);

    // 3 Sleek QR Turnstiles with green LED indicators
    for (let i = 0; i < 3; i++) {
      const tx = -1.2 + i * 1.2;
      const pillar = new THREE.Mesh(new THREE.BoxGeometry(0.18, 0.72, 0.85), matChrome);
      pillar.position.set(tx, 0.36, -1.65);
      pillar.castShadow = true;
      complexGroup.add(pillar);
      const led = new THREE.Mesh(new THREE.BoxGeometry(0.1, 0.05, 0.14), new THREE.MeshBasicMaterial({ color: 0x30d158 }));
      led.position.set(tx, 0.74, -1.35);
      complexGroup.add(led);
    }

    // Mini EV Car Showcase / Charging Bay accent in Lobby-East alcove
    const evCar = new THREE.Group();
    const carBody = new THREE.Mesh(
      new THREE.BoxGeometry(1.05, 0.36, 2.1),
      new THREE.MeshStandardMaterial({ color: 0x1e293b, roughness: 0.22, metalness: 0.85 })
    );
    carBody.position.y = 0.28;
    carBody.castShadow = true;
    const carCab = new THREE.Mesh(
      new THREE.BoxGeometry(0.88, 0.28, 1.05),
      new THREE.MeshStandardMaterial({ color: 0x0f172a, roughness: 0.15, metalness: 0.9 })
    );
    carCab.position.set(0, 0.56, -0.05);
    evCar.add(carBody, carCab);
    evCar.position.set(1.65, 0, -3.7);
    complexGroup.add(evCar);

    const evCharger = new THREE.Mesh(new THREE.BoxGeometry(0.24, 1.1, 0.22), matDarkMetal);
    evCharger.position.set(2.55, 0.55, -3.7);
    complexGroup.add(evCharger);
    const evLed = new THREE.Mesh(new THREE.BoxGeometry(0.26, 0.28, 0.05), new THREE.MeshBasicMaterial({ color: 0xa4ff2e }));
    evLed.position.set(2.55, 0.86, -3.58);
    complexGroup.add(evLed);

    // === ZONE D: PT & STRENGTH STUDIO (Sprint Turf, Squat Racks, Dumbbells, Treadmills) ===
    // Green Sprint Turf Strip
    const turf = new THREE.Mesh(
      new THREE.PlaneGeometry(7.6, 1.6),
      new THREE.MeshStandardMaterial({ color: 0x267343, roughness: 0.92 })
    );
    turf.rotation.x = -Math.PI / 2;
    turf.position.set(-7.5, 0.03, 5.9);
    turf.receiveShadow = true;
    complexGroup.add(turf);

    // 2 Olympic Power Squat Racks with Barbells & Red/Yellow Bumper Plates
    function buildPowerRack(x, z) {
      const g = new THREE.Group();
      const postPos = [[-0.55, -0.45], [0.55, -0.45], [-0.55, 0.45], [0.55, 0.45]];
      postPos.forEach(([px, pz]) => {
        const p = new THREE.Mesh(new THREE.BoxGeometry(0.08, 1.8, 0.08), matDarkMetal);
        p.position.set(px, 0.9, pz);
        p.castShadow = true;
        g.add(p);
      });
      const topBar = new THREE.Mesh(new THREE.BoxGeometry(1.18, 0.08, 0.98), matCrimson);
      topBar.position.y = 1.78;
      g.add(topBar);
      // Barbell + plates
      const bar = new THREE.Mesh(new THREE.CylinderGeometry(0.025, 0.025, 1.8, 8), matChrome);
      bar.rotation.z = Math.PI / 2;
      bar.position.set(0, 1.05, 0.42);
      g.add(bar);
      [-0.75, 0.75].forEach((bx) => {
        const plate = new THREE.Mesh(new THREE.CylinderGeometry(0.22, 0.22, 0.08, 14), matCrimson);
        plate.rotation.z = Math.PI / 2;
        plate.position.set(bx, 1.05, 0.42);
        plate.castShadow = true;
        g.add(plate);
      });
      // Weight bench inside rack
      const bench = new THREE.Mesh(new THREE.BoxGeometry(0.36, 0.32, 0.95), matDarkMetal);
      bench.position.set(0, 0.16, 0.05);
      bench.castShadow = true;
      g.add(bench);

      g.position.set(x, 0, z);
      complexGroup.add(g);
    }
    buildPowerRack(-10.1, 1.65);
    buildPowerRack(-7.7, 1.65);

    // 3 Cardio Treadmills
    for (let i = 0; i < 3; i++) {
      const tx = -5.6 + i * 0.92;
      const deck = new THREE.Mesh(new THREE.BoxGeometry(0.62, 0.14, 1.45), matDarkMetal);
      deck.position.set(tx, 0.08, 1.85);
      deck.castShadow = true;
      complexGroup.add(deck);
      const consolePost = new THREE.Mesh(new THREE.BoxGeometry(0.56, 0.92, 0.14), matDarkMetal);
      consolePost.position.set(tx, 0.56, 1.2);
      consolePost.rotation.x = 0.15;
      complexGroup.add(consolePost);
      const screen = new THREE.Mesh(new THREE.BoxGeometry(0.42, 0.24, 0.04), new THREE.MeshBasicMaterial({ color: 0x26e6ff }));
      screen.position.set(tx, 0.94, 1.28);
      complexGroup.add(screen);
    }

    // Dumbbell Rack along West Wall
    const dbRack = new THREE.Mesh(new THREE.BoxGeometry(0.45, 0.58, 2.4), matDarkMetal);
    dbRack.position.set(-11.4, 0.29, 4.1);
    dbRack.castShadow = true;
    complexGroup.add(dbRack);

    // === ZONE E: RFID LOCKER ROOM, GLASS RAIN SHOWERS & CEDAR FIN SAUNA ===
    // Sub-partition walls inside Spa zone (creates realistic bathroom/sauna rooms like reference!)
    addWall(0, 3.6, 5.7, 0.18, 2.1);
    addWall(0.2, 5.5, 0.18, 3.6, 2.1);

    // North half of Spa: RFID Locker Rows + Oak Changing Bench
    for (let i = 0; i < 5; i++) {
      const lk = new THREE.Mesh(
        new THREE.BoxGeometry(0.48, 1.55, 0.44),
        i % 2 === 0 ? matDarkMetal : matCrimson
      );
      lk.position.set(-2.1 + i * 0.56, 0.775, 0.65);
      lk.castShadow = true;
      complexGroup.add(lk);
    }
    const lkBench = new THREE.Mesh(new THREE.BoxGeometry(2.6, 0.34, 0.48), matOak);
    lkBench.position.set(-0.9, 0.17, 2.1);
    lkBench.castShadow = true;
    complexGroup.add(lkBench);

    // South-West sub-room: Cedar Fin Sauna with warm amber glow + glowing stove!
    const saunaFloor = new THREE.Mesh(new THREE.PlaneGeometry(2.85, 3.55), new THREE.MeshStandardMaterial({ map: makeWoodTexture('#c78343', '#b37133'), roughness: 0.6 }));
    saunaFloor.rotation.x = -Math.PI / 2;
    saunaFloor.position.set(-1.38, 0.025, 5.5);
    complexGroup.add(saunaFloor);

    const sBench1 = new THREE.Mesh(new THREE.BoxGeometry(2.5, 0.32, 0.58), matCedar);
    sBench1.position.set(-1.4, 0.36, 4.15);
    sBench1.castShadow = true;
    const sBench2 = new THREE.Mesh(new THREE.BoxGeometry(2.5, 0.32, 0.58), matCedar);
    sBench2.position.set(-1.4, 0.72, 4.78);
    sBench2.castShadow = true;
    complexGroup.add(sBench1, sBench2);

    const stove = new THREE.Mesh(new THREE.CylinderGeometry(0.24, 0.24, 0.62, 12), matDarkMetal);
    stove.position.set(-2.2, 0.31, 6.5);
    complexGroup.add(stove);
    const coals = new THREE.Mesh(new THREE.CylinderGeometry(0.2, 0.2, 0.08, 12), new THREE.MeshBasicMaterial({ color: 0xff5a1f }));
    coals.position.set(-2.2, 0.65, 6.5);
    complexGroup.add(coals);

    const saunaGlow = new THREE.PointLight(0xff8c2b, 1.35, 5.5, 2);
    saunaGlow.position.set(-1.4, 1.5, 5.4);
    complexGroup.add(saunaGlow);

    // South-East sub-room: Glass Rain Shower Cubicles
    for (let i = 0; i < 2; i++) {
      const divider = new THREE.Mesh(new THREE.BoxGeometry(0.06, 1.85, 1.5), matGlass);
      divider.position.set(1.15 + i * 1.1, 0.925, 4.75);
      complexGroup.add(divider);
      const showerHead = new THREE.Mesh(new THREE.CylinderGeometry(0.14, 0.14, 0.05, 12), matChrome);
      showerHead.position.set(0.72 + i * 1.1, 1.75, 4.25);
      complexGroup.add(showerHead);
    }

    // === ZONE F: APEX PROTEIN CAFE & LOUNGE ===
    // L-Shaped Marble/Dark-Wood Barista Counter
    const barMain = new THREE.Mesh(new THREE.BoxGeometry(4.2, 0.92, 0.78), matDarkMetal);
    barMain.position.set(8.6, 0.46, 1.15);
    barMain.castShadow = true;
    complexGroup.add(barMain);
    const barTop = new THREE.Mesh(
      new THREE.BoxGeometry(4.3, 0.08, 0.88),
      new THREE.MeshStandardMaterial({ color: 0xf1f5f9, roughness: 0.25, metalness: 0.05 })
    );
    barTop.position.set(8.6, 0.94, 1.15);
    complexGroup.add(barTop);

    // Espresso machine + shaker station
    const espresso = new THREE.Mesh(new THREE.BoxGeometry(0.68, 0.42, 0.45), matCrimson);
    espresso.position.set(7.6, 1.18, 1.1);
    espresso.castShadow = true;
    complexGroup.add(espresso);

    // 4 Bar Stools
    for (let i = 0; i < 4; i++) {
      const sx = 7.0 + i * 1.05;
      const leg = new THREE.Mesh(new THREE.CylinderGeometry(0.04, 0.16, 0.56, 10), matDarkMetal);
      leg.position.set(sx, 0.28, 2.02);
      const seat = new THREE.Mesh(new THREE.CylinderGeometry(0.22, 0.22, 0.08, 14), matOak);
      seat.position.set(sx, 0.58, 2.02);
      seat.castShadow = true;
      complexGroup.add(leg, seat);
    }

    // 3 Round Lounge Café Tables + Chairs
    const tableCoords = [[5.4, 4.9], [8.1, 5.2], [10.4, 4.5]];
    tableCoords.forEach(([tx, tz]) => {
      const tTop = new THREE.Mesh(new THREE.CylinderGeometry(0.58, 0.58, 0.07, 18), matOak);
      tTop.position.set(tx, 0.64, tz);
      tTop.castShadow = true;
      const tLeg = new THREE.Mesh(new THREE.CylinderGeometry(0.05, 0.22, 0.62, 10), matDarkMetal);
      tLeg.position.set(tx, 0.31, tz);
      complexGroup.add(tTop, tLeg);

      // 2 chairs per table
      [-0.78, 0.78].forEach((dx) => {
        const ch = new THREE.Mesh(new THREE.BoxGeometry(0.38, 0.42, 0.38), matDarkMetal);
        ch.position.set(tx + dx, 0.21, tz);
        ch.castShadow = true;
        complexGroup.add(ch);
      });
    });

    // Potted architectural indoor plants around Lobby, Cafe & PT!
    addPlant(3.55, 6.6, 1.05);
    addPlant(11.2, 6.5, 1.15);
    addPlant(-2.45, -6.75, 1.0);
    addPlant(-11.2, 6.7, 1.0);

    /* ------------------------------------------------------------------------
       4. INTERACTION STATE: HOVER DIVE-IN, CLICK LOCK, DRAG ORBIT & INSPECTOR
       ------------------------------------------------------------------------ */
    const raycaster = new THREE.Raycaster();
    const pointer = new THREE.Vector2(-99, -99);
    const pointerNdc = new THREE.Vector2(0, 0);

    let hoveredZone = null;
    let lockedZone = null;
    let chipHovered = false;
    let autoOrbit = false;
    let isDragging = false;
    let dragStartX = 0;
    let dragYawOffset = 0;
    let targetYawOffset = 0;

    const desiredCamPos = OVERVIEW_POS.clone();
    const desiredCamLook = OVERVIEW_LOOK.clone();

    function updateInspectorUI(zoneKey) {
      const info = ZONES[zoneKey];
      if (!info) return;

      const elTag = document.getElementById('fp-d-tag');
      const elTitle = document.getElementById('fp-d-title');
      const elDesc = document.getElementById('fp-d-desc');
      const elSpecs = document.getElementById('fp-d-specs');
      if (elTag) elTag.textContent = info.tag;
      if (elTitle) elTitle.textContent = info.title;
      if (elDesc) elDesc.textContent = info.desc;
      if (elSpecs) {
        elSpecs.innerHTML = info.specs.map((sp) => `<span>${sp}</span>`).join('');
      }
      if (fpCta) {
        fpCta.textContent = info.cta;
        fpCta.setAttribute('href', info.url);
      }
    }

    function syncActiveVisuals() {
      const activeKey = lockedZone || hoveredZone;

      // Update zone rim highlights
      Object.entries(zoneRims).forEach(([k, rim]) => {
        rim.targetOpacity = k === activeKey ? 0.92 : 0.0;
      });

      // Update quick-select chips
      zoneChips.forEach((btn) => {
        btn.classList.toggle('is-active', btn.getAttribute('data-arch-chip') === activeKey);
      });

      if (activeKey && ZONES[activeKey]) {
        const z = ZONES[activeKey];
        desiredCamPos.copy(z.camPos);
        desiredCamLook.copy(z.camLook);
        hoverLight.color.setHex(z.color);
        hoverLight.position.set(z.bounds.x, 4.4, z.bounds.z);
        updateInspectorUI(activeKey);

        if (hudTitle) hudTitle.textContent = `${z.name} · ${z.status}`;
        if (hudHint) {
          hudHint.textContent = lockedZone
            ? 'Oda kilitlendi · Çıkmak için Genel Bakış veya ESC'
            : 'İçeri 3D yaklaşıldı · Sabitlemek için tıkla';
        }
        if (hudPill) hudPill.classList.add('is-focused');
        if (resetBtn) resetBtn.hidden = !lockedZone;
      } else {
        desiredCamPos.copy(OVERVIEW_POS);
        desiredCamLook.copy(OVERVIEW_LOOK);
        if (hudTitle) hudTitle.textContent = '3D Kompleks Mimarisi · 6 Aktif Bölge';
        if (hudHint) hudHint.textContent = 'Mouse ile bir odanın üstüne gel — kamera 3D içine yaklaşır';
        if (hudPill) hudPill.classList.remove('is-focused');
        if (resetBtn) resetBtn.hidden = true;
      }
    }

    function resetToOverview() {
      lockedZone = null;
      hoveredZone = null;
      targetYawOffset = 0;
      syncActiveVisuals();
    }

    // Pointer Move -> Real-time 3D Raycast Hover Dive-In
    canvas.addEventListener('pointermove', (e) => {
      const rect = canvas.getBoundingClientRect();
      if (!rect.width || !rect.height) return;
      const nx = ((e.clientX - rect.left) / rect.width) * 2 - 1;
      const ny = -((e.clientY - rect.top) / rect.height) * 2 + 1;
      pointer.set(nx, ny);
      pointerNdc.set(nx, ny);

      if (isDragging) {
        const dx = e.clientX - dragStartX;
        dragStartX = e.clientX;
        targetYawOffset = Math.max(-0.85, Math.min(0.85, targetYawOffset + dx * 0.0045));
        return;
      }

      if (lockedZone) return;

      raycaster.setFromCamera(pointer, camera);
      const hits = raycaster.intersectObjects(hitMeshes, false);
      const hitKey = hits.length > 0 ? hits[0].object.userData.zoneKey : null;
      if (hitKey !== hoveredZone) {
        hoveredZone = hitKey;
        canvas.style.cursor = hitKey ? 'pointer' : 'grab';
        syncActiveVisuals();
      }
    });

    canvas.addEventListener('pointerleave', () => {
      isDragging = false;
      pointerNdc.set(0, 0);
      if (!lockedZone && !chipHovered && hoveredZone !== null) {
        hoveredZone = null;
        canvas.style.cursor = 'grab';
        syncActiveVisuals();
      }
    });

    canvas.addEventListener('pointerdown', (e) => {
      isDragging = true;
      dragStartX = e.clientX;
      canvas.dataset.downX = String(e.clientX);
      canvas.dataset.downY = String(e.clientY);
      canvas.style.cursor = 'grabbing';
    });

    window.addEventListener('pointerup', (e) => {
      if (!isDragging) return;
      isDragging = false;
      canvas.style.cursor = hoveredZone ? 'pointer' : 'grab';

      const dx = Math.abs(e.clientX - Number(canvas.dataset.downX || 0));
      const dy = Math.abs(e.clientY - Number(canvas.dataset.downY || 0));
      if (dx < 6 && dy < 6) {
        // Click detected: toggle lock on hovered room
        const rect = canvas.getBoundingClientRect();
        if (e.clientX >= rect.left && e.clientX <= rect.right && e.clientY >= rect.top && e.clientY <= rect.bottom) {
          raycaster.setFromCamera(pointer, camera);
          const hits = raycaster.intersectObjects(hitMeshes, false);
          const hitKey = hits.length > 0 ? hits[0].object.userData.zoneKey : null;
          if (hitKey) {
            lockedZone = lockedZone === hitKey ? null : hitKey;
            hoveredZone = hitKey;
            syncActiveVisuals();
          } else if (lockedZone) {
            resetToOverview();
          }
        }
      }
    });

    // Quick Zone Chips (Hover zooms in immediately, Click locks!)
    zoneChips.forEach((chip) => {
      const zKey = chip.getAttribute('data-arch-chip');
      chip.addEventListener('mouseenter', () => {
        chipHovered = true;
        if (!lockedZone && zKey && ZONES[zKey]) {
          hoveredZone = zKey;
          syncActiveVisuals();
        }
      });
      chip.addEventListener('mouseleave', () => {
        chipHovered = false;
        if (!lockedZone) {
          hoveredZone = null;
          syncActiveVisuals();
        }
      });
      chip.addEventListener('click', () => {
        if (!zKey || !ZONES[zKey]) return;
        lockedZone = lockedZone === zKey ? null : zKey;
        hoveredZone = zKey;
        syncActiveVisuals();
      });
    });

    resetBtn?.addEventListener('click', resetToOverview);

    orbitBtn?.addEventListener('click', () => {
      autoOrbit = !autoOrbit;
      orbitBtn.classList.toggle('is-active', autoOrbit);
      if (autoOrbit && lockedZone) {
        lockedZone = null;
        hoveredZone = null;
        syncActiveVisuals();
      }
    });

    window.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && lockedZone) {
        resetToOverview();
      }
    });

    // Responsive ResizeObserver
    function resize() {
      const w = stageEl.clientWidth || 640;
      const h = stageEl.clientHeight || 480;
      renderer.setSize(w, h, false);
      camera.aspect = w / h;
      camera.updateProjectionMatrix();
    }
    resize();
    const ro = new ResizeObserver(resize);
    ro.observe(stageEl);

    // Animation Loop with Smooth Exponential Damping
    let lastTime = performance.now();
    let orbitAngle = 0;

    function animate(now) {
      if (!isVisible || document.hidden) {
        rafId = 0;
        return;
      }
      rafId = requestAnimationFrame(animate);
      const dt = Math.min(0.05, (now - lastTime) / 1000);
      lastTime = now;

      const activeKey = lockedZone || hoveredZone;

      // Subtle architectural rotation or auto-orbit
      if (autoOrbit && !activeKey) {
        orbitAngle += dt * 0.24;
        complexGroup.rotation.y += (Math.sin(orbitAngle) * 0.55 - complexGroup.rotation.y) * Math.min(1, dt * 3.5);
      } else {
        dragYawOffset += (targetYawOffset - dragYawOffset) * Math.min(1, dt * 6);
        const subtleParallax = activeKey ? pointerNdc.x * 0.06 : pointerNdc.x * 0.1;
        complexGroup.rotation.y += (dragYawOffset + subtleParallax - complexGroup.rotation.y) * Math.min(1, dt * 5);
      }

      // Smooth 3D Camera Dive-In / Pull-Out Interpolation
      const camEase = 1 - Math.exp(-dt * (activeKey ? 5.6 : 4.2));
      const targetPos = desiredCamPos.clone();
      if (activeKey) {
        // Gentle live micro-parallax inside the zoomed room
        targetPos.x += pointerNdc.x * 0.42;
        targetPos.y += pointerNdc.y * 0.28;
      } else {
        targetPos.x += pointerNdc.x * 0.7;
        targetPos.y += pointerNdc.y * 0.35;
      }

      camera.position.lerp(targetPos, camEase);
      currentLook.lerp(desiredCamLook, camEase);
      camera.lookAt(currentLook);

      // Smooth Hover Spotlight + Floor Rim Opacity
      const targetIntensity = activeKey ? 2.1 : 0.0;
      hoverLight.intensity += (targetIntensity - hoverLight.intensity) * Math.min(1, dt * 7);

      Object.values(zoneRims).forEach((r) => {
        r.mat.opacity += (r.targetOpacity - r.mat.opacity) * Math.min(1, dt * 8);
      });

      // Subtle pulsing on warm Fin sauna light
      saunaGlow.intensity = 1.25 + Math.sin(now * 0.003) * 0.2;

      renderer.render(scene, camera);
    }

    startLoopFn = () => {
      if (!rafId && isVisible && !document.hidden) {
        lastTime = performance.now();
        rafId = requestAnimationFrame(animate);
      }
    };

    document.addEventListener('visibilitychange', () => {
      if (!document.hidden && isVisible) startLoopFn();
    });

    syncActiveVisuals();
    // Immediate synchronous first frame so the 3D complex is visible on frame 0
    renderer.render(scene, camera);
    startLoopFn();
  }
}
