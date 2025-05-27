<?php

include('../header.php');

?>
<body class="transition-colors duration-300">
    <div class="min-h-screen flex flex-col items-center py-8 px-4 sm:px-6 lg:px-8">
        <header class="w-full max-w-4xl flex justify-between items-center mb-8">
            <h3 class="text-2xl sm:text-4xl font-extrabold text-gray-900 dark:text-white">Aposta Inteligente - iTatecnica</h3>
            <button id="themeToggle" class="btn-modern button-secondary">
                <i class="fas fa-sun" id="themeIcon"></i>
                <span class="ml-2">Alternar Tema</span>
            </button>
        </header>

        <main class="w-full max-w-7xl grid grid-cols-1 md:grid-cols-2 gap-8">
            <section class="container-card p-6 rounded-lg shadow-lg">
                <h2 class="text-2xl font-semibold mb-4">Gerar Números Aleatórios</h2>
                <div class="mb-4">
                    <label for="lotterySelect" class="block text-sm font-medium mb-2">Escolha a Loteria:</label>
                    <select id="lotterySelect" class="w-full p-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="diadesorte">Dia de Sorte</option>
                        <option value="duplasena">Duplasena</option>
                        <option value="quina">Quina</option>
                        <option value="lotofacil">Lotofácil</option>
                        <option value="lotomania">Lotomania</option>
                        <option value="megasena">Megasena</option>
                        <option value="supersete">Supersete</option>
                        <option value="timemania">Timemania</option>
                        <option value="maismilionaria">+Milionária</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="gameQuantitySelect" class="block text-sm font-medium mb-2">Quantidade de Jogos:</label>
                    <select id="gameQuantitySelect" class="w-full p-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="1">1 Jogo</option>
                        <option value="2">2 Jogos</option>
                        <option value="3">3 Jogos</option>
                        <option value="4">4 Jogos</option>
                        <option value="5">5 Jogos</option>
                    </select>
                </div>
                <div class="mb-4 flex items-center">
                    <input type="checkbox" id="useStatsForGeneration" class="mr-2 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                    <label for="useStatsForGeneration" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Usar Estatísticas para Geração
                    </label>
                    <i class="fas fa-info-circle text-gray-400 ml-2 cursor-help" 
                       title="Gera números usando 50% dos mais sorteados e 50% de números aleatórios (incluindo os menos sorteados e não sorteados). Requer histórico populado na Análise Estatística."></i>
                </div>
                <button id="generateNumbersBtn" class="btn-modern button-primary w-full">
                    <i class="fas fa-dice"></i>
                    Gerar Números
                </button>
                <div id="generatedNumbers" class="mt-4 p-4 border border-dashed border-gray-300 rounded-md min-h-[80px] dark:border-gray-600">
                    <p class="text-gray-500 dark:text-gray-400">Números gerados aparecerão aqui.</p>
                </div>
                <button id="saveGeneratedBtn" class="btn-modern button-secondary w-full mt-4" style="display: none;">
                    <i class="fas fa-save"></i>
                    Salvar no Histórico
                </button>
            </section>

            <section class="container-card p-6 rounded-lg shadow-lg">
                <h2 class="text-2xl font-semibold mb-4">Consultar Resultados</h2>
                <div class="mb-4">
                    <label for="resultLotterySelect" class="block text-sm font-medium mb-2">Escolha a Loteria:</label>
                    <select id="resultLotterySelect" class="w-full p-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="diadesorte">Dia de Sorte</option>
                        <option value="duplasena">Duplasena</option>
                        <option value="federal">Federal</option>
                        <option value="quina">Quina</option>
                        <option value="loteca">Loteca</option>
                        <option value="lotofacil">Lotofácil</option>
                        <option value="lotomania">Lotomania</option>
                        <option value="megasena">Megasena</option>
                        <option value="supersete">Supersete</option>
                        <option value="timemania">Timemania</option>
                        <option value="maismilionaria">+Milionária</option>
                    </select>
                </div>
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4 mb-4">
                    <button id="currentResultBtn" class="btn-modern button-primary flex-1">
                        <i class="fas fa-search"></i>
                        Concurso Atual
                    </button>
                    <button id="previousResultBtn" class="btn-modern button-secondary flex-1">
                        <i class="fas fa-history"></i>
                        Concurso Anterior
                    </button>
                </div>
                <div id="lotteryResults" class="mt-4 p-4 border border-dashed border-gray-300 rounded-md min-h-[120px] dark:border-gray-600">
                    <p class="text-gray-500 dark:text-gray-400">Resultados aparecerão aqui.</p>
                </div>
            </section>

            <section class="container-card p-6 rounded-lg shadow-lg md:col-span-2">
                <h2 class="text-2xl font-semibold mb-4">Análise Estatística</h2>
                <div class="mb-4">
                    <label for="statsLotterySelect" class="block text-sm font-medium mb-2">Escolha a Loteria:</label>
                    <select id="statsLotterySelect" class="w-full p-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="diadesorte">Dia de Sorte</option>
                        <option value="duplasena">Duplasena</option>
                        <option value="quina">Quina</option>
                        <option value="lotofacil">Lotofácil</option>
                        <option value="lotomania">Lotomania</option>
                        <option value="megasena">Megasena</option>
                        <option value="supersete">Supersete</option>
                        <option value="timemania">Timemania</option>
                        <option value="maismilionaria">+Milionária</option>
                    </select>
                </div>
<button id="populateHistoryBtn" class="btn-modern button-secondary w-full mb-4" disabled>
    <i class="fas fa-cloud-download-alt"></i>
    Popular Histórico (Últimos 50)
</button>
                <button id="analyzeStatsBtn" class="btn-modern button-primary w-full">
                    <i class="fas fa-chart-bar"></i>
                    Analisar Estatísticas
                </button>
                <div id="statsResults" class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div id="mostFrequentNumbersContainer" class="p-4 border border-dashed border-gray-300 rounded-md dark:border-gray-600">
                        <h3 class="text-xl font-medium mb-2">Números Mais Sorteados</h3>
                        <div id="mostFrequentNumbers" class="text-gray-500 dark:text-gray-400 flex flex-wrap gap-2">
                            </div>
                    </div>
                    <div id="leastFrequentNumbersContainer" class="p-4 border border-dashed border-gray-300 rounded-md dark:border-gray-600">
                        <h3 class="text-xl font-medium mb-2">Números Menos Sorteados</h3>
                        <div id="leastFrequentNumbers" class="text-gray-500 dark:text-gray-400 flex flex-wrap gap-2">
                            </div>
                    </div>
                    <div id="mostFrequentMonths" class="p-4 border border-dashed border-gray-300 rounded-md dark:border-gray-600 hidden">
                        <h3 class="text-xl font-medium mb-2">Meses da Sorte Mais Frequentes</h3>
                        <div id="monthsContent" class="text-gray-500 dark:text-gray-400 flex flex-wrap gap-2"></div>
                    </div>
                    <div id="mostFrequentTeams" class="p-4 border border-dashed border-gray-300 rounded-md dark:border-gray-600 hidden">
                        <h3 class="text-xl font-medium mb-2">Times do Coração Mais Frequentes</h3>
                        <div id="teamsContent" class="text-gray-500 dark:text-gray-400 flex flex-wrap gap-2"></div>
                    </div>
                    <div id="mostFrequentTrevos" class="p-4 border border-dashed border-gray-300 rounded-md dark:border-gray-600 hidden">
                        <h3 class="text-xl font-medium mb-2">Trevos Mais Sorteados (+Milionária)</h3>
                        <div id="trevosContent" class="text-gray-500 dark:text-gray-400 flex flex-wrap gap-2"></div>
                    </div>
                    <div id="leastFrequentTrevos" class="p-4 border border-dashed border-gray-300 rounded-md dark:border-gray-600 hidden">
                        <h3 class="text-xl font-medium mb-2">Trevos Menos Sorteados (+Milionária)</h3>
                        <div id="leastTrevosContent" class="text-gray-500 dark:text-gray-400 flex flex-wrap gap-2"></div>
                    </div>

                    <div id="statsMessage" class="md:col-span-2 text-center text-gray-600 dark:text-gray-400 mt-2">
                        </div>
                </div>
            </section>

            <section class="container-card p-6 rounded-lg shadow-lg">
                <h2 class="text-2xl font-semibold mb-4">Exportar Dados</h2>
                <div class="mb-4">
                    <label for="exportDataType" class="block text-sm font-medium mb-2">Tipo de Dados:</label>
                    <select id="exportDataType" class="w-full p-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="generated">Números Gerados</option>
                        <option value="results">Resultados da Loteria</option>
                        <option value="stats">Estatísticas</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="exportFormat" class="block text-sm font-medium mb-2">Formato:</label>
                    <select id="exportFormat" class="w-full p-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="json">JSON</option>
                        <option value="sql">SQL</option>
                        <option value="txt">TXT (CSV)</option>
                    </select>
                </div>
                <button id="exportBtn" class="btn-modern button-primary w-full">
                    <i class="fas fa-download"></i>
                    Exportar
                </button>
            </section>

            <section class="container-card p-6 rounded-lg shadow-lg">
                <h2 class="text-2xl font-semibold mb-4">Histórico de Apostas</h2>
                <div id="bettingHistory" class="mt-4 p-4 border border-dashed border-gray-300 rounded-md min-h-[150px] overflow-y-auto dark:border-gray-600">
                    <p class="text-gray-500 dark:text-gray-400">Seu histórico de apostas aparecerá aqui.</p>
                </div>
                <button id="clearHistoryBtn" class="btn-modern button-secondary w-full mt-4">
                    <i class="fas fa-trash-alt"></i>
                    Limpar Histórico
                </button>
            </section>

            <section class="container-card p-6 rounded-lg shadow-lg md:col-span-2">
                <h2 class="text-2xl font-semibold mb-4">Análise po IA</h2>
                <div class="mb-4">
                    <label for="aiLotterySelect" class="block text-sm font-medium mb-2">Escolha a Loteria para IA:</label>
                    <select id="aiLotterySelect" class="w-full p-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="diadesorte">Dia de Sorte</option>
                        <option value="duplasena">Duplasena</option>
                        <option value="quina">Quina</option>
                        <option value="loteca">Loteca</option>
                        <option value="lotofacil">Lotofácil</option>
                        <option value="lotomania">Lotomania</option>
                        <option value="megasena">Megasena</option>
                        <option value="supersete">Supersete</option>
                        <option value="timemania">Timemania</option>
                        <option value="maismilionaria">+Milionária</option>
                    </select>
                </div>
                <button id="runAiAnalysisBtn" class="btn-modern button-primary w-full">
                    <i class="fas fa-brain"></i>
                    Rodar Análise de IA
                </button>
                <div id="aiAnalysisResults" class="mt-4 p-4 border border-dashed border-gray-300 rounded-md min-h-[100px] dark:border-gray-600">
                    <p class="text-gray-500 dark:text-gray-400">Resultados da análise de IA aparecerão aqui.</p>
                </div>
            </section>
        </main>

        <footer class="mt-8 text-center text-gray-600 dark:text-gray-400">
            <p>&copy; 2025 Sistema de Loterias para auxiliar suas apostas. Desenvolvido por: jeGodoy.</p>
			<p> Jogue com consciência, iTatecnica apoia o jogo responsável </p>
        </footer>
    </div>

    <div id="messageModal" class="modal hidden">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h3 id="modalTitle" class="text-xl font-semibold mb-4"></h3>
            <p id="modalMessage"></p>
            <div class="mt-4 text-right">
                <button class="btn-modern button-primary" onclick="closeModal()">OK</button>
            </div>
        </div>
    </div>

    <script>
        // Variáveis globais para armazenar o número do concurso atualmente exibido e o número do concurso anterior
        let currentDisplayedContestNumber = null;
        let currentPreviousContestNumber = null; // Armazena o numeroConcursoAnterior vindo da API

        // Objeto para armazenar as configurações das loterias (dezenas, min, max, etc.)
        // Estas configurações devem estar sincronizadas com as do backend (lottery_functions.php)
        const lotteryConfigs = {
            'diadesorte': { count: 7, min: 1, max: 31, hasMonth: true },
            'duplasena': { count: 6, min: 1, max: 50 },
            'federal': { count: 5, min: 1, max: 100000 }, // Federal é por bilhete
            'quina': { count: 5, min: 1, max: 80 },
            'loteca': { count: 14, min: 1, max: 14 }, // Loteca é por resultados de jogos
            'lotofacil': { count: 15, min: 1, max: 25 },
            'lotomania': { count: 50, min: 1, max: 100 },
            'megasena': { count: 6, min: 1, max: 60 },
            'supersete': { count: 7, min: 0, max: 9 },
            'timemania': { count: 10, min: 1, max: 80, hasTeam: true },
            'maismilionaria': { count: 6, min: 1, max: 50, trevos_count: 2, trevos_min: 1, trevos_max: 6 }
        };

        // Array com os nomes dos meses para Dia de Sorte
        const monthNames = [
            "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
            "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
        ];

        // Objeto para armazenar as estatísticas de números mais/menos sorteados
        let lotteryStatistics = {}; 

        // Função para mostrar o modal de mensagem
        function showModal(title, message, isConfirm = false, confirmCallback = null) {
            document.getElementById('modalTitle').innerText = title;
            document.getElementById('modalMessage').innerText = message;
            const modal = document.getElementById('messageModal');
            const okButton = modal.querySelector('.modal-content button');

            if (isConfirm) {
                okButton.textContent = 'Sim';
                let cancelButton = okButton.parentNode.querySelector('.btn-modern.button-secondary');
                if (!cancelButton) {
                    cancelButton = document.createElement('button');
                    cancelButton.className = 'btn-modern button-secondary ml-2';
                    cancelButton.textContent = 'Não';
                    cancelButton.onclick = closeModal;
                    okButton.parentNode.insertBefore(cancelButton, okButton.nextSibling);
                }

                okButton.onclick = () => {
                    if (confirmCallback) {
                        confirmCallback();
                    }
                    closeModal();
                };
            } else {
                okButton.textContent = 'OK';
                okButton.onclick = closeModal;
                const existingCancelButton = okButton.parentNode.querySelector('.btn-modern.button-secondary');
                if (existingCancelButton) {
                    existingCancelButton.remove();
                }
            }
            modal.classList.remove('hidden');
        }

        // Função para fechar o modal
        function closeModal() {
            document.getElementById('messageModal').classList.add('hidden');
        }

        // Event listener para o botão de fechar do modal
        document.querySelector('.modal .close-button').addEventListener('click', closeModal);

        // Função para alternar o tema (claro/escuro)
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const body = document.body;

        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            body.classList.add('dark-mode');
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
        } else {
            body.classList.remove('dark-mode');
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        }

        themeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            if (body.classList.contains('dark-mode')) {
                localStorage.setItem('theme', 'dark');
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
            } else {
                localStorage.setItem('theme', 'light');
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            }
        });

        let currentGeneratedNumbers = [];

        const API_BASE_URL = 'api.php';

        async function fetchData(action, params = {}) {
            const urlParams = new URLSearchParams(params);
            const url = `${API_BASE_URL}?action=${action}&${urlParams.toString()}`;
            try {
                const response = await fetch(url);
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error(`Erro HTTP! status: ${response.status}, resposta: ${errorText}`);
                    throw new Error(`Erro HTTP! status: ${response.status}`);
                }
                return await response.json();
            } catch (error) {
                console.error('Erro ao buscar dados:', error);
                showModal('Erro', `Não foi possível carregar os dados. Detalhes: ${error.message}`);
                return null;
            }
        }

        /**
         * Gera números de loteria baseados em estatísticas (50% mais sorteados, 50% aleatórios).
         * @param {string} lotteryType O tipo de loteria.
         * @param {object} mostFrequent Objeto com os números mais frequentes e suas contagens.
         * @returns {Array|object} Array de números gerados, ou objeto {dezenas, trevos} para +Milionária.
         */
        function generateNumbersBasedOnStats(lotteryType, mostFrequent) {
            const config = lotteryConfigs[lotteryType];
            if (!config) {
                console.error("Configuração da loteria não encontrada para geração estatística:", lotteryType);
                return [];
            }

            const totalNumbersNeeded = config.count;
            const numbers = new Set(); // Usar Set para garantir números únicos

            // Calcular 50% dos números a serem pegos dos mais frequentes
            const numFromMost = Math.round(totalNumbersNeeded * 0.50);

            // Converter os números mais frequentes para um array para facilitar a seleção
            const mostFreqNumbersArray = Object.keys(mostFrequent).map(Number);

            // 1. Selecionar aleatoriamente 'numFromMost' números dos mais frequentes
            // Criar uma cópia para poder embaralhar sem afetar o original
            let shuffledMostFreq = [...mostFreqNumbersArray];
            // Função de embaralhamento Fisher-Yates
            for (let i = shuffledMostFreq.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [shuffledMostFreq[i], shuffledMostFreq[j]] = [shuffledMostFreq[j], shuffledMostFreq[i]];
            }

            // Pegar 'numFromMost' números únicos da lista embaralhada
            let pickedFromMost = 0;
            for (let i = 0; i < shuffledMostFreq.length && pickedFromMost < numFromMost; i++) {
                if (!numbers.has(shuffledMostFreq[i])) {
                    numbers.add(shuffledMostFreq[i]);
                    pickedFromMost++;
                }
            }

            // 2. Completar com números aleatórios do intervalo total, evitando duplicatas
            while (numbers.size < totalNumbersNeeded) {
                let randomNumber = Math.floor(Math.random() * (config.max - config.min + 1)) + config.min;
                if (!numbers.has(randomNumber)) {
                    numbers.add(randomNumber);
                }
            }

            let generatedDezenas = Array.from(numbers).sort((a, b) => a - b);

            // Lógica específica para +Milionária: gerar trevos aleatórios
            if (lotteryType === 'maismilionaria') {
                const trevos = new Set();
                while (trevos.size < config.trevos_count) {
                    trevos.add(Math.floor(Math.random() * (config.trevos_max - config.trevos_min + 1)) + config.trevos_min);
                }
                return { dezenas: generatedDezenas, trevos: Array.from(trevos).sort((a, b) => a - b) };
            } 
            // Lógica específica para Dia de Sorte: gerar Mês da Sorte aleatório
            else if (lotteryType === 'diadesorte' && config.hasMonth) {
                const randomMonthIndex = Math.floor(Math.random() * monthNames.length);
                const mesDaSorte = monthNames[randomMonthIndex];
                return { dezenas: generatedDezenas, mesDaSorte: mesDaSorte };
            }

            return generatedDezenas;
        }

        /**
         * Gera números de loteria puramente aleatórios.
         * @param {string} lotteryType O tipo de loteria.
         * @returns {Array|object} Array de números gerados, ou objeto {dezenas, trevos|mesDaSorte} para +Milionária/Dia de Sorte.
         */
        function generatePureRandomNumbers(lotteryType) {
            const config = lotteryConfigs[lotteryType];
            if (!config) {
                console.error("Configuração da loteria não encontrada para geração aleatória:", lotteryType);
                return [];
            }

            const numbers = new Set();
            while (numbers.size < config.count) {
                numbers.add(Math.floor(Math.random() * (config.max - config.min + 1)) + config.min);
            }
            let generatedDezenas = Array.from(numbers).sort((a, b) => a - b);

            // Lógica específica para +Milionária: gerar trevos aleatórios
            if (lotteryType === 'maismilionaria') {
                const trevos = new Set();
                while (trevos.size < config.trevos_count) {
                    trevos.add(Math.floor(Math.random() * (config.trevos_max - config.trevos_min + 1)) + config.trevos_min);
                }
                return { dezenas: generatedDezenas, trevos: Array.from(trevos).sort((a, b) => a - b) };
            } 
            // Lógica específica para Dia de Sorte: gerar Mês da Sorte aleatório
            else if (lotteryType === 'diadesorte' && config.hasMonth) {
                const randomMonthIndex = Math.floor(Math.random() * monthNames.length);
                const mesDaSorte = monthNames[randomMonthIndex];
                return { dezenas: generatedDezenas, mesDaSorte: mesDaSorte };
            }

            return generatedDezenas;
        }


        const generateNumbersBtn = document.getElementById('generateNumbersBtn');
        const lotterySelect = document.getElementById('lotterySelect');
        const gameQuantitySelect = document.getElementById('gameQuantitySelect');
        const generatedNumbersDiv = document.getElementById('generatedNumbers');
        const saveGeneratedBtn = document.getElementById('saveGeneratedBtn');
        const useStatsForGenerationCheckbox = document.getElementById('useStatsForGeneration');


        generateNumbersBtn.addEventListener('click', async () => {
            generatedNumbersDiv.innerHTML = '<p class="text-gray-500 dark:text-gray-400">Gerando números...</p>';
            saveGeneratedBtn.style.display = 'none';

            const lotteryType = lotterySelect.value;
            const quantity = parseInt(gameQuantitySelect.value);
            const useStats = useStatsForGenerationCheckbox.checked;

            let allGeneratedGames = [];

            if (useStats) {
                const stats = lotteryStatistics[lotteryType];
                // Verifica se há estatísticas disponíveis e se os números mais frequentes estão populados
                // Para Dia de Sorte, a geração estatística de dezenas ainda é baseada nos números mais frequentes
                if (!stats || !stats.mostFrequent || Object.keys(stats.mostFrequent).length === 0) {
                    showModal('Atenção', 'Para usar a geração estatística, por favor, popule o histórico e analise as estatísticas para esta loteria primeiro.');
                    generatedNumbersDiv.innerHTML = '<p class="text-red-500">Estatísticas não disponíveis para geração.</p>';
                    return;
                }
                
                for (let i = 0; i < quantity; i++) {
                    // Passa apenas os números mais frequentes para a função, pois os menos frequentes não serão usados diretamente
                    const game = generateNumbersBasedOnStats(lotteryType, stats.mostFrequent);
                    allGeneratedGames.push(game);
                }

            } else {
                // Geração puramente aleatória (comportamento atual)
                for (let i = 0; i < quantity; i++) {
                    const game = generatePureRandomNumbers(lotteryType);
                    allGeneratedGames.push(game);
                }
            }

            if (allGeneratedGames.length > 0) {
                currentGeneratedNumbers = allGeneratedGames;
                generatedNumbersDiv.innerHTML = '';

                currentGeneratedNumbers.forEach((game, index) => {
                    const gameContainer = document.createElement('div');
                    gameContainer.className = 'mb-2 p-2 border border-gray-200 rounded-md dark:border-gray-700';
                    gameContainer.innerHTML = `<p class="font-semibold text-gray-700 dark:text-gray-300">Jogo ${index + 1}:</p>`;
                    const numbersContainer = document.createElement('div');
                    numbersContainer.className = 'flex flex-wrap justify-center items-center gap-2';

                    // Lógica para exibir dezenas
                    let dezenasToDisplay = [];
                    let mesDaSorteToDisplay = '';
                    let trevosToDisplay = [];

                    if (lotteryType === 'maismilionaria') {
                        dezenasToDisplay = game.dezenas;
                        trevosToDisplay = game.trevos;
                    } else if (lotteryType === 'diadesorte') {
                        dezenasToDisplay = game.dezenas;
                        mesDaSorteToDisplay = game.mesDaSorte;
                    } else {
                        dezenasToDisplay = game; // Para loterias que retornam apenas um array de números
                    }

                    dezenasToDisplay.forEach(num => {
                        const bubble = document.createElement('span');
                        bubble.className = 'number-bubble';
                        bubble.textContent = num;
                        numbersContainer.appendChild(bubble);
                    });
                    
                    // Exibir trevos se for +Milionária
                    if (trevosToDisplay.length > 0) {
                        const trevosLabel = document.createElement('p');
                        trevosLabel.className = 'font-semibold text-gray-700 dark:text-gray-300 mt-2 w-full text-center';
                        trevosLabel.textContent = 'Trevos:';
                        numbersContainer.appendChild(trevosLabel);
                        trevosToDisplay.forEach(trevo => {
                            const trevoBubble = document.createElement('span');
                            trevoBubble.className = 'trevo-bubble';
                            trevoBubble.textContent = trevo;
                            numbersContainer.appendChild(trevoBubble);
                        });
                    }

                    // Exibir Mês da Sorte se for Dia de Sorte
                    if (mesDaSorteToDisplay) {
                        const monthLabel = document.createElement('p');
                        monthLabel.className = 'font-semibold text-gray-700 dark:text-gray-300 mt-2 w-full text-center';
                        monthLabel.textContent = `Mês da Sorte: ${mesDaSorteToDisplay}`;
                        numbersContainer.appendChild(monthLabel);
                    }
                    
                    gameContainer.appendChild(numbersContainer);
                    generatedNumbersDiv.appendChild(gameContainer);
                });

                saveGeneratedBtn.style.display = 'inline-flex';
            } else {
                generatedNumbersDiv.innerHTML = '<p class="text-red-500">Erro ao gerar números.</p>';
            }
        });

        saveGeneratedBtn.addEventListener('click', async () => {
            if (currentGeneratedNumbers.length === 0) {
                showModal('Atenção', 'Nenhum número gerado para salvar.');
                return;
            }

            const lotteryType = lotterySelect.value;
            const payload = {
                lotteryType: lotteryType,
                numbers: currentGeneratedNumbers
            };

            try {
                const response = await fetch(`${API_BASE_URL}?action=saveBetHistory`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                if (!response.ok) {
                    throw new Error(`Erro HTTP! status: ${response.status}`);
                }
                const result = await response.json();
                if (result.success) {
                    showModal('Sucesso', 'Números salvos no histórico!');
                    loadBettingHistory();
                    currentGeneratedNumbers = [];
                    saveGeneratedBtn.style.display = 'none';
                    generatedNumbersDiv.innerHTML = '<p class="text-gray-500 dark:text-gray-400">Números gerados aparecerão aqui.</p>';
                } else {
                    showModal('Erro', `Falha ao salvar: ${result.message || 'Erro desconhecido'}`);
                }
            } catch (error) {
                console.error('Erro ao salvar histórico:', error);
                showModal('Erro', `Não foi possível salvar o histórico. Detalhes: ${error.message}`);
            }
        });


        const currentResultBtn = document.getElementById('currentResultBtn');
        const previousResultBtn = document.getElementById('previousResultBtn');
        const resultLotterySelect = document.getElementById('resultLotterySelect');
        const lotteryResultsDiv = document.getElementById('lotteryResults');

        async function displayLotteryResults(lotteryType, type = 'current', contestNumberToFetch = null) {
            lotteryResultsDiv.innerHTML = '<p class="text-gray-500 dark:text-gray-400">Buscando resultados...</p>';
            
            const params = { lottery: lotteryType, type: type };
            if (contestNumberToFetch !== null) {
                params.contestNumber = contestNumberToFetch;
            }
            
            const data = await fetchData('getResults', params);

            console.log("Dados recebidos do backend para resultados:", data);

            if (data && data.numero) { // Verifica se 'numero' existe para garantir que é um resultado válido
                currentDisplayedContestNumber = data.numero;
                currentPreviousContestNumber = data.numeroConcursoAnterior || null; 

                let html = `
                    <h3 class="text-xl font-medium mb-2">Concurso ${data.numero || 'N/A'} - ${data.nomeLoteria || 'N/A'}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">Data do Sorteio: ${data.dataApuracao || 'N/A'}</p>
                `;
                
                // Exibição de dezenas principais
                if (lotteryType === 'duplasena' && data.listaDezenas && Array.isArray(data.listaDezenas[0])) {
                    html += '<div class="mb-4">';
                    html += `<p class="font-semibold text-gray-700 dark:text-gray-300">1º Sorteio:</p>`;
                    html += '<div class="flex flex-wrap justify-center items-center gap-2">';
                    if (data.listaDezenas[0]) { // Verifica se o 1º sorteio existe
                        data.listaDezenas[0].forEach(dezena => {
                            html += `<span class="number-bubble">${dezena}</span>`;
                        });
                    }
                    html += '</div>';
                    
                    html += `<p class="font-semibold text-gray-700 dark:text-gray-300 mt-2">2º Sorteio:</p>`;
                    html += '<div class="flex flex-wrap justify-center items-center gap-2">';
                    if (data.listaDezenas[1]) { // Verifica se o 2º sorteio existe
                        data.listaDezenas[1].forEach(dezena => {
                            html += `<span class="number-bubble">${dezena}</span>`;
                        });
                    }
                    html += '</div>';
                    html += '</div>';
                } else if (data.dezenasSorteadas && data.dezenasSorteadas.length > 0) {
                    html += '<div class="flex flex-wrap justify-center items-center gap-2 mb-4">';
                    data.dezenasSorteadas.forEach(dezena => {
                        html += `<span class="number-bubble">${dezena}</span>`;
                    });
                    html += '</div>';
                } else if (data.listaDezenas && data.listaDezenas.length > 0) {
                     // Para loterias como Megasena, Quina, Lotofacil, Lotomania, Timemania, Dia de Sorte, Supersete, +Milionária
                    html += '<div class="flex flex-wrap justify-center items-center gap-2 mb-4">';
                    data.listaDezenas.forEach(dezena => {
                        html += `<span class="number-bubble">${dezena}</span>`;
                    });
                    html += '</div>';
                } else if (!data.listaResultadoFederal && !data.listaResultadosLoteca) {
                    html += '<p class="text-gray-500 dark:text-gray-400">Nenhum número sorteado disponível ou formato inesperado.</p>';
                }


                // Exibição de informações específicas por loteria
                if (lotteryType === 'federal' && data.listaResultadoFederal && data.listaResultadoFederal.length > 0) {
                    html += '<div class="overflow-x-auto"><table class="table-auto w-full text-sm mt-4">';
                    html += '<thead><tr><th>Prêmio</th><th>Bilhete</th><th>Valor do Prêmio</th></tr></thead><tbody>';
                    data.listaResultadoFederal.forEach(premio => {
                        html += `<tr><td>${premio.premio}</td><td>${premio.bilhete}</td><td>R$ ${premio.valorDoPremio.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}</td></tr>`;
                    });
                    html += '</tbody></table></div>';
                } else if (lotteryType === 'loteca' && data.listaResultadosLoteca && data.listaResultadosLoteca.length > 0) {
                    let lotecaHtml = '<h3 class="text-xl font-medium mt-4 mb-2">Resultados da Loteca</h3>';
                    lotecaHtml += '<div class="overflow-x-auto"><table class="table-auto w-full text-sm mt-4">';
                    lotecaHtml += '<thead><tr><th>Jogo</th><th>Time 1</th><th>Placar</th><th>Time 2</th><th>Resultado</th></tr></thead><tbody>';
                    data.listaResultadosLoteca.forEach(jogo => {
                        html += `<tr>
                            <td>${jogo.numeroJogo}</td>
                            <td>${jogo.nomeTimeUm}</td>
                            <td>${jogo.golsTimeUm} x ${jogo.golsTimeDois}</td>
                            <td>${jogo.nomeTimeDois}</td>
                            <td>${jogo.colunaUm === 1 ? '1' : (jogo.colunaDois === 1 ? '2' : 'X')}</td>
                        </tr>`;
                    });
                    html += '</tbody></table></div>';
                    html += lotecaHtml;
                } else if (lotteryType === 'diadesorte' && data.nomeTimeCoracaoMesSorte) {
                    html += `<p class="mt-2 text-lg font-semibold">Mês da Sorte: ${data.nomeTimeCoracaoMesSorte}</p>`;
                } else if (lotteryType === 'timemania' && data.nomeTimeCoracaoMesSorte) {
                    html += `<p class="mt-2 text-lg font-semibold">Time do Coração: ${data.nomeTimeCoracaoMesSorte}</p>`;
                } else if (lotteryType === 'maismilionaria' && data.trevosSorteados && data.trevosSorteados.length > 0) {
                    html += `<p class="font-semibold text-gray-700 dark:text-gray-300 mt-2">Trevos Sorteados:</p>`;
                    html += '<div class="flex flex-wrap justify-center items-center gap-2">';
                    data.trevosSorteados.forEach(trevo => {
                        html += `<span class="trevo-bubble">${trevo}</span>`;
                    });
                    html += '</div>';
                }


                if (data.acumulado) {
                    html += `<p class="mt-2 text-lg font-semibold">Acumulado: ${data.acumulado ? 'Sim' : 'Não'}</p>`;
                }
                if (data.valorEstimadoProximoConcurso) {
                    html += `<p class="mt-2 text-lg font-semibold">Estimativa Próximo Concurso: R$ ${data.valorEstimadoProximoConcurso.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}</p>`;
                }
                if (data.proximoConcurso) {
                    html += `<p class="mt-2 text-lg font-semibold">Próximo Concurso: ${data.proximoConcurso}</p>`;
                }
                if (data.dataProximoConcurso) {
                    html += `<p class="mt-2 text-lg font-semibold">Data Próximo Concurso: ${data.dataProximoConcurso}</p>`;
                }

                lotteryResultsDiv.innerHTML = html;

            } else {
                currentDisplayedContestNumber = null;
                currentPreviousContestNumber = null;
                showModal('Erro', data ? (data.message || 'Não foi possível obter os resultados ou o concurso não foi encontrado.') : 'Não foi possível obter os resultados ou o concurso não foi encontrado.');
                lotteryResultsDiv.innerHTML = '<p class="text-red-500">Não foi possível obter os resultados ou o concurso não foi encontrado.</p>';
            }
        }

        currentResultBtn.addEventListener('click', () => displayLotteryResults(resultLotterySelect.value, 'current'));
        
        previousResultBtn.addEventListener('click', () => {
            const lotteryType = resultLotterySelect.value;
            if (currentPreviousContestNumber !== null && currentPreviousContestNumber > 0) {
                displayLotteryResults(lotteryType, 'specific', currentPreviousContestNumber);
            } else {
                showModal('Atenção', 'Não há concursos anteriores disponíveis para esta loteria no momento. Tente consultar o concurso atual primeiro ou um concurso anterior específico se souber o número.');
            }
        });


        const analyzeStatsBtn = document.getElementById('analyzeStatsBtn');
        const statsLotterySelect = document.getElementById('statsLotterySelect');
        const mostFrequentNumbersDiv = document.getElementById('mostFrequentNumbers');
        const leastFrequentNumbersDiv = document.getElementById('leastFrequentNumbers');
        const statsMessageDiv = document.getElementById('statsMessage');

        // Referências para as novas divs de estatísticas específicas
        const mostFrequentMonthsDiv = document.getElementById('mostFrequentMonths');
        const monthsContentDiv = document.getElementById('monthsContent');
        const mostFrequentTeamsDiv = document.getElementById('mostFrequentTeams');
        const teamsContentDiv = document.getElementById('teamsContent');
        const mostFrequentTrevosDiv = document.getElementById('mostFrequentTrevos');
        const trevosContentDiv = document.getElementById('trevosContent');
        const leastFrequentTrevosDiv = document.getElementById('leastFrequentTrevos');
        const leastTrevosContentDiv = document.getElementById('leastTrevosContent');

        // Novos containers para as seções de números mais/menos sorteados
        const mostFrequentNumbersContainer = document.getElementById('mostFrequentNumbersContainer');
        const leastFrequentNumbersContainer = document.getElementById('leastFrequentNumbersContainer');


        const populateHistoryBtn = document.getElementById('populateHistoryBtn');
        populateHistoryBtn.addEventListener('click', async () => {
            const lotteryType = statsLotterySelect.value;
            const limit = 50; 

            statsMessageDiv.innerHTML = `<p class="text-blue-500">Populando histórico com os últimos ${limit} concursos para ${lotteryType}. Isso pode levar alguns segundos...</p>`;
            mostFrequentNumbersDiv.innerHTML = '<p class="text-gray-500 dark:text-gray-400">...</p>';
            leastFrequentNumbersDiv.innerHTML = '<p class="text-gray-500 dark:text-gray-400">...</p>';
            // Esconde as seções extras enquanto popula
            mostFrequentMonthsDiv.classList.add('hidden');
            mostFrequentTeamsDiv.classList.add('hidden');
            mostFrequentTrevosDiv.classList.add('hidden');
            leastFrequentTrevosDiv.classList.add('hidden');
            // Esconde as seções de números mais/menos sorteados por padrão ao popular
            mostFrequentNumbersContainer.classList.add('hidden');
            leastFrequentNumbersContainer.classList.add('hidden');


            const data = await fetchData('populateHistoricalData', { lottery: lotteryType, limit: limit });

            if (data && data.success) {
                showModal('Sucesso', data.message);
                analyzeStatsBtn.click(); 
            } else {
                showModal('Erro', data.message || 'Erro ao popular histórico.');
                statsMessageDiv.innerHTML = `<p class="text-red-500">${data.message || 'Erro ao popular histórico.'}</p>`;
            }
        });


        analyzeStatsBtn.addEventListener('click', async () => {
            mostFrequentNumbersDiv.innerHTML = '<p class="text-gray-500 dark:text-gray-400">Analisando...</p>';
            leastFrequentNumbersDiv.innerHTML = '<p class="text-gray-500 dark:text-gray-400">Analisando...</p>';
            statsMessageDiv.innerHTML = ''; 
            // Esconde todas as seções extras por padrão
            mostFrequentMonthsDiv.classList.add('hidden');
            mostFrequentTeamsDiv.classList.add('hidden');
            mostFrequentTrevosDiv.classList.add('hidden');
            leastFrequentTrevosDiv.classList.add('hidden');
            // Esconde as seções de números mais/menos sorteados por padrão
            mostFrequentNumbersContainer.classList.add('hidden');
            leastFrequentNumbersContainer.classList.add('hidden');


            const lotteryType = statsLotterySelect.value;
            const data = await fetchData('getStatistics', { lottery: lotteryType });

            if (data && data.stats) {
                // Lógica para exibir/ocultar as seções de números mais/menos sorteados
                if (data.stats.mostFrequent && Object.keys(data.stats.mostFrequent).length > 0) {
                    let mostHtml = '';
                    for (const num in data.stats.mostFrequent) {
                        mostHtml += `<span class="number-bubble">${num}</span>`;
                    }
                    mostFrequentNumbersDiv.innerHTML = mostHtml;
                    mostFrequentNumbersContainer.classList.remove('hidden'); // Mostra o container
                } else {
                    mostFrequentNumbersDiv.innerHTML = '<p class="text-gray-500 dark:text-gray-400">Nenhum dado de números mais sorteados.</p>';
                    mostFrequentNumbersContainer.classList.add('hidden'); // Oculta o container se não houver dados
                }

                if (data.stats.leastFrequent && Object.keys(data.stats.leastFrequent).length > 0) {
                    let leastHtml = '';
                    for (const num in data.stats.leastFrequent) {
                        leastHtml += `<span class="number-bubble">${num}</span>`;
                    }
                    leastFrequentNumbersDiv.innerHTML = leastHtml;
                    leastFrequentNumbersContainer.classList.remove('hidden'); // Mostra o container
                } else {
                    leastFrequentNumbersDiv.innerHTML = '<p class="text-gray-500 dark:text-gray-400">Nenhum dado de números menos sorteados.</p>';
                    leastFrequentNumbersContainer.classList.add('hidden'); // Oculta o container se não houver dados
                }


                // Exibe estatísticas de Meses da Sorte (Dia de Sorte)
                if (lotteryType === 'diadesorte' && data.stats.mostFrequentMonths && Object.keys(data.stats.mostFrequentMonths).length > 0) {
                    let monthsHtml = '';
                    for (const month in data.stats.mostFrequentMonths) {
                        monthsHtml += `<span class="p-2 rounded-md bg-indigo-100 text-indigo-800 dark:bg-indigo-700 dark:text-indigo-100 font-semibold text-sm m-1">${month}</span>`;
                    }
                    monthsContentDiv.innerHTML = monthsHtml;
                    mostFrequentMonthsDiv.classList.remove('hidden');
                }

                // Exibe estatísticas de Times do Coração (Timemania)
                if (lotteryType === 'timemania' && data.stats.mostFrequentTeams && Object.keys(data.stats.mostFrequentTeams).length > 0) {
                    let teamsHtml = '';
                    for (const team in data.stats.mostFrequentTeams) {
                        teamsHtml += `<span class="p-2 rounded-md bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100 font-semibold text-sm m-1">${team}</span>`;
                    }
                    teamsContentDiv.innerHTML = teamsHtml;
                    mostFrequentTeamsDiv.classList.remove('hidden');
                }

                // Exibe estatísticas de Trevos (+Milionária)
                if (lotteryType === 'maismilionaria' && data.stats.mostFrequentTrevos && Object.keys(data.stats.mostFrequentTrevos).length > 0) {
                    let trevosHtml = '';
                    for (const trevo in data.stats.mostFrequentTrevos) {
                        trevosHtml += `<span class="trevo-bubble">${trevo}</span>`;
                    }
                    trevosContentDiv.innerHTML = trevosHtml;
                    mostFrequentTrevosDiv.classList.remove('hidden');
                }

                // Exibe estatísticas de Trevos Menos Sorteados (+Milionária)
                if (lotteryType === 'maismilionaria' && data.stats.leastFrequentTrevos && Object.keys(data.stats.leastFrequentTrevos).length > 0) {
                    let leastTrevosHtml = '';
                    for (const trevo in data.stats.leastFrequentTrevos) {
                        leastTrevosHtml += `<span class="trevo-bubble">${trevo}</span>`;
                    }
                    leastTrevosContentDiv.innerHTML = leastTrevosHtml;
                    leastFrequentTrevosDiv.classList.remove('hidden');
                }


                if (data.stats.message) {
                    statsMessageDiv.innerHTML = `<p>${data.stats.message}</p>`;
                }

                // Armazena as estatísticas para uso na geração de números
                lotteryStatistics[lotteryType] = data.stats;

            } else {
                mostFrequentNumbersDiv.innerHTML = '<p class="text-red-500">Erro ao analisar ou dados insuficientes.</p>';
                leastFrequentNumbersDiv.innerHTML = '<p class="text-red-500">Erro ao analisar ou dados insuficientes.</p>';
                statsMessageDiv.innerHTML = `<p class="text-red-500">${data ? (data.message || 'Não foi possível carregar as estatísticas.') : 'Não foi possível carregar as estatísticas.'}</p>`;
            }
        });

        const exportBtn = document.getElementById('exportBtn');
        const exportDataType = document.getElementById('exportDataType');
        const exportFormat = document.getElementById('exportFormat');

        exportBtn.addEventListener('click', () => {
            const dataType = exportDataType.value;
            const format = exportFormat.value;
            window.open(`${API_BASE_URL}?action=exportData&dataType=${dataType}&format=${format}`, '_blank');
        });

        const bettingHistoryDiv = document.getElementById('bettingHistory');
        const clearHistoryBtn = document.getElementById('clearHistoryBtn');

        async function loadBettingHistory() {
            bettingHistoryDiv.innerHTML = '<p class="text-gray-500 dark:text-gray-400">Carregando histórico...</p>';
            const data = await fetchData('getBetHistory');

            if (data && data.history && data.history.length > 0) {
                let html = '<table class="table-auto text-sm"><thead><tr><th>Loteria</th><th>Números Gerados</th><th>Data</th></tr></thead><tbody>';
                data.history.forEach(item => {
                    let numbersDisplay = '';
                    // Verifica se o item.generatedNumbers é um array de arrays (múltiplos jogos) ou um único jogo (array de números ou objeto)
                    const isMultiGame = Array.isArray(item.generatedNumbers) && Array.isArray(item.generatedNumbers[0]);

                    if (isMultiGame) {
                        numbersDisplay = item.generatedNumbers.map(game => {
                            if (game.dezenas && game.trevos) { // +Milionária
                                return `Dezenas: [${game.dezenas.join(', ')}] Trevos: [${game.trevos.join(', ')}]`;
                            } else if (game.dezenas && game.mesDaSorte) { // Dia de Sorte
                                return `Dezenas: [${game.dezenas.join(', ')}] Mês da Sorte: ${game.mesDaSorte}`;
                            }
                            return `[${game.join(', ')}]`; // Outras loterias
                        }).join('<br>');
                    } else {
                        // Caso seja um único jogo, que pode ser um array ou um objeto (Dia de Sorte, +Milionária)
                        if (item.generatedNumbers.dezenas && item.generatedNumbers.trevos) { // +Milionária
                            numbersDisplay = `Dezenas: [${item.generatedNumbers.dezenas.join(', ')}] Trevos: [${item.generatedNumbers.trevos.join(', ')}]`;
                        } else if (item.generatedNumbers.dezenas && item.generatedNumbers.mesDaSorte) { // Dia de Sorte
                            numbersDisplay = `Dezenas: [${item.generatedNumbers.dezenas.join(', ')}] Mês da Sorte: ${item.generatedNumbers.mesDaSorte}`;
                        } else if (Array.isArray(item.generatedNumbers)) { // Outras loterias (array simples)
                            numbersDisplay = `[${item.generatedNumbers.join(', ')}]`;
                        } else {
                            numbersDisplay = 'Formato de números desconhecido.';
                        }
                    }

                    html += `<tr>
                        <td>${item.lotteryType.charAt(0).toUpperCase() + item.lotteryType.slice(1)}</td>
                        <td>${numbersDisplay}</td>
                        <td>${new Date(item.generationDate).toLocaleString('pt-BR')}</td>
                    </tr>`;
                });
                html += '</tbody></table>';
                bettingHistoryDiv.innerHTML = html;
            } else {
                bettingHistoryDiv.innerHTML = '<p class="text-gray-500 dark:text-gray-400">Nenhum histórico de apostas encontrado.</p>';
            }
        }

        clearHistoryBtn.addEventListener('click', async () => {
            showModal('Confirmar Limpeza', 'Tem certeza que deseja limpar todo o histórico de apostas?', true, async () => {
                const data = await fetchData('clearBetHistory');
                if (data && data.success) {
                    showModal('Sucesso', 'Histórico de apostas limpo!');
                    loadBettingHistory();
                } else {
                    showModal('Erro', `Falha ao limpar histórico: ${data.message || 'Erro desconhecido'}`);
                }
            });
        });

        document.addEventListener('DOMContentLoaded', loadBettingHistory);


        const runAiAnalysisBtn = document.getElementById('runAiAnalysisBtn');
        const aiLotterySelect = document.getElementById('aiLotterySelect');
        const aiAnalysisResultsDiv = document.getElementById('aiAnalysisResults');

        runAiAnalysisBtn.addEventListener('click', async () => {
            aiAnalysisResultsDiv.innerHTML = '<p class="text-gray-500 dark:text-gray-400">Executando análise de IA...</p>';
            const lotteryType = aiLotterySelect.value;
            const data = await fetchData('getAIAnalysis', { lottery: lotteryType });

            if (data && data.analysis) {
                let html = '<h3 class="text-xl font-medium mb-2">Resultados da Análise de IA:</h3>';
                if (data.analysis.aprioriRules && data.analysis.aprioriRules.length > 0) {
                    html += '<p class="font-semibold">Regras de Associação (Apriori):</p><ul class="list-disc list-inside">';
                    data.analysis.aprioriRules.forEach(rule => {
                        html += `<li>${rule}</li>`;
                    });
                    html += '</ul>';
                } else {
                    html += '<p class="text-gray-500 dark:text-gray-400">Nenhuma regra de associação encontrada ou dados insuficientes.</p>';
                }
                html += `<p class="mt-4 text-sm text-gray-600 dark:text-gray-300">${data.analysis.message || ''}</p>`;
                aiAnalysisResultsDiv.innerHTML = html;
            } else {
                showModal('Erro', data.message || 'Erro ao executar a análise de IA ou dados insuficientes.');
                aiAnalysisResultsDiv.innerHTML = '<p class="text-red-500">Erro ao executar a análise de IA ou dados insuficientes.</p>';
            }
        });

    </script>
</body>
</html>
<?php

include('../footer.php');

?>