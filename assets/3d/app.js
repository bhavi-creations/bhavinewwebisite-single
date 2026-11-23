/* =====================================================================
   BHAVI CREATIONS — page animations (GSAP + ScrollTrigger + Lenis)
   The WebGL scene lives in scene.js and reads the [data-scene] sections.
   ===================================================================== */
(function () {
  'use strict';
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return [].slice.call((r || document).querySelectorAll(s)); };
  var root = document.documentElement;
  var reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  var fine = matchMedia('(pointer: fine)').matches;
  var hasGsap = !!(window.gsap && window.ScrollTrigger);
  var DATA = window.BHAVI || { services: [], whatsapp: '' };
  var lenis = null;

  if ('scrollRestoration' in history) history.scrollRestoration = 'manual';

  /* ---------- helpers ---------- */
  function splitChars(el) {
    (function walk(node) {
      [].slice.call(node.childNodes).forEach(function (n) {
        if (n.nodeType === 3) {
          var frag = document.createDocumentFragment();
          n.textContent.split(/(\s+)/).forEach(function (part) {
            if (!part) return;
            if (/^\s+$/.test(part)) { frag.appendChild(document.createTextNode(' ')); return; }
            var w = document.createElement('span'); w.className = 'w';
            Array.from(part).forEach(function (ch) {
              var c = document.createElement('span'); c.className = 'c'; c.textContent = ch; w.appendChild(c);
            });
            frag.appendChild(w);
          });
          n.parentNode.replaceChild(frag, n);
        } else if (n.nodeType === 1 && n.tagName !== 'BR') walk(n);
      });
    })(el);
    return $$('.c', el);
  }
  function splitWords(el) {
    (function walk(node) {
      [].slice.call(node.childNodes).forEach(function (n) {
        if (n.nodeType === 3) {
          var frag = document.createDocumentFragment();
          n.textContent.split(/(\s+)/).forEach(function (part) {
            if (!part) return;
            if (/^\s+$/.test(part)) { frag.appendChild(document.createTextNode(' ')); return; }
            var w = document.createElement('span'); w.className = 'mw'; w.textContent = part; frag.appendChild(w);
          });
          n.parentNode.replaceChild(frag, n);
        } else if (n.nodeType === 1 && n.tagName !== 'BR') walk(n);
      });
    })(el);
    return $$('.mw', el);
  }
  function scrollToTarget(target) {
    if (lenis) lenis.scrollTo(target, { duration: 1.6 });
    else if (typeof target === 'number') scrollTo({ top: target, behavior: reduce ? 'auto' : 'smooth' });
    else target.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth' });
  }
  function pad(n) { return String(n).padStart(2, '0'); }

  /* ---------- no GSAP: static, readable fallback ---------- */
  if (!hasGsap) {
    root.classList.add('static');
    root.classList.remove('is-loading');
    var ld = $('#loader'); if (ld) ld.classList.add('done');
    $$('[data-count]').forEach(function (el) { el.textContent = el.dataset.count; });
  }

  /* ---------- smooth scroll ---------- */
  if (hasGsap) gsap.registerPlugin(ScrollTrigger);
  if (hasGsap && window.Lenis && !reduce) {
    lenis = new Lenis({ duration: 1.15, easing: function (t) { return Math.min(1, 1.001 - Math.pow(2, -10 * t)); }, smoothWheel: true });
    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add(function (t) { lenis.raf(t * 1000); });
    gsap.ticker.lagSmoothing(0);
    lenis.stop();
  }
  window.BHAVI_LENIS = lenis;

  /* anchor links */
  document.addEventListener('click', function (e) {
    var a = e.target.closest('a[href^="#"]');
    if (!a) return;
    var id = a.getAttribute('href');
    if (id.length < 2 && id !== '#') return;
    var t = id === '#' || id === '#home' ? 0 : $(id);
    if (t === null) return;
    e.preventDefault();
    closeMenu();
    var modal = $('#svcModal'); if (modal && modal.open) closeModal();
    scrollToTarget(t);
  });

  /* ---------- header, menu, progress, to-top ---------- */
  var hdr = $('#hdr'), burger = $('#burger'), menu = $('#menu');
  function closeMenu() {
    if (!menu.classList.contains('open')) return;
    menu.classList.remove('open'); menu.setAttribute('aria-hidden', 'true');
    burger.setAttribute('aria-expanded', 'false'); burger.setAttribute('aria-label', 'Open menu');
    if (lenis) lenis.start();
  }
  burger.addEventListener('click', function () {
    var open = !menu.classList.contains('open');
    if (!open) return closeMenu();
    menu.classList.add('open'); menu.setAttribute('aria-hidden', 'false');
    burger.setAttribute('aria-expanded', 'true'); burger.setAttribute('aria-label', 'Close menu');
    if (lenis) lenis.stop();
  });
  addEventListener('keydown', function (e) { if (e.key === 'Escape') closeMenu(); });

  var lastY = 0, bar = $('#progressBar'), toTop = $('#toTop'), ttRing = $('#toTopRing');
  function onScroll() {
    var y = scrollY, h = document.documentElement.scrollHeight - innerHeight, p = h > 0 ? y / h : 0;
    hdr.classList.toggle('scrolled', y > 40);
    hdr.classList.toggle('hide', y > lastY && y > 400 && !menu.classList.contains('open'));
    lastY = y;
    bar.style.transform = 'scaleX(' + p + ')';
    toTop.classList.toggle('show', y > 500);
    ttRing.style.strokeDashoffset = 125.7 * (1 - p);
  }
  addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* active nav link */
  var navLinks = $$('[data-nav]');
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (!en.isIntersecting) return;
        var key = en.target.id;
        navLinks.forEach(function (l) { l.classList.toggle('active', l.dataset.nav === key); });
      });
    }, { rootMargin: '-45% 0px -50% 0px' });
    ['home', 'about', 'services', 'work', 'packages', 'contact'].forEach(function (id) { var s = document.getElementById(id); if (s) io.observe(s); });
  }

  /* WhatsApp label peek */
  var wa = $('.wa');
  if (wa && !reduce) setInterval(function () { wa.classList.add('open'); setTimeout(function () { wa.classList.remove('open'); }, 4000); }, 15000);

  /* ---------- cursor ---------- */
  var cursor = $('.cursor');
  if (fine && hasGsap && !reduce) {
    var dot = $('.cursor-dot'), ring = $('.cursor-ring'), label = $('.cursor-ring em');
    var dx = gsap.quickTo(dot, 'x', { duration: .12 }), dy = gsap.quickTo(dot, 'y', { duration: .12 });
    var rx = gsap.quickTo(ring, 'x', { duration: .5, ease: 'power3' }), ry = gsap.quickTo(ring, 'y', { duration: .5, ease: 'power3' });
    addEventListener('pointermove', function (e) { cursor.classList.add('ready'); dx(e.clientX); dy(e.clientY); rx(e.clientX); ry(e.clientY); }, { passive: true });
    document.addEventListener('pointerover', function (e) {
      var t = e.target.closest('[data-cursor], a, button, input, select, textarea, .coverflow');
      cursor.classList.toggle('hover', !!t && !(t.dataset && t.dataset.cursor));
      var lab = t && t.dataset ? t.dataset.cursor || (t.classList.contains('coverflow') ? 'Drag' : '') : '';
      cursor.classList.toggle('label', !!lab);
      if (lab) { label.textContent = lab; cursor.classList.remove('hover'); }
    });
  } else if (cursor) cursor.remove();

  /* ---------- magnetic + tilt ---------- */
  if (fine && !reduce) {
    $$('[data-magnetic]').forEach(function (el) {
      el.addEventListener('pointermove', function (e) {
        var r = el.getBoundingClientRect();
        var x = (e.clientX - r.left - r.width / 2) * .3, y = (e.clientY - r.top - r.height / 2) * .35;
        el.style.transform = 'translate(' + x + 'px,' + y + 'px)';
      });
      el.addEventListener('pointerleave', function () { el.style.transform = ''; });
    });
    $$('[data-tilt]').forEach(function (el) {
      var base = el.classList.contains('plan-hot') && innerWidth > 1180 ? ' translateY(-14px)' : '';
      el.style.transition = 'transform .6s cubic-bezier(.22,1,.36,1)';
      el.addEventListener('pointermove', function (e) {
        var r = el.getBoundingClientRect(), px = (e.clientX - r.left) / r.width, py = (e.clientY - r.top) / r.height;
        el.style.setProperty('--mx', px * 100 + '%'); el.style.setProperty('--my', py * 100 + '%');
        el.style.transition = 'transform .15s linear';
        el.style.transform = 'perspective(1000px) rotateX(' + ((.5 - py) * 12) + 'deg) rotateY(' + ((px - .5) * 14) + 'deg)' + base;
      });
      el.addEventListener('pointerleave', function () { el.style.transition = 'transform .8s cubic-bezier(.22,1,.36,1)'; el.style.transform = base ? 'translateY(-14px)' : ''; });
    });
  }

  /* ---------- about: know more ---------- */
  var aboutBtn = $('#aboutBtn'), aboutMore = $('#aboutMore');
  aboutBtn.addEventListener('click', function () {
    var open = aboutMore.classList.toggle('open');
    aboutBtn.setAttribute('aria-expanded', open);
    aboutBtn.querySelector('span').textContent = open ? 'Show Less' : 'Know More';
    setTimeout(function () { if (hasGsap) ScrollTrigger.refresh(); }, 750);
  });

  /* ---------- services modal ---------- */
  var modal = $('#svcModal');
  function openModal(i) {
    var s = DATA.services[i]; if (!s) return;
    $('#svcTitle').textContent = s.title;
    $('#svcSub').textContent = s.sub;
    $('#svcDesc').textContent = s.desc;
    var g = $('#svcGallery'); g.innerHTML = '';
    s.images.forEach(function (src) {
      var img = new Image(); img.src = src; img.alt = s.title; img.loading = 'lazy';
      img.onerror = function () { img.remove(); };
      g.appendChild(img);
    });
    if (modal.showModal) modal.showModal(); else modal.setAttribute('open', '');
    if (lenis) lenis.stop();
  }
  function closeModal() {
    if (modal.close) modal.close(); else modal.removeAttribute('open');
    if (lenis) lenis.start();
  }
  modal.addEventListener('close', function () { if (lenis) lenis.start(); });
  modal.addEventListener('click', function (e) {
    if (e.target === modal) closeModal();
    var c = e.target.closest('[data-close]');
    if (c && c.tagName === 'BUTTON') closeModal();
  });

  /* ---------- packages -> contact form ---------- */
  $$('[data-package]').forEach(function (a) {
    a.addEventListener('click', function () { $('#packageChoice').value = a.dataset.package; });
  });

  /* ---------- contact form -> WhatsApp ---------- */
  var form = $('#contactForm');
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var ok = true;
    ['fName', 'fEmail'].forEach(function (id) {
      var el = document.getElementById(id), valid = el.checkValidity() && el.value.trim();
      el.parentNode.classList.toggle('invalid', !valid); if (!valid) ok = false;
    });
    var note = $('#formNote');
    if (!ok) { note.textContent = 'Please enter your name and a valid email.'; return; }
    var f = new FormData(form), lines = ['Hello Bhavi Creations! 👋', '', 'Name: ' + f.get('name'), 'Email: ' + f.get('email')];
    if (f.get('number')) lines.push('Phone: ' + f.get('number'));
    if (f.get('package')) lines.push('Plan: ' + f.get('package'));
    if (f.get('place')) lines.push('Place: ' + f.get('place'));
    if (f.get('remarks')) lines.push('', f.get('remarks'));
    window.open('https://api.whatsapp.com/send?phone=' + DATA.whatsapp + '&text=' + encodeURIComponent(lines.join('\n')), '_blank', 'noopener');
    $('#formBtnTxt').textContent = 'Sent ✓';
    note.textContent = 'Thank you! WhatsApp opened with your details — just press send.';
    form.reset();
  });

  /* ---------- coverflow ---------- */
  (function () {
    var cf = $('#coverflow'); if (!cf) return;
    var cards = $$('.cf', cf), dots = $('#cfDots'), idx = 0, n = cards.length, timer;
    cards.forEach(function (_, i) {
      var b = document.createElement('button'); b.type = 'button'; b.setAttribute('aria-label', 'Slide ' + (i + 1));
      b.addEventListener('click', function () { go(i); }); dots.appendChild(b);
    });
    function layout() {
      var mob = innerWidth < 640;
      cards.forEach(function (c, i) {
        var d = i - idx;
        if (d > n / 2) d -= n; if (d < -n / 2) d += n;
        var ad = Math.abs(d);
        c.style.transform = 'translate(-50%,-50%) translateX(' + (d * (mob ? 62 : 48)) + '%) translateZ(' + (-ad * 260) + 'px) rotateY(' + (-d * 38) + 'deg)';
        c.style.opacity = ad > 2 ? 0 : 1 - ad * .22;
        c.style.zIndex = 10 - ad;
        c.style.filter = ad ? 'brightness(' + (1 - ad * .25) + ')' : 'none';
        c.classList.toggle('active', d === 0);
      });
      $$('button', dots).forEach(function (b, i) { b.classList.toggle('on', i === idx); });
    }
    function go(i) { idx = (i + n) % n; layout(); restart(); }
    function restart() { clearInterval(timer); if (!reduce) timer = setInterval(function () { go(idx + 1); }, 4000); }
    $('#cfPrev').addEventListener('click', function () { go(idx - 1); });
    $('#cfNext').addEventListener('click', function () { go(idx + 1); });
    cards.forEach(function (c, i) { c.addEventListener('click', function () { if (i !== idx) go(i); }); });
    var sx = null;
    cf.addEventListener('pointerdown', function (e) { sx = e.clientX; });
    addEventListener('pointerup', function (e) { if (sx === null) return; var d = e.clientX - sx; if (Math.abs(d) > 40) go(idx + (d < 0 ? 1 : -1)); sx = null; });
    addEventListener('resize', layout);
    layout(); restart();
  })();

  /* ---------- client logo ring geometry ---------- */
  function layoutLogoRing() {
    var ringEl = $('#logoRing'); if (!ringEl) return;
    var plates = $$('.logo-plate', ringEl), n = plates.length;
    var w = innerWidth < 640 ? 130 : innerWidth < 1000 ? 160 : 190;
    var R = Math.round((w + 26) / 2 / Math.tan(Math.PI / n));
    ringEl.style.setProperty('--lw', w + 'px');
    plates.forEach(function (p, i) { p.style.transform = 'rotateY(' + (i * 360 / n) + 'deg) translateZ(' + R + 'px)'; });
  }
  layoutLogoRing();
  addEventListener('resize', layoutLogoRing);

  /* =====================================================================
     GSAP SCROLL ANIMATIONS
     ===================================================================== */
  if (!hasGsap) return;
  ScrollTrigger.config({ ignoreMobileResize: true });

  /* split text now (before the loader finishes) */
  var heroChars = splitChars($('[data-hero-split]'));
  gsap.set(heroChars, { yPercent: 115, rotateX: -80, opacity: 0 });
  gsap.set(['.hero-eyebrow', '.hero-lead', '.hero-acts .btn', '.hero-badge', '.ticker', '.hdr'], { opacity: 0, y: 30 });

  /* ---------- preloader ---------- */
  (function () {
    var num = $('#loaderNum'), lbar = $('#loaderBar'), fill = $('.loader-fill'), state = { p: 0 }, loaded = false, minDone = false;
    addEventListener('load', function () { loaded = true; });
    if (document.readyState === 'complete') loaded = true;
    setTimeout(function () { minDone = true; }, reduce ? 200 : 1400);
    setTimeout(function () { loaded = true; }, 6000); /* never block longer than 6s */
    function tick() {
      var fast = loaded && minDone, dr = gsap.ticker.deltaRatio(60);
      var target = fast ? 100 : Math.min(state.p + .9 * dr, 88);
      state.p += (target - state.p) * (1 - Math.pow(1 - (fast ? .14 : .06), dr));
      if (target === 100 && state.p > 99.5) state.p = 100;
      num.textContent = Math.round(state.p);
      lbar.style.transform = 'scaleX(' + state.p / 100 + ')';
      fill.style.setProperty('--fill', (100 - state.p) + '%');
      if (state.p === 100) { gsap.ticker.remove(tick); finish(); }
    }
    gsap.ticker.add(tick);
    function finish() {
      var tl = gsap.timeline({ delay: .25 });
      tl.add(function () {
        $('#loader').classList.add('done');
        root.classList.remove('is-loading');
        scrollTo(0, 0);
        if (lenis) lenis.start();
        window.dispatchEvent(new Event('bhavi:ready'));
      })
        .to('.hdr', { opacity: 1, y: 0, duration: 1, ease: 'expo.out' }, .35)
        .to('.hero-eyebrow', { opacity: 1, y: 0, duration: 1, ease: 'expo.out' }, .45)
        .to(heroChars, { yPercent: 0, rotateX: 0, opacity: 1, duration: 1.3, ease: 'expo.out', stagger: .022 }, .5)
        .to(['.hero-lead', '.hero-acts .btn'], { opacity: 1, y: 0, duration: 1, ease: 'expo.out', stagger: .1 }, 1.1)
        .to(['.hero-badge', '.ticker'], { opacity: 1, y: 0, duration: 1, ease: 'expo.out', stagger: .1 }, 1.25)
        .add(function () { gsap.set('.hdr', { clearProps: 'transform,opacity' }); ScrollTrigger.refresh(); });
    }
  })();

  var mm = gsap.matchMedia();

  /* hero parallax out */
  gsap.to('.hero-in', { yPercent: -18, opacity: .15, ease: 'none', scrollTrigger: { trigger: '.hero', start: 'top top', end: 'bottom top', scrub: true } });

  /* ticker skew by velocity */
  if (lenis && !reduce) {
    var skewSet = gsap.quickTo('.ticker-track', 'skewX', { duration: .4 });
    lenis.on('scroll', function (l) { skewSet(gsap.utils.clamp(-12, 12, l.velocity * -.4)); });
  }

  /* section titles: char reveal */
  $$('[data-split]').forEach(function (el) {
    var chars = splitChars(el);
    $$('.w', el).forEach(function (w) { w.classList.add('mask-line'); });
    gsap.from(chars, {
      yPercent: 110, rotate: 6, opacity: 0, duration: 1.1, ease: 'expo.out', stagger: .018,
      scrollTrigger: { trigger: el, start: 'top 85%', once: true }
    });
  });

  /* generic reveals */
  $$('[data-reveal]').forEach(function (el) {
    gsap.from(el, { y: 60, opacity: 0, duration: 1.2, ease: 'expo.out', scrollTrigger: { trigger: el, start: 'top 88%', once: true } });
  });
  $$('.eyebrow:not(.hero-eyebrow)').forEach(function (el) {
    gsap.from(el, { x: -30, opacity: 0, duration: 1, ease: 'expo.out', scrollTrigger: { trigger: el, start: 'top 90%', once: true } });
  });

  /* stats: 3D flip-in + counters */
  gsap.from('.stat', { rotateX: -70, y: 80, opacity: 0, transformOrigin: '50% 100%', duration: 1.3, ease: 'expo.out', stagger: .08, scrollTrigger: { trigger: '.stats-grid', start: 'top 85%', once: true } });
  $$('[data-count]').forEach(function (el) {
    var o = { v: 0 }, n = +el.dataset.count;
    ScrollTrigger.create({ trigger: el, start: 'top 90%', once: true, onEnter: function () {
      gsap.to(o, { v: n, duration: 2.2, ease: 'power3.out', onUpdate: function () { el.textContent = Math.round(o.v); } });
    } });
  });

  /* manifesto: words light up on scroll */
  var words = splitWords($('[data-words]'));
  gsap.to(words, { opacity: 1, ease: 'none', stagger: .1, scrollTrigger: { trigger: '.mf-text', start: 'top 80%', end: 'bottom 45%', scrub: true } });

  /* ---------- SERVICES: 3D ring ---------- */
  (function () {
    var ringEl = $('#ring'), cards = $$('.svc-card', ringEl), n = cards.length, step = 360 / n, R = 0, rot = { v: 0 }, last = -1;
    var idxEl = $('#ringIdx'), nameEl = $('#ringName');
    function geom() {
      var w = innerWidth < 640 ? 200 : innerWidth < 1000 ? 230 : 260, h = Math.round(w * 1.36);
      ringEl.style.setProperty('--cw', w + 'px'); ringEl.style.setProperty('--ch', h + 'px');
      R = Math.round((w + (innerWidth < 640 ? 18 : 34)) / 2 / Math.tan(Math.PI / n));
      cards.forEach(function (c, i) { c.style.transform = 'rotateY(' + (i * step) + 'deg) translateZ(' + R + 'px)'; });
      render();
    }
    function render() {
      ringEl.style.transform = 'translateZ(' + (-R) + 'px) rotateY(' + rot.v + 'deg)';
      var active = ((Math.round(-rot.v / step) % n) + n) % n;
      cards.forEach(function (c, i) {
        var a = ((i * step + rot.v) % 360 + 540) % 360 - 180; /* -180..180, 0 = front */
        var f = Math.abs(a) / 180;
        c.style.filter = 'brightness(' + (1 - f * .75) + ')';
        c.style.pointerEvents = Math.abs(a) < 60 ? 'auto' : 'none';
      });
      if (active !== last) {
        last = active;
        cards.forEach(function (c, i) { c.classList.toggle('active', i === active); });
        idxEl.textContent = pad(active + 1);
        nameEl.textContent = DATA.services[active] ? DATA.services[active].title : '';
      }
    }
    cards.forEach(function (c) { c.addEventListener('click', function () { openModal(+c.dataset.svc); }); });
    geom();
    addEventListener('resize', geom);
    gsap.to(rot, {
      v: -step * (n - 1), ease: 'none', onUpdate: render,
      scrollTrigger: { trigger: '.services', start: 'top top', end: '+=' + (n * 32) + '%', pin: '.svc-pin', scrub: 1, anticipatePin: 1,
        snap: { snapTo: 1 / (n - 1), duration: { min: .2, max: .6 }, ease: 'power2.inOut', delay: .05 } }
    });
    gsap.from('.ring', { rotateX: 60, y: 200, opacity: 0, duration: 1.6, ease: 'expo.out', scrollTrigger: { trigger: '.services', start: 'top 70%', once: true } });
  })();

  /* ---------- WORK: 3D tunnel ---------- */
  (function () {
    var cards = $$('.wk'), n = cards.length, GAP = 1500, last = -1;
    var nameEl = $('#wkName'), idxEl = $('#wkIdx'), visit = $('#wkVisit'), prog = $('#wkProg');
    cards.forEach(function (c) {
      var v = c.querySelector('video'), img = c.querySelector('img');
      if (v) v.addEventListener('error', function () { v.remove(); }, true);
      if (img) img.addEventListener('error', function () { img.remove(); });
      c.addEventListener('click', function () { window.open(c.dataset.url, '_blank', 'noopener'); });
    });
    function render(p) {
      var mob = innerWidth < 900, cam = p * (n - 1) * GAP, side = mob ? 0 : Math.min(innerWidth * .2, 300);
      cards.forEach(function (c, i) {
        var z = -i * GAP + cam;
        var x = (i % 2 ? 1 : -1) * side * Math.min(1, Math.max(0, -z / GAP));
        var y = mob ? 0 : (i % 3 - 1) * 30 * Math.min(1, Math.max(0, -z / GAP));
        var rotY = (i % 2 ? -1 : 1) * 14 * Math.min(1, Math.max(0, -z / GAP));
        var o = z > 0 ? Math.max(0, 1 - z / 260) : Math.max(0, 1 + z / (GAP * 3.2));
        c.style.transform = 'translate3d(' + x + 'px,' + y + 'px,' + z + 'px) rotateY(' + rotY + 'deg)';
        c.style.opacity = o.toFixed(3);
        c.style.visibility = o < .01 ? 'hidden' : 'visible';
        c.style.zIndex = 100 - i;
      });
      var active = Math.min(n - 1, Math.round(p * (n - 1)));
      prog.style.transform = 'scaleX(' + p + ')';
      if (active !== last) {
        last = active;
        cards.forEach(function (c, i) {
          c.classList.toggle('on', i === active);
          var v = c.querySelector('video');
          if (v) { if (i === active) { var pr = v.play(); if (pr && pr.catch) pr.catch(function () {}); } else v.pause(); }
        });
        nameEl.textContent = cards[active].dataset.name;
        idxEl.textContent = pad(active + 1) + ' / ' + pad(n);
        visit.href = cards[active].dataset.url;
        gsap.fromTo(nameEl, { yPercent: 60, opacity: 0 }, { yPercent: 0, opacity: 1, duration: .6, ease: 'expo.out' });
      }
    }
    render(0);
    ScrollTrigger.create({
      trigger: '.work', start: 'top top', end: '+=' + (n * 80) + '%', pin: '.work-pin', scrub: 1, anticipatePin: 1,
      onUpdate: function (st) { render(st.progress); },
      onRefresh: function (st) { render(st.progress); }
    });
  })();

  /* ---------- ABOUT ---------- */
  gsap.from('.about-frame', { rotateY: -35, rotateX: 10, x: -80, opacity: 0, duration: 1.6, ease: 'expo.out', scrollTrigger: { trigger: '.about', start: 'top 70%', once: true } });
  gsap.from('.about-chip', { scale: .4, opacity: 0, duration: 1.2, delay: .5, ease: 'back.out(1.8)', scrollTrigger: { trigger: '.about', start: 'top 70%', once: true } });
  gsap.from('#aboutBtn', { y: 40, opacity: 0, duration: 1, ease: 'expo.out', scrollTrigger: { trigger: '#aboutBtn', start: 'top 92%', once: true } });

  /* ---------- PROCESS: horizontal 3D ---------- */
  (function () {
    var track = $('#procTrack'), steps = $$('.step', track), idxEl = $('#procIdx'), line = $('#procLine'), n = steps.length - 1;
    function dist() { return Math.max(0, track.scrollWidth - innerWidth); }
    var tween = gsap.to(track, {
      x: function () { return -dist(); }, ease: 'none',
      scrollTrigger: {
        trigger: '.process', start: 'top top', end: function () { return '+=' + dist() * 1.1; }, pin: '.proc-pin', scrub: 1, anticipatePin: 1, invalidateOnRefresh: true,
        onUpdate: function (st) {
          line.style.transform = 'scaleX(' + st.progress + ')';
          idxEl.textContent = pad(Math.min(n, Math.floor(st.progress * n) + 1));
        }
      }
    });
    steps.forEach(function (s) {
      gsap.fromTo(s, { rotateY: -40, z: -260, opacity: .25 }, {
        rotateY: 0, z: 0, opacity: 1, ease: 'none',
        scrollTrigger: { trigger: s, containerAnimation: tween, start: 'left 100%', end: 'left 45%', scrub: true }
      });
      var ico = s.querySelector('.step-ico');
      if (ico) gsap.fromTo(ico, { rotate: -90, scale: .4 }, { rotate: 0, scale: 1, ease: 'none', scrollTrigger: { trigger: s, containerAnimation: tween, start: 'left 95%', end: 'left 55%', scrub: true } });
    });
  })();

  /* ---------- PACKAGES ---------- */
  gsap.from('.plan', { y: 120, rotateX: -35, opacity: 0, transformOrigin: '50% 0%', duration: 1.4, ease: 'expo.out', stagger: .12,
    scrollTrigger: { trigger: '.plans', start: 'top 82%', once: true },
    onComplete: function () { gsap.set('.plan', { clearProps: 'transform' }); } });

  /* ---------- CLIENTS ---------- */
  gsap.from('.logo-stage', { scale: .6, opacity: 0, duration: 1.6, ease: 'expo.out', scrollTrigger: { trigger: '.logo-stage', start: 'top 85%', once: true } });

  /* ---------- GALLERY ---------- */
  gsap.from('.coverflow', { y: 100, opacity: 0, rotateX: 25, duration: 1.4, ease: 'expo.out', scrollTrigger: { trigger: '.coverflow', start: 'top 85%', once: true } });

  /* ---------- CONTACT ---------- */
  gsap.from('.form', { rotateY: 25, x: 80, opacity: 0, duration: 1.5, ease: 'expo.out', scrollTrigger: { trigger: '.contact', start: 'top 70%', once: true } });
  gsap.from('.ci', { x: -40, opacity: 0, duration: 1, stagger: .12, ease: 'expo.out', scrollTrigger: { trigger: '.contact-info', start: 'top 90%', once: true } });

  /* ---------- FOOTER big text fill ---------- */
  gsap.to('.ftr-big span', { '--fill': '100%', ease: 'none', scrollTrigger: { trigger: '.ftr', start: 'top 95%', end: 'top 25%', scrub: true } });

  /* keep pin math right once fonts/images settle */
  if (document.fonts && document.fonts.ready) document.fonts.ready.then(function () { ScrollTrigger.refresh(); });
  addEventListener('load', function () { ScrollTrigger.refresh(); });
})();
