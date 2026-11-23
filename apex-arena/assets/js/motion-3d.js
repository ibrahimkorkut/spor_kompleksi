/**
 * APEX ARENA — 3D Perspective Monolith & Kintsugi Marble-Gold Basketball WebGL Engine (v1.3.0)
 * 100% offline Three.js procedural PBR sculpture + scroll-driven 3D rotateX choreography.
 *
 * @package ApexArena
 */
import * as THREE from './vendor/three.module.js';

const runway = document.getElementById('apex-3d-runway');
if (runway) {
  initMotionStage(runway);
}

/* ============================================================================
   Deterministic 3D Value Noise & Domain-Warped fBm (Zero External Textures)
   ============================================================================ */
function hash3(x, y, z) {
  let n = Math.sin(x * 127.1 + y * 311.7 + z * 74.7) * 43758.5453123;
  return n - Math.floor(n);
}

function noise3(x, y, z) {
  const ix = Math.floor(x);
  const iy = Math.floor(y);
  const iz = Math.floor(z);
  const fx = x - ix;
  const fy = y - iy;
  const fz = z - iz;
  const ux = fx * fx * (3 - 2 * fx);
  const uy = fy * fy * (3 - 2 * fy);
  const uz = fz * fz * (3 - 2 * fz);

  const n000 = hash3(ix, iy, iz);
  const n100 = hash3(ix + 1, iy, iz);
  const n010 = hash3(ix, iy + 1, iz);
  const n110 = hash3(ix + 1, iy + 1, iz);
  const n001 = hash3(ix, iy, iz + 1);
  const n101 = hash3(ix + 1, iy, iz + 1);
  const n011 = hash3(ix, iy + 1, iz + 1);
  const n111 = hash3(ix + 1, iy + 1, iz + 1);

  const nx00 = n000 + (n100 - n000) * ux;
  const nx10 = n010 + (n110 - n010) * ux;
  const nx01 = n001 + (n101 - n001) * ux;
  const nx11 = n011 + (n111 - n011) * ux;

  const nxy0 = nx00 + (nx10 - nx00) * uy;
  const nxy1 = nx01 + (nx11 - nx01) * uy;
  return nxy0 + (nxy1 - nxy0) * uz;
}

function fbm3(x, y, z, octaves = 4) {
  let value = 0;
  let amp = 0.5;
  let freq = 1;
  for (let i = 0; i < octaves; i++) {
    value += amp * noise3(x * freq, y * freq, z * freq);
    freq *= 2.04;
    amp *= 0.5;
  }
  return value;
}

/* ============================================================================
   Procedural 1024x512 PBR Maps: Sculpted White Marble + 24K Gold Kintsugi Seams
   ============================================================================ */
function createKintsugiBasketballMaps() {
  const W = 1024;
  const H = 512;

  const albedoC = document.createElement('canvas');
  const metalC = document.createElement('canvas');
  const roughC = document.createElement('canvas');
  const bumpC = document.createElement('canvas');
  const emissiveC = document.createElement('canvas');

  [albedoC, metalC, roughC, bumpC, emissiveC].forEach((c) => {
    c.width = W;
    c.height = H;
  });

  const aCtx = albedoC.getContext('2d');
  const mCtx = metalC.getContext('2d');
  const rCtx = roughC.getContext('2d');
  const bCtx = bumpC.getContext('2d');
  const eCtx = emissiveC.getContext('2d');

  const aImg = aCtx.createImageData(W, H);
  const mImg = mCtx.createImageData(W, H);
  const rImg = rCtx.createImageData(W, H);
  const bImg = bCtx.createImageData(W, H);
  const eImg = eCtx.createImageData(W, H);

  const aD = aImg.data;
  const mD = mImg.data;
  const rD = rImg.data;
  const bD = bImg.data;
  const eD = eImg.data;

  for (let py = 0; py < H; py++) {
    const v = (py + 0.5) / H;
    const phi = v * Math.PI;
    const sinPhi = Math.sin(phi);
    const ny = Math.cos(phi);

    for (let px = 0; px < W; px++) {
      const u = (px + 0.5) / W;
      const theta = u * Math.PI * 2;
      const nx = sinPhi * Math.cos(theta);
      const nz = sinPhi * Math.sin(theta);

      // 1. Sculpted alabaster white marble base with organic smoky veining
      const warp = fbm3(nx * 2.4 + 3.1, ny * 2.4 - 1.7, nz * 2.4 + 0.9, 3);
      const veinWave = Math.sin((nx * 3.8 + ny * 2.4 + warp * 4.2) * Math.PI);
      const marbleStreak = Math.pow(Math.abs(veinWave), 0.35);
      const microGrain = noise3(nx * 38, ny * 38, nz * 38);

      // Matte marble RGB (warm alabaster white -> subtle architectural stone grey)
      const baseLum = 208 + marbleStreak * 36 + (microGrain - 0.5) * 10;
      let ar = Math.min(250, Math.max(155, baseLum + 3));
      let ag = Math.min(248, Math.max(152, baseLum + 1));
      let ab = Math.min(244, Math.max(146, baseLum - 4));

      let metal = 10; // low metalness for ceramic/marble
      let rough = 166 + (1 - marbleStreak) * 34 + (microGrain - 0.5) * 18;
      let bump = 165 + (microGrain - 0.5) * 38;
      let er = 0;
      let eg = 0;
      let eb = 0;

      // 2. Authentic 3D Championship Basketball Seam Channels (zero pole distortion)
      const dEq = Math.abs(ny);
      const dMer = Math.abs(nz);
      const sideSaddle = Math.abs(Math.abs(nx) + 0.22 * (ny * ny - nz * nz) - 0.62);
      const seamDist = Math.min(dEq, dMer, sideSaddle);

      // 3. Organic Jagged Kintsugi Fracture Veins (3D domain-warped ridge cracks)
      const w1 = fbm3(nx * 3.1 + 7.2, ny * 3.1 + 1.4, nz * 3.1 - 4.3, 3);
      const crackA = Math.abs(fbm3(nx * 2.2 + w1 * 1.35, ny * 2.2 - w1 * 1.15, nz * 2.2 + w1 * 0.9, 4) - 0.5);
      const crackB = Math.abs(fbm3(nx * 2.6 - w1 * 1.1 + 5.0, ny * 2.6 + w1 * 1.3 - 2.0, nz * 2.6 - w1 * 0.8, 3) - 0.52);
      const kintsugiDist = Math.min(crackA, crackB * 1.18);

      // Combine recessed obsidian crevice + flanking 24K molten gold repair lacquer
      const isSeamCore = seamDist < 0.0115;
      const isSeamGold = seamDist >= 0.0115 && seamDist < 0.031;
      const isCrackCore = kintsugiDist < 0.0085;
      const isCrackGold = kintsugiDist >= 0.0085 && kintsugiDist < 0.025;

      if (isSeamCore || isCrackCore) {
        // Deep charcoal-obsidian fissure core (gives dramatic Kintsugi broken-marble depth)
        ar = 20;
        ag = 17;
        ab = 12;
        metal = 45;
        rough = 195;
        bump = 24;
        er = 18;
        eg = 12;
        eb = 2;
      } else if (isSeamGold || isCrackGold) {
        // Gleaming 24K molten gold Kintsugi vein
        const shimmer = 0.88 + microGrain * 0.24;
        ar = Math.min(255, 246 * shimmer);
        ag = Math.min(255, 206 * shimmer);
        ab = Math.min(255, 96 * shimmer);
        metal = 250; // pure metallic gold
        rough = 34 + microGrain * 22; // mirror-gloss lacquer
        bump = 232; // slightly raised molten gold ridge
        er = 148;
        eg = 104;
        eb = 20;
      } else if (seamDist < 0.044 || kintsugiDist < 0.036) {
        // Subtle darkened bevel lip right next to the fracture
        ar *= 0.88;
        ag *= 0.88;
        ab *= 0.87;
        bump = 135;
      }

      const idx = (py * W + px) * 4;
      aD[idx] = ar;
      aD[idx + 1] = ag;
      aD[idx + 2] = ab;
      aD[idx + 3] = 255;

      mD[idx] = mD[idx + 1] = mD[idx + 2] = metal;
      mD[idx + 3] = 255;

      rD[idx] = rD[idx + 1] = rD[idx + 2] = Math.min(255, Math.max(0, rough));
      rD[idx + 3] = 255;

      bD[idx] = bD[idx + 1] = bD[idx + 2] = Math.min(255, Math.max(0, bump));
      bD[idx + 3] = 255;

      eD[idx] = er;
      eD[idx + 1] = eg;
      eD[idx + 2] = eb;
      eD[idx + 3] = 255;
    }
  }

  aCtx.putImageData(aImg, 0, 0);
  mCtx.putImageData(mImg, 0, 0);
  rCtx.putImageData(rImg, 0, 0);
  bCtx.putImageData(bImg, 0, 0);
  eCtx.putImageData(eImg, 0, 0);

  const map = new THREE.CanvasTexture(albedoC);
  map.colorSpace = THREE.SRGBColorSpace;
  map.anisotropy = 8;

  const metalnessMap = new THREE.CanvasTexture(metalC);
  const roughnessMap = new THREE.CanvasTexture(roughC);
  const bumpMap = new THREE.CanvasTexture(bumpC);
  const emissiveMap = new THREE.CanvasTexture(emissiveC);
  emissiveMap.colorSpace = THREE.SRGBColorSpace;

  return { map, metalnessMap, roughnessMap, bumpMap, emissiveMap };
}

/* ============================================================================
   Procedural Studio Softbox Environment (Makes 24K Gold Reflections Shimmer!)
   ============================================================================ */
function buildStudioEnvironment(renderer) {
  const pmrem = new THREE.PMREMGenerator(renderer);
  const envScene = new THREE.Scene();
  envScene.background = new THREE.Color(0x0c0c10);

  const addPanel = (color, intensity, pos, scale, rot) => {
    const mat = new THREE.MeshBasicMaterial({
      color: new THREE.Color(color).multiplyScalar(intensity),
      side: THREE.DoubleSide,
    });
    const mesh = new THREE.Mesh(new THREE.PlaneGeometry(1, 1), mat);
    mesh.position.set(...pos);
    mesh.scale.set(...scale);
    mesh.rotation.set(...rot);
    envScene.add(mesh);
  };

  // Overhead studio softbox
  addPanel(0xffffff, 4.2, [0, 7, 2], [10, 6, 1], [Math.PI / 2, 0, 0]);
  // Warm golden key reflector (right)
  addPanel(0xffe09c, 4.5, [7, 2, 3], [5, 8, 1], [0, -Math.PI / 2.4, 0]);
  // Cool sculptural rim strip (left-back)
  addPanel(0xb9ccff, 2.4, [-7, 1, -2], [4, 9, 1], [0, Math.PI / 2.2, 0]);
  // Subtle front-top fill card
  addPanel(0xf5f5f7, 2.0, [2, 3, 8], [6, 4, 1], [0, Math.PI, 0]);

  const envRT = pmrem.fromScene(envScene, 0.04);
  pmrem.dispose();
  return envRT.texture;
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

  // Initialize Three.js WebGLRenderer with alpha transparency (sits OVER typography!)
  let renderer;
  try {
    renderer = new THREE.WebGLRenderer({
      canvas,
      alpha: true,
      antialias: true,
      powerPreference: 'high-performance',
    });
  } catch (err) {
    return;
  }

  renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
  renderer.toneMapping = THREE.ACESFilmicToneMapping;
  renderer.toneMappingExposure = 1.18;
  renderer.outputColorSpace = THREE.SRGBColorSpace;

  const scene = new THREE.Scene();
  scene.environment = buildStudioEnvironment(renderer);

  const camera = new THREE.PerspectiveCamera(34, 1, 0.1, 100);
  camera.position.set(0, 0, 6.2);

  // Dramatic chiaroscuro studio lighting (inspired by Kintsugi sculptural look)
  const keyLight = new THREE.DirectionalLight(0xfff9ee, 3.0);
  keyLight.position.set(3.8, 4.6, 4.5);
  scene.add(keyLight);

  const goldRim = new THREE.PointLight(0xffcf5c, 16, 16);
  goldRim.position.set(-3.4, 1.8, 2.6);
  scene.add(goldRim);

  const coolEdge = new THREE.DirectionalLight(0xa3bcff, 1.15);
  coolEdge.position.set(-2.5, -3.5, -2.5);
  scene.add(coolEdge);

  const ambient = new THREE.AmbientLight(0xd8dae6, 0.32);
  scene.add(ambient);

  // Master sculptural rig
  const rig = new THREE.Group();
  scene.add(rig);

  // 1. Kintsugi White Marble & 24K Gold-Veined 3D Basketball (balanced sculptural scale so headline stays readable)
  const pbr = createKintsugiBasketballMaps();
  const ballGeo = new THREE.SphereGeometry(1.12, 96, 64);
  const ballMat = new THREE.MeshStandardMaterial({
    map: pbr.map,
    metalnessMap: pbr.metalnessMap,
    roughnessMap: pbr.roughnessMap,
    bumpMap: pbr.bumpMap,
    bumpScale: 0.048,
    emissive: new THREE.Color(0xd99b26),
    emissiveMap: pbr.emissiveMap,
    emissiveIntensity: 0.42,
    metalness: 1.0,
    roughness: 1.0,
    envMapIntensity: 1.35,
  });
  const ballMesh = new THREE.Mesh(ballGeo, ballMat);
  rig.add(ballMesh);

  // 2. Sculptural hairline 24K gold orbital ring framing the basketball
  const ringGeo = new THREE.TorusGeometry(1.52, 0.0075, 16, 140);
  const goldMat = new THREE.MeshStandardMaterial({
    color: 0xf7d36b,
    metalness: 0.98,
    roughness: 0.16,
    emissive: 0x5c3e08,
    emissiveIntensity: 0.35,
    envMapIntensity: 1.6,
  });
  const ringMesh = new THREE.Mesh(ringGeo, goldMat);
  ringMesh.rotation.set(Math.PI * 0.38, -0.35, 0.25);
  rig.add(ringMesh);

  // 3. Floating beveled Kintsugi ceramic & gold fracture shards
  const shardMatMarble = new THREE.MeshStandardMaterial({
    color: 0xe8e5de,
    roughness: 0.58,
    metalness: 0.08,
    envMapIntensity: 1.1,
  });
  const shards = [];
  const shardCount = 14;
  for (let i = 0; i < shardCount; i++) {
    const isGold = i % 3 === 0;
    const geo = new THREE.OctahedronGeometry(isGold ? 0.065 : 0.095, 0);
    geo.scale(1.5, 0.42, 0.95);
    const mesh = new THREE.Mesh(geo, isGold ? goldMat : shardMatMarble);

    const a = (i / shardCount) * Math.PI * 2 + 0.3;
    const elev = ((i % 5) - 2) * 0.34;
    const baseRadius = 1.38 + (i % 3) * 0.18;

    mesh.position.set(
      Math.cos(a) * baseRadius,
      elev,
      Math.sin(a) * baseRadius * 0.85
    );
    mesh.rotation.set(i * 0.8, i * 1.1, i * 0.5);
    rig.add(mesh);
    shards.push({
      mesh,
      angle: a,
      elev,
      baseRadius,
      speed: 0.25 + (i % 4) * 0.1,
    });
  }

  // Responsive canvas sizing
  function resize() {
    const rect = monolith.getBoundingClientRect();
    const w = Math.max(1, Math.round(rect.width));
    const h = Math.max(1, Math.round(rect.height));
    renderer.setSize(w, h, false);
    camera.aspect = w / h;
    // Pull camera cleanly on tablet & portrait mobile so headline stays crystal-clear behind the Kintsugi ball
    camera.position.z = w < 520 ? 9.8 : w < 900 ? 8.2 : 6.7;
    camera.updateProjectionMatrix();
  }

  resize();
  window.addEventListener('resize', resize);

  // Interactive pointer tracking + Scroll state
  let targetMouseX = 0;
  let targetMouseY = 0;
  let mouseX = 0;
  let mouseY = 0;
  let targetScrollP = 0;
  let scrollP = 0;
  let isVisible = true;
  let lastTime = performance.now();
  let idleSpin = 0;

  monolith.addEventListener(
    'pointermove',
    (e) => {
      const rect = monolith.getBoundingClientRect();
      targetMouseX = ((e.clientX - rect.left) / rect.width - 0.5) * 2;
      targetMouseY = ((e.clientY - rect.top) / rect.height - 0.5) * 2;
    },
    { passive: true }
  );

  monolith.addEventListener('pointerleave', () => {
    targetMouseX = 0;
    targetMouseY = 0;
  });

  function computeProgress() {
    const rect = runwayEl.getBoundingClientRect();
    const scrollable = Math.max(1, runwayEl.offsetHeight - window.innerHeight);
    return Math.max(0, Math.min(1, -rect.top / scrollable));
  }

  if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver(
      (entries) => {
        isVisible = entries[0] ? entries[0].isIntersecting : true;
      },
      { rootMargin: '250px 0px' }
    );
    io.observe(runwayEl);
  }

  function applyMonolithTransform(p, mx, my) {
    const reduced = isReducedMotion();
    if (reduced) {
      monolith.style.transform = 'none';
      monolith.style.borderRadius = '24px';
      return;
    }

    let rotX = 0;
    let translateY = 0;
    let scale = 1;
    let radius = 26;

    if (p < 0.36) {
      // Exact Webflow GIF entrance: dark slab starts tilted flat in deep 3D perspective & rises upright!
      const t = 1 - p / 0.36; // 1 -> 0
      const ease = t * t * (3 - 2 * t);
      rotX = ease * 60;
      translateY = ease * 28;
      scale = 1 - ease * 0.16;
      radius = 22 + ease * 16;
    } else if (p > 0.70) {
      // Smooth 3D perspective fold-away exit
      const t = (p - 0.70) / 0.30; // 0 -> 1
      const ease = t * t * (3 - 2 * t);
      rotX = -ease * 34;
      translateY = -ease * 12;
      scale = 1 - ease * 0.13;
      radius = 22 + ease * 18;
    }

    const tiltY = mx * 3.4;
    const tiltX = -my * 2.2;

    monolith.style.transform =
      'translate3d(0,' +
      translateY.toFixed(2) +
      'vh,0) rotateX(' +
      (rotX + tiltX).toFixed(2) +
      'deg) rotateY(' +
      tiltY.toFixed(2) +
      'deg) scale(' +
      scale.toFixed(4) +
      ')';
    monolith.style.borderRadius = radius.toFixed(1) + 'px';

    // Kinetic opposing horizontal drift on massive gold typography
    const drift = (p - 0.5) * 44;
    if (lineTop) {
      lineTop.style.transform = 'translate3d(' + (-drift + mx * -10).toFixed(1) + 'px,0,0)';
    }
    if (lineBot) {
      lineBot.style.transform = 'translate3d(' + (drift + mx * 10).toFixed(1) + 'px,0,0)';
    }
  }

  function animate(now) {
    window.requestAnimationFrame(animate);
    const dt = Math.min(0.05, (now - lastTime) / 1000);
    lastTime = now;

    targetScrollP = computeProgress();
    scrollP += (targetScrollP - scrollP) * 0.12;
    mouseX += (targetMouseX - mouseX) * 0.08;
    mouseY += (targetMouseY - mouseY) * 0.08;

    applyMonolithTransform(scrollP, mouseX, mouseY);

    if (!isVisible) return;

    const reduced = isReducedMotion();
    if (!reduced) {
      idleSpin += dt * 0.24;
    }

    // Basketball sculptural motion synced 1:1 with scroll + subtle float + pointer look
    const entranceLift = scrollP < 0.36 ? (1 - scrollP / 0.36) * -1.35 : 0;
    const bob = reduced ? 0 : Math.sin(now * 0.0014) * 0.065;

    ballMesh.rotation.y = 0.55 + scrollP * Math.PI * 2.1 + idleSpin + mouseX * 0.42;
    ballMesh.rotation.x = 0.22 + scrollP * 0.85 + mouseY * 0.28;
    ballMesh.rotation.z = -0.14 + Math.sin(scrollP * Math.PI) * 0.16;

    rig.position.y = -0.22 + entranceLift + bob;
    rig.position.x = mouseX * 0.16;

    // Orbital hairline ring counter-rotation
    ringMesh.rotation.z = 0.25 - scrollP * Math.PI * 1.4 - idleSpin * 0.5;
    ringMesh.rotation.x = Math.PI * 0.38 + mouseY * 0.25;

    // Shards expand delicately at entrance/exit and gather cleanly at hero peak
    const spread = Math.abs(scrollP - 0.54) * 0.65;
    for (let i = 0; i < shards.length; i++) {
      const s = shards[i];
      const orbitA = s.angle + scrollP * 1.6 + idleSpin * s.speed;
      const r = s.baseRadius + spread;
      s.mesh.position.set(
        Math.cos(orbitA) * r,
        s.elev + Math.sin(now * 0.0012 + i) * 0.07,
        Math.sin(orbitA) * r * 0.82
      );
      s.mesh.rotation.x += dt * 0.4 * s.speed;
      s.mesh.rotation.y += dt * 0.55 * s.speed;
    }

    renderer.render(scene, camera);
  }

  targetScrollP = computeProgress();
  scrollP = targetScrollP;
  applyMonolithTransform(scrollP, 0, 0);
  window.requestAnimationFrame(animate);
}
