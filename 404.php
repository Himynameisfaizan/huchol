<?php
http_response_code(404);

// Header = DB connect + SEO + <head> + topbar + navbar
include_once 'includes/header.php';

// ---- Fetch WhatsApp number dynamically from contacts table ----
$C = q("SELECT wp_number, phone FROM contacts ORDER BY id ASC LIMIT 1");
$wa_raw = $C[0]['wp_number'] ?? $C[0]['phone'] ?? '';
$wa_num = preg_replace('/\D+/', '', (string)$wa_raw);
if (strlen($wa_num) === 10) {
    $wa_num = '91' . $wa_num;
}

// ---- Fetch active categories for quick buyer navigation ----
$CC  = ['#B7791F', '#C8281E', '#2E7D4F', '#8A3B12'];
$FCI = [
    'photo-1508061253366-f7da158b6d46',
    'photo-1567306226416-28f0efdc88ce',
    'photo-1540420773420-3366772f4999',
    'photo-1596040033229-a9821ebd058d'
];
$cats = [];
foreach (q("SELECT categories, slug_url, image FROM categories WHERE status=1 ORDER BY id ASC LIMIT 4") as $i => $r) {
    $cats[] = [
        $r['categories'],
        $r['slug_url'],
        'Bulk supply, graded and packed to your specification.',
        img($r['image'], 'category', un($FCI[$i % 4], 700))
    ];
}
?>

<style>
.err404-hero {
    background: var(--leaf) var(--jaali);
    color: #fff;
    padding: clamp(48px, 7vw, 88px) 0 clamp(56px, 8vw, 96px);
    text-align: center;
}
.err404-box {
    max-width: 720px;
    margin: 0 auto;
}
.err404-num {
    font-family: 'Yatra One', 'Hind', serif;
    font-size: clamp(4.8rem, 12vw, 8rem);
    line-height: 0.95;
    color: var(--gold);
    letter-spacing: 2px;
    margin-bottom: 10px;
}
.err404-box h1 {
    font-size: clamp(1.85rem, 3.8vw, 2.9rem);
    color: #fff;
    margin-bottom: 14px;
}
.err404-box .lead {
    margin: 0 auto 26px;
    max-width: 54ch;
}
.err404-search {
    margin: 0 auto 28px;
    max-width: 520px;
}
.err404-actions {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 12px;
}
.err404-links {
    margin-top: 30px;
    padding-top: 24px;
    border-top: 1px solid rgba(255, 255, 255, .16);
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 10px;
}
.err404-links a {
    border: 1px solid rgba(255, 255, 255, .28);
    border-radius: 999px;
    padding: .35rem 1.05rem;
    font-size: .92rem;
    font-weight: 600;
    color: #cfe0d2;
    transition: .2s;
}
.err404-links a:hover {
    background: var(--gold);
    color: var(--leaf);
    border-color: var(--gold);
}
@media (max-width: 620px) {
    .err404-actions .btn {
        width: 100%;
    }
}
</style>

<main id="main">

    <!-- 404 Hero Section (Huchol Leaf Green + Jaali + Marigold theme) -->
    <section class="err404-hero">
        <div class="wrap">
            <div class="err404-box">
                <p class="greet"><span class="hi">Error 404</span> Page Not Found</p>
                <div class="err404-num" aria-hidden="true">404</div>
                <h1>We couldn’t find that page</h1>
                <p class="lead">The product, guide, or link you’re looking for may have moved or its URL changed. Search our wholesale catalogue below or head straight to our core aisles.</p>

                <!-- Product Search Bar (Note: check products.php uses 'q' or 'search' param) -->
                <form action="products.php" method="GET" class="sbar hs err404-search" role="search">
                    <input type="search" name="q" placeholder="Search bulk products (e.g. Almonds, Red Chilli, Onions)..." aria-label="Search products" required>
                    <button type="submit" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>

                <!-- Primary CTAs -->
                <div class="err404-actions">
                    <a href="index.php" class="btn">Back to Home</a>
                    <a href="products.php" class="btn o">Browse All Products</a>
                    <?php if ($wa_num): ?>
                        <a href="https://wa.me/<?= e($wa_num) ?>?text=<?= rawurlencode('Hello Huchol Agro Traders, I need help finding a product / bulk quotation.') ?>" target="_blank" rel="noopener" class="btn green">
                            <i class="fa-brands fa-whatsapp"></i> WhatsApp Trade Desk
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Quick Links -->
                <div class="err404-links">
                    <a href="about.php">About Us</a>
                    <a href="products.php">All Products</a>
                    <a href="service.php">Services</a>
                    <a href="blog.php">Buying Guides</a>
                    <a href="contact.php">Contact Us</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Dynamic Core Categories (Same layout as index.php) -->
    <?php if (!empty($cats)): ?>
        <section class="sec">
            <div class="wrap">
                <div class="sh">
                    <h2>Explore our wholesale aisles</h2>
                    <span class="hi">Stocked and ready to quote</span>
                    <p>Looking for a specific commodity? Jump directly into one of our four bulk supply categories.</p>
                </div>
                <div class="cat-grid">
                    <?php foreach ($cats as $i => $c): ?>
                        <a class="cat" href="category.php?slug=<?= e($c[1]) ?>" style="--c:<?= $CC[$i % 4] ?>">
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
    <?php endif; ?>

    <!-- Bottom CTA Band -->
    <section class="sec cbs">
        <div class="wrap">
            <div class="cband">
                <div>
                    <h2>Need a bulk rate right away?</h2>
                    <p>Share the product, grade, quantity and delivery city. Our trade desk replies with a written rate and sample plan.</p>
                </div>
                <div class="cta">
                    <a href="contact.php" class="btn d">Request bulk quote</a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include 'includes/footer.php'; ?>