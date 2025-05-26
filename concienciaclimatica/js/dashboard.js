// Función genérica para cargar un gráfico con jQuery AJAX
function cargarGrafico(idCanvas, pregunta, titulo, tipo = 'bar') {
  $.ajax({
    url: `http://localhost/ConCienciaClimatica/concienciaclimatica/backend/encuesta/${pregunta}`,
    method: 'GET',
    dataType: 'json',
    success: function (data) {
      if (!Array.isArray(data)) {
        console.error(`Error en la respuesta de ${pregunta}:`, data);
        return;
      }

      const etiquetas = data.map(d => d.respuesta);
      const valores = data.map(d => parseInt(d.total));

      new Chart(document.getElementById(idCanvas), {
        type: tipo,
        data: {
          labels: etiquetas,
          datasets: [{
            label: titulo,
            data: valores
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false
        }
      });
    },
    error: function (xhr, status, error) {
      console.error(`Error al cargar ${pregunta}:`, status, error);
    }
  });
}

// === Funciones para cada vista con tipos manuales ===

function cargarGraficosVista1() {
  cargarGrafico("chart1", "p1", "¿Conoces VW?", 'bar');
  cargarGrafico("chart2", "p2", "¿Conoces Audi?", 'pie');
  cargarGrafico("chart3", "p3", "Iniciativas reconocidas", 'doughnut');
}

function cargarGraficosVista2() {
  cargarGrafico("chart4", "p4", "Contribución ambiental VW", 'bar');
  cargarGrafico("chart5", "p5", "Contribución ambiental Audi", 'polarArea');
  cargarGrafico("chart6", "p6", "Medios informativos", 'bar');
  cargarGrafico("chart7", "p7", "Suficiencia de info", 'pie');
}

function cargarGraficosVista3() {
  cargarGrafico("chart8", "p8", "Relevancia ambiental", 'bar');
  cargarGrafico("chart9", "p9", "Mejoras sugeridas", 'doughnut');
  cargarGrafico("chart10", "p10", "¿Participarías?", 'line');
}

// === Mostrar vistas y cargar sus gráficos ===

function mostrarVista(id) {
  $(".vista").hide();
  $("#" + id).show();

  if (id === 'vista1') cargarGraficosVista1();
  if (id === 'vista2') cargarGraficosVista2();
  if (id === 'vista3') cargarGraficosVista3();
}

// === Mostrar vista 1 al cargar ===

$(document).ready(function () {
  mostrarVista('vista1');
});
