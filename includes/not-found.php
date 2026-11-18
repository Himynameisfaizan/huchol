<?php
// Included by a page when its record is missing. Sends a real 404, keeps the site chrome.
http_response_code(404);
$noindex = true;
$meta_title = ($notfound[0] ?? 'Page not found') . ' | ' . $brand['name'];
$meta_desc = $notfound[1] ?? ''; $meta_key = '';
include __DIR__ . '/header.php';
?>
<main id="main">
<section class="sec"><div class="wrap nf">
    <h1><?= e($notfound[0] ?? 'Page not found') ?></h1>
    <p><?= e($notfound[1] ?? 'The page you are looking for is not available.') ?></p>
    <div class="cta"><a class="btn d" href="category.php">Browse all products</a><a class="btn o d" href="blog.php">Read our guides</a></div>
    <ul class="nf-l"><?php foreach ($nav_cats as $s => $cd): ?><li><a href="category.php?slug=<?= e($s) ?>"><?= e($cd['name']) ?></a></li><?php endforeach; ?></ul>
</div></section>
</main>
<?php include __DIR__ . '/footer.php'; ?>