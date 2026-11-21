<?php
/**
 * Shared portfolio helpers — used by our_works.php (frontend) and the admin
 * "Our Works" pages. One place for categories, schema upgrades and the
 * social-video (Instagram / Facebook / YouTube / Vimeo) link handling.
 */

if (defined('BHAVI_PORTFOLIO_LIB')) return;
define('BHAVI_PORTFOLIO_LIB', true);

/**
 * Portfolio categories, in the order they appear on the page.
 * `type`   = value stored in our_works.media_type (keep existing values).
 * `layout` = website | seo | reel | gallery
 * `aspect` = gallery card ratio, `fit` = cover | contain
 * `preview`= cards shown before "View all".
 * Add a new service here and it appears in admin + website automatically.
 */
function portfolio_categories(): array
{
    return [
        'websites' => ['type' => 'Website', 'tab' => 'Websites', 'layout' => 'website', 'preview' => 3,
            'eyebrow' => 'Websites', 'heading' => 'Websites that convert',
            'intro' => 'Fast, mobile-first business websites designed to turn visitors into calls and enquiries.'],
        'seo' => ['type' => 'SEO', 'tab' => 'SEO', 'layout' => 'seo', 'preview' => 3,
            'eyebrow' => 'SEO & Google Business', 'heading' => 'Ranking results',
            'intro' => 'From page 5 to the top of Google — keyword rankings, traffic and GMB calls we\'ve grown.'],
        'reels' => ['type' => 'Reels', 'tab' => 'Reels', 'layout' => 'reel', 'preview' => 4,
            'eyebrow' => 'Reels & Reach', 'heading' => 'Reels people actually watch',
            'intro' => 'Scripted, shot and edited in-house — Telugu & English reels built for reach on Instagram and YouTube Shorts.'],
        'posters' => ['type' => 'Posters', 'tab' => 'Creative Posters', 'layout' => 'gallery', 'aspect' => '4 / 5', 'fit' => 'cover', 'preview' => 8,
            'eyebrow' => 'Creative Posters', 'heading' => 'Posters that stop the scroll',
            'intro' => 'Social media posts, hiring ads, festival greetings and offer creatives — on-brand, every time.'],
        'logo' => ['type' => 'Logo', 'tab' => 'Logos', 'layout' => 'gallery', 'aspect' => '1 / 1', 'fit' => 'contain', 'preview' => 8,
            'eyebrow' => 'Logo Design', 'heading' => 'Logos with character',
            'intro' => 'Brand marks that are simple, memorable and work everywhere — from signboards to social profiles.'],
        'photoshoot' => ['type' => 'Photo Shoot', 'tab' => 'Shoot', 'layout' => 'gallery', 'aspect' => '4 / 5', 'fit' => 'cover', 'preview' => 8,
            'eyebrow' => 'Shoots', 'heading' => 'Moments, beautifully captured',
            'intro' => 'Product, team and location shoots that give your brand real, original visuals.'],
        'videos' => ['type' => 'Videos', 'tab' => 'Videos', 'layout' => 'reel', 'preview' => 4,
            'eyebrow' => 'Brand Videos', 'heading' => 'Videos that tell your story',
            'intro' => 'Brand films, promos and explainers — planned, shot and edited by our team.'],
        'testimonials' => ['type' => 'Testimonials', 'tab' => 'Testimonials', 'layout' => 'reel', 'preview' => 4,
            'eyebrow' => 'Testimonials', 'heading' => 'Hear it from our clients',
            'intro' => 'Real clients, real words — video testimonials from businesses we\'ve helped grow.'],
        'animatedvideos' => ['type' => 'Animated Videos', 'tab' => 'Animated Videos', 'layout' => 'reel', 'preview' => 4,
            'eyebrow' => 'Animated Videos', 'heading' => 'Motion that explains',
            'intro' => 'Animated explainers and motion graphics that make complex ideas easy to follow.'],
        'visitingcards' => ['type' => 'Visiting Cards', 'tab' => 'Visiting Cards', 'layout' => 'gallery', 'aspect' => '16 / 10', 'fit' => 'contain', 'preview' => 8,
            'eyebrow' => 'Visiting Cards', 'heading' => 'First impressions, in your pocket',
            'intro' => 'Business cards designed to look sharp and print perfectly.'],
        'pamphlets' => ['type' => 'Pamphlets', 'tab' => 'Pamphlets', 'layout' => 'gallery', 'aspect' => '4 / 5', 'fit' => 'contain', 'preview' => 8,
            'eyebrow' => 'Pamphlets & Flyers', 'heading' => 'Print that gets picked up',
            'intro' => 'Pamphlets and flyers for launches, offers and events — clear, bold and print-ready.'],
        'brochures' => ['type' => 'Brochures', 'tab' => 'Brochures', 'layout' => 'gallery', 'aspect' => '4 / 5', 'fit' => 'contain', 'preview' => 8,
            'eyebrow' => 'Brochures', 'heading' => 'Brochures that sell',
            'intro' => 'Company profiles, product catalogues and service brochures — designed to be kept.'],
        'hoardings' => ['type' => 'Hoardings', 'tab' => 'Hoardings', 'layout' => 'gallery', 'aspect' => '16 / 9', 'fit' => 'contain', 'preview' => 8,
            'eyebrow' => 'Hoardings', 'heading' => 'Big ideas, bigger formats',
            'intro' => 'Outdoor hoardings and banners designed to be read in seconds from the road.'],
    ];
}

/** Admin dropdown values: media_type => label. */
function portfolio_media_types(): array
{
    $types = [];
    foreach (portfolio_categories() as $key => $cat) $types[$cat['type']] = $cat;
    return $types;
}

/** Layout for a stored media_type (falls back to gallery for unknown types). */
function portfolio_layout_for(string $mediaType): string
{
    foreach (portfolio_categories() as $cat) {
        if (strcasecmp($cat['type'], trim($mediaType)) === 0) return $cat['layout'];
    }
    return 'gallery';
}

/**
 * Adds the new portfolio columns to `our_works` if they are missing.
 * Safe to call on every admin request (one SHOW COLUMNS query).
 */
function portfolio_ensure_schema(mysqli $conn): void
{
    $wanted = [
        'description'  => 'TEXT NULL',
        'tags'         => 'VARCHAR(255) NULL',
        'keyword'      => 'VARCHAR(255) NULL',
        'rank_before'  => 'VARCHAR(50) NULL',
        'rank_after'   => 'VARCHAR(50) NULL',
        'stat1_value'  => 'VARCHAR(50) NULL',
        'stat1_label'  => 'VARCHAR(100) NULL',
        'stat2_value'  => 'VARCHAR(50) NULL',
        'stat2_label'  => 'VARCHAR(100) NULL',
        'sort_order'   => 'INT NOT NULL DEFAULT 0',
    ];
    $have = [];
    $res = $conn->query('SHOW COLUMNS FROM our_works');
    if (!$res) return;
    while ($row = $res->fetch_assoc()) $have[strtolower($row['Field'])] = true;
    foreach ($wanted as $col => $def) {
        if (!isset($have[$col])) $conn->query("ALTER TABLE our_works ADD COLUMN `$col` $def");
    }
}

/** Escape for HTML output. */
function pf_e($value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/** Returns the URL if it is a valid http(s) URL, otherwise ''. */
function pf_safe_url($url): string
{
    $url = trim((string) ($url ?? ''));
    if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) return '';
    $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
    return in_array($scheme, ['http', 'https'], true) ? $url : '';
}

/** "https://www.ialign-dental.com/x" -> "ialign-dental.com" */
function pf_domain(string $url): string
{
    $host = (string) parse_url($url, PHP_URL_HOST);
    return preg_replace('/^www\./i', '', $host);
}

/**
 * Works out how to play a social video link.
 * Returns ['platform' => instagram|youtube|facebook|vimeo|'', 'label' => 'Instagram',
 *          'embed' => iframe src or '', 'autoplay' => bool]
 */
function pf_video_info(string $url): array
{
    $url = pf_safe_url($url);
    $info = ['platform' => '', 'label' => '', 'embed' => '', 'autoplay' => false];
    if ($url === '') return $info;
    $host = strtolower(preg_replace('/^(www\.|m\.)/i', '', (string) parse_url($url, PHP_URL_HOST)));

    // YouTube: watch?v=, youtu.be/, shorts/, embed/, live/
    if (preg_match('~(?:youtube\.com/(?:watch\?(?:.*&)?v=|shorts/|embed/|live/|v/)|youtu\.be/)([A-Za-z0-9_-]{11})~i', $url, $m)) {
        $id = $m[1];
        return ['platform' => 'youtube', 'label' => 'YouTube', 'autoplay' => true,
            'embed' => "https://www.youtube.com/embed/$id?autoplay=1&mute=1&loop=1&playlist=$id&controls=0&playsinline=1&modestbranding=1&rel=0&iv_load_policy=3"];
    }
    // Instagram: /reel/ID, /reels/ID, /p/ID, /tv/ID
    if (strpos($host, 'instagram.com') !== false || strpos($host, 'instagr.am') !== false) {
        if (preg_match('~/(reel|reels|p|tv)/([A-Za-z0-9_-]+)~i', $url, $m)) {
            $kind = strtolower($m[1]) === 'p' ? 'p' : 'reel';
            return ['platform' => 'instagram', 'label' => 'Instagram', 'autoplay' => false,
                'embed' => "https://www.instagram.com/$kind/{$m[2]}/embed/"];
        }
        return ['platform' => 'instagram', 'label' => 'Instagram', 'embed' => '', 'autoplay' => false];
    }
    // Facebook: any video / reel / watch URL (fb.watch too)
    if (strpos($host, 'facebook.com') !== false || $host === 'fb.watch' || strpos($host, 'fb.com') !== false) {
        return ['platform' => 'facebook', 'label' => 'Facebook', 'autoplay' => true,
            'embed' => 'https://www.facebook.com/plugins/video.php?href=' . rawurlencode($url) . '&show_text=false&autoplay=true&mute=true&loop=true&width=560'];
    }
    // Vimeo
    if (strpos($host, 'vimeo.com') !== false && preg_match('~vimeo\.com/(?:video/)?(\d+)~i', $url, $m)) {
        return ['platform' => 'vimeo', 'label' => 'Vimeo', 'autoplay' => true,
            'embed' => "https://player.vimeo.com/video/{$m[1]}?autoplay=1&muted=1&loop=1&background=1"];
    }
    return $info;
}

/** File extension groups. */
function pf_file_kind(string $file): string
{
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'avif'], true)) return 'image';
    if (in_array($ext, ['mp4', 'webm', 'mov', 'm4v'], true)) return 'video';
    if ($ext === 'pdf') return 'pdf';
    return $file === '' ? '' : 'other';
}

/** Allowed upload extensions (admin). */
function pf_allowed_ext(): array
{
    return ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg', 'avif', 'mp4', 'mov', 'm4v', 'webm', 'pdf'];
}

/**
 * Insert (no $existing) or update a work from $_POST / $_FILES['media_file'].
 * $uploadDir is relative to the calling admin script, e.g. 'uploads/staff/'.
 * Returns [bool ok, string message].
 */
function portfolio_save_work(mysqli $conn, ?array $existing, string $uploadDir): array
{
    $text = static fn(string $k, int $max = 255) => mb_substr(trim((string) ($_POST[$k] ?? '')), 0, $max);

    $data = [
        'media_type'  => $text('media_type', 100),
        'title'       => $text('title'),
        'media_link'  => $text('media_link', 500),
        'description' => $text('description', 1000),
        'tags'        => $text('tags'),
        'keyword'     => $text('keyword'),
        'rank_before' => $text('rank_before', 50),
        'rank_after'  => $text('rank_after', 50),
        'stat1_value' => $text('stat1_value', 50),
        'stat1_label' => $text('stat1_label', 100),
        'stat2_value' => $text('stat2_value', 50),
        'stat2_label' => $text('stat2_label', 100),
    ];
    $sortOrder = (int) ($_POST['sort_order'] ?? 0);

    if ($data['media_type'] === '') return [false, 'Please select a portfolio section.'];
    if ($data['media_link'] !== '' && pf_safe_url($data['media_link']) === '') return [false, 'The link must start with http:// or https://'];

    $oldFile = $existing['file_path'] ?? '';
    $newFile = $oldFile;
    $deleteOld = false;

    if (!empty($_POST['remove_file'])) {
        $newFile = '';
        $deleteOld = true;
    }

    $upload = $_FILES['media_file'] ?? null;
    if ($upload && $upload['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($upload['error'] !== UPLOAD_ERR_OK) {
            return [false, 'Upload failed (error code ' . (int) $upload['error'] . '). The file may be larger than the server limit (' . ini_get('upload_max_filesize') . ').'];
        }
        $ext = strtolower(pathinfo($upload['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, pf_allowed_ext(), true)) return [false, 'Invalid file type. Allowed: ' . implode(', ', pf_allowed_ext())];
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $name = uniqid('work_') . '.' . $ext;
        if (!move_uploaded_file($upload['tmp_name'], $uploadDir . $name)) return [false, 'Could not save the uploaded file.'];
        $newFile = $name;
        $deleteOld = true;
    }

    if ($newFile === '' && $data['media_link'] === '') return [false, 'Please upload a file or add a link.'];

    $cols = array_keys($data);
    $values = array_values($data);
    $values[] = $newFile;
    $values[] = $sortOrder;

    if ($existing) {
        $set = implode(', ', array_map(static fn($c) => "`$c` = ?", $cols)) . ', file_path = ?, sort_order = ?';
        $stmt = $conn->prepare("UPDATE our_works SET $set WHERE id = ?");
        $values[] = (int) $existing['id'];
        $stmt->bind_param(str_repeat('s', count($cols) + 1) . 'ii', ...$values);
    } else {
        $stmt = $conn->prepare('INSERT INTO our_works (`' . implode('`, `', $cols) . '`, file_path, sort_order) VALUES (' . str_repeat('?, ', count($cols) + 1) . '?)');
        $stmt->bind_param(str_repeat('s', count($cols) + 1) . 'i', ...$values);
    }

    if (!$stmt->execute()) return [false, 'Database error: ' . $stmt->error];

    if ($deleteOld && $oldFile !== '' && $oldFile !== $newFile) {
        $oldPath = $uploadDir . basename($oldFile);
        if (is_file($oldPath)) @unlink($oldPath);
    }
    return [true, $existing ? 'Work updated successfully.' : 'Work added successfully.'];
}
