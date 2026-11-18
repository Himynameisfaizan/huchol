(function () {
  var h = document.querySelector('.site-header'), n = document.getElementById('nav'), t = document.querySelector('.tg');
  addEventListener('scroll', function () { h.classList.toggle('s', scrollY > 8); }, { passive: true });
  if (t && n) {
    t.addEventListener('click', function () { t.setAttribute('aria-expanded', n.classList.toggle('o')); });
    n.querySelectorAll('a').forEach(function (a) { a.addEventListener('click', function () { n.classList.remove('o'); t.setAttribute('aria-expanded', 'false'); }); });
  }
  // Product filter chips
  var chips = document.querySelectorAll('[data-f]'), cards = document.querySelectorAll('[data-c]');
  chips.forEach(function (c) {
    c.addEventListener('click', function () {
      chips.forEach(function (x) { x.setAttribute('aria-pressed', x === c); });
      cards.forEach(function (k) { k.hidden = !(c.dataset.f === 'all' || k.dataset.c === c.dataset.f); });
    });
  });
  // Any [data-p] button prefills the enquiry form's product field and scrolls to it
  document.querySelectorAll('[data-p]').forEach(function (b) {
    b.addEventListener('click', function () {
      var i = document.getElementById('pi'), f = document.getElementById('rfq');
      if (!i || !f) return;
      i.value = b.dataset.p; f.scrollIntoView({ behavior: 'smooth' });
      setTimeout(function () { i.focus({ preventScroll: true }); }, 600);
    });
  });
})();

// Inner pages: product gallery thumbnails + copy-link button
(function () {
  var thumbs = document.querySelectorAll('[data-thumb]'), main = document.getElementById('gm');
  thumbs.forEach(function (b) {
    b.addEventListener('click', function () {
      if (!main) return;
      main.style.display = ''; main.src = b.dataset.thumb;
      thumbs.forEach(function (x) { x.classList.toggle('on', x === b); });
    });
  });
  document.querySelectorAll('[data-copy]').forEach(function (b) {
    b.addEventListener('click', function () {
      var u = location.href.split('#')[0], done = function () { b.textContent = 'Link copied'; };
      if (navigator.clipboard) navigator.clipboard.writeText(u).then(done, function () { prompt('Copy this link', u); });
      else prompt('Copy this link', u);
    });
  });
})();