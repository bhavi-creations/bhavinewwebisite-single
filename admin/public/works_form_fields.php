<?php
/**
 * Shared form fields for "Upload Work" and "Edit Work".
 * Expects $work (array; empty for new) and portfolio_lib.php loaded.
 * Fields are shown/hidden by media type (website / seo / reel / gallery).
 */
$work = $work ?? [];
$v = static fn(string $k) => htmlspecialchars((string) ($work[$k] ?? ''), ENT_QUOTES, 'UTF-8');
$isEdit = !empty($work['id']);
?>
<style>
    .pf-admin .form-group label { font-weight: 600; color: #3a3b45; }
    .pf-admin .hint { font-size: 12px; color: #858796; margin-top: 4px; }
    .pf-admin .pf-box { border: 1px solid #e3e6f0; border-radius: 8px; padding: 16px 16px 4px; margin-bottom: 16px; background: #fafbfd; }
    .pf-admin .pf-box h6 { font-weight: 700; color: #1cc88a; margin-bottom: 12px; }
    .pf-admin [data-show] { display: none; }
    #pfLinkInfo { font-size: 13px; margin-top: 6px; }
</style>

<div class="pf-admin">
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="mediaType">Portfolio section / service *</label>
            <select class="form-control" name="media_type" id="mediaType" required>
                <option value="">-- Select --</option>
                <?php
                $current = (string) ($work['media_type'] ?? '');
                $known = false;
                foreach (portfolio_media_types() as $type => $cat) {
                    $sel = strcasecmp($current, $type) === 0 ? ' selected' : '';
                    if ($sel) $known = true;
                    echo '<option value="' . htmlspecialchars($type) . '" data-layout="' . $cat['layout'] . '"' . $sel . '>' . htmlspecialchars($cat['tab']) . '</option>';
                }
                if ($current !== '' && !$known) echo '<option value="' . htmlspecialchars($current) . '" data-layout="gallery" selected>' . htmlspecialchars($current) . '</option>';
                ?>
            </select>
        </div>
        <div class="form-group col-md-6">
            <label for="title">Title / Client name</label>
            <input type="text" class="form-control" name="title" id="title" value="<?= $v('title') ?>" maxlength="255"
                   placeholder="e.g. IALiGN Signature Dental Care">
            <div class="hint">Shown as the card heading. If empty, the website domain is used.</div>
        </div>
    </div>

    <div class="form-group">
        <label for="mediaLink" id="mediaLinkLabel">Link</label>
        <input type="url" class="form-control" name="media_link" id="mediaLink" value="<?= $v('media_link') ?>" placeholder="https://">
        <div class="hint" id="mediaLinkHint">Where the card goes when clicked.</div>
        <div id="pfLinkInfo"></div>
    </div>

    <div class="form-group">
        <label for="mediaFile" id="mediaFileLabel">Upload file</label>
        <?php if ($isEdit && !empty($work['file_path'])): ?>
            <div class="mb-2">
                <?php
                $path = 'uploads/staff/' . rawurlencode(basename($work['file_path']));
                $kind = pf_file_kind($work['file_path']);
                if ($kind === 'image') echo "<img src='$path' style='max-width:200px;max-height:200px;border:1px solid #ddd;padding:4px;border-radius:6px'>";
                elseif ($kind === 'video') echo "<video src='$path' muted loop autoplay playsinline style='max-width:160px;max-height:240px;border-radius:6px'></video>";
                elseif ($kind === 'pdf') echo "<a href='$path' target='_blank' class='btn btn-sm btn-outline-info'>View current PDF</a>";
                ?>
                <div class="custom-control custom-checkbox mt-2">
                    <input type="checkbox" class="custom-control-input" id="removeFile" name="remove_file" value="1">
                    <label class="custom-control-label" for="removeFile" style="font-weight:400">Remove current file</label>
                </div>
            </div>
        <?php endif; ?>
        <input type="file" class="form-control-file" name="media_file" id="mediaFile" accept="image/*,video/mp4,video/webm,video/quicktime,.pdf">
        <div class="hint" id="mediaFileHint">Image, video (MP4/WebM) or PDF.</div>
    </div>

    <!-- Website + gallery + reel: tags & description -->
    <div class="form-row">
        <div class="form-group col-md-6" data-show="website seo gallery">
            <label for="tags">Tags</label>
            <input type="text" class="form-control" name="tags" id="tags" value="<?= $v('tags') ?>" maxlength="255" placeholder="Healthcare, Website + SEO">
            <div class="hint">Comma separated. Website cards show all; SEO cards show the first one (e.g. "Local SEO", "GMB").</div>
        </div>
        <div class="form-group col-md-6" data-show="website reel gallery">
            <label for="description" id="descLabel">Short description</label>
            <input type="text" class="form-control" name="description" id="description" value="<?= $v('description') ?>" maxlength="300">
            <div class="hint" id="descHint"></div>
        </div>
    </div>

    <!-- SEO -->
    <div class="pf-box" data-show="seo">
        <h6>SEO / Google Business results</h6>
        <div class="form-row">
            <div class="form-group col-md-6"><label>Keyword</label>
                <input type="text" class="form-control" name="keyword" value="<?= $v('keyword') ?>" placeholder="best dentist in Gajuwaka"></div>
            <div class="form-group col-md-3"><label>Rank before</label>
                <input type="text" class="form-control" name="rank_before" value="<?= $v('rank_before') ?>" placeholder="48"></div>
            <div class="form-group col-md-3"><label>Rank after</label>
                <input type="text" class="form-control" name="rank_after" value="<?= $v('rank_after') ?>" placeholder="1"></div>
        </div>
    </div>

    <!-- Stats (SEO + Reels) -->
    <div class="pf-box" data-show="seo reel">
        <h6 id="statsTitle">Numbers</h6>
        <div class="form-row">
            <div class="form-group col-md-3"><label>Stat 1 value</label>
                <input type="text" class="form-control" name="stat1_value" value="<?= $v('stat1_value') ?>" placeholder="+120%"></div>
            <div class="form-group col-md-3"><label>Stat 1 label</label>
                <input type="text" class="form-control" name="stat1_label" id="stat1Label" value="<?= $v('stat1_label') ?>"></div>
            <div class="form-group col-md-3"><label>Stat 2 value</label>
                <input type="text" class="form-control" name="stat2_value" value="<?= $v('stat2_value') ?>" placeholder="+45"></div>
            <div class="form-group col-md-3"><label>Stat 2 label</label>
                <input type="text" class="form-control" name="stat2_label" id="stat2Label" value="<?= $v('stat2_label') ?>"></div>
        </div>
    </div>

    <div class="form-group" style="max-width:240px">
        <label for="sortOrder">Priority</label>
        <input type="number" class="form-control" name="sort_order" id="sortOrder" value="<?= (int) ($work['sort_order'] ?? 0) ?>">
        <div class="hint">Higher numbers show first. Leave 0 for newest-first.</div>
    </div>
</div>

<script>
(function () {
    var type = document.getElementById('mediaType');
    var link = document.getElementById('mediaLink');
    var info = document.getElementById('pfLinkInfo');
    var file = document.getElementById('mediaFile');
    var copy = {
        website: { link: 'Website URL *', linkHint: '"View project" opens this link.', file: 'Website screenshot (optional)', fileHint: 'Leave empty to show the website automatically from the URL. Or upload a full-page screenshot (it scrolls on hover).',
                   desc: 'One line on what was built', descHint: 'e.g. Appointment booking, treatments pages.' },
        seo:     { link: 'Project link (optional)', linkHint: 'Optional — client website or Google Maps profile.', file: 'Search Console / GMB screenshot', fileHint: 'Graph or insights screenshot.',
                   stats: 'Results (e.g. +120% / Organic traffic, +45 / GMB calls per month)', s1: 'Organic traffic', s2: 'GMB calls / month' },
        reel:    { link: 'Instagram / YouTube / Facebook link', linkHint: 'Paste the reel/video link. Clicking the card opens it on that platform.', file: 'Video file (MP4) — optional',
                   fileHint: 'Upload the MP4 for guaranteed autoplay + loop (needed for Instagram — Instagram does not allow autoplay embeds). YouTube, Facebook & Vimeo links autoplay without a file.',
                   desc: 'Client · Service', descHint: 'e.g. IALiGN Dental · Shoot + edit', stats: 'Views & reach (optional)', s1: 'Views', s2: 'Reach' },
        gallery: { link: 'Link (optional)', linkHint: 'If set, clicking the card opens this link. An Instagram post link alone also works — the post is shown on the card. If empty, the image opens full screen.', file: 'Image / PDF', fileHint: 'JPG, PNG, WebP or PDF. Optional if you added an Instagram / YouTube / Facebook link.',
                   desc: 'Subtitle', descHint: 'e.g. Social media post, Hiring ad – Field sales, Festival greeting.' }
    };
    function layout() { var o = type.options[type.selectedIndex]; return (o && o.dataset.layout) || 'gallery'; }
    function set(id, text) { var el = document.getElementById(id); if (el && text !== undefined) el.textContent = text; }
    function update() {
        var l = layout(), c = copy[l];
        document.querySelectorAll('.pf-admin [data-show]').forEach(function (el) {
            el.style.display = el.dataset.show.split(' ').indexOf(l) > -1 ? '' : 'none';
        });
        set('mediaLinkLabel', c.link); set('mediaLinkHint', c.linkHint);
        set('mediaFileLabel', c.file); set('mediaFileHint', c.fileHint);
        set('descLabel', c.desc); set('descHint', c.descHint); set('statsTitle', c.stats);
        ['stat1Label', 'stat2Label'].forEach(function (id, i) { document.getElementById(id).placeholder = (i ? c.s2 : c.s1) || ''; });
        link.required = l === 'website';
        detect();
    }
    function detect() {
        var u = link.value.trim(), msg = '', ok = true;
        if (layout() === 'reel' && u) {
            if (/youtube\.com|youtu\.be/i.test(u)) msg = '✔ YouTube detected — will autoplay & loop (muted).';
            else if (/instagram\.com|instagr\.am/i.test(u)) msg = '⚠ Instagram detected — Instagram blocks autoplay. Upload the MP4 too so it autoplays & loops; the card will still open Instagram on click.';
            else if (/facebook\.com|fb\.watch|fb\.com/i.test(u)) msg = '✔ Facebook detected — will autoplay (muted).';
            else if (/vimeo\.com/i.test(u)) msg = '✔ Vimeo detected — will autoplay & loop.';
            else { msg = 'Link not recognised as Instagram / YouTube / Facebook — upload the video file.'; ok = false; }
        }
        info.textContent = msg;
        info.className = msg ? (ok && msg.charAt(0) === '✔' ? 'text-success' : 'text-warning') : '';
    }
    type.addEventListener('change', update);
    link.addEventListener('input', detect);
    update();

    // Require a file OR a link (reels can be link-only).
    var form = type.form;
    form.addEventListener('submit', function (e) {
        var hasExisting = <?= $isEdit && !empty($work['file_path']) ? 'true' : 'false' ?>;
        var removing = document.getElementById('removeFile') && document.getElementById('removeFile').checked;
        var hasFile = file.files.length > 0 || (hasExisting && !removing);
        if (!hasFile && !link.value.trim()) {
            e.preventDefault();
            alert('Please upload a file or add a link.');
        }
    });
})();
</script>
