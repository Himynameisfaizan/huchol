<?php
// Blog article: ?slug=<blog slug>
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/sections.php';

$slug = trim($_GET['slug'] ?? '');
$b = $slug !== '' ? (q("SELECT * FROM blogs WHERE status = 1 AND slug = '" . esc($slug) . "' LIMIT 1")[0] ?? null) : null;
if (!$b) {
    $notfound = ['Article not found', 'We could not find that article. It may have moved, so try our latest guides.'];
    include __DIR__ . '/includes/not-found.php'; exit;
}

[$body, $toc] = prose($b['description']);
$mins   = read_time($b['description']);
$title  = txt($b['title']);
$cover  = img($b['image'], 'blogs', un('photo-1596040033229-a9821ebd058d', 1100));
$related = q("SELECT title, slug, author, image, meta_desc, description, created_at FROM blogs WHERE status = 1 AND blog_id <> " . (int)$b['blog_id'] . " ORDER BY created_at DESC LIMIT 3");
$prods = q("SELECT p.id, p.pro_name, p.pro_img, p.slug_url, c.slug_url AS cslug FROM products p LEFT JOIN categories c ON c.cate_id = p.pro_cate WHERE p.is_disabled = 0 ORDER BY RAND() LIMIT 3");
$cats  = q("SELECT categories, slug_url FROM categories WHERE status = 1 ORDER BY id");

$meta_title = $b['meta_title'] ?: $title . ' | ' . $brand['name'];
$meta_desc  = $b['meta_desc'] ?: txt($b['description'], 155);
$meta_key   = $b['meta_key'] ?: '';
$canonical_url = base_url() . 'blog-details.php?slug=' . urlencode($b['slug']);
$ts = strtotime($b['created_at']) ?: time();
$schema = jld(['@context' => 'https://schema.org', '@graph' => [
    ['@type' => 'Article', 'headline' => $title, 'image' => [$cover], 'datePublished' => date('c', $ts), 'dateModified' => date('c', $ts), 'author' => ['@type' => 'Organization', 'name' => $b['author'] ?: $brand['name']], 'publisher' => ['@type' => 'Organization', 'name' => $brand['name']], 'description' => $meta_desc, 'mainEntityOfPage' => $canonical_url],
    ['@type' => 'BreadcrumbList', 'itemListElement' => [['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => base_url()], ['@type' => 'ListItem', 'position' => 2, 'name' => 'Insights', 'item' => base_url() . 'blog.php'], ['@type' => 'ListItem', 'position' => 3, 'name' => $title, 'item' => $canonical_url]]]]]) . ($b['schema_markup'] ?? '');

include __DIR__ . '/includes/header.php';
$shareU = urlencode($canonical_url); $shareT = urlencode($title);
?>
<div class="crumbbar"><div class="wrap"><?= crumbs([['Home', './'], ['Insights', 'blog.php'], [mb_strimwidth($title, 0, 60, '...'), null]], 'dk2') ?></div></div>

<main id="main">
<section class="sec art-sec">
    <div class="wrap art">
        <article>
            <header class="ah">
                <span class="badge">Buying guide</span>
                <h1><?= e($title) ?></h1>
                <div class="am">
                    <span><i class="fa-regular fa-calendar"></i><?= e(date_fmt($b['created_at'])) ?></span>
                    <span><i class="fa-regular fa-clock"></i><?= $mins ?> min read</span>
                    <span><i class="fa-regular fa-user"></i><?= e($b['author'] ?: $brand['name']) ?></span>
                </div>
            </header>
            <div class="ahero"><?= pic($cover, $title, true) ?></div>

            <?php if (count($toc) > 2): ?>
            <details class="toc" open><summary>In this guide</summary><ol><?php foreach ($toc as $t): ?><li><a href="#<?= e($t[0]) ?>"><?= e($t[1]) ?></a></li><?php endforeach; ?></ol></details>
            <?php endif; ?>

            <div class="prose"><?= $body ?></div>

            <div class="share">
                <b>Share this guide</b>
                <a target="_blank" rel="noopener" href="https://wa.me/?text=<?= $shareT ?>%20<?= $shareU ?>" aria-label="Share on WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                <a target="_blank" rel="noopener" href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $shareU ?>" aria-label="Share on LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                <a target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareU ?>" aria-label="Share on Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                <button type="button" data-copy><i class="fa-regular fa-copy"></i> Copy link</button>
            </div>

            <div class="author">
                <?= mark(52) ?>
                <div><b>Written by the <?= e($brand['name']) ?> trade desk</b>
                <p>We source, grade and dispatch dry fruits, fresh produce and spices from <?= e($brand['city']) ?>. Questions about this guide? <a href="contact.php">Ask our trade desk</a>.</p></div>
            </div>
        </article>

        <aside class="asd">
            <div class="sbox dkc"><h3>Need a rate today?</h3><p>Send your product, grade, quantity and delivery city. We reply with a written rate and a sample plan.</p><a class="btn" href="contact.php?subject=<?= urlencode('Enquiry from guide: ' . $title) ?>">Get bulk rate</a></div>
            <?php if ($prods): ?>
            <div class="sbox"><h3>Popular products</h3>
                <ul class="mini"><?php foreach ($prods as $pr): ?>
                    <li><a href="product-details.php?slug=<?= e(urlencode($pr['slug_url'])) ?>"><span class="th"><?= pic(product_img($pr['pro_img'], $pr['cslug'], 160), $pr['pro_name']) ?></span><?= e($pr['pro_name']) ?></a></li>
                <?php endforeach; ?></ul>
            </div>
            <?php endif; ?>
            <div class="sbox"><h3>Browse categories</h3>
                <ul><?php foreach ($cats as $c): ?><li><a href="category.php?slug=<?= e($c['slug_url']) ?>"><?= e($c['categories']) ?></a></li><?php endforeach; ?></ul>
            </div>
        </aside>
    </div>
</section>

<?php if ($related): ?>
<section class="sec alt"><div class="wrap">
    <div class="bh"><div class="sh"><h2>Keep reading</h2><span class="hi">More guides for buyers</span></div><a href="blog.php" class="btn o d">All articles</a></div>
    <div class="bg-grid"><?php foreach ($related as $r) echo blog_card($r); ?></div>
</div></section>
<?php endif;
cta_band('Ready to order?', 'Tell us the product, grade and quantity. Our trade desk will send a written rate and a sample plan.');
?>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>