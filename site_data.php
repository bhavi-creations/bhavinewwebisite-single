<?php
/* =====================================================================
   BHAVI CREATIONS — WEBSITE DATA
   Edit the content here. index.php reads everything from this file.
   (Data moved from the old footer.php JS arrays and index.php markup.)
   ===================================================================== */

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
/* encode spaces etc. in local asset paths, keep slashes */
function asset($p) { return implode('/', array_map('rawurlencode', explode('/', $p))); }

$CONTACT = [
  'phone'      => '+91 96423 43434',
  'phone_raw'  => '919642343434',
  'email'      => 'admin@bhavicreations.com',
  'address'    => 'Plot no 28, RTO Office Rd, behind Lazza Ice Cream, Ranga Rao Nagar, Kakinada, Andhra Pradesh 533003',
  'login'      => 'https://bhavicreations.com/software/login.php',
];

$SOCIALS = [
  ['Facebook',  'bi-facebook',  'https://www.facebook.com/BhavicreationsPvtLtd/'],
  ['Instagram', 'bi-instagram', 'https://www.instagram.com/bhavicreations_pvtltd/'],
  ['LinkedIn',  'bi-linkedin',  'https://www.linkedin.com/in/bhavi-creations-pvt-ltd-926651235'],
  ['YouTube',   'bi-youtube',   'https://www.youtube.com/@bhavicreationspvtltd'],
  ['Pinterest', 'bi-pinterest', 'https://in.pinterest.com/bhavicreations/'],
];

/* [number, suffix, label] */
$STATS = [
  [1000, '+', 'Visitors'],
  [500,  '+', 'Happy Clients'],
  [140,  '+', 'Projects Completed'],
  [11,   '+', 'Years of Experience'],
  [90,   '%', 'Client Satisfaction'],
  [2,    '+', 'Awards Won'],
];

/* SERVICES: title, subtitle, description, icon, images */
$SERVICES = [
  ['Social Media', 'Creative Content Creation & Visual Design',
   'Build a powerful social media presence with creative content, strategic planning, and consistent engagement that increases brand awareness, attracts new customers, and drives real business growth across all major platforms.',
   'bi-instagram', ['assets/img/Social-Media-Marketing.webp', 'assets/img/social-media-1.jpg', 'assets/img/social-media-2.jfif']],
  ['Paid Ads', 'Google & Meta Ads',
   'Reach the right audience with high-performing paid advertising campaigns that increase website traffic, generate quality leads, and maximize your return on investment across Google and social media platforms.',
   'bi-megaphone', ['assets/img/paid-ads.png', 'assets/img/meta-ads-1.png', 'assets/img/facebook-ads-2.png', 'assets/img/google-ads-3.png', 'assets/img/linked-in-ads-4.png']],
  ['SEO', 'Search Engine Optimization that ranks',
   'Improve your website’s visibility with powerful SEO strategies that help your business rank higher on Google, attract qualified traffic, and generate more leads through sustainable organic growth.',
   'bi-search', ['assets/img/seo1.png', 'assets/img/seo-1.png', 'assets/img/seo-2.png', 'assets/img/seo-3.png']],
  ['Website Development', 'Website Design',
   'A website helps people learn about your business, products, and services. We create modern, fast, and mobile-friendly websites that help your business grow online.',
   'bi-window', ['assets/img/website-design.png', 'assets/img/website-design-1.png', 'assets/img/website-design-2.png', 'assets/img/website-design-3.png', 'assets/img/website-design-4.png']],
  ['Graphic Designing', 'Logo, website & brand identity design',
   'We create visually compelling designs — from logos and branding to print and digital media — that capture attention, communicate clearly, and leave a lasting impression.',
   'bi-palette2', ['assets/img/Graphic-Design.png', 'assets/img/graphic-design-2.png', 'assets/img/graphic-design-1.png', 'assets/img/graphic-design-3.png', 'assets/img/graphic-design-4.png']],
  ['Video Editing', 'Create engaging videos that inspire',
   'Creative video editing & colour grading, motion graphics & animated visuals that make your brand story move.',
   'bi-play-btn', ['assets/img/Video-editing.png', 'assets/img/video-editing-1.png', 'assets/img/video-editing-2.png']],
  ['GMB', 'Google My Business',
   'Help your Google Business Profile rank higher on Google Maps and local search, attract qualified traffic, and generate more leads through sustainable organic growth.',
   'bi-geo-alt', ['assets/img/gmb.png', 'assets/img/gmb-1.png', 'assets/img/gmb-2.png', 'assets/img/gmb-3.png']],
  ['Mobile App', 'Mobile apps development',
   'Fast, intuitive Android and iOS apps designed around your customers — from idea and UI design to development and launch.',
   'bi-phone', ['assets/img/mobile-app-development.png', 'assets/img/mobile-app-1.png', 'assets/img/mobile-app-2.png', 'assets/img/mobile-app-3.png', 'assets/img/mobile-app-4.png']],
  ['Branding', 'Unique Logo & Visual Identity Design',
   'Create a strong and memorable brand identity that builds trust, connects with your audience, and helps your business stand out in today’s competitive market with a consistent and professional image.',
   'bi-gem', ['assets/img/branding1.png']],
  ['Digital Marketing', 'Hoardings & banner promotions',
   'Promote your business with theatre ads, website designing, banners, pamphlets, and other traditional marketing solutions. Reach more local customers and grow your brand with confidence.',
   'bi-graph-up-arrow', ['assets/img/Digital-marketing-1.png', 'assets/img/Digital-marketing-2.png']],
];

/* WEB WORK: name, tag, fallback colour, media (mp4 or image), live url */
$SITES = [
  ['Srinivasa Dental',   'Dental Clinic',        '#1f3a5f', 'assets/video/srinivasa_dental_video.mp4', 'https://srinivasadentalkakinada.com/'],
  ['Krishna Denta Cure', 'Dental Clinic',        '#2a2a2a', 'assets/video/krishna_dentacure_video.mp4', 'https://krishnadentacureclinic.com/'],
  ['Vision Dental',      'Dental Clinic',        '#7a4b2a', 'assets/video/vision.mp4', 'https://visiondentalguntur.com/'],
  ['Apple Dental',       'Dental Specialities',  '#6b1f2a', 'assets/video/Apple_dental_specialities.png', 'https://appledentalspecialities.in/'],
  ['Nivis Labs',         'Laboratory',           '#2c3e8f', 'assets/video/nivis_labs.png', 'https://nivislabs.in/'],
  ['V&V Salons',         'Software Development', '#018f87', 'assets/video/vnv_saloon.png', 'https://vnvsalons.com/'],
  ['Care n Concern',     'Healthcare',           '#444444', 'assets/video/Care_n_concern.png', 'https://carenconcern.com/'],
];

/* PROCESS */
$STEPS = [
  ['Discover', 'We understand your goals, audience and business needs.', 'bi-binoculars'],
  ['Strategy', 'We research, analyze and create a winning plan for you.', 'bi-diagram-3'],
  ['Design',   'Our creative team brings your brand to life.', 'bi-vector-pen'],
  ['Review',   'We refine, perfect and ensure everything is just right.', 'bi-patch-check'],
  ['Launch',   'We launch with impact and support your growth.', 'bi-rocket-takeoff'],
  ['Growth',   'We optimize and scale your brand for long-term success.', 'bi-graph-up-arrow'],
];

/* PACKAGES — plan columns + feature rows (1 = included) */
$PLANS = [
  ['Basic',    'Start your digital presence',   'bi-lightning-charge', false],
  ['Standard', 'Build your brand consistently', 'bi-star',             false],
  ['Premium',  'Complete brand support',        'bi-gem',              true ],
  ['Growth',   'Scale with performance',        'bi-graph-up-arrow',   false],
];
$FEATURES = [
  ['Social Media Management', [1,1,1,1]],
  ['Graphic Design',          [1,1,1,1]],
  ['Logo Designing',          [0,0,1,1]],
  ['Video Editing',           [0,1,1,1]],
  ['Content Strategy',        [0,1,1,1]],
  ['SEO Optimization',        [0,0,1,1]],
  ['Website Support',         [0,0,0,1]],
  ['Paid Ads Management',     [0,0,1,1]],
  ['Monthly Reports',         [0,1,1,1]],
  ['Priority Support',        [0,0,1,1]],
];

/* CLIENT LOGOS: name, image, url, dark plate (for white logos) */
$LOGOS = [
  ['Srinivasa Dental',           'assets/img/Srinivasa-bg.png',                 'https://srinivasadentalkakinada.com/', false],
  ['Krishna Dental',             'assets/img/krishna-dental-1.png',             'https://krishnadentacureclinic.com/', false],
  ['iAlign Signature',           'assets/img/IALIGN-1.png',                     'https://ialignsignaturedentalcare.com/', false],
  ['Anish Dental',               'assets/img/Anish-dental-1.png',               'https://anishdental.in/', false],
  ['Dr. Subhash Plastic Surgeon','assets/img/Subash logo white.png',            'https://drsubhashplasticsurgeon.com/', true],
  ['Care N Concern',             'assets/img/care_n_concern.jpg',               'https://carenconcern.com/', false],
  ['Apple Dental',               'assets/img/apple-dental-1.png',               'https://appledentalspecialities.in/', false],
  ['Nayana Groups',              'assets/img/Nayana-groups-logo.png',           'https://nayanagroups.com/', false],
  ['Neurostar Hospital',         'assets/img/neurostar-logo-11.png',            'https://neurostarhospital.com/', false],
  ['Rajahmundry Steels',         'assets/img/rajahmundry-steels-logo_1.png',    '', false],
  ['Mega Modulars',              'assets/img/megamodulars-logo-1.png',          'https://megamodularindustries.com/', false],
  ['Smart Physio Care',          'assets/img/smart-physio-care-1.png',          'https://smartphysiocarehospitals.com/', false],
  ['V & V Salons',               'assets/img/V_AND_V_SALON_LOGO_NEW-1.png',     'https://vnvsalons.com/', false],
  ['Vision Dental',              'assets/img/vision-dental-1.png',              'https://visiondentalguntur.com/', false],
  ['Oncology',                   'assets/img/Oncology_logo-1.png',              'https://www.askoncologist.com/', false],
];

/* GALLERY (old "news" slider images) */
$GALLERY = [
  'assets/img/img-2.png',
  'assets/img/img-3.png',
  'assets/img/social media.png',
  'assets/img/website.png',
  'assets/img/seo_1.png',
  'assets/img/Graphic Design1.png',
  'assets/img/video-productions1.png',
];

$PLACES = ['Vizag', 'Kakinada', 'Rajahmundry', 'Hyderabad', 'Mumbai', 'Delhi', 'Thiruvananthapuram'];
