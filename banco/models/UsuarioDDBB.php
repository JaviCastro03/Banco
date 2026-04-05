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

    function insertarUsuario($dni, $nombre, $apellidos, $email, $telefono, $password, $direccion, $fecha_nacimiento, $foto_perfil = null){

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql="INSERT INTO usuarios 
        (dni, nombre, apellidos, email, telefono, password_hash, direccion, fecha_nacimiento, foto_perfil, activo, created_at, updated_at) 
        VALUES 
        (:dni, :nombre, :apellidos, :email, :telefono, :password_hash, :direccion, :fecha_nacimiento, :foto_perfil, 1, NOW(), NOW())";

        $consulta=$this->conexion->prepare($sql);

        $consulta->bindParam(':dni',$dni);
        $consulta->bindParam(':nombre',$nombre);
        $consulta->bindParam(':apellidos',$apellidos);
        $consulta->bindParam(':email',$email);
        $consulta->bindParam(':telefono',$telefono);
        $consulta->bindParam(':password_hash',$password_hash);
        $consulta->bindParam(':direccion',$direccion);
        $consulta->bindParam(':fecha_nacimiento',$fecha_nacimiento);
        $consulta->bindParam(':foto_perfil',$foto_perfil);

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

    function verificarLogin($email, $password){
        $hash = $this->getPasswordHash($email);

        if($hash && password_verify($password, $hash)){
            return true;
        }
        return false;
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

    function setActivo($email, $activo){
        $sql="UPDATE usuarios SET activo = :activo, updated_at = NOW() WHERE email = :email";
        $consulta=$this->conexion->prepare($sql);
        $consulta->bindParam(':email',$email);
        $consulta->bindParam(':activo',$activo);

        return $consulta->execute();
    }
}