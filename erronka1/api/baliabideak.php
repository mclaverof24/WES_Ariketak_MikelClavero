<?php
declare(strict_types=1);
require __DIR__ . '/../includes/bootstrap.php';

crud([
    'table'  => 'baliabideak',
    'fields' => [
        'kategoriaId'  => ['type' => 'int', 'required' => true],
        'izena'        => ['type' => 'string', 'required' => true, 'max' => 150],
        'deskribapena' => ['type' => 'text'],
        'marka'        => ['type' => 'string', 'max' => 100],
        'modeloa'      => ['type' => 'string', 'max' => 100],
    ],
]);