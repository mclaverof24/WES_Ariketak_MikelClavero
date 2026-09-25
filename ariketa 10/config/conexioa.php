<?php
/**
 * Datu-basera konektatzeko klasea (Singleton eredua).
 * Datos de conexión: ajusta $host, $db, $user, $pass según tu MariaDB local.
 */
class Conexioa {
    private static $pdo = null;

    public static function lortu() {
        if (self::$pdo === null) {
            $host = 'localhost';
            $db   = 'hackaton';
            $user = 'mclaverof24';
            $pass = 'root';
            $dsn  = "mysql:host=$host;dbname=$db;charset=utf8";

            try {
                self::$pdo = new PDO($dsn, $user, $pass);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Errorea datu-basera konektatzean: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}