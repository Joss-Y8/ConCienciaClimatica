// Elementos del DOM
const loginForm = document.getElementById("login-form");
const registerForm = document.getElementById("register-form");
const mensaje = document.getElementById("mensaje");

// Función para mostrar el formulario de registro
function mostrarRegistro() {
  loginForm.classList.add("hidden");
  registerForm.classList.remove("hidden");

  // Cambiar el mensaje del panel izquierdo
  mensaje.innerText = "Únete a ConCienciaClimática y forma parte del cambio hacia un futuro más verde.";
}

// Función para mostrar el formulario de login
function mostrarLogin() {
  registerForm.classList.add("hidden");
  loginForm.classList.remove("hidden");

  // Restaurar el mensaje original
  mensaje.innerText = "Un planeta más verde, comienza con una mente informada.";
}
