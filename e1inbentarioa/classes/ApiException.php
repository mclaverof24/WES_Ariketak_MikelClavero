<?php
declare(strict_types=1);

/**
 * Error de la API: lleva el código HTTP y, si procede, el detalle por campo.
 * Lo lanzan las clases (Kategoria, Gailua...) y lo convierte en JSON el manejador de includes/bootstrap.php.
 */
class ApiException extends Exception
{
    public function __construct(string $message, int $status = 400, public readonly array $details = [])
    {
        parent::__construct($message, $status);
    }
}
