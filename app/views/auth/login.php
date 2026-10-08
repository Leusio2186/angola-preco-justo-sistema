<!DOCTYPE html>
<html lang="pt-AO">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Iniciar Sessão</title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1><?php echo APP_NAME; ?></h1>
            <a href="<?php echo APP_URL; ?>/public/index.php?pagina=home" style="color: white; text-decoration: none; font-size: 14px;">&larr; Voltar ao Início</a>
        </div>
    </header>

    <main class="container">
        <div style="max-width: 400px; margin: 40px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h2 style="text-align: center; margin-bottom: 25px;">Iniciar Sessão</h2>
            
            <form action="" method="POST">
                <div style="margin-bottom: 15px;">
                    <label for="email" style="display: block; margin-bottom: 5px; font-weight: bold;">E-mail</label>
                    <input type="email" id="email" name="email" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
                
                <div style="margin-bottom: 25px;">
                    <label for="senha" style="display: block; margin-bottom: 5px; font-weight: bold;">Senha</label>
                    <input type="password" id="senha" name="senha" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
                
                <button type="submit" style="width: 100%; padding: 12px; background-color: #0056b3; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold;">Entrar</button>
            </form>
            
            <p style="text-align: center; margin-top: 20px; font-size: 14px;">
                Ainda não tem conta? <a href="<?php echo APP_URL; ?>/public/index.php?pagina=registo" style="color: #0056b3;">Criar conta</a>
            </p>
        </div>
    </main>
</body>
</html>