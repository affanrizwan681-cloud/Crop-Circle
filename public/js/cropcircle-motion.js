(function () {
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var protectedSections = '.cc-home-hero, .cc-testimonials';

    function isProtected(element) {
        return Boolean(element.closest(protectedSections));
    }

    function formatCounter(element, value) {
        var prefix = element.getAttribute('data-count-prefix') || '';
        var suffix = element.getAttribute('data-count-suffix') || '';
        var decimals = Math.max(0, Math.min(2, Number(element.getAttribute('data-count-decimals')) || 0));
        return prefix + new Intl.NumberFormat(undefined, { minimumFractionDigits: decimals, maximumFractionDigits: decimals }).format(value) + suffix;
    }

    var counters = Array.prototype.slice.call(document.querySelectorAll('.cc-count-up'));
    counters.forEach(function (counter) {
        if (isProtected(counter)) return;
        var target = Number(counter.getAttribute('data-count-target'));
        counter.textContent = formatCounter(counter, 0);

        function finish() {
            counter.textContent = formatCounter(counter, target);
        }

        if (reduceMotion || !('IntersectionObserver' in window)) {
            finish();
            return;
        }

        var duration = Number(counter.getAttribute('data-count-duration')) || 1600;
        var countObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                countObserver.unobserve(counter);
                if (target === 0) {
                    finish();
                    return;
                }
                var start = null;
                function tick(timestamp) {
                    if (start === null) start = timestamp;
                    var progress = Math.min((timestamp - start) / duration, 1);
                    var eased = 1 - Math.pow(1 - progress, 3);
                    var decimals = Math.max(0, Math.min(2, Number(counter.getAttribute('data-count-decimals')) || 0));
                    var scale = Math.pow(10, decimals);
                    counter.textContent = formatCounter(counter, Math.round(target * eased * scale) / scale);
                    if (progress < 1) window.requestAnimationFrame(tick);
                    else finish();
                }
                window.requestAnimationFrame(tick);
            });
        }, { threshold: 0.45 });
        countObserver.observe(counter);
    });

    var glassSelector = 'main .card';
    var interactiveCardSelector = 'main .product-card-click, main .card-hover';

    document.querySelectorAll(interactiveCardSelector).forEach(function (element) {
        if (isProtected(element)) return;
        element.addEventListener('pointerdown', function () { element.classList.add('cc-card-pressed'); }, { passive: true });
        ['pointerup', 'pointercancel', 'pointerleave'].forEach(function (eventName) {
            element.addEventListener(eventName, function () { element.classList.remove('cc-card-pressed'); }, { passive: true });
        });
    });
    document.querySelectorAll(glassSelector).forEach(function (element) {
        if (isProtected(element)) return;
        element.classList.add('cc-glass-surface');
        if (reduceMotion) return;

        element.addEventListener('pointermove', function (event) {
            var bounds = element.getBoundingClientRect();
            if (!bounds.width || !bounds.height) return;
            var x = ((event.clientX - bounds.left) / bounds.width) * 100;
            var y = ((event.clientY - bounds.top) / bounds.height) * 100;
            element.style.setProperty('--cc-glass-x', x.toFixed(1) + '%');
            element.style.setProperty('--cc-glass-y', y.toFixed(1) + '%');
        }, { passive: true });
    });

    if (reduceMotion) return;

    var revealSelector = 'main .card, main h1, main h2, main .page-title, main .table-responsive, main .alert, main .portal-sidebar, footer .footer-heading';
    var observer = 'IntersectionObserver' in window
        ? new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('cc-motion-visible');
                observer.unobserve(entry.target);
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -36px 0px' })
        : null;

    var delays = new Map();
    document.querySelectorAll(revealSelector).forEach(function (element) {
        if (isProtected(element) || element.dataset.ccMotionReady === 'true') return;
        element.dataset.ccMotionReady = 'true';
        element.classList.add('cc-motion-ready');

        var parent = element.parentElement;
        var index = delays.get(parent) || 0;
        delays.set(parent, index + 1);
        element.style.setProperty('--cc-motion-delay', Math.min(index * 55, 275) + 'ms');

        if (observer) observer.observe(element);
        else element.classList.add('cc-motion-visible');
    });
})();


