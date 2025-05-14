document.addEventListener("DOMContentLoaded", () => {
  const monthYearDisplay = document.getElementById("month-year");
  const calendarBody = document.getElementById("calendar-body");
  const prevMonthButton = document.getElementById("prev-month");
  const nextMonthButton = document.getElementById("next-month");

  let currentDate = new Date();

  const months = [
    "Enero",
    "Febrero",
    "Marzo",
    "Abril",
    "Mayo",
    "Junio",
    "Julio",
    "Agosto",
    "Septiembre",
    "Octubre",
    "Noviembre",
    "Diciembre",
  ];

  const specialDates = [
    {
      day: 16,
      month: 8,
      year: 2024,
      label: "Día Internacional de la Preservación de la Capa de Ozono",
    },
    {
      day: 5,
      month: 6,
      year: 2024,
      label:
        "Semana global de la Evaluación 2024: Avances en el Diseño y Evaluación de la Política Climática en México",
    },
    {
      day: 11,
      month: 4,
      year: 2024,
      label:
        "Encuentro de Ciudades del Sur Sureste de México y Mesoamérica ante el Cambio Climático",
    },
    {
      day: 11,
      month: 11,
      year: 2024,
      label: "Participación de México en la COP29",
    },
    {
      day: 7,
      month: 9,
      year: 2024,
      label: "Reforestación en Texcal, Puebla",
    },
    {
      day: 26,
      month: 9,
      year: 2024,
      label: "ExpoSustenta 2024: Juventud por la Acción Climática, Veracruz",
    },
    {
      day: 21,
      month: 11,
      year: 2024,
      label: "Día de Acción Climática 2024",
    },
    {
      day: 22,
      month: 10,
      year: 2024,
      label:
        "II Congreso Internacional de Investigación en Cambio Climático y Salud, Ciuadd de Mexico",
    },
    {
      day: 14,
      month: 8,
      year: 2024,
      label: "Flextival: Promoviendo la Economía Circular, Ciudad de Mexico",
    },
    {
      day: 26,
      month: 6,
      year: 2024,
      label: "Pacto Global Red México 2024, Ciudad de Mexico",
    },
    {
      day: 25,
      month: 10,
      year: 2024,
      label:
        "Digital Summit 2024: Innovación para un Futuro Energético Verde, Ciudad de Mexico",
    },
    {
      day: 28,
      month: 9,
      year: 2024,
      label: "Octava Jornada de Reforestacion en Puebla, Sembremos esperanza",
    },
    {
      day: 2,
      month: 9,
      year: 2024,
      label:
        "Premiacion del Concurso, 'Reciclando por el futuo', Secretaria de Planecion y finanzas",
    },
    {
      day: 3,
      month: 9,
      year: 2024,
      label: "Museo Itinerante de las ODS, San Andres Cholula",
    },
    {
      day: 4,
      month: 9,
      year: 2024,
      label:
        "Domo Educativo de proyecciones, Escuela Primaria Oficial Matutina Belisario Dominguez",
    },
    {
      day: 6,
      month: 9,
      year: 2024,
      label:
        "Mercadito solidairo, explanda de secretaria de planeacion y finanzas",
    },
    {
      day: 30,
      month: 10,
      year: 2024,
      label:
        "Taller Planificacion de Areas verdes, Parque Jerusalem, Barrio de Analco, 9:00-13:00",
    },
  ];

  function generateCalendar(date) {
    const year = date.getFullYear();
    const month = date.getMonth();

    monthYearDisplay.textContent = `${months[month]} ${year}`;

    let firstDay = new Date(year, month, 1).getDay(); // Día inicial (0 = domingo)
    const lastDate = new Date(year, month + 1, 0).getDate(); // Último día del mes

    calendarBody.innerHTML = "";

    let row = document.createElement("tr");
    let dayCounter = 1;

    for (let i = 0; i < firstDay; i++) {
      const emptyCell = document.createElement("td");
      row.appendChild(emptyCell);
    }

    for (let i = firstDay; i < 7; i++) {
      const cell = createDayCell(dayCounter, month, year);
      row.appendChild(cell);
      dayCounter++;
    }
    calendarBody.appendChild(row);

    while (dayCounter <= lastDate) {
      row = document.createElement("tr");
      for (let i = 0; i < 7 && dayCounter <= lastDate; i++) {
        const cell = createDayCell(dayCounter, month, year);
        row.appendChild(cell);
        dayCounter++;
      }
      calendarBody.appendChild(row);
    }
  }

  function createDayCell(day, month, year) {
    const cell = document.createElement("td");
    cell.classList.add("day");

    const specialDate = specialDates.find(
      (special) =>
        special.day === day && special.month === month && special.year === year
    );

    if (specialDate) {
      cell.classList.add("special-day");
      cell.innerHTML = `<div>${day}</div><small>${specialDate.label}</small>`;
    } else {
      cell.textContent = day;
    }

    return cell;
  }

  prevMonthButton.addEventListener("click", () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    generateCalendar(currentDate);
  });

  nextMonthButton.addEventListener("click", () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    generateCalendar(currentDate);
  });

  generateCalendar(currentDate);
});
