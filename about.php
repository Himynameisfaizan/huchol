<?php
// Header include karein
include_once 'includes/header.php';

// ---- Fetch Dynamic About Content ----
$A = q("SELECT * FROM about_sections ORDER BY section_order ASC, id ASC LIMIT 1");
$about_title = txt($A[0]['title'] ?? 'Bought at the source. Packed to your spec.');
// Content HTML format mein show karne ke liye direct variable assign kiya hai
$about_content = $A[0]['content'] ?? '<p>We supply premium quality products across India.</p>';
$about_image = img($A[0]['image_url'] ?? '', 'about_us', un('photo-1586528116311-ad8dd3c8310d', 900));

// ---- Fetch Testimonials for Trust Section ----
$T = [];
foreach (q("SELECT * FROM testimonials WHERE status=1 ORDER BY display_order ASC, test_id DESC LIMIT 4") as $r) {
    $m = txt($r['message'] ?? '');
    if ($m === '') continue;
    $T[] = [txt($r['name'] ?? ''), implode(', ', array_filter([txt($r['designation'] ?? ''), txt($r['client_company'] ?? '')])), $m, max(1, min(5, (int)($r['rating'] ?? 5))), !empty($r['image']) ? img($r['image'], 'testimonials', '') : ''];
}
?>
<main id="main">

    <!-- Inner Page Hero Section -->
    <section class="hero" style="padding: 60px 0; text-align: center;">
        <div class="wrap">
            <div class="hero-txt" style="max-width: 800px; margin: 0 auto;">
                <p class="greet"><span class="hi">About</span> Huchol Agro Traders</p>
                <h1>Sourcing, Grading and Packing with Precision</h1>
                <p class="lead">Learn how we buy directly from farms and mandis, and pack to your exact specifications.</p>
            </div>
        </div>
    </section>

    <!-- Dynamic About Section -->
    <section class="sec" id="about">
        <div class="wrap ab">
            <div class="pics">
                <div class="p1"><?= pic($about_image, 'About Us - Our warehouse and packing') ?></div>
            </div>
            <div>
                <div class="sh">
                    <h2><?= e($about_title) ?></h2><span class="hi">Who we are and how we work</span>
                </div>
                <!-- Database se HTML content yahan print hoga -->
                <div class="abt-content" style="margin-top: 15px; line-height: 1.6;">
                    <?= $about_content ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust Section (Added for customer confidence - Design match with index.php) -->
    <section class="trust sec dk" style="background: var(--bg-alt);">
        <div class="wrap">
            <div class="sh" style="text-align: center; max-width: 600px; margin: 0 auto 40px;">
                <h2>Why buyers trust our supply</h2><span class="hi">Our Core Guarantees</span>
            </div>
            <div class="tr-grid">
                <div>
                    <h3>Direct from Source</h3>
                    <p>We eliminate middlemen by buying directly from farms and processing units for steady quality.</p>
                </div>
                <div>
                    <h3>Strict Quality Checks</h3>
                    <p>Moisture, size, and appearance are strictly verified against the agreed sample before loading.</p>
                </div>
                <div>
                    <h3>Safe Packaging</h3>
                    <p>Your goods are packed securely in cartons, crates, or vacuum bags suitable for transit.</p>
                </div>
                <div>
                    <h3>Clean Paperwork</h3>
                    <p>100% compliant documentation including GST invoices, e-way bills, and export certificates.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Dynamic Testimonials Section -->
    <?php if(!empty($T)): ?>
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
    <?php endif; ?>

</main>
<?php include 'includes/footer.php'; ?>