<?php
// Blog listing: ?q=<search>&page=N
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/sections.php';

$qs  = trim($_GET['q'] ?? '');
$pgN = max(1, (int)($_GET['page'] ?? 1));
$per = 9;
$W = 'status = 1';
if ($qs !== '') { $l = str_replace(['%', '_'], ['\\%', '\\_'], esc($qs)); $W .= " AND (title LIKE '%$l%' OR meta_desc LIKE '%$l%' OR description LIKE '%$l%')"; }
$total = (int)(q("SELECT COUNT(*) AS c FROM blogs WHERE $W")[0]['c'] ?? 0);
$pages = max(1, (int)ceil($total / $per)); $pgN = min($pgN, $pages); $off = ($pgN - 1) * $per;
$posts = q("SELECT blog_id, title, slug, author, image, meta_desc, description, created_at FROM blogs WHERE $W ORDER BY created_at DESC, blog_id DESC LIMIT $off, $per");
$topics = array_merge(array_column(q("SELECT categories FROM categories WHERE status=1 ORDER BY id"), 'categories'), ['Packing', 'Export']);

$noindex = $qs !== '';
$canonical_url = base_url() . 'blog.php' . ($pgN > 1 ? '?page=' . $pgN : '');
$featured = ($pgN === 1 && $qs === '' && $posts) ? array_shift($posts) : null;

include __DIR__ . '/includes/header.php';
?>
<main id="main">
<section class="pghead nb">
    <div class="wrap">
        <?= crumbs([['Home', './'], ['Insights', null]]) ?>
        <h1>Buying guides and trade notes</h1>
        <p class="lead">Practical guides on grades, packing, seasons and paperwork for buyers of dry fruits, fresh produce and spices.</p>
        <form class="sbar hs" method="get" action="blog.php" role="search">
            <input type="search" name="q" value="<?= e($qs) ?>" placeholder="Search guides, e.g. cashew grades" aria-label="Search guides">
            <button type="submit" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
    </div>
</section>

<section class="sec">
    <div class="wrap">
        <div class="chips" role="group" aria-label="Topics">
            <a href="blog.php" class="<?= $qs === '' ? 'on' : '' ?>">All</a>
            <?php foreach ($topics as $t): ?><a href="blog.php?q=<?= urlencode($t) ?>" class="<?= strcasecmp($qs, $t) === 0 ? 'on' : '' ?>"><?= e($t) ?></a><?php endforeach; ?>
        </div>
        <?php if ($qs !== ''): ?><p class="lh"><span><?= $total ?> article<?= $total === 1 ? '' : 's' ?> for “<?= e($qs) ?>”</span> <a class="rd" href="blog.php">Clear search</a></p><?php endif; ?>

        <?php if ($featured): $fu = 'blog-details.php?slug=' . urlencode($featured['slug']); $fi = img($featured['image'], 'blogs', un('photo-1596040033229-a9821ebd058d', 900)); ?>
        <article class="feat">
            <a class="im" href="<?= e($fu) ?>"><?= pic($fi, $featured['title'], true) ?></a>
            <div class="bd">
                <span class="badge">Latest guide</span>
                <h2><a href="<?= e($fu) ?>"><?= e($featured['title']) ?></a></h2>
                <p><?= e(txt($featured['meta_desc'], 190) ?: txt($featured['description'], 190)) ?></p>
                <small class="meta"><?= e(date_fmt($featured['created_at'])) ?> by <?= e($featured['author']) ?>, <?= read_time($featured['description']) ?> min read</small>
                <a class="btn d fbtn" href="<?= e($fu) ?>">Read the guide</a>
            </div>
        </article>
        <?php endif; ?>

        <?php if ($posts): ?>
            <div class="bg-grid"><?php foreach ($posts as $b) echo blog_card($b); ?></div>
            <?= pager($pgN, $pages, array_filter(['q' => $qs], 'strlen')) ?>
        <?php elseif (!$featured): ?>
            <div class="empty"><h3>No articles found</h3><p>Try a different word, or ask our trade desk directly.</p><br><a class="btn d" href="contact.php">Ask the trade desk</a></div>
        <?php endif; ?>
    </div>
</section>

<?php
why_block('Guides written for buyers', 'What to expect', [['Written from the trade desk', 'Each guide draws on everyday buying, grading and dispatch work.'], ['Practical checklists', 'Specifications, packing and paperwork you can use when you order.'], ['Plain language', 'Grades, sizes and trade terms explained without jargon.'], ['Ask us anything', 'Need a specific answer? Our trade desk replies during business hours.']]);
cta_band('Have a sourcing question?', 'Tell us what you buy and in what quantity. We will reply with a written rate and practical advice on grade and packing.');
?>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>