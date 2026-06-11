import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

/* ---------------------------------------------------------------------------
   Lightweight interactions. No dependencies, respects reduced-motion and
   degrades gracefully: elements only start hidden once the `.js` class is set
   (added inline in <head>), and are shown immediately if anything is missing.
--------------------------------------------------------------------------- */
(function () {
    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const revealAll = () => {
        document.querySelectorAll('.reveal').forEach((el) => el.classList.add('is-visible'));
    };

    /* Scroll reveal. */
    const initReveal = () => {
        const targets = document.querySelectorAll('.reveal');

        if (prefersReduced || !('IntersectionObserver' in window) || targets.length === 0) {
            revealAll();
            return;
        }

        const observer = new IntersectionObserver(
            (entries, obs) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        obs.unobserve(entry.target);
                    }
                });
            },
            { rootMargin: '0px 0px -8% 0px', threshold: 0.08 }
        );

        targets.forEach((el) => observer.observe(el));
    };

    /* Fade images in once loaded (opacity only, so no layout shift). */
    const initImageReveal = () => {
        document.querySelectorAll('img.img-reveal').forEach((img) => {
            const show = () => img.classList.add('is-loaded');

            if (prefersReduced || img.complete) {
                show();
                return;
            }

            img.addEventListener('load', show, { once: true });
            img.addEventListener('error', show, { once: true });
        });
    };

    /* Give the sticky header a stronger shadow once the page scrolls. */
    const initHeader = () => {
        const header = document.querySelector('[data-elevate]');

        if (!header) {
            return;
        }

        let ticking = false;

        const update = () => {
            header.classList.toggle('is-scrolled', window.scrollY > 8);
            ticking = false;
        };

        window.addEventListener(
            'scroll',
            () => {
                if (!ticking) {
                    ticking = true;
                    requestAnimationFrame(update);
                }
            },
            { passive: true }
        );

        update();
    };

    const init = () => {
        initReveal();
        initImageReveal();
        initHeader();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
