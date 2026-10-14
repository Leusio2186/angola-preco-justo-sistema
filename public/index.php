<?php
session_start();

// Define a raiz do projeto de forma limpa
define('ROOT_PATH', dirname(__DIR__));

// Carrega configurações e base de dados
require_once ROOT_PATH . '/app/config/config.php';
require_once ROOT_PATH . '/app/config/database.php';

// Carrega o gestor de rotas
require_once ROOT_PATH . '/routes/web.php';