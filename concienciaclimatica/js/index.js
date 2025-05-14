/// Selecciona el contenedor del carrusel y las imágenes
const carousel = document.querySelector(".noticias");
const images = carousel.querySelectorAll("a");
let currentIndex = 0;

// Función para mover el carrusel
function moveCarousel() {
  // Esconde todas las imágenes
  images.forEach((img, index) => {
    img.style.display = "none";
  });

  // Muestra la imagen actual
  images[currentIndex].style.display = "block";

  // Avanza al siguiente índice
  currentIndex = (currentIndex + 1) % images.length;
}

setInterval(moveCarousel, 3000); 

moveCarousel();



