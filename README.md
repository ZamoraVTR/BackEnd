# DESAFIO BACKEND

## Configuração do Ambiente

### Requisitos
- _PHP >= 8.0_ e [extensões](https://www.php.net/manual/pt_BR/extensions.php) (**não esquecer de instalar as seguintes extensões: _pdo_, _pdo_sqlite_ e _sqlite3_**);
- _SQLite_;
- _Composer_.

### Instalação
- Instalar dependências pelo composer com `composer install` na raiz do projeto;
- Servir a pasta _public_ do projeto através de algum servidor.
  (_Sugestão [PHP Built in Server](https://www.php.net/manual/en/features.commandline.webserver.)_)

## Sobre o Projeto

- O cliente XPTO Ltda. contratou seu serviço para realizar alguns ajustes em seu sistema de cadastro de produtos;
- O sistema permite o cadastro, edição e remoção de _produtos_ e _categorias de produtos_ para uma _empresa_;
- Para que sejam possíveis os cadastros, alterações e remoções é necessário um usuário administrador;
- O sistema possui categorias padrão que pertencem a todas as empresas, bem como categorias personalizadas dedicadas a uma dada empresa. As categorias padrão são: (`clothing`, `phone`, `computer` e `house`) e **devem** aparecer para todas as _empresas_;
- O sistema tem um relatório de dados dedicado ao cliente.

## Sobre a API
As rotas estão divididas em:
  -  _CRUD_ de _categorias_;
  - _CRUD_ de _produtos_;
  - Rota de busca de um _relatório_ que retorna um _html_.

**Atenção**, é bem importante que se adicione o _header_ `admin_user_id` com o id do usuário desejado ao acessar as rotas para simular o uso de um usuário no sistema.

A documentação da API se encontra na pasta `docs/api-docs.pdf`
  - A documentação assume que a url base é `localhost:8000` mas você pode usar qualquer outra url ao configurar o servidor;
  - O _header_ `admin_user_id` na documentação está indicado com valor `1` mas pode ser usado o id de qualquer outro usuário caso deseje (_pesquisando no banco de dados é possível ver os outros id's de usuários_).
  
Caso opte por usar o [Insomnia](https://insomnia.rest/) o arquivo para importação se encontra em `docs/insomnia-api.json`.
Caso opte por usar o [Postman](https://www.postman.com/) o arquivo para importação se encontra em `docs/postman-api.json`.

## Sobre o Banco de Dados
- O banco de dados é um _sqlite_ simples e já vem com dados preenchidos por padrão no projeto;
- O banco tem um arquivo de backup em `db/db-backup.sqlite` com o estado inicial do projeto caso precise ser "resetado".

## Demandas
Abaixo, as solicitações do cliente:

### Categorias
- [ ] A categoria está vindo errada na listagem de produtos para alguns casos
  (_exemplo: produto `blue trouser` está vindo na categoria `phone` e deveria ser `clothing`_);
- [ ] Alguns produtos estão vindo com a categoria `null` ao serem pesquisados individualmente (_exemplo: produto `iphone 8`_);
- [ ] Cadastrei o produto `king size bed` em mais de uma categoria, mas ele aparece **apenas** na categoria `furniture` na busca individual do produto.

### Filtros e Ordenamento
Para a listagem de produtos:
- [ ] Gostaria de poder filtrar os produtos ativos e inativos;
- [ ] Gostaria de poder filtrar os produtos por categoria;
- [ ] Gostaria de poder ordenar os produtos por data de cadastro.

### Relatório
- [ ] O relatório não está mostrando a coluna de logs corretamente, se possível, gostaria de trazer no seguinte formato:
  (Nome do usuário, Tipo de alteração e Data),
  (Nome do usuário, Tipo de alteração e Data),
  (Nome do usuário, Tipo de alteração e Data)
  Exemplo:
  (John Doe, Criação, 01/12/2023 12:50:30),
  (Jane Doe, Atualização, 11/12/2023 13:51:40),
  (Joe Doe, Remoção, 21/12/2023 14:52:50)

### Logs
- [ ] Gostaria de saber qual usuário mudou o preço do produto `iphone 8` por último.

### Extra
- [ ] Aqui fica um desafio extra **opcional**: _criar um ambiente com_ Docker _para a api_.

**Seu trabalho é atender às 7 demandas solicitadas pelo cliente.**

Caso julgue necessário, podem ser adicionadas ou modificadas as rotas da api. Caso altere, por favor, explique o porquê e indique as alterações nesse `README`.

Sinta-se a vontade para refatorar o que achar pertinente, considerando questões como arquitetura, padrões de código, padrões restful, _segurança_ e quaisquer outras boas práticas. Levaremos em conta essas mudanças.

Boa sorte! :)

## Suas Respostas, Duvidas e Observações
[Adicione  aqui suas respostas, dúvidas e observações]

## Demandas -
-Usei o insomnia , para fazer e executar os testes das demandas exigidas no teste.

-Fiz uma pequena alteração no arquivo insomnia-api.json, para classificar as categorias , para que eu pudesse criar o filtro de busca exigido no teste.

-No arquivo "routes.php" adicionei a rota "/last-price-change" , para gerar o log de alteraçoes de preços , que pode ser acessada "http://localhost:8000/last-price-change",
lembre-se de adicionar o id do produto que deseja buscar o log de alteração , ex: "http://localhost:8000/last-price-change/4".

-Acessei o banco de dados fornecido para o teste através do "DB Browser for SQLite" para atender a demanda "Relatório", alterando o nome da coluna "timestamp" para "created_at", e também
adicionei a coluna "user_name".

-Adicionei o framework PHPUnit que pode ser instalado através do comando "composer require --dev phpunit/phpunit", 
para criar alguns testes para os arquivos "ProductService" e "ReportService". 
A execução dos testes pode ser feita através do comando "./vendor/bin/phpunit test/ReportServiceTest.php test/ProductServiceTest.php"

-Também criei o arquivo "ReportService.php" para que as consultas , nao ficassem expostas, no controlador "ReportController",procurei também manter o padrão em inglês do projeto com as variáveis e funções, as demais alterações no projeto, adicionei comentários, para que possa ser entendido o que as funções e requisições fazem.

-O arquivo composer.json foi atualizado para adicionar as dependências necessárias do Slim Framework e do PHPUnit.

## Desafio extra -
-Adicionei arquivos de configuração e inicialização do ambiente Docker para facilitar a execução do projeto em diferentes ambientes.

-Execução do Projeto com o Docker

  Certifique-se de ter o Docker instalado em sua máquina.

  No terminal, navegue até o diretório raiz do projeto onde se encontra o arquivo Dockerfile.

  Execute o seguinte comando para construir a imagem Docker:

  docker build -t nome_da_imagem .

  Após a construção da imagem, execute o seguinte comando para iniciar o contêiner Docker e executar o servidor embutido do PHP:

  docker run -p 8000:8000 nome_da_imagem

  O servidor estará disponível em http://localhost:8000

  # Observações --
   Infelizmente fazendo os testes no insomnia , através do servidor subido pelo docker , encontrei alguns "erros" ao alterar o valor do Header "admin_user_id" de 1 , para 2 , 3 ou 4 ,
   Subindo o projeto direto pelo servidor imbutido do PHP "php -S localhost:8000 -t public" o projeto esta 100% funcional , cumprindo todas as exigências, não sobrou tempo de entrega do projeto para que eu pudesse debugar e corrigir o problema ao subir com o docker , optei por entregar com o Dockerfile , mesmo com erro , porque acho que é melhor do que entregar sem a minha tentativa!

   Desde já , agradeço pela oportunidade , espero que esteja cumprindo todas as necessidades exigidas! Desejo um ótimo dia para quem estiver lendo isso !
   Abraços !!