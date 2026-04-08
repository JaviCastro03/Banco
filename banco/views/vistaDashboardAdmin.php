<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin - EcoBank</title>
    <link rel="stylesheet" href="./views/styles/styles.css">
</head>
<body>

<div class="admin-container">

    <!-- HEADER -->
    <div class="admin-header">
        <h1>Panel Admin - <?php echo $_SESSION["nombre"]; ?></h1>

        <form action="logout.php" method="post">
            <button class="logout-btn">Cerrar sesión</button>
        </form>
    </div>

    <!-- BUSCADOR -->
    <input type="text" id="buscador" placeholder="Buscar usuario...">

    <!-- TABLA -->
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
            <tr>
                <td>${u.id}</td>

                <td><input value="${u.nombre}" onchange="actualizar(${u.id})"></td>
                <td><input value="${u.apellidos}" onchange="actualizar(${u.id})"></td>
                <td><input value="${u.email}" onchange="actualizar(${u.id})"></td>
                <td><input value="${u.telefono}" onchange="actualizar(${u.id})"></td>

                <td class="direccion">
                    ${u.via ?? ''}, ${u.numero_piso ?? ''}<br>
                    ${u.municipio ?? ''} (${u.provincia ?? ''})
                </td>

                <!-- 🔥 ROL -->
                <td>
                    <select onchange="actualizar(${u.id})">
                        <option value="0" ${u.rol == 0 ? "selected" : ""}>No verificado</option>
                        <option value="1" ${u.rol == 1 ? "selected" : ""}>Usuario</option>
                        <option value="2" ${u.rol == 2 ? "selected" : ""}>Admin</option>
                    </select>
                </td>

                <td>
                    <button onclick="eliminar(${u.id})">❌</button>
                </td>
            </tr>`;
                });

                document.querySelector("#tablaUsuarios tbody").innerHTML = html;
            });
    }

    function actualizar(id) {
        let fila = document.querySelector(`button[onclick="eliminar(${id})"]`).closest("tr");

        fetch("index.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: `ajax=1&accion=actualizar&id=${id}
        &nombre=${fila.children[1].children[0].value}
        &apellidos=${fila.children[2].children[0].value}
        &email=${fila.children[3].children[0].value}
        &telefono=${fila.children[4].children[0].value}
        &rol=${fila.children[6].children[0].value}`
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

    // BUSCADOR
    document.getElementById("buscador").addEventListener("input", e => {
        cargarUsuarios(e.target.value);
    });

    // CARGA INICIAL
    cargarUsuarios();

</script>

</body>
</html>