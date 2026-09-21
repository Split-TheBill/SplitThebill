const input = document.getElementById('phone-number');

if (input) {
    const normalizePhoneNumber = () => {
        let value = input.value.trim();

        if (value === '') {
            return;
        }

        if (value.startsWith('0')) {
            value = `+62${value.slice(1)}`;
        } else if (!value.startsWith('+62')) {
            value = `+62${value.replace(/^\+/, '')}`;
        }

        const numbersAfterPrefix = value.slice(3).replace(/\D/g, '').replace(/^0+/, '');
        input.value = `+62${numbersAfterPrefix}`;
    };

    input.addEventListener('input', normalizePhoneNumber);
    input.addEventListener('blur', normalizePhoneNumber);
}
