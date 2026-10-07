<?php
declare(strict_types=1);
require __DIR__ . '/../includes/bootstrap.php';

crud([
    'table'  => 'kategoriak',
    'fields' => [
        'izena' => ['type' => 'string', 'required' => true, 'max' => 100],
    ],
]);