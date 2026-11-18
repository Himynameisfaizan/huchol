<?php
// Category listing: ?slug=<category>&sub=<sub-category>&q=<search>&page=N
// With no slug it works as "all products" (products.php simply loads this file).
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/sections.php';

$self    = basename($_SERVER['PHP_SELF']);
$slug    = trim($_GET['slug'] ?? $_GET['cat'] ?? '');
$subSlug = trim($_GET['sub'] ?? '');
$qs      = trim($_GET['q'] ?? '');
$pgN     = max(1, (int)($_GET['page'] ?? 1));
$per     = 12;

$cat = $slug !== '' ? (q("SELECT * FROM categories WHERE slug_url='" . esc($slug) . "' AND status=1 LIMIT 1")[0] ?? null) : null;
if ($slug !== '' && !$cat) {
    $notfound = ['Category not found', 'We could not find that category. Pick one of our product lines instead.'];
    include __DIR__ . '/includes/not-found.php';
    exit;
}

// Sub-categories / sidebar
$subs = $cat ? q("SELECT s.id, s.categories, s.slug_url, s.meta_title, s.meta_desc, s.meta_key, (SELECT COUNT(*) FROM products p WHERE p.pro_sub_cate = s.id AND p.is_disabled = 0) AS n FROM sub_categories s WHERE s.cate_id=" . (int)$cat['cate_id'] . " AND s.status=1 ORDER BY s.id") : [];
$sub = null;
foreach ($subs as $s) if ($s['slug_url'] === $subSlug) $sub = $s;
$allCats = $cat ? [] : q("SELECT c.categories, c.slug_url, COUNT(p.id) AS n FROM categories c LEFT JOIN products p ON p.pro_cate = c.cate_id AND p.is_disabled = 0 WHERE c.status=1 GROUP BY c.id, c.categories, c.slug_url ORDER BY c.id");

// Products
$where = ['p.is_disabled = 0'];
if ($cat) $where[] = 'p.pro_cate = ' . (int)$cat['cate_id'];
if ($sub) $where[] = 'p.pro_sub_cate = ' . (int)$sub['id'];
if ($qs !== '') {
    $l = str_replace(['%', '_'], ['\\%', '\\_'], esc($qs));
    $where[] = "(p.pro_name LIKE '%$l%' OR p.short_desc LIKE '%$l%' OR p.meta_key LIKE '%$l%')";
}
$W = implode(' AND ', $where);
$total = (int)(q("SELECT COUNT(*) AS c FROM products p WHERE $W")[0]['c'] ?? 0);
$pages = max(1, (int)ceil($total / $per));
$pgN = min($pgN, $pages);
$off = ($pgN - 1) * $per;
$items = q("SELECT p.id, p.pro_name, p.pro_img, p.slug_url, p.short_desc, c.categories AS cat, c.slug_url AS cslug FROM products p LEFT JOIN categories c ON c.cate_id = p.pro_cate WHERE $W ORDER BY p.pro_name ASC LIMIT $off, $per");
$guides = q("SELECT title, slug, author, image, meta_desc, description, created_at FROM blogs WHERE status=1 ORDER BY created_at DESC LIMIT 3");

// Content + SEO
$info  = cat_info($cat['slug_url'] ?? '', $cat['categories'] ?? 'products');
$h1    = $cat ? ($sub ? $sub['categories'] . ' (' . $cat['categories'] . ')' : $cat['categories']) : ($qs !== '' ? 'Search results' : 'All products');
$intro = $cat ? $info['intro'] : 'Dry fruits, fresh fruits, vegetables and spices, sourced directly and packed for wholesale. Filter by category or search for a product to request a rate.';
$ent   = $sub ?: $cat;
if ($ent) {
    $meta_title = $ent['meta_title'] ?: $h1 . ' | ' . $brand['name'];
    $meta_desc  = $ent['meta_desc'] ?: txt($intro, 155);
    $meta_key   = $ent['meta_key'] ?: strtolower($h1) . ' wholesale';
}
$noindex = $qs !== '';
$cp = array_filter(['slug' => $cat ? $slug : '', 'sub' => $sub ? $sub['slug_url'] : ''], 'strlen');
if ($pgN > 1) $cp['page'] = $pgN;
$canonical_url = base_url() . $self . ($cp ? '?' . http_build_query($cp) : '');

$crumbs = [['Home', './'], ['Products', 'category.php']];
if ($cat) $crumbs[] = [$cat['categories'], $sub ? 'category.php?slug=' . urlencode($slug) : null];
if ($sub) $crumbs[] = [$sub['categories'], null];
$bc = [];
foreach ($crumbs as $i => $c) $bc[] = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $c[0], 'item' => $c[1] ? base_url() . ($c[1] === './' ? '' : $c[1]) : $canonical_url];
$schema = jld(['@context' => 'https://schema.org', '@graph' => [['@type' => 'BreadcrumbList', 'itemListElement' => $bc], ['@type' => 'CollectionPage', 'name' => $h1, 'url' => $canonical_url], faq_schema($info['faq'])]]) . ($cat['schema_markup'] ?? '');

include __DIR__ . '/includes/header.php';
$catImg = $cat ? img($cat['image'] ?? '', 'categories', un(cat_img_id($slug), 900)) : '';
$subject = $cat ? $cat['categories'] : 'Bulk enquiry';
?>
<main id="main">

    <section class="pghead">
        <div class="wrap split">
            <div>
                <?= crumbs($crumbs) ?>
                <h1><?= e($h1) ?></h1>
                <p class="lead"><?= e($intro) ?></p>
                <div class="pills"><span><?= $total ?> product<?= $total === 1 ? '' : 's' ?> listed</span><span>Samples on request</span><span>Pan-India dispatch</span></div>
            </div>
            <?php if ($cat): ?><div class="pgimg"><?= pic($catImg, $cat['categories'], true) ?></div><?php endif; ?>
        </div>
    </section>

    <section class="trust">
        <div class="wrap">
            <div class="tr-grid">
                <?php foreach ($info['facts'] as $f): ?><div>
                        <h3><?= e($f[0]) ?></h3>
                        <p><?= e($f[1]) ?></p>
                    </div><?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="sec" id="listing">
        <div class="wrap lst">
            <aside class="side">
                <form class="sbar" method="get" action="<?= e($self) ?>" role="search">
                    <?php if ($cat): ?><input type="hidden" name="slug" value="<?= e($slug) ?>"><?php endif; ?>
                    <!-- <?php if ($sub): ?><input type="hidden" name="sub" value="<?= e($sub['slug_url']) ?>"><?php endif; ?> -->
                    <input type="search" name="q" value="<?= e($qs) ?>" placeholder="Search products" aria-label="Search products">
                    <button type="submit" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div class="sbox">
                    <h3><?= $cat ? 'Browse ' . e($cat['categories']) : 'Categories' ?></h3>
                    <ul>
                        <?php if ($cat): ?>
                            <li><a href="category.php?slug=<?= e($slug) ?>" class="<?= !$sub ? 'on' : '' ?>">All <?= e($cat['categories']) ?></a></li>
                            <!-- <?php foreach ($subs as $s): ?><li><a href="category.php?slug=<?= e($slug) ?>&sub=<?= e($s['slug_url']) ?>" class="<?= $sub && $sub['id'] == $s['id'] ? 'on' : '' ?>"><?= e($s['categories']) ?> <small><?= (int)$s['n'] ?></small></a></li><?php endforeach; ?> -->
                        <?php else: ?>
                            <li><a href="category.php" class="on">All products</a></li>
                            <?php foreach ($allCats as $c): ?><li><a href="category.php?slug=<?= e($c['slug_url']) ?>"><?= e($c['categories']) ?> <small><?= (int)$c['n'] ?></small></a></li><?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="sbox dkc">
                    <h3>Need a custom grade?</h3>
                    <p>Tell us the grade, pack size and quantity. We reply with a written rate.</p>
                    <a class="btn" href="contact.php?subject=<?= urlencode($subject) ?>">Send requirement</a>
                </div>
            </aside>

            <div>
                <div class="lh">
                    <p><?= $total ? 'Showing ' . ($off + 1) . ' to ' . ($off + count($items)) . ' of ' . $total : 'No products found' ?><?= $qs !== '' ? ' for “' . e($qs) . '”' : '' ?></p>
                    <?php if ($qs !== ''): ?><a class="rd" href="<?= e($self . ($cat ? '?slug=' . urlencode($slug) : '')) ?>">Clear search</a><?php endif; ?>
                </div>
                <?php if ($items): ?>
                    <div class="pg-grid"><?php foreach ($items as $p) echo product_card($p); ?></div>
                    <!-- <?= pager($pgN, $pages, array_filter(['slug' => $cat ? $slug : '', 'sub' => $sub ? $sub['slug_url'] : '', 'q' => $qs], 'strlen')) ?> -->
                <?php else: ?>
                    <div class="empty">
                        <h3>We could not find a match</h3>
                        <p>Do not worry. We source more lines than we list, so send us what you need and we will find it.</p><br><a class="btn d" href="contact.php?subject=<?= urlencode($qs ?: $subject) ?>">Send your requirement</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php
    why_block('Why buyers choose us for ' . ($cat ? strtolower($cat['categories']) : 'bulk supply'), 'What we check before it ships', $info['checks']);
    steps_block();
    faq_block('Questions buyers ask us', ($cat ? $cat['categories'] : 'Bulk supply') . ' FAQs', $info['faq']);
    if ($guides): ?>
        <section class="sec alt">
            <div class="wrap">
                <div class="bh">
                    <div class="sh">
                        <h2>Buying guides and trade notes</h2><span class="hi">Know the market before you buy</span>
                    </div><a href="blog.php" class="btn o d">All articles</a>
                </div>
                <div class="bg-grid"><?php foreach ($guides as $b) echo blog_card($b); ?></div>
            </div>
        </section>
    <?php endif;
    cta_band('Tell us what you need, we will quote today', 'Share the product, grade, quantity and delivery city. Our trade desk replies with a written rate and a sample plan.', $subject);
    ?>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>