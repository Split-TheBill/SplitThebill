async function copyTransferTo(button) {
    const bankDetails = button?.closest('.bank-details');
    const transferTarget = bankDetails?.querySelector('.Transfer-To');
    const label = button?.querySelector('[data-copy-label]');

    if (!transferTarget) {
        return;
    }

    const transferText = transferTarget.textContent.trim();

    try {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(transferText);
        } else {
            const temporaryInput = document.createElement('textarea');
            temporaryInput.value = transferText;
            temporaryInput.setAttribute('readonly', '');
            temporaryInput.style.position = 'fixed';
            temporaryInput.style.opacity = '0';
            document.body.appendChild(temporaryInput);
            temporaryInput.select();
            document.execCommand('copy');
            temporaryInput.remove();
        }

        if (label) {
            const originalText = label.textContent;
            label.textContent = 'Tersalin';
            window.setTimeout(() => {
                label.textContent = originalText;
            }, 1800);
        }
    } catch (error) {
        if (label) {
            label.textContent = 'Gagal menyalin';
        }
    }
}
