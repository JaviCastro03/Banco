
<?php
class UsuarioDDBB {

    private $conexion;

    function __construct(){
        $this->conexion = ConexionDB::getConexion('banca_online');
    }

    function emailExiste($email){
        $sql="SELECT id FROM usuarios WHERE email = :email";
        $consulta=$this->conexion->prepare($sql);
        $consulta->bindParam(':email',$email);
        $consulta->execute();

        return $consulta->rowCount() > 0;
    }

    static function comprobarPassword($password, $cpassword){
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
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios 
    (dni, nombre, apellidos, email, telefono, password_hash, fecha_nacimiento, provincia, municipio, via, numero_piso,rol)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,0)";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
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
    }
    function dniExiste($dni){
        $sql = "SELECT id FROM usuarios WHERE dni = :dni";
        $consulta = $this->conexion->prepare($sql);
        $consulta->bindParam(':dni', $dni);
        $consulta->execute();

        return $consulta->rowCount() > 0;
    }
    function getPasswordHash($email){
        $sql="SELECT password_hash FROM usuarios WHERE email = :email";
        $consulta=$this->conexion->prepare($sql);
        $consulta->bindParam(':email',$email);
        $consulta->execute();

        if($fila=$consulta->fetch(PDO::FETCH_ASSOC)){
            return $fila['password_hash'];
        }
        return null;
    }

    function getIdUsuario($email){
        $sql="SELECT id FROM usuarios WHERE email = :email";
        $consulta=$this->conexion->prepare($sql);
        $consulta->bindParam(':email',$email);
        $consulta->execute();

        if($fila=$consulta->fetch(PDO::FETCH_ASSOC)){
            return $fila['id'];
        }
        return null;
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
        $sql = "UPDATE usuarios SET nombre=?, apellidos=?, email=?, telefono=?, rol=? WHERE id=?";
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
                t.fecha,
                u.nombre,
                u.apellidos
            FROM transacciones t
            JOIN cuentas c ON t.cuenta_origen_id = c.id
            JOIN usuarios u ON c.usuario_id = u.id
            WHERE u.id = ?
            ORDER BY t.fecha DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
