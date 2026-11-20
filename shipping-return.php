<?php
$meta_title = 'Shipping & Delivery Policy | Huchol Agro Traders';
$meta_desc  = 'Pan-India truck and rail dispatch, mandi delivery schedules, packaging standards and export freight terms from Huchol Agro Traders.';
$meta_key   = 'shipping policy, delivery policy, pan india bulk dispatch, export shipping, huchol agro traders';

include_once 'includes/header.php';

$brand_full = $site_full_name ?? trim(($brand['name'] ?? 'Huchol') . ' ' . ($brand['suffix'] ?? 'Agro Traders'));
$c_address  = $ct['address'] ?? '';
?>

<main id="main">

    <section class="pghead nb">
        <div class="wrap">
            <ol class="crumbs">
                <li><a href="./">Home</a></li>
                <li>Shipping &amp; Delivery Policy</li>
            </ol>
            <h1>Shipping &amp; Delivery Policy</h1>
            <p class="lead">How we pack, document and dispatch bulk dry fruits, fresh produce and spices across India and overseas.</p>
            <div class="pills">
                <span>Last updated: <?= e(date('d M Y')) ?></span>
                <span>Pan-India &amp; Export Logistics</span>
            </div>
        </div>
    </section>

    <section class="sec art-sec">
        <div class="wrap art">

            <!-- Left Column: Policy Content -->
            <article class="prose" style="background:#fff;border:1px solid var(--line);border-radius:var(--r);padding:clamp(22px,4vw,42px);">
                <p><strong><?= e($brand_full) ?></strong> coordinates transport, packing and dispatch paperwork from our trade desk so consignments reach your godown, kitchen or port in sound condition.</p>

                <h2>1. Processing &amp; Dispatch Timelines</h2>
                <ul>
                    <li><strong>Fresh Fruits &amp; Vegetables:</strong> Dispatched daily or on agreed weekly schedules for Delhi NCR and regional routes, aligned with agreed morning delivery windows.</li>
                    <li><strong>Dry Fruits, Nuts &amp; Spices (Trial &amp; Standard Bulk):</strong> Graded, checked and dispatched within <strong>2 to 4 business days</strong> of purchase order and payment confirmation.</li>
                    <li><strong>Private Label &amp; Container Exports:</strong> Dispatch schedules are confirmed in writing on the proforma invoice based on packing lead time, inspection and vessel/flight cut-offs.</li>
                </ul>

                <h2>2. Pan-India Domestic Dispatch</h2>
                <p>We dispatch across India via full truck load (FTL), part-load transport, rail parcel and courier (for samples and small trial lots). Typical transit times range from <strong>1 to 7 business days</strong> depending on destination city and transport mode.</p>
                <p>Every domestic consignment travels with a valid <strong>GST invoice</strong> and <strong>e-way bill</strong> (where applicable). Loading photos, LR / bilty copies and transporter contact numbers are shared upon dispatch.</p>

                <h2>3. Packing Standards for the Route</h2>
                <ul>
                    <li><strong>Dry Fruits &amp; Nuts:</strong> 10 kg and 20 kg lined cartons, vacuum packs or tins to guard against moisture.</li>
                    <li><strong>Spices:</strong> 25 kg and 50 kg PP or jute bags, or custom retail pouches for repackers.</li>
                    <li><strong>Fresh Fruits &amp; Vegetables:</strong> Ventilated cartons, plastic crates or mesh bags chosen for firmness and route duration.</li>
                </ul>

                <h2>4. International Shipping &amp; Exports</h2>
                <p>Export consignments are shipped via sea or air freight on agreed Incoterms (such as <strong>Ex-Works, FOB or CIF</strong>). We assist with commercial invoices, packing lists, certificates of origin, phytosanitary certificates and COA where required. Unless otherwise specified in writing, destination-country customs clearance, import duties and local port charges are the buyer’s responsibility.</p>

                <h2>5. Freight Responsibility &amp; Transit Inspection</h2>
                <p>For <strong>ex-Delhi / ex-godown</strong> quotes, freight and transit risk pass to the buyer once the lot is loaded and handed to the nominated transporter. Buyers are advised to inspect bag/carton count, seals and condition at unloading and record any shortage or damage on the delivery receipt (LR/POD) immediately, notifying our desk within 24 hours (fresh produce) or 48 hours (dry fruits and spices).</p>

                <h2>6. Dispatch &amp; Tracking Desk</h2>
                <p>For transport estimates, freight options or dispatch updates, contact our desk:</p>

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
                        <li><a href="shipping-policy" class="on">Shipping &amp; Delivery</a></li>
                        <li><a href="terms-and-conditions">Terms &amp; Conditions</a></li>
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
                    <h3>Check freight to your city</h3>
                    <p>Tell us your product, quantity and destination city or port for a delivered or FOB quotation.</p>
                    <a href="contact" class="btn" style="width:100%;margin-bottom:10px;">Get delivered rate</a>
                    <a href="https://wa.me/<?= e($wa) ?>?text=<?= rawurlencode('Hello ' . $brand_full . ', I need a bulk rate with dispatch details for my city.') ?>" target="_blank" rel="noopener" class="btn green" style="width:100%;margin-bottom:12px;">
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
                    <h2>Planning a full truck load or export shipment?</h2>
                    <p>Our desk handles packing, GST/e-way paperwork, phytosanitary support and transport coordination.</p>
                </div>
                <div class="cta">
                    <a href="contact" class="btn d">Request bulk quote</a>
                    <a href="https://wa.me/<?= e($wa) ?>" target="_blank" rel="noopener" class="btn green"><i class="fa-brands fa-whatsapp"></i> WhatsApp us</a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include 'includes/footer.php'; ?>