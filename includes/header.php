<?php
/** Global header: DB connect, helpers, Base URL, SEO engine, Schema & Navigation */
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/functions.php';   // DB connection, brand config, helpers

// ---- 1. Auto Base Path & Origin (Works on Localhost subfolder + Live root) ----
$is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['SERVER_PORT'] ?? '') == 443)
    || (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

$scheme = $is_https ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
$is_local = (stripos($host, 'localhost') !== false || strpos($host, '127.0.0.1') === 0);

$doc_root = rtrim(str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: ''), '/');
$app_root = rtrim(str_replace('\\', '/', realpath(dirname(__DIR__)) ?: ''), '/');
if ($doc_root !== '' && strpos($app_root, $doc_root) === 0) {
    $rel = trim(substr($app_root, strlen($doc_root)), '/');
    $base_path = $rel === '' ? '/' : '/' . $rel . '/';
} else {
    $dir = trim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
    $base_path = $dir === '' ? '/' : '/' . $dir . '/';
}

// Asset/HTML base URL (matches current host so localhost CSS/JS never break)
$base_url = $scheme . '://' . $host . $base_path;

// SEO canonical origin (forces https://www. on live domain; keeps current host on localhost)
if ($is_local) {
    $seo_origin = $scheme . '://' . $host;
} else {
    $seo_host   = stripos($host, 'www.') === 0 ? $host : 'www.' . $host;
    $seo_origin = 'https://' . $seo_host;
}
$seo_base = $seo_origin . $base_path;

// ---- 2. Contact Settings (contacts table) ----
$ct = array_merge(
    ['email' => '', 'phone' => '', 'wp_number' => '', 'working_hours' => '', 'address' => '', 'facebook' => '', 'instagram' => '', 'twitter' => '', 'linkdin' => ''],
    q("SELECT email, phone, wp_number, working_hours, address, facebook, instagram, twitter, linkdin FROM contacts LIMIT 1")[0] ?? []
);
$digits = function ($s) {
    $d = preg_replace('/\D/', '', (string)$s);
    return strlen($d) === 10 ? '91' . $d : $d;
};
$mail = $ct['email'] ?: 'info@example.com';
$tel  = $digits($ct['phone']) ?: '919800000000';
$wa   = $digits($ct['wp_number']) ?: $tel;
$disp_phone = preg_match('/^91(\d{5})(\d{5})$/', $tel, $m) ? "+91 $m[1] $m[2]" : '+' . $tel;
$hours = $ct['working_hours'] ?: ($brand['hours'] ?? '');

$logo = img(q("SELECT logo_path FROM logos WHERE location='header' AND is_active=1 ORDER BY id DESC LIMIT 1")[0]['logo_path'] ?? '', 'logo', '');

// ---- 3. Nav Categories ----
$nav_cats = [];
foreach (q("SELECT cate_id, categories AS category_name, slug_url AS slug FROM categories WHERE status=1 ORDER BY id ASC") as $c) {
    $subs = q("SELECT categories AS subcat_name, slug_url AS slug FROM sub_categories WHERE cate_id=" . (int)$c['cate_id'] . " AND status=1");
    $nav_cats[$c['slug']] = ['name' => $c['category_name'], 'subs' => $subs];
}

// ---- 4. Dynamic SEO (Meta Title, Description, Keywords, OG Image, Schema) ----
$current_page_name = basename($_SERVER['PHP_SELF'] ?? 'index.php');
$is_404 = (http_response_code() === 404 || $current_page_name === '404.php');
$slug_param = trim((string)($_GET['slug'] ?? $_GET['slug_url'] ?? ''));
$cat_param  = trim((string)($_GET['category'] ?? ''));

$site_full_name = trim(($brand['name'] ?? 'Huchol') . ' ' . ($brand['suffix'] ?? 'Agro Traders'));
$default_title  = $site_full_name . ' | Wholesale Dry Fruits, Fresh Produce & Spices';
$default_desc   = 'Bulk supply of dry fruits, fresh fruits, vegetables and spices for wholesalers, hotels, retailers and exporters.';
$default_key    = 'dry fruits wholesale, fresh vegetables supplier, spices exporter india';

$og_type  = $og_type  ?? 'website';
$og_image = $og_image ?? '';
$auto_schema_data = null;

if ($is_404) {
    $noindex    = true;
    $meta_title = $meta_title ?? ('404 - Page Not Found | ' . $site_full_name);
    $meta_desc  = $meta_desc  ?? 'The page you are looking for could not be found. Browse wholesale dry fruits, fresh produce and spices.';
    $meta_key   = $meta_key   ?? $default_key;
    $schema     = '';
} elseif (empty($meta_title)) {
    $meta_title = $default_title;
    $meta_desc  = $default_desc;
    $meta_key   = $default_key;
    $schema     = $schema ?? '';

    if (!empty($ok) && isset($conn)) {
        $esc_slug = $conn->real_escape_string($slug_param);

        // A. Category page SEO
        if ($current_page_name === 'category.php' && $slug_param !== '') {
            $row = q("SELECT categories, meta_title, meta_desc, meta_key, schema_markup, image FROM categories WHERE slug_url='$esc_slug' AND status=1 LIMIT 1")[0] ?? [];
            if ($row) {
                $meta_title = ($row['meta_title'] ?: ($row['categories'] . ' Wholesale Supplier | ' . $site_full_name));
                $meta_desc  = ($row['meta_desc']  ?: $default_desc);
                $meta_key   = ($row['meta_key']   ?: $default_key);
                $schema     = $row['schema_markup'] ?? '';
                if (!empty($row['image'])) $og_image = img($row['image'], 'category', '');
                $auto_schema_data = ['type' => 'category', 'row' => $row];
            }
        }
        // B. Product details page SEO
        elseif ($current_page_name === 'product-details.php' && $slug_param !== '') {
            $row = q("SELECT pro_name, short_desc, pro_img, meta_title, meta_desc, meta_key, schema_markup FROM products WHERE slug_url='$esc_slug' AND is_disabled=0 LIMIT 1")[0] ?? [];
            if ($row) {
                $meta_title = ($row['meta_title'] ?: ($row['pro_name'] . ' Bulk Supplier | ' . $site_full_name));
                $meta_desc  = ($row['meta_desc']  ?: trim(strip_tags((string)$row['short_desc'])) ?: $default_desc);
                $meta_key   = ($row['meta_key']   ?: $default_key);
                $schema     = $row['schema_markup'] ?? '';
                $first_img  = preg_split('/[,|;"]+/', trim(stripslashes((string)($row['pro_img'] ?? '')), '[]" '))[0] ?? '';
                if ($first_img !== '') $og_image = img($first_img, 'products', '');
                $auto_schema_data = ['type' => 'product', 'row' => $row];
            }
        }
        // C. Blog details page SEO
        elseif ($current_page_name === 'blog-details.php' && $slug_param !== '') {
            $row = q("SELECT title, author, image, meta_title, meta_desc, meta_key, schema_markup, created_at FROM blogs WHERE slug='$esc_slug' AND status=1 LIMIT 1")[0] ?? [];
            if ($row) {
                $og_type    = 'article';
                $meta_title = ($row['meta_title'] ?: ($row['title'] . ' | ' . $site_full_name));
                $meta_desc  = ($row['meta_desc']  ?: $default_desc);
                $meta_key   = ($row['meta_key']   ?: $default_key);
                $schema     = $row['schema_markup'] ?? '';
                if (!empty($row['image'])) $og_image = img($row['image'], 'blogs', '');
                $auto_schema_data = ['type' => 'blog', 'row' => $row];
            }
        }
        // D. Custom dynamic page SEO
        elseif ($current_page_name === 'page.php' && $slug_param !== '') {
            $row = q("SELECT page_title, meta_title, meta_desc, meta_key, schema_markup FROM custom_pages WHERE slug_url='$esc_slug' AND status=1 LIMIT 1")[0] ?? [];
            if ($row) {
                $meta_title = ($row['meta_title'] ?: ($row['page_title'] . ' | ' . $site_full_name));
                $meta_desc  = ($row['meta_desc']  ?: $default_desc);
                $meta_key   = ($row['meta_key']   ?: $default_key);
                $schema     = $row['schema_markup'] ?? '';
            }
        }
        // E. Standard pages (meta + page_schemas tables)
        else {
            $pg = $conn->real_escape_string($current_page_name);
            $md = q("SELECT meta_title, meta_desc, meta_key FROM meta WHERE page_url='$pg' LIMIT 1")[0] ?? [];
            $meta_title = ($md['meta_title'] ?? '') ?: $meta_title;
            $meta_desc  = ($md['meta_desc'] ?? '') ?: $meta_desc;
            $meta_key   = ($md['meta_key'] ?? '') ?: $meta_key;
            $schema     = q("SELECT schema_markup FROM page_schemas WHERE page_url='$pg' LIMIT 1")[0]['schema_markup'] ?? '';
        }
    }
}
$schema = trim((string)($schema ?? ''));

// ---- 5. Clean Canonical URL Builder (No .php, No duplicate ?slug=) ----
if (!$is_404 && !isset($canonical_url)) {
    if ($current_page_name === 'index.php') {
        $canonical_url = $seo_base;
    } elseif ($current_page_name === 'category.php' && $slug_param !== '') {
        $canonical_url = $seo_base . 'category/' . rawurlencode($slug_param);
    } elseif ($current_page_name === 'product-details.php' && $slug_param !== '') {
        $canonical_url = $seo_base . 'product/' . rawurlencode($slug_param);
    } elseif ($current_page_name === 'blog-details.php' && $slug_param !== '') {
        $canonical_url = $seo_base . 'blog/' . rawurlencode($slug_param);
    } elseif ($current_page_name === 'page.php' && $slug_param !== '') {
        $canonical_url = $seo_base . 'page/' . rawurlencode($slug_param);
    } elseif ($current_page_name === 'products.php' && $cat_param !== '') {
        $canonical_url = $seo_base . 'products/category/' . rawurlencode($cat_param);
    } else {
        $clean_name = preg_replace('/\.php$/i', '', $current_page_name);
        $canonical_url = $seo_base . rawurlencode($clean_name);
    }
}

// Convert relative OG image to absolute URL for social/schema previews
if ($og_image !== '' && !preg_match('#^https?://#i', $og_image)) {
    $og_image = $seo_base . ltrim($og_image, '/');
}

// ---- 6. Auto Fallback JSON-LD Schema if DB schema_markup is empty ----
if (!$is_404 && $schema === '') {
    $json_flags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
    if ($auto_schema_data && $auto_schema_data['type'] === 'product') {
        $r = $auto_schema_data['row'];
        $sd = [
            '@context'    => 'https://schema.org',
            '@type'       => 'Product',
            'name'        => $r['pro_name'],
            'description' => $meta_desc,
            'url'         => $canonical_url,
            'brand'       => ['@type' => 'Brand', 'name' => $site_full_name],
        ];
        if ($og_image !== '') $sd['image'] = $og_image;
        $schema = '<script type="application/ld+json">' . json_encode($sd, $json_flags) . '</script>';
    } elseif ($auto_schema_data && $auto_schema_data['type'] === 'blog') {
        $r = $auto_schema_data['row'];
        $sd = [
            '@context'         => 'https://schema.org',
            '@type'            => 'BlogPosting',
            'headline'         => $r['title'],
            'description'      => $meta_desc,
            'datePublished'    => date('c', strtotime($r['created_at'] ?: 'now')),
            'author'           => ['@type' => 'Organization', 'name' => $r['author'] ?: $site_full_name],
            'publisher'        => ['@type' => 'Organization', 'name' => $site_full_name],
            'mainEntityOfPage' => $canonical_url,
        ];
        if ($og_image !== '') $sd['image'] = $og_image;
        $schema = '<script type="application/ld+json">' . json_encode($sd, $json_flags) . '</script>';
    } elseif ($auto_schema_data && $auto_schema_data['type'] === 'category') {
        $r = $auto_schema_data['row'];
        $sd = [
            '@context'    => 'https://schema.org',
            '@type'       => 'CollectionPage',
            'name'        => $r['categories'],
            'description' => $meta_desc,
            'url'         => $canonical_url,
        ];
        $schema = '<script type="application/ld+json">' . json_encode($sd, $json_flags) . '</script>';
    } else {
        $sd = [
            '@context'    => 'https://schema.org',
            '@type'       => 'Organization',
            'name'        => $site_full_name,
            'url'         => $seo_base,
            'email'       => $mail,
            'telephone'   => '+' . $tel,
            'description' => $default_desc,
        ];
        if (!empty($ct['address'])) {
            $sd['address'] = [
                '@type'          => 'PostalAddress',
                'streetAddress'  => $ct['address'],
                'addressCountry' => 'IN',
            ];
        }
        $schema = '<script type="application/ld+json">' . json_encode($sd, $json_flags) . '</script>';
    }
} elseif ($schema !== '' && stripos($schema, '<script') === false) {
    // Agar admin ne bina <script> tag ke raw JSON paste kiya ho to safely wrap karo
    $schema = '<script type="application/ld+json">' . $schema . '</script>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Base URL fixes CSS, JS, images & links on clean nested URLs (/category/..., /product/..., /blog/...) -->
    <base href="<?= e($base_url) ?>">

    <title><?= e($meta_title) ?></title>
    <meta name="description" content="<?= e($meta_desc) ?>">
    <meta name="keywords" content="<?= e($meta_key) ?>">

    <?php if ($is_404): ?>
        <meta name="robots" content="noindex,nofollow">
    <?php elseif (!empty($noindex)): ?>
        <meta name="robots" content="noindex,follow">
    <?php else: ?>
        <meta name="robots" content="index,follow">
    <?php endif; ?>

    <?php if (!$is_404 && !empty($canonical_url)): ?>
        <link rel="canonical" href="<?= e($canonical_url) ?>">
    <?php endif; ?>

    <meta property="og:site_name" content="<?= e($site_full_name) ?>">
    <meta property="og:type" content="<?= e($og_type) ?>">
    <meta property="og:title" content="<?= e($meta_title) ?>">
    <meta property="og:description" content="<?= e($meta_desc) ?>">
    <?php if (!$is_404 && !empty($canonical_url)): ?>
        <meta property="og:url" content="<?= e($canonical_url) ?>">
    <?php endif; ?>
    <?php if ($og_image !== ''): ?>
        <meta property="og:image" content="<?= e($og_image) ?>">
    <?php endif; ?>
    <meta name="twitter:card" content="<?= $og_image !== '' ? 'summary_large_image' : 'summary' ?>">
    <meta name="twitter:title" content="<?= e($meta_title) ?>">
    <meta name="twitter:description" content="<?= e($meta_desc) ?>">

    <?php if ($schema !== '') echo $schema . "\n"; ?>

    <link rel="icon" href="assets/img/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind:wght@400;500;600;700&family=Yatra+One&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('assets/style/style.css')) ?>">
    <?php foreach (($extra_css ?? []) as $css): ?><link rel="stylesheet" href="<?= e(asset($css)) ?>"><?php endforeach; ?>
</head>
<body>
<a class="skip" href="<?= e($_SERVER['REQUEST_URI'] ?? './') ?>#main">Skip to content</a>

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
                <li><a href="about">About</a></li>
                <li class="has-dropdown">
                    <a href="products">Products <i class="fa-solid fa-chevron-down dd-icon"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="products" class="all">View all products</a></li>
                        <?php foreach ($nav_cats as $slug => $cd): ?>
                        <li class="<?= $cd['subs'] ? 'has-sub-dropdown' : '' ?>">
                            <a href="category/<?= e($slug) ?>"><?= e($cd['name']) ?></a>
                            <?php if ($cd['subs']): ?>
                            <ul class="sub-dropdown-menu">
                                <?php foreach ($cd['subs'] as $sub): ?>
                                <li><a href="category/<?= e($slug) ?>?sub=<?= e($sub['slug']) ?>"><?= e($sub['subcat_name']) ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <!-- <li><a href="service">Services</a></li> -->
                <li><a href="blog">Insights</a></li>
            </ul>
            <a href="contact" class="btn">Get bulk quote</a>
        </nav>
    </div>
</header>