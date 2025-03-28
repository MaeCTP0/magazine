document.getElementById('registration-form').addEventListener('submit', function (event) {
    event.preventDefault();

    // Очистка сообщений об ошибках
    clearErrors();

    // Получаем значения полей
    const email = document.getElementById('email').value;
    const firstName = document.getElementById('first-name').value;
    const lastName = document.getElementById('last-name').value;
    const age = document.getElementById('age').value;
    const gender = document.querySelector('input[name="gender"]:checked');
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm-password').value;
    const agree = document.getElementById('agree').checked;

    let isValid = true;

    // Валидация e-mail
    if (!validateEmail(email)) {
        showError('email-error', 'Введите корректный e-mail.');
        isValid = false;
    }

    // Валидация имени
    if (firstName.trim() === '') {
        showError('first-name-error', 'Имя обязательно для заполнения.');
        isValid = false;
    }

    // Валидация фамилии
    if (lastName.trim() === '') {
        showError('last-name-error', 'Фамилия обязательна для заполнения.');
        isValid = false;
    }

    // Валидация возраста
    if (age < 1 || age > 120) {
        showError('age-error', 'Введите корректный возраст.');
        isValid = false;
    }

    // Валидация пола
    if (!gender) {
        showError('gender-error', 'Выберите пол.');
        isValid = false;
    }

    // Валидация пароля
    if (password.length < 6) {
        showError('password-error', 'Пароль должен содержать не менее 6 символов.');
        isValid = false;
    }

    // Валидация подтверждения пароля
    if (password !== confirmPassword) {
        showError('confirm-password-error', 'Пароли не совпадают.');
        isValid = false;
    }

    // Валидация согласия
    if (!agree) {
        showError('agree-error', 'Необходимо согласие на обработку данных.');
        isValid = false;
    }

    // Если все данные корректны, отправляем форму
    if (isValid) {
        this.submit();
    }
});

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(String(email).toLowerCase());
}

function showError(elementId, message) {
    const errorElement = document.getElementById(elementId);
    errorElement.textContent = message;
    errorElement.style.display = 'block';
}

function clearErrors() {
    const errorMessages = document.querySelectorAll('.error-message');
    errorMessages.forEach(function (element) {
        element.textContent = '';
        element.style.display = 'none';
    });
}