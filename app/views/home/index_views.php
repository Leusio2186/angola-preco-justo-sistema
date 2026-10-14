<?php
// Configuração da Conexão com o Banco de Dados precos_angola
$host = 'localhost';
$user = 'root';
$password = '';$dbname = 'precos_angola';

$conn = new mysqli($host,$user, $password,$dbname);
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// 1. Estatísticas Reais para os Contadores
$total_lojas =$conn->query("SELECT COUNT(*) as t FROM stores WHERE status = 'aprovada'")->fetch_assoc()['t'] ?? 0;
$total_precos =$conn->query("SELECT COUNT(*) as t FROM store_products")->fetch_assoc()['t'] ?? 0;
$total_usuarios =$conn->query("SELECT COUNT(*) as t FROM users")->fetch_assoc()['t'] ?? 0;

// 2. Lojas para o Mini Mapa
$result_lojas =$conn->query("SELECT id, name, latitude, longitude, address, neighborhood FROM stores WHERE status = 'aprovada' LIMIT 4");
$lojas_array = [];
while($row =$result_lojas->fetch_assoc()) {
    $lojas_array[] =$row;
}
?>

<!DOCTYPE html>
<html lang="pt-AO">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Início</title>
    <!-- O teu CSS principal -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/assets/css/index.css">
    <!-- Leaflet CSS (Para o Mini Mapa real) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Chart.js (Para o Gráfico de Variação) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
</head>
<body>

    <!-- CABEÇALHO / NAVBAR -->
    <header>
        <nav id="nav">
            <div class="nav_left">
                <a href="<?php echo APP_URL; ?>/public/index.php?pagina=home"><img src="..//public/assets/images/sem fundo1.png" alt="logotipo"></a>
                <ul>
                    <li><a href="<?php echo APP_URL; ?>/public/index.php?pagina=home">Início</a></li>
                    <li><a href="<?php echo APP_URL; ?>/public/index.php?pagina=produto_detalhes">Produtos</a></li>
                    <li><a href="<?php echo APP_URL; ?>/public/index.php?pagina=mercado">Mercados</a></li>
                    <li><a href="<?php echo APP_URL; ?>/public/index.php?pagina=sobre">Sobre</a></li>
                    <li><a href="<?php echo APP_URL; ?>/public/index.php?pagina=contacto">Contacte</a></li>
                    <li><a href="<?php echo APP_URL; ?>/public/index.php?pagina=estatisticas">Estatística</a></li>
                </ul>
            </div>
            <div class="nav_right">
                <a href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell preview-icon"><path d="M10.268 21a2 2 0 0 0 3.464 0"/><path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326"/></svg>
                </a>
                <a href="<?php echo APP_URL; ?>/public/index.php?pagina=login"><button type="button" class="button_1">Entrar</button></a>
                <a href="<?php echo APP_URL; ?>/public/index.php?pagina=registo"><button type="button" class="button_2">Registar</button></a>
            </div>
        </nav>
    </header>
    
    <!-- SEÇÃO HERO E BARRA DE PESQUISA -->
    <section id="meio">
        <div class="container">
            <h1>Compare preços,</h1>
            <h1>faça melhores escolhas</h1>
            <h1>e contribua para uma</h1>
            <h1>Angola mais justa</h1>
            <p>Consulte os precos de produtos essenciais em diferentes</p>
            <p>mercados e estabelecimentos. Informação real, atualizada e colaborativa</p>
        </div>
        
        <div id="container_1">
            <form action="pesquisa.php" method="GET" class="search-form-grid">
                <div class="container_input">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search preview-icon"><path d="m21 21-4.34-4.34"/><circle cx="11" cy="11" r="8"/></svg>

                    <input type="text" name="" id="" placeholder="Ex: Arroz, Óleo, Açucar..." required>
                </div>
        
                <!-- Aqui vai ficar a seleção para escolheres onde estás localizado e onde estás a procura dos produtos -->
                <div class="container_seletor1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin preview-icon"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
                    
                    <select name="localizacao" id="localizacao" required>
                        <option value="" disable selected>Localização</option>
                        <option value="Huambo">Huambo</option>
                        <option value="Luanda">Luanda</option>
                        <option value="Benguela">Benguela</option>
                        <option value="Lubango">Lubango</option>
                        <option value="Cuanza-sul">Cuanza-sul</option>
                    </select>
                </div>
        
                <!-- Aqui vai ficar a seleção das categorias que temos disponiveis no sistema -->
                <div class="container_seletor2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-grid preview-icon"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
                    
                    <select name="categoria" id="categoria" required>
                        <option value="" disable selected>Todas as Categorias</option>
                        <?php
                        $cat_res =$conn->query("SELECT id, name FROM categories");
                        while($cat =$cat_res->fetch_assoc()) {
                            echo "<option value='".$cat['id']."'>".$cat['name']."</option>";
                        }
                        ?>    
                    </select>
                </div>
        
                <button type="submit" class="container_button">
                    
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search preview-icon"><path d="m21 21-4.34-4.34"/><circle cx="11" cy="11" r="8"/></svg>

                    Pesquisar
                </button>
            </form>
        </div>
    </section>

    <!-- BOTÕES DE FILTRO POR CATEGORIA -->
    <div id="button_cards">
        <button type="button" class="button_1"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg> Todos</button>
        <button type="button" class="button_2"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart preview-icon"><path d="m2.05 2.05 1.099-.028a1 1 0 0 1 1.008.815l2.69 14.347A1 1 0 0 0 7.83 18H18"/><path d="M4.563 5h16.435a1 1 0 0 1 .981 1.204l-1.026 6.226A2 2 0 0 1 18.962 14H6.25"/><circle cx="18" cy="20" r="2"/><circle cx="8" cy="20" r="2"/></svg> Alimentação</button>
        <button type="button" class="button_3"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-spray-can preview-icon"><path d="M3 3h.01"/><path d="M7 5h.01"/><path d="M11 7h.01"/><path d="M3 7h.01"/><path d="M7 9h.01"/><path d="M3 11h.01"/><rect width="4" height="4" x="15" y="5"/><path d="m19 9 2 2v10c0 .6-.4 1-1 1h-6c-.6 0-1-.4-1-1V11l2-2"/><path d="m13 14 8-2"/><path d="m13 19 8-2"/></svg> Higiene</button>
        <button type="button" class="button_4"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-broom preview-icon"><path d="M13.5 10.5 22 2"/><path d="M14.734 13.841a2 2 0 00-.314-2.42L12.58 9.58a2 2 0 00-2.421-.314l-7.657 4.461A1 1 0 002.3 15.3l6.403 6.403a1 1 0 001.571-.204z"/><path d="m5 18 2-2"/><path d="m7.699 10.7 5.602 5.601"/></svg> Limpeza</button>
        <button type="button" class="button_5"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-cup-soda preview-icon"><path d="m6 8 1.75 12.28a2 2 0 0 0 2 1.72h4.54a2 2 0 0 0 2-1.72L18 8"/><path d="M5 8h14"/><path d="M7 15a6.47 6.47 0 0 1 5 0 6.47 6.47 0 0 0 5 0"/><path d="m12 8 1-6h2"/></svg> Bebidas</button>
        <button type="button" class="button_6"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg> Outros</button>
    </div>

    <!-- PAINEL CENTRAL (GRID COM AS 5 CAIXAS PRINCIPAIS) -->
    <main id="meio_principal">


        <!-- 1. PRODUTOS EM DESTAQUE -->
        <div id="meio_1">
            <div class="sub_1">
                <div class="sub_2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-tags preview-icon" id="svg1"><path d="M13.172 2a2 2 0 0 1 1.414.586l6.71 6.71a2.4 2.4 0 0 1 0 3.408l-4.592 4.592a2.4 2.4 0 0 1-3.408 0l-6.71-6.71A2 2 0 0 1 6 9.172V3a1 1 0 0 1 1-1z"/><path d="M2 7v6.172a2 2 0 0 0 .586 1.414l6.71 6.71a2.4 2.4 0 0 0 3.191.193"/><circle cx="10.5" cy="6.5" r=".5" fill="currentColor"/></svg>
                    <h2>Produtos em Destaque</h2>
                </div>
                <a href="<?php echo APP_URL; ?>/public/index.php?pagina=produto_detalhes">Ver todos 
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="svg_2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>
            </div>

            <div class="tabela-destaques-container">
                <table class="tabela-produtos">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>1.º Preço</th>
                            <th>2.º Preço</th>
                            <th>3.º Preço</th>
                            <th>Atualizado em</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql_prod = "SELECT id, name, unit_measure, image_path FROM products LIMIT 4";
                        $res_prod = $conn->query($sql_prod);
                        if ($res_prod &&$res_prod->num_rows > 0) {
                            while($prod =$res_prod->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td class='prod-td-info'>";
                                echo "<img src='public/assets/images/".($prod['image_path'] ?: 'default.png')."' alt='Produto' class='img-produto-tabela'>";
                                echo "<div><strong>".$prod['name']."</strong><br><small>(".($prod['unit_measure']).")</small></div>";
                                echo "</td>";
                                echo "<td><strong>1.000 Kz</strong><br><small>Kero Supermercado</small></td>";
                                echo "<td>1.200 Kz<br><small>Super 3 Irmãos</small></td>";
                                echo "<td>1.450 Kz<br><small>Mercado Central</small></td>";
                                echo "<td><small>Hoje, 10:24</small></td>";
                                echo "<td><a href='detalhes.php?id=".$prod['id']."' class='btn-comparar'>Comparar</a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' style='text-align:center; padding: 20px; color:#666;'>Nenhum produto cadastrado no banco de dados.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>


        <!-- 2. LOJAS PRÓXIMAS & MINI MAPA -->
        <div id="meio_2">
            <div class="sub_1">
                <div class="sub_2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map preview-icon" id="svg2"><path d="M14.106 5.553a2 2 0 0 0 1.788 0l3.659-1.83A1 1 0 0 1 21 4.619v12.764a1 1 0 0 1-.553.894l-4.553 2.277a2 2 0 0 1-1.788 0l-4.212-2.106a2 2 0 0 0-1.788 0l-3.659 1.83A1 1 0 0 1 3 19.381V6.618a1 1 0 0 1 .553-.894l4.553-2.277a2 2 0 0 1 1.788 0z"/><path d="M15 5.764v15"/><path d="M9 3.236v15"/></svg>

                    <h2>Lojas Próximas</h2>
                </div>
                <!-- Clicar aqui abre a página do mapa completo com todas as lojas cadastradas -->
                <a href="<?php echo APP_URL; ?>/public/index.php?pagina=mapa">
                    Ver no mapa 
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="svg_2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>
            </div>
            

            <!-- Aqui vai ficar um mini mapa de Angola/Huambo-->
            <!-- Mini Mapa Interativo (Leaflet.js) -->
            <div id="mini-map" class="mini_mapa"></div>

            <!-- Aqui vai ficar a variação das lojás mais frequentadas ou mais pesquisadas no máximo 5-->
            <div class="mercados">
                <?php foreach($lojas_array as$loja): ?>
                    <div class="loja-item-lista">
                        <div>
                            <strong><?php echo htmlspecialchars($loja['name']); ?></strong>
                            <span><?php echo htmlspecialchars($loja['address'] . ' - ' .$loja['neighborhood']); ?></span>
                        </div>
                        <span class="loja-distancia">1,2 km</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>


        <!-- 3. VARIAÇÃO DE PREÇOS -->
        <div id="meio_3">
            <div class="sub_1">
                <div class="sub_2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chart-no-axes-combined preview-icon" id="svg3"><path d="M12 16v5"/><path d="M16 14.639V21"/><path d="M20 10.656V21"/><path d="m22 3-8.646 8.646a.5.5 0 0 1-.708 0L9.354 8.354a.5.5 0 0 0-.707 0L2 15"/><path d="M4 18.463V21"/><path d="M8 14.656V21"/></svg>
    
                    <h2>Variação de Precos</h2>
                </div>
                <a href="<?php echo APP_URL; ?>/public/index.php?pagina=estatisticas">7 dias 
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right preview-icon" id="svg_2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>
            </div>

            <!-- Aqui vai ficar a variação da mudança dos preços dos produtos mais pesquisados -->
            <div class="variacao_preco">
                <canvas id="graficoVariacao"></canvas>
            </div>

            <!-- Aqui vai ficar a variação das lojás mais frequentadas ou mais pesquisadas no máximo 3-->
            <div class="variacao_lojas">
                <h3>Variação de Lojás</h3>
            </div>
        </div>
        
        <!-- 4. PRODUTOS MAIS PESQUISADOS -->
        <div id="meio_4">
            <div class="sub_1">
                <div class="sub_2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-flame preview-icon" id="svg4"><path d="M12 3q1 4 4 6.5t3 5.5a1 1 0 0 1-14 0 5 5 0 0 1 1-3 1 1 0 0 0 5 0c0-2-1.5-3-1.5-5q0-2 2.5-4"/></svg>
    
                    <h2>Produtos Mais Pesquisados</h2>
                </div>
                <a href="<?php echo APP_URL; ?>/public/index.php?pagina=estatisticas">
                    Ver todos
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right preview-icon" id="svg_2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>
            </div>

            <!-- Aqui vai ficar o top 5 dos produtos mais pesquisadas -->
            <div class="sub_3">
                <?php
                $sql_top = "SELECT name FROM products LIMIT 5";
                $res_top =$conn->query($sql_top);$i = 1;
                while($top =$res_top->fetch_assoc()) {
                    echo "<div class='top-produto-item'>";
                    echo "<span><strong class='badge-pos'>#".$i++."</strong> ".$top['name']."</span>";
                    echo "<span class='text-muted'>1.245 buscas</span>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>

        <!-- 5. CONTRIBUA COM OS PREÇOS E ESTATÍSTICAS (COM ANIMAÇÃO EM TEMPO REAL) -->
        <div id="contribua_precos">
            <div id="Card_principal">
                <img src="..//..//public/assets/images/icone1-Photoroom.png" alt="svg">
                <div id="Subcard_principal">
                    <h3>Contribua com os preços!</h3>
                    <p class="p1">Seu contributo ajuda outras pessoas</p>
                    <p class="p2">a fazeram melhores escolhas.</p>
                    <a href="<?php echo APP_URL; ?>/public/index.php?pagina=contribuir">
                        Registar Preço 
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <div id="Cards">
                <div class="Cards_1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                    </svg>

                    <!--O Número de lojás cadastradas vai ficar aqui vai vir do Banco automaticamente -->
                    <h1 id="contador-lojas" class="transition-all duration-300 transform">
                        <?php echo number_format($total_lojas, 0, ',', '.'); ?>
                    </h1>
                    <p>Lojás cadastradas</p>
                </div>

                <div class="Cards_2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-tags preview-icon"><path d="M13.172 2a2 2 0 0 1 1.414.586l6.71 6.71a2.4 2.4 0 0 1 0 3.408l-4.592 4.592a2.4 2.4 0 0 1-3.408 0l-6.71-6.71A2 2 0 0 1 6 9.172V3a1 1 0 0 1 1-1z"/><path d="M2 7v6.172a2 2 0 0 0 .586 1.414l6.71 6.71a2.4 2.4 0 0 0 3.191.193"/><circle cx="10.5" cy="6.5" r=".5" fill="currentColor"/></svg>
                    
                    <!--O Número de preços registrados vai ficar aqui vai vir do Banco automaticamente -->
                    <h1 id="contador-precos" class="transition-all duration-300 transform">
                        <?php echo number_format($total_precos, 0, ',', '.'); ?>
                    </h1>
                    <p>Preços registrados</p>
                </div>

                <div class="Cards_3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.    5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>


                    <!--O Número de Utilizadores registrados vai ficar aqui vai vir do Banco automaticamente -->
                    <h1 id="contador-usuarios" class="transition-all duration-300 transform">
                        <?php echo number_format($total_usuarios, 0, ',', '.'); ?>
                    </h1>
                    <p>Utilizadores ativos</p>
                </div>
            </div>
        </div>
    </main>


    <!-- RODAPÉ COMPLETO -->
    <footer id="footer">
        <div class="footer_img">
            <a href="<?php echo APP_URL; ?>/public/index.php?pagina=home"><img src="..//public/assets/images/sem fundo1.png" alt="logotipo"></a>
        </div>

        <div class="footer_links1">
            <h3>Links Rápidos</h3>
            <ul>
                <li><a href="<?php echo APP_URL; ?>/public/index.php?pagina=home">Início</a></li>
                <li><a href="<?php echo APP_URL; ?>/public/index.php?pagina=contacto">Contacte</a></li>
                <li><a href="<?php echo APP_URL; ?>/public/index.php?pagina=loja_detalhes">Produtos</a></li>
                <li><a href="<?php echo APP_URL; ?>/public/index.php?pagina=mercado">Mercados</a></li>
                <li><a href="<?php echo APP_URL; ?>/public/index.php?pagina=estatisticas">Estatística</a></li>
            </ul>
        </div>

        <div class="footer_links2">
            <h3>Para Utilizadores</h3>
            <ul>
                <li><a href="<?php echo APP_URL; ?>/public/index.php?pagina=sobre">Ajuda</a></li> 
                <li><a href="<?php echo APP_URL; ?>/public/index.php?pagina=privacidade">Privacidade</a></li>
                <li><a href="<?php echo APP_URL; ?>/public/index.php?pagina=funcionamento">Como Funciona</a></li>
                <li><a href="<?php echo APP_URL; ?>/public/index.php?pagina=home">Termos de Uso</a></li>
            </ul>
        </div>

        <div class="footer_links3">
            <h3>Siga-nos</h3>
            <ul>
                <li>
                    <a href="#">
                        <?xml version="1.0" encoding="utf-8"?>
                        <!-- License: MIT. Made by artcoholic: https://github.com/artcoholic/akar-icons -->
                        <svg width="30px" height="30px" viewBox="0 0 24 24" fill="#ffffff" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M0 12.067C0 18.0335 4.33333 22.9944 10 24V15.3333H7V12H10V9.33332C10 6.33332 11.9333 4.66666 14.6667 4.66666C15.5333 4.66666 16.4667 4.79999 17.3333 4.93332V7.99999H15.8C14.3333 7.99999 14 8.73332 14 9.66666V12H17.2L16.6667 15.3333H14V24C19.6667 22.9944 24 18.0335 24 12.067C24 5.43017 18.6 0 12 0C5.4 0 0 5.43017 0 12.067Z"/>
                        </svg>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <?xml version="1.0" encoding="utf-8"?>
                        <!-- License: MIT. Made by michaelampr: https://github.com/michaelampr/jam -->
                        <svg fill="#ffffff" width="30px" height="30px" viewBox="-2 -2 24 24" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMinYMin" class="jam jam-instagram"><path d='M14.017 0h-8.07A5.954 5.954 0 0 0 0 5.948v8.07a5.954 5.954 0 0 0 5.948 5.947h8.07a5.954 5.954 0 0 0 5.947-5.948v-8.07A5.954 5.954 0 0 0 14.017 0zm3.94 14.017a3.94 3.94 0 0 1-3.94 3.94h-8.07a3.94 3.94 0 0 1-3.939-3.94v-8.07a3.94 3.94 0 0 1 3.94-3.939h8.07a3.94 3.94 0 0 1 3.939 3.94v8.07z'/><path d='M9.982 4.819A5.17 5.17 0 0 0 4.82 9.982a5.17 5.17 0 0 0 5.163 5.164 5.17 5.17 0 0 0 5.164-5.164A5.17 5.17 0 0 0 9.982 4.82zm0 8.319a3.155 3.155 0 1 1 0-6.31 3.155 3.155 0 0 1 0 6.31z'/><circle cx='15.156' cy='4.858' r='1.237'/></svg>
                    </a>
                </li>

                <li>
                    <a href="#" class="icone">
                        <?xml version="1.0" encoding="utf-8"?>
                        <!-- License: MIT. Made by michaelampr: https://github.com/michaelampr/jam -->
                        <svg fill="#ffffff" width="30px" height="30px" viewBox="-2 -2 24 24" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMinYMin" class="jam jam-whatsapp"><path d='M9.516.012C4.206.262.017 4.652.033 9.929a9.798 9.798 0 0 0 1.085 4.465L.06 19.495a.387.387 0 0 0 .47.453l5.034-1.184a9.981 9.981 0 0 0 4.284 1.032c5.427.083 9.951-4.195 10.12-9.58C20.15 4.441 15.351-.265 9.516.011zm6.007 15.367a7.784 7.784 0 0 1-5.52 2.27 7.77 7.77 0 0 1-3.474-.808l-.701-.347-3.087.726.65-3.131-.346-.672A7.62 7.62 0 0 1 2.197 9.9c0-2.07.812-4.017 2.286-5.48a7.85 7.85 0 0 1 5.52-2.271c2.086 0 4.046.806 5.52 2.27a7.672 7.672 0 0 1 2.287 5.48c0 2.052-.825 4.03-2.287 5.481z'/><path d='M14.842 12.045l-1.931-.55a.723.723 0 0 0-.713.186l-.472.478a.707.707 0 0 1-.765.16c-.913-.367-2.835-2.063-3.326-2.912a.694.694 0 0 1 .056-.774l.412-.53a.71.71 0 0 0 .089-.726L7.38 5.553a.723.723 0 0 0-1.125-.256c-.539.453-1.179 1.14-1.256 1.903-.137 1.343.443 3.036 2.637 5.07 2.535 2.349 4.566 2.66 5.887 2.341.75-.18 1.35-.903 1.727-1.494a.713.713 0 0 0-.408-1.072z'/></svg>
                    </a>
                </li>
            </ul>
        </div>
    </footer>

    <div class="footer_baixo">
        <p>©2026 Angola Preço Justo. Todos os direitos reservados</p>
        <p>Um projecto de estudantes angolanos para uma Angola melhor.</p>
    </div>


    <!-- SCRIPTS JAVASCRIPT (MAPA, GRÁFICO E POLLING EM TEMPO REAL) -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // 1. Inicialização do Mini Mapa (Leaflet.js) centrado em Huambo
        const map = L.map('mini-map').setView([-12.7761, 15.7344], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        // Injetando as lojas do PHP para os marcadores do Leaflet
        const lojasCoordenadas = <?php echo json_encode($lojas_array); ?>;
        lojasCoordenadas.forEach(loja => {
            if (loja.latitude && loja.longitude) {
                L.marker([loja.latitude, loja.longitude]).addTo(map)
                  .bindPopup(`<b>${loja.name}</b><br>${loja.address}`);
            }
        });

        // 2. Gráfico de Variação de Preços (Chart.js)
        const ctx = document.getElementById('graficoVariacao').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['17/09', '18/09', '19/09', '20/09', '21/09', '22/09', '23/09'],
                datasets: [{
                    label: 'Média de Preços (Kz)',
                    data: [1100, 1050, 1000, 1080, 1020, 1000, 1000],
                    borderColor: '#059669',
                    backgroundColor: 'rgba(5, 150, 105, 0.05)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // 3. Efeito de Animação (Slide Up) e Polling em Tempo Real para os Contadores
        function atualizarComEfeito(elementoId, novoValor) {
            const elemento = document.getElementById(elementoId);
            if (!elemento || elemento.innerText === novoValor) return;

            elemento.style.transform = 'translateY(-10px)';
            elemento.style.opacity = '0';

            setTimeout(() => {
                elemento.innerText = novoValor;
                elemento.style.transform = 'translateY(10px)';
                
                setTimeout(() => {
                    elemento.style.transform = 'translateY(0)';
                    elemento.style.opacity = '1';
                }, 50);
            }, 250);
        }

        function verificarAtualizacoesTempoReal() {
            fetch('api-estatisticas.php')
                .then(res => res.json())
                .then(data => {
                    atualizarComEfeito('contador-lojas', data.lojas);
                    atualizarComEfeito('contador-precos', data.precos);
                    atualizarComEfeito('contador-usuarios', data.usuarios);
                })
                .catch(err => console.log('Aguardando nova sincronização...'));
        }

        // Verifica a base de dados a cada 5 segundos automaticamente sem recarregar a página
        setInterval(verificarAtualizacoesTempoReal, 5000);
    </script>
</body>
</html>