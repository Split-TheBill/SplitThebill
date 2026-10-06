import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { Flip } from 'gsap/Flip';
import { formatSplitPrice } from './split-demo';

gsap.registerPlugin(ScrollTrigger, Flip);

export function initMotion() {
    const landing = document.querySelector('.landing');
    const hero = landing?.querySelector('[data-interactive-hero]');
    if (!hero) return;

    let introPlayed = false;
    const media = gsap.matchMedia();

    media.add({
        motion: '(prefers-reduced-motion: no-preference)',
        desktop: '(min-width: 1024px) and (min-height: 650px)',
        pointer: '(hover: hover) and (pointer: fine)',
    }, (context) => {
        if (!context.conditions.motion) return;

        const cleanups = [];
        const listen = (element, event, callback) => {
            element.addEventListener(event, callback);
            cleanups.push(() => element.removeEventListener(event, callback));
        };
        const receipt = hero.querySelector('[data-receipt]');
        const receiptStage = hero.querySelector('[data-receipt-stage]');
        const receiptRotation = receipt ? Number(gsap.getProperty(receipt, 'rotation')) : 5;

        // Animate position and decorative marks; text is always readable.
        if (!introPlayed && window.scrollY < 100) {
            introPlayed = true;
            const words = [...hero.querySelectorAll('[data-hero-word]')];
            const intro = [...hero.querySelectorAll('[data-hero-intro]')];
            const lines = [...hero.querySelectorAll('[data-receipt-line]')];
            const stamp = hero.querySelector('[data-receipt-stamp]');
            const entrance = gsap.timeline({ defaults: { ease: 'power3.out' } });

            if (words.length) entrance.fromTo(words, { yPercent: 18 }, { yPercent: 0, duration: 0.9, stagger: 0.09 }, 0);
            if (intro.length) entrance.fromTo(intro, { y: 14 }, { y: 0, duration: 0.75, stagger: 0.08 }, 0.14);
            if (receipt) entrance.fromTo(receipt, { y: 34, scale: 0.96 }, { y: 0, scale: 1, duration: 1 }, 0.1);
            if (lines.length) entrance.fromTo(lines, { scaleX: 0, transformOrigin: 'left center' }, { scaleX: 1, duration: 0.6, stagger: 0.07 }, 0.35);
            if (stamp) entrance.fromTo(stamp, { scale: 1.18 }, { scale: 1, duration: 0.5, ease: 'back.out(2)' }, 0.65);
        }

        if (receiptStage) {
            gsap.to(receiptStage, {
                y: context.conditions.desktop ? 65 : 24,
                ease: 'none',
                scrollTrigger: {
                    trigger: hero,
                    start: 'top top',
                    end: 'bottom top',
                    scrub: 0.8,
                    invalidateOnRefresh: true,
                },
            });
        }

        if (receipt && receiptStage && context.conditions.pointer) {
            const shift = gsap.quickTo(receipt, 'x', { duration: 0.65, ease: 'power3.out' });
            const skew = gsap.quickTo(receipt, 'skewY', { duration: 0.65, ease: 'power3.out' });
            const rotate = gsap.quickTo(receipt, 'rotation', { duration: 0.65, ease: 'power3.out' });

            listen(receiptStage, 'pointermove', (event) => {
                const bounds = receiptStage.getBoundingClientRect();
                const x = gsap.utils.clamp(-0.5, 0.5, (event.clientX - bounds.left) / bounds.width - 0.5);
                const y = gsap.utils.clamp(-0.5, 0.5, (event.clientY - bounds.top) / bounds.height - 0.5);
                shift(x * 10);
                skew(-y * 1.5);
                rotate(receiptRotation + x * 3);
            });

            listen(receiptStage, 'pointerleave', () => {
                shift(0);
                skew(0);
                rotate(receiptRotation);
            });
        }

        landing.querySelectorAll('[data-reveal]').forEach((element) => {
            if (element.closest('[data-interactive-hero]')) return;
            if (element.parentElement?.closest('[data-reveal]')) return;

            const reveal = context.add(null, () => {
                gsap.fromTo(element, { y: 22 }, { y: 0, duration: 0.8, ease: 'power3.out' });
            });

            ScrollTrigger.create({ trigger: element, start: 'top 91%', once: true, onEnter: reveal });
        });

        landing.querySelectorAll('[data-marquee]').forEach((marquee) => {
            const track = marquee.querySelector('[data-marquee-track]');
            if (!track) return;

            gsap.to(track, {
                x: () => -Math.min(Math.max(0, track.scrollWidth - marquee.clientWidth), marquee.clientWidth * 0.35),
                ease: 'none',
                scrollTrigger: {
                    trigger: marquee,
                    start: 'top bottom',
                    end: 'bottom top',
                    scrub: 0.8,
                    invalidateOnRefresh: true,
                },
            });
        });

        landing.querySelectorAll('[data-catalog-row], [data-resource-row]').forEach((row) => {
            const arrow = row.querySelector('[data-row-arrow]');
            const line = row.querySelector('[data-row-line]');
            const moveArrow = arrow ? gsap.quickTo(arrow, 'x', { duration: 0.35, ease: 'power3.out' }) : null;
            if (line) gsap.set(line, { scaleX: 0, transformOrigin: 'left center' });

            const enter = context.add(null, () => {
                moveArrow?.(7);
                if (line) gsap.to(line, { scaleX: 1, duration: 0.4, ease: 'power2.out', overwrite: true });
            });
            const leave = context.add(null, () => {
                moveArrow?.(0);
                if (line) gsap.to(line, { scaleX: 0, duration: 0.35, ease: 'power2.out', overwrite: true });
            });

            if (context.conditions.pointer) {
                listen(row, 'pointerenter', enter);
                listen(row, 'pointerleave', leave);
            }
            listen(row, 'focusin', enter);
            listen(row, 'focusout', leave);
        });

        const process = landing.querySelector('[data-process]');
        if (process) {
            const steps = [...process.querySelectorAll('[data-process-step]')];
            const states = [...process.querySelectorAll('[data-process-state]')];
            const visual = process.querySelector('[data-process-visual]');
            const progress = process.querySelector('[data-process-progress]');
            let activeStep = -1;

            const setActive = context.add(null, (index) => {
                if (index === activeStep) return;
                activeStep = index;
                steps.forEach((step, stepIndex) => step.classList.toggle('is-active', stepIndex === index));
                states.forEach((state, stateIndex) => state.classList.toggle('is-active', stateIndex === index));
                if (progress) gsap.to(progress, { scaleY: (index + 1) / Math.max(1, steps.length), duration: 0.45, ease: 'power2.out', overwrite: true });
            });

            if (progress) gsap.set(progress, { scaleY: 1 / Math.max(1, steps.length) });
            setActive(0);
            steps.forEach((step, index) => {
                ScrollTrigger.create({
                    trigger: step,
                    start: 'top 58%',
                    end: 'bottom 58%',
                    onEnter: () => setActive(index),
                    onEnterBack: () => setActive(index),
                });
            });

            if (visual && context.conditions.desktop && process.offsetHeight > visual.offsetHeight + 160) {
                ScrollTrigger.create({
                    trigger: visual,
                    pin: visual,
                    start: 'top 120px',
                    endTrigger: process,
                    end: () => `bottom ${visual.offsetHeight + 120}px`,
                    pinSpacing: false,
                    invalidateOnRefresh: true,
                    anticipatePin: 1,
                });
            }

            cleanups.push(() => {
                steps.forEach((step, index) => step.classList.toggle('is-active', index === 0));
                states.forEach((state, index) => state.classList.toggle('is-active', index === 0));
            });
        }

        landing.querySelectorAll('[data-split-demo]').forEach((demo) => {
            const price = demo.querySelector('[data-split-price]');
            const shares = [...demo.querySelectorAll('[data-split-share]')];
            const number = { value: Number(demo.dataset.price) || 0 };
            let layoutState;
            let priceTween;

            const beforeChange = () => {
                layoutState = Flip.getState(shares);
            };

            const afterChange = context.add(null, (event) => {
                if (layoutState) {
                    Flip.from(layoutState, {
                        targets: shares,
                        duration: 0.48,
                        ease: 'power3.inOut',
                        scale: true,
                        stagger: 0.02,
                        onEnter: (elements) => gsap.fromTo(elements, { scale: 0.75 }, { scale: 1, duration: 0.4, ease: 'back.out(1.5)' }),
                    });
                    layoutState = null;
                }

                if (price) {
                    priceTween?.kill();
                    priceTween = gsap.to(number, {
                        value: event.detail.price,
                        duration: 0.45,
                        ease: 'power2.out',
                        onUpdate: () => { price.textContent = formatSplitPrice(number.value); },
                        onComplete: () => { price.textContent = formatSplitPrice(event.detail.price); },
                    });
                }
            });

            listen(demo, 'stb:split-before-change', beforeChange);
            listen(demo, 'stb:split-change', afterChange);
            cleanups.push(() => {
                priceTween?.kill();
                if (price) price.textContent = formatSplitPrice(Number(demo.dataset.price));
            });
        });

        return () => cleanups.forEach((cleanup) => cleanup());
    }, landing);

    return () => media.revert();
}
