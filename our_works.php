<?php
require_once __DIR__ . '/portfolio_lib.php';
include __DIR__ . '/db.connection/db_connection.php';

portfolio_ensure_schema($conn);

/* ---------- Load all works and group them by category ---------- */
$categories = portfolio_categories();
$typeToKey = [];
foreach ($categories as $key => $cat) $typeToKey[strtolower($cat['type'])] = $key;

$grouped = array_fill_keys(array_keys($categories), []);
$loadError = false;
$res = $conn->query('SELECT * FROM our_works ORDER BY sort_order DESC, id DESC');
if ($res === false) {
    $loadError = true;
} else {
    while ($row = $res->fetch_assoc()) {
        $type = trim((string) $row['media_type']);
        $key = $typeToKey[strtolower($type)] ?? null;
        if ($key === null) {
            // Any new media_type added in admin shows up automatically as a gallery section.
            $key = 'type-' . preg_replace('/[^a-z0-9]+/', '-', strtolower($type));
            if (!isset($categories[$key])) {
                $categories[$key] = ['type' => $type, 'tab' => $type, 'layout' => 'gallery', 'aspect' => '4 / 5', 'fit' => 'cover',
                    'preview' => 8, 'eyebrow' => $type, 'heading' => $type, 'intro' => ''];
                $grouped[$key] = [];
            }
        }
        $grouped[$key][] = $row;
    }
}
$conn->close();

// Sections marked 'hidden' => true in portfolio_lib.php are not shown on the website (data is kept).
foreach ($categories as $key => $cat) {
    if (!empty($cat['hidden'])) unset($grouped[$key], $categories[$key]);
}

$visible = array_filter($grouped, static fn($items) => count($items) > 0);

/* ---------- Hero numbers (live counts from the database) ---------- */
$count = static fn(array $keys) => array_sum(array_map(static fn($k) => count($grouped[$k] ?? []), $keys));
$heroStats = array_filter([
    ['value' => array_sum(array_map('count', $grouped)), 'suffix' => '+', 'label' => 'Projects delivered'],
    ['value' => $count(['websites']), 'suffix' => '+', 'label' => 'Websites launched'],
    ['value' => $count(['reels', 'videos', 'animatedvideos', 'testimonials']), 'suffix' => '+', 'label' => 'Reels & videos'],
    ['value' => $count(['posters', 'logo', 'visitingcards', 'pamphlets', 'brochures', 'hoardings']), 'suffix' => '+', 'label' => 'Creatives designed'],
], static fn($s) => $s['value'] > 0);

$uploadBase = 'admin/public/'; // + uploads/staff/<image> or upload/video/<video>

/** Public URL of an uploaded file, or '' when missing. */
function pf_upload_url(array $item, string $base): string
{
    $rel = pf_work_path((string) ($item['file_path'] ?? ''));
    if ($rel === '' || !is_file(__DIR__ . '/' . $base . $rel)) return '';
    return $base . pf_work_path((string) $item['file_path'], true);
}

function pf_title(array $item, string $fallback): string
{
    $title = trim((string) ($item['title'] ?? ''));
    if ($title !== '') return $title;
    $link = pf_safe_url($item['media_link'] ?? '');
    $info = pf_video_info($link);
    if ($link !== '' && $info['platform'] === '') return pf_domain($link);
    return $fallback;
}

function pf_tags(array $item): array
{
    return array_values(array_filter(array_map('trim', explode(',', (string) ($item['tags'] ?? '')))));
}

/* ---------- Card renderers ---------- */
function render_website_card(array $item, string $base, bool $hidden): void
{
    $link = pf_safe_url($item['media_link'] ?? '');
    $img = pf_upload_url($item, $base);
    $isSoftware = strcasecmp(trim((string) ($item['media_type'] ?? '')), 'Customised Software') === 0;
    $domain = $link !== '' ? pf_domain($link) : ($isSoftware ? 'software' : 'website');
    $title = pf_title($item, $isSoftware ? 'Software project' : 'Website project');
    $tags = pf_tags($item);
    ?>
    <article class="pf-card pf-web<?= $hidden ? ' is-extra' : '' ?>">
        <div class="pf-web__frame">
            <div class="pf-web__bar"><i></i><i></i><i></i><span><?= pf_e($domain) ?></span></div>
            <div class="pf-web__shot">
                <?php if ($img && pf_file_kind($img) === 'image'): ?>
                    <img src="<?= pf_e($img) ?>" alt="<?= pf_e($title) ?> website" loading="lazy">
                <?php elseif ($img && pf_file_kind($img) === 'video'): ?>
                    <video data-src="<?= pf_e($img) ?>" muted loop playsinline preload="none" class="pf-autoplay"></video>
                <?php elseif ($link !== ''): ?>
                    <?php /* No screenshot uploaded: show an automatic live screenshot of the website. */ ?>
                    <img src="https://s.wordpress.com/mshots/v1/<?= rawurlencode($link) ?>?w=1200&amp;h=1800"
                         alt="<?= pf_e($title) ?> website" loading="lazy" referrerpolicy="no-referrer"
                         onerror="this.hidden=true;this.nextElementSibling.hidden=false">
                    <span class="pf-placeholder" hidden><?= pf_e($domain) ?></span>
                <?php else: ?>
                    <span class="pf-placeholder"><?= pf_e($domain) ?></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="pf-web__body">
            <?php if ($tags): ?><div class="pf-tags"><?php foreach ($tags as $i => $t): ?><span class="pf-tag<?= $i % 2 ? ' pf-tag--gold' : '' ?>"><?= pf_e($t) ?></span><?php endforeach; ?></div><?php endif; ?>
            <?php if ($title !== '') : ?><h3><?= pf_e($title) ?></h3><?php endif; ?>
            <?php if (trim((string) ($item['description'] ?? '')) !== ''): ?><p><?= pf_e($item['description']) ?></p><?php endif; ?>
            <?php if ($link !== ''): ?><a class="pf-link" href="<?= pf_e($link) ?>" target="_blank" rel="noopener">View project <span aria-hidden="true">→</span></a><?php endif; ?>
        </div>
    </article>
    <?php
}

function render_seo_card(array $item, string $base, bool $hidden, bool $featured): void
{
    $img = pf_upload_url($item, $base);
    $link = pf_safe_url($item['media_link'] ?? '');
    $tags = pf_tags($item);
    $stats = [];
    foreach ([1, 2] as $n) {
        $v = trim((string) ($item["stat{$n}_value"] ?? ''));
        if ($v !== '') $stats[] = [$v, trim((string) ($item["stat{$n}_label"] ?? ''))];
    }
    $before = trim((string) ($item['rank_before'] ?? ''));
    $after = trim((string) ($item['rank_after'] ?? ''));
    ?>
    <article class="pf-card pf-seo<?= $featured ? ' pf-seo--dark' : '' ?><?= $hidden ? ' is-extra' : '' ?>">
        <div class="pf-seo__head">
            <h3><?= pf_e(pf_title($item, 'SEO project')) ?></h3>
            <?php if ($tags): ?><span class="pf-tag pf-tag--gold"><?= pf_e($tags[0]) ?></span><?php endif; ?>
        </div>
        <?php if (trim((string) ($item['keyword'] ?? '')) !== ''): ?><p class="pf-seo__kw">Keyword: “<?= pf_e($item['keyword']) ?>”</p><?php endif; ?>
        <?php if ($before !== '' || $after !== ''): ?>
            <div class="pf-seo__rank">
                <div><small>Before</small><b class="is-before">#<?= pf_e(ltrim($before, '#') ?: '–') ?></b></div>
                <span class="pf-seo__arrow" aria-hidden="true">→</span>
                <div><small>After</small><b>#<?= pf_e(ltrim($after, '#') ?: '–') ?></b></div>
            </div>
        <?php endif; ?>
        <?php if ($stats): ?>
            <div class="pf-seo__stats"><?php foreach ($stats as [$v, $l]): ?><div><b><?= pf_e($v) ?></b><small><?= pf_e($l) ?></small></div><?php endforeach; ?></div>
        <?php endif; ?>
        <?php if ($img && pf_file_kind($img) === 'image'): ?>
            <a class="pf-seo__shot" href="<?= pf_e($img) ?>" data-lightbox><img src="<?= pf_e($img) ?>" alt="Ranking report" loading="lazy"></a>
        <?php elseif ($img && pf_file_kind($img) === 'video'): ?>
            <div class="pf-seo__shot"><video data-src="<?= pf_e($img) ?>" muted loop playsinline preload="none" class="pf-autoplay"></video></div>
        <?php endif; ?>
        <?php if ($link !== ''): ?><a class="pf-link" href="<?= pf_e($link) ?>" target="_blank" rel="noopener">View project <span aria-hidden="true">→</span></a><?php endif; ?>
    </article>
    <?php
}

function render_reel_card(array $item, string $base, bool $hidden, int $index, string $fallbackTitle): void
{
    $file = pf_upload_url($item, $base);
    $fileKind = $file ? pf_file_kind($file) : '';
    $link = pf_safe_url($item['media_link'] ?? '');
    $video = pf_video_info($link);
    $badge = $video['label'] ?: 'Reel';
    $title = pf_title($item, $fallbackTitle);
    $views = trim((string) ($item['stat1_value'] ?? ''));
    $reach = trim((string) ($item['stat2_value'] ?? ''));
    ?>
    <article class="pf-reel<?= $index % 2 ? ' pf-reel--alt' : '' ?><?= $hidden ? ' is-extra' : '' ?>">
        <div class="pf-reel__media">
            <span class="pf-reel__badge pf-badge--<?= pf_e($video['platform'] ?: 'reel') ?>"><?= pf_e($badge) ?></span>
            <?php if ($fileKind === 'video'): ?>
                <video data-src="<?= pf_e($file) ?>" muted loop playsinline preload="none" class="pf-autoplay"></video>
            <?php elseif ($video['embed'] !== ''): ?>
                <iframe data-src="<?= pf_e($video['embed']) ?>" title="<?= pf_e($title) ?>" class="pf-embed pf-embed--<?= pf_e($video['platform']) ?>"
                        allow="autoplay; encrypted-media; picture-in-picture" loading="lazy" scrolling="no" frameborder="0" tabindex="-1"></iframe>
            <?php elseif ($fileKind === 'image'): ?>
                <img src="<?= pf_e($file) ?>" alt="<?= pf_e($title) ?>" loading="lazy">
            <?php else: ?>
                <span class="pf-placeholder">Video</span>
            <?php endif; ?>
            <?php if ($link !== ''): ?>
                <a class="pf-reel__hit" href="<?= pf_e($link) ?>" target="_blank" rel="noopener" aria-label="Watch <?= pf_e($title ?: "this video") ?> on <?= pf_e($video['label'] ?: 'the original site') ?>">
                    <span class="pf-reel__play" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></span>
                </a>
            <?php endif; ?>
            <?php if ($views !== '' || $reach !== ''): ?>
                <div class="pf-reel__stats">
                    <div><b><?= pf_e($views ?: '–') ?></b><small><?= pf_e(trim((string) ($item['stat1_label'] ?? '')) ?: 'Views') ?></small></div>
                    <div class="is-right"><b><?= pf_e($reach ?: '–') ?></b><small><?= pf_e(trim((string) ($item['stat2_label'] ?? '')) ?: 'Reach') ?></small></div>
                </div>
            <?php endif; ?>
        </div>
        <?php if ($title !== '') : ?><h3><?= pf_e($title) ?></h3><?php endif; ?>
        <?php if (trim((string) ($item['description'] ?? '')) !== ''): ?><p><?= pf_e($item['description']) ?></p><?php endif; ?>
    </article>
    <?php
}

function render_gallery_card(array $item, string $base, bool $hidden, int $index, array $cat): void
{
    $file = pf_upload_url($item, $base);
    $kind = $file ? pf_file_kind($file) : '';
    $link = pf_safe_url($item['media_link'] ?? '');
    $embed = $kind === '' ? pf_video_info($link) : ['platform' => '', 'embed' => ''];
    $title = pf_title($item, $cat['tab']);
    $sub = trim((string) ($item['description'] ?? ''));
    $tones = ['dark', 'cream', 'green', 'sand', 'cream', 'dark', 'sand', 'green'];
    $tone = $tones[$index % count($tones)];
    // Click: open the link if one is set, otherwise enlarge the image.
    $href = $link !== '' ? $link : ($kind === 'image' || $kind === 'pdf' ? $file : '');
    $tag = $href !== '' ? 'a' : 'div';
    $attrs = '';
    if ($href !== '') {
        $attrs = ' href="' . pf_e($href) . '"';
        $attrs .= ($link === '' && $kind === 'image') ? ' data-lightbox' : ' target="_blank" rel="noopener"';
    }
    ?>
    <<?= $tag ?> class="pf-poster pf-tone--<?= $tone ?><?= $hidden ? ' is-extra' : '' ?>"<?= $attrs ?>
        style="--ar: <?= pf_e($cat['aspect'] ?? '4 / 5') ?>; --fit: <?= pf_e($cat['fit'] ?? 'cover') ?>">
        <div class="pf-poster__media">
            <?php if ($kind === 'image'): ?>
                <img src="<?= pf_e($file) ?>" alt="<?= pf_e($title) ?>" loading="lazy">
            <?php elseif ($kind === 'video'): ?>
                <video data-src="<?= pf_e($file) ?>" muted loop playsinline preload="none" class="pf-autoplay"></video>
            <?php elseif ($kind === 'pdf'): ?>
                <span class="pf-placeholder pf-placeholder--pdf">PDF<br><small>Tap to view</small></span>
            <?php elseif ($embed['embed'] !== ''): ?>
                <?php /* No file uploaded: show the Instagram / YouTube / Facebook post itself from the link. */ ?>
                <iframe data-src="<?= pf_e($embed['embed']) ?>" title="<?= pf_e($title) ?>" class="pf-embed pf-embed--<?= pf_e($embed['platform']) ?>"
                        loading="lazy" scrolling="no" frameborder="0" tabindex="-1"></iframe>
            <?php else: ?>
                <span class="pf-placeholder"><?= pf_e($cat['tab']) ?></span>
            <?php endif; ?>
        </div>
        <div class="pf-poster__cap">
            <b><?= pf_e($title) ?></b>
            <?php if ($sub !== ''): ?><small><?= pf_e($sub) ?></small><?php endif; ?>
        </div>
    </<?= $tag ?>>
    <?php
}

include __DIR__ . '/header.php';
?>

<main class="pf" id="portfolio-page">

    <!-- ============ HERO ============ -->
    <section class="pf-hero">
        <div class="pf-wrap">
            <p class="pf-eyebrow">Our Portfolio</p>
            <h1>Work that helps brands <em>shine</em>.</h1>
            <p class="pf-hero__lead">Websites, SEO, reels and creative posters we've built for businesses across Vizag and beyond — real projects, real results.</p>
            <?php if ($heroStats): ?>
                <div class="pf-hero__stats">
                    <?php foreach ($heroStats as $s): ?>
                        <div class="pf-stat"><b data-count="<?= (int) $s['value'] ?>"><?= (int) $s['value'] ?><?= pf_e($s['suffix']) ?></b><span><?= pf_e($s['label']) ?></span></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ============ FILTER BAR (every service, even ones with no work yet) ============ -->
    <div class="pf-filter-wrap">
        <!-- Mobile: services as a dropdown -->
        <label class="pf-filter-select">
            <span class="pf-filter-select__label">Select service</span>
            <select id="pfFilterSelect" aria-label="Select service">
                <option value="all">All Work (<?= array_sum(array_map('count', $grouped)) ?>)</option>
                <?php foreach ($grouped as $key => $items): ?>
                    <option value="<?= pf_e($key) ?>"><?= pf_e($categories[$key]['tab']) ?> (<?= count($items) ?>)</option>
                <?php endforeach; ?>
            </select>
        </label>
        <div class="pf-filter" role="navigation" aria-label="Filter portfolio">
            <button type="button" class="is-active" data-filter="all">All Work <span class="pf-count"><?= array_sum(array_map('count', $grouped)) ?></span></button>
            <?php foreach ($grouped as $key => $items): ?>
                <button type="button" data-filter="<?= pf_e($key) ?>"><?= pf_e($categories[$key]['tab']) ?> <span class="pf-count"><?= count($items) ?></span></button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ============ SECTIONS ============ -->
    <div class="pf-sections">
        <?php if ($loadError): ?>
            <p class="pf-empty">Unable to load our work right now. Please try again shortly.</p>
        <?php elseif (!$visible): ?>
            <p class="pf-empty pf-empty--all">Our portfolio is being updated — check back soon.</p>
        <?php endif; ?>

        <?php $n = 0; foreach ($grouped as $key => $items):
            $cat = $categories[$key];
            $total = count($items);
            if ($total > 0) $n++;
            $preview = (int) ($cat['preview'] ?? 4);
            $layout = $cat['layout'];
            $gridClass = ['website' => 'pf-grid--3', 'seo' => 'pf-grid--3', 'reel' => 'pf-grid--4', 'gallery' => 'pf-grid--4'][$layout];
            ?>
            <section class="pf-section<?= $total ? '' : ' is-empty' ?>" id="<?= pf_e($key) ?>" data-section="<?= pf_e($key) ?>">
                <div class="pf-wrap">
                    <div class="pf-section__head">
                        <div>
                            <p class="pf-eyebrow"><?= $total ? sprintf('%02d', $n) . ' — ' : '' ?><?= pf_e($cat['eyebrow']) ?></p>
                            <h2><?= pf_e($cat['heading']) ?></h2>
                        </div>
                        <?php if ($cat['intro'] !== ''): ?><p class="pf-section__intro"><?= pf_e($cat['intro']) ?></p><?php endif; ?>
                    </div>

                    <?php if (!$total): ?>
                        <div class="pf-soon">
                            <b>New <?= pf_e(strtolower($cat['tab'])) ?> coming soon</b>
                            <span>We're adding fresh work here. Want yours to be next?</span>
                            <a class="pf-btn pf-btn--gold" href="https://api.whatsapp.com/send?phone=919642343434" target="_blank" rel="noopener">Let's talk</a>
                        </div>
                    <?php else: ?>
                    <div class="pf-grid <?= $gridClass ?>">
                        <?php foreach ($items as $i => $item) {
                            $hidden = $i >= $preview;
                            if ($layout === 'website') render_website_card($item, $uploadBase, $hidden);
                            elseif ($layout === 'seo') render_seo_card($item, $uploadBase, $hidden, $i === 0);
                            elseif ($layout === 'reel') render_reel_card($item, $uploadBase, $hidden, $i, '');
                            else render_gallery_card($item, $uploadBase, $hidden, $i, $cat);
                        } ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($total > $preview): ?>
                        <div class="pf-more">
                            <button type="button" class="pf-more__btn" data-more="<?= pf_e($key) ?>"
                                    data-label-more="View all <?= pf_e(strtolower($cat['tab'])) ?> (<?= $total ?>)" data-label-less="Show less">
                                <span>View all <?= pf_e(strtolower($cat['tab'])) ?> (<?= $total ?>)</span> <i aria-hidden="true">→</i>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        <?php endforeach; ?>
    </div>

    <!-- ============ CTA ============ -->
    <section class="pf-cta-wrap">
        <div class="pf-cta">
            <div>
                <h2>Your brand could be our next <em>success story</em>.</h2>
                <p>We're here to help you shine.</p>
            </div>
            <div class="pf-cta__actions">
                <a class="pf-btn pf-btn--gold" href="tel:+919642343434">Call 9642 34 34 34</a>
                <a class="pf-btn pf-btn--ghost" href="mailto:admin@bhavicreations.com">Email us</a>
            </div>
        </div>
    </section>
</main>

<!-- Image lightbox -->
<div class="pf-lightbox" id="pfLightbox" hidden>
    <button type="button" class="pf-lightbox__close" aria-label="Close">×</button>
    <img alt="">
</div>

<script>
(function () {
    var page = document.getElementById('portfolio-page');
    if (!page) return;

    /* ---- Lazy-load + autoplay videos / embeds only while visible ---- */
    var media = [].slice.call(page.querySelectorAll('video[data-src], iframe[data-src]'));
    function load(el) {
        if (el.dataset.loaded) return;
        el.dataset.loaded = '1';
        el.src = el.dataset.src;
        if (el.tagName === 'VIDEO') { el.muted = true; el.load(); }
    }
    function isShown(el) { return el.offsetParent !== null; }
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                var el = e.target;
                if (e.isIntersecting && isShown(el)) {
                    load(el);
                    if (el.tagName === 'VIDEO') { var p = el.play(); if (p && p.catch) p.catch(function () {}); }
                } else if (el.tagName === 'VIDEO' && !el.paused) {
                    el.pause();
                }
            });
        }, { rootMargin: '200px 0px' });
        media.forEach(function (el) { io.observe(el); });
    } else {
        media.forEach(function (el) { load(el); if (el.tagName === 'VIDEO') el.play(); });
    }
    function refreshMedia() { // re-check items that just became visible
        if (!io) return;
        media.forEach(function (el) { io.unobserve(el); io.observe(el); });
    }

    /* ---- "View all" buttons ---- */
    page.querySelectorAll('[data-more]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var section = document.getElementById(btn.dataset.more);
            var open = section.classList.toggle('is-expanded');
            btn.querySelector('span').textContent = open ? btn.dataset.labelLess : btn.dataset.labelMore;
            btn.classList.toggle('is-open', open);
            if (!open) section.scrollIntoView({ behavior: 'smooth', block: 'start' });
            refreshMedia();
        });
    });

    /* ---- Filter tabs (All Work / Websites / SEO / ...) ---- */
    var buttons = [].slice.call(page.querySelectorAll('[data-filter]'));
    var sections = [].slice.call(page.querySelectorAll('.pf-section'));
    var select = document.getElementById('pfFilterSelect');
    function applyFilter(key, scroll) {
        if (key !== 'all' && !document.getElementById(key)) key = 'all';
        buttons.forEach(function (b) { b.classList.toggle('is-active', b.dataset.filter === key); });
        if (select) select.value = key;
        sections.forEach(function (s) {
            var match = key === 'all' || s.dataset.section === key;
            s.hidden = !match;
            // A single category shows every project; "All Work" shows the preview.
            s.classList.toggle('is-filtered', key !== 'all' && match);
        });
        if (scroll) {
            var bar = page.querySelector('.pf-filter-wrap');
            window.scrollTo({ top: bar.getBoundingClientRect().top + window.scrollY - 90, behavior: 'smooth' });
        }
        refreshMedia();
    }
    function pick(key) {
        history.replaceState(null, '', key === 'all' ? location.pathname + location.search : '#' + key);
        applyFilter(key, true);
    }
    buttons.forEach(function (b) {
        b.addEventListener('click', function () { pick(b.dataset.filter); });
    });
    if (select) select.addEventListener('change', function () { pick(select.value); });
    if (location.hash.length > 1) applyFilter(location.hash.slice(1), false);
    window.addEventListener('hashchange', function () { applyFilter(location.hash.slice(1) || 'all', false); });

    /* ---- Lightbox for images without a link ---- */
    var lb = document.getElementById('pfLightbox');
    var lbImg = lb.querySelector('img');
    page.addEventListener('click', function (e) {
        var a = e.target.closest('[data-lightbox]');
        if (!a) return;
        e.preventDefault();
        lbImg.src = a.getAttribute('href');
        lbImg.alt = (a.querySelector('img') || {}).alt || '';
        lb.hidden = false;
        document.body.style.overflow = 'hidden';
    });
    function closeLb() { lb.hidden = true; lbImg.src = ''; document.body.style.overflow = ''; }
    lb.addEventListener('click', function (e) { if (e.target !== lbImg) closeLb(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !lb.hidden) closeLb(); });
})();
</script>

<?php include __DIR__ . '/chatbot.php'; ?>
<?php include __DIR__ . '/footer.php'; ?>
