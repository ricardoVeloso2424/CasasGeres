import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

/* ---------------------------------------------------------------------------
   Lightweight scroll reveal. No dependencies, respects reduced-motion and
   degrades gracefully: elements only start hidden once the `.js` class is set
   (added inline in <head>), and are shown immediately if anything is missing.
--------------------------------------------------------------------------- */
(function () {
    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const revealAll = () => {
        document.querySelectorAll('.reveal').forEach((el) => el.classList.add('is-visible'));
    };

    const init = () => {
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

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
