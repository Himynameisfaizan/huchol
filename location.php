<?php
require_once __DIR__ . '/includes/functions.php';

$brand_full = trim(($brand['name'] ?? 'Huchol') . ' ' . ($brand['suffix'] ?? 'Agro Traders'));
$meta_title = 'Locations & Trade Pages | ' . $brand_full;
$meta_desc  = 'Explore wholesale supply locations, export trade guides and commercial sourcing pages published by ' . $brand_full . '.';
$meta_key   = 'huchol agro traders locations, wholesale agro supply india, dry fruits spices suppliers';

$search_q = trim((string)($_GET['q'] ?? ''));

// Fetch all active custom pages (newest first)
$all_pages = q("SELECT id, page_title, breadcrumb_title, slug_url, content, meta_desc, created_at
                FROM custom_pages
                WHERE status = 1 AND slug_url <> ''
                ORDER BY id DESC");
$total_all = count($all_pages);

// Filter pages if search query is present
$customPages = $all_pages;
if ($search_q !== '') {
    $needle = mb_strtolower($search_q);
    $customPages = array_values(array_filter($all_pages, function ($row) use ($needle) {
        $hay = mb_strtolower(
            ($row['breadcrumb_title'] ?? '') . ' ' .
            ($row['page_title'] ?? '') . ' ' .
            ($row['meta_desc'] ?? '') . ' ' .
            strip_tags((string)($row['content'] ?? ''))
        );
        return mb_strpos($hay, $needle) !== false;
    }));
}
$shown_count = count($customPages);

include_once __DIR__ . '/includes/header.php';
?>

<style>
.loc-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(min(300px, 100%), 1fr));
    gap: 22px;
}
.loc-card {
    background: #fff;
    border: 1px solid var(--line);
    border-top: 5px solid var(--leaf);
    border-radius: var(--r);
    padding: 24px;
    display: flex;
    flex-direction: column;
    transition: transform .25s var(--e), box-shadow .25s, border-color .25s;
}
.loc-card:hover {
    transform: translateY(-4px);
    border-top-color: var(--gold);
    box-shadow: 0 18px 36px rgba(14, 59, 42, .12);
}
.loc-top {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    margin-bottom: 14px;
}
.loc-ic {
    flex: none;
    width: 46px;
    height: 46px;
    display: grid;
    place-items: center;
    background: var(--leaf);
    color: var(--gold);
    border-radius: 14px 0 14px 0;
    font-size: 1.1rem;
}
.loc-top h3 {
    font-size: 1.25rem;
    line-height: 1.25;
    margin: 2px 0 4px;
    color: var(--ink);
    overflow-wrap: anywhere;
}
.loc-top h3 a:hover {
    color: var(--tan);
}
.loc-date {
    display: block;
    font-size: .8rem;
    color: var(--mut);
}
.loc-desc {
    color: var(--mut);
    font-size: .94rem;
    line-height: 1.65;
    margin-bottom: 18px;
    display: -webkit-box;
    line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.loc-ft {
    margin-top: auto;
    padding-top: 14px;
    border-top: 1px dashed var(--line);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
}
.loc-ft small {
    font-size: .8rem;
    color: var(--mut);
    font-weight: 600;
}
.loc-go {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font: 700 .92rem 'Hind', sans-serif;
    color: var(--chilli);
}
.loc-go:hover {
    color: var(--leaf);
}
</style>

<main id="main">

    <!-- Page Header (Matches products.php & policy pages) -->
    <section class="pghead nb">
        <div class="wrap">
            <ol class="crumbs">
                <li><a href="./">Home</a></li>
                <li>Locations &amp; Trade Pages</li>
            </ol>
            <h1>Locations &amp; Trade Pages</h1>
            <p class="lead">Browse region-specific wholesale guides, export trade information and commercial supply pages published by <?= e($brand_full) ?>.</p>

            <div class="pills">
                <span><?= $total_all ?> <?= $total_all === 1 ? 'page' : 'pages' ?> published</span>
                <span>Pan-India &amp; Export Supply</span>
                <span>Bulk Quotations</span>
            </div>

            <?php if ($total_all > 0): ?>
                <form action="location" method="GET" class="sbar hs" role="search">
                    <input type="search" name="q" value="<?= e($search_q) ?>" placeholder="Search location or trade page (e.g. export, Delhi, saffron)..." aria-label="Search location pages">
                    <button type="submit" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            <?php endif; ?>
        </div>
    </section>

    <!-- Custom Pages Card Showcase -->
    <section class="sec">
        <div class="wrap">
            <div class="lh">
                <div>
                    <?php if ($search_q !== ''): ?>
                        Showing <strong><?= $shown_count ?></strong> <?= $shown_count === 1 ? 'result' : 'results' ?> for “<?= e($search_q) ?>” (out of <?= $total_all ?> total)
                    <?php else: ?>
                        Showing <strong><?= $shown_count ?></strong> <?= $shown_count === 1 ? 'published page' : 'published pages' ?>
                    <?php endif; ?>
                </div>
                <?php if ($search_q !== ''): ?>
                    <a href="location" class="rd">Clear search &times;</a>
                <?php endif; ?>
            </div>

            <?php if (!empty($customPages)): ?>
                <div class="loc-grid">
                    <?php foreach ($customPages as $cp):
                        $rawTitle = trim((string)($cp['breadcrumb_title'] ?? ''));
                        if ($rawTitle === '') $rawTitle = trim((string)($cp['page_title'] ?? ''));
                        $cardTitle = $rawTitle !== '' ? ucwords($rawTitle) : 'Trade Guide';

                        $slug = trim((string)($cp['slug_url'] ?? ''));
                        $pageUrl = 'page/' . rawurlencode($slug);

                        // Use meta_desc only if meaningful; otherwise build clean excerpt from content
                        $desc = trim(strip_tags((string)($cp['meta_desc'] ?? '')));
                        if (mb_strlen($desc) < 25 || strcasecmp($desc, $slug) === 0) {
                            $desc = txt($cp['content'] ?? '', 155);
                        }
                        if ($desc === '') {
                            $desc = 'Explore wholesale supply specifications, grading standards and bulk enquiry details from ' . $brand_full . '.';
                        }

                        $dateLabel = !empty($cp['created_at']) ? date('d M Y', strtotime($cp['created_at'])) : '';
                    ?>
                        <article class="loc-card">
                            <div class="loc-top">
                                <div class="loc-ic" aria-hidden="true">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <div>
                                    <h3><a href="<?= e($pageUrl) ?>"><?= e($cardTitle) ?></a></h3>
                                    <?php if ($dateLabel): ?>
                                        <span class="loc-date">Published <?= e($dateLabel) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <p class="loc-desc"><?= e($desc) ?></p>

                            <div class="loc-ft">
                                <small><?= e($brand_full) ?></small>
                                <a class="loc-go" href="<?= e($pageUrl) ?>">
                                    View page <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty">
                    <h3><?= $search_q !== '' ? 'No matching pages found' : 'No pages published yet' ?></h3>
                    <p>
                        <?= $search_q !== ''
                            ? 'Try a different keyword or browse all published pages.'
                            : 'Published location and trade information pages will appear here automatically.' ?>
                    </p>
                    <?php if ($search_q !== ''): ?>
                        <div style="margin-top:18px;">
                            <a href="location" class="btn d">Show all pages</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Bottom CTA Band -->
    <section class="sec cbs">
        <div class="wrap">
            <div class="cband">
                <div>
                    <h2>Looking for bulk supply in your city?</h2>
                    <p>Share the product, grade, quantity and delivery city or port. Our trade desk replies with a written rate and sample plan.</p>
                </div>
                <div class="cta">
                    <a href="contact" class="btn d">Get bulk quote</a>
                    <a href="https://wa.me/<?= e($wa) ?>?text=<?= rawurlencode('Hello ' . $brand_full . ', I want to enquire about bulk supply for my location.') ?>" target="_blank" rel="noopener" class="btn green">
                        <i class="fa-brands fa-whatsapp"></i> WhatsApp us
                    </a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>