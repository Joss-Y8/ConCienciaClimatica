function mostrarmsj(divId, mensaje) {
  let div = document.getElementById(divId);
  div.innerHTML = mensaje;
  div.style.display = "block";
}

function ocultarMensaje(divId) {
  let div = document.getElementById(divId);
  div.style.display = "none";
}

// Validaciones individuales
function validar_nombre() {
  let entrada = document.getElementById("form-name").value.trim();
  if (entrada === "" || entrada.length > 100) {
      mostrarmsj("error1", "El campo de nombre no puede estar vacío y debe tener máximo 100 caracteres.");
      return false;
  }
  ocultarMensaje("error1");
  return true;
}

function validar_email() {
  let entrada = document.getElementById("form-email").value.trim();
  if (entrada === "" || entrada.length > 50) {
      mostrarmsj("error2", "El campo de email no puede estar vacío y debe tener máximo 50 caracteres.");
      return false;
  }
  ocultarMensaje("error2");
  return true;
}

function validar_edad() {
  let seleccion = document.querySelector('input[name="form-ed"]:checked');
  if (seleccion) {
      if (seleccion.value === "menor") {
          document.getElementById("mensaje-menor").style.display = "block";
          alert("Correo enviado: Permisos que el tutor debe firmar.");
          return true;
      } else {
          alert("Es el momento perfecto para que comiences a apoyar!!");
          return true;
      }
  } else {
      mostrarmsj("error4", "Por favor selecciona una opción de edad.");
      return false;
  }
}

function validar_opciones() {
  let seleccion = document.querySelector('input[name="forma_contribucion"]:checked');
  if (seleccion) {
      if (seleccion.value === "monetariamente") {
          alert("Gracias por apoyar monetariamente. Esto ayudará a financiar los proyectos.");
      } else if (seleccion.value === "otro") {
          alert("Gracias por brindarnos ideas para mejorar.");
      }
      ocultarMensaje("error5");
      return true;
  } else {
      mostrarmsj("error5", "Por favor selecciona una opción de contribución.");
      return false;
  }
}

function validar_numero() {
  let entrada = document.getElementById("form-tel").value.trim();
  let num = /^\d{10}$/;
  if (!num.test(entrada)) {
      mostrarmsj("error3", "El número telefónico debe tener exactamente 10 dígitos.");
      return false;
  }
  ocultarMensaje("error3");
  return true;
}

// Manejador del botón "Enviar"
document.getElementById("enviar").addEventListener("click", function (event) {
  event.preventDefault();

  // Ejecuta todas las validaciones y registra los resultados
  let esNombreValido = validar_nombre();
  let esEmailValido = validar_email();
  let esNumeroValido = validar_numero();
  let esEdadValida = validar_edad();
  let esOpcionesValida = validar_opciones();

  // Si todas las validaciones pasan, muestra el mensaje de éxito
  if (esNombreValido && esEmailValido && esNumeroValido && esEdadValida && esOpcionesValida) {
      alert("Formulario enviado correctamente. ¡Gracias por tu participación!");
  }
});
