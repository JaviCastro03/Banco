<?php
require_once "ConexionDB.php";

class UsuarioDDBB {

    private $conexion;

    public function __construct(){
        $this->conexion = ConexionDB::getConexion('banca_online');
    }

    /* ======================================================
                            USUARIOS
    ====================================================== */

    public function emailExiste($email){
        $sql = "SELECT id FROM usuarios WHERE email = :email";
        $consulta = $this->conexion->prepare($sql);
        $consulta->bindParam(':email', $email);
        $consulta->execute();
        return $consulta->rowCount() > 0;
    }

    public static function comprobarPassword($password, $cpassword){
        return $password === $cpassword;
    }

    public function insertarUsuario(
        $dni,
        $nombre,
        $apellidos,
        $email,
        $telefono,
        $password,
        $fecha_nacimiento,
        $provincia,
        $municipio,
        $via,
        $numero_piso
    ) {
        try {
            $this->conexion->beginTransaction();

            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuarios 
                (dni, nombre, apellidos, email, telefono, password_hash,
                 fecha_nacimiento, provincia, municipio, via, numero_piso, rol)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                $dni,
                $nombre,
                $apellidos,
                $email,
                $telefono,
                $password_hash,
                $fecha_nacimiento,
                $provincia,
                $municipio,
                $via,
                $numero_piso
            ]);

            $usuario_id = $this->conexion->lastInsertId();
            $iban = $this->generarIBAN();

            $sqlCuenta = "INSERT INTO cuentas 
                (usuario_id, iban, saldo, tipo, moneda, activa)
                VALUES (?, ?, 0.00, 'corriente', 'EUR', 1)";

            $stmtCuenta = $this->conexion->prepare($sqlCuenta);
            $stmtCuenta->execute([$usuario_id, $iban]);

            $this->conexion->commit();
            return true;

        } catch (Exception $e) {
            $this->conexion->rollBack();
            return false;
        }
    }

    public function dniExiste($dni){
        $sql = "SELECT id FROM usuarios WHERE dni = :dni";
        $consulta = $this->conexion->prepare($sql);
        $consulta->bindParam(':dni', $dni);
        $consulta->execute();
        return $consulta->rowCount() > 0;
    }

    public function getPasswordHash($email){
        $sql = "SELECT password_hash FROM usuarios WHERE email = :email";
        $consulta = $this->conexion->prepare($sql);
        $consulta->bindParam(':email', $email);
        $consulta->execute();
        $fila = $consulta->fetch(PDO::FETCH_ASSOC);
        return $fila['password_hash'] ?? null;
    }

    public function getIdUsuario($email){
        $sql = "SELECT id FROM usuarios WHERE email = :email";
        $consulta = $this->conexion->prepare($sql);
        $consulta->bindParam(':email', $email);
        $consulta->execute();
        $fila = $consulta->fetch(PDO::FETCH_ASSOC);
        return $fila['id'] ?? null;
    }

    public function getNombreUsuario($email){
        $sql = "SELECT nombre FROM usuarios WHERE email = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetchColumn();
    }

    public function getRol($email){
        $sql = "SELECT rol FROM usuarios WHERE email = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetchColumn();
    }

    public function setRol($email, $rol){
        $sql = "UPDATE usuarios SET rol = ? WHERE email = ?";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([$rol, $email]);
    }

    public function getUsuarios($busqueda = "") {
        if ($busqueda != "") {
            $sql = "SELECT * FROM usuarios WHERE nombre LIKE ? OR email LIKE ?";
            $stmt = $this->conexion->prepare($sql);
            $like = "%$busqueda%";
            $stmt->execute([$like, $like]);
        } else {
            $sql = "SELECT * FROM usuarios";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarUsuario($id, $nombre, $apellidos, $email, $telefono, $rol) {
        $sql = "UPDATE usuarios 
                SET nombre=?, apellidos=?, email=?, telefono=?, rol=? 
                WHERE id=?";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([$nombre, $apellidos, $email, $telefono, $rol, $id]);
    }

    public function eliminarUsuario($id) {
        $sql = "DELETE FROM usuarios WHERE id=?";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function getHistorialUsuario($usuario_id) {
        $sql = "SELECT 
                    t.id,
                    t.tipo,
                    t.importe,
                    t.estado,
                    t.concepto,
                    t.fecha
                FROM transacciones t
                JOIN cuentas c ON t.cuenta_origen_id = c.id
                WHERE c.usuario_id = ?
                ORDER BY t.fecha DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ======================================================
                            BIZUM
    ====================================================== */

    public function enviarBizum($usuario_id, $telefono_destino, $importe, $concepto = "Bizum") {
        try {
            $this->conexion->beginTransaction();

            if ($importe <= 0) {
                throw new Exception("El importe debe ser mayor que cero.");
            }

            $sql = "SELECT id, saldo FROM cuentas WHERE usuario_id = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$usuario_id]);
            $cuentaOrigen = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$cuentaOrigen) {
                throw new Exception("El usuario no tiene cuenta bancaria.");
            }

            if ($cuentaOrigen['saldo'] < $importe) {
                throw new Exception("Saldo insuficiente.");
            }

            $sql = "SELECT id, nombre, apellidos FROM usuarios WHERE telefono = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$telefono_destino]);
            $destinatario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$destinatario) {
                throw new Exception("El teléfono no está registrado.");
            }

            if ($destinatario['id'] == $usuario_id) {
                throw new Exception("No puedes enviarte un Bizum a ti mismo.");
            }

            $sql = "SELECT id FROM cuentas WHERE usuario_id = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$destinatario['id']]);
            $cuentaDestino = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$cuentaDestino) {
                throw new Exception("El destinatario no tiene cuenta bancaria.");
            }

            $stmt = $this->conexion->prepare(
                "UPDATE cuentas SET saldo = saldo - ? WHERE id = ?"
            );
            $stmt->execute([$importe, $cuentaOrigen['id']]);

            $stmt = $this->conexion->prepare(
                "UPDATE cuentas SET saldo = saldo + ? WHERE id = ?"
            );
            $stmt->execute([$importe, $cuentaDestino['id']]);

            $referencia = uniqid("BIZUM_");

            $stmt = $this->conexion->prepare(
                "INSERT INTO transacciones
                (cuenta_origen_id, cuenta_destino_id, importe, tipo, estado, concepto, referencia, ip_origen)
                VALUES (?, ?, ?, 'bizum', 'completada', ?, ?, ?)"
            );

            $stmt->execute([
                $cuentaOrigen['id'],
                $cuentaDestino['id'],
                $importe,
                $concepto,
                $referencia,
                $_SERVER['REMOTE_ADDR'] ?? null
            ]);

            $transaccion_id = $this->conexion->lastInsertId();
            $nombreDestino = $destinatario['nombre'] . " " . $destinatario['apellidos'];

            $stmt = $this->conexion->prepare(
                "INSERT INTO bizum
                (transaccion_id, telefono_destino, nombre_destino, mensaje)
                VALUES (?, ?, ?, ?)"
            );

            $stmt->execute([
                $transaccion_id,
                $telefono_destino,
                $nombreDestino,
                $concepto
            ]);

            $this->conexion->commit();

            return [
                "ok" => true,
                "mensaje" => "Bizum enviado correctamente."
            ];

        } catch (Exception $e) {
            $this->conexion->rollBack();
            return [
                "ok" => false,
                "mensaje" => $e->getMessage()
            ];
        }
    }

    /* ======================================================
                CÓDIGOS DE VERIFICACIÓN
    ====================================================== */

    public function generarCodigoVerificacion($usuario_id)
    {
        $codigo = random_int(10000, 99999);
        $expiracion = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        $sql = "INSERT INTO codigos_verificacion
                (usuario_id, codigo, expira_en, usado)
                VALUES (?, ?, ?, 0)";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id, $codigo, $expiracion]);

        return $codigo;
    }

    public function verificarCodigo($usuario_id, $codigo)
    {
        $sql = "SELECT id FROM codigos_verificacion
                WHERE usuario_id = ?
                AND codigo = ?
                AND usado = 0
                AND expira_en > NOW()
                ORDER BY id DESC
                LIMIT 1";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id, $codigo]);
        $registro = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($registro) {
            $update = "UPDATE codigos_verificacion SET usado = 1 WHERE id = ?";
            $stmt = $this->conexion->prepare($update);
            $stmt->execute([$registro['id']]);
            return true;
        }

        return false;
    }

    /* ======================================================
                        GENERAR IBAN
    ====================================================== */

    private function generarIBAN() {
        do {
            $iban = 'ES' .
                random_int(10, 99) .
                str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT) .
                str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT) .
                str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT) .
                str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT) .
                str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

            $sql = "SELECT COUNT(*) FROM cuentas WHERE iban = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$iban]);

        } while ($stmt->fetchColumn() > 0);

        return $iban;
    }
}
?>