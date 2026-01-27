export function updateInputBackground(input, checkReadOnly = false) {
    const wrapper = input.closest('.input-wrapper');
    if (wrapper && (!checkReadOnly || !input.readOnly)) {
        wrapper.classList.toggle('filled', input.value.trim() !== '');
    }
}

export function initInputBackgrounds(selector = '.input-wrapper input', checkReadOnly = false) {
    const inputs = document.querySelectorAll(selector);
    inputs.forEach(input => {
        updateInputBackground(input, checkReadOnly);
        input.addEventListener('input', () => updateInputBackground(input, checkReadOnly));
        input.addEventListener('change', () => updateInputBackground(input, checkReadOnly));
    });
}