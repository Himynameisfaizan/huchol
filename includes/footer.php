<footer class="site-footer">
    <div class="wrap fg">
        <div>
            <a class="brand"
                href="./"><?= mark(44) ?><span><b><?= e($brand['name']) ?></b><small><?= e($brand['suffix']) ?></small></span></a>
            <p class="fabout">Wholesale supply of dry fruits, fresh fruits, vegetables and spices from
                <?= e($brand['city']) ?>, for traders, hotels, retailers and exporters.</p>
            <div class="soc">
                <?php foreach (['facebook' => 'fa-facebook-f', 'instagram' => 'fa-instagram', 'twitter' => 'fa-x-twitter', 'linkdin' => 'fa-linkedin-in'] as $k => $ic): if (!empty($ct[$k])): ?>
                <a href="<?= e($ct[$k]) ?>" target="_blank" rel="noopener" aria-label="<?= e($k) ?>"><i
                        class="fa-brands <?= $ic ?>"></i></a>
                <?php endif; endforeach; ?>
            </div>
        </div>
        <div>
            <h4>Company</h4>
            <ul>
                <li><a href="about">About us</a></li>
                <li><a href="products">All products</a></li>
                <li><a href="service">Services</a></li>
                <li><a href="blog">Insights</a></li>
                <li><a href="contact">Contact</a></li>
            </ul>
        </div>
        <div>
            <h4>Products</h4>
            <ul><?php foreach ($nav_cats as $slug => $cd): ?><li><a
                        href="category/<?= e($slug) ?>"><?= e($cd['name']) ?></a></li><?php endforeach; ?></ul>
        </div>
        <div>
            <h4>Trade desk</h4>
            <ul class="fc">
                <?php if ($ct['address']): ?><li><i class="fa-solid fa-location-dot"></i> <?= e($ct['address']) ?></li>
                <?php endif; ?>
                <li><i class="fa-solid fa-phone"></i> <a href="tel:+<?= e($tel) ?>"><?= e($disp_phone) ?></a></li>
                <li><i class="fa-regular fa-envelope"></i> <a href="mailto:<?= e($mail) ?>"><?= e($mail) ?></a></li>
                <li><i class="fa-regular fa-clock"></i> <?= e($hours) ?></li>
            </ul>
        </div>
    </div>
    <div class="wrap fb">
        <span>&copy; <?= date('Y') ?> <?= e($brand['name'] . ' ' . $brand['suffix']) ?>. All rights reserved. Designed & Developed by <a href="https://www.digitalwebtrackers.com" target="_blank" rel="noopener" style="color: #F5A800;">Digital Web Trackers  </a></span>
        <?php if ($brand['licences']): ?><span
            class="lic"><?php foreach ($brand['licences'] as $l): ?><em><?= e($l) ?></em><?php endforeach; ?></span><?php endif; ?>
    </div>
</footer>

<a class="wa" target="_blank" rel="noopener" aria-label="Chat on WhatsApp"
    href="https://wa.me/<?= e($wa) ?>?text=<?= rawurlencode($brand['wa_msg']) ?>"><i
        class="fa-brands fa-whatsapp"></i></a>
<script src="<?= e(asset('assets/js/main.js')) ?>"></script>
</body>

</html>