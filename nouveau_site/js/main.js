/* =============================================================
   MAIN.JS — Interactions globales (curseur, scroll, animations)
   ============================================================= */
(function () {
    'use strict';

    const isTouch = ('ontouchstart' in window) || navigator.maxTouchPoints > 0;
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Marquer "loaded" pour déclencher l'apparition du nom dans le hero
    window.addEventListener('load', () => {
        document.documentElement.classList.add('is-loaded');
    });

    /* 1) CURSEUR PERSONNALISÉ */
    const ring = document.querySelector('.cursor-ring');
    const dot = document.querySelector('.cursor-dot');
    if (ring && dot && !isTouch) {
        let mx = window.innerWidth / 2, my = window.innerHeight / 2;
        let rx = mx, ry = my;
        window.addEventListener('mousemove', (e) => {
            mx = e.clientX; my = e.clientY;
            dot.style.transform = `translate(${mx}px, ${my}px) translate(-50%, -50%)`;
        }, { passive: true });
        (function animate() {
            rx += (mx - rx) * 0.18;
            ry += (my - ry) * 0.18;
            ring.style.transform = `translate(${rx}px, ${ry}px) translate(-50%, -50%)`;
            requestAnimationFrame(animate);
        })();
        const hoverable = 'a, button, .project-card, .filter-btn, .filter-link, .dropdown-toggle, .dropdown-item, .cv-pill, .project-tag, label, [data-hover]';
        // Délégation sur tout le document, y compris contenu injecté dynamiquement
        document.addEventListener('mouseover', (e) => {
            if (e.target.closest && e.target.closest(hoverable)) ring.classList.add('hover');
        });
        document.addEventListener('mouseout', (e) => {
            if (e.target.closest && e.target.closest(hoverable)) ring.classList.remove('hover');
        });
        document.addEventListener('mouseleave', () => { ring.style.opacity = '0'; dot.style.opacity = '0'; });
        document.addEventListener('mouseenter', () => { ring.style.opacity = '1'; dot.style.opacity = '1'; });
    }

    /* 2) HEADER au scroll */
    const header = document.querySelector('.header');
    if (header) {
        const hasHero = !!document.querySelector('.hero');
        const onScroll = () => {
            if (!hasHero || window.scrollY > 50) header.classList.add('scrolled');
            else header.classList.remove('scrolled');
        };
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    }

    /* 3) Animations au scroll (IntersectionObserver) */
    function bindReveals() {
        const reveals = document.querySelectorAll('[data-reveal]:not(.is-visible)');
        if (!reveals.length) return;
        if ('IntersectionObserver' in window && !prefersReducedMotion) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });
            reveals.forEach(el => observer.observe(el));
        } else {
            reveals.forEach(el => el.classList.add('is-visible'));
        }
    }
    bindReveals();
    // re-binder après chargement async des projets
    const mo = new MutationObserver(() => bindReveals());
    mo.observe(document.body, { childList: true, subtree: true });

    /* 4) Compteurs animés */
    const counters = document.querySelectorAll('.stat-number[data-count]');
    if (counters.length && 'IntersectionObserver' in window) {
        const animateCount = (el) => {
            const target = parseInt(el.dataset.count, 10);
            if (isNaN(target)) return;
            const plusEl = el.querySelector('.plus');
            const plusHTML = plusEl ? plusEl.outerHTML : '';
            const duration = 1400;
            const start = performance.now();
            function tick(now) {
                const t = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - t, 3);
                const value = Math.round(target * eased);
                el.innerHTML = value + plusHTML;
                if (t < 1) requestAnimationFrame(tick);
            }
            requestAnimationFrame(tick);
        };
        const countObs = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCount(entry.target);
                    countObs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        counters.forEach(c => countObs.observe(c));
    }

    /* 5) Transitions entre pages (avec exclusion des ancres internes) */
    const overlay = document.querySelector('.page-transition');
    if (overlay && !prefersReducedMotion) {
        overlay.classList.remove('active');
        document.addEventListener('click', (e) => {
            const a = e.target.closest('a');
            if (!a) return;
            const href = a.getAttribute('href');
            if (!href || href === '#') return;
            if (href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:')) return;
            if (a.target === '_blank') return;
            try {
                const url = new URL(href, window.location.href);
                if (url.origin !== window.location.origin) return;
                if (url.hash) return; // laisser le navigateur gérer tous les liens ancre
            } catch (err) { return; }
            e.preventDefault();
            overlay.classList.add('active');
            setTimeout(() => { window.location.href = href; }, 450);
        });
    }
})();
