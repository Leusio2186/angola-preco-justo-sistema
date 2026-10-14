<!DOCTYPE html>
<html lang="pt-AO">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Iniciar Sessão</title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/assets/css/login_&_registro.css">
</head>
<body>

    <header>
        <div class="caixa_top"> 
            <a href="<?php echo APP_URL; ?>/public/index.php?pagina=home" style="white: black; text-decoration: none; font-size: 14px;">&larr; Voltar ao Início</a>
        </div>
    </header>

    <main id="container">
        <div class="container_principal">
            <div class="container_top">
                <img src="..//public/assets/images/sem fundo1.png" alt="logotipo">
                <h2 style="text-align: center; margin-bottom: 5px;">Recuperar Palavra-passe</h2>
                <p>Recupere a sua palavra-passe hoje sem nenhuma complicação.</p>
            </div>

            <form action="<?php echo APP_URL; ?>/public/index.php?pagina=recuperar_password_process" method="POST" id="container_form">
                <div class="input_1" style="margin-bottom: 15px;">
                    <div class="caixa_1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user preview-icon"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <input type="email" id="gmail" name="gmail" placeholder="Digite o seu E-mail" required style="width: 100%; padding: 10px; border: none">
                    </div>
                </div>

                <button type="submit">Recuperar</button>
            </form>

            <div class="container_down">

                <p class="p_final">
                    <a href="<?php echo APP_URL; ?>/public/index.php?pagina=login">Voltar ao Login?</a>
                </p> 
            </div>
        </div>
    </main>

</body>
</html>