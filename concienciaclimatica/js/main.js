document.addEventListener("DOMContentLoaded", () => {
  const modal = document.getElementById("legalModal");
  const openModal = document.getElementById("openModal");
  const closeModal = modal.querySelector(".close");

  openModal.addEventListener("click", (event) => {
    event.preventDefault();
    modal.style.display = "block";
  });

  closeModal.addEventListener("click", () => {
    modal.style.display = "none";
  });

  window.addEventListener("click", (event) => {
    if (event.target === modal) {
      modal.style.display = "none";
    }
  });
});

const scrollBtn = document.createElement('button');
scrollBtn.innerHTML = '↑';
scrollBtn.classList.add('scroll-to-top');
document.body.appendChild(scrollBtn);

scrollBtn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

window.addEventListener('scroll', () => {
    scrollBtn.style.display = window.scrollY > 300 ? 'block' : 'none';
});
