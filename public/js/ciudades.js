document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById("ciudadInput");
    const hiddenInput = document.getElementById("ciudad_nombre");
    const sugerencias = document.getElementById("sugerencias");

    input.addEventListener("input", function () {
        let q = input.value;
        if (q.length < 2) {
            sugerencias.innerHTML = "";
            return;
        }

        fetch(`/ciudades/buscar?q=${q}`)
            .then(res => res.json())
            .then(data => {
                sugerencias.innerHTML = "";
                data.forEach(ciudad => {
                    let li = document.createElement("li");
                    li.textContent = ciudad.nombre;
                    li.addEventListener("click", () => {
                        input.value = ciudad.nombre;
                        hiddenInput.value = ciudad.nombre;
                        sugerencias.innerHTML = "";
                    });
                    sugerencias.appendChild(li);
                });
            });
    });

    // Si el usuario escribe algo nuevo que no selecciona de la lista
    input.addEventListener("blur", function () {
        hiddenInput.value = input.value;
    });
});
