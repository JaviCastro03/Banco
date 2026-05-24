<?php
require_once __DIR__ . "/ConexionDB.php";

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

    public function insertarUsuario($dni, $nombre, $apellidos, $email, $telefono, $password,
                                    $fecha_nacimiento, $provincia, $municipio, $via, $numero_piso) {
        try {
            $this->conexion->beginTransaction();
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios (dni, nombre, apellidos, email, telefono, password_hash,
                     fecha_nacimiento, provincia, municipio, via, numero_piso, rol)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$dni, $nombre, $apellidos, $email, $telefono, $password_hash,
                $fecha_nacimiento, $provincia, $municipio, $via, $numero_piso]);
            $usuario_id = $this->conexion->lastInsertId();
            $iban = $this->generarIBAN();
            $sqlCuenta = "INSERT INTO cuentas (usuario_id, iban, saldo, tipo, moneda, activa)
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

    public function getDatosUsuario($usuario_id) {
        $sql = "SELECT u.*, c.iban, c.saldo, c.tipo as tipo_cuenta, c.moneda, c.id as cuenta_id
                FROM usuarios u
                LEFT JOIN cuentas c ON c.usuario_id = u.id AND c.activa = 1
                WHERE u.id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarPerfil($usuario_id, $nombre, $apellidos, $email, $telefono,
                                     $provincia, $municipio, $via, $numero_piso) {
        $sql = "UPDATE usuarios SET nombre=?, apellidos=?, email=?, telefono=?,
                provincia=?, municipio=?, via=?, numero_piso=?, updated_at=NOW() WHERE id=?";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([$nombre, $apellidos, $email, $telefono,
            $provincia, $municipio, $via, $numero_piso, $usuario_id]);
    }

    public function cambiarPassword($usuario_id, $password_actual, $nueva_password) {
        $sql = "SELECT password_hash FROM usuarios WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id]);
        $hash = $stmt->fetchColumn();
        if (!password_verify($password_actual, $hash)) {
            return ['ok' => false, 'mensaje' => 'La contraseña actual es incorrecta.'];
        }
        $nuevo_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET password_hash = ? WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$nuevo_hash, $usuario_id]);
        return ['ok' => true, 'mensaje' => 'Contraseña actualizada correctamente.'];
    }

    public function getUsuarios($busqueda = "") {
        if ($busqueda != "") {
            $sql = "SELECT * FROM usuarios WHERE activo = 1 AND (nombre LIKE ? OR email LIKE ?)";
            $stmt = $this->conexion->prepare($sql);
            $like = "%$busqueda%";
            $stmt->execute([$like, $like]);
        } else {
            $sql = "SELECT * FROM usuarios WHERE activo = 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarUsuario($id, $nombre, $apellidos, $email, $telefono, $rol) {
        $sql = "UPDATE usuarios SET nombre=?, apellidos=?, email=?, telefono=?, rol=? WHERE id=?";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([$nombre, $apellidos, $email, $telefono, $rol, $id]);
    }

    public function eliminarUsuario($id) {
        $sql = "UPDATE usuarios SET activo = 0 WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([$id]);
    }

    /* ======================================================
                        TARJETAS
    ====================================================== */

    public function getTarjetaUsuario($usuario_id) {
        $sql = "SELECT t.* FROM tarjetas t
                JOIN cuentas c ON t.cuenta_id = c.id
                WHERE c.usuario_id = ? AND t.activa = 1
                LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crearTarjeta($cuenta_id, $titular, $tipo = 'debito') {
        $numero = $this->generarNumeroTarjeta();
        $cvv = str_pad(random_int(0, 999), 3, '0', STR_PAD_LEFT);
        $cvv_hash = password_hash($cvv, PASSWORD_DEFAULT);
        $fecha_cad = date('Y-m-d', strtotime('+4 years'));

        $sql = "INSERT INTO tarjetas (cuenta_id, numero_tarjeta, titular, fecha_caducidad, cvv_hash, tipo, activa)
                VALUES (?, ?, ?, ?, ?, ?, 1)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$cuenta_id, $numero, $titular, $fecha_cad, $cvv_hash, $tipo]);
        return [
            'numero_tarjeta'  => $numero,
            'titular'         => $titular,
            'fecha_caducidad' => $fecha_cad,
            'tipo'            => $tipo,
            'cvv'             => $cvv
        ];
    }

    private function generarNumeroTarjeta() {
        do {
            $numero = '4' . implode('', array_map(fn() => random_int(0, 9), range(1, 15)));
            $stmt = $this->conexion->prepare("SELECT COUNT(*) FROM tarjetas WHERE numero_tarjeta = ?");
            $stmt->execute([$numero]);
        } while ($stmt->fetchColumn() > 0);
        return $numero;
    }

    /* ======================================================
                TRANSACCIONES / HISTORIAL
    ====================================================== */

    public function getHistorialUsuario($usuario_id) {
        $sql = "SELECT
                    t.id, t.tipo, t.importe, t.estado, t.concepto, t.referencia, t.fecha,
                    CASE WHEN t.cuenta_origen_id = c.id THEN 'salida' ELSE 'entrada' END as direccion,
                    CASE
                        WHEN t.cuenta_origen_id = c.id THEN
                            CASE
                                WHEN t.tipo = 'bizum' THEN b.nombre_destino
                                WHEN t.tipo = 'transferencia' THEN tf.titular_destino
                                ELSE NULL
                            END
                        ELSE
                            CONCAT(uOrigen.nombre, ' ', uOrigen.apellidos)
                    END as destinatario,
                    CASE
                        WHEN t.tipo = 'bizum' THEN b.telefono_destino
                        WHEN t.tipo = 'transferencia' THEN tf.iban_destino
                        ELSE NULL
                    END as destino_referencia
                FROM transacciones t
                JOIN cuentas c ON (t.cuenta_origen_id = c.id OR t.cuenta_destino_id = c.id)
                JOIN cuentas cOrigen ON t.cuenta_origen_id = cOrigen.id
                JOIN usuarios uOrigen ON cOrigen.usuario_id = uOrigen.id
                LEFT JOIN bizum b ON b.transaccion_id = t.id
                LEFT JOIN transferencias tf ON tf.transaccion_id = t.id
                WHERE c.usuario_id = ?
                GROUP BY t.id
                ORDER BY t.fecha DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getResumenMes($usuario_id) {
        $sql = "SELECT
                COALESCE(SUM(CASE WHEN t.cuenta_origen_id = c.id AND t.estado='completada' THEN t.importe ELSE 0 END), 0) as enviado,
                COALESCE(SUM(CASE WHEN t.cuenta_destino_id = c.id AND t.estado='completada' THEN t.importe ELSE 0 END), 0) as recibido,
                COUNT(CASE WHEN t.cuenta_origen_id = c.id THEN 1 END) as num_enviadas
            FROM cuentas c
            LEFT JOIN transacciones t ON (t.cuenta_origen_id = c.id OR t.cuenta_destino_id = c.id)
                AND t.estado='completada'
                AND MONTH(t.fecha) = MONTH(NOW())
                AND YEAR(t.fecha) = YEAR(NOW())
            WHERE c.usuario_id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: ['enviado' => 0, 'recibido' => 0, 'num_enviadas' => 0];
    }

    public function getDesglose($usuario_id, $filtro = 'total') {
        $intervalos = [
            'dias'    => '30 DAY',
            'semanas' => '12 WEEK',
            'meses'   => '6 MONTH',
            'años'    => '5 YEAR',
        ];
        $where = isset($intervalos[$filtro])
            ? "AND t.fecha >= DATE_SUB(NOW(), INTERVAL {$intervalos[$filtro]})"
            : "";

        $sql = "SELECT
                    t.tipo,
                    CASE WHEN t.cuenta_origen_id = c.id THEN 'salida' ELSE 'entrada' END as direccion,
                    COUNT(*) as total,
                    SUM(t.importe) as volumen
                FROM transacciones t
                JOIN cuentas c ON (t.cuenta_origen_id = c.id OR t.cuenta_destino_id = c.id)
                WHERE c.usuario_id = ? AND t.estado = 'completada' $where
                GROUP BY t.tipo, direccion";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDatosGrafica($usuario_id, $filtro = 'meses') {
        switch ($filtro) {
            case 'dias':
                $formato  = '%Y-%m-%d';
                $intervalo = '30 DAY';
                $label    = 'dia';
                break;
            case 'semanas':
                $formato  = '%x-W%v';
                $intervalo = '12 WEEK';
                $label    = 'semana';
                break;
            case 'años':
                $formato  = '%Y';
                $intervalo = '5 YEAR';
                $label    = 'año';
                break;
            default:
                $formato  = '%Y-%m';
                $intervalo = '6 MONTH';
                $label    = 'mes';
                break;
        }

        $sql = "SELECT
                DATE_FORMAT(t.fecha, '$formato') as periodo,
                COALESCE(SUM(CASE WHEN t.cuenta_origen_id = c.id AND t.estado='completada' THEN t.importe ELSE 0 END), 0) as gastado,
                COALESCE(SUM(CASE WHEN t.cuenta_destino_id = c.id AND t.estado='completada' THEN t.importe ELSE 0 END), 0) as recibido
            FROM transacciones t
            JOIN cuentas c ON (t.cuenta_origen_id = c.id OR t.cuenta_destino_id = c.id)
            WHERE c.usuario_id = ?
            AND t.fecha >= DATE_SUB(NOW(), INTERVAL $intervalo)
            GROUP BY DATE_FORMAT(t.fecha, '$formato')
            ORDER BY periodo ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUltimosMovimientos($usuario_id, $limite = 5) {
        $sql = "SELECT
                    t.id, t.tipo, t.importe, t.concepto, t.fecha, t.estado,
                    CASE WHEN t.cuenta_origen_id = c.id THEN 'salida' ELSE 'entrada' END as direccion,
                    CASE
                        WHEN t.cuenta_origen_id = c.id THEN
                            CASE
                                WHEN t.tipo = 'bizum' THEN b.nombre_destino
                                WHEN t.tipo = 'transferencia' THEN tf.titular_destino
                                ELSE NULL
                            END
                        ELSE
                            CONCAT(uOrigen.nombre, ' ', uOrigen.apellidos)
                    END as destinatario
                FROM transacciones t
                JOIN cuentas c ON (t.cuenta_origen_id = c.id OR t.cuenta_destino_id = c.id)
                JOIN cuentas cOrigen ON t.cuenta_origen_id = cOrigen.id
                JOIN usuarios uOrigen ON cOrigen.usuario_id = uOrigen.id
                LEFT JOIN bizum b ON b.transaccion_id = t.id
                LEFT JOIN transferencias tf ON tf.transaccion_id = t.id
                WHERE c.usuario_id = ?
                GROUP BY t.id
                ORDER BY t.fecha DESC
                LIMIT ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $usuario_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ======================================================
                            BIZUM
    ====================================================== */
    public function getUltimosBizum($usuario_id) {
        $sql = "
        SELECT
            b.telefono_destino,
            b.nombre_destino
        FROM transacciones t
        INNER JOIN cuentas c ON t.cuenta_origen_id = c.id
        INNER JOIN bizum b ON b.transaccion_id = t.id
        INNER JOIN usuarios u ON u.telefono = b.telefono_destino
        WHERE c.usuario_id = ?
        AND t.tipo = 'bizum'
        AND u.activo = 1
        GROUP BY b.telefono_destino, b.nombre_destino
        ORDER BY MAX(t.fecha) DESC
        LIMIT 5
    ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUltimasTransferencias($usuario_id) {
        $sql = "
        SELECT DISTINCT
            tf.iban_destino,
            tf.titular_destino
        FROM transacciones t
        JOIN cuentas c ON t.cuenta_origen_id = c.id
        JOIN transferencias tf ON tf.transaccion_id = t.id
        WHERE c.usuario_id = ?
        AND t.tipo = 'transferencia'
        ORDER BY t.fecha DESC
        LIMIT 5
    ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function enviarBizum($usuario_id, $telefono_destino, $importe, $concepto = "Bizum") {
        try {
            $this->conexion->beginTransaction();
            if ($importe <= 0) throw new Exception("El importe debe ser mayor que cero.");

            $stmt = $this->conexion->prepare("SELECT id, saldo FROM cuentas WHERE usuario_id = ?");
            $stmt->execute([$usuario_id]);
            $cuentaOrigen = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$cuentaOrigen) throw new Exception("El usuario no tiene cuenta bancaria.");
            if ($cuentaOrigen['saldo'] < $importe) throw new Exception("Saldo insuficiente.");

            $stmt = $this->conexion->prepare("SELECT id, nombre, apellidos FROM usuarios WHERE telefono = ?");
            $stmt->execute([$telefono_destino]);
            $destinatario = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$destinatario) throw new Exception("El teléfono no está registrado en EcoBank.");
            if ($destinatario['id'] == $usuario_id) throw new Exception("No puedes enviarte un Bizum a ti mismo.");

            $stmt = $this->conexion->prepare("SELECT id FROM cuentas WHERE usuario_id = ?");
            $stmt->execute([$destinatario['id']]);
            $cuentaDestino = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$cuentaDestino) throw new Exception("El destinatario no tiene cuenta bancaria.");

            $this->conexion->prepare("UPDATE cuentas SET saldo = saldo - ? WHERE id = ?")->execute([$importe, $cuentaOrigen['id']]);
            $this->conexion->prepare("UPDATE cuentas SET saldo = saldo + ? WHERE id = ?")->execute([$importe, $cuentaDestino['id']]);

            $referencia = uniqid("BIZUM_");
            $stmt = $this->conexion->prepare("INSERT INTO transacciones (cuenta_origen_id, cuenta_destino_id, importe, tipo, estado, concepto, referencia, ip_origen) VALUES (?, ?, ?, 'bizum', 'completada', ?, ?, ?)");
            $stmt->execute([$cuentaOrigen['id'], $cuentaDestino['id'], $importe, $concepto, $referencia, $_SERVER['REMOTE_ADDR'] ?? null]);
            $transaccion_id = $this->conexion->lastInsertId();

            $nombreDestino = trim($destinatario['nombre']) . " " . trim($destinatario['apellidos']);
            $stmt = $this->conexion->prepare("INSERT INTO bizum (transaccion_id, telefono_destino, nombre_destino, mensaje) VALUES (?, ?, ?, ?)");
            $stmt->execute([$transaccion_id, $telefono_destino, $nombreDestino, $concepto]);

            $this->conexion->commit();
            return ["ok" => true, "mensaje" => "Bizum enviado correctamente a $nombreDestino.", "referencia" => $referencia, "transaccion_id" => $transaccion_id];
        } catch (Exception $e) {
            $this->conexion->rollBack();
            return ["ok" => false, "mensaje" => $e->getMessage()];
        }
    }

    /* ======================================================
                        TRANSFERENCIAS
    ====================================================== */

    public static function validarIBAN($iban) {
        $iban = strtoupper(str_replace(' ', '', $iban));
        if (strlen($iban) !== 24) return false;
        $reordenado = substr($iban, 4) . substr($iban, 0, 4);
        $numerico = '';
        foreach (str_split($reordenado) as $c) {
            $numerico .= ctype_alpha($c) ? (ord($c) - 55) : $c;
        }
        return bcmod($numerico, '97') === '1';
    }

    public function enviarTransferencia($usuario_id, $iban_destino, $titular_destino, $importe, $concepto = "Transferencia") {
        try {
            $this->conexion->beginTransaction();
            if ($importe <= 0) throw new Exception("El importe debe ser mayor que cero.");

            $stmt = $this->conexion->prepare("SELECT id, saldo FROM cuentas WHERE usuario_id = ?");
            $stmt->execute([$usuario_id]);
            $cuentaOrigen = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$cuentaOrigen) throw new Exception("No tienes cuenta bancaria.");
            if ($cuentaOrigen['saldo'] < $importe) throw new Exception("Saldo insuficiente.");
            if (trim($iban_destino) === '') throw new Exception("El IBAN destino no puede estar vacío.");

            $stmt = $this->conexion->prepare("SELECT id FROM cuentas WHERE iban = ?");
            $stmt->execute([$iban_destino]);
            $cuentaDestino = $stmt->fetch(PDO::FETCH_ASSOC);

            // No puedes transferirte a tu propia cuenta
            if ($cuentaDestino && $cuentaDestino['id'] == $cuentaOrigen['id']) {
                throw new Exception("No puedes realizar una transferencia a tu propia cuenta.");
            }

            $this->conexion->prepare("UPDATE cuentas SET saldo = saldo - ? WHERE id = ?")->execute([$importe, $cuentaOrigen['id']]);
            if ($cuentaDestino) {
                $this->conexion->prepare("UPDATE cuentas SET saldo = saldo + ? WHERE id = ?")->execute([$importe, $cuentaDestino['id']]);
            }

            $referencia = uniqid("TRANS_");
            $stmt = $this->conexion->prepare("INSERT INTO transacciones (cuenta_origen_id, cuenta_destino_id, importe, tipo, estado, concepto, referencia, ip_origen) VALUES (?, ?, ?, 'transferencia', 'completada', ?, ?, ?)");
            $stmt->execute([$cuentaOrigen['id'], $cuentaDestino['id'] ?? null, $importe, $concepto, $referencia, $_SERVER['REMOTE_ADDR'] ?? null]);
            $transaccion_id = $this->conexion->lastInsertId();

            $stmt = $this->conexion->prepare("INSERT INTO transferencias (transaccion_id, iban_destino, titular_destino, concepto, requiere_verificacion) VALUES (?, ?, ?, ?, 1)");
            $stmt->execute([$transaccion_id, $iban_destino, $titular_destino, $concepto]);

            $this->conexion->commit();
            return ["ok" => true, "mensaje" => "Transferencia realizada correctamente.", "referencia" => $referencia, "transaccion_id" => $transaccion_id];
        } catch (Exception $e) {
            $this->conexion->rollBack();
            return ["ok" => false, "mensaje" => $e->getMessage()];
        }
    }

    /* ======================================================
                CÓDIGOS DE VERIFICACIÓN
    ====================================================== */

    public function generarCodigoVerificacion($usuario_id) {
        $codigo = random_int(10000, 99999);
        $expiracion = date("Y-m-d H:i:s", strtotime("+10 minutes"));
        $sql = "INSERT INTO codigos_verificacion (usuario_id, codigo, expira_en, usado) VALUES (?, ?, ?, 0)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id, $codigo, $expiracion]);
        return $codigo;
    }

    public function verificarCodigo($usuario_id, $codigo) {
        $sql = "SELECT id FROM codigos_verificacion WHERE usuario_id = ? AND codigo = ? AND usado = 0 AND expira_en > NOW() ORDER BY id DESC LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id, $codigo]);
        $registro = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($registro) {
            $this->conexion->prepare("UPDATE codigos_verificacion SET usado = 1 WHERE id = ?")->execute([$registro['id']]);
            return true;
        }
        return false;
    }

    /* ======================================================
                        NOTIFICACIONES
    ====================================================== */

    public function getNotificaciones($usuario_id, $solo_no_leidas = false) {
        $sql = "SELECT * FROM notificaciones WHERE usuario_id = ?";
        if ($solo_no_leidas) $sql .= " AND leida = 0";
        $sql .= " ORDER BY created_at DESC LIMIT 20";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function marcarNotificacionesLeidas($usuario_id) {
        $stmt = $this->conexion->prepare("UPDATE notificaciones SET leida = 1 WHERE usuario_id = ?");
        return $stmt->execute([$usuario_id]);
    }

    public function contarNoLeidas($usuario_id) {
        $stmt = $this->conexion->prepare("SELECT COUNT(*) FROM notificaciones WHERE usuario_id = ? AND leida = 0");
        $stmt->execute([$usuario_id]);
        return $stmt->fetchColumn();
    }

    /* ======================================================
                        GENERAR IBAN
    ====================================================== */

    private function generarIBAN() {
        do {
            // 20 dígitos del BBAN: 8 banco/sucursal + 2 control cuenta + 10 número cuenta
            $bban = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT)
                . str_pad(random_int(0, 99), 2, '0', STR_PAD_LEFT)
                . str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT)
                . str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

            // Calcula dígitos de control: E=14, S=28, dígitos provisionales=00
            $reordenado = $bban . '142800';
            $resto = bcmod($reordenado, '97');
            $control = str_pad(98 - intval($resto), 2, '0', STR_PAD_LEFT);

            $iban = 'ES' . $control . $bban;

            $stmt = $this->conexion->prepare("SELECT COUNT(*) FROM cuentas WHERE iban = ?");
            $stmt->execute([$iban]);
        } while ($stmt->fetchColumn() > 0);

        return $iban;
    }

    /* ======================================================
                    ADMINISTRACION - RESUMEN
    ====================================================== */

    public function getResumenAdmin() {
        $total_usuarios = $this->conexion->query("SELECT COUNT(*) FROM usuarios WHERE activo = 1")->fetchColumn();
        $usuarios_verificados = $this->conexion->query("SELECT COUNT(*) FROM usuarios WHERE activo = 1 AND rol >= 1")->fetchColumn();
        $total_cuentas = $this->conexion->query("SELECT COUNT(*) FROM cuentas WHERE activa = 1")->fetchColumn();
        $total_transacciones = $this->conexion->query("SELECT COUNT(*) FROM transacciones")->fetchColumn();
        $trans_este_mes = $this->conexion->query(
            "SELECT COUNT(*) FROM transacciones WHERE MONTH(fecha) = MONTH(NOW()) AND YEAR(fecha) = YEAR(NOW())"
        )->fetchColumn();
        $stmt = $this->conexion->query(
            "SELECT nombre, apellidos, email, rol FROM usuarios WHERE activo = 1 ORDER BY id DESC LIMIT 5"
        );
        $ultimos_usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return compact('total_usuarios', 'usuarios_verificados', 'total_cuentas',
            'total_transacciones', 'trans_este_mes', 'ultimos_usuarios');
    }

    public function getCuentaUsuario($usuario_id) {
        $sql = "SELECT c.*, CONCAT(u.nombre, ' ', u.apellidos) as titular, u.email
                FROM cuentas c JOIN usuarios u ON u.id = c.usuario_id
                WHERE c.usuario_id = ? AND c.activa = 1 LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllCuentas() {
        $sql = "SELECT c.*, CONCAT(u.nombre, ' ', u.apellidos) as titular, u.email, u.id as usuario_id
                FROM cuentas c JOIN usuarios u ON u.id = c.usuario_id
                WHERE u.activo = 1 ORDER BY c.id DESC";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllMovimientos() {
        $sql = "SELECT t.id, t.tipo, t.importe, t.estado, t.concepto, t.referencia, t.fecha,
                    co.iban as cuenta_origen_iban,
                    CASE WHEN t.tipo = 'bizum' THEN b.nombre_destino
                         WHEN t.tipo = 'transferencia' THEN tf.titular_destino ELSE NULL END as destinatario
                FROM transacciones t
                LEFT JOIN cuentas co ON co.id = t.cuenta_origen_id
                LEFT JOIN bizum b ON b.transaccion_id = t.id
                LEFT JOIN transferencias tf ON tf.transaccion_id = t.id
                ORDER BY t.fecha DESC LIMIT 500";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ======================================================
                    PDF - TRANSACCIÓN
    ====================================================== */

    public function getTransaccionParaPDF($transaccion_id, $usuario_id) {
        $sql = "SELECT
                    t.id, t.tipo, t.importe, t.estado, t.concepto, t.referencia, t.fecha,
                    CONCAT(u.nombre,' ',u.apellidos) as titular_origen,
                    co.iban as iban_origen,
                    CASE WHEN t.tipo='bizum' THEN b.nombre_destino
                         WHEN t.tipo='transferencia' THEN tf.titular_destino ELSE '-' END as destinatario,
                    CASE WHEN t.tipo='bizum' THEN b.telefono_destino
                         WHEN t.tipo='transferencia' THEN tf.iban_destino ELSE '-' END as destino_ref
                FROM transacciones t
                JOIN cuentas co ON co.id = t.cuenta_origen_id
                JOIN usuarios u ON u.id = co.usuario_id
                LEFT JOIN bizum b ON b.transaccion_id = t.id
                LEFT JOIN transferencias tf ON tf.transaccion_id = t.id
                WHERE t.id = ? AND co.usuario_id = ?
                LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$transaccion_id, $usuario_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* ======================================================
                    SIMULACIONES
    ====================================================== */

    public function getSimulaciones($usuario_id) {
        $sql = "SELECT * FROM simulaciones WHERE usuario_id = ? ORDER BY created_at DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearSimulacion($usuario_id, $nombre, $tipo, $importe, $intervalo_dias) {
        // Si importe es 0 o negativo (luz, agua), asignar random entre 20 y 100
        if ($importe <= 0) {
            $importe = mt_rand(20, 100);
        }
        // La primera ejecución es inmediata (ahora) para que se pueda procesar enseguida
        $proxima = date('Y-m-d H:i:s');
        $sql = "INSERT INTO simulaciones (usuario_id, nombre, tipo, importe, intervalo_dias, activa, proxima_ejecucion)
                VALUES (?, ?, ?, ?, ?, 1, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id, $nombre, $tipo, $importe, $intervalo_dias, $proxima]);
        return $this->conexion->lastInsertId();
    }

    public function eliminarSimulacion($id, $usuario_id) {
        $stmt = $this->conexion->prepare("DELETE FROM simulaciones WHERE id = ? AND usuario_id = ?");
        return $stmt->execute([$id, $usuario_id]);
    }

    public function toggleSimulacion($id, $usuario_id) {
        $stmt = $this->conexion->prepare("UPDATE simulaciones SET activa = NOT activa WHERE id = ? AND usuario_id = ?");
        return $stmt->execute([$id, $usuario_id]);
    }

    public function procesarSimulaciones($usuario_id) {
        $stmt = $this->conexion->prepare(
            "SELECT * FROM simulaciones WHERE usuario_id = ? AND activa = 1 AND proxima_ejecucion <= NOW()"
        );
        $stmt->execute([$usuario_id]);
        $pendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $cuenta = $this->conexion->prepare("SELECT id, saldo FROM cuentas WHERE usuario_id = ? AND activa = 1");
        $cuenta->execute([$usuario_id]);
        $cuentaData = $cuenta->fetch(PDO::FETCH_ASSOC);
        if (!$cuentaData) return ['ok' => false, 'mensaje' => 'Sin cuenta bancaria.'];

        $procesadas = 0;
        foreach ($pendientes as $sim) {
            // Para luz y agua, importe random cada vez que se ejecuta
            $importe = $sim['importe'];
            $nombresRandom = ['Luz', 'Agua'];
            if (in_array($sim['nombre'], $nombresRandom)) {
                $importe = mt_rand(20, 100);
                // Actualizar el importe guardado con el nuevo random
                $this->conexion->prepare("UPDATE simulaciones SET importe = ? WHERE id = ?")
                    ->execute([$importe, $sim['id']]);
            }

            if ($sim['tipo'] === 'gasto') {
                // Verificar saldo suficiente
                if ($cuentaData['saldo'] < $importe) continue;

                $this->conexion->prepare("UPDATE cuentas SET saldo = saldo - ? WHERE id = ?")
                    ->execute([$importe, $cuentaData['id']]);
                $cuentaData['saldo'] -= $importe;

                // Transacción: origen = cuenta del usuario, destino = NULL
                $referencia = uniqid("SIM_GASTO_");
                $this->conexion->prepare(
                    "INSERT INTO transacciones (cuenta_origen_id, cuenta_destino_id, importe, tipo, estado, concepto, referencia)
                     VALUES (?, NULL, ?, 'transferencia', 'completada', ?, ?)"
                )->execute([$cuentaData['id'], $importe, "Simulación gasto: " . $sim['nombre'], $referencia]);

            } else {
                // Ingreso: dinero entra en la cuenta
                $this->conexion->prepare("UPDATE cuentas SET saldo = saldo + ? WHERE id = ?")
                    ->execute([$importe, $cuentaData['id']]);
                $cuentaData['saldo'] += $importe;

                // Transacción: origen = destino = cuenta del usuario (ingreso simulado)
                $referencia = uniqid("SIM_INGRESO_");
                $this->conexion->prepare(
                    "INSERT INTO transacciones (cuenta_origen_id, cuenta_destino_id, importe, tipo, estado, concepto, referencia)
     VALUES (NULL, ?, ?, 'transferencia', 'completada', ?, ?)"
                )->execute([$cuentaData['id'], $importe, "Simulación ingreso: " . $sim['nombre'], $referencia]);
            }

            // Notificación
            $signo = $sim['tipo'] === 'gasto' ? '-' : '+';
            $importeF = number_format($importe, 2, ',', '.');
            $this->conexion->prepare(
                "INSERT INTO notificaciones (usuario_id, tipo, contenido) VALUES (?, 'info', ?)"
            )->execute([$usuario_id, "Simulación {$sim['nombre']}: {$signo}€{$importeF}"]);

            // Calcular próxima ejecución
            $proxima = date('Y-m-d H:i:s', strtotime("+" . $sim['intervalo_dias'] . " days"));
            $this->conexion->prepare(
                "UPDATE simulaciones SET ultima_ejecucion = NOW(), proxima_ejecucion = ? WHERE id = ?"
            )->execute([$proxima, $sim['id']]);

            $procesadas++;
        }

        $saldoActual = $this->conexion->prepare("SELECT saldo FROM cuentas WHERE id = ?");
        $saldoActual->execute([$cuentaData['id']]);
        $nuevoSaldo = $saldoActual->fetchColumn();

        return ['ok' => true, 'procesadas' => $procesadas, 'nuevo_saldo' => $nuevoSaldo];
    }

    public function getStatsMovimientos() {
        $total = $this->conexion->query("SELECT COUNT(*) FROM transacciones")->fetchColumn();
        $este_mes = $this->conexion->query("SELECT COUNT(*) FROM transacciones WHERE MONTH(fecha)=MONTH(NOW()) AND YEAR(fecha)=YEAR(NOW())")->fetchColumn();
        $vol_bizum = $this->conexion->query("SELECT COALESCE(SUM(importe),0) FROM transacciones WHERE tipo='bizum' AND estado='completada'")->fetchColumn();
        $vol_trans = $this->conexion->query("SELECT COALESCE(SUM(importe),0) FROM transacciones WHERE tipo='transferencia' AND estado='completada'")->fetchColumn();
        return compact('total', 'este_mes', 'vol_bizum', 'vol_trans');
    }
}
?>