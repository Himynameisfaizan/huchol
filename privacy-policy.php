<?php
$meta_title = 'Privacy Policy | Huchol Agro Traders';
$meta_desc  = 'Read how Huchol Agro Traders collects, uses and protects business and buyer information for bulk dry fruits, fresh produce and spices enquiries.';
$meta_key   = 'privacy policy, huchol agro traders, bulk agro supplier privacy, wholesale trade data protection';

include_once 'includes/header.php';

$brand_full = $site_full_name ?? trim(($brand['name'] ?? 'Huchol') . ' ' . ($brand['suffix'] ?? 'Agro Traders'));
$c_address  = $ct['address'] ?? '';
?>

<main id="main">

    <section class="pghead nb">
        <div class="wrap">
            <ol class="crumbs">
                <li><a href="./">Home</a></li>
                <li>Privacy Policy</li>
            </ol>
            <h1>Privacy Policy</h1>
            <p class="lead">How <?= e($brand_full) ?> handles buyer enquiries, trade documentation and contact details shared with our desk.</p>
            <div class="pills">
                <span>Last updated: <?= e(date('d M Y')) ?></span>
                <span>B2B Trade Desk</span>
            </div>
        </div>
    </section>

    <section class="sec art-sec">
        <div class="wrap art">

            <!-- Left Column: Policy Content -->
            <article class="prose" style="background:#fff;border:1px solid var(--line);border-radius:var(--r);padding:clamp(22px,4vw,42px);">
                <p>Welcome to <strong><?= e($brand_full) ?></strong>. We respect the privacy of every wholesaler, hotel, caterer, retailer, food processor and export buyer who visits our website or submits a bulk enquiry for dry fruits, fresh fruits, fresh vegetables and spices. This policy explains what information we collect, why we collect it and how we safeguard it.</p>

                <h2>1. Information We Collect</h2>
                <p>When you request a quotation, ask for a sample or communicate with our trade desk, we may collect:</p>
                <ul>
                    <li><strong>Contact &amp; Business Details:</strong> Contact person name, company or firm name, email address, phone or WhatsApp number and delivery city or port.</li>
                    <li><strong>Order &amp; Specification Details:</strong> Product names, grades, pack sizes, quantities, rate basis (ex-Delhi, delivered, FOB or CIF) and dispatch instructions.</li>
                    <li><strong>Trade &amp; Billing Compliance Data:</strong> GSTIN, billing and godown addresses, FSSAI details, IEC code or destination export requirements needed to issue valid invoices and transport documents.</li>
                    <li><strong>Technical &amp; Usage Data:</strong> Basic browser, device and server log data (such as IP address and pages viewed) used strictly to keep the website secure and functional.</li>
                </ul>

                <h2>2. How We Use Your Information</h2>
                <ul>
                    <li>To prepare written bulk quotations, arrange representative samples and confirm lot availability.</li>
                    <li>To process confirmed purchase orders, pack goods to specification and issue GST invoices, e-way bills and export paperwork.</li>
                    <li>To coordinate loading, transport tracking and delivery updates via phone, email or WhatsApp.</li>
                    <li>To meet statutory, tax, customs and food-safety record-keeping obligations under Indian law.</li>
                    <li>To prevent spam, fraudulent enquiries and unauthorised access to our website.</li>
                </ul>

                <h2>3. Payment &amp; Banking Details</h2>
                <p>Commercial settlements for wholesale orders are handled via agreed bank transfers (NEFT, RTGS, IMPS, LC) or authorised payment instruments. We do not store debit card, credit card or net-banking credentials on our website servers.</p>

                <h2>4. Information Sharing</h2>
                <p>We do not sell, rent or trade buyer data to third-party marketers. Information is shared only on a need-to-know basis with:</p>
                <ul>
                    <li><strong>Transport &amp; Freight Partners:</strong> Fleet operators, rail booking agents, courier services and freight forwarders solely to deliver samples and consignments.</li>
                    <li><strong>Inspection &amp; Compliance Agencies:</strong> Third-party quality inspectors, phytosanitary agencies or customs brokers where requested for your order.</li>
                    <li><strong>Legal Authorities:</strong> Tax, GST or regulatory authorities where disclosure is required by applicable law.</li>
                </ul>

                <h2>5. Cookies</h2>
                <p>Our website uses essential cookies to keep forms and navigation working smoothly. You can disable cookies in your browser settings at any time without blocking access to our product catalogue.</p>

                <h2>6. Data Security &amp; Retention</h2>
                <p>Enquiry records and trade documents are stored with restricted administrative access and SSL-encrypted connections. We retain commercial and billing records only for as long as needed to support repeat trade accounts and statutory tax compliance.</p>

                <h2>7. Updates &amp; Contact</h2>
                <p>We may update this policy periodically to reflect operational or legal changes. For questions about your data or to request deletion of an enquiry record, contact our trade desk:</p>

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
                        <li><a href="privacy-policy" class="on">Privacy Policy</a></li>
                        <li><a href="refund-policy">Refund &amp; Cancellation</a></li>
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
                    <h3>Need today’s bulk rate?</h3>
                    <p>Share your product, grade, pack size and delivery city. Our trade desk replies with a written quotation.</p>
                    <a href="contact" class="btn" style="width:100%;margin-bottom:10px;">Request bulk quote</a>
                    <a href="https://wa.me/<?= e($wa) ?>?text=<?= rawurlencode('Hello ' . $brand_full . ', I would like to discuss a bulk requirement.') ?>" target="_blank" rel="noopener" class="btn green" style="width:100%;margin-bottom:12px;">
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
                    <h2>Have a question about bulk orders?</h2>
                    <p>Speak directly with our trade desk for specifications, samples, GST billing and dispatch schedules.</p>
                </div>
                <div class="cta">
                    <a href="contact" class="btn d">Contact trade desk</a>
                    <a href="https://wa.me/<?= e($wa) ?>" target="_blank" rel="noopener" class="btn green"><i class="fa-brands fa-whatsapp"></i> WhatsApp us</a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include 'includes/footer.php'; ?>