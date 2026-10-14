/* <!------- SCRIPTS JAVASCRIPT (Para o Mapa, Gráfico e Polling em Tempo Real --------> */

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

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