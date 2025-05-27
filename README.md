Sistema de Loterias Inteligente

Este projeto é um sistema web desenvolvido em PHP para auxiliar usuários na geração de números para apostas na loteria da Caixa Econômica Federal, além de oferecer consulta de resultados, análise estatística e histórico de jogos.
Visão Geral do Projeto

O Sistema de Loterias Inteligente tem como objetivo principal fornecer ferramentas interativas e baseadas em dados para apostadores. Ele se integra à API oficial da Caixa para obter resultados de concursos em tempo real e utiliza análises estatísticas para oferecer insights sobre os números.
Funcionalidades Principais:

 Geração de Números Aleatórios: Gere jogos para diversas loterias (Megasena, Quina, Lotofácil, etc.), com a opção de gerar múltiplos jogos de uma vez.

 Geração Inteligente de Números: Utilize a análise estatística dos números mais sorteados para gerar jogos com 50% de dezenas "quentes" e 50% de dezenas aleatórias.

 Consulta de Resultados: Verifique os resultados do concurso atual e navegue por concursos anteriores (requer histórico populado) para todas as loterias da Caixa.

 Análise Estatística Avançada:

  Popular automaticamente o histórico de concursos passados (últimos 50) para cada loteria.

  Exibe os números mais e menos sorteados.

  Para Dia de Sorte, exibe os Meses da Sorte mais frequentes.

  Para Timemania, exibe os Times do Coração mais frequentes.

  Para +Milionária, exibe os Trevos mais e menos sorteados.

 Histórico de Apostas: Mantenha um registro dos números que você gerou.

 Exportação de Dados: Exporte resultados e números gerados em formatos JSON, CSV (TXT) ou SQL.

 Interface Moderna: Design responsivo com opção de tema claro/escuro, adaptável para computadores, tablets e celulares.

 Análise de IA (PHP-ML): Utiliza algoritmos de Machine Learning (Apriori) para encontrar padrões de associação entre os números sorteados, oferecendo insights adicionais (lembre-se: loterias são jogos de azar).

Tecnologias Utilizadas

 Backend: PHP 8.4+

  Composer (gerenciador de dependências)

  PHP-ML (para funcionalidades de Machine Learning)

  cURL (para comunicação com APIs externas)

 Frontend:

  HTML5

  CSS (Tailwind CSS para estilização e responsividade)

  JavaScript (Vanilla JS para interatividade e requisições AJAX)

  Servidor Web: Apache2 (ou Nginx)

  Ambiente: Máquina Virtual Oracle Cloud Free Tier (ou qualquer ambiente Linux/Windows com PHP e Apache/Nginx)

Instalação e Configuração

Siga os passos abaixo para configurar e rodar o projeto em seu servidor.
Pré-requisitos

 Servidor web (Apache2 ou Nginx) instalado e configurado.

 PHP 8.4 ou superior instalado, com as extensões php-curl, php-json e php-mbstring habilitadas.

 Composer instalado globalmente.

Passos de Instalação

  Clone ou Baixe o Projeto:
    Copie todos os arquivos do projeto para o diretório raiz do seu servidor web (ex: /var/www/html/seu_projeto/ ou C:\Apache24\htdocs\seu_projeto\).

  Estrutura de Pastas:
    Certifique-se de que a estrutura de pastas seja a seguinte:

    /seu_projeto/
    ├── index.php         (Frontend HTML/JS)
    ├── api.php           (Backend PHP - principal endpoint)
    ├── lottery_functions.php (Funções de geração de números)
    ├── stats_handler.php (Funções de análise estatística)
    ├── composer.json     (Configuração do Composer)
    ├── vendor/           (Gerado pelo Composer - contém PHP-ML)
    ├── data/             (Pasta para arquivos JSON de histórico - precisa de permissão de escrita)
    └── php_error.log     (Log de erros do PHP - precisa de permissão de escrita)

Instalar Dependências do Composer:

 Abra o terminal SSH (ou prompt de comando) e navegue até o diretório raiz do seu projeto (ex: cd /var/www/html/seu_projeto/).

 Crie o arquivo composer.json se ainda não o tiver, com o seguinte conteúdo:

        {
            "name": "seu-nome/loteria-system",
            "description": "Sistema de loteria com PHP e IA",
            "type": "project",
            "minimum-stability": "dev",
            "prefer-stable": true,
            "require": {
                "php": ">=8.0",
                "php-ai/php-ml": "^0.11"
            },
            "autoload": {
                "psr-4": {
                    "App\\": "src/"
                }
            }
        }

 Execute o Composer para instalar as bibliotecas:

        composer install

 Se você tiver problemas com permissões, tente com sudo: sudo composer install. Se o Composer reclamar do minimum-stability, certifique-se de que as linhas "minimum-stability": "dev" e "prefer-stable": true estão no seu composer.json.

 Configurar Permissões de Pasta:
    O servidor web precisa de permissão de escrita na pasta data/ para salvar os históricos e no arquivo php_error.log.

    sudo chmod -R 777 /var/www/html/seu_projeto/data/
    sudo chmod 666 /var/www/html/seu_projeto/php_error.log

  (Ajuste /var/www/html/seu_projeto/ para o caminho real do seu projeto).

 Configuração do Apache2 (Exemplo):
    Certifique-se de que o DocumentRoot do seu Virtual Host ou da configuração padrão do Apache aponte para o diretório raiz do seu projeto.
    Exemplo de Virtual Host (em /etc/apache2/sites-available/000-default.conf ou similar):

    <VirtualHost *:80>
        ServerAdmin webmaster@localhost
        DocumentRoot /var/www/html/seu_projeto/

        <Directory /var/www/html/seu_projeto/>
            Options Indexes FollowSymLinks
            AllowOverride All
            Require all granted
        </Directory>

        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined
    </VirtualHost>

Após alterar, recarregue o Apache: sudo systemctl reload apache2.

Como Usar

  Acesse a Aplicação:
    Abra seu navegador e acesse a URL do seu servidor (ex: http://seu_ip_ou_dominio/).

  Popular Histórico (Essencial para Estatísticas e IA):

  Vá para a seção "Análise Estatística".

  Selecione a loteria desejada no dropdown.

  Clique no botão "Popular Histórico (Últimos 50)". Isso buscará os últimos 50 concursos da API da Caixa e os salvará localmente. Este passo é crucial para que as análises estatísticas e de IA tenham dados para trabalhar.

  Gerar Números Aleatórios:

  Selecione a loteria e a quantidade de jogos.

  Para usar a geração baseada em estatísticas (50% mais sorteados, 50% aleatórios), marque a caixa correspondente. Lembre-se de popular o histórico primeiro.

  Clique em "Gerar Números".

  Você pode salvar os jogos gerados no "Histórico de Apostas".

Consultar Resultados:

  Selecione a loteria.

  Clique em "Concurso Atual" para ver o último resultado.

  Clique em "Concurso Anterior" para navegar pelos resultados passados (requer que você tenha consultado o "Concurso Atual" para popular o histórico ou que o histórico já tenha sido populado).

Análise Estatística:

  Selecione a loteria.

  Certifique-se de ter populado o histórico para a loteria selecionada.

  Clique em "Analisar Estatísticas" para ver os números mais/menos sorteados, meses da sorte ou times do coração (dependendo da loteria).

Análise de IA (PHP-ML):

  Selecione a loteria.

  Certifique-se de ter populado o histórico para a loteria selecionada.

  Clique em "Rodar Análise de IA" para ver as regras de associação (padrões de números que saem juntos).

Histórico de Apostas:

  Revise os jogos que você gerou e salvou.

  Use o botão "Limpar Histórico" para remover todos os jogos salvos.

Exportar Dados:

  Selecione o tipo de dado (Números Gerados, Resultados da Loteria, Estatísticas) e o formato (JSON, SQL, TXT/CSV).

  Clique em "Exportar" para baixar o arquivo.

Solução de Problemas Comuns

  Erro HTTP 500:

  Verifique o php_error.log no diretório do seu projeto para mensagens de erro detalhadas.

  Certifique-se de que todas as permissões de arquivo/pasta estão corretas (especialmente data/ e php_error.log).

  Confirme que o Composer e a biblioteca PHP-ML estão instalados corretamente.

  JSON.parse: unexpected character ou unexpected end of data:

Isso geralmente significa que o PHP está imprimindo algo (um aviso, erro ou espaço em branco) antes do JSON. Verifique se não há espaços em branco antes da tag <?php ou depois da tag ?> em qualquer um dos seus arquivos PHP.

  "Composer autoload.php não encontrado":

  Navegue até o diretório raiz do seu projeto no terminal e execute composer install.

  "Erro ao obter resultados da API da Caixa. Código HTTP: 404":

A API da Caixa pode ter alterado suas URLs ou a forma como os concursos são acessados. Verifique o php_error.log para a URL exata que está sendo tentada e tente acessá-la diretamente no navegador para confirmar.

Desenvolvido por: jeGodoy
