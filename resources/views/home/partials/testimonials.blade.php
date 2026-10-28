@push('styles')
<style>
    .cc-testimonials {
        --cc-testimonial-forest: #103b28;
        --cc-testimonial-green: #215c3a;
        --cc-testimonial-mint: #e6f2e8;
        --cc-testimonial-cream: #f6f8f1;
        position: relative;
        overflow: hidden;
        padding: clamp(64px, 8vw, 112px) 0;
        color: #183426;
        background: linear-gradient(180deg, #f7f8f2, #fff 44%, #f7f8f2);
    }
    .cc-testimonials__header { max-width: 760px; margin: 0 auto 48px; text-align: center; }
    .cc-testimonials__eyebrow { display: inline-block; margin-bottom: 12px; color: var(--cc-testimonial-green); font-size: .76rem; font-weight: 800; letter-spacing: .17em; }
    .cc-testimonials__title { margin: 0 0 14px; color: var(--cc-testimonial-forest); font-size: clamp(2rem, 3.5vw, 3.2rem); font-weight: 800; letter-spacing: -.04em; line-height: 1.08; }
    .cc-testimonials__intro { max-width: 660px; margin: 0 auto; color: #5b6d60; font-size: clamp(1rem, 1.3vw, 1.13rem); line-height: 1.7; }
    .cc-testimonials__timeline { position: relative; max-width: 1120px; margin: 0 auto; }
    .cc-testimonials__path { position: absolute; z-index: 0; inset: 0 auto 0 50%; width: 74px; height: 100%; overflow: visible; transform: translateX(-50%); pointer-events: none; }
    .cc-testimonials__path-track, .cc-testimonials__path-draw { fill: none; stroke-linecap: round; stroke-width: 3.2; }
    .cc-testimonials__path-track { stroke: #dce8dd; }
    .cc-testimonials__path-draw { stroke: #4b9563; stroke-width: 4; filter: drop-shadow(0 2px 3px rgba(33,92,58,.18)); }
    .cc-testimonials__list { position: relative; z-index: 1; display: flex; flex-direction: column; gap: 26px; margin: 0; padding: 0; list-style: none; }
    .cc-testimonials__item { display: grid; grid-template-columns: minmax(0, 1fr) 74px minmax(0, 1fr); align-items: center; min-height: 220px; }
    .cc-testimonials__card { width: 100%; padding: clamp(22px, 3vw, 34px); border: 1px solid rgba(24,52,38,.07); border-radius: 24px; background: #fff; box-shadow: 0 18px 48px rgba(16,59,40,.09); opacity: var(--card-reveal, 1); transform: translateY(var(--card-offset, 0)) scale(var(--card-scale, 1)); transition: opacity .18s linear, transform .18s linear, box-shadow .22s ease; }
    .cc-testimonials__item:nth-child(odd) .cc-testimonials__card { grid-column: 1; grid-row: 1; }
    .cc-testimonials__item:nth-child(even) .cc-testimonials__card { grid-column: 3; grid-row: 1; }
    .cc-testimonials__card:hover { box-shadow: 0 24px 56px rgba(16,59,40,.14); }
    .cc-testimonials__marker { display: grid; place-items: center; grid-column: 2; grid-row: 1; justify-self: center; width: 46px; height: 46px; border: 5px solid #f7f8f2; border-radius: 50%; color: #fff; background: var(--cc-testimonial-green); box-shadow: 0 0 0 1px rgba(33,92,58,.15), 0 6px 18px rgba(16,59,40,.2); opacity: var(--card-reveal, 1); transform: scale(var(--marker-scale, 1)); transition: opacity .18s linear, transform .18s linear; }
    .cc-testimonials__marker i { font-size: 1rem; }
    .cc-testimonials__meta { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 18px; }
    .cc-testimonials__label { display: inline-flex; align-items: center; gap: 7px; padding: 6px 11px; border-radius: 999px; color: #215c3a; background: var(--cc-testimonial-mint); font-size: .72rem; font-weight: 800; letter-spacing: .03em; }
    .cc-testimonials__stars { display: inline-flex; gap: 2px; color: #dca638; }
    .cc-testimonials__stars i { opacity: var(--card-reveal, 1); transform: scale(var(--star-scale, 1)); transition: opacity .18s linear, transform .18s linear; transition-delay: calc(var(--star-index, 0) * 24ms); }
    .cc-testimonials__quote { margin: 0 0 22px; color: #31493a; font-size: clamp(1rem, 1.2vw, 1.08rem); font-weight: 500; line-height: 1.72; }
    .cc-testimonials__product { display: inline-block; margin: -8px 0 20px; padding: 5px 9px; border-radius: 8px; color: #617467; background: #f4f7f2; font-size: .76rem; }
    .cc-testimonials__author { display: flex; align-items: center; gap: 12px; padding-top: 17px; border-top: 1px solid #edf1eb; }
    .cc-testimonials__initials { display: grid; place-items: center; flex: 0 0 42px; width: 42px; height: 42px; border-radius: 50%; color: #fff; background: linear-gradient(135deg, #215c3a, #78a982); font-size: .82rem; font-weight: 800; }
    .cc-testimonials__name { color: #1b3b29; font-size: .92rem; font-weight: 800; }
    .cc-testimonials__role { display: block; color: #718075; font-size: .77rem; }
    .cc-testimonials__empty { display: grid; justify-items: center; gap: 10px; max-width: 650px; margin: 0 auto; padding: 38px 28px; border: 1px solid #e4ede3; border-radius: 24px; text-align: center; background: rgba(255,255,255,.86); box-shadow: 0 18px 48px rgba(16,59,40,.07); }
    .cc-testimonials__empty-icon { display: grid; place-items: center; width: 54px; height: 54px; border-radius: 18px; color: var(--cc-testimonial-green); background: var(--cc-testimonial-mint); font-size: 1.4rem; }
    .cc-testimonials__empty h3 { margin: 4px 0 0; color: var(--cc-testimonial-forest); font-size: 1.2rem; font-weight: 800; }
    .cc-testimonials__empty p { max-width: 520px; margin: 0; color: #647468; font-size: .94rem; line-height: 1.65; }
    .cc-testimonials__data-note { max-width: 760px; margin: 30px auto 0; color: #728077; text-align: center; font-size: .78rem; line-height: 1.55; }
    @media (max-width: 767.98px) {
        .cc-testimonials { padding-block: 58px; }
        .cc-testimonials__header { margin-bottom: 36px; }
        .cc-testimonials__path { left: 20px; width: 40px; transform: none; }
        .cc-testimonials__item { grid-template-columns: 40px minmax(0, 1fr); min-height: 0; gap: 0; }
        .cc-testimonials__marker { grid-column: 1; grid-row: 1; justify-self: start; width: 38px; height: 38px; border-width: 4px; }
        .cc-testimonials__item:nth-child(odd) .cc-testimonials__card,
        .cc-testimonials__item:nth-child(even) .cc-testimonials__card { grid-column: 2; grid-row: 1; }
        .cc-testimonials__card { padding: 21px; border-radius: 20px; }
        .cc-testimonials__list { gap: 22px; }
    }
    @media (max-width: 420px) {
        .cc-testimonials__meta { align-items: flex-start; flex-direction: column; }
    }
    @media (prefers-reduced-motion: reduce) {
        .cc-testimonials__card, .cc-testimonials__marker, .cc-testimonials__stars i { transition: none; }
    }
</style>
@endpush

<section class="cc-testimonials" aria-labelledby="cc-testimonials-title">
    <div class="container">
        <header class="cc-testimonials__header">
            <div class="cc-testimonials__eyebrow">VOICES OF OUR COMMUNITY</div>
            <h2 class="cc-testimonials__title" id="cc-testimonials-title">One Community. Shared Success.</h2>
            <p class="cc-testimonials__intro">From customers discovering fresh local produce to farmers growing their businesses, see what our community says about CropCircle.</p>
        </header>

        @if($featuredTestimonials->isNotEmpty())
            <div class="cc-testimonials__timeline" data-testimonial-path>
                <svg class="cc-testimonials__path" viewBox="0 0 100 1000" preserveAspectRatio="none" aria-hidden="true" focusable="false">
                    <path class="cc-testimonials__path-track" d="M50 0 C20 130 80 180 50 250 C20 380 80 430 50 500 C20 630 80 680 50 750 C20 880 80 930 50 1000" />
                    <path class="cc-testimonials__path-draw" data-testimonial-draw d="M50 0 C20 130 80 180 50 250 C20 380 80 430 50 500 C20 630 80 680 50 750 C20 880 80 930 50 1000" />
                </svg>
                <ol class="cc-testimonials__list">
                    @foreach($featuredTestimonials as $feedback)
                        @php
                            $reviewerName = $feedback->user->name;
                            $reviewerInitials = collect(preg_split('/\s+/', trim($reviewerName)))->filter()->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('');
                        @endphp
                        <li class="cc-testimonials__item" data-testimonial-item>
                            <article class="cc-testimonials__card" data-testimonial-card>
                                <div class="cc-testimonials__meta">
                                    <span class="cc-testimonials__label"><i class="bi {{ $feedback->submitter_role === 'farmer' ? 'bi-flower1' : 'bi-basket2' }}" aria-hidden="true"></i> {{ ucfirst($feedback->submitter_role) }} Story</span>
                                    @if($feedback->rating)
                                    <span class="cc-testimonials__stars" aria-label="Rated {{ $feedback->rating }} out of 5 stars">
                                        @for($star = 1; $star <= 5; $star++)
                                            <i class="bi {{ $star <= $feedback->rating ? 'bi-star-fill' : 'bi-star' }}" style="--star-index: {{ $star }}" aria-hidden="true"></i>
                                        @endfor
                                    </span>
                                    @endif
                                </div>
                                <p class="cc-testimonials__quote">&ldquo;{{ $feedback->message }}&rdquo;</p>
                                <div class="cc-testimonials__author">
                                    <span class="cc-testimonials__initials" aria-hidden="true">{{ $reviewerInitials ?: 'C' }}</span>
                                    <span><span class="cc-testimonials__name">{{ $feedback->user->name }}</span><span class="cc-testimonials__role">{{ ucfirst($feedback->submitter_role) }} &middot; CropCircle website review</span></span>
                                </div>
                            </article>
                            <span class="cc-testimonials__marker" data-testimonial-marker aria-hidden="true"><i class="bi bi-leaf"></i></span>
                        </li>
                    @endforeach
                </ol>
            </div>
        @else
            <div class="cc-testimonials__empty">
                <span class="cc-testimonials__empty-icon"><i class="bi bi-chat-heart" aria-hidden="true"></i></span>
                <h3>Community stories are on their way</h3>
                <p>Customer and farmer website reviews appear here only after the submitter consents to public display and an administrator reviews and features them. Issues, suggestions, and unselected feedback remain private.</p>
            </div>
        @endif

        @if($featuredTestimonials->isNotEmpty())

        @endif
    </div>
</section>

@if($featuredTestimonials->isNotEmpty())
    @push('scripts')
    <script>
        (function () {
            var reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            document.querySelectorAll('[data-testimonial-path]').forEach(function (timeline) {
                var path = timeline.querySelector('[data-testimonial-draw]');
                var cards = Array.prototype.slice.call(timeline.querySelectorAll('[data-testimonial-card]'));
                var markers = Array.prototype.slice.call(timeline.querySelectorAll('[data-testimonial-marker]'));
                var length = 0;

                function clamp(value) { return Math.max(0, Math.min(1, value)); }

                function buildPath() {
                    if (!path || !timeline.getBoundingClientRect) return;
                    var bounds = timeline.getBoundingClientRect();
                    var height = Math.max(bounds.height, 1);
                    var points = markers.map(function (marker) {
                        var rect = marker.getBoundingClientRect();
                        return clamp((rect.top + rect.height / 2 - bounds.top) / height) * 1000;
                    }).sort(function (a, b) { return a - b; });
                    var d = 'M 50 0';
                    var previous = 0;
                    points.forEach(function (point) {
                        var delta = Math.max(1, point - previous);
                        d += ' C 18 ' + (previous + delta * .33) + ', 82 ' + (previous + delta * .67) + ', 50 ' + point;
                        previous = point;
                    });
                    var remaining = Math.max(1, 1000 - previous);
                    d += ' C 18 ' + (previous + remaining * .33) + ', 82 ' + (previous + remaining * .67) + ', 50 1000';
                    path.setAttribute('d', d);
                    length = path.getTotalLength();
                    path.style.strokeDasharray = length;
                }

                function update() {
                    if (reducedMotion) {
                        if (path && length) path.style.strokeDashoffset = 0;
                        cards.forEach(function (card) {
                            card.style.setProperty('--card-reveal', 1);
                            card.style.setProperty('--card-offset', '0px');
                            card.style.setProperty('--card-scale', 1);
                        });
                        markers.forEach(function (marker) {
                            marker.style.setProperty('--card-reveal', 1);
                            marker.style.setProperty('--marker-scale', 1);
                        });
                        return;
                    }

                    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;
                    var bounds = timeline.getBoundingClientRect();
                    var progress = clamp((viewportHeight * .72 - bounds.top) / (bounds.height + viewportHeight * .44));
                    if (path && length) path.style.strokeDashoffset = length * (1 - progress);

                    cards.forEach(function (card, index) {
                        var cardBounds = card.getBoundingClientRect();
                        var reveal = clamp((viewportHeight * .82 - cardBounds.top) / (viewportHeight * .25));
                        card.style.setProperty('--card-reveal', reveal.toFixed(3));
                        card.style.setProperty('--card-offset', ((1 - reveal) * 22).toFixed(1) + 'px');
                        card.style.setProperty('--card-scale', (.975 + reveal * .025).toFixed(3));
                        var marker = markers[index];
                        if (marker) {
                            marker.style.setProperty('--card-reveal', reveal.toFixed(3));
                            marker.style.setProperty('--marker-scale', (.72 + reveal * .28).toFixed(3));
                        }
                    });
                }

                var scheduled = false;
                function requestUpdate() {
                    if (scheduled) return;
                    scheduled = true;
                    window.requestAnimationFrame(function () {
                        scheduled = false;
                        update();
                    });
                }

                buildPath();
                update();
                window.addEventListener('scroll', requestUpdate, { passive: true });
                window.addEventListener('resize', function () { buildPath(); requestUpdate(); });
            });
        })();
    </script>
    @endpush
@endif
