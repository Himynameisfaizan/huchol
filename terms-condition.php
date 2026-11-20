<?php
$meta_title = 'Terms & Conditions | Huchol Agro Traders';
$meta_desc  = 'Commercial terms and conditions governing wholesale enquiries, quotations, supply and website use for Huchol Agro Traders.';
$meta_key   = 'terms and conditions, wholesale trade terms, huchol agro traders, bulk supply terms';

include_once 'includes/header.php';

$brand_full = $site_full_name ?? trim(($brand['name'] ?? 'Huchol') . ' ' . ($brand['suffix'] ?? 'Agro Traders'));
$c_address  = $ct['address'] ?? '';
?>

<main id="main">

    <section class="pghead nb">
        <div class="wrap">
            <ol class="crumbs">
                <li><a href="./">Home</a></li>
                <li>Terms &amp; Conditions</li>
            </ol>
            <h1>Terms &amp; Conditions</h1>
            <p class="lead">Commercial terms governing website usage, wholesale quotations, lot specifications and supply by <?= e($brand_full) ?>.</p>
            <div class="pills">
                <span>Last updated: <?= e(date('d M Y')) ?></span>
                <span>Commercial Trade Terms</span>
            </div>
        </div>
    </section>

    <section class="sec art-sec">
        <div class="wrap art">

            <!-- Left Column: Policy Content -->
            <article class="prose" style="background:#fff;border:1px solid var(--line);border-radius:var(--r);padding:clamp(22px,4vw,42px);">
                <p>By accessing this website or requesting a wholesale quotation from <strong><?= e($brand_full) ?></strong>, you agree to the following Terms &amp; Conditions. Specific commercial terms stated on a signed purchase order or proforma invoice will take precedence over general website terms where they differ.</p>

                <h2>1. Scope of Business</h2>
                <p><?= e($brand_full) ?> operates as a B2B wholesale supplier of dry fruits, fresh fruits, fresh vegetables and spices. Product listings, specifications and buying guides on this website are provided for commercial reference and enquiry purposes.</p>

                <h2>2. Quotations, Rates &amp; Validity</h2>
                <ul>
                    <li>Agricultural commodity rates fluctuate daily with mandi arrivals, crop seasons and freight costs.</li>
                    <li>Written quotations are valid only for the validity window stated on the quotation and are subject to stock availability at confirmation.</li>
                    <li>An order is treated as confirmed only upon written purchase order acceptance and receipt of the agreed advance or payment instrument.</li>
                </ul>

                <h2>3. Natural Variations in Agro Produce</h2>
                <p>Dry fruits, fresh fruits, vegetables and whole spices are natural agricultural products. Minor batch-to-batch variations in shade, aroma, kernel count or seasonal availability can occur across crops and regions and are normal characteristics of natural produce, provided the lot conforms to the agreed written grade and sample tolerance.</p>

                <h2>4. Taxes, Duties &amp; Documentation</h2>
                <p>Applicable GST, mandi cess (where relevant) and freight charges are specified separately on quotations and invoices. For export orders, destination customs duties, import permits, port demurrage and local taxes remain the buyer’s sole responsibility unless expressly agreed otherwise in writing.</p>

                <h2>5. Samples &amp; Third-Party Inspection</h2>
                <p>Representative samples may be provided prior to bulk confirmation. Buyers are welcome to arrange pre-dispatch inspection (in person or through a nominated third-party agency) at our packing facility before loading.</p>

                <h2>6. General Information Disclaimer</h2>
                <p>Buying guides, grade explainers and product notes published on our website are intended strictly for general trade and culinary reference and do not constitute medical, nutritional or legal advice.</p>

                <h2>7. Limitation of Liability &amp; Force Majeure</h2>
                <p><?= e($brand_full) ?> shall not be liable for indirect or consequential losses, or for delays caused by events beyond reasonable control, including extreme weather, crop failure, transport strikes, port congestion or government trade restrictions. Where any claim is established, our maximum liability shall not exceed the invoice value of the affected lot.</p>

                <h2>8. Governing Law &amp; Jurisdiction</h2>
                <p>All transactions, invoices and disputes arising out of website use or commercial supply shall be governed by the laws of India, subject to the jurisdiction of the competent courts in Delhi, India.</p>

                <h2>9. Contact Information</h2>
                <p>For clarifications regarding these terms or commercial agreements, contact our trade desk:</p>

                <div style="background:var(--alt);border-left:4px solid var(--gold);border-radius:var(--r);padding:18px 20px;margin-top:16px;">
                    <p style="margin-bottom:6px;"><strong><?= e($brand_full) ?></strong></p>
                    <?php if ($c_address): ?>
                        <p style="margin-bottom:6px;"><strong>Address:</strong> <?= e($c_address) ?></p>
                    <?php endif; ?>
                    <p style="margin-bottom:6px;"><strong>Phone / WhatsApp:</strong> <a href="tel:+<?= e($tel) ?>"><?= e($disp_phone) ?></a></p>
                    <p style="margin-bottom:6px;"><strong>Email:</strong> <a href="mailto:<?= e($mail) ?>"><?= e($mail) ?></a></p>
                    <?php if ($hours): ?>
                        <p style="margin-bottom:0;"><strong>Hours:</strong> <?= e($hours) ?></p>
                    <?php endif; ?>
                </div>
            </article>

            <!-- Right Column: Sticky Sidebar -->
            <aside class="asd">
                <div class="sbox">
                    <h3>Search Catalogue</h3>
                    <form action="products" method="GET" class="sbar" role="search">
                        <input type="search" name="q" placeholder="Search almonds, chilli, onions..." aria-label="Search products" required>
                        <button type="submit" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                </div>

                <div class="sbox">
                    <h3>Policies &amp; Legal</h3>
                    <ul>
                        <li><a href="privacy-policy">Privacy Policy</a></li>
                        <li><a href="refund-policy">Refund &amp; Cancellation</a></li>
                        <li><a href="shipping-policy">Shipping &amp; Delivery</a></li>
                        <li><a href="terms-and-conditions" class="on">Terms &amp; Conditions</a></li>
                    </ul>
                </div>

                <?php if (!empty($nav_cats)): ?>
                <div class="sbox">
                    <h3>Wholesale Aisles</h3>
                    <ul>
                        <?php foreach ($nav_cats as $slug => $cd): ?>
                            <li><a href="category/<?= e($slug) ?>"><?= e($cd['name']) ?> <small>&rarr;</small></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <div class="sbox dkc">
                    <h3>Ready to place an enquiry?</h3>
                    <p>Get a written rate, sample plan and dispatch timeline from our wholesale desk.</p>
                    <a href="contact" class="btn" style="width:100%;margin-bottom:10px;">Request bulk quote</a>
                    <a href="https://wa.me/<?= e($wa) ?>?text=<?= rawurlencode('Hello ' . $brand_full . ', I would like to request a bulk quotation.') ?>" target="_blank" rel="noopener" class="btn green" style="width:100%;margin-bottom:12px;">
                        <i class="fa-brands fa-whatsapp"></i> WhatsApp desk
                    </a>
                    <p style="margin:0;font-size:.9rem;"><a href="tel:+<?= e($tel) ?>"><i class="fa-solid fa-phone" style="color:var(--gold);margin-right:6px;"></i><?= e($disp_phone) ?></a></p>
                </div>
            </aside>

        </div>
    </section>

    <section class="sec cbs">
        <div class="wrap">
            <div class="cband">
                <div>
                    <h2>Looking for a steady wholesale supply partner?</h2>
                    <p>Send us your product list, required grades and delivery city for today’s rate.</p>
                </div>
                <div class="cta">
                    <a href="contact" class="btn d">Get bulk quote</a>
                    <a href="https://wa.me/<?= e($wa) ?>" target="_blank" rel="noopener" class="btn green"><i class="fa-brands fa-whatsapp"></i> WhatsApp us</a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include 'includes/footer.php'; ?>