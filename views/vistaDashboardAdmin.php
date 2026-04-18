<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin - EcoBank</title>
    <link rel="stylesheet" href="./views/styles/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>

<!-- CABECERA -->
<?php require_once "./views/cabecera.php"; ?>

<div class="admin-container">

    <div class="admin-header">
        <h1>Panel Admin - <?php echo $_SESSION["nombre"]; ?></h1>
    </div>

    <input type="text" id="buscador" placeholder="Buscar usuario...">

    <div class="tabla-container">
        <table id="tablaUsuarios">
            <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

</div>

<script>

    let filaEditando = null;

    function cargarUsuarios(busqueda = "") {
        fetch("index.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: `ajax=1&accion=buscar&busqueda=${busqueda}`
        })
            .then(res => res.json())
            .then(data => {
                let html = "";

                data.forEach(u => {
                    html += `
            <tr onclick="verHistorial(${u.id})">
                <td>${u.id}</td>

                <td><input value="${u.nombre}" disabled onclick="event.stopPropagation()"></td>
                <td><input value="${u.apellidos}" disabled onclick="event.stopPropagation()"></td>
                <td><input value="${u.email}" disabled onclick="event.stopPropagation()"></td>
                <td><input value="${u.telefono}" disabled onclick="event.stopPropagation()"></td>

                <td class="direccion">
                    ${u.via ?? ''}, ${u.numero_piso ?? ''}<br>
                    ${u.municipio ?? ''} (${u.provincia ?? ''})
                </td>

                <td>
                    <select disabled onclick="event.stopPropagation()">
                        <option value="0" ${u.rol == 0 ? "selected" : ""}>No verificado</option>
                        <option value="1" ${u.rol == 1 ? "selected" : ""}>Usuario</option>
                        <option value="2" ${u.rol == 2 ? "selected" : ""}>Admin</option>
                    </select>
                </td>

                <td>
                    <div class="acciones" onclick="event.stopPropagation()">

                        <div class="menu-acciones">
                            <button class="btn-menu" onclick="toggleMenu(${u.id})">⋮</button>

                            <div class="dropdown" id="menu-${u.id}">
                                <div onclick="editar(${u.id})">Editar</div>
                                <div onclick="eliminar(${u.id})">Eliminar</div>
                            </div>
                        <div class="dropdown" id="menu-${u.id}">
                            <div onclick="editar(${u.id})">Editar</div>
                            <div onclick="eliminar(${u.id})">Eliminar</div>
                        </div>

                        <button class="btn-guardar"  onclick="actualizar(${u.id})" title="Guardar">
                            <i class="bi bi-floppy-fill"></i>
                        </button>

                    </div>
                </td>
            </tr>`;
                });

                document.querySelector("#tablaUsuarios tbody").innerHTML = html;
            });
    }

    function editar(id) {

        document.querySelectorAll(".dropdown").forEach(d => d.style.display = "none");

        let fila = document.querySelector(`#menu-${id}`).closest("tr");

        if (filaEditando && filaEditando !== fila) {
            bloquearFila(filaEditando);
        }

        let inputs = fila.querySelectorAll("input");
        let select = fila.querySelector("select");

        inputs.forEach(i => i.disabled = false);
        select.disabled = false;

        fila.classList.add("editando");
        filaEditando = fila;

        let btnGuardar = fila.querySelector(".btn-guardar");
        if (btnGuardar) btnGuardar.style.display = "inline-block";

        let btnMenu = fila.querySelector(".btn-menu");
        if (btnMenu) btnMenu.style.visibility = "hidden";
    }

    function bloquearFila(fila) {

        let inputs = fila.querySelectorAll("input");
        let select = fila.querySelector("select");

        inputs.forEach(i => i.disabled = true);
        select.disabled = true;

        fila.classList.remove("editando");

        let btnGuardar = fila.querySelector(".btn-guardar");
        if (btnGuardar) btnGuardar.style.display = "none";

        let btnMenu = fila.querySelector(".btn-menu");
        if (btnMenu) btnMenu.style.visibility = "visible";
    }

    function actualizar(id) {
        let fila = document.querySelector(`#menu-${id}`).closest("tr");

        fetch("index.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: `ajax=1&accion=actualizar&id=${id}
        &nombre=${fila.children[1].children[0].value}
        &apellidos=${fila.children[2].children[0].value}
        &email=${fila.children[3].children[0].value}
        &telefono=${fila.children[4].children[0].value}
        &rol=${fila.children[6].children[0].value}`
        })
            .then(() => {
                bloquearFila(fila);
                filaEditando = null;
                cargarUsuarios();
            });
    }

    function eliminar(id) {
        if (!confirm("¿Eliminar usuario?")) return;

        fetch("index.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: `ajax=1&accion=eliminar&id=${id}`
        }).then(() => cargarUsuarios());
    }

    function toggleMenu(id) {
        document.querySelectorAll(".dropdown").forEach(d => d.style.display = "none");
        let menu = document.getElementById(`menu-${id}`);
        menu.style.display = (menu.style.display === "block") ? "none" : "block";
    }

    document.addEventListener("click", () => {
        document.querySelectorAll(".dropdown").forEach(d => d.style.display = "none");
    });

    function verHistorial(id) {
        fetch("index.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: `ajax=1&accion=historial&id=${id}`
        })
            .then(res => res.json())
            .then(data => {
                let html = `
        <div class="modal">
            <h2>Historial del usuario</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Tipo</th>
                    <th>Descripción</th>
                    <th>Importe</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                </tr>
        `;

                data.forEach(t => {
                    html += `
            <tr>
                <td>${t.id}</td>
                <td>${t.tipo}</td>
                <td>${t.concepto ?? '-'}</td>
                <td class="${t.tipo === 'transferencia' ? 'rojo' : 'verde'}">
                    ${t.importe}€
                </td>
                <td>${t.estado}</td>
                <td>${t.fecha}</td>
            </tr>`;
                });

                html += `
            </table>
            <button onclick="cerrarModal()">Cerrar</button>
        </div>`;

                document.body.insertAdjacentHTML("beforeend",
                    `<div class="modal-overlay" onclick="cerrarModal()"></div>`
                );

                document.body.insertAdjacentHTML("beforeend", html);
            });
    }

    function cerrarModal() {
        document.querySelector(".modal")?.remove();
        document.querySelector(".modal-overlay")?.remove();
    }

    document.getElementById("buscador").addEventListener("input", e => {
        cargarUsuarios(e.target.value);
    });

    cargarUsuarios();
</script>

</body>
</html>