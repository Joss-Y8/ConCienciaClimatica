$(document).ready(function () {

  const API_URL = "/ConCienciaClimatica/concienciaclimatica/backend";

  // --- LOGIN ---
  $("#formLogin").submit(function (e) {
    e.preventDefault();

    const data = {
      correo: $("#formLogin input[name='correo']").val(),
      password: $("#formLogin input[name='password']").val()
    };

    $.ajax({
      url: `${API_URL}/login`,
      type: "POST",
      data: JSON.stringify(data),
      contentType: "application/json",
      success: function (response) {
        console.log("RESPUESTA:", response);
        if (response.status === "success") {
          window.location.href = "../html/hpage_login.html";
        } else {
          $("#login-message").html("ERROR " + response.message);
        }
      },
      error: function () {
        $("#login-message").html("ERROR. No se pudo conectar con el servidor.");
      }
    });
  });

  // --- SIGNUP ---
  $("#formSignup").submit(function (e) {
    e.preventDefault();

    const pass = $("#formSignup input[name='password']").val();
    const passc = $("#formSignup input[name='passwordc']").val();
    if (pass !== passc) {
      $("#signup-message").html("No hay coincidencia entre contraseñas.");
      return;
    }

    const data = {
      nombre: $("#formSignup input[name='nombre']").val(),
      apellido: $("#formSignup input[name='apellido']").val(),
      correo: $("#formSignup input[name='correo']").val(),
      password: pass,
      edad: $("#formSignup input[name='edad']").val(),
      zona: $("#formSignup select[name='city']").val()
    };

    $.ajax({
      url: `${API_URL}/signup`,
      type: "POST",
      data: JSON.stringify(data),
      contentType: "application/json",
      success: function (response) {
        if (response.status === "success") {
          $("#signup-message").html("Registro exitoso. Inicia sesión.");
          $("#formSignup")[0].reset();
        } else {
          $("#signup-message").html("ERROR " + response.message);
        }
      },
      error: function () {
        $("#signup-message").html("Error al registrar.");
      }
    });
  });

  // --- CARGAR PERFIL ---
  function cargarPerfil() {
    fetch(`${API_URL}/perfil`)
      .then(res => res.json())
      .then(data => {
        console.log("Datos recibidos del perfil:", data);
        if (data.status !== "error") {
          
          $("h2").first().text(`${data.nombre} ${data.apellido}`);

          //Aqui se pone la infromación personal 
          $("#nombre").val(`${data.nombre} ${data.apellido}`);
          $("#correo").val(data.correo);
          $("#edad").val(data.edad); 
          $("#ubi").val(data.zona || "");

          //esto es para las estadisticas 
          $(".estadisticas .valor").eq(0).text(data.insignias || "0");
          $(".estadisticas .valor").eq(1).text(data.propuestas || "0");

          $("#form-info input").prop("disabled", true); //los campos de información están deshabilitados
          $("#editarPerfilBtn").text("Modificar");
          editando = false;
        } else {
          console.warn("Error cargando perfil", data.message);
        }
      })
      .catch(err => console.error("Error al conectar con el servidor:", err));
  }

  let editando = false; 

  // --- ACTUALIZAR PERFIL ---
  $("#editarPerfilBtn").on("click", function () {
    if (!editando) {
      // Cambiar a modo edición
      $("#form-info input").prop("disabled", false);
      $(this).text("Guardar cambios");
      editando = true;
    } else {
      // Guardar cambios
      const data = {
        nombre: $("#form-info input[name='nombre']").val().split(" ")[0],
        apellido: $("#form-info input[name='nombre']").val().split(" ").slice(1).join(" "),
        correo: $("#form-info input[name='correo']").val(),
        zona: $("#form-info input[name='ubicacion']").val(),
        edad: $("#form-info input[name='edad']").val()
      };

      $.ajax({
        url: `${API_URL}/perfil`,
        type: "PUT",
        data: JSON.stringify(data),
        contentType: "application/json",
        success: function (response) {
          if (response.status === "success") {
            alert("Datos actualizados correctamente");

            cargarPerfil();

            // Volver a modo solo lectura
            $("#form-info input").prop("disabled", true);
            $("#editarPerfilBtn").text("Modificar");
            editando = false;
          } else {
            alert("Error: " + response.message);
          }
        },
        error: function () {
          alert("Error al actualizar los datos del perfil");
        }
      });
    }
  });
  // --- LOGOUT ---
  $("#btnLogout").on("click", function () {
    fetch(`${API_URL}/logout`, { method: "POST" })
      .then(res => res.json())
      .then(data => {
        if (data.status === "success") {
          alert=(data.message); 
          window.location.href = "../index.html";
        } else {
          alert("Cuidado " + data.message);
        }
      });
  });

  // --- Cargar perfil si estamos en perfil.html ---
  if (window.location.pathname.includes("perfil.html")) {
    cargarPerfil();
  }
});
