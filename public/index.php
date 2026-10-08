<?php
// Inicia a sessão do utilizador (necessário para manter o utilizador logado)
session_start();

// Carrega as configurações principais e a base de dados
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';

// Carrega o sistema de rotas que vai decidir qual ecrã mostrar
require_once __DIR__ . '/../routes/web.php';