document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[role="tablist"]').forEach((tabList) => {
        const tabs = Array.from(tabList.querySelectorAll('[role="tab"][data-target-tab]'));

        if (tabs.length === 0) {
            return;
        }

        const activateTab = (selectedTab, moveFocus = false) => {
            tabs.forEach((tab) => {
                const panelSelector = tab.getAttribute('data-target-tab');
                const panel = panelSelector ? document.querySelector(panelSelector) : null;
                const isSelected = tab === selectedTab;

                tab.setAttribute('aria-selected', String(isSelected));
                tab.setAttribute('tabindex', isSelected ? '0' : '-1');

                if (panel) {
                    panel.hidden = !isSelected;
                    panel.classList.toggle('hidden', !isSelected);
                }
            });

            if (moveFocus) {
                selectedTab.focus();
            }
        };

        tabs.forEach((tab, index) => {
            tab.addEventListener('click', () => activateTab(tab));
            tab.addEventListener('keydown', (event) => {
                let nextIndex = null;

                if (event.key === 'ArrowRight' || event.key === 'ArrowDown') {
                    nextIndex = (index + 1) % tabs.length;
                } else if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') {
                    nextIndex = (index - 1 + tabs.length) % tabs.length;
                } else if (event.key === 'Home') {
                    nextIndex = 0;
                } else if (event.key === 'End') {
                    nextIndex = tabs.length - 1;
                }

                if (nextIndex !== null) {
                    event.preventDefault();
                    activateTab(tabs[nextIndex], true);
                }
            });
        });

        const initiallySelected = tabs.find((tab) => tab.getAttribute('aria-selected') === 'true') ?? tabs[0];
        activateTab(initiallySelected);
    });
});
