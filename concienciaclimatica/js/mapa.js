// mapScript.js

document.addEventListener("DOMContentLoaded", () => {
  // 1. Inicializar el mapa en Puebla
  const map = L.map("map").setView([19.0413, -98.2062], 13);

  // 2. Cargar los tiles de OpenStreetMap
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution:
      '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
  }).addTo(map);

  // 3. Definir eventos (puedes reemplazar con datos reales o dinamizarlos)
  const eventos = [
    {
      titulo: "Charla: Adaptación al Cambio Climático",
      latlng: [19.0430, -98.2070],
      descripcion:
        "Ponente: Dra. Martínez. Lugar: Centro Cultural Puebla. Fecha: 10 Jun 2025",
    },
    {
      titulo: "Taller de Energías Renovables",
      latlng: [19.0385, -98.2105],
      descripcion:
        "Organiza: Universidad de las Américas Puebla. Fecha: 15 Jun 2025",
    },
    {
      titulo: "Caminata Ecológica",
      latlng: [19.0455, -98.2000],
      descripcion:
        "Punto de encuentro: Parque Juárez. Fecha: 20 Jun 2025",
    },
    
   {
    titulo: "Foro de Movilidad Sustentable",
    latlng: [19.0510, -98.2230],
    descripcion: "Plaza de la Victoria • 2025-06-25"
  },
  {
    titulo: "Expo Tecnologías Limpias",
    latlng: [19.0377, -98.2090],
    descripcion: "Centro Expositor • 2025-06-30"
  },
  {
    titulo: "Plantatón Comunitaria",
    latlng: [19.0335, -98.2130],
    descripcion: "Barrio de Xanenetla • 2025-07-05"
  }
  ];

  // 4. Añadir un marcador para cada evento
  eventos.forEach((evt) => {
    L.marker(evt.latlng)
      .addTo(map)
      .bindPopup(`<strong>${evt.titulo}</strong><br>${evt.descripcion}`);
  });
});
