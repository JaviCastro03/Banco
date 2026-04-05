<?php
require_once "Config.php";
/**
@author PABLO RUIZ
 */
//Clase que usa el patrón de diseño Singleton
//Garantiza que solo va a existir una única conexión a la BBDD
class ConexionDB{
    private static $instancia;

    private function __construct(){

    }

    private function __clone(){

    }

    public static function getConexion($nombreBD){
        if(self::$instancia==null){
            try {
                $option=SGBD.":host=".SERVIDOR.";dbname=".$nombreBD;
                self::$instancia = new PDO($option, USERDB, PASSWORDDB);
            }catch(PDOException $e){
                self::$instancia=null;
            }
        }
        return self::$instancia;
    }

    public static function cerrarConexion(){
        self::$instancia=null;

    }
}

