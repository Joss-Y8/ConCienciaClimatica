// Animar noticias al desplazarse
const observer = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("appear");
      }
    });
  },
  {
    threshold: 0.5,
  }
);

document.querySelectorAll(".news-item").forEach((item) => {
  observer.observe(item);
});

function openModal(imageSrc) {
  const modal = document.getElementById("zoomModal");
  const zoomedImage = document.getElementById("zoomedImage");
  zoomedImage.src = imageSrc;
  modal.style.display = "flex";
}

// Función para cerrar el modal
function closeModal() {
  const modal = document.getElementById("zoomModal");
  modal.style.display = "none"; // Ocultar el modal
}

// Cerrar el modal al hacer clic fuera de la imagen
document.getElementById("zoomModal").addEventListener("click", (e) => {
  if (e.target === document.getElementById("zoomModal")) {
    closeModal();
  }
});
