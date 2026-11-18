<?php
/** Reusable cards, trust sections and per-category content for inner pages. */

// ---------- Cards ----------
function crumbs($items, $cls = '') {
    $o = '<ol class="crumbs ' . $cls . '">';
    foreach ($items as $i => $c) $o .= '<li>' . ($c[1] && $i < count($items) - 1 ? '<a href="' . e($c[1]) . '">' . e($c[0]) . '</a>' : '<span aria-current="page">' . e($c[0]) . '</span>') . '</li>';
    return $o . '</ol>';
}
function product_card($p) {
    $u = 'product-details.php?slug=' . urlencode($p['slug_url']);
    $im = product_img($p['pro_img'] ?? '', $p['cslug'] ?? '');
    return '<article class="pc"><a class="ph" href="' . e($u) . '">' . pic($im, $p['pro_name']) . '</a><div class="bd"><small class="tag">' . e($p['cat'] ?? 'Products') . '</small>'
        . '<h3><a href="' . e($u) . '">' . e($p['pro_name']) . '</a></h3><p>' . e(txt($p['short_desc'] ?? '', 96) ?: 'Graded and packed for wholesale. Rate and MOQ on request.') . '</p>'
        . '<a class="pc-btn" href="' . e($u) . '#enquire">Request bulk rate</a></div></article>';
}
function blog_card($b) {
    $u = 'blog-details.php?slug=' . urlencode($b['slug']);
    $im = img($b['image'] ?? '', 'blogs', un('photo-1596040033229-a9821ebd058d', 700));
    $ex = txt($b['meta_desc'] ?? '', 125) ?: txt($b['description'] ?? '', 125);
    return '<article class="bl"><a class="im" href="' . e($u) . '">' . pic($im, $b['title']) . '</a><small class="meta">' . e(date_fmt($b['created_at'] ?? '')) . ' by ' . e($b['author'] ?? '') . '</small>'
        . '<h3><a href="' . e($u) . '">' . e($b['title']) . '</a></h3><p>' . e($ex) . '</p><a class="rd" href="' . e($u) . '">Read the guide</a></article>';
}

// ---------- Trust sections ----------
function why_block($title, $hi, $items, $alt = true) {
    $ic = ['fa-magnifying-glass', 'fa-ruler-combined', 'fa-box-open', 'fa-file-invoice'];
    echo '<section class="sec' . ($alt ? ' alt' : '') . '"><div class="wrap"><div class="sh"><h2>' . e($title) . '</h2><span class="hi">' . e($hi) . '</span></div><div class="why">';
    foreach ($items as $i => $it) echo '<div><i class="fa-solid ' . $ic[$i % 4] . '"></i><h3>' . e($it[0]) . '</h3><p>' . e($it[1]) . '</p></div>';
    echo '</div></div></section>';
}
function steps_block() {
    echo '<section class="sec dk"><div class="wrap"><div class="sh"><h2>From your enquiry to your godown</h2><span class="hi">How an order moves</span></div><ol class="steps">'
        . '<li class="st"><span class="n">1</span><h3>Tell us what you need</h3><p>Product, grade, quantity and delivery city, by form or WhatsApp.</p></li>'
        . '<li class="st"><span class="n">2</span><h3>Sample and rate</h3><p>A sample and a written rate for the agreed specification.</p></li>'
        . '<li class="st"><span class="n">3</span><h3>Grade, pack, inspect</h3><p>Sorting, moisture and quality checks, then packing as agreed.</p></li>'
        . '<li class="st"><span class="n">4</span><h3>Dispatch and paperwork</h3><p>Truck, rail or sea, with GST invoice, e-way bill and export documents.</p></li></ol></div></section>';
}
function faq_block($title, $hi, $faqs) {
    echo '<section class="sec"><div class="wrap"><div class="sh"><h2>' . e($title) . '</h2><span class="hi">' . e($hi) . '</span></div><div class="faq">';
    foreach ($faqs as $i => $f) echo '<details' . ($i === 0 ? ' open' : '') . '><summary>' . e($f[0]) . '</summary><p>' . e($f[1]) . '</p></details>';
    echo '</div></div></section>';
}
function faq_schema($faqs) {
    return ['@type' => 'FAQPage', 'mainEntity' => array_map(function ($f) { return ['@type' => 'Question', 'name' => $f[0], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f[1]]]; }, $faqs)];
}
function cta_band($title, $text, $subject = '') {
    global $wa, $brand;
    echo '<section class="sec cbs"><div class="wrap"><div class="cband"><div><h2>' . e($title) . '</h2><p>' . e($text) . '</p></div><div class="cta">'
        . '<a class="btn d" href="contact.php?subject=' . urlencode($subject) . '">Get bulk rate</a>'
        . '<a class="btn green" target="_blank" rel="noopener" href="https://wa.me/' . e($wa) . '?text=' . rawurlencode($brand['wa_msg'] . ($subject ? ' Product: ' . $subject : '')) . '"><i class="fa-brands fa-whatsapp"></i> WhatsApp us</a></div></div></div></section>';
}

$WHY = [['Checked against the sample', 'Every lot is checked against the agreed sample before it is loaded.'], ['Packed to your spec', 'Cartons, crates, bags or your own private label, as agreed.'], ['Clean paperwork', 'GST invoice and e-way bill with every consignment, export documents on request.'], ['Dispatch across India', 'Truck and rail dispatch from Delhi, with delivery tracking for regular accounts.']];

function product_faqs($n) {
    return [
        ["What is the minimum order for $n?", "Trial lots are available, and regular supply is quoted in bulk quantities. Share your required quantity and we will confirm the minimum on your quotation."],
        ["Can I get a sample of $n before ordering?", "Yes. We send a representative sample so you can compare it with the lot before you commit to a bulk order."],
        ["How is $n packed and dispatched?", "We pack to the specification agreed with you, using cartons, crates or bags as suited to the product, and dispatch by truck or rail across India. Export packing is available on request."],
        ["Do you provide a GST invoice and transport documents?", "Yes. Every consignment goes with a GST invoice and e-way bill, and we support export documentation for overseas orders."],
        ["How do I get today's rate for $n?", "Rates move with the market. Send your quantity, grade and delivery city through the enquiry form or WhatsApp, and our trade desk will reply with a written rate during business hours."],
    ];
}

// ---------- Per-category content (falls back to a generic version for new categories) ----------
function cat_info($slug, $name) {
    $I = [
    'dry-fruits-nuts' => [
        'intro' => 'We buy almonds, cashews, raisins and pistachios from trusted processors and importers, sort them by grade and size, and pack them so they reach you crisp and fresh. Whether you restock a sweet shop, run a repacking unit or supply gift hampers, we quote against your grade, quantity and pack size.',
        'facts' => [['Popular lines', 'Almonds, cashews, raisins, pistachios'], ['Grades', 'Cashew W180, W240, W320; almonds by count per ounce'], ['Packing', '10 and 20 kg cartons, vacuum packs, tins'], ['Availability', 'Year-round']],
        'checks' => [['Moisture and crispness', 'Nuts are checked for dryness and freshness before packing.'], ['Size and colour sorting', 'Each lot is sorted to the grade on your quotation.'], ['Clean, sealed packing', 'Liners, vacuum packs or tins protect flavour in transit.'], ['Sample before you commit', 'A sample matching the lot is sent before a bulk order.']],
        'faq' => [['What is the minimum order for dry fruits?', 'Trial lots start from around 100 kg, and regular supply is quoted in metric tonnes. Share your quantity and we will confirm the minimum on your quotation.'], ['How should I store nuts after delivery?', 'Keep them sealed in a cool, dry place away from direct sunlight. Vacuum packs and tins keep nuts fresh for longer than open cartons.'], ['Can you pack under our own brand?', 'Yes. We can pack in your pack sizes and labels for private-label orders. Send us your artwork and expected quantity.'], ['Do you supply outside Delhi?', 'Yes. We dispatch across India by truck and rail with a GST invoice and e-way bill, and we support export orders with the required documents.']]],
    'fresh-fruits' => [
        'intro' => 'Our fruit comes from the mandi and from growers in season: apples, bananas, pomegranates, mangoes and more. We sort by size, colour and ripeness and crate it carefully, so it reaches wholesalers, retailers and hotels in good condition.',
        'facts' => [['Popular lines', 'Apples, bananas, pomegranates, seasonal mangoes'], ['Grades', 'Sorted by size, colour and ripeness stage'], ['Packing', 'Cartons and crates, 5 to 20 kg'], ['Availability', 'Seasonal; stored and imported lots outside the season']],
        'checks' => [['Ripeness stage agreed', 'Tell us how ripe you need it on arrival and we plan the pick and transit to match.'], ['Size and colour sorting', 'Fruit is graded so every carton looks and weighs alike.'], ['Bruise and damage check', 'Damaged pieces are removed before crating.'], ['Careful crating', 'Cartons and ventilated crates reduce damage in transit.']],
        'faq' => [['How do you handle seasonal fruit?', 'We tell you early which fruit is in season and which is coming from storage or imports, so you can plan your menu or shelf accordingly.'], ['Can you supply fruit at a specific ripeness?', 'Yes. Share the ripeness you need on the day of arrival and your delivery city, and we will plan harvest stage and transit time to match.'], ['How is fresh fruit packed for transport?', 'In cartons or ventilated crates, sized and sorted so the load travels well. Packing is agreed with you before dispatch.'], ['How are fruit rates decided?', 'Rates follow the daily mandi movement. We quote against your quantity and grade, with a clearly stated validity.']]],
    'fresh-vegetables' => [
        'intro' => 'We supply everyday vegetables and seasonal specials to hotels, caterers, retailers and processors. Specifications for size, colour and trimming are agreed in writing, so each delivery looks like the last one.',
        'facts' => [['Popular lines', 'Onions, tomatoes, leafy greens, seasonal vegetables'], ['Grades', 'Size, colour and trimming agreed in writing'], ['Packing', 'Crates, mesh bags and bundles'], ['Availability', 'Staples year-round; seasonal items in cooler months']],
        'checks' => [['Written specification', 'Size, colour and trimming are agreed before the first order.'], ['Timed delivery', 'A delivery window is fixed so your kitchen can plan its prep.'], ['Handled for freshness', 'Crates for soft produce, mesh bags for onions and potatoes.'], ['Clear rate basis', 'Per kg or per crate, fixed weekly or revised daily, as you prefer.']],
        'faq' => [['Can you supply on a daily or weekly schedule?', 'Yes. We can set up a regular supply plan with fixed items, quantities and delivery windows, and adjust it as your menu changes.'], ['What happens if a lot does not meet the specification?', 'Report it on delivery with photos and our trade desk will review it with you and arrange a fair resolution.'], ['Do you supply hotels, caterers and restaurant groups?', 'Yes. Food-service buyers are a core part of our vegetable business, and we plan deliveries around kitchen schedules.'], ['Which areas do you deliver to?', 'We deliver in and around Delhi on agreed schedules, and to other cities by truck or rail for larger orders.']]],
    'spices' => [
        'intro' => 'We supply whole and ground spices, cleaned and sorted for traders, packers, hotels and food processors. Chilli, cumin, cardamom and black pepper are available by variety and size, with samples before bulk orders.',
        'facts' => [['Popular lines', 'Chilli, cumin, cardamom, black pepper'], ['Grades', 'By variety, size (mm for cardamom) and purity'], ['Packing', '25 and 50 kg PP or jute bags, pouches'], ['Availability', 'Year-round; new crops in season']],
        'checks' => [['Aroma and colour', 'Lots are smelled and inspected for freshness and colour.'], ['Cleaned and sorted', 'Stems, stones and dust are removed and sizes are graded.'], ['Moisture controlled', 'Dry stock keeps flavour and avoids mould in storage.'], ['Sample matched to lot', 'The sample you approve matches the lot we ship.']],
        'faq' => [['Do you supply whole or ground spices?', 'Mainly whole spices, and ground spices on request. Tell us your mesh size and packing, and we will quote accordingly.'], ['Can I get a sample before a bulk order?', 'Yes. We send a sample so you can check aroma, colour and cleanliness before you order in bulk.'], ['Are the spices cleaned before packing?', 'Yes. We remove foreign matter and sort by size or variety, with machine-cleaned lots available.'], ['Do you export spices?', 'Yes. We support export orders with invoices, packing lists and the certificates your destination requires.']]],
    ];
    if (isset($I[$slug])) return $I[$slug];
    $n = strtolower($name ?: 'products');
    return [
        'intro' => "Browse our range of $n, sourced directly, graded to your specification and packed for wholesale. Tell us your grade, quantity and delivery city and our trade desk will send a written rate and a sample.",
        'facts' => [['Sourcing', 'Direct from farms, mandis and processors'], ['Grading', 'Agreed with you before packing'], ['Packing', 'Cartons, crates, bags or private label'], ['Dispatch', 'Pan-India, with export support']],
        'checks' => [['Quality checked', 'Every lot is checked against the agreed sample.'], ['Sorted to grade', 'Size, colour and moisture are agreed first.'], ['Packed for the route', 'Packing is chosen for your distance and handling.'], ['Clean paperwork', 'GST invoice and e-way bill with every consignment.']],
        'faq' => [["What is the minimum order for $n?", 'Trial lots are available and regular supply is quoted in bulk. Share your quantity and we will confirm the minimum.'], ['Can I get a sample first?', 'Yes. We send a sample so you can compare it with the lot before ordering in bulk.'], ['Do you provide a GST invoice?', 'Yes, with an e-way bill for every consignment, plus export documents on request.'], ['Do you dispatch across India?', 'Yes, by truck and rail, and we support export orders.']],
    ];
}