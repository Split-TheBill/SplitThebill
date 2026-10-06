import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

export function initMotion() {
    const hero = document.querySelector('[data-interactive-hero]');
    if (!hero) return;

    // Keep motion quiet: one entrance and short, one-time section reveals.
    // Content remains visible if JavaScript is slow or disabled.
    gsap.matchMedia().add('(prefers-reduced-motion: no-preference)', () => {
        const intro = [...hero.querySelectorAll('.reveal-on-scroll')];

        if (intro.length && performance.now() < 1500 && window.scrollY < 80) {
            gsap.fromTo(intro,
                { y: 16 },
                {
                    y: 0,
                    duration: 0.65,
                    stagger: 0.08,
                    ease: 'power2.out',
                    onComplete: () => gsap.set(intro, { clearProps: 'transform' }),
                },
            );
        }

        document.querySelectorAll('.reveal-on-scroll').forEach((element) => {
            if (element.closest('[data-interactive-hero]')) return;
            if (element.parentElement?.closest('.reveal-on-scroll')) return;

            ScrollTrigger.create({
                trigger: element,
                start: 'top 90%',
                once: true,
                onEnter: () => gsap.fromTo(element,
                    { y: 14 },
                    {
                        y: 0,
                        duration: 0.65,
                        ease: 'power2.out',
                        onComplete: () => gsap.set(element, { clearProps: 'transform' }),
                    },
                ),
            });
        });
    });
}
