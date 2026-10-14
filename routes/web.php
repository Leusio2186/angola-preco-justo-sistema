<?php
// routes/web.php

// Captura a página que o utilizador quer aceder através do link. Se não existir, vai para 'home'
$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 'home';

switch ($pagina) {
    // Público
    case 'home':
        require_once ROOT_PATH . '/app/views/home/index_views.php';
        break;
    case 'pesquisa':
        require_once ROOT_PATH . '/app/views/produtos/pesquisa_views.php';
        break;
    case 'produto_detalhes':
        require_once ROOT_PATH . '/app/views/produtos/detalhes_views.php';
        break;
    case 'mapa':
        require_once ROOT_PATH . '/app/views/lojas/mapa_geral_views.php';
        break;
    case 'mercado':
        require_once ROOT_PATH . '/app/views/lojas/mercados_views.php';
        break;
    case 'loja_detalhes':
        require_once ROOT_PATH . '/app/views/lojas/detalhes_loja_views.php';
        break;
    case 'estatisticas':
        require_once ROOT_PATH . '/app/views/estatisticas/estatisticas_views.php';
        break;

    // Processes
    case 'login_process':
        require_once ROOT_PATH . '/app/processes/registro_process.php';
        break;
    case 'registro_process':
        require_once ROOT_PATH . '/app/processes/registro_process.php';
        break;
    case 'recuperar_password_process':
        require_once ROOT_PATH . '/app/processes/registro_process.php';
        break;

    // Auth
    case 'login':
        require_once ROOT_PATH . '/app/views/auth/login_views.php';
        break;
    case 'registo':
        require_once ROOT_PATH . '/app/views/auth/registro_views.php';
        break;
    case 'recuperar_password':
        require_once ROOT_PATH . '/app/views/auth/recuperar_password_views.php';
        break;

    // Utilizador
    case 'contribuir':
        require_once ROOT_PATH . '/app/views/usuario/contribuir_views.php';
        break;
    case 'perfil':
        require_once ROOT_PATH . '/app/views/usuario/perfil_views.php';
        break;
    case 'historico':
        require_once ROOT_PATH . '/app/views/usuario/historico_contribuicoes_views.php';
        break;

    // Admin
    case 'admin_dashboard':
        require_once ROOT_PATH . '/app/views/admin/dashboard_views.php';
        break;
    case 'admin_lojas':
        require_once ROOT_PATH . '/app/views/admin/aprovar_lojas_views.php';
        break;

    // Institucionais
    case 'sobre':
        require_once ROOT_PATH . '/app/views/informacoes/sobre_views.php';
        break;
    case 'contacto':
        require_once ROOT_PATH . '/app/views/informacoes/contacto_views.php';
        break;
    case 'privacidade':
        require_once ROOT_PATH . '/app/views/informacoes/privacidade_views.php';
        break;
    case 'funcionamento':
        require_once ROOT_PATH . '/app/views/informacoes/funcionamento_views.php';
        break;

    default:
        http_response_code(404);
        echo "<h1 style='text-align: center; margin-top: 50px;'>Erro 404: Página não encontrada!</h1>";
        break;
}