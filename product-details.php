<?php
// Product detail: ?slug=<product slug>
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/sections.php';

$slug = trim($_GET['slug'] ?? '');
$p = null;
if ($slug !== '') {
    $cond = "p.slug_url='" . esc($slug) . "'" . (ctype_digit($slug) ? ' OR p.id=' . (int)$slug : '');
    $p = q("SELECT p.*, c.categories AS cat_name, c.slug_url AS cat_slug, s.categories AS sub_name, s.slug_url AS sub_slug FROM products p LEFT JOIN categories c ON c.cate_id = p.pro_cate LEFT JOIN sub_categories s ON s.id = p.pro_sub_cate WHERE p.is_disabled = 0 AND ($cond) LIMIT 1")[0] ?? null;
}
if (!$p) {
    $notfound = ['Product not found', 'This product is not listed right now. Browse our range or send us your requirement and we will source it.'];
    include __DIR__ . '/includes/not-found.php'; exit;
}
if ($p['slug_url'] !== '' && $p['slug_url'] !== $slug) { header('Location: product/' . urlencode($p['slug_url']), true, 301); exit; }

$name = txt($p['pro_name']);
$cs   = $p['cat_slug'] ?? '';

// Images: pro_img (one, comma list or JSON) + product_images table
$files = img_list($p['pro_img']);
foreach (q("SELECT image_path FROM product_images WHERE product_id=" . (int)$p['id'] . " ORDER BY id") as $r) $files[] = $r['image_path'];
$imgs = []; foreach (array_unique($files) as $f) { $u = img($f, 'products', ''); if ($u !== '') $imgs[] = $u; }
if (!$imgs) $imgs[] = un(cat_img_id($cs), 900);

// Quick facts are read from the specification table inside the description
$facts = [];
if (preg_match_all('#<tr>\s*<td>(.*?)</td>\s*<td>(.*?)</td>\s*</tr>#is', (string)$p['description'], $m, PREG_SET_ORDER))
    foreach ($m as $r) { $k = txt($r[1]); $v = txt($r[2]); if ($k !== '' && $v !== '' && strtolower($k) !== 'product') $facts[] = [$k, $v]; }
$facts = array_slice($facts, 0, 4);
[$body] = prose($p['description'] ?: '<p>' . e(txt($p['short_desc'])) . '</p>');

$related = q("SELECT p.id, p.pro_name, p.pro_img, p.slug_url, p.short_desc, c.categories AS cat, c.slug_url AS cslug FROM products p LEFT JOIN categories c ON c.cate_id = p.pro_cate WHERE p.is_disabled = 0 AND p.id <> " . (int)$p['id'] . " ORDER BY (p.pro_cate = " . (int)$p['pro_cate'] . ") DESC, RAND() LIMIT 4");

// SEO + schema (set before header so the <head> is right)
$faqs       = product_faqs($name);
$meta_title = $p['meta_title'] ?: $name . ' Wholesale Supplier | ' . $brand['name'];
$meta_desc  = $p['meta_desc'] ?: txt($p['short_desc'], 155);
$meta_key   = $p['meta_key'] ?: strtolower($name) . ' wholesale';
$canonical_url = base_url() . 'product/' . urlencode($p['slug_url'] ?: $slug);
$crumbs = [['Home', './'], ['Products', 'category.php']];
if ($p['cat_name']) $crumbs[] = [$p['cat_name'], 'category/' . urlencode($cs)];
$crumbs[] = [$name, null];
$bc = []; foreach ($crumbs as $i => $c) $bc[] = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $c[0], 'item' => $c[1] ? base_url() . ($c[1] === './' ? '' : $c[1]) : $canonical_url];
$schema = jld(['@context' => 'https://schema.org', '@graph' => [
    ['@type' => 'Product', 'name' => $name, 'image' => $imgs, 'description' => txt($p['short_desc'] ?: $p['description'], 300), 'sku' => (string)$p['pro_id'], 'category' => $p['cat_name'], 'brand' => ['@type' => 'Brand', 'name' => $brand['name']]],
    ['@type' => 'BreadcrumbList', 'itemListElement' => $bc], faq_schema($faqs)]]) . ($p['schema_markup'] ?? '');

include __DIR__ . '/includes/header.php';
$waMsg = rawurlencode($brand['wa_msg'] . ' Product: ' . $name);
?>
<div class="crumbbar"><div class="wrap"><?= crumbs($crumbs, 'dk2') ?></div></div>

<main id="main">
<section class="wrap pd">
    <div class="gal">
        <div class="main"><img id="gm" src="<?= e($imgs[0]) ?>" alt="<?= e($name) ?>" onerror="this.style.display='none'"></div>
        <?php if (count($imgs) > 1): ?>
        <div class="thumbs"><?php foreach ($imgs as $i => $u): ?><button type="button" class="<?= $i === 0 ? 'on' : '' ?>" data-thumb="<?= e($u) ?>" aria-label="View image <?= $i + 1 ?>"><img src="<?= e($u) ?>" alt="" loading="lazy"></button><?php endforeach; ?></div>
        <?php endif; ?>
    </div>
    <div class="pdi">
        <?php if ($p['cat_name']): ?><a class="tag" href="category/<?= e(urlencode($cs)) ?>"><?= e($p['cat_name']) ?></a><?php endif; ?>
        <?php if ($p['sub_name']): ?> <a class="tag" href="category/<?= e(urlencode($cs)) ?>&sub=<?= e($p['sub_slug']) ?>"><?= e($p['sub_name']) ?></a><?php endif; ?>
        <h1><?= e($name) ?></h1>
        <p class="sd"><?= e(txt($p['short_desc']) ?: 'Graded and packed for wholesale. Rate and order size on request.') ?></p>
        <?php if ($facts): ?><dl class="kf"><?php foreach ($facts as $f): ?><div><dt><?= e($f[0]) ?></dt><dd><?= e($f[1]) ?></dd></div><?php endforeach; ?></dl><?php endif; ?>
        <div class="cta">
            <a class="btn" href="#enquire">Request bulk rate</a>
            <a class="btn green" target="_blank" rel="noopener" href="https://wa.me/<?= e($wa) ?>?text=<?= $waMsg ?>"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
            <a class="btn o d" href="tel:+<?= e($tel) ?>"><i class="fa-solid fa-phone"></i> Call</a>
        </div>
        <div class="assure">
            <div><i class="fa-solid fa-clipboard-check"></i><span><b>Checked before loading</b>Matched to the agreed sample</span></div>
            <div><i class="fa-solid fa-box-open"></i><span><b>Packed to your spec</b>Cartons, crates, bags, private label</span></div>
            <div><i class="fa-solid fa-file-invoice"></i><span><b>GST invoice, e-way bill</b>With every consignment</span></div>
            <div><i class="fa-solid fa-truck-fast"></i><span><b>Pan-India dispatch</b>Truck and rail, export support</span></div>
        </div>
    </div>
</section>

<section class="sec alt">
    <div class="wrap pdx">
        <div class="prose">
            <?= $body ?>
            <h2>How we supply <?= e($name) ?></h2>
            <ul>
                <li>A sample on request, before you commit to a bulk order</li>
                <li>Packing agreed with you: cartons, crates, bags or your own private label</li>
                <li>GST invoice and e-way bill with every consignment</li>
                <li>Dispatch from <?= e($brand['city']) ?> by truck or rail, with export support on request</li>
            </ul>
        </div>
        <aside class="eq" id="enquire">
            <form class="fm" action="contact.php" method="POST">
                <h3>Request bulk rate</h3>
                <input type="hidden" name="subject" value="Enquiry: <?= e($name) ?>">
                <label>Contact person *<input name="name" placeholder="Your name" autocomplete="name" required></label>
                <label>Company<input name="company" placeholder="Company name" autocomplete="organization"></label>
                <label>Email *<input type="email" name="email" placeholder="you@company.com" autocomplete="email" required></label>
                <label>WhatsApp / phone *<input type="tel" name="phone" placeholder="+91 98765 43210" autocomplete="tel" required></label>
                <label>Order size<select name="quantity"><option>Trial order (100 to 500 kg)</option><option>1 to 5 MT</option><option>Full truck load (about 10 MT)</option><option>Container load (export)</option><option>Monthly repeat requirement</option></select></label>
                <label>Delivery city or port<input name="destination" placeholder="Mumbai / Jebel Ali, UAE"></label>
                <label>Requirement<textarea name="message" rows="3" placeholder="Grade, pack size, rate basis (ex-Delhi, delivered, FOB, CIF)..."></textarea></label>
                <div><button type="submit" class="btn">Request rate and sample</button></div>
                <p class="note">Our trade desk replies with a written rate during business hours (<?= e($hours) ?>).</p>
            </form>
        </aside>
    </div>
</section>

<?php
why_block('What you get with every order', 'The same checks on every lot', $WHY, false);
steps_block();
faq_block('Questions about ' . $name, 'Ordering ' . $name, $faqs);
if ($related): ?>
<section class="sec alt"><div class="wrap">
    <div class="bh"><div class="sh"><h2>You may also need</h2><span class="hi">One purchase order, many lines</span></div><a href="category" class="btn o d">All products</a></div>
    <div class="pg-grid"><?php foreach ($related as $r) echo product_card($r); ?></div>
</div></section>
<?php endif;
cta_band('Want a rate for ' . $name . ' today?', 'Send your quantity, grade and delivery city. We reply with a written rate and a sample plan.', $name);
?>
</main>

<div class="stickbar">
    <a class="btn" href="#enquire">Get bulk rate</a>
    <a class="btn green" target="_blank" rel="noopener" href="https://wa.me/<?= e($wa) ?>?text=<?= $waMsg ?>"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>