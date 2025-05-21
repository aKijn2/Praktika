document.querySelector("form").addEventListener("submit", function (e) {
    const phone = document.getElementById("phone");
    const nan = document.getElementById("nan");
    const password = document.getElementById("password");
    const confirm = document.getElementById("confirm_password");

    let valid = true;

    // Teléfono: exactamente 9 dígitos
    if (!/^\d{9}$/.test(phone.value)) {
        phone.setCustomValidity("Telefonoak 9 zenbaki izan behar ditu.");
        valid = false;
    } else {
        phone.setCustomValidity("");
    }

    // NAN: opcional, pero si se pone, debe cumplir formato
    if (nan.value !== "" && !/^\d{8}[A-Za-z]$/.test(nan.value)) {
        nan.setCustomValidity("NAN-ak 8 zenbaki eta letra bat izan behar ditu.");
        valid = false;
    } else {
        nan.setCustomValidity("");
    }

    // Contraseñas coinciden
    if (password.value !== confirm.value) {
        confirm.setCustomValidity("Pasahitzak ez dira berdinak.");
        valid = false;
    } else {
        confirm.setCustomValidity("");
    }

    // Contraseña segura
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/;
    if (!passwordRegex.test(password.value)) {
        password.setCustomValidity("Pasahitza ez da nahikoa segurua.");
        valid = false;
    } else {
        password.setCustomValidity("");
    }

    if (!valid) {
        e.preventDefault(); // No enviar si hay errores
        [...document.querySelectorAll("input")].forEach(input => input.reportValidity());
    }
});
