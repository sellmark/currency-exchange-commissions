<?php

$bin = $_GET['bin'] ?? '';
$alpha2 = 'PL';

if ('41417360' === $bin) {
    $alpha2 = 'AU';
}

if ('4745030' === $bin) {
    $alpha2 = 'GB';
}

$response = [
    'country' => [
        'alpha2' => $alpha2,
    ],
];

echo json_encode($response);
