/* =============================================================
   PORTFOLIO — Interactions & animations
   ============================================================= */
(function(){
    'use strict';

    const isTouch = ('ontouchstart' in window) || navigator.maxTouchPoints > 0;
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* -------------------------------------------------------------
       1) CURSEUR PERSONNALISÉ
       ------------------------------------------------------------- */
    const ring = document.querySelector('.cursor-ring');
    const dot  = document.querySelector('.cursor-dot');

    if (ring && dot && !isTouch) {
        let mx = window.innerWidth / 2, my = window.innerHeight / 2;
        let rx = mx, ry = my;

        window.addEventListener('mousemove', (e) => {
            mx = e.clientX; my = e.clientY;
            dot.style.transform = `translate(${mx}px, ${my}px) translate(-50%, -50%)`;
        }, { passive: true });

        // Animation douce du ring
        function animate(){
            rx += (mx - rx) * 0.18;
            ry += (my - ry) * 0.18;
            ring.style.transform = `translate(${rx}px, ${ry}px) translate(-50%, -50%)`;
            requestAnimationFrame(animate);
        }
        animate();

        // hover sur éléments cliquables
        const hoverable = 'a, button, .project-card, .filter-btn, .domain-btn, .dropdown-toggle, .dropdown-item, .cv-pill, [data-hover]';
        document.addEventListener('mouseover', (e) => {
            if (e.target.closest(hoverable)) ring.classList.add('hover');
        });
        document.addEventListener('mouseout', (e) => {
            if (e.target.closest && e.target.closest(hoverable)) ring.classList.remove('hover');
        });

        // disparition hors fenêtre
        document.addEventListener('mouseleave', () => {
            ring.style.opacity = '0'; dot.style.opacity = '0';
        });
        document.addEventListener('mouseenter', () => {
            ring.style.opacity = '1'; dot.style.opacity = '1';
        });
    }

    /* -------------------------------------------------------------
       2) HEADER : changement d'état au scroll
       ------------------------------------------------------------- */
    const header = document.querySelector('.header');
    if (header) {
        const onScroll = () => {
            if (window.scrollY > 50) header.classList.add('scrolled');
            else header.classList.remove('scrolled');
        };
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    }

    /* -------------------------------------------------------------
       3) ANIMATIONS AU SCROLL (IntersectionObserver)
       ------------------------------------------------------------- */
    const reveals = document.querySelectorAll('[data-reveal]');
    if (reveals.length && 'IntersectionObserver' in window && !prefersReducedMotion) {
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

    /* -------------------------------------------------------------
       4) PARALLAXE LÉGER (images du hero)
       ------------------------------------------------------------- */
    const parallaxItems = document.querySelectorAll('.parallax');
    if (parallaxItems.length && !prefersReducedMotion) {
        let ticking = false;
        const updateParallax = () => {
            const sy = window.scrollY;
            parallaxItems.forEach(el => {
                const speed = parseFloat(el.dataset.parallax || '0.25');
                const offset = sy * speed;
                el.style.transform = `translate3d(0, ${offset}px, 0)`;
            });
            ticking = false;
        };
        window.addEventListener('scroll', () => {
            if (!ticking) {
                window.requestAnimationFrame(updateParallax);
                ticking = true;
            }
        }, { passive: true });
        updateParallax();
    }

    /* -------------------------------------------------------------
       5) COMPTEURS ANIMÉS (statistiques)
       ------------------------------------------------------------- */
    const counters = document.querySelectorAll('.stat-number[data-count]');
    if (counters.length && 'IntersectionObserver' in window) {
        const animateCount = (el) => {
            const target = parseInt(el.dataset.count, 10);
            const hasPlus = el.querySelector('.plus');
            const plusHTML = hasPlus ? hasPlus.outerHTML : '';
            const duration = 1400;
            const start = performance.now();
            function tick(now){
                const t = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - t, 3);
                const value = Math.round(target * eased);
                el.innerHTML = (hasPlus && el.innerHTML.startsWith('<span')) ? plusHTML + value : value + (hasPlus ? plusHTML : '');
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

    /* -------------------------------------------------------------
       6) TRANSITIONS ENTRE PAGES
       ------------------------------------------------------------- */
    const overlay = document.querySelector('.page-transition');
    if (overlay && !prefersReducedMotion) {
        // au chargement : on cache l'overlay s'il est déjà actif
        overlay.classList.remove('active');

        document.addEventListener('click', (e) => {
            const a = e.target.closest('a');
            if (!a) return;
            const href = a.getAttribute('href');
            if (!href) return;
            // ignorer ancres, externes, mailto, tel, target=_blank
            if (href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:')) return;
            if (a.target === '_blank') return;
            try {
                const url = new URL(href, window.location.href);
                if (url.origin !== window.location.origin) return;
                if (url.pathname === window.location.pathname && url.hash) return;
            } catch(err) { return; }

            e.preventDefault();
            overlay.classList.add('active');
            setTimeout(() => { window.location.href = href; }, 450);
        });
    }

    /* -------------------------------------------------------------
       7) MAGNETIC HOVER sur les cards (effet subtil)
       ------------------------------------------------------------- */
    if (!isTouch && !prefersReducedMotion) {
        document.querySelectorAll('.project-card').forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const r = card.getBoundingClientRect();
                const x = (e.clientX - r.left) / r.width - 0.5;
                const y = (e.clientY - r.top) / r.height - 0.5;
                card.style.transform = `translateY(-6px) perspective(900px) rotateX(${-y * 3}deg) rotateY(${x * 3}deg)`;
            });
            card.addEventListener('mouseleave', () => {
                card.style.transform = '';
            });
        });
    }

    /* -------------------------------------------------------------
       8) CACHER LE GRAIN SUR APPAREILS À FAIBLE PERF (optionnel)
       ------------------------------------------------------------- */
    // (laissé pour future ajustement)
})();
