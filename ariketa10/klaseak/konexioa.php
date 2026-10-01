<?php
/**
 * Clase que gestiona la conexión con la base de datos (PDO).
 */
class Konexioa
{
    private static ?PDO $pdo = null;

    private const HOST = 'localhost';
    private const DB   = 'hackaton';
    private const USER = 'mclaverof24';
    private const PASS = 'root';

    public static function lortu(): PDO
    {
        if (self::$pdo === null) {
            $dsn = 'mysql:host=' . self::HOST . ';dbname=' . self::DB . ';charset=utf8mb4';
            self::$pdo = new PDO($dsn, self::USER, self::PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }
        return self::$pdo;
    }
}