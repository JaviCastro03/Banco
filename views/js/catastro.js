
<!-- ================= JS CATÁSTRO ================= -->

    const provincia = document.getElementById("provincia");
    const municipio = document.getElementById("municipio");
    const via = document.getElementById("via");

    const url = "controllers/api/catastro.php";

    // PROVINCIAS
    fetch(url, {
    method: "POST",
    headers: {"Content-Type": "application/x-www-form-urlencoded"},
    body: "accion=provincias"
})
    .then(res => res.json())
    .then(data => {
    data.forEach(p => {
        provincia.innerHTML += `<option value="${p}">${p}</option>`;
    });
});

    // MUNICIPIOS
    provincia.addEventListener("change", () => {

    municipio.innerHTML = "<option value=''>Municipio</option>";
    via.innerHTML = "<option value=''>Vía</option>";

    fetch(url, {
    method: "POST",
    headers: {"Content-Type": "application/x-www-form-urlencoded"},
    body: `accion=municipios&provincia=${provincia.value}`
})
    .then(res => res.json())
    .then(data => {
    data.forEach(m => {
    municipio.innerHTML += `<option value="${m}">${m}</option>`;
});
});
});

    // VÍAS
    municipio.addEventListener("change", () => {

    via.innerHTML = "<option value=''>Vía</option>";

    fetch(url, {
    method: "POST",
    headers: {"Content-Type": "application/x-www-form-urlencoded"},
    body: `accion=vias&provincia=${provincia.value}&municipio=${municipio.value}`
})
    .then(res => res.json())
    .then(data => {
    data.forEach(v => {
    via.innerHTML += `<option value="${v}">${v}</option>`;
});
});
});
