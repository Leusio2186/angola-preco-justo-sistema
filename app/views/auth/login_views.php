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
                <h2 style="text-align: center; margin-bottom: 5px;">Iniciar Sessão</h2>
                <p>Bem-vindo de volta! Aceda para comparar preços e contribuir no Huambo.</p>
            </div>

            <form action="<?php echo APP_URL; ?>/public/index.php?pagina=login_process" method="POST" id="container_form">
                <div class="input_1" style="margin-bottom: 15px;">
                    <div class="caixa_1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user preview-icon"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <input type="email" id="email" name="email" placeholder="Digite o seu E-mail" required style="width: 100%; padding: 10px; border: none">
                    </div>
                </div>
                
                <div class="input_2" style="margin-bottom: 15px;">
                    <div class="caixa_2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-lock preview-icon"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <input type="password" id="senha" name="senha" placeholder="Digite a sua Palavra-passe" required style="width: 100%; padding: 10px; border: none;">
                    </div>
                </div>

                <p><a href="<?php echo APP_URL; ?>/public/index.php?pagina=recuperar_password">Esqueceu-se da palavra-passe?</a></p>
                <button type="submit">Entrar</button>
            </form>

            <div class="container_down">
                <span> ---------------------- Ou inicie sessão com ----------------------- </span>
                <div class="svg">
                    <ul>
                        <li>
                            <!-- svg do google, depois pega na internet -->
                            <a href="#">
                                <?xml version="1.0" encoding="utf-8"?>
                                <!-- License: MIT. Made by artcoholic: https://github.com/artcoholic/akar-icons -->
                                <svg width="30px" height="30px" viewBox="0 0 24 24" fill="#ffffff" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0 12.067C0 18.0335 4.33333 22.9944 10 24V15.3333H7V12H10V9.33332C10 6.33332 11.9333 4.66666 14.6667 4.66666C15.5333 4.66666 16.4667 4.79999 17.3333 4.93332V7.99999H15.8C14.3333 7.99999 14 8.73332 14 9.66666V12H17.2L16.6667 15.3333H14V24C19.6667 22.9944 24 18.0335 24 12.067C24 5.43017 18.6 0 12 0C5.4 0 0 5.43017 0 12.067Z"/>
                                </svg>
                            </a>
                        </li>
                    </ul>
                </div>

                <p class="p_final">
                    Ainda não tem conta? <a href="<?php echo APP_URL; ?>/public/index.php?pagina=registo">Registe-se aqui!</a>
                </p> 
            </div>
        </div>
    </main>

</body>
</html>