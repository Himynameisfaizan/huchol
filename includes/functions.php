<?php
/** Shared bootstrap: DB connection, brand config and helpers.
 *  Safe to require BEFORE header.php, so pages can query the DB and set SEO meta first. */
require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../config/site.php';
$ok = isset($conn) && !$conn->connect_error;

function q($sql) { global $conn, $ok; $r = []; if ($ok && ($res = @$conn->query($sql))) while ($x = $res->fetch_assoc()) $r[] = $x; return $r; }
function esc($s) { global $conn, $ok; return $ok ? $conn->real_escape_string((string)$s) : addslashes((string)$s); }
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function txt($s, $n = 0) { $s = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string)$s)))); return $n ? mb_strimwidth($s, 0, $n, '...') : $s; }
function un($id, $w = 800) { return "https://images.unsplash.com/$id?auto=format&fit=crop&w=$w&q=80"; }
function asset($p) { $f = dirname(__DIR__) . '/' . $p; return $p . (is_file($f) ? '?v=' . filemtime($f) : ''); }
function pic($src, $alt, $eager = false) { return '<img src="' . e($src) . '" alt="' . e($alt) . '"' . ($eager ? '' : ' loading="lazy"') . ' onerror="this.style.display=\'none\'">'; }
function img($f, $d, $fb) {
    global $site;
    $f = trim((string)$f);
    if ($f === '') return $fb;
    if (preg_match('#^https?://#', $f)) return $f;
    $c = ltrim($f, '/'); $b = basename($c);
    $root = dirname(__DIR__) . '/'; $base = rtrim((string)$site, '/') . '/';
    foreach ([$c, "admin/$c", "uploads/$d/$b", "admin/uploads/$d/$b", "assets/img/uploads/$d/$b", "assets/images/$b", "admin/assets/img/uploads/$d/$b", "admin/assets/img/uploads/$b"] as $p)
        if (is_file($root . $p)) return $base . $p;
    return $fb;
}
function mark($w = 44) {
    return '<svg viewBox="0 0 48 48" width="' . $w . '" height="' . $w . '" aria-hidden="true"><circle cx="24" cy="24" r="23" fill="#F5A800"/><path d="M11 34C11 22 19 14 37 13c0 14-8 22-20 22-2 0-4-.3-6-1z" fill="#0E3B2A"/><path d="M13 37c6-9 12-14 21-18" stroke="#F5A800" stroke-width="2" fill="none" stroke-linecap="round"/></svg>';
}

// ---- Page helpers ----
function base_url() {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || ($_SERVER['SERVER_PORT'] ?? '') == 443;
    $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
    return ($https ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $dir . '/';
}
function cat_img_id($slug) {
    $m = ['dry-fruits-nuts' => 'photo-1508061253366-f7da158b6d46', 'fresh-fruits' => 'photo-1567306226416-28f0efdc88ce', 'fresh-vegetables' => 'photo-1540420773420-3366772f4999', 'spices' => 'photo-1596040033229-a9821ebd058d'];
    return $m[$slug] ?? 'photo-1596040033229-a9821ebd058d';
}
function img_list($raw) {   // pro_img can be one path, a comma list or a JSON array
    $raw = trim(stripslashes((string)$raw), '[]" ');
    return array_values(array_filter(array_map(function ($x) { return trim($x, " \"'[]"); }, preg_split('/[,|;"]+/', $raw))));
}
function product_img($raw, $cslug, $w = 700) { return img(img_list($raw)[0] ?? '', 'products', un(cat_img_id($cslug), $w)); }
function read_time($html) { return max(1, (int)ceil(str_word_count(strip_tags((string)$html)) / 200)); }
function date_fmt($d) { $t = strtotime((string)$d); return $t ? date('d M Y', $t) : ''; }
function jld($data) { return '<script type="application/ld+json">' . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>'; }
function prose($html) {     // adds ids to h2 (for the contents list) and makes tables scroll on phones
    $toc = []; $i = 0;
    $html = preg_replace_callback('#<h2[^>]*>(.*?)</h2>#is', function ($m) use (&$toc, &$i) {
        $t = txt($m[1]); $id = 's' . (++$i) . '-' . trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($t)), '-');
        $toc[] = [$id, $t]; return '<h2 id="' . $id . '">' . $m[1] . '</h2>';
    }, (string)$html);
    $html = preg_replace(['#<table#i', '#</table>#i'], ['<div class="tw"><table', '</table></div>'], $html);
    return [$html, $toc];
}
function pager($pg, $pages, $params) {
    if ($pages < 2) return '';
    $u = function ($n) use ($params) { if ($n > 1) $params['page'] = $n; else unset($params['page']); return '?' . http_build_query($params); };
    $o = '<nav class="pager" aria-label="Pagination">'; $gap = false;
    if ($pg > 1) $o .= '<a href="' . e($u($pg - 1)) . '" rel="prev" aria-label="Previous page">&lsaquo;</a>';
    for ($i = 1; $i <= $pages; $i++) {
        if ($i == 1 || $i == $pages || abs($i - $pg) <= 1) { $o .= $i == $pg ? '<span class="on" aria-current="page">' . $i . '</span>' : '<a href="' . e($u($i)) . '">' . $i . '</a>'; $gap = false; }
        elseif (!$gap) { $o .= '<span class="dots">&hellip;</span>'; $gap = true; }
    }
    if ($pg < $pages) $o .= '<a href="' . e($u($pg + 1)) . '" rel="next" aria-label="Next page">&rsaquo;</a>';
    return $o . '</nav>';
}