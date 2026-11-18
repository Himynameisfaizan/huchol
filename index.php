<?php
// Header = DB connect + SEO + <head> + topbar + navbar (body yahin se khulti hai, footer.php band karta hai)
include_once 'includes/header.php';

$CC  = ['#B7791F', '#C8281E', '#2E7D4F', '#8A3B12', '#17543A', '#6B4E16'];   // category tile colours
$FCI = ['photo-1508061253366-f7da158b6d46', 'photo-1596040033229-a9821ebd058d', 'photo-1567306226416-28f0efdc88ce', 'photo-1540420773420-3366772f4999'];

// ---- Hero + About ----
$B = q("SELECT banner_path FROM banners ORDER BY display_order ASC, id DESC LIMIT 1");
$hero = img($B[0]['banner_path'] ?? '', 'banners', un('photo-1596040033229-a9821ebd058d', 900));
$A = q("SELECT title, content, image_url FROM about_sections ORDER BY section_order ASC, id ASC LIMIT 1");
$about_t = txt($A[0]['title'] ?? '') ?: 'Bought at the mandi, checked before it leaves';
$about_c = txt($A[0]['content'] ?? '');
$about_i = img($A[0]['image_url'] ?? '', 'about_us', un('photo-1586528116311-ad8dd3c8310d', 900));

// ---- Categories: DB first, then fill the four core aisles that are missing ----
$FC = [
    ['Dry Fruits & Nuts', 'dry-fruits-nuts', 'Almonds, cashews, raisins, pistachios and walnuts, graded and packed.', 'photo-1508061253366-f7da158b6d46'],
    ['Fresh Fruits',      'fresh-fruits',    'Seasonal mandi fruit sorted by size and ripeness, packed in crates.', 'photo-1567306226416-28f0efdc88ce'],
    ['Fresh Vegetables',  'fresh-vegetables', 'Everyday and leafy vegetables for hotels, caterers and retail.', 'photo-1540420773420-3366772f4999'],
    ['Spices',            'spices',          'Whole and ground spices, cleaned and packed in 5 to 50 kg bags.', 'photo-1596040033229-a9821ebd058d'],
];
$cats = [];
$seen = [];
foreach (q("SELECT categories, slug_url, image FROM categories WHERE status=1 ORDER BY id ASC") as $i => $r) {
    $cats[] = [$r['categories'], $r['slug_url'], 'Bulk supply, graded and packed to your specification.', img($r['image'], 'category', un($FCI[$i % 4], 700))];
    $seen[strtolower($r['categories'])] = 1;
}
foreach ($FC as $f) if (!isset($seen[strtolower($f[0])])) $cats[] = [$f[0], $f[1], $f[2], un($f[3], 700)];

// ---- Products ----
$FP = [
    ['Premium Almonds', 'Dry Fruits & Nuts', 'dry-fruits-nuts', 'photo-1508061253366-f7da158b6d46'],
    ['Seasonal Fruit Crates', 'Fresh Fruits', 'fresh-fruits', 'photo-1567306226416-28f0efdc88ce'],
    ['Farm Vegetables', 'Fresh Vegetables', 'fresh-vegetables', 'photo-1540420773420-3366772f4999'],
    ['Dried Red Chilli', 'Spices', 'spices', 'photo-1588252303782-cb80119abd6d'],
];
$P = [];
foreach (q("SELECT p.pro_name, p.pro_img, p.slug_url, c.categories AS cat, c.slug_url AS cslug FROM products p LEFT JOIN categories c ON c.cate_id = p.pro_cate WHERE p.is_disabled = 0 ORDER BY p.id DESC LIMIT 8") as $i => $r) {
    if (txt($r['pro_name']) === '') continue;
    $f = preg_split('/[,|;"]+/', trim(stripslashes((string)$r['pro_img']), '[]" '))[0];
    $P[] = ['n' => txt($r['pro_name']), 'cat' => $r['cat'] ?: 'Products', 'cs' => $r['cslug'] ?: 'all', 'img' => img($f, 'products', un($FP[$i % 4][3], 700)), 'slug' => $r['slug_url'] ?? ''];
}
if (count($P) < 4) {
    $P = [];
    foreach ($FP as $x) $P[] = ['n' => $x[0], 'cat' => $x[1], 'cs' => $x[2], 'img' => un($x[3], 700), 'slug' => ''];
}
$chips = [];
foreach ($P as $p) $chips[$p['cs']] = $p['cat'];

// ---- Services ----
$FS = [
    ['Bulk sourcing and procurement', 'Direct buying from farms, mandis and processing units for steady quality and fair rates.'],
    ['Grading, sorting and packing', 'Size, moisture and colour agreed first, then packed in cartons, crates, jute or PP bags.'],
    ['Pan-India dispatch', 'Truck and rail dispatch with GST invoice and e-way bill, tracked until delivery.'],
    ['Export and private label', 'Export documents, phytosanitary and COA support, with your own labels on request.'],
];
$S = [];
foreach (q("SELECT service_name, short_desc FROM services ORDER BY id ASC LIMIT 4") as $r) if (txt($r['service_name']) && txt($r['short_desc'])) $S[] = [txt($r['service_name']), txt($r['short_desc'])];
foreach ($FS as $f) if (count($S) < 3) $S[] = $f;

// ---- Testimonials ----
$T = [];
foreach (q("SELECT * FROM testimonials WHERE status=1 ORDER BY display_order ASC, test_id DESC LIMIT 6") as $r) {
    $m = txt($r['message'] ?? '');
    if ($m === '') continue;
    $T[] = [txt($r['name'] ?? '') ?: 'Buyer', implode(', ', array_filter([txt($r['designation'] ?? ''), txt($r['client_company'] ?? '')])), $m, max(1, min(5, (int)($r['rating'] ?? 5))), !empty($r['image']) ? img($r['image'], 'testimonials', '') : ''];
}
if (count($T) < 2) $T = [
    ['Rakesh Gupta', 'Owner, Gupta Dry Fruit Traders', 'Cashew and almond lots matched the sample every time. Packing was neat and the invoice and e-way bill were ready before the truck left.', 5, ''],
    ['Neha Sharma', 'Purchase Head, hotel group', 'Our kitchens need vegetables on a tight schedule. Deliveries have been on time and the grading has stayed consistent.', 5, ''],
];

// ---- Blogs ----
$Bl = q("SELECT title, slug, author, image, meta_desc, description, created_at FROM blogs WHERE status=1 ORDER BY created_at DESC, blog_id DESC LIMIT 3");
?>
<main id="main">

    <section class="hero">
        <div class="wrap">
            <div class="hero-txt">
                <p class="greet"><span class="hi">Wholesale</span> Supply from <?= e($brand['city']) ?></p>
                <h1>Bought at the source. Packed to your spec. Supplied in bulk.</h1>
                <p class="lead">Dry fruits, fresh fruits, vegetables and spices for wholesalers, hotels, retailers and exporters, sourced directly from farms and mandis around <?= e($brand['city']) ?>.</p>
                <div class="cta"><a href="products.php" class="btn">Browse products</a><a href="#rfq" class="btn o">Get bulk rates</a></div>
                <div class="need"><b>I am looking for</b>
                    <?php foreach ($cats as $c): ?><button type="button" data-p="<?= e($c[0]) ?>"><?= e($c[0]) ?></button><?php endforeach; ?>
                </div>
            </div>
            <div class="mos">
                <figure class="m1"><?= pic($hero, 'Dry fruits, fresh produce and spices', true) ?></figure>
                <figure class="m2"><?= pic(un('photo-1567306226416-28f0efdc88ce', 600), 'Fresh fruit', true) ?></figure>
                <figure class="m3"><?= pic(un('photo-1540420773420-3366772f4999', 600), 'Fresh vegetables', true) ?></figure>
                <div class="stamp">Direct from farms &amp; mandis</div>
            </div>
        </div>
    </section>

    <section class="trust">
        <div class="wrap">
            <div class="tr-grid">
                <div>
                    <h3>Direct sourcing</h3>
                    <p>Farms, mandis and processing units, with no extra layers.</p>
                </div>
                <div>
                    <h3>Graded to spec</h3>
                    <p>Size, moisture and colour agreed before packing.</p>
                </div>
                <div>
                    <h3>Packed for the route</h3>
                    <p>Cartons, crates, jute and PP bags, vacuum packs.</p>
                </div>
                <div>
                    <h3>Domestic and export</h3>
                    <p>GST invoice, e-way bill, phytosanitary and COA on request.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="sec" id="categories">
        <div class="wrap">
            <div class="sh">
                <h2>Four aisles, one supplier</h2><span class="hi">Everything on one purchase order</span>
                <p>Dry fruits, fresh fruit, vegetables and spices, so one purchase desk can cover your whole list.</p>
            </div>
            <div class="cat-grid">
                <?php foreach ($cats as $i => $c): ?>
                    <a class="cat" href="category.php?slug=<?= e($c[1]) ?>" style="--c:<?= $CC[$i % 6] ?>">
                        <div class="ph"><?= pic($c[3], $c[0]) ?></div>
                        <div class="bd">
                            <h3><?= e($c[0]) ?></h3>
                            <p><?= e($c[2]) ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="sec alt" id="about">
        <div class="wrap ab">
            <div class="pics">
                <div class="p1"><?= pic($about_i, 'Our warehouse and packing area') ?></div>
                <div class="p2"><?= pic(un('photo-1596040033229-a9821ebd058d', 500), 'Spices') ?></div>
            </div>
            <div>
                <div class="sh">
                    <h2><?= e($about_t) ?></h2><span class="hi">Who we are and how we buy</span>
                </div>
                <p class="abt"><?= e($about_c ? mb_strimwidth($about_c, 0, 460, '...') : 'We buy dry fruits, fruit, vegetables and spices at the source, sort them by grade and pack them for the buyer’s route. Every lot is checked before loading, and the paperwork goes with it.') ?></p>
                <ul class="pts">
                    <li><b>Quality checked before loading</b>Moisture, size and appearance are verified against the agreed sample.</li>
                    <li><b>Packing that suits your trade</b>10, 25 and 50 kg packs, crates, or your own private label.</li>
                    <li><b>Clean paperwork</b>GST invoice, e-way bill and export documents handled by our desk.</li>
                </ul>
                <a href="about.php" class="btn d">About us</a>
            </div>
        </div>
    </section>

    <section class="sec" id="products">
        <div class="wrap">
            <div class="sh">
                <h2>Fresh in the catalogue</h2><span class="hi">Stocked and ready to quote</span>
                <p>Ask for the day’s rate. Prices move with the mandi, so we quote against your quantity and grade.</p>
            </div>
            <div class="chips" role="group" aria-label="Filter products">
                <button type="button" data-f="all" aria-pressed="true">All</button>
                <?php foreach ($chips as $cs => $cn): ?><button type="button" data-f="<?= e($cs) ?>" aria-pressed="false"><?= e($cn) ?></button><?php endforeach; ?>
            </div>
            <div class="pg-grid">
                <?php foreach ($P as $p): $u = $p['slug'] ? 'product-details.php?slug=' . urlencode($p['slug']) : 'products.php'; ?>
                    <article class="pc" data-c="<?= e($p['cs']) ?>">
                        <a class="ph" href="<?= e($u) ?>"><?= pic($p['img'], $p['n']) ?></a>
                        <div class="bd">
                            <small class="tag"><?= e($p['cat']) ?></small>
                            <h3><a href="<?= e($u) ?>"><?= e($p['n']) ?></a></h3>
                            <p>Graded and packed for wholesale. Rate and MOQ on request.</p>
                            <button type="button" class="pc-btn" data-p="<?= e($p['n']) ?>">Request bulk rate</button>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="sec dk" id="process">
        <div class="wrap">
            <div class="sh">
                <h2>From your enquiry to your godown</h2><span class="hi">How an order moves</span>
            </div>
            <ol class="steps">
                <li class="st"><span class="n">1</span>
                    <h3>Tell us what you need</h3>
                    <p>Product, grade, quantity and delivery city, by form or WhatsApp.</p>
                </li>
                <li class="st"><span class="n">2</span>
                    <h3>Sample and rate</h3>
                    <p>A sample and a written rate for the agreed specification.</p>
                </li>
                <li class="st"><span class="n">3</span>
                    <h3>Grade, pack, inspect</h3>
                    <p>Sorting, moisture and quality checks, then packing as agreed.</p>
                </li>
                <li class="st"><span class="n">4</span>
                    <h3>Dispatch and paperwork</h3>
                    <p>Truck, rail or sea, with GST invoice, e-way bill and export documents.</p>
                </li>
            </ol>
        </div>
    </section>

    <section class="sec" id="services">
        <div class="wrap">
            <div class="sh">
                <h2>What our trade desk handles</h2><span class="hi">Sourcing to dispatch, under one roof</span>
            </div>
            <div class="sv-grid">
                <?php foreach ($S as $s): ?>
                    <div class="sv">
                        <h3><?= e($s[0]) ?></h3>
                        <p><?= e(mb_strimwidth($s[1], 0, 170, '...')) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="sec alt" id="testimonials">
        <div class="wrap">
            <div class="sh">
                <h2>What our buyers say</h2><span class="hi">Feedback from trade buyers</span>
            </div>
            <div class="tc-scroll" tabindex="0">
                <?php foreach ($T as $t): ?>
                    <figure class="tc">
                        <span class="stars" aria-label="<?= $t[3] ?> out of 5"><?= str_repeat('★', $t[3]) ?></span>
                        <blockquote><?= e($t[2]) ?></blockquote>
                        <figcaption class="au">
                            <?= $t[4] ? '<img src="' . e($t[4]) . '" alt="" onerror="this.style.display=\'none\'">' : '<i>' . e(strtoupper(mb_substr($t[0], 0, 1))) . '</i>' ?>
                            <div><b><?= e($t[0]) ?></b><?php if ($t[1]): ?><small><?= e($t[1]) ?></small><?php endif; ?></div>
                        </figcaption>
                    </figure>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php if ($Bl): ?>
        <section class="sec" id="blogs">
            <div class="wrap">
                <div class="bh">
                    <div class="sh">
                        <h2>Buying guides and trade notes</h2><span class="hi">Know the market before you buy</span>
                    </div><a href="blog.php" class="btn o d">All articles</a>
                </div>
                <div class="bg-grid">
                    <?php foreach ($Bl as $b):
                        $bi = img($b['image'], 'blogs', un('photo-1596040033229-a9821ebd058d', 700));
                        $ex = txt($b['meta_desc'], 125) ?: txt($b['description'], 125);
                        $u = 'blog-details.php?slug=' . urlencode($b['slug']); ?>
                        <article class="bl">
                            <a class="im" href="<?= e($u) ?>"><?= pic($bi, $b['title']) ?></a>
                            <small class="meta"><?= e(date('d M Y', strtotime($b['created_at'] ?: 'now'))) ?> by <?= e($b['author']) ?></small>
                            <h3><a href="<?= e($u) ?>"><?= e($b['title']) ?></a></h3>
                            <p><?= e($ex) ?></p>
                            <a class="rd" href="<?= e($u) ?>">Read the guide</a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="sec rq" id="rfq">
        <div class="wrap rg">
            <div>
                <div class="sh">
                    <h2>Get today’s bulk rate</h2><span class="hi">Share your requirement</span>
                    <p>Tell us the product, grade, quantity and where it should go. Our trade desk replies with a written rate within business hours.</p>
                </div>
                <ul class="pts">
                    <li><b>Samples before you commit</b>Representative samples by courier or hand delivery.</li>
                    <li><b>Quality check before loading</b>Photos and checks on request, third-party inspection if you need it.</li>
                    <li><b>Private label and custom packing</b>Your brand, your pack size.</li>
                </ul>
            </div>
            <form class="fm" action="contact.php" method="POST">
                <h3>Bulk enquiry</h3>
                <label>Contact person *<input name="name" placeholder="Rakesh Gupta" autocomplete="name" required></label>
                <label>Company *<input name="company" placeholder="Gupta Dry Fruit Traders" autocomplete="organization" required></label>
                <label>Email *<input type="email" name="email" placeholder="you@company.com" autocomplete="email" required></label>
                <label>WhatsApp / phone *<input type="tel" name="phone" placeholder="+91 98765 43210" autocomplete="tel" required></label>
                <label>Product *<input id="pi" name="subject" placeholder="Almonds, red chilli, tomatoes..." required></label>
                <label>Order size<select name="quantity">
                        <option>Trial order (100 to 500 kg)</option>
                        <option>1 to 5 MT</option>
                        <option>Full truck load (about 10 MT)</option>
                        <option>Container load (export)</option>
                        <option>Monthly repeat requirement</option>
                    </select></label>
                <label class="w">Delivery city or port<input name="destination" placeholder="Mumbai / Jebel Ali, UAE"></label>
                <label class="w">Specifications *<textarea name="message" rows="3" placeholder="Grade, size, pack (10 / 25 / 50 kg), rate basis (ex-Delhi, FOB, CIF)..." required></textarea></label>
                <div class="w"><button type="submit" class="btn">Request rate and sample</button></div>
            </form>
        </div>
    </section>

</main>
<?php include 'includes/footer.php'; ?>