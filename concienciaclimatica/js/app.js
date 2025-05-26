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

          //se insertan dinámicamente las insignias 
          const grid = document.querySelector(".grid-insignias");
          grid.innerHTML = ""; // Limpiar antes de agregar

          if (data.insignias_ganadas && data.insignias_ganadas.length > 0) {
            data.insignias_ganadas.forEach(ins => {
              const item = document.createElement("div");
              item.className = "insignia";
              item.innerHTML = `
                <img src="${ins.imagen}" alt="${ins.nombre}">
                <p>${ins.nombre}</p>
              `;
              grid.appendChild(item);
            });
          }else{
              grid.innerHTML = "<h2 class='sin-insignias'>Aún no has ganado insignias. Realiza nuestras actividades y forma parte de la comunidad ¡Suerte!</h2>";
          }

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

  //modifique la lógica porque no me dejaba utilizar mis funciones jaja :(, si ya no funciona me dices para que lo cambie. Atte: Joss
    const formEncuesta = document.querySelector("#formEncuesta");
    if (formEncuesta) {
      formEncuesta.addEventListener("submit", function(e) {
        e.preventDefault();

        const formData = new FormData(formEncuesta);

        function getCheckboxValues(name) {
          const checkboxes = formEncuesta.querySelectorAll(`input[name="${name}[]"]:checked`);
          const values = [];
          checkboxes.forEach(checkbox => values.push(checkbox.value));
          return values;
        }

        const data = {
          vw_iniciativa: formData.get("q1") === "si" ? 1 : 0,
          audi_iniciativa: formData.get("q2") === "si" ? 1 : 0,
          vw_conocidas: getCheckboxValues("q3"),
          audi_conocidas: getCheckboxValues("q4"),
          info_vw: parseInt(formData.get("q5")),
          info_audi: parseInt(formData.get("q6")),
          medios: getCheckboxValues("q7"),
          suficiencia: formData.get("q8"),
          relevancia: formData.get("q9"),
          mejoras: getCheckboxValues("q10")
        };

        fetch(`${API_URL}/encuesta`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(respuesta => {
          alert(respuesta.message);
          console.log("Respuesta del servidor:", respuesta);
        })
        .catch(error => {
          console.error("Error al enviar la encuesta:", error);
          alert("Error al enviar la encuesta.");
        });
      });
    }


  //cargamos actividades
  function cargarActividades() {
    fetch(`${API_URL}/actividades`)
      .then(res => res.json())
      .then(data => {
        console.log("Actividades recibidas: ", data);
        const contenedor = document.querySelector(".misiones");
        contenedor.innerHTML = "";

        data.forEach(act => {
          console.log(`Actividad ID ${act.id}:`, act);
          const tarjeta = document.createElement("div");
          tarjeta.className = "tarjeta-mision";
          tarjeta.setAttribute("data-meta", act.meta || 0);

          // Estado simulado
          let estadoBoton = "Iniciar Actividad";
          if (act.completada) {
            estadoBoton = "Completado";
          } else if (act.meta_alcanzada) {
            estadoBoton = "Siguiente Nivel";
          } else if (act.iniciada) {
            estadoBoton = "Actualizar Progreso";
          }

          const inputHabilitado = act.iniciada && !act.completada ? "": "disabled"; 
          const descripcionNivel = act.meta && act.unidad ? `Nivel actual: debes completar ${act.meta} ${act.unidad}` : ''; 
          tarjeta.innerHTML = `
            <h2>${act.nombre}</h2>
            <img src="${act.imagen}" alt="${act.nombre}" class="img-actividad">
            <p>${act.descripcion}</p>
            <p class="descripcion-nivel">${descripcionNivel}</p>
            <div class="barra-container">
              <div class="barra-progreso">
                <div class="progreso"></div>
                <div class="circle active">1</div>
                <div class="circle">2</div>
                <div class="circle">3</div>
              </div>
            </div>
            <div class="progreso-interaccion">
              <input type = "number" min = "1" placeholder="Ingresa tu progreso" class = "input-progreso" data-id="${act.id}" ${inputHabilitado}/>
              <button class="btn progreso-btn" data-id="${act.id}">${estadoBoton}</button>
            </div>
            <span class="insignia oculto">Insignia desbloqueada</span>
          `;
          
          console.log("Datos para la barra: ", {id: act.id, nivel: act.nivel_actual, progreso: act.progreso_actual, meta: act.meta })
          //progreso visual
          pintarBarra(tarjeta, act.nivel_actual || 0, act.progreso_actual || 0, act.meta || 1);


          contenedor.appendChild(tarjeta);
        });

        // Eventos para los botones
        $(".progreso-btn").on("click", function () {
          const idActividad = $(this).data("id");
          const estado = $(this).text().trim();
          console.log(`Botón clickeado - Actividad ID: ${idActividad}, Estado: ${estado}`);
          const boton = $(this);

          if (estado === "Iniciar Actividad") {
            $.ajax({
              url: `${API_URL}/actividad/iniciar`,
              type: "POST",
              data: JSON.stringify({ id_actividad: idActividad }),
              contentType: "application/json",
              success: function (res) {
                if (res.status === "success") {
                  showToast("Actividad iniciada");
                  boton.text("Actualizar Progreso");
                  cargarActividades();
                } else {
                  showToast(res.message);
                }
              },
              error: function () {
                showToast("Error al iniciar actividad");
              }
            });

          } else if (estado === "Actualizar Progreso") {
            const tarjeta = $(this).closest(".tarjeta-mision")[0];
            const input = tarjeta.querySelector(`.input-progreso[data-id='${idActividad}']`);
            const cantidad =parseInt(input.value); 
            const boton = $(this); 
            console.log("Cantidad leída del input:", cantidad);
            if (!cantidad || isNaN(cantidad)) {
              showToast("Ingresa un número válido.");
              return;
            }
            input.value = "";

            $.ajax({
              url: `${API_URL}/actividad/progreso`,
              type: "PUT",
              data: JSON.stringify({ id_actividad: idActividad, cantidad: cantidad }),
              contentType: "application/json",
              success: function (res) {
                if (res.status === "success") {
                  showToast(res.message);

                  // Actualizar barra visual todavía no se actualiza al instante. 
                  const barra = tarjeta.querySelector(".progreso");
                  const meta = parseInt(tarjeta.getAttribute("data-meta"));
                  const progreso = res.progreso_actual || cantidad;
                  barra.setAttribute("data-actual", progreso); // valor de atributo

                  const porcentaje = Math.min(100, (progreso / meta) * 100);
                  barra.style.width = `${porcentaje}%`;
                  cargarActividades();


                  // Círculos de nivel
                  const nivelActual = res.nivel_actual || 1; 
                  const circles = tarjeta.querySelectorAll(".circle");
                  circles.forEach((circle, i) => {
                    if (i <= nivelActual) {
                      circle.classList.add("active");
                    } else {
                      circle.classList.remove("active");
                    }
                  });

                  if (res.estado === "meta_alcanzada") {
                    boton.text("Siguiente Nivel");
                  }

                } else {
                  showToast(res.message, true);
                }
              },
              error: function () {
                showToast("Error al actualizar progreso", true);
              }
            });

          } else if (estado === "Siguiente Nivel") {
            $.ajax({
              url: `${API_URL}/actividad/nivel`,
              type: "PUT",
              data: JSON.stringify({ id_actividad: idActividad }),
              contentType: "application/json",
              success: function (res) {
                if (res.status === "success") {
                  showToast(res.message);
                  if (res.estado === "completado") {
                    boton.text("Completado");
                    cargarActividades();
                    boton.prop("disabled", true);
                  } else {
                    boton.text("Actualizar Progreso");
                    cargarActividades();

                  }
                } else {
                  showToast(res.message);
                }
              },
              error: function () {
                showToast("Error al subir de nivel");
              }
            });
          }
        });
      })
      .catch(err => console.error("Error al cargar actividades:", err));
  }

  if (window.location.pathname.includes("actividades.html")) {
    cargarActividades();
  }
        
    $('#form-propuesta').on('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        $.ajax({
            url: API_URL + "/propuesta",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhrFields: { withCredentials: true },
            success: function (response) {
                try {
                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                    console.log("Respuesta del servidor:", data);

                    if (data.success) {
                        alert("Exito " + data.message);

                        // Agregar visualmente la tarjeta
                        const nombre = formData.get("nombre");
                        const descripcion = formData.get("descripcion");
                        const imagenFile = $('#form-propuesta input[name="imagen"]')[0].files[0];
                        const imagenURL = URL.createObjectURL(imagenFile); // vista temporal

                        const nuevaTarjeta = `
                            <div class="tarjeta-mision">
                                <h2> ${nombre}</h2>
                                <img src="${imagenURL}" alt="${nombre}" style="width:100%; border-radius: 8px; margin-bottom: 8px;">
                                <p>${descripcion}</p>
                                <button class="btn">Unirme</button>
                            </div>
                        `;

                        $('.misiones').prepend(nuevaTarjeta);
                        $('#form-propuesta')[0].reset();
                    } else {
                        alert("Error: " + (data.message || "Algo salió mal"));
                    }
                } catch (e) {
                    console.error("Error al interpretar respuesta:", e);
                    alert("Respuesta inesperada del servidor");
                }
            },
            error: function (xhr, status, error) {
                console.error("Error AJAX:", error);
                alert("Error en la petición");
            }
        });
    });  


  // 1. Obtener ID del usuario actual
    if (window.location.pathname.includes("propuestas.html")) {
    $.get(API_URL + "/perfil", function (perfil) {
        usuarioActualId = perfil.id;
        cargarPropuestas(); 
    });
  }

  let usuarioActualId = null;

  function cargarPropuestas() {
      $.get(API_URL + "/propuestas", function (propuestas) {
          console.log("Respuesta de /propuestas: ", propuestas);
          $('.misiones').empty();

          propuestas.forEach(function (p) {
              const esCreador = parseInt(p.id_usuario) === parseInt(usuarioActualId);
              const yaUnido = parseInt(p.unido) === 1;
              const completado = parseInt(p.completado) === 1;

              console.log("Propuesta:", p);
              console.log("id_usuario:", p.id_usuario, "usuarioActualId:", usuarioActualId, "¿Es creador?", esCreador);
              console.log("¿Ya unido?", yaUnido, "¿Completado?", completado);

              let boton = "";

              if (esCreador) {
                  boton = `<button class="btn" disabled>Propia</button>`;
              } else if (completado) {
                  boton = `<button class="btn" disabled>Completada</button>`;
              } else if (yaUnido) {
                  boton = `<button class="btn completar-btn" data-id="${p.id}">Completar actividad</button>`;
              } else {
                  boton = `<button class="btn unirme-btn"
                              data-id="${p.id}" 
                              data-nombre="${p.nombre}" 
                              data-descripcion="${p.descripcion}" 
                              data-imagen="${p.imagen}" 
                              data-niveles="${p.niveles}">
                              Unirme
                          </button>`;
              }

              const tarjeta = `
                  <div class="tarjeta-mision">
                      <h2>🌿 ${p.nombre}</h2>
                      <img src="../${p.imagen}" alt="${p.nombre}" style="width:100%; border-radius: 8px; margin-bottom: 8px;">
                      <p>${p.descripcion}</p>
                      ${boton}
                  </div>
              `;

              $('.misiones').append(tarjeta);
          });
      });
  }


  // Cuando el usuario hace clic en "Unirme"
  $(document).on('click', '.unirme-btn', function () {
      const idPropuesta = $(this).data('id');

      $.ajax({
          url: API_URL + "/propuesta/unirse",
          type: "POST",
          contentType: "application/json",
          data: JSON.stringify({ id_propuesta: idPropuesta }),
          success: function (res) {
            console.log("Respuesta al unirse:", res);
            if (res.success) {
              alert("Ahora puedes completar esta propuesta");
              $(`button[data-id='${idPropuesta}']`)
                .text("Completar actividad")
                .removeClass("unirme-btn")
                .addClass("completar-btn");
            } else {
              alert("Error: " + (res.message || "Algo salió mal"));
            }    
          },
          error: function () {
              alert("Error al unirse");
          }
      });
  });


  // Cuando hace clic en "Completar actividad"
  /*$(document).on('click', '.completar-btn', function () {
      const idPropuesta = $(this).data('id');

      $.ajax({
          url: API_URL + "/propuesta/completar",
          type: "PUT",
          contentType: "application/json",
          data: JSON.stringify({ id_propuesta: idPropuesta }),
          success: function (res) {
            console.log("Respuesta al completra: ", res);
              if (res.success) {
                  alert("¡Actividad completada!");
                  $(this).prop("disabled", true).text("Completada");
              } else {
                  alert(res.message);
              }
          },
          error: function () {
              alert("Error al completar");
          }
      });
  });*/

  // Cuando hace clic en "Completar actividad"
  $(document).on('click', '.completar-btn', function () {
      const idPropuesta = $(this).data('id');

      $.ajax({
          url: API_URL + "/propuesta/completar",
          type: "PUT",
          contentType: "application/json",
          data: JSON.stringify({ id_propuesta: idPropuesta }),
          success: function (res) {
              console.log("Respuesta al completar:", res);

              if (res.success) {
                  alert("¡Actividad completada!");
                  // Desactivar el botón actual
                  $(`button[data-id='${idPropuesta}']`)
                    .prop("disabled", true)
                    .text("Completada");
              } else {
                  alert("Error: " + res.message);
              }
          },
          error: function (xhr) {
              console.error("Error al completar:", xhr.responseText);
              alert("Error al completar actividad");
          }
      });
  });

  //Huella de Carbono
  $('#formHuella').submit(function (e) {
    e.preventDefault();

    const $form = $(this);
    const $btn = $form.find('button[type="submit"]');
    const btnOriginalText = $btn.text();
    $btn.prop('disabled', true).text('Calculando...');

    const respuestas = [];

    $("select[name^='respuesta_']").each(function (index) {
      const selectedOption = $(this).find(":selected");
      respuestas.push({
        numero_pregunta: index + 1,
        puntaje: parseInt(selectedOption.attr('puntaje')) || 0
      });
    });

    console.log("Respuestas a enviar:", respuestas); // ← Verifica antes de enviar

    $.ajax({
      url: `${API_URL}/huella`,
      type: "POST",
      data: JSON.stringify({ respuestas: respuestas }),
      contentType: "application/json",
      success: function (response) {
        console.log("Respuesta del servidor:", response); // ← Verifica después de recibir respuesta

        if (response.status === "success") {
          mostrarResultadoPersonal(response.resultado);
        } else {
          alert("Error: " + response.message);
        }

        $btn.prop('disabled', false).text(btnOriginalText);
      },
      error: function (xhr, status, error) {
        console.error("Error en la solicitud:", status, error); // ← Captura errores de red
        alert("Ocurrió un error al enviar la encuesta.");
        $btn.prop('disabled', false).text(btnOriginalText);
      }
    });
  });

      function mostrarResultadoPersonal(resultado) {
        console.log("Resultado recibido para mostrar:", resultado); // ← Verifica datos antes de mostrar

        $('#formulario').hide();

        $('#huella-categoria').text(resultado.categoria)
          .css('color', resultado.color);

        $('#huella-valor').text(resultado.puntaje_total)
          .css('color', resultado.color);

        $('#huella-mensaje').html(`
          <p>${resultado.mensaje_positivo}</p>
        `);

        $('#resultado-huella').fadeIn(0, function () {
          $(this).addClass('mostrar');
        });

        $('html, body').animate({
          scrollTop: $('#resultado-huella').offset().top
        }, 500);
      }

      //Cambio de contraseña
    $("#form-pass").submit(function (e) {
        e.preventDefault();

        const actual = $("#pass").val();
        const nueva = $("#nwpass").val();
        const confirmar = $("#conpass").val();

        const $mensaje = $("#pass-message");
        $mensaje.text("").removeClass("success error");

        $.ajax({
            url: `${API_URL}/cambiar-password`,
            type: "PUT",
            contentType: "application/json",
            data: JSON.stringify({ actual, nueva, confirmar }),
            success: function (res) {
                $mensaje.text(res.message);

                if (res.success) {
                    $mensaje.addClass("success");
                    $("#form-pass")[0].reset();
                } else {
                    $mensaje.addClass("error");
                }
            },
            error: function () {
                $mensaje.text("Error al cambiar la contraseña.").addClass("error");
            }
        });
    });

    //mensajes de seguridad de contraseña 
    $("#nwpass").on("input", function () {
      const password = $(this).val();
      const strengthMsg = $("#password-strength-msg");

      let strength = 0;
      if (password.length >= 8) strength++;
      if (/[A-Z]/.test(password)) strength++;
      if (/[0-9]/.test(password)) strength++;
      if (/[\W_]/.test(password)) strength++;

      if (!password) {
        strengthMsg.text("").removeClass("weak medium strong");
      } else if (strength <= 1) {
        strengthMsg.text("Contraseña débil").removeClass("medium strong").addClass("weak");
      } else if (strength === 2 || strength === 3) {
        strengthMsg.text("Contraseña medianamente segura").removeClass("weak strong").addClass("medium");
      } else {
        strengthMsg.text("Contraseña segura").removeClass("weak medium").addClass("strong");
      }
      
    });

    //cargar eventos en el mapa
    if (window.location.pathname.includes("mapa.html")) {
        const map = L.map("map", {
        maxBounds: [
          [17.7, -99.3],  // Latitud mínima, Longitud mínima
          [20.4, -96.5]   // Latitud máxima, Longitud máxima
        ],
        maxBoundsViscosity: 1.0
      }).setView([19.0413, -98.2062], 8);

        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
          attribution:
            '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        }).addTo(map);

        // Llamada AJAX para cargar eventos
        $.get(`${API_URL}/eventos`, function (eventos) {
          eventos.forEach(function (evt) {
            const popupContent = `
              <strong>${evt.nombre}</strong><br>
              ${evt.descripcion}<br>
              <em>${evt.ubicacion}</em><br>
              <small>${evt.fecha}</small>
            `;
            L.marker([evt.latitud, evt.longitud])
              .addTo(map)
              .bindPopup(popupContent);
          });
        }).fail(function () {
          console.error("No se pudieron cargar los eventos desde el servidor.");
        });
    }

});
    

function pintarBarra(tarjeta, nivel, progreso, meta) {
  const barra = tarjeta.querySelector(".progreso");
  const circles = tarjeta.querySelectorAll(".circle");

  // Activar círculos según el nivel actual
  circles.forEach((circle, i) => {
    if (i < nivel) {
      circle.classList.add("active");
    } else {
      circle.classList.remove("active");
    }
  });

  // Calcular el porcentaje de progreso
  const porcentaje = meta && progreso !== undefined ? Math.min(100, (progreso / meta) * 100) : 0;
  barra.style.width = `${porcentaje}%`;
}

function showToast(message, isError = false) {
  const toast = document.getElementById("toast");
  toast.textContent = message;
  toast.className = "toast" + (isError ? " error" : "") + " show";

  setTimeout(() => {
    toast.classList.remove("show");
  }, 3000);
}


