<?php
require_once __DIR__ . "/Config.php";

if (!class_exists('ConexionDB')) {

    class ConexionDB {
        private static $instancia = null;

        private function __construct() {}
        private function __clone() {}

        public static function getConexion($nombreBD) {
            if (self::$instancia === null) {
                try {
                    $dsn = SGBD . ":host=" . SERVIDOR . ";dbname=" . $nombreBD . ";charset=utf8mb4";
                    self::$instancia = new PDO($dsn, USERDB, PASSWORDDB);
                    self::$instancia->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    self::$instancia->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    die("Error de conexión: " . $e->getMessage());
                }
            }
            return self::$instancia;
        }

        public static function cerrarConexion() {
            self::$instancia = null;
        }
    }
}
?>