<?php
require_once __DIR__ . '/includes/functions.php';

$slug = trim((string)($_GET['slug'] ?? ''));
$pageData = null;

if ($slug !== '' && !empty($ok) && isset($conn)) {
    $esc_slug = $conn->real_escape_string($slug);
    $pageData = q("SELECT * FROM custom_pages WHERE slug_url = '$esc_slug' AND status = 1 LIMIT 1")[0] ?? null;
}

// Proper HTTP 404 if slug is missing or inactive (no soft-404 JS redirect)
if (!$pageData) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

$brand_full = trim(($brand['name'] ?? 'Huchol') . ' ' . ($brand['suffix'] ?? 'Agro Traders'));

// Clean display title & breadcrumb title
$raw_crumb   = trim((string)($pageData['breadcrumb_title'] ?? ''));
$raw_title   = trim((string)($pageData['page_title'] ?? ''));
$crumb_title = $raw_crumb !== '' ? ucwords($raw_crumb) : ($raw_title !== '' ? ucwords($raw_title) : 'Trade Guide');

// If content starts with an <h1>, use it as the clean page headline when page_title is short/generic
$content_html = (string)($pageData['content'] ?? '');
$hero_title   = $raw_title !== '' ? ucwords($raw_title) : $crumb_title;
if (preg_match('/<h1\b[^>]*>(.*?)<\/h1>/is', $content_html, $h1m)) {
    $h1_plain = trim(strip_tags($h1m[1]));
    if ($h1_plain !== '' && mb_strlen($raw_title) < 25) {
        $hero_title = $h1_plain;
        // Remove duplicate first H1 from body so the page has strictly one H1 (SEO best practice)
        $content_html = preg_replace('/<h1\b[^>]*>.*?<\/h1>/is', '', $content_html, 1);
    } else {
        // Demote any remaining H1 inside content to H2
        $content_html = preg_replace('/<(\/?)h1\b/i', '<$1h2', $content_html);
    }
}

// Wrap bare tables in .tw so wide tables scroll cleanly inside the card on mobile
$content_html = preg_replace_callback('/<table\b[^>]*>.*?<\/table>/is', function ($m) {
    return '<div class="tw">' . $m[0] . '</div>';
}, $content_html);

// ---- SEO Meta & Safe Schema Validation ----
$raw_mt = trim((string)($pageData['meta_title'] ?? ''));
$raw_md = trim(strip_tags((string)($pageData['meta_desc'] ?? '')));
$raw_mk = trim((string)($pageData['meta_key'] ?? ''));

$meta_title = ($raw_mt !== '' && strcasecmp($raw_mt, $slug) !== 0)
    ? $raw_mt
    : ($hero_title . ' | ' . $brand_full);

$meta_desc = (mb_strlen($raw_md) >= 25 && strcasecmp($raw_md, $slug) !== 0)
    ? $raw_md
    : (txt($pageData['content'] ?? '', 158) ?: ('Wholesale information and bulk supply details from ' . $brand_full . '.'));

$meta_key = ($raw_mk !== '' && strcasecmp($raw_mk, $slug) !== 0)
    ? $raw_mk
    : ($crumb_title . ', wholesale agro supplier, ' . $brand_full);

// Only accept schema_markup if it is valid <script> block or valid JSON; otherwise let header.php build fallback
$schema = '';
$raw_schema = trim((string)($pageData['schema_markup'] ?? ''));
if ($raw_schema !== '') {
    if (stripos($raw_schema, '<script') !== false) {
        $schema = $raw_schema;
    } else {
        json_decode($raw_schema);
        if (json_last_error() === JSON_ERROR_NONE) {
            $schema = '<script type="application/ld+json">' . $raw_schema . '</script>';
        }
    }
}

include_once __DIR__ . '/includes/header.php';

// ---- Sidebar Data: Other Custom Pages + Recent Blogs ----
$current_id  = (int)($pageData['id'] ?? 0);
$other_pages = q("SELECT page_title, breadcrumb_title, slug_url FROM custom_pages WHERE status = 1 AND id != $current_id AND slug_url <> '' ORDER BY id DESC LIMIT 6");
$recent_blogs = q("SELECT title, slug, image, created_at FROM blogs WHERE status = 1 ORDER BY created_at DESC, blog_id DESC LIMIT 3");

$share_url   = !empty($canonical_url) ? $canonical_url : ($base_url . 'page/' . rawurlencode($slug));
$c_address   = $ct['address'] ?? '';
$pub_date    = !empty($pageData['created_at']) ? date('d M Y', strtotime($pageData['created_at'])) : '';
?>

<main id="main">

    <!-- Page Header -->
    <section class="pghead nb">
        <div class="wrap">
            <ol class="crumbs">
                <li><a href="./">Home</a></li>
                <li><a href="location">Locations &amp; Pages</a></li>
                <li><?= e($crumb_title) ?></li>
            </ol>
            <h1><?= e($hero_title) ?></h1>
            <div class="pills">
                <?php if ($pub_date): ?>
                    <span>Updated <?= e($pub_date) ?></span>
                <?php endif; ?>
                <span><?= e($brand_full) ?></span>
                <span>Wholesale &amp; Bulk Supply</span>
            </div>
        </div>
    </section>

    <!-- Main 2-Column Layout (.art from style.css) -->
    <section class="sec art-sec">
        <div class="wrap art">

            <!-- Left Column: Dynamic Content Card -->
            <div>
                <article style="background:#fff;border:1px solid var(--line);border-radius:var(--r);padding:clamp(22px,4vw,42px);">
                    <div class="prose">
                        <?= $content_html ?>
                    </div>

                    <!-- Trade Desk Contact Box (Dynamic from contacts table) -->
                    <div style="background:var(--alt);border-left:4px solid var(--gold);border-radius:var(--r);padding:18px 20px;margin-top:28px;">
                        <p style="margin-bottom:6px;"><strong>Enquire with <?= e($brand_full) ?></strong></p>
                        <?php if ($c_address): ?>
                            <p style="margin-bottom:6px;font-size:.94rem;color:var(--mut);"><strong>Address:</strong> <?= e($c_address) ?></p>
                        <?php endif; ?>
                        <p style="margin-bottom:6px;font-size:.94rem;"><strong>Phone / WhatsApp:</strong> <a href="tel:+<?= e($tel) ?>" style="color:var(--chilli);text-decoration:underline;"><?= e($disp_phone) ?></a></p>
                        <p style="margin-bottom:0;font-size:.94rem;"><strong>Email:</strong> <a href="mailto:<?= e($mail) ?>" style="color:var(--chilli);text-decoration:underline;"><?= e($mail) ?></a></p>
                    </div>

                    <!-- Social Share Bar (Styled natively by .share in style.css) -->
                    <div class="share">
                        <b>Share this page:</b>
                        <a href="https://wa.me/?text=<?= rawurlencode($hero_title . ' - ' . $share_url) ?>" target="_blank" rel="noopener" aria-label="Share on WhatsApp">
                            <i class="fa-brands fa-whatsapp"></i> WhatsApp
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= rawurlencode($share_url) ?>" target="_blank" rel="noopener" aria-label="Share on Facebook">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?= rawurlencode($share_url) ?>&amp;text=<?= rawurlencode($hero_title) ?>" target="_blank" rel="noopener" aria-label="Share on X">
                            <i class="fa-brands fa-x-twitter"></i>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= rawurlencode($share_url) ?>" target="_blank" rel="noopener" aria-label="Share on LinkedIn">
                            <i class="fa-brands fa-linkedin-in"></i>
                        </a>
                        <button type="button" id="copyPageUrl" data-url="<?= e($share_url) ?>">
                            <i class="fa-regular fa-copy"></i> Copy link
                        </button>
                    </div>
                </article>
            </div>

            <!-- Right Column: Sticky Sidebar (.asd from style.css) -->
            <aside class="asd">

                <!-- Search Products -->
                <div class="sbox">
                    <h3>Search Catalogue</h3>
                    <form action="products" method="GET" class="sbar" role="search">
                        <input type="search" name="q" placeholder="Search almonds, chilli, onions..." aria-label="Search products" required>
                        <button type="submit" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                </div>

                <!-- Dynamic Wholesale Categories -->
                <?php if (!empty($nav_cats)): ?>
                <div class="sbox">
                    <h3>Wholesale Aisles</h3>
                    <ul>
                        <?php foreach ($nav_cats as $cslug => $cd): ?>
                            <li>
                                <a href="category/<?= e($cslug) ?>" class="<?= $cslug === $slug ? 'on' : '' ?>">
                                    <?= e($cd['name']) ?> <small>&rarr;</small>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Other Location / Custom Pages -->
                <?php if (!empty($other_pages)): ?>
                <div class="sbox">
                    <h3>More Trade Pages</h3>
                    <ul>
                        <?php foreach ($other_pages as $op):
                            $op_name = trim((string)($op['breadcrumb_title'] ?: $op['page_title']));
                        ?>
                            <li>
                                <a href="page/<?= e(rawurlencode($op['slug_url'])) ?>">
                                    <?= e(ucwords($op_name)) ?> <small>&rarr;</small>
                                </a>
                            </li>
                        <?php endforeach; ?>
                        <li><a href="location" style="color:var(--tan);font-weight:700;">View all pages <small>&rarr;</small></a></li>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Recent Buying Guides (Uses img() helper safely for both local uploads & URLs) -->
                <?php if (!empty($recent_blogs)): ?>
                <div class="sbox">
                    <h3>Recent Buying Guides</h3>
                    <ul class="mini">
                        <?php foreach ($recent_blogs as $rb):
                            $rb_img = img($rb['image'] ?? '', 'blogs', un('photo-1596040033229-a9821ebd058d', 300));
                            $rb_url = 'blog/' . rawurlencode($rb['slug']);
                        ?>
                            <li>
                                <a href="<?= e($rb_url) ?>">
                                    <span class="th"><?= pic($rb_img, $rb['title']) ?></span>
                                    <span>
                                        <?= e(mb_strimwidth($rb['title'], 0, 56, '...')) ?>
                                        <small style="display:block;color:var(--mut);font-weight:400;"><?= e(date('d M Y', strtotime($rb['created_at'] ?: 'now'))) ?></small>
                                    </span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Dark Trade Desk CTA Box -->
                <div class="sbox dkc">
                    <h3>Need today’s bulk rate?</h3>
                    <p>Send your product, grade, pack size and destination city. Our desk replies with a written quotation.</p>
                    <a href="contact" class="btn" style="width:100%;margin-bottom:10px;">Request bulk quote</a>
                    <a href="https://wa.me/<?= e($wa) ?>?text=<?= rawurlencode('Hello ' . $brand_full . ', I am enquiring from: ' . $hero_title) ?>" target="_blank" rel="noopener" class="btn green" style="width:100%;margin-bottom:12px;">
                        <i class="fa-brands fa-whatsapp"></i> WhatsApp desk
                    </a>
                    <p style="margin:0;font-size:.9rem;">
                        <a href="tel:+<?= e($tel) ?>"><i class="fa-solid fa-phone" style="color:var(--gold);margin-right:6px;"></i><?= e($disp_phone) ?></a>
                    </p>
                </div>

            </aside>

        </div>
    </section>

    <!-- Bottom CTA Band -->
    <section class="sec cbs">
        <div class="wrap">
            <div class="cband">
                <div>
                    <h2>Request a sample and today’s wholesale rate</h2>
                    <p>Tell us your required grade, quantity and delivery city. Our trade desk handles packing, GST billing and dispatch.</p>
                </div>
                <div class="cta">
                    <a href="contact" class="btn d">Get bulk quote</a>
                    <a href="https://wa.me/<?= e($wa) ?>" target="_blank" rel="noopener" class="btn green"><i class="fa-brands fa-whatsapp"></i> WhatsApp us</a>
                </div>
            </div>
        </div>
    </section>

</main>

<script>
(function () {
    var btn = document.getElementById('copyPageUrl');
    if (!btn) return;
    btn.addEventListener('click', function () {
        var url = btn.getAttribute('data-url') || window.location.href;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(function () {
                btn.innerHTML = '<i class="fa-solid fa-check"></i> Copied';
                setTimeout(function () { btn.innerHTML = '<i class="fa-regular fa-copy"></i> Copy link'; }, 2000);
            });
        }
    });
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>