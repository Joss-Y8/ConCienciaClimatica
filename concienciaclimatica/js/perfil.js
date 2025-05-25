<script>
document.addEventListener("DOMContentLoaded", function () {
    fetch("../backend/api/PERFIL/perfil.php") // Ajusta la ruta si cambia
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                const perfil = data.perfil;
                document.getElementById("nombre").value = perfil.nombre;
                document.getElementById("correo").value = perfil.correo;
                document.getElementById("ubi").value = perfil.zona;
                document.getElementById("nombre-usuario").textContent = perfil.nombre;
            } else {
                alert("No se pudo cargar el perfil: " + data.message);
                window.location.href = "./hpage_login.html"; // redirige si no está logueado
            }
        })
        .catch(error => {
            console.error("Error al obtener perfil:", error);
            alert("Error al cargar el perfil");
        })
});
</script>
