import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

const revealSelector = '.reveal-on-scroll';
const animatedCardsSelector = '.product-card, .step-card, .testimonial-card, .faq-item';
const clearRevealStyles = (targets) => gsap.set(targets, { clearProps: 'opacity,transform' });

export function initMotion() {
    const hero = document.querySelector('[data-interactive-hero]');
    // Checkout and admin pages stay still; the editorial motion belongs to the landing page.
    if (!hero) return;

    const heroAmbient = hero?.querySelector('[data-hero-ambient]');
    const stepsTrack = document.querySelector('[data-steps-track]');
    const stepCards = [...document.querySelectorAll('.step-card')];
    const scrollProgress = document.querySelector('[data-scroll-progress]');
    const animatedElements = new Set();
    const liveTweens = new Set();
    const motion = gsap.matchMedia();

    const track = (tween) => {
        liveTweens.add(tween);
        return tween;
    };

    const reveal = (element, options = {}) => {
        const isCard = element.matches(animatedCardsSelector);
        const delayClass = element.className.match(/(?:^|\s)reveal-delay-([1-4])(?:\s|$)/);
        const delay = options.delay ?? (delayClass ? Number(delayClass[1]) * 0.08 : 0);

        ScrollTrigger.create({
            trigger: element,
            start: 'top 88%',
            once: true,
            onEnter: () => {
                animatedElements.add(element);
                track(gsap.fromTo(element,
                    { opacity: 0, y: isCard ? 32 : 24, scale: isCard ? 0.985 : 1 },
                    {
                        opacity: 1,
                        y: 0,
                        scale: 1,
                        duration: isCard ? 0.82 : 0.75,
                        delay,
                        ease: 'power3.out',
                        overwrite: 'auto',
                        onComplete: () => clearRevealStyles(element),
                    },
                ));
            },
        });
    };

    motion.add('(prefers-reduced-motion: no-preference)', () => {
        if (hero) {
            const intro = [...hero.querySelectorAll('.site-shell > .reveal-on-scroll')];

            // Skip the entrance if the optional chunk arrived after the page was already read.
            if (intro.length && performance.now() < 1500 && window.scrollY < 80) {
                intro.forEach((element) => animatedElements.add(element));
                track(gsap.fromTo(intro,
                    { opacity: 0, y: 28 },
                    {
                        opacity: 1,
                        y: 0,
                        duration: 0.95,
                        delay: 0.08,
                        stagger: 0.12,
                        ease: 'power3.out',
                        onComplete: () => clearRevealStyles(intro),
                    },
                ));
            }

            if (heroAmbient) {
                track(gsap.fromTo(heroAmbient,
                    { yPercent: -5 },
                    {
                        yPercent: 8,
                        ease: 'none',
                        scrollTrigger: {
                            trigger: hero,
                            start: 'top top',
                            end: 'bottom top',
                            scrub: 0.8,
                        },
                    },
                ));
                animatedElements.add(heroAmbient);
            }
        }

        const genericReveals = [...document.querySelectorAll(revealSelector)]
            .filter((element) => !element.closest('[hidden]'))
            .filter((element) => !element.closest('[data-interactive-hero]'))
            .filter((element) => !element.matches(animatedCardsSelector))
            .filter((element) => !element.parentElement?.closest(revealSelector))
            .filter((element) => !element.querySelector(animatedCardsSelector))
            .filter((element) => !element.querySelector('[data-count-to]'));

        genericReveals.forEach((element) => reveal(element));

        document.querySelectorAll(animatedCardsSelector).forEach((card, index) => {
            if (!card.closest('[hidden]')) {
                reveal(card, { delay: (index % 3) * 0.055 });
            }
        });

        const counters = [...document.querySelectorAll('[data-count-to]')];
        const stats = counters[0]?.closest('section');

        if (stats && counters.length) {
            const cells = counters.map((counter) => counter.closest('div.flex'));
            const formatter = new Intl.NumberFormat('id-ID');

            ScrollTrigger.create({
                trigger: stats,
                start: 'top 85%',
                once: true,
                onEnter: () => {
                    cells.forEach((cell, index) => {
                        if (!cell) return;
                        animatedElements.add(cell);
                        track(gsap.fromTo(cell,
                            { opacity: 0, y: 18 },
                            {
                                opacity: 1,
                                y: 0,
                                duration: 0.7,
                                delay: index * 0.09,
                                ease: 'power3.out',
                                onComplete: () => clearRevealStyles(cell),
                            },
                        ));
                    });

                    counters.forEach((counter, index) => {
                        const target = Number(counter.dataset.countTo);
                        if (!Number.isFinite(target)) return;

                        const value = { current: 0 };
                        const suffix = counter.dataset.countSuffix || '';
                        track(gsap.to(value, {
                            current: target,
                            duration: 1.4,
                            delay: index * 0.09,
                            ease: 'power2.out',
                            onUpdate: () => {
                                counter.textContent = `${formatter.format(Math.round(value.current))}${suffix}`;
                            },
                            onComplete: () => {
                                counter.textContent = `${formatter.format(target)}${suffix}`;
                            },
                        }));
                    });
                },
            });
        }

        if (scrollProgress) {
            gsap.set(scrollProgress, { scaleX: 0 });
            track(gsap.to(scrollProgress, {
                scaleX: 1,
                ease: 'none',
                scrollTrigger: {
                    start: 0,
                    end: () => ScrollTrigger.maxScroll(window),
                    scrub: 0.15,
                    invalidateOnRefresh: true,
                },
            }));
        }

        return () => {
            liveTweens.forEach((tween) => tween.kill());
            clearRevealStyles([...animatedElements]);
            counters.forEach((counter) => {
                const target = Number(counter.dataset.countTo);
                if (Number.isFinite(target)) {
                    counter.textContent = `${new Intl.NumberFormat('id-ID').format(target)}${counter.dataset.countSuffix || ''}`;
                }
            });
            if (scrollProgress) gsap.set(scrollProgress, { clearProps: 'transform' });
            liveTweens.clear();
            animatedElements.clear();
        };
    });

    motion.add('(min-width: 1024px) and (hover: hover) and (pointer: fine) and (prefers-reduced-motion: no-preference)', () => {
        if (hero && heroAmbient) {
            const moveX = gsap.quickTo(heroAmbient, 'x', { duration: 0.7, ease: 'power3.out' });
            const moveY = gsap.quickTo(heroAmbient, 'y', { duration: 0.7, ease: 'power3.out' });
            const onPointerMove = (event) => {
                const bounds = hero.getBoundingClientRect();
                moveX(((event.clientX - bounds.left) / bounds.width - 0.5) * 36);
                moveY(((event.clientY - bounds.top) / bounds.height - 0.5) * 36);
            };
            const onPointerLeave = () => {
                moveX(0);
                moveY(0);
            };

            hero.addEventListener('pointermove', onPointerMove, { passive: true });
            hero.addEventListener('pointerleave', onPointerLeave);

            return () => {
                hero.removeEventListener('pointermove', onPointerMove);
                hero.removeEventListener('pointerleave', onPointerLeave);
                gsap.killTweensOf(heroAmbient, 'x,y');
            };
        }

        return undefined;
    });

    motion.add('(min-width: 1024px) and (prefers-reduced-motion: no-preference)', () => {
        if (!stepsTrack || !stepCards.length) return undefined;

        const stepsProgress = stepsTrack.querySelector('[data-steps-progress]');
        if (stepsProgress) {
            gsap.set(stepsProgress, { scaleY: 0 });
            gsap.to(stepsProgress, {
                scaleY: 1,
                ease: 'none',
                scrollTrigger: {
                    trigger: stepsTrack,
                    start: 'top 72%',
                    end: 'bottom 34%',
                    scrub: 0.5,
                },
            });
        }

        stepCards.forEach((card) => {
            ScrollTrigger.create({
                trigger: card,
                start: 'top 67%',
                end: 'bottom 43%',
                onToggle: (trigger) => card.classList.toggle('is-active', trigger.isActive),
            });
        });

        return () => {
            stepCards.forEach((card) => card.classList.remove('is-active'));
            if (stepsProgress) gsap.set(stepsProgress, { clearProps: 'transform' });
        };
    });
}
