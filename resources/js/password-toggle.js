export function initPasswordToggle(options = {}) {
    const {
        checkboxId = 'showPassword',
        passwordFieldId = 'password',
        confirmFieldId = 'password_confirmation'
    } = options;

    const checkbox = document.getElementById(checkboxId);
    if (!checkbox) return;

    const passwordInput = document.getElementById(passwordFieldId);
    const confirmInput = document.getElementById(confirmFieldId);

    checkbox.addEventListener('change', function () {
        const type = this.checked ? 'text' : 'password';
        if (passwordInput) passwordInput.type = type;
        if (confirmInput) confirmInput.type = type;
    });
}

export default initPasswordToggle;
