/* =====================================================================
   BHAVI CREATIONS — WebGL 3D background (Three.js)
   A liquid-gold "brand core", orbit rings, floating gold shapes and a
   particle field. The camera flies forward as you scroll, and the core
   glides to a new pose for each [data-scene] section.
   ===================================================================== */
import * as THREE from 'three';
import { RoomEnvironment } from 'three/addons/environments/RoomEnvironment.js';
import { EffectComposer } from 'three/addons/postprocessing/EffectComposer.js';
import { RenderPass } from 'three/addons/postprocessing/RenderPass.js';
import { UnrealBloomPass } from 'three/addons/postprocessing/UnrealBloomPass.js';
import { OutputPass } from 'three/addons/postprocessing/OutputPass.js';

const canvas = document.getElementById('webgl');
const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
const isMobile = () => innerWidth < 768;
const lowPower = isMobile() || (navigator.hardwareConcurrency || 8) <= 4;

let renderer;
try {
  renderer = new THREE.WebGLRenderer({ canvas, antialias: !lowPower, alpha: false, powerPreference: 'high-performance' });
} catch (err) {
  console.warn('WebGL unavailable, using CSS background.', err);
}

if (renderer) init();

function init() {
  renderer.setPixelRatio(Math.min(devicePixelRatio, lowPower ? 1.5 : 1.8));
  renderer.setSize(innerWidth, innerHeight);
  renderer.setClearColor(0x05070a, 1);
  renderer.toneMapping = THREE.ACESFilmicToneMapping;
  renderer.toneMappingExposure = 1.05;

  const scene = new THREE.Scene();
  scene.fog = new THREE.FogExp2(0x05070a, 0.035);
  const camera = new THREE.PerspectiveCamera(45, innerWidth / innerHeight, 0.1, 200);
  camera.position.set(0, 0, 8);

  const pmrem = new THREE.PMREMGenerator(renderer);
  scene.environment = pmrem.fromScene(new RoomEnvironment(renderer), 0.04).texture;

  scene.add(new THREE.AmbientLight(0xffffff, 0.25));
  const key = new THREE.DirectionalLight(0xffe2a8, 2.2); key.position.set(4, 5, 6); scene.add(key);
  const rim = new THREE.PointLight(0x1fd39a, 30, 30); rim.position.set(-5, -2, 2); scene.add(rim);

  /* ---------------- brand core (noise-displaced liquid gold) ---------------- */
  const NOISE = /* glsl */`
    vec3 mod289(vec3 x){return x-floor(x*(1./289.))*289.;}
    vec4 mod289(vec4 x){return x-floor(x*(1./289.))*289.;}
    vec4 permute(vec4 x){return mod289(((x*34.)+1.)*x);}
    vec4 taylorInvSqrt(vec4 r){return 1.79284291400159-.85373472095314*r;}
    float snoise(vec3 v){
      const vec2 C=vec2(1./6.,1./3.);const vec4 D=vec4(0.,.5,1.,2.);
      vec3 i=floor(v+dot(v,C.yyy));vec3 x0=v-i+dot(i,C.xxx);
      vec3 g=step(x0.yzx,x0.xyz);vec3 l=1.-g;vec3 i1=min(g.xyz,l.zxy);vec3 i2=max(g.xyz,l.zxy);
      vec3 x1=x0-i1+C.xxx;vec3 x2=x0-i2+C.yyy;vec3 x3=x0-D.yyy;
      i=mod289(i);
      vec4 p=permute(permute(permute(i.z+vec4(0.,i1.z,i2.z,1.))+i.y+vec4(0.,i1.y,i2.y,1.))+i.x+vec4(0.,i1.x,i2.x,1.));
      float n_=.142857142857;vec3 ns=n_*D.wyz-D.xzx;
      vec4 j=p-49.*floor(p*ns.z*ns.z);vec4 x_=floor(j*ns.z);vec4 y_=floor(j-7.*x_);
      vec4 x=x_*ns.x+ns.yyyy;vec4 y=y_*ns.x+ns.yyyy;vec4 h=1.-abs(x)-abs(y);
      vec4 b0=vec4(x.xy,y.xy);vec4 b1=vec4(x.zw,y.zw);
      vec4 s0=floor(b0)*2.+1.;vec4 s1=floor(b1)*2.+1.;vec4 sh=-step(h,vec4(0.));
      vec4 a0=b0.xzyw+s0.xzyw*sh.xxyy;vec4 a1=b1.xzyw+s1.xzyw*sh.zzww;
      vec3 p0=vec3(a0.xy,h.x);vec3 p1=vec3(a0.zw,h.y);vec3 p2=vec3(a1.xy,h.z);vec3 p3=vec3(a1.zw,h.w);
      vec4 norm=taylorInvSqrt(vec4(dot(p0,p0),dot(p1,p1),dot(p2,p2),dot(p3,p3)));
      p0*=norm.x;p1*=norm.y;p2*=norm.z;p3*=norm.w;
      vec4 m=max(.6-vec4(dot(x0,x0),dot(x1,x1),dot(x2,x2),dot(x3,x3)),0.);m=m*m;
      return 42.*dot(m*m,vec4(dot(p0,x0),dot(p1,x1),dot(p2,x2),dot(p3,x3)));
    }`;

  const coreUniforms = {
    uTime: { value: 0 },
    uDistort: { value: 0.35 },
    uFreq: { value: 1.4 },
    uEmerald: { value: 0 },
    uMouse: { value: new THREE.Vector2() },
  };
  const coreMat = new THREE.MeshPhysicalMaterial({
    color: 0xd9ab4f, metalness: 1, roughness: 0.2, clearcoat: 1, clearcoatRoughness: 0.15,
    iridescence: 0.22, iridescenceIOR: 1.4, envMapIntensity: 1.25,
  });
  coreMat.onBeforeCompile = (shader) => {
    Object.assign(shader.uniforms, coreUniforms);
    shader.vertexShader = shader.vertexShader
      .replace('#include <common>', `#include <common>
        uniform float uTime; uniform float uDistort; uniform float uFreq; uniform vec2 uMouse;
        varying float vNoise;
        ${NOISE}
        float disp(vec3 p){
          float n = snoise(p * uFreq + vec3(uTime*.18, uTime*.12, uTime*.1));
          n += .18 * snoise(p * uFreq * 2.1 - vec3(uTime*.1));
          return n;
        }`)
      .replace('#include <beginnormal_vertex>', `
        vec3 pp = position;
        float d0 = disp(pp);
        vec3 tA = normalize(cross(normal, vec3(0.,1.,0.) + vec3(.0001)));
        vec3 tB = normalize(cross(normal, tA));
        float eps = .02;
        vec3 pA = pp + tA*eps; vec3 pB = pp + tB*eps;
        vec3 q0 = pp + normal * d0 * uDistort;
        vec3 qA = pA + normalize(pA) * disp(pA) * uDistort;
        vec3 qB = pB + normalize(pB) * disp(pB) * uDistort;
        vec3 objectNormal = normalize(cross(qA - q0, qB - q0));
        if (dot(objectNormal, normal) < 0.) objectNormal = -objectNormal;
        vNoise = d0;
        #ifdef USE_TANGENT
          vec3 objectTangent = vec3( tangent.xyz );
        #endif`)
      .replace('#include <begin_vertex>', `vec3 transformed = q0;`);
    shader.fragmentShader = shader.fragmentShader
      .replace('#include <common>', `#include <common>
        uniform float uEmerald; uniform float uTime; varying float vNoise;`)
      .replace('#include <color_fragment>', `#include <color_fragment>
        vec3 gold = mix(vec3(.55,.33,.06), vec3(1.,.82,.45), smoothstep(-.8,.9,vNoise));
        vec3 emer = mix(vec3(.02,.25,.17), vec3(.25,.9,.65), smoothstep(-.8,.9,vNoise));
        diffuseColor.rgb = mix(gold, emer, uEmerald * smoothstep(-.2, 1., vNoise));`);
  };
  const coreGeo = new THREE.IcosahedronGeometry(1.35, lowPower ? 48 : 96);
  const core = new THREE.Mesh(coreGeo, coreMat);

  const shell = new THREE.Mesh(
    new THREE.IcosahedronGeometry(2.05, 1),
    new THREE.MeshBasicMaterial({ color: 0xf6d98b, wireframe: true, transparent: true, opacity: 0.12 })
  );

  const rings = new THREE.Group();
  const ringMat = new THREE.MeshBasicMaterial({ color: 0xf6d98b, transparent: true, opacity: 0.55 });
  [[2.5, 0.006, [1.2, 0.3, 0]], [2.9, 0.004, [0.4, 1.1, 0.3]], [3.35, 0.008, [1.7, -0.5, 0.6]]].forEach(([r, t, rot]) => {
    const m = new THREE.Mesh(new THREE.TorusGeometry(r, t, 8, 180), ringMat.clone());
    m.rotation.set(...rot);
    m.material.opacity = 0.25 + Math.random() * 0.3;
    // orbiting bead
    const bead = new THREE.Mesh(new THREE.SphereGeometry(0.05, 16, 16), new THREE.MeshBasicMaterial({ color: 0xfff1c4 }));
    bead.position.x = r;
    m.add(bead);
    rings.add(m);
  });

  const coreGroup = new THREE.Group();
  coreGroup.add(core, shell, rings);
  scene.add(coreGroup);

  /* ---------------- floating gold shapes ---------------- */
  const goldMetal = new THREE.MeshStandardMaterial({ color: 0xd9ab4f, metalness: 1, roughness: 0.22, envMapIntensity: 1.2 });
  const emeraldGlass = new THREE.MeshPhysicalMaterial({ color: 0x0f7a58, metalness: 0.2, roughness: 0.1, transmission: 0.6, thickness: 0.6, envMapIntensity: 1.5, transparent: true });
  const shapes = [];
  const geos = [
    new THREE.OctahedronGeometry(0.35), new THREE.TorusGeometry(0.28, 0.1, 16, 48),
    new THREE.BoxGeometry(0.42, 0.42, 0.42), new THREE.IcosahedronGeometry(0.3, 0),
    new THREE.TorusKnotGeometry(0.22, 0.07, 80, 12), new THREE.ConeGeometry(0.25, 0.5, 4),
  ];
  const SHAPE_COUNT = lowPower ? 16 : 30;
  for (let i = 0; i < SHAPE_COUNT; i++) {
    const m = new THREE.Mesh(geos[i % geos.length], i % 4 === 0 ? emeraldGlass : goldMetal);
    const side = i % 2 ? 1 : -1;
    m.position.set(side * (2.8 + Math.random() * 4.5), (Math.random() - 0.5) * 7, 2 - i * (lowPower ? 4.2 : 2.3));
    m.scale.setScalar(0.6 + Math.random() * 0.9);
    m.userData = { rs: new THREE.Vector3(Math.random(), Math.random(), Math.random()).multiplyScalar(0.6), fy: Math.random() * Math.PI * 2, base: m.position.y };
    shapes.push(m);
    scene.add(m);
  }

  /* ---------------- particle field ---------------- */
  const COUNT = lowPower ? 2200 : 5200, DEPTH = 80;
  const pos = new Float32Array(COUNT * 3), seed = new Float32Array(COUNT), col = new Float32Array(COUNT * 3);
  const cGold = new THREE.Color(0xf6d98b), cWhite = new THREE.Color(0xffffff), cEm = new THREE.Color(0x3ee0a8);
  for (let i = 0; i < COUNT; i++) {
    const r = 1.5 + Math.pow(Math.random(), 0.6) * 14, a = Math.random() * Math.PI * 2;
    pos[i * 3] = Math.cos(a) * r;
    pos[i * 3 + 1] = Math.sin(a) * r * 0.7;
    pos[i * 3 + 2] = 8 - Math.random() * DEPTH;
    seed[i] = Math.random();
    const c = Math.random() < 0.65 ? cGold : Math.random() < 0.8 ? cWhite : cEm;
    col[i * 3] = c.r; col[i * 3 + 1] = c.g; col[i * 3 + 2] = c.b;
  }
  const pGeo = new THREE.BufferGeometry();
  pGeo.setAttribute('position', new THREE.BufferAttribute(pos, 3));
  pGeo.setAttribute('aSeed', new THREE.BufferAttribute(seed, 1));
  pGeo.setAttribute('aColor', new THREE.BufferAttribute(col, 3));
  const pMat = new THREE.ShaderMaterial({
    transparent: true, depthWrite: false, blending: THREE.AdditiveBlending,
    uniforms: { uTime: { value: 0 }, uPR: { value: renderer.getPixelRatio() }, uSpeed: { value: 0 } },
    vertexShader: /* glsl */`
      attribute float aSeed; attribute vec3 aColor; uniform float uTime; uniform float uPR; uniform float uSpeed;
      varying vec3 vColor; varying float vAlpha;
      void main(){
        vec3 p = position;
        p.x += sin(uTime*.3 + aSeed*20.)*.25;
        p.y += cos(uTime*.25 + aSeed*15.)*.25;
        vec4 mv = modelViewMatrix * vec4(p,1.);
        gl_Position = projectionMatrix * mv;
        float s = (2. + aSeed*4.) * (1. + uSpeed*2.);
        gl_PointSize = s * uPR * (10. / -mv.z);
        vColor = aColor;
        vAlpha = (.35 + .65*sin(uTime*1.5 + aSeed*40.)*.5+.5) * smoothstep(60., 6., -mv.z) * smoothstep(.2, 2., -mv.z);
      }`,
    fragmentShader: /* glsl */`
      varying vec3 vColor; varying float vAlpha;
      void main(){
        float d = length(gl_PointCoord - .5);
        float a = smoothstep(.5, 0., d);
        gl_FragColor = vec4(vColor, a*a*vAlpha);
      }`,
  });
  const points = new THREE.Points(pGeo, pMat);
  scene.add(points);

  /* ---------------- post-processing ---------------- */
  let composer = null, bloom = null;
  if (!lowPower) {
    composer = new EffectComposer(renderer);
    composer.addPass(new RenderPass(scene, camera));
    bloom = new UnrealBloomPass(new THREE.Vector2(innerWidth, innerHeight), 0.55, 0.6, 0.82);
    composer.addPass(bloom);
    composer.addPass(new OutputPass());
  }

  /* ---------------- section poses ---------------- */
  // x/y in world units relative to camera view, z = depth offset, s = scale, d = distortion, e = emerald mix
  const POSES = {
    hero:      { x: 2.5,  y: 0.25, z: 0,   s: 0.95, d: 0.22, e: 0.0, f: 0.95 },
    stats:     { x: 3.4,  y: -1.2, z: -3,  s: 0.6,  d: 0.2,  e: 0.3, f: 0.9 },
    manifesto: { x: 3.3,  y: 1.7,  z: -2,  s: 0.8,  d: 0.32, e: 0.6, f: 1.2 },
    services:  { x: -3.8, y: -1.6, z: -8,  s: 0.85, d: 0.22, e: 0.0, f: 1.0 },
    work:      { x: 0,    y: 0,    z: -16, s: 1.4,  d: 0.3,  e: 0.4, f: 0.9 },
    about:     { x: -3.4, y: 1.9,  z: -4,  s: 0.6,  d: 0.2,  e: 0.0, f: 1.0 },
    process:   { x: 3.6,  y: 2.7,  z: -6,  s: 0.7,  d: 0.4,  e: 0.8, f: 1.3 },
    packages:  { x: -3.9, y: 2.5,  z: -6,  s: 0.6,  d: 0.22, e: 0.0, f: 1.0 },
    clients:   { x: 0,    y: 0.1,  z: -10, s: 0.9,  d: 0.28, e: 0.5, f: 1.1 },
    gallery:   { x: 3.6,  y: 1.9,  z: -6,  s: 0.6,  d: 0.25, e: 0.2, f: 1.0 },
    contact:   { x: 2.3,  y: -0.1, z: -3,  s: 1.0,  d: 0.28, e: 0.1, f: 1.0 },
    footer:    { x: 0,    y: 1.4,  z: -6,  s: 0.9,  d: 0.35, e: 0.5, f: 0.9 },
  };
  const MOBILE_POSE = { hero: { x: 0.4, y: 1.5, s: 0.85 } };
  const sections = [...document.querySelectorAll('[data-scene]')];
  const cur = { ...POSES.hero };

  function targetPose() {
    const mid = innerHeight * 0.5;
    let key = 'hero';
    for (const s of sections) {
      const r = s.getBoundingClientRect();
      if (r.top <= mid && r.bottom > mid) { key = s.dataset.scene; break; }
    }
    const p = { ...(POSES[key] || POSES.hero) };
    if (isMobile()) {
      Object.assign(p, MOBILE_POSE[key] || {});
      p.x *= 0.35; p.s *= 0.8;
    }
    return p;
  }

  /* ---------------- input ---------------- */
  const mouse = new THREE.Vector2(), mouseS = new THREE.Vector2();
  addEventListener('pointermove', (e) => {
    mouse.x = e.clientX / innerWidth - 0.5;
    mouse.y = e.clientY / innerHeight - 0.5;
  }, { passive: true });

  let lastScroll = scrollY, speed = 0, intro = reduce ? 1 : 0;
  addEventListener('bhavi:ready', () => { intro = 0.0001; }, { once: true });
  if (!document.documentElement.classList.contains('is-loading')) intro = 0.0001;

  /* ---------------- resize ---------------- */
  function resize() {
    camera.aspect = innerWidth / innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(innerWidth, innerHeight);
    if (composer) { composer.setSize(innerWidth, innerHeight); bloom.resolution.set(innerWidth, innerHeight); }
    pMat.uniforms.uPR.value = renderer.getPixelRatio();
  }
  addEventListener('resize', resize);

  /* ---------------- loop ---------------- */
  const clock = new THREE.Clock();
  let running = true;
  document.addEventListener('visibilitychange', () => {
    running = !document.hidden;
    if (running) { clock.getDelta(); requestAnimationFrame(loop); }
  });

  const tmp = new THREE.Vector3();
  function loop() {
    if (!running) return;
    const dt = Math.min(clock.getDelta(), 0.05);
    const t = clock.elapsedTime;
    const docH = Math.max(1, document.documentElement.scrollHeight - innerHeight);
    const prog = Math.min(1, scrollY / docH);

    // scroll velocity (for particle stretch + spin)
    speed += ((Math.abs(scrollY - lastScroll) / Math.max(dt, 0.001)) / 4000 - speed) * 0.08;
    speed = Math.min(speed, 1.2);
    lastScroll = scrollY;

    // intro: core grows out of nothing after the loader
    if (intro > 0 && intro < 1) intro = Math.min(1, intro + dt * 0.6);
    const introE = 1 - Math.pow(1 - intro, 4);

    // camera flies through the field as you scroll
    const camZ = 8 - prog * (DEPTH - 22);
    camera.position.z += (camZ - camera.position.z) * 0.08;
    mouseS.lerp(mouse, 0.05);
    camera.position.x = mouseS.x * 0.9;
    camera.position.y = -mouseS.y * 0.6;
    camera.lookAt(camera.position.x * 0.4, camera.position.y * 0.4, camera.position.z - 10);

    // ease core pose toward the current section
    const tp = targetPose(), k = reduce ? 1 : 0.045;
    for (const key in tp) cur[key] += (tp[key] - cur[key]) * k;

    tmp.set(cur.x, cur.y, camera.position.z - 8 + cur.z);
    coreGroup.position.copy(tmp);
    coreGroup.scale.setScalar(cur.s * (0.001 + introE));
    coreUniforms.uDistort.value = cur.d + speed * 0.35 + Math.sin(t * 0.6) * 0.04;
    coreUniforms.uFreq.value = cur.f;
    coreUniforms.uEmerald.value = cur.e;
    coreUniforms.uTime.value = t * (reduce ? 0 : 1);

    core.rotation.y += dt * (0.12 + speed * 1.5);
    core.rotation.x = mouseS.y * 0.6 + Math.sin(t * 0.2) * 0.2;
    core.rotation.z = mouseS.x * 0.4;
    shell.rotation.y -= dt * 0.08; shell.rotation.x += dt * 0.05;
    rings.children.forEach((r, i) => { r.rotation.z += dt * (0.25 + i * 0.12) * (i % 2 ? -1 : 1); });
    rim.position.set(coreGroup.position.x - 4, coreGroup.position.y - 2, coreGroup.position.z + 4);
    key.position.set(coreGroup.position.x + 4, coreGroup.position.y + 5, coreGroup.position.z + 6);

    shapes.forEach((m) => {
      const u = m.userData;
      m.rotation.x += dt * u.rs.x; m.rotation.y += dt * u.rs.y;
      m.position.y = u.base + Math.sin(t * 0.6 + u.fy) * 0.35;
    });

    pMat.uniforms.uTime.value = t;
    pMat.uniforms.uSpeed.value = speed;
    points.rotation.z = t * 0.01 + prog * 1.2;

    if (composer) composer.render(); else renderer.render(scene, camera);
    if (!reduce) requestAnimationFrame(loop);
  }
  requestAnimationFrame(loop);
  if (reduce) addEventListener('scroll', () => requestAnimationFrame(loop), { passive: true });
}
