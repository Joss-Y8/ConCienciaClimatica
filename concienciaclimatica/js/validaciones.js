/*
//Login
document.getElementById("formLogin").addEventListener("submit", function(event) {
    if (!validarLogin()) {
        event.preventDefault();
    }
});

function validarLogin() {
    const correo = document.querySelector('#formLogin input[name="correo"]').value.trim();
    const password = document.querySelector('#formLogin input[name="password"]').value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!correo || !emailRegex.test(correo)) {
        document.getElementById("login-message").innerText = "Por favor ingresa un correo válido.";
        return false;
    }

    if (!password || password.length < 6) {
        document.getElementById("login-message").innerText = "La contraseña debe tener al menos 6 caracteres.";
        return false;
    }

    document.getElementById("login-message").innerText = "";
    return true;
}

//Registro
document.getElementById("formSignup").addEventListener("submit", function(event) {
    if (!validarRegistro()) {
        event.preventDefault();
    }
});

function validarRegistro() {
    const nombre = document.querySelector('#formSignup input[name="nombre"]').value.trim();
    const apellido = document.querySelector('#formSignup input[name="apellido"]').value.trim();
    const correo = document.querySelector('#formSignup input[name="correo"]').value.trim();
    const password = document.querySelector('#formSignup input[name="password"]').value.trim();
    const passwordc = document.querySelector('#formSignup input[name="passwordc"]').value.trim();
    const edad = parseInt(document.querySelector('#formSignup input[name="edad"]').value.trim());
    const municipio = document.querySelector('#formSignup select[name="city"]').value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!nombre || /\d/.test(nombre)) {
        mostrarError("Nombre inválido.");
        return false;
    }

    if (!apellido || /\d/.test(apellido)) {
        mostrarError("Apellido inválido.");
        return false;
    }

    if (!correo || !emailRegex.test(correo)) {
        mostrarError("Correo electrónico inválido.");
        return false;
    }

    if (!password || password.length < 6) {
        mostrarError("La contraseña debe tener al menos 6 caracteres.");
        return false;
    }

    if (password !== passwordc) {
        mostrarError("Las contraseñas no coinciden.");
        return false;
    }

    if (isNaN(edad) || edad < 10 || edad > 120) {
        mostrarError("Edad inválida.");
        return false;
    }

    if (!municipio || municipio === "Selecciona tu municipio") {
        mostrarError("Selecciona tu municipio.");
        return false;
    }

    document.getElementById("signup-message").innerText = "";
    return true;

    function mostrarError(mensaje) {
        document.getElementById("signup-message").innerText = mensaje;
    }
}


//Huella de carbono

document.getElementById("formHuella").addEventListener("submit", function(event) {
  if (!validarHuellaCarbono()) {
    event.preventDefault(); // Evita el envío si hay errores
  }
});

function validarHuellaCarbono() {
  // IDs de las preguntas del 1 al 10
  const preguntas = ["q1", "q2", "q3", "q4", "q5", "q6", "q7", "q8", "q9", "q10"];

  for (let id of preguntas) {
    const select = document.getElementById(id);
    if (!select || !select.value) {
      alert(`Por favor, responde la pregunta ${id.toUpperCase().replace("Q", "")}.`);
      return false;
    }
  }

  return true;
}




//Empresas

document.getElementById("formEncuesta").addEventListener("submit", function(event) {
  // Evita que el formulario se envíe si hay errores
  if (!validarFormularioEncuesta()) {
    event.preventDefault();
  }
});

function validarFormularioEncuesta() {
  // Lista de campos select
  const selects = ["q1", "q2", "q5", "q6", "q8", "q9"];
  for (let id of selects) {
    const select = document.getElementById(id);
    if (!select || !select.value) {
      alert(`Por favor, selecciona una opción en la pregunta ${id.toUpperCase()}.`);
      return false;
    }
  }

  // Lista de campos checkbox por grupo
  const checkboxGrupos = {
    "q3": "pregunta 3",
    "q4": "pregunta 4",
    "q7": "pregunta 7",
    "q10": "pregunta 10"
  };

  for (let grupo in checkboxGrupos) {
    const checkboxes = document.querySelectorAll(`input[name="${grupo}[]"]`);
    const algunoSeleccionado = Array.from(checkboxes).some(cb => cb.checked);
    if (!algunoSeleccionado) {
      alert(`Selecciona al menos una opción en ${checkboxGrupos[grupo]}.`);
      return false;
    }
  }

  return true; // Todo correcto
}
*/

document.addEventListener("DOMContentLoaded", function () {
  // LOGIN
  const formLogin = document.getElementById("formLogin");
  if (formLogin) {
    formLogin.addEventListener("submit", function (event) {
      if (!validarLogin()) event.preventDefault();
    });
  }

  // SIGNUP
  const formSignup = document.getElementById("formSignup");
  if (formSignup) {
    formSignup.addEventListener("submit", function (event) {
      if (!validarRegistro()) event.preventDefault();
    });
  }

  // ENCUESTA (formEncuesta)
  const formEncuesta = document.getElementById("formEncuesta");
  if (formEncuesta) {
    formEncuesta.addEventListener("submit", function (event) {
      if (!validarFormularioEncuesta()) event.preventDefault();
    });
  }

  // HUELLA DE CARBONO
  const formHuella = document.getElementById("formHuella");
  if (formHuella) {
    formHuella.addEventListener("submit", function (event) {
      if (!validarHuellaCarbono()) event.preventDefault();
    });
  }
});

// VALIDACIÓN LOGIN
function validarLogin() {
  const correo = document.querySelector('#formLogin input[name="correo"]').value.trim();
  const password = document.querySelector('#formLogin input[name="password"]').value.trim();
  const emailRegex = /^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/;

  if (!correo || !emailRegex.test(correo)) {
    document.getElementById("login-message").innerText = "Por favor ingresa un correo válido.";
    return false;
  }

  if (!password || password.length < 6) {
    document.getElementById("login-message").innerText = "La contraseña debe tener al menos 6 caracteres.";
    return false;
  }

  document.getElementById("login-message").innerText = "";
  return true;
}

// VALIDACIÓN REGISTRO
function validarRegistro() {
  const nombre = document.querySelector('#formSignup input[name="nombre"]').value.trim();
  const apellido = document.querySelector('#formSignup input[name="apellido"]').value.trim();
  const correo = document.querySelector('#formSignup input[name="correo"]').value.trim();
  const password = document.querySelector('#formSignup input[name="password"]').value.trim();
  const passwordc = document.querySelector('#formSignup input[name="passwordc"]').value.trim();
  const edad = parseInt(document.querySelector('#formSignup input[name="edad"]').value.trim());
  const municipio = document.querySelector('#formSignup select[name="city"]').value;
  const emailRegex = /^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/;

  if (!nombre || /\\d/.test(nombre)) return mostrarError("Nombre inválido.");
  if (!apellido || /\\d/.test(apellido)) return mostrarError("Apellido inválido.");
  if (!correo || !emailRegex.test(correo)) return mostrarError("Correo electrónico inválido.");
  if (!password || password.length < 6) return mostrarError("La contraseña debe tener al menos 6 caracteres.");
  if (password !== passwordc) return mostrarError("Las contraseñas no coinciden.");
  if (isNaN(edad) || edad < 10 || edad > 120) return mostrarError("Edad inválida.");
  if (!municipio || municipio === "Selecciona tu municipio") return mostrarError("Selecciona tu municipio.");

  document.getElementById("signup-message").innerText = "";
  return true;

  function mostrarError(mensaje) {
    document.getElementById("signup-message").innerText = mensaje;
    return false;
  }
}

// === Función Global: HUELLA DE CARBONO ===
function validarHuellaCarbono() {
  const preguntas = ["q1", "q2", "q3", "q4", "q5", "q6", "q7", "q8", "q9", "q10"];
  for (let id of preguntas) {
    const select = document.getElementById(id);
    if (!select || !select.value) {
      alert(`Por favor, responde la pregunta ${id.toUpperCase().replace("Q", "")}.`);
      return false;
    }
  }
  return true;
}

// === Función Global: FORMULARIO ENCUESTA ===
function validarFormularioEncuesta() {
  const selects = ["q1", "q2", "q5", "q6", "q8", "q9"];
  for (let id of selects) {
    const select = document.getElementById(id);
    if (!select || !select.value) {
      alert(`Por favor, selecciona una opción en la pregunta ${id.toUpperCase()}.`);
      return false;
    }
  }

  const checkboxGrupos = {
    "q3": "pregunta 3",
    "q4": "pregunta 4",
    "q7": "pregunta 7",
    "q10": "pregunta 10"
  };

  for (let grupo in checkboxGrupos) {
    const checkboxes = document.querySelectorAll(`input[name="${grupo}[]"]`);
    const algunoSeleccionado = Array.from(checkboxes).some(cb => cb.checked);
    if (!algunoSeleccionado) {
      alert(`Selecciona al menos una opción en ${checkboxGrupos[grupo]}.`);
      return false;
    }
  }

  return true;
}

// === DOMContentLoaded: Enlazar formularios ===
document.addEventListener("DOMContentLoaded", function () {
  const formHuella = document.getElementById("formHuella");
  if (formHuella) {
    formHuella.addEventListener("submit", function (event) {
      if (!validarHuellaCarbono()) {
        event.preventDefault();
      }
    });
  }

  const formEncuesta = document.getElementById("formEncuesta");
  if (formEncuesta) {
    formEncuesta.addEventListener("submit", function (event) {
      if (!validarFormularioEncuesta()) {
        event.preventDefault();
      }
    });
  }
});
