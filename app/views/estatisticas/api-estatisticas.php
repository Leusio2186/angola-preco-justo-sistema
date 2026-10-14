<?php
header('Content-Type: application/json');
$conn = new mysqli('localhost', 'root', '', 'precos_angola');

$lojas = $conn->query("SELECT COUNT(*) as t FROM stores WHERE status = 'aprovada'")->fetch_assoc()['t'] ?? 0;
$precos = $conn->query("SELECT COUNT(*) as t FROM store_products")->fetch_assoc()['t'] ?? 0;
$usuarios = $conn->query("SELECT COUNT(*) as t FROM users")->fetch_assoc()['t'] ?? 0;

echo json_encode([
    "lojas" => number_format($lojas, 0, ',', '.'),
    "precos" => number_format($precos, 0, ',', '.'),
    "usuarios" => number_format($usuarios, 0, ',', '.')
]);
?>