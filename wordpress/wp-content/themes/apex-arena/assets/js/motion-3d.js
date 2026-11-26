/**
 * APEX ARENA — 3D Perspective Monolith & Cast-Iron Spin-Lock 3D Dumbbell WebGL Engine (v1.6.0)
 * Senior Frontend Performance Engineering Pass:
 * - Lazy Three.js + PBR texture initialization via IntersectionObserver (0ms Hero load blocking!)
 * - True off-screen rAF pause & visibilitychange sleep (0% off-screen CPU/GPU usage!)
 * - Zero forced synchronous layouts during scroll/pointermove (ResizeObserver-cached geometry)
 * - Dirty-checked monolith CSS transforms & quantized borderRadius (no idle clip-mask rebuilds)
 * - Adaptive mobile/mid-range quality (pixelRatio, procedural texture resolution, lathe segments)
 * - Full WebGL geometry/material/texture/renderer disposal on pagehide
 *
 * @package ApexArena
 */
import * as THREE from './vendor/three.module.js';

const runway = document.getElementById('apex-3d-runway');
if (runway) {
  initMotionStage(runway);
}

/* ============================================================================
   Deterministic 2D Noise for Forged Cast-Iron & Knurled Steel PBR Maps
   ============================================================================ */
function hash2(x, y) {
  let h = (Math.imul(x | 0, 374761393) + Math.imul(y | 0, 668265263)) | 0;
  h = Math.imul(h ^ (h >>> 13), 1274126177);
  return ((h ^ (h >>> 16)) >>> 0) * 2.3283064365386963e-10;
}

function noise2(x, y) {
  const ix = Math.floor(x);
  const iy = Math.floor(y);
  const fx = x - ix;
  const fy = y - iy;
  const ux = fx * fx * (3 - 2 * fx);
  const uy = fy * fy * (3 - 2 * fy);
  const a = hash2(ix, iy);
  const b = hash2(ix + 1, iy);
  const c = hash2(ix, iy + 1);
  const d = hash2(ix + 1, iy + 1);
  return a + (b - a) * ux + (c - a) * uy + (a - b - c + d) * ux * uy;
}

function fbm2(x, y, octaves = 4) {
  let v = 0;
  let amp = 0.5;
  let freq = 1;
  for (let i = 0; i < octaves; i++) {
    v += amp * noise2(x * freq, y * freq);
    freq *= 2.05;
    amp *= 0.5;
  }
  return v;
}

/* ============================================================================
   Procedural PBR Textures: Sand-Cast Iron Plates + Knurled Brushed Steel Grip
   ============================================================================ */
function createCastIronMaps(size) {
  const W = size;
  const H = size;
  const albedoC = document.createElement('canvas');
  const roughC = document.createElement('canvas');
  const bumpC = document.createElement('canvas');
  albedoC.width = roughC.width = bumpC.width = W;
  albedoC.height = roughC.height = bumpC.height = H;

  const aCtx = albedoC.getContext('2d');
  const rCtx = roughC.getContext('2d');
  const bCtx = bumpC.getContext('2d');

  const aImg = aCtx.createImageData(W, H);
  const rImg = rCtx.createImageData(W, H);
  const bImg = bCtx.createImageData(W, H);

  for (let y = 0; y < H; y++) {
    const v = y / H;
    for (let x = 0; x < W; x++) {
      const u = x / W;
      const coarse = fbm2(u * 10, v * 10, 3);
      const scuff = fbm2(u * 24 + 5.1, v * 24 - 2.3, 2);
      const grain = hash2(x, y);

      const wear = Math.max(0, scuff - 0.56) * 2.4;
      const base = 38 + coarse * 26 + wear * 58 + (grain - 0.5) * 14;
      const ar = Math.min(135, Math.max(24, base + 2));
      const ag = Math.min(132, Math.max(24, base + 1));
      const ab = Math.min(138, Math.max(26, base + 4));

      const rough = Math.min(235, Math.max(72, 168 - wear * 85 + (grain - 0.5) * 30));
      const bump = Math.min(255, Math.max(0, 128 + (coarse - 0.5) * 70 + (grain - 0.5) * 85));

      const i = (y * W + x) * 4;
      aImg.data[i] = ar;
      aImg.data[i + 1] = ag;
      aImg.data[i + 2] = ab;
      aImg.data[i + 3] = 255;

      rImg.data[i] = rImg.data[i + 1] = rImg.data[i + 2] = rough;
      rImg.data[i + 3] = 255;

      bImg.data[i] = bImg.data[i + 1] = bImg.data[i + 2] = bump;
      bImg.data[i + 3] = 255;
    }
  }

  aCtx.putImageData(aImg, 0, 0);
  rCtx.putImageData(rImg, 0, 0);
  bCtx.putImageData(bImg, 0, 0);

  const map = new THREE.CanvasTexture(albedoC);
  map.colorSpace = THREE.SRGBColorSpace;
  map.wrapS = map.wrapT = THREE.RepeatWrapping;
  map.repeat.set(2, 2);

  const roughnessMap = new THREE.CanvasTexture(roughC);
  roughnessMap.wrapS = roughnessMap.wrapT = THREE.RepeatWrapping;
  roughnessMap.repeat.set(2, 2);

  const bumpMap = new THREE.CanvasTexture(bumpC);
  bumpMap.wrapS = bumpMap.wrapT = THREE.RepeatWrapping;
  bumpMap.repeat.set(2, 2);

  return { map, roughnessMap, bumpMap };
}

function createKnurledSteelMaps(width) {
  const W = width;
  const H = width >> 1;
  const albedoC = document.createElement('canvas');
  const bumpC = document.createElement('canvas');
  albedoC.width = bumpC.width = W;
  albedoC.height = bumpC.height = H;

  const aCtx = albedoC.getContext('2d');
  const bCtx = bumpC.getContext('2d');
  const aImg = aCtx.createImageData(W, H);
  const bImg = bCtx.createImageData(W, H);

  for (let y = 0; y < H; y++) {
    const v = y / H;
    const inGripZone = v > 0.14 && v < 0.86;
    for (let x = 0; x < W; x++) {
      const u = x / W;
      const brush = fbm2(u * 6, v * 48, 3);
      const patina = fbm2(u * 10 + 3, v * 10 + 1, 3);

      let knurl = 0.5;
      if (inGripZone) {
        const d1 = Math.abs(((u * 54 + v * 28) % 1) - 0.5) * 2;
        const d2 = Math.abs(((u * 54 - v * 28 + 100) % 1) - 0.5) * 2;
        knurl = Math.min(d1, d2);
      }

      const lum = 108 + brush * 34 + (patina - 0.5) * 22 - (inGripZone ? (1 - knurl) * 22 : 0);
      const r = Math.min(200, Math.max(68, lum + 4));
      const g = Math.min(198, Math.max(68, lum + 2));
      const b = Math.min(206, Math.max(72, lum + 6));
      const bmp = inGripZone ? Math.round(70 + knurl * 175) : Math.round(125 + (brush - 0.5) * 40);

      const i = (y * W + x) * 4;
      aImg.data[i] = r;
      aImg.data[i + 1] = g;
      aImg.data[i + 2] = b;
      aImg.data[i + 3] = 255;

      bImg.data[i] = bImg.data[i + 1] = bImg.data[i + 2] = bmp;
      bImg.data[i + 3] = 255;
    }
  }

  aCtx.putImageData(aImg, 0, 0);
  bCtx.putImageData(bImg, 0, 0);

  const map = new THREE.CanvasTexture(albedoC);
  map.colorSpace = THREE.SRGBColorSpace;
  map.wrapS = map.wrapT = THREE.RepeatWrapping;

  const bumpMap = new THREE.CanvasTexture(bumpC);
  bumpMap.wrapS = bumpMap.wrapT = THREE.RepeatWrapping;

  return { map, bumpMap };
}

/* ============================================================================
   High-Contrast Studio Softbox Environment
   ============================================================================ */
function buildStudioEnvironment(renderer) {
  const pmrem = new THREE.PMREMGenerator(renderer);
  const envScene = new THREE.Scene();
  envScene.background = new THREE.Color(0x14151c);
  const tempGeos = [];
  const tempMats = [];

  const addPanel = (color, intensity, pos, scale, rot) => {
    const mat = new THREE.MeshBasicMaterial({
      color: new THREE.Color(color).multiplyScalar(intensity),
      side: THREE.DoubleSide,
    });
    const geo = new THREE.PlaneGeometry(1, 1);
    tempGeos.push(geo);
    tempMats.push(mat);
    const mesh = new THREE.Mesh(geo, mat);
    mesh.position.set(...pos);
    mesh.scale.set(...scale);
    mesh.rotation.set(...rot);
    envScene.add(mesh);
  };

  addPanel(0xffffff, 5.8, [0, 7, 1.5], [14, 5, 1], [Math.PI / 2, 0, 0]);
  addPanel(0xfff0cc, 4.8, [8, 2.5, 4], [5, 10, 1], [0, -Math.PI / 2.3, 0]);
  addPanel(0xd4e2ff, 4.4, [-8, 1.5, 2], [5, 10, 1], [0, Math.PI / 2.3, 0]);
  addPanel(0xf4e268, 2.2, [0, -6.5, 2], [12, 5, 1], [-Math.PI / 2, 0, 0]);
  addPanel(0xeef1f7, 2.4, [0, 1.2, 9], [8, 5, 1], [0, Math.PI, 0]);

  const envRT = pmrem.fromScene(envScene, 0.035);
  tempGeos.forEach((g) => g.dispose());
  tempMats.forEach((m) => m.dispose());
  pmrem.dispose();
  return envRT;
}

/* ============================================================================
   Procedural CAD Sculpting: Cast-Iron Spin-Lock Dumbbell
   ============================================================================ */
function buildCastIronPlateGeometry(outerR, thickness, boreR, radialSegments) {
  const halfT = thickness * 0.5;
  const bevel = 0.042;
  const rimWidth = outerR * 0.16;
  const dishDepth = thickness * 0.23;
  const hubR = boreR + 0.15;

  const pts = [
    new THREE.Vector2(boreR, -halfT + 0.015),
    new THREE.Vector2(boreR + 0.02, -halfT),
    new THREE.Vector2(hubR, -halfT),
    new THREE.Vector2(hubR + 0.05, -halfT + dishDepth),
    new THREE.Vector2(outerR - rimWidth - 0.04, -halfT + dishDepth),
    new THREE.Vector2(outerR - rimWidth, -halfT),
    new THREE.Vector2(outerR - bevel, -halfT),
    new THREE.Vector2(outerR, -halfT + bevel),
    new THREE.Vector2(outerR, halfT - bevel),
    new THREE.Vector2(outerR - bevel, halfT),
    new THREE.Vector2(outerR - rimWidth, halfT),
    new THREE.Vector2(outerR - rimWidth - 0.04, halfT - dishDepth),
    new THREE.Vector2(hubR + 0.05, halfT - dishDepth),
    new THREE.Vector2(hubR, halfT),
    new THREE.Vector2(boreR + 0.02, halfT),
    new THREE.Vector2(boreR, halfT - 0.015),
  ];

  const geo = new THREE.LatheGeometry(pts, radialSegments);
  geo.rotateZ(Math.PI / 2);
  geo.computeVertexNormals();
  return geo;
}

function buildThreadedShaftGeometry(length, baseR, threadCount, radialSegments) {
  const pts = [
    new THREE.Vector2(0.001, 0),
    new THREE.Vector2(baseR - 0.018, 0),
    new THREE.Vector2(baseR, 0.02),
  ];
  const usable = length - 0.04;
  const step = usable / threadCount;

  for (let i = 0; i < threadCount; i++) {
    const y0 = 0.02 + i * step;
    pts.push(new THREE.Vector2(baseR * 0.87, y0 + step * 0.22));
    pts.push(new THREE.Vector2(baseR * 1.06, y0 + step * 0.52));
    pts.push(new THREE.Vector2(baseR * 1.06, y0 + step * 0.68));
    pts.push(new THREE.Vector2(baseR * 0.87, y0 + step * 0.96));
  }
  pts.push(new THREE.Vector2(baseR, length - 0.015));
  pts.push(new THREE.Vector2(baseR - 0.018, length));
  pts.push(new THREE.Vector2(0.001, length));

  const geo = new THREE.LatheGeometry(pts, radialSegments);
  geo.computeVertexNormals();
  return geo;
}

function buildStarSpinLockGeometry(innerR, outerR, depth, curveSegments) {
  const shape = new THREE.Shape();
  const lobes = 6;
  const steps = lobes * (curveSegments >= 28 ? 24 : 16);
  for (let i = 0; i <= steps; i++) {
    const a = (i / steps) * Math.PI * 2;
    const wave = Math.cos(a * lobes);
    const shaped = Math.sign(wave) * Math.pow(Math.abs(wave), 0.72);
    const r = outerR * (0.81 + 0.19 * shaped);
    const x = Math.cos(a) * r;
    const y = Math.sin(a) * r;
    if (i === 0) shape.moveTo(x, y);
    else shape.lineTo(x, y);
  }
  shape.closePath();

  const hole = new THREE.Path();
  hole.absarc(0, 0, innerR, 0, Math.PI * 2, true);
  shape.holes.push(hole);

  const geo = new THREE.ExtrudeGeometry(shape, {
    depth,
    bevelEnabled: true,
    bevelThickness: 0.022,
    bevelSize: 0.018,
    bevelSegments: curveSegments >= 28 ? 4 : 2,
    curveSegments,
  });
  geo.translate(0, 0, -depth * 0.5);
  geo.rotateY(Math.PI / 2);
  geo.computeVertexNormals();
  return geo;
}

function createDumbbellSculpture(isLowTier) {
  const group = new THREE.Group();
  const texSize = isLowTier ? 192 : 256;
  const plateSegs = isLowTier ? 56 : 96;
  const shaftSegs = isLowTier ? 32 : 48;
  const curveSegs = isLowTier ? 18 : 32;

  const ironMaps = createCastIronMaps(texSize);
  const steelMaps = createKnurledSteelMaps(texSize);

  const ironMat = new THREE.MeshStandardMaterial({
    map: ironMaps.map,
    roughnessMap: ironMaps.roughnessMap,
    bumpMap: ironMaps.bumpMap,
    bumpScale: 0.026,
    color: 0xffffff,
    metalness: 0.76,
    roughness: 0.92,
    envMapIntensity: 1.55,
  });

  const steelGripMat = new THREE.MeshStandardMaterial({
    map: steelMaps.map,
    bumpMap: steelMaps.bumpMap,
    bumpScale: 0.02,
    color: 0xd0d4dc,
    metalness: 0.88,
    roughness: 0.32,
    envMapIntensity: 1.65,
  });

  const collarSteelMat = new THREE.MeshStandardMaterial({
    color: 0x9aa0aa,
    metalness: 0.84,
    roughness: 0.36,
    envMapIntensity: 1.5,
  });

  const threadSteelMat = new THREE.MeshStandardMaterial({
    color: 0x62666f,
    metalness: 0.86,
    roughness: 0.34,
    envMapIntensity: 1.55,
  });

  const starNutMat = new THREE.MeshStandardMaterial({
    color: 0x52555e,
    metalness: 0.82,
    roughness: 0.36,
    envMapIntensity: 1.65,
  });

  const handleLen = 1.48;
  const barR = 0.142;
  const handleGeo = new THREE.CylinderGeometry(barR, barR, handleLen, shaftSegs, 1);
  handleGeo.rotateZ(Math.PI / 2);
  group.add(new THREE.Mesh(handleGeo, steelGripMat));

  const stopCollarGeo = new THREE.CylinderGeometry(0.225, 0.225, 0.13, shaftSegs);
  stopCollarGeo.rotateZ(Math.PI / 2);

  const bigPlateGeo = buildCastIronPlateGeometry(1.06, 0.25, barR * 1.02, plateSegs);
  const smallPlateGeo = buildCastIronPlateGeometry(0.84, 0.23, barR * 1.02, plateSegs);
  const starNutGeo = buildStarSpinLockGeometry(barR * 0.98, 0.39, 0.14, curveSegs);
  const threadGeo = buildThreadedShaftGeometry(1.08, barR * 1.02, 11, shaftSegs);

  [-1, 1].forEach((side) => {
    const collar = new THREE.Mesh(stopCollarGeo, collarSteelMat);
    collar.position.x = side * 0.67;
    group.add(collar);

    const bigPlate = new THREE.Mesh(bigPlateGeo, ironMat);
    bigPlate.position.x = side * 0.88;
    group.add(bigPlate);

    const smallPlate = new THREE.Mesh(smallPlateGeo, ironMat);
    smallPlate.position.x = side * 1.15;
    group.add(smallPlate);

    const starNut = new THREE.Mesh(starNutGeo, starNutMat);
    starNut.position.x = side * 1.37;
    starNut.rotation.x = side > 0 ? 0.22 : -0.18;
    group.add(starNut);

    const thread = new THREE.Mesh(threadGeo, threadSteelMat);
    thread.rotation.z = side > 0 ? -Math.PI / 2 : Math.PI / 2;
    thread.position.x = side * 0.74;
    group.add(thread);
  });

  return {
    group,
    dispose() {
      [handleGeo, stopCollarGeo, bigPlateGeo, smallPlateGeo, starNutGeo, threadGeo].forEach((g) => g.dispose());
      [ironMat, steelGripMat, collarSteelMat, threadSteelMat, starNutMat].forEach((m) => m.dispose());
      ironMaps.map.dispose();
      ironMaps.roughnessMap.dispose();
      ironMaps.bumpMap.dispose();
      steelMaps.map.dispose();
      steelMaps.bumpMap.dispose();
    },
  };
}

/* ============================================================================
   Main 3D Stage & Scroll-Driven Perspective Monolith Choreography
   ============================================================================ */
function initMotionStage(runwayEl) {
  const monolith = runwayEl.querySelector('[data-3d-monolith]');
  const canvas = runwayEl.querySelector('[data-3d-canvas]');
  const lineTop = runwayEl.querySelector('[data-3d-line="top"]');
  const lineBot = runwayEl.querySelector('[data-3d-line="bottom"]');
  const replayBtn = runwayEl.querySelector('[data-3d-replay]');
  if (!monolith || !canvas) return;

  const isMobileViewport = window.innerWidth < 768;
  const lowHardware =
    (typeof navigator.deviceMemory === 'number' && navigator.deviceMemory <= 4) ||
    (typeof navigator.hardwareConcurrency === 'number' && navigator.hardwareConcurrency <= 4);
  const isLowTier = isMobileViewport || lowHardware;

  const isReducedMotion = () =>
    document.documentElement.classList.contains('a11y-reduce-motion');

  if (replayBtn) {
    replayBtn.addEventListener('click', () => {
      const cinema = document.getElementById('apex-cinema-runway');
      if (cinema) {
        cinema.scrollIntoView({ behavior: isReducedMotion() ? 'auto' : 'smooth' });
      }
    });
  }

  // Optional zero-overhead perf stats (?perf=1)
  const perfEnabled = window.location.search.indexOf('perf=1') !== -1;
  const perfStats = {
    initialized: false,
    initMs: 0,
    isRunning: false,
    renderedFrames: 0,
    pixelRatio: 1,
    tier: isLowTier ? 'low-mobile' : 'desktop',
  };
  if (perfEnabled) {
    window.__APEX_PERF__ = window.__APEX_PERF__ || {};
    window.__APEX_PERF__.motion3d = perfStats;
  }

  // Cached layout geometry — ZERO forced layouts per frame!
  let cachedRunwayTop = 0;
  let cachedScrollable = 1;
  let cachedMonolithRect = { left: 0, top: 0, width: 1, height: 1 };
  let layoutDirty = true;

  let renderer = null;
  let scene = null;
  let camera = null;
  let rig = null;
  let dumbbell = null;
  let sculptureCleanup = null;
  let envRT = null;
  let webglInitialized = false;

  let targetMouseX = 0;
  let targetMouseY = 0;
  let mouseX = 0;
  let mouseY = 0;
  let targetScrollP = 0;
  let scrollP = 0;
  let isVisible = false;
  let rafId = null;
  let lastTime = performance.now();
  let idlePhase = 0;
  let resizeRafId = null;

  // Dirty-check strings to skip redundant DOM/clip-mask writes
  let lastMonoTransform = '';
  let lastMonoRadius = '';
  let lastTopTransform = '';
  let lastBotTransform = '';

  function measureLayout() {
    const rRect = runwayEl.getBoundingClientRect();
    cachedRunwayTop = rRect.top + window.scrollY;
    cachedScrollable = Math.max(1, runwayEl.offsetHeight - window.innerHeight);
    cachedMonolithRect = monolith.getBoundingClientRect();
    layoutDirty = false;
  }

  function computeProgress() {
    if (layoutDirty) measureLayout();
    return Math.max(0, Math.min(1, (window.scrollY - cachedRunwayTop) / cachedScrollable));
  }

  function resizeRenderer() {
    if (!renderer || !camera) return;
    const rect = monolith.getBoundingClientRect();
    cachedMonolithRect = rect;
    const w = Math.max(1, Math.round(rect.width));
    const h = Math.max(1, Math.round(rect.height));
    renderer.setSize(w, h, false);
    camera.aspect = w / h;
    camera.position.z = w < 520 ? 9.6 : w < 900 ? 7.8 : 6.2;
    camera.updateProjectionMatrix();
  }

  // Lazy-initialize Three.js & procedural PBR maps ONLY when approaching this section
  function initWebGLIfNeeded() {
    if (webglInitialized) return;
    webglInitialized = true;
    const t0 = performance.now();

    try {
      renderer = new THREE.WebGLRenderer({
        canvas,
        alpha: true,
        antialias: true,
        powerPreference: 'high-performance',
      });
    } catch (_) {
      return;
    }

    const pr = Math.min(window.devicePixelRatio || 1, isLowTier ? 1 : 1.5);
    renderer.setPixelRatio(pr);
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.24;
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    perfStats.pixelRatio = pr;

    scene = new THREE.Scene();
    envRT = buildStudioEnvironment(renderer);
    scene.environment = envRT.texture;

    camera = new THREE.PerspectiveCamera(34, 1, 0.1, 100);
    camera.position.set(0, 0, 6.4);

    const keyLight = new THREE.DirectionalLight(0xfffaf0, 3.6);
    keyLight.position.set(4.2, 5.2, 4.8);
    scene.add(keyLight);

    const rimLeft = new THREE.DirectionalLight(0xd6e6ff, 2.8);
    rimLeft.position.set(-5.0, 2.6, -2.2);
    scene.add(rimLeft);

    const goldBounce = new THREE.PointLight(0xf4e268, 12, 16);
    goldBounce.position.set(0.6, -3.2, 3.4);
    scene.add(goldBounce);

    const frontFill = new THREE.DirectionalLight(0xf0f2f8, 1.35);
    frontFill.position.set(-1.5, 0.8, 6.0);
    scene.add(frontFill);

    rig = new THREE.Group();
    scene.add(rig);

    const built = createDumbbellSculpture(isLowTier);
    dumbbell = built.group;
    sculptureCleanup = built.dispose;
    rig.add(dumbbell);

    resizeRenderer();
    perfStats.initialized = true;
    perfStats.initMs = Math.round(performance.now() - t0);
    if (perfEnabled) runwayEl.setAttribute('data-perf', JSON.stringify(perfStats));
  }

  function applyMonolithTransform(p, mx, my) {
    if (isReducedMotion()) {
      if (lastMonoTransform !== 'none') {
        monolith.style.transform = 'none';
        lastMonoTransform = 'none';
      }
      if (lastMonoRadius !== '24px') {
        monolith.style.borderRadius = '24px';
        lastMonoRadius = '24px';
      }
      return;
    }

    let rotX = 0;
    let translateY = 0;
    let scale = 1;
    let radius = 26;

    if (p < 0.36) {
      const t = 1 - p / 0.36;
      const ease = t * t * (3 - 2 * t);
      rotX = ease * 60;
      translateY = ease * 28;
      scale = 1 - ease * 0.16;
      radius = 22 + ease * 16;
    } else if (p > 0.70) {
      const t = (p - 0.70) / 0.30;
      const ease = t * t * (3 - 2 * t);
      rotX = -ease * 34;
      translateY = -ease * 12;
      scale = 1 - ease * 0.13;
      radius = 22 + ease * 18;
    }

    const tiltY = mx * 3.4;
    const tiltX = -my * 2.2;

    const monoTransform =
      'translate3d(0,' +
      translateY.toFixed(2) +
      'vh,0) rotateX(' +
      (rotX + tiltX).toFixed(2) +
      'deg) rotateY(' +
      tiltY.toFixed(2) +
      'deg) scale(' +
      scale.toFixed(4) +
      ')';

    if (monoTransform !== lastMonoTransform) {
      monolith.style.transform = monoTransform;
      lastMonoTransform = monoTransform;
    }

    // Quantize borderRadius to whole pixels to prevent 60 FPS clip-mask invalidation when subtle/idle
    const monoRadius = Math.round(radius) + 'px';
    if (monoRadius !== lastMonoRadius) {
      monolith.style.borderRadius = monoRadius;
      lastMonoRadius = monoRadius;
    }

    const drift = (p - 0.5) * 44;
    if (lineTop) {
      const topT = 'translate3d(' + (-drift + mx * -10).toFixed(1) + 'px,0,0)';
      if (topT !== lastTopTransform) {
        lineTop.style.transform = topT;
        lastTopTransform = topT;
      }
    }
    if (lineBot) {
      const botT = 'translate3d(' + (drift + mx * 10).toFixed(1) + 'px,0,0)';
      if (botT !== lastBotTransform) {
        lineBot.style.transform = botT;
        lastBotTransform = botT;
      }
    }
  }

  function startLoop() {
    if (rafId !== null || !isVisible || document.hidden) return;
    initWebGLIfNeeded();
    lastTime = performance.now();
    perfStats.isRunning = true;
    rafId = window.requestAnimationFrame(animate);
  }

  function stopLoop() {
    if (rafId !== null) {
      window.cancelAnimationFrame(rafId);
      rafId = null;
    }
    perfStats.isRunning = false;
    if (perfEnabled) runwayEl.setAttribute('data-perf', JSON.stringify(perfStats));
  }

  function animate(now) {
    rafId = null;
    if (!isVisible || document.hidden) {
      perfStats.isRunning = false;
      return;
    }

    const dt = Math.min(0.05, (now - lastTime) / 1000);
    lastTime = now;

    targetScrollP = computeProgress();
    scrollP += (targetScrollP - scrollP) * 0.14;
    mouseX += (targetMouseX - mouseX) * 0.08;
    mouseY += (targetMouseY - mouseY) * 0.08;

    applyMonolithTransform(scrollP, mouseX, mouseY);

    if (renderer && scene && camera && dumbbell && rig) {
      const reduced = isReducedMotion();
      if (!reduced) {
        idlePhase += dt * 0.45;
      }

      const entranceLift = scrollP < 0.36 ? (1 - scrollP / 0.36) * -1.35 : 0;
      const bob = reduced ? 0 : Math.sin(now * 0.0015) * 0.07;

      dumbbell.rotation.y =
        -0.58 + (scrollP - 0.5) * Math.PI * 1.9 + Math.sin(idlePhase) * 0.24 + mouseX * 0.52;
      dumbbell.rotation.x =
        0.22 + (scrollP - 0.5) * 0.65 + Math.cos(idlePhase * 0.7) * 0.08 + mouseY * 0.34;
      dumbbell.rotation.z = -0.24 + Math.sin(scrollP * Math.PI) * 0.14;

      rig.position.y = -0.1 + entranceLift + bob;
      rig.position.x = mouseX * 0.18;

      renderer.render(scene, camera);
      perfStats.renderedFrames++;
    }

    rafId = window.requestAnimationFrame(animate);
  }

  function onPointerEnter() {
    cachedMonolithRect = monolith.getBoundingClientRect();
  }

  function onPointerMove(e) {
    if (!isVisible || cachedMonolithRect.width < 1) return;
    targetMouseX = ((e.clientX - cachedMonolithRect.left) / cachedMonolithRect.width - 0.5) * 2;
    targetMouseY = ((e.clientY - cachedMonolithRect.top) / cachedMonolithRect.height - 0.5) * 2;
  }

  function onPointerLeave() {
    targetMouseX = 0;
    targetMouseY = 0;
  }

  function onResizeDebounced() {
    if (resizeRafId) return;
    resizeRafId = window.requestAnimationFrame(() => {
      resizeRafId = null;
      measureLayout();
      resizeRenderer();
    });
  }

  function onVisibilityChange() {
    if (document.hidden) {
      stopLoop();
    } else if (isVisible) {
      startLoop();
    }
  }

  monolith.addEventListener('pointerenter', onPointerEnter, { passive: true });
  monolith.addEventListener('pointermove', onPointerMove, { passive: true });
  monolith.addEventListener('pointerleave', onPointerLeave);
  window.addEventListener('resize', onResizeDebounced, { passive: true });
  window.addEventListener('load', onResizeDebounced, { passive: true });
  document.addEventListener('visibilitychange', onVisibilityChange);

  // Observer 1: Pre-warm WebGL 900px before viewport so it never blocks Hero load/scroll
  let prewarmIo = null;
  let activeIo = null;
  let ro = null;

  if ('IntersectionObserver' in window) {
    prewarmIo = new IntersectionObserver(
      (entries) => {
        if (entries[0] && entries[0].isIntersecting) {
          initWebGLIfNeeded();
          if (prewarmIo) {
            prewarmIo.disconnect();
            prewarmIo = null;
          }
        }
      },
      { rootMargin: '900px 0px' }
    );
    prewarmIo.observe(runwayEl);

    // Observer 2: Strictly start/stop 60 FPS WebGL loop when visible (0% off-screen work!)
    activeIo = new IntersectionObserver(
      (entries) => {
        const visible = entries[0] ? entries[0].isIntersecting : false;
        isVisible = visible;
        if (visible) {
          measureLayout();
          targetScrollP = computeProgress();
          scrollP = targetScrollP;
          startLoop();
        } else {
          stopLoop();
        }
      },
      { rootMargin: '220px 0px' }
    );
    activeIo.observe(runwayEl);
  } else {
    isVisible = true;
    startLoop();
  }

  if ('ResizeObserver' in window) {
    ro = new ResizeObserver(onResizeDebounced);
    ro.observe(runwayEl);
  }

  function cleanup() {
    stopLoop();
    if (resizeRafId) window.cancelAnimationFrame(resizeRafId);
    if (prewarmIo) prewarmIo.disconnect();
    if (activeIo) activeIo.disconnect();
    if (ro) ro.disconnect();
    monolith.removeEventListener('pointerenter', onPointerEnter);
    monolith.removeEventListener('pointermove', onPointerMove);
    monolith.removeEventListener('pointerleave', onPointerLeave);
    window.removeEventListener('resize', onResizeDebounced);
    window.removeEventListener('load', onResizeDebounced);
    document.removeEventListener('visibilitychange', onVisibilityChange);
    if (sculptureCleanup) sculptureCleanup();
    if (envRT) envRT.dispose();
    if (renderer) renderer.dispose();
  }

  window.addEventListener('pagehide', cleanup);

  // Set initial resting perspective pose without spinning up WebGL yet
  measureLayout();
  targetScrollP = computeProgress();
  scrollP = targetScrollP;
  applyMonolithTransform(scrollP, 0, 0);
}
