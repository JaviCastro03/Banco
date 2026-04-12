<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<header class="cabecera">
    <div class="cabecera-contenedor">
        <div class="cabecera-izquierda">
            <img src="views/styles/logo.png" alt="EcoBank" class="logo-cabecera">
            <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION["nombre"] ?? "Usuario"); ?></h2>
        </div>

        <div class="cabecera-derecha">
            <form action="logout.php" method="post">
                <button type="submit" class="btn-cerrar">Cerrar sesión</button>
            </form>
        </div>
    </div>
</header>