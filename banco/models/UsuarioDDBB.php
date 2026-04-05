
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

    function insertarUsuario($dni, $nombre, $apellidos, $email, $telefono, $password, $fecha_nacimiento = null){

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql="INSERT INTO usuarios 
        (dni, nombre, apellidos, email, telefono, password_hash, fecha_nacimiento, rol) 
        VALUES 
        (:dni, :nombre, :apellidos, :email, :telefono, :password_hash, :fecha_nacimiento, 0)";

        $consulta=$this->conexion->prepare($sql);

        $consulta->bindParam(':dni',$dni);
        $consulta->bindParam(':nombre',$nombre);
        $consulta->bindParam(':apellidos',$apellidos);
        $consulta->bindParam(':email',$email);
        $consulta->bindParam(':telefono',$telefono);
        $consulta->bindParam(':password_hash',$password_hash);
        $consulta->bindParam(':fecha_nacimiento',$fecha_nacimiento);

        return $consulta->execute();
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
}
