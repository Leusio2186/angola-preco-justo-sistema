<?php
session_start();

// Passo 1: Apagar todas as variáveis da sessão
session_unset();

// Passo 2: Apagar o COOKIE de sessão do browser
// (session_destroy() sozinho não faz isto!)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),   // nome do cookie (ex: PHPSESSID)
        '',               // valor vazio
        time() - 42000,  // data no passado = apaga o cookie
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Passo 3: Destruir a sessão no servidor
session_destroy();

// Passo 4: Redirecionar para o login
// Como este ficheiro está na mesma pasta que login.view.php,
// o caminho é simples e nunca falha!
header("Location: login.view.php");
exit();
?>
