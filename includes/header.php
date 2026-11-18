<?php
/** Global header: DB connect, helpers, SEO engine, navigation */
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/functions.php';   // DB connection, brand config, helpers

// ---- Contact settings (contacts table) ----
$ct = array_merge(['email' => '', 'phone' => '', 'wp_number' => '', 'working_hours' => '', 'address' => '', 'facebook' => '', 'instagram' => '', 'twitter' => '', 'linkdin' => ''],
    q("SELECT email, phone, wp_number, working_hours, address, facebook, instagram, twitter, linkdin FROM contacts LIMIT 1")[0] ?? []);
$digits = function ($s) { $d = preg_replace('/\D/', '', (string)$s); return strlen($d) === 10 ? '91' . $d : $d; };
$mail = $ct['email'] ?: 'info@example.com';
$tel  = $digits($ct['phone']) ?: '919800000000';
$wa   = $digits($ct['wp_number']) ?: $tel;
$disp_phone = preg_match('/^91(\d{5})(\d{5})$/', $tel, $m) ? "+91 $m[1] $m[2]" : '+' . $tel;
$hours = $ct['working_hours'] ?: $brand['hours'];

$logo = img(q("SELECT logo_path FROM logos WHERE location='header' AND is_active=1 ORDER BY id DESC LIMIT 1")[0]['logo_path'] ?? '', 'logo', '');

// ---- Nav categories ----
$nav_cats = [];
foreach (q("SELECT cate_id, categories AS category_name, slug_url AS slug FROM categories WHERE status=1 ORDER BY id ASC") as $c) {
    $subs = q("SELECT categories AS subcat_name, slug_url AS slug FROM sub_categories WHERE cate_id=" . (int)$c['cate_id'] . " AND status=1");
    $nav_cats[$c['slug']] = ['name' => $c['category_name'], 'subs' => $subs];
}

// ---- SEO + schema (meta / page_schemas tables, keyed by file name) ----
$current_page_name = basename($_SERVER['PHP_SELF']);
if (empty($meta_title)) {
    $meta_title = $brand['name'] . ' ' . $brand['suffix'] . ' | Wholesale Dry Fruits, Fresh Produce & Spices';
    $meta_desc  = 'Bulk supply of dry fruits, fresh fruits, vegetables and spices for wholesalers, hotels, retailers and exporters.';
    $meta_key   = 'dry fruits wholesale, fresh vegetables supplier, spices exporter india';
    $schema     = '';
    if ($ok) {
        $pg = $conn->real_escape_string($current_page_name);
        $md = q("SELECT meta_title, meta_desc, meta_key FROM meta WHERE page_url='$pg' LIMIT 1")[0] ?? [];
        $meta_title = ($md['meta_title'] ?? '') ?: $meta_title;
        $meta_desc  = ($md['meta_desc'] ?? '') ?: $meta_desc;
        $meta_key   = ($md['meta_key'] ?? '') ?: $meta_key;
        $schema     = q("SELECT schema_markup FROM page_schemas WHERE page_url='$pg' LIMIT 1")[0]['schema_markup'] ?? '';
    }
}
$schema = $schema ?? '';

// ---- Canonical (keeps slug / cat for details & category pages) ----
if (!isset($canonical_url)) {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || ($_SERVER['SERVER_PORT'] ?? '') == 443;
    $qs = '';
    foreach (['slug', 'slug_url', 'cat'] as $k) if (!empty($_GET[$k])) { $qs = '?' . $k . '=' . urlencode(trim($_GET[$k])); break; }
    $canonical_url = ($https ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) . $qs;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($meta_title) ?></title>
    <meta name="description" content="<?= e($meta_desc) ?>">
    <meta name="keywords" content="<?= e($meta_key) ?>">
    <?php if (!empty($noindex)): ?><meta name="robots" content="noindex,follow"><?php endif; ?>
    <link rel="canonical" href="<?= e($canonical_url) ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= e($meta_title) ?>">
    <meta property="og:description" content="<?= e($meta_desc) ?>">
    <meta property="og:url" content="<?= e($canonical_url) ?>">
    <?php if (!empty($schema)) echo $schema; ?>
    <link rel="icon" href="assets/img/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind:wght@400;500;600;700&family=Yatra+One&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('assets/style/style.css')) ?>">
    <?php foreach (($extra_css ?? []) as $css): ?><link rel="stylesheet" href="<?= e(asset($css)) ?>"><?php endforeach; ?>
</head>
<body>
<a class="skip" href="#main">Skip to content</a>

<div class="topbar"><div class="wrap">
    <div class="tl">
        <a href="mailto:<?= e($mail) ?>"><i class="fa-regular fa-envelope"></i> <?= e($mail) ?></a>
        <a href="tel:+<?= e($tel) ?>"><i class="fa-solid fa-phone"></i> <?= e($disp_phone) ?></a>
    </div>
    <span><i class="fa-regular fa-clock"></i> <?= e($hours) ?></span>
</div></div>

<header class="site-header" id="top">
    <div class="wrap nav-wrap">
        <a class="brand" href="./">
            <?= $logo ? '<img src="' . e($logo) . '" alt="' . e($brand['name']) . '" height="44">' : mark(44) ?>
            <span><b><?= e($brand['name']) ?></b><small><?= e($brand['suffix']) ?></small></span>
        </a>
        <button class="tg" aria-label="Menu" aria-expanded="false" aria-controls="nav"><i></i></button>
        <nav id="nav">
            <ul>
                <li><a href="./">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li class="has-dropdown">
                    <a href="products.php">Products <i class="fa-solid fa-chevron-down dd-icon"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="products.php" class="all">View all products</a></li>
                        <?php foreach ($nav_cats as $slug => $cd): ?>
                        <li class="<?= $cd['subs'] ? 'has-sub-dropdown' : '' ?>">
                            <a href="category.php?slug=<?= e($slug) ?>"><?= e($cd['name']) ?></a>
                            <?php if ($cd['subs']): ?>
                            <ul class="sub-dropdown-menu">
                                <?php foreach ($cd['subs'] as $sub): ?>
                                <li><a href="category.php?slug=<?= e($slug) ?>&sub=<?= e($sub['slug']) ?>"><?= e($sub['subcat_name']) ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <li><a href="service.php">Services</a></li>
                <li><a href="blog.php">Insights</a></li>
            </ul>
            <a href="contact.php" class="btn">Get bulk quote</a>
        </nav>
    </div>
</header>