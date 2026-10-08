<?php
// Captura a página que o utilizador quer aceder através do link. Se não existir, vai para a 'home'
$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 'home';

// Sistema simples de rotas para decidir qual ficheiro carregar
switch ($pagina) {
    case 'home':
        require_once __DIR__ . '/../app/views/home/index.php';
        break;
    case 'login':
        require_once __DIR__ . '/../app/views/auth/login.php';
        break;
    case 'registo':
        require_once __DIR__ . '/../app/views/auth/registo.php';
        break;
    default:
        echo "<h1 style='text-align: center; margin-top: 50px;'>Erro 404: Página não encontrada!</h1>";
        break;
}