<?php
declare(strict_types=1);

// Ajusta estos valores a tu entorno (XAMPP: root sin contraseña por defecto)
const DB_HOST = 'localhost';
const DB_NAME = 'e1inbentarioa';
const DB_USER = 'mclaverof24';
const DB_PASS = 'root';

// Nombre del aula donde se ubican por defecto los equipos nuevos
const GELA_ALMAZENA = 'Almacén';

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
    }
    return $pdo;
}