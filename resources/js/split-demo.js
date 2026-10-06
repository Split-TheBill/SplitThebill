const MIN_PEOPLE = 2;
const MAX_PEOPLE = 6;
const rupiah = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
});

export const formatSplitPrice = (amount) => rupiah.format(Math.round(amount)).replace(/\u00a0/g, ' ');

// The calculation belongs in the main bundle so it also works without GSAP.
export function initSplitDemo() {
    document.querySelectorAll('[data-split-demo]').forEach((demo) => {
        if (demo.dataset.splitReady === 'true') return;

        const minus = demo.querySelector('[data-split-minus]');
        const plus = demo.querySelector('[data-split-plus]');
        const output = demo.querySelector('[data-split-count]');
        const price = demo.querySelector('[data-split-price]');
        const shares = demo.querySelector('[data-split-shares]');
        const announcement = demo.querySelector('[data-split-announcement]');
        const total = Number(demo.dataset.total);

        if (!minus || !plus || !output || !price || !shares || !Number.isFinite(total) || total <= 0) return;

        let people = Math.max(MIN_PEOPLE, Math.min(MAX_PEOPLE, Math.round(Number(demo.dataset.count)) || 4));
        const shareElements = [...shares.querySelectorAll('[data-split-share]')];
        const template = shareElements[0];

        for (let index = shareElements.length; index < MAX_PEOPLE; index += 1) {
            const share = template ? template.cloneNode(true) : document.createElement('span');
            share.removeAttribute('id');
            share.setAttribute('data-split-share', '');
            shares.appendChild(share);
            shareElements.push(share);
        }

        shareElements.forEach((share, index) => {
            share.dataset.flipId = `split-share-${index + 1}`;
            share.setAttribute('aria-hidden', 'true');
            share.textContent = String(index + 1).padStart(2, '0');
        });

        const render = (announce = false) => {
            const amount = Math.round(total / people);
            const formatted = formatSplitPrice(amount);

            demo.dataset.count = String(people);
            demo.dataset.price = String(amount);
            output.textContent = String(people);
            price.textContent = formatted;
            minus.disabled = people === MIN_PEOPLE;
            plus.disabled = people === MAX_PEOPLE;

            shareElements.forEach((share, index) => {
                share.hidden = index >= people;
            });

            if (announce && announcement) {
                announcement.textContent = `${people} orang, ${formatted} per orang. Simulasi belum termasuk biaya admin.`;
            }

            return amount;
        };

        const changePeople = (direction) => {
            const next = Math.max(MIN_PEOPLE, Math.min(MAX_PEOPLE, people + direction));
            if (next === people) return;

            const detail = {
                previousCount: people,
                count: next,
                previousPrice: Math.round(total / people),
                price: Math.round(total / next),
            };

            // Motion can record the current layout before the functional update.
            demo.dispatchEvent(new CustomEvent('stb:split-before-change', { detail }));
            people = next;
            render(true);
            demo.dispatchEvent(new CustomEvent('stb:split-change', { detail }));
        };

        minus.addEventListener('click', () => changePeople(-1));
        plus.addEventListener('click', () => changePeople(1));
        render();
        demo.dataset.splitReady = 'true';
    });
}
