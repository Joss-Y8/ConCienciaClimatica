document.addEventListener("DOMContentLoaded", () => {
  // Menú desplegable responsive
  const toggle = document.getElementById("menu-toggle");
  const nav = document.getElementById("nav");

  if (toggle && nav) {
    toggle.addEventListener("click", () => {
      nav.classList.toggle("open");
    })
  }

  // Controlador de pestañas
  const tabLinks = document.querySelectorAll(".tab-link");
  tabLinks.forEach(btn => {
    btn.addEventListener("click", (event) => {
      const tabName = btn.getAttribute("data-tab"); // usamos atributo personalizado
      openTab(event, tabName);
    })
  })
});

function openTab(evt, tabName) {
  const tabContent = document.querySelectorAll(".tab-content");
  const tabLinks = document.querySelectorAll(".tab-link");

  tabContent.forEach(content => content.classList.remove("active"));
  tabLinks.forEach(link => link.classList.remove("active"));

  const selectedTab = document.getElementById(tabName);
  if (selectedTab) selectedTab.classList.add("active");
  if (evt.currentTarget) evt.currentTarget.classList.add("active");
}