<!DOCTYPE html>
<html lang="pt-AO">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Início</title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/assets/css/style.css">
    <style>
        .nav-links a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
            font-weight: bold;
            padding: 8px 15px;
            border-radius: 4px;
            background-color: rgba(255,255,255,0.2);
        }
        .nav-links a:hover { background-color: rgba(255,255,255,0.3); }
    </style>
</head>
<body>
    <header>
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div style="text-align: left;">
                <h1 style="margin-bottom: 5px;"><?php echo APP_NAME; ?></h1>
                <p style="margin: 0; font-size: 14px;">O seu portal de comparação de preços</p>
            </div>
            <div class="nav-links">
                <a href="<?php echo APP_URL; ?>/public/index.php?pagina=login">Entrar</a>
                <a href="<?php echo APP_URL; ?>/public/index.php?pagina=registo">Criar Conta</a>
            </div>
        </div>
    </header>

    <main class="container">
        <div style="margin-top: 60px; text-align: center;">
            <h2>Encontre o preço mais justo no Huambo!</h2>
            <p style="margin-top: 15px; font-size: 18px;">Pesquise produtos e compare preços nas melhores lojas da cidade.</p>
        </div>
    </main>
</body>
</html>