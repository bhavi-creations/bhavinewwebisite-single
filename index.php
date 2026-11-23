<?php
/* =====================================================================
   BHAVI CREATIONS — 3D ANIMATED HOMEPAGE
   Content lives in site_data.php. Styles: assets/3d/style.css
   3D scene: assets/3d/scene.js · Animations: assets/3d/app.js
   Old homepage kept as index_old.php
   ===================================================================== */
require __DIR__ . '/site_data.php';
header('Cache-Control: no-cache, must-revalidate');
$v = function ($f) { return @filemtime(__DIR__ . '/' . $f) ?: time(); };
$wa = 'https://api.whatsapp.com/send?phone=' . $CONTACT['phone_raw'];
?>
<!DOCTYPE html>
<html lang="en" class="is-loading">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Bhavi Creations Private Limited — Premium Branding & Creative Agency</title>
  <meta name="description" content="Bhavi Creations is a premium branding & creative agency in Kakinada. Branding, websites, creative content, digital marketing and offline advertising that deliver real results.">
  <meta name="theme-color" content="#05070a">
  <link rel="icon" href="assets/img/favicon.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=Manrope:wght@400;500;600;700&family=Instrument+Serif:ital@1&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="assets/3d/style.css?v=<?= $v('assets/3d/style.css') ?>">
  <script type="importmap">
  { "imports": {
      "three": "https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.module.js",
      "three/addons/": "https://cdn.jsdelivr.net/npm/three@0.160.0/examples/jsm/"
  } }
  </script>
</head>
<body>

<!-- ============ PRELOADER ============ -->
<div class="loader" id="loader" aria-hidden="true">
  <div class="loader-logo"><span class="loader-fill"></span></div>
  <div class="loader-meta"><span>Bhavi Creations</span><b id="loaderNum">0</b></div>
  <div class="loader-bar"><i id="loaderBar"></i></div>
</div>

<!-- ============ 3D CANVAS ============ -->
<canvas id="webgl" aria-hidden="true"></canvas>
<div class="grain" aria-hidden="true"></div>
<div class="vignette" aria-hidden="true"></div>

<!-- cursor + progress -->
<div class="cursor" aria-hidden="true"><span class="cursor-dot"></span><span class="cursor-ring"><em></em></span></div>
<div class="progress" aria-hidden="true"><i id="progressBar"></i></div>

<!-- ============ HEADER ============ -->
<header class="hdr" id="hdr">
  <a href="#home" class="hdr-logo" aria-label="Bhavi Creations — Home">
    <img src="assets/3d/logo-white.svg" alt="Bhavi Creations" width="150" height="53">
  </a>
  <nav class="hdr-nav" id="nav" aria-label="Main">
    <a href="#home" data-nav="home">Home</a>
    <a href="#about" data-nav="about">About</a>
    <a href="#services" data-nav="services">Services</a>
    <a href="#work" data-nav="work">Work</a>
    <a href="our_works.php">Portfolio</a>
    <a href="#packages" data-nav="packages">Packages</a>
    <a href="#contact" data-nav="contact">Contact</a>
    <a href="<?= e($CONTACT['login']) ?>">Login</a>
  </nav>
  <a href="<?= e($wa) ?>" target="_blank" rel="noopener" class="btn btn-gold hdr-cta" data-magnetic>
    <span>Let's Talk</span><i class="bi bi-arrow-up-right"></i>
  </a>
  <button class="burger" id="burger" aria-label="Open menu" aria-expanded="false" aria-controls="menu"><span></span><span></span></button>
</header>

<div class="menu" id="menu" aria-hidden="true">
  <nav>
    <a href="#home"><small>01</small>Home</a>
    <a href="#about"><small>02</small>About</a>
    <a href="#services"><small>03</small>Services</a>
    <a href="#work"><small>04</small>Work</a>
    <a href="our_works.php"><small>05</small>Portfolio</a>
    <a href="#packages"><small>06</small>Packages</a>
    <a href="#contact"><small>07</small>Contact</a>
    <a href="<?= e($CONTACT['login']) ?>"><small>08</small>Login</a>
  </nav>
  <div class="menu-foot">
    <a href="tel:+<?= e($CONTACT['phone_raw']) ?>"><?= e($CONTACT['phone']) ?></a>
    <a href="mailto:<?= e($CONTACT['email']) ?>"><?= e($CONTACT['email']) ?></a>
  </div>
</div>

<main id="smooth">

<!-- ============ 01 HERO ============ -->
<section class="hero" id="home" data-scene="hero">
  <div class="hero-in">
    <p class="eyebrow hero-eyebrow"><span class="dot"></span>India's Premium Branding &amp; Creative Agency</p>
    <h1 class="hero-title" data-hero-split>We Build <em>Brands</em><br>That People <span class="gold-text">Remember.</span></h1>
    <div class="hero-bottom">
      <p class="hero-lead">We help businesses grow through branding, websites, creative content, digital marketing, and offline advertising that deliver real results.</p>
      <div class="hero-acts">
        <a href="#services" class="btn btn-gold" data-magnetic><span>Our Services</span><i class="bi bi-arrow-down-right"></i></a>
        <a href="#work" class="btn btn-ghost" data-magnetic><span>See Our Work</span><i class="bi bi-play-fill"></i></a>
      </div>
    </div>
  </div>

  <a href="#stats" class="hero-badge" aria-label="Scroll down">
    <svg viewBox="0 0 120 120" aria-hidden="true"><defs><path id="circ" d="M60,60 m-46,0 a46,46 0 1,1 92,0 a46,46 0 1,1 -92,0"/></defs>
      <text><textPath href="#circ" textLength="286">BRAND ARCHITECTURE · PERFORMANCE · CREATIVITY ·</textPath></text></svg>
    <i class="bi bi-arrow-down"></i>
  </a>

  <div class="hero-scroll" aria-hidden="true"><span>Scroll to explore</span><i></i></div>

  <div class="ticker" aria-hidden="true">
    <div class="ticker-track">
      <?php for ($k = 0; $k < 2; $k++): foreach ($SERVICES as $s): ?>
        <span><?= e($s[0]) ?></span><b>✦</b>
      <?php endforeach; endfor; ?>
    </div>
  </div>
</section>

<!-- ============ 02 STATS ============ -->
<section class="stats" id="stats" data-scene="stats">
  <div class="wrap">
    <div class="stats-grid">
      <?php foreach ($STATS as $i => $s): ?>
      <div class="stat glass" data-tilt>
        <span class="stat-idx">0<?= $i + 1 ?></span>
        <strong><span data-count="<?= (int)$s[0] ?>">0</span><sup><?= e($s[1]) ?></sup></strong>
        <p><?= e($s[2]) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
    <p class="stats-quote" data-reveal>You don't have a marketing problem. You have a <span class="gold-text">Bhavi Creations</span> problem. <em>We're here to fix it.</em></p>
  </div>
</section>

<!-- ============ 03 MANIFESTO ============ -->
<section class="manifesto" id="manifesto" data-scene="manifesto">
  <div class="wrap">
    <p class="eyebrow">( The question of the decade )</p>
    <p class="mf-text" data-words>What happens when everyone in the world gets a <em>Genie?</em> 8 billion people. 8 billion genies. Every wish, granted. The wish gets easy — but <em>vision</em> gets rare. Bhavi Creations helps brands choose the right story, the right moment, and the right way to be <span class="gold-text">remembered.</span></p>
    <a href="#about" class="link-arrow" data-reveal>Find yours <i class="bi bi-arrow-right"></i></a>
  </div>
</section>

<!-- ============ 04 SERVICES (3D RING) ============ -->
<section class="services" id="services" data-scene="services">
  <span id="brands" class="anchor"></span>
  <div class="svc-pin">
    <div class="sec-head wrap">
      <div>
        <p class="eyebrow">( 03 — What we do )</p>
        <h2 class="sec-title" data-split>Services that <em>move</em> brands.</h2>
      </div>
      <p class="sec-sub">Scroll to spin the ring. Tap any service to open its world.</p>
    </div>
    <div class="ring-stage">
      <div class="ring" id="ring">
        <?php foreach ($SERVICES as $i => $s): ?>
        <button type="button" class="svc-card" data-svc="<?= $i ?>" data-cursor="Open">
          <span class="svc-img"><img src="<?= e(asset($s[4][0])) ?>" alt="" loading="lazy"></span>
          <span class="svc-body">
            <span class="svc-top"><i class="bi <?= e($s[3]) ?>"></i><small><?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?></small></span>
            <strong><?= e($s[0]) ?></strong>
            <span class="svc-sub"><?= e($s[1]) ?></span>
          </span>
        </button>
        <?php endforeach; ?>
      </div>
      <div class="ring-floor" aria-hidden="true"></div>
    </div>
    <div class="ring-caption">
      <span id="ringIdx">01</span><i></i><span id="ringName"><?= e($SERVICES[0][0]) ?></span>
    </div>
  </div>
</section>

<dialog class="modal" id="svcModal" aria-labelledby="svcTitle">
  <button class="modal-x" type="button" aria-label="Close" data-close><i class="bi bi-x-lg"></i></button>
  <div class="modal-in">
    <div class="modal-text">
      <p class="eyebrow" id="svcSub"></p>
      <h3 id="svcTitle"></h3>
      <p id="svcDesc"></p>
      <a href="#contact" class="btn btn-gold" data-close><span>Start a project</span><i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="modal-gallery" id="svcGallery"></div>
  </div>
</dialog>

<!-- ============ 05 WEB WORK (3D TUNNEL) ============ -->
<section class="work" id="work" data-scene="work">
  <div class="work-pin">
    <div class="work-head wrap">
      <div>
        <p class="eyebrow">( 04 — Web Work )</p>
        <h2 class="sec-title">Live sites we <em>designed</em> &amp; built.</h2>
      </div>
      <p class="work-count"><b>50+</b> websites delivered</p>
    </div>
    <div class="tunnel" id="tunnel">
      <?php foreach ($SITES as $i => $s): $isVid = preg_match('/\.mp4$/i', $s[3]); ?>
      <article class="wk" style="--c:<?= e($s[2]) ?>" data-name="<?= e($s[0]) ?>" data-url="<?= e($s[4]) ?>">
        <div class="wk-media">
          <?php if ($isVid): ?>
            <video src="<?= e(asset($s[3])) ?>" muted loop playsinline preload="metadata"></video>
          <?php else: ?>
            <img src="<?= e(asset($s[3])) ?>" alt="<?= e($s[0]) ?> website" loading="lazy">
          <?php endif; ?>
          <span class="wk-fallback"><?= e($s[0]) ?></span>
        </div>
        <div class="wk-bar"><span></span><span></span><span></span><em><?= e(parse_url($s[4], PHP_URL_HOST)) ?></em></div>
      </article>
      <?php endforeach; ?>
    </div>
    <div class="work-hud wrap">
      <div class="work-hud-l">
        <small id="wkIdx">01 / <?= str_pad(count($SITES), 2, '0', STR_PAD_LEFT) ?></small>
        <h3 id="wkName"><?= e($SITES[0][0]) ?></h3>
      </div>
      <div class="work-hud-r">
        <a href="<?= e($SITES[0][4]) ?>" id="wkVisit" class="btn btn-ghost" target="_blank" rel="noopener" data-magnetic><span>Visit Site</span><i class="bi bi-arrow-up-right"></i></a>
        <a href="our_works.php" class="link-arrow">View all work <i class="bi bi-arrow-right"></i></a>
      </div>
      <div class="work-prog"><i id="wkProg"></i></div>
    </div>
  </div>
</section>

<!-- ============ 06 ABOUT ============ -->
<section class="about" id="about" data-scene="about">
  <div class="wrap about-grid">
    <div class="about-visual">
      <div class="about-frame" data-tilt>
        <span class="orbit o1"></span><span class="orbit o2"></span>
        <img src="assets/img/phani_ceo_sir.png" alt="Mr. Ch. Naga Phaneendra, CEO of Bhavi Creations" loading="lazy">
        <div class="about-chip glass"><b>11+</b><span>Years of<br>creative leadership</span></div>
      </div>
    </div>
    <div class="about-text">
      <p class="eyebrow">( 05 — About us )</p>
      <h2 class="sec-title" data-split>The <em>vision</em> behind Bhavi.</h2>
      <p class="about-lead" data-reveal>Meet <strong>Mr. Ch. Naga Phaneendra</strong>, CEO of Bhavi Creations, nestled in Kakinada, behind the J.N.T.U. Engineering College Playground. Under his leadership, Bhavi Creations thrives with a commitment to innovation and excellence, propelling it to the forefront of the industry.</p>
      <div class="about-more" id="aboutMore">
        <p>Phaneendra's strategic insights and passion define the company's ethos, ensuring a journey towards artistic brilliance. Beyond business, he's a visionary with a profound love for the arts, rooted in Kakinada's cultural richness. Phaneendra fosters an environment of innovation and creativity, exploring the intersection of technology and art. Reach out for inquiries or collaborations at <a href="mailto:<?= e($CONTACT['email']) ?>"><?= e($CONTACT['email']) ?></a> or <a href="tel:+<?= e($CONTACT['phone_raw']) ?>"><?= e($CONTACT['phone']) ?></a>.</p>
      </div>
      <button type="button" class="btn btn-ghost" id="aboutBtn" aria-expanded="false" aria-controls="aboutMore" data-magnetic><span>Know More</span><i class="bi bi-plus-lg"></i></button>
    </div>
  </div>
</section>

<!-- ============ 07 PROCESS (HORIZONTAL 3D) ============ -->
<section class="process" id="process" data-scene="process">
  <div class="proc-pin">
    <div class="sec-head wrap">
      <div>
        <p class="eyebrow">( 06 — Our process )</p>
        <h2 class="sec-title" data-split>A proven process <em>that works.</em></h2>
      </div>
      <p class="sec-sub proc-count"><b id="procIdx">01</b> / <?= str_pad(count($STEPS), 2, '0', STR_PAD_LEFT) ?></p>
    </div>
    <div class="proc-track" id="procTrack">
      <?php foreach ($STEPS as $i => $s): ?>
      <article class="step glass">
        <span class="step-num"><?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?></span>
        <i class="bi <?= e($s[2]) ?> step-ico"></i>
        <h3><?= e($s[0]) ?></h3>
        <p><?= e($s[1]) ?></p>
      </article>
      <?php endforeach; ?>
      <article class="step step-end">
        <p>The best choice is</p>
        <h3 class="gold-text">Bhavi Creations</h3>
        <a href="#contact" class="btn btn-gold" data-magnetic><span>Let's begin</span><i class="bi bi-arrow-right"></i></a>
      </article>
    </div>
    <div class="proc-line wrap"><i id="procLine"></i></div>
  </div>
</section>

<!-- ============ 08 PACKAGES ============ -->
<section class="packages" id="packages" data-scene="packages">
  <div class="wrap">
    <div class="sec-head sec-head-c">
      <p class="eyebrow"><i class="bi bi-stars"></i> Signature Packages</p>
      <h2 class="sec-title" data-split>Premium solutions for <em>ambitious brands.</em></h2>
      <p class="sec-sub">Compare our branding and digital growth plans, designed to support businesses at every stage of their journey.</p>
    </div>
    <div class="plans">
      <?php foreach ($PLANS as $p => $plan): ?>
      <article class="plan<?= $plan[3] ? ' plan-hot' : '' ?>" data-tilt>
        <?php if ($plan[3]): ?><span class="plan-flag">Recommended</span><?php endif; ?>
        <div class="plan-head">
          <span class="plan-ico"><i class="bi <?= e($plan[2]) ?>"></i></span>
          <h3><?= e($plan[0]) ?></h3>
          <p><?= e($plan[1]) ?></p>
        </div>
        <ul>
          <?php foreach ($FEATURES as $f): $on = $f[1][$p]; ?>
          <li class="<?= $on ? 'on' : 'off' ?>"><i class="bi <?= $on ? 'bi-check-lg' : 'bi-x-lg' ?>"></i><?= e($f[0]) ?></li>
          <?php endforeach; ?>
        </ul>
        <a href="#contact" class="btn <?= $plan[3] ? 'btn-gold' : 'btn-ghost' ?>" data-package="<?= e($plan[0]) ?>"><span>Choose <?= e($plan[0]) ?></span><i class="bi bi-arrow-right"></i></a>
      </article>
      <?php endforeach; ?>
    </div>
    <p class="plans-note"><i class="bi bi-shield-check"></i> Every plan can be customized based on your brand requirements.</p>
  </div>
</section>

<!-- ============ 09 CLIENTS (3D LOGO RING) ============ -->
<section class="clients" id="clients" data-scene="clients">
  <div class="wrap sec-head sec-head-c">
    <p class="eyebrow">( Brands we've worked with )</p>
    <h2 class="sec-title" data-split>Trusted by brands that <em>lead.</em></h2>
  </div>
  <div class="logo-stage">
    <div class="logo-ring" id="logoRing">
      <?php foreach ($LOGOS as $l):
        $img = '<img src="' . e(asset($l[1])) . '" alt="' . e($l[0]) . '" loading="lazy">'; ?>
        <div class="logo-plate<?= $l[3] ? ' dark' : '' ?>">
          <?= $l[2] ? '<a href="' . e($l[2]) . '" target="_blank" rel="noopener" aria-label="' . e($l[0]) . '">' . $img . '</a>' : $img ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="marquee" aria-hidden="true">
    <div class="marquee-track">
      <?php for ($k = 0; $k < 2; $k++): foreach ($LOGOS as $l): ?><span><?= e($l[0]) ?></span><b>✦</b><?php endforeach; endfor; ?>
    </div>
  </div>
</section>

<!-- ============ 10 GALLERY (COVERFLOW) ============ -->
<section class="gallery" id="gallery" data-scene="gallery">
  <div class="wrap sec-head">
    <div>
      <p class="eyebrow">( Inside Bhavi )</p>
      <h2 class="sec-title" data-split>Creativity <em>in motion.</em></h2>
    </div>
    <div class="cf-nav">
      <button type="button" id="cfPrev" aria-label="Previous" data-magnetic><i class="bi bi-arrow-left"></i></button>
      <button type="button" id="cfNext" aria-label="Next" data-magnetic><i class="bi bi-arrow-right"></i></button>
    </div>
  </div>
  <div class="coverflow" id="coverflow">
    <?php foreach ($GALLERY as $g): ?>
      <figure class="cf"><img src="<?= e(asset($g)) ?>" alt="Bhavi Creations work" loading="lazy" draggable="false"></figure>
    <?php endforeach; ?>
  </div>
  <div class="cf-dots" id="cfDots"></div>
</section>

<!-- ============ 11 CONTACT ============ -->
<section class="contact" id="contact" data-scene="contact">
  <span id="cta" class="anchor"></span>
  <div class="wrap contact-grid">
    <div class="contact-text">
      <p class="eyebrow">( Start a project )</p>
      <h2 class="contact-title" data-split>Let's get <em>in touch.</em></h2>
      <p class="contact-lead">Tell us about your brand. We'll reply on WhatsApp with a plan that fits.</p>
      <div class="contact-info">
        <a href="tel:+<?= e($CONTACT['phone_raw']) ?>" class="ci glass"><i class="bi bi-telephone"></i><span><small>Call</small><?= e($CONTACT['phone']) ?></span></a>
        <a href="mailto:<?= e($CONTACT['email']) ?>" class="ci glass"><i class="bi bi-envelope"></i><span><small>Email</small><?= e($CONTACT['email']) ?></span></a>
      </div>
    </div>
    <form class="form glass" id="contactForm" novalidate>
      <div class="field"><input name="name" id="fName" placeholder=" " required autocomplete="name"><label for="fName">Full name</label></div>
      <div class="field-row">
        <div class="field"><input type="email" name="email" id="fEmail" placeholder=" " required autocomplete="email"><label for="fEmail">Email</label></div>
        <div class="field"><input type="tel" name="number" id="fPhone" placeholder=" " autocomplete="tel"><label for="fPhone">Phone</label></div>
      </div>
      <div class="field-row">
        <div class="field field-sel">
          <select name="package" id="packageChoice">
            <option value="">Select a plan (optional)</option>
            <?php foreach ($PLANS as $plan): ?><option value="<?= e($plan[0]) ?>"><?= e($plan[0]) ?></option><?php endforeach; ?>
          </select><label for="packageChoice" class="up">Plan</label><i class="bi bi-chevron-down"></i>
        </div>
        <div class="field field-sel">
          <select name="place" id="contactPlace">
            <option value="">Select a place</option>
            <?php foreach ($PLACES as $pl): ?><option value="<?= e($pl) ?>"><?= e($pl) ?></option><?php endforeach; ?>
          </select><label for="contactPlace" class="up">Place</label><i class="bi bi-chevron-down"></i>
        </div>
      </div>
      <div class="field"><textarea name="remarks" id="fMsg" rows="4" placeholder=" "></textarea><label for="fMsg">Message</label></div>
      <button class="btn btn-gold btn-block" type="submit" data-magnetic><span id="formBtnTxt">Send message</span><i class="bi bi-send"></i></button>
      <p class="form-note" id="formNote" role="status"></p>
    </form>
  </div>
</section>

</main>

<!-- ============ FOOTER ============ -->
<footer class="ftr" data-scene="footer">
  <div class="ftr-big" aria-hidden="true"><span data-text="BHAVI CREATIONS">BHAVI CREATIONS</span></div>
  <div class="wrap ftr-grid">
    <div class="ftr-brand">
      <img src="assets/3d/logo-white.svg" alt="Bhavi Creations" width="200" height="71">
      <p>We are a premium branding &amp; creative agency, helping businesses stand out, attract customers and grow.</p>
      <div class="socials">
        <?php foreach ($SOCIALS as $s): ?>
        <a href="<?= e($s[2]) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= e($s[0]) ?>" data-magnetic><i class="bi <?= e($s[1]) ?>"></i></a>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="ftr-col">
      <h6>Explore</h6>
      <a href="#about">About</a><a href="#services">Services</a><a href="#work">Work</a><a href="our_works.php">Portfolio</a><a href="#packages">Packages</a>
    </div>
    <div class="ftr-col">
      <h6>Contact</h6>
      <p><i class="bi bi-geo-alt"></i> <?= e($CONTACT['address']) ?></p>
      <a href="tel:+<?= e($CONTACT['phone_raw']) ?>"><i class="bi bi-telephone"></i> <?= e($CONTACT['phone']) ?></a>
      <a href="mailto:<?= e($CONTACT['email']) ?>"><i class="bi bi-envelope"></i> <?= e($CONTACT['email']) ?></a>
    </div>
  </div>
  <div class="wrap ftr-bottom">
    <p>© 2022 Bhavi Creations Pvt Ltd. All Rights Reserved.</p>
    <p class="ftr-stamp">Designed with love @ <img src="assets/img/Bhavi_Branding_Stamp.png" alt="Bhavi Branding" width="44" height="44"></p>
  </div>
</footer>

<!-- floating widgets -->
<div class="floaters">
  <a href="#home" class="to-top" id="toTop" aria-label="Back to top">
    <svg viewBox="0 0 44 44" aria-hidden="true"><circle cx="22" cy="22" r="20"/><circle cx="22" cy="22" r="20" class="tt-p" id="toTopRing"/></svg>
    <i class="bi bi-arrow-up"></i>
  </a>
  <a href="<?= e($wa) ?>" class="wa" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
    <i class="fa-brands fa-whatsapp"></i><span>Chat with us</span>
  </a>
</div>

<script>window.BHAVI = <?= json_encode([
  'services' => array_map(fn($s) => ['title' => $s[0], 'sub' => $s[1], 'desc' => $s[2], 'images' => array_map('asset', $s[4])], $SERVICES),
  'whatsapp' => $CONTACT['phone_raw'],
], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP) ?>;</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lenis@1.1.13/dist/lenis.min.js"></script>
<script src="assets/3d/app.js?v=<?= $v('assets/3d/app.js') ?>"></script>
<script type="module" src="assets/3d/scene.js?v=<?= $v('assets/3d/scene.js') ?>"></script>

<?php include __DIR__ . '/chatbot.php'; ?>
</body>
</html>
