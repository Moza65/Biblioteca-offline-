// Mostrar/ocultar senha
const togglePassword = document.getElementById("togglePassword");
const password = document.getElementById("password");
const eyeIcon = document.getElementById("eyeIcon");
const eyeOffIcon = document.getElementById("eyeOffIcon");

if (togglePassword && password) {
    togglePassword.addEventListener("click", (e) => {
        e.preventDefault();
        if (password.type === "password") {
            password.type = "text";
            if (eyeIcon) eyeIcon.style.display = "block";
            if (eyeOffIcon) eyeOffIcon.style.display = "none";
        } else {
            password.type = "password";
            if (eyeIcon) eyeIcon.style.display = "none";
            if (eyeOffIcon) eyeOffIcon.style.display = "block";
        }
    });
}

// Simulação de login
const loginForm = document.getElementById("loginForm");
if (loginForm) {
    loginForm.addEventListener("submit", function(e) {
        e.preventDefault();
        const email = document.getElementById("email").value;
        
        if (email) {
            alert("Login realizado com sucesso! (simulação)");
            // Aqui você pode adicionar redirecionamento ou outras ações
        }
    });
}
