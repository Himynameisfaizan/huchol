<?php
$meta_title = 'Refund & Cancellation Policy | Huchol Agro Traders';
$meta_desc  = 'Read the B2B refund, cancellation, quality claim and replacement policy of Huchol Agro Traders for bulk dry fruits, fresh produce and spices.';
$meta_key   = 'refund policy, cancellation policy, huchol agro traders, bulk order claims';

include_once 'includes/header.php';

$brand_full = $site_full_name ?? trim(($brand['name'] ?? 'Huchol') . ' ' . ($brand['suffix'] ?? 'Agro Traders'));
$c_address  = $ct['address'] ?? '';
?>

<main id="main">

    <section class="pghead nb">
        <div class="wrap">
            <ol class="crumbs">
                <li><a href="./">Home</a></li>
                <li>Refund &amp; Cancellation Policy</li>
            </ol>
            <h1>Refund &amp; Cancellation Policy</h1>
            <p class="lead">Clear terms for bulk order cancellations, lot verification, transit damage claims and credit settlements.</p>
            <div class="pills">
                <span>Last updated: <?= e(date('d M Y')) ?></span>
                <span>Wholesale &amp; Export Orders</span>
            </div>
        </div>
    </section>

    <section class="sec art-sec">
        <div class="wrap art">

            <!-- Left Column: Policy Content -->
            <article class="prose" style="background:#fff;border:1px solid var(--line);border-radius:var(--r);padding:clamp(22px,4vw,42px);">
                <p>At <strong><?= e($brand_full) ?></strong>, every consignment of dry fruits, fresh fruits, fresh vegetables and spices is sourced against agreed specifications and checked prior to loading. Because agricultural commodities are perishable, food-grade and traded against daily mandi rates, the following cancellation, claim and refund terms apply to all wholesale and export orders.</p>

                <h2>1. Order Cancellation Policy</h2>
                <h3>Before Procurement or Packing</h3>
                <p>Standard wholesale orders may be cancelled in writing before lot procurement, grading or packing begins. Any advance received will be adjusted against future orders or refunded after deducting actual bank or administrative charges, if applicable.</p>

                <h3>After Packing or Dispatch</h3>
                <p>Once goods are sorted, packed, loaded or handed over to a transporter or freight forwarder, the order <strong>cannot be cancelled</strong>.</p>

                <h3>Fresh Produce, Custom Packing &amp; Export Orders</h3>
                <ul>
                    <li><strong>Fresh Fruits &amp; Vegetables:</strong> Daily mandi procurement begins immediately after cut-off; cancellations are not accepted once procurement has started.</li>
                    <li><strong>Private Label &amp; Custom Packing:</strong> Orders involving custom pouch printing, private labels or non-standard pack weights cannot be cancelled once production or packing starts.</li>
                    <li><strong>Export Consignments:</strong> Container or air-freight orders are non-cancellable once sourcing, phytosanitary inspection or customs documentation has commenced.</li>
                </ul>

                <h2>2. Returns &amp; Quality Claims</h2>
                <p>Under food-safety and hygiene norms, we do not accept general returns (such as change of requirement or local market rate fluctuations) on edible agro commodities. However, we stand behind the specification agreed on your purchase order. A replacement, credit note or partial/full refund is considered strictly if:</p>
                <ul>
                    <li>The delivered lot materially differs from the approved sample or written grade specification.</li>
                    <li>An incorrect item or verified short-weight consignment was dispatched.</li>
                    <li>Packaging was severely damaged during transit under a delivered-rate arrangement where transit risk rests with us.</li>
                </ul>

                <h2>3. Claim Reporting Window</h2>
                <p>Because freshness and storage conditions change rapidly after unloading, claims must be reported in writing to our trade desk with supporting evidence (lot photos, unloading video, weighment slip and invoice number) within:</p>
                <ul>
                    <li><strong>Fresh Fruits &amp; Vegetables:</strong> Within <strong>24 hours</strong> of delivery (or at the time of gate inspection for hotel/catering drops).</li>
                    <li><strong>Dry Fruits, Nuts &amp; Spices:</strong> Within <strong>48 hours</strong> of delivery, with packaging intact and stored in cool, dry conditions.</li>
                </ul>

                <h2>4. Verification &amp; Refund Timeline</h2>
                <p>Once a valid claim is verified by our quality team, we will issue a mutually agreed resolution—either a replacement lot, a GST credit note adjusted against your ledger, or a bank refund. Approved bank refunds are processed back to the buyer’s verified business account within <strong>5 to 7 business days</strong>.</p>

                <h2>5. Trade Desk Support</h2>
                <p>To submit a cancellation request or report a consignment issue, please reach our desk directly:</p>

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
                        <li><a href="refund-policy" class="on">Refund &amp; Cancellation</a></li>
                        <li><a href="shipping-policy">Shipping &amp; Delivery</a></li>
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
                    <h3>Need help with an order?</h3>
                    <p>Share your invoice or PO number with our trade desk for quick assistance on dispatch or claims.</p>
                    <a href="contact" class="btn" style="width:100%;margin-bottom:10px;">Contact trade desk</a>
                    <a href="https://wa.me/<?= e($wa) ?>?text=<?= rawurlencode('Hello ' . $brand_full . ', I need assistance regarding an order.') ?>" target="_blank" rel="noopener" class="btn green" style="width:100%;margin-bottom:12px;">
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
                    <h2>Want a sample before placing a bulk order?</h2>
                    <p>Verify grade, size and moisture upfront with representative samples sent directly from our desk.</p>
                </div>
                <div class="cta">
                    <a href="contact" class="btn d">Request sample &amp; rate</a>
                    <a href="https://wa.me/<?= e($wa) ?>" target="_blank" rel="noopener" class="btn green"><i class="fa-brands fa-whatsapp"></i> WhatsApp us</a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include 'includes/footer.php'; ?>