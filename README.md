# EcoBank

Aplicación web bancaria educativa desarrollada en PHP. Permite registrar usuarios con verificación por correo electrónico, iniciar sesión, gestionar la cuenta desde un panel de usuario y administrar el sistema desde un panel de administración.

> Proyecto con fines educativos. No debe usarse con datos ni dinero reales.

## Funcionalidades

- Registro de usuarios con verificación de cuenta por correo (PHPMailer + Gmail SMTP).
- Inicio y cierre de sesión con contraseñas cifradas (`password_hash` / `password_verify`).
- Cambio de contraseña desde el perfil.
- Panel de administración.
- Generación de documentos en PDF.
- Endpoints de API en la carpeta `controllers/api`.

## Tecnologías

- PHP
- MySQL / MariaDB
- PHPMailer (dependencias gestionadas con Composer)
- XAMPP (Apache + MySQL) para el entorno local

## Estructura del proyecto

```
banco/
├── controllers/     # Lógica de la aplicación (controlador.php, api, verificar, generarPDF.php)
├── models/          # Acceso a datos, configuración y dependencias (vendor)
├── views/           # Vistas y plantillas
├── index.php        # Punto de entrada
├── logout.php       # Cierre de sesión
├── composer.json
└── 127_0_0_1 (24).sql   # Volcado de la base de datos
```

## Instalación

1. **Clona el repositorio** dentro de la carpeta de XAMPP:

   ```bash
   cd C:\xampp\htdocs
   git clone https://github.com/JaviCastro03/Banco.git banco
   ```

2. **Inicia Apache y MySQL** desde el panel de XAMPP.

3. **Importa la base de datos**: abre `http://localhost/phpmyadmin`, crea una base de datos e importa el archivo `.sql` del proyecto.

4. **Configura la conexión** a la base de datos en `models/Config.php` con tus datos locales.

5. **Configura el correo**: copia la plantilla y rellénala con tus datos.

   ```bash
   copy models\MailConfig.example.php models\MailConfig.php
   ```

   ```php
   <?php
   return [
       'user' => 'tu_correo@gmail.com',
       'pass' => 'tu_contraseña_de_aplicacion',
   ];
   ```

   La contraseña debe ser una **contraseña de aplicación** de Google (Cuenta de Google → Seguridad → Contraseñas de aplicaciones), no la contraseña normal de tu cuenta.

6. **Instala las dependencias** si falta la carpeta `models/vendor`:

   ```bash
   composer install
   ```

7. Abre `http://localhost/banco/` en el navegador.

## Seguridad

- `models/MailConfig.php` está en `.gitignore`: las credenciales de correo no se suben al repositorio.
- Usa siempre tus propias credenciales y no publiques contraseñas reales en el código.

## Autor

**JaviCastro03** — [github.com/JaviCastro03](https://github.com/JaviCastro03)
