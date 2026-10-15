# 🚚 FreteFácil

O **FreteFácil** é uma plataforma web desenvolvida para conectar empresas que precisam realizar entregas a proprietários de veículos utilitários (PCUs).

A plataforma permite que empresas publiquem fretes e acompanhem seus pagamentos, enquanto os motoristas podem encontrar oportunidades, gerenciar seus fretes e acompanhar seus ganhos por meio da carteira digital.

## 📋 Funcionalidades

### 🏢 Empresa

- Cadastro e login;
- Publicação de fretes;
- Visualização e gerenciamento dos fretes publicados;
- Acompanhamento do status dos fretes;
- Realização de pagamentos.

### 🚚 Motorista / PCU

- Cadastro e login;
- Visualização de fretes disponíveis;
- Aceitação e acompanhamento de fretes;
- Carteira digital;
- Consulta de saldo e histórico de movimentações;
- Acompanhamento de pagamentos.

## 🛠️ Tecnologias utilizadas

- **PHP 8.4 + Apache:** execução da aplicação web no ambiente Docker;
- **MySQL 8.0:** gerenciamento do banco de dados;
- **PDO:** conexão entre PHP e banco de dados;
- **HTML5, CSS3 e JavaScript:** desenvolvimento da interface;
- **Bootstrap:** componentes e estilização;
- **Docker:** criação e execução dos containers;
- **Git e GitHub:** versionamento do código.

## 🏗️ Estrutura do projeto

O projeto utiliza uma arquitetura organizada em camadas, baseada em MVC, com DAOs para separar as operações de acesso ao banco de dados.

```text
fretefacil/
├── components/
├── config/
├── controllers/
├── database/
│   ├── 01_estrutura.sql
│   ├── 02_dados_teste.sql
│   └── README.md
├── docs/
├── models/
├── static/
├── views/
├── .dockerignore
├── .env
├── .env.example
├── .gitignore
├── compose.yaml
├── dockerfile-apache
├── dockerfile-mysql
├── index.php
├── logout.php
├── parar-ambiente.bat
├── README.md
├── remover-ambiente.bat
└── start.bat
```

> O arquivo `.env` é criado localmente e não deve ser enviado ao GitHub. O arquivo `.env.example` serve como modelo das variáveis de ambiente.

### Arquivos Docker e scripts

- `dockerfile-apache`: define a imagem da aplicação PHP com Apache.
- `dockerfile-mysql`: define a imagem MySQL personalizada e inclui os scripts SQL de inicialização.
- `start.bat`: verifica e cria os recursos necessários, inicia os containers e abre a aplicação.
- `parar-ambiente.bat`: para os containers sem removê-los.
- `remover-ambiente.bat`: remove os containers, as imagens do projeto e a rede, preservando o volume de dados.
- `compose.yaml`: arquivo de configuração Docker Compose disponível no projeto. As instruções abaixo utilizam o `start.bat`.

## 🧩 Arquitetura

### Models

Representam as entidades utilizadas pelo sistema, como empresa, PCU, frete, pagamento, carteira e usuário.

### DAOs

DAO significa *Data Access Object*. Essa camada concentra as operações de acesso ao banco de dados e os comandos SQL relacionados às entidades.

### Controllers

Controlam o fluxo da aplicação e fazem a comunicação entre as interfaces e as operações de acesso aos dados.

### Views

São as interfaces utilizadas pelos usuários, responsáveis pela apresentação das informações e pela interação com o sistema.

### Fluxo básico

```text
Usuário
   ↓
View
   ↓
Controller
   ↓
DAO
   ↓
Banco de dados
```

## 🗄️ Banco de dados

O FreteFácil utiliza MySQL. Os scripts SQL ficam na pasta `database/`.

### `01_estrutura.sql`

Cria a estrutura do banco, incluindo as tabelas e os relacionamentos necessários para o funcionamento do sistema.

### `02_dados_teste.sql`

Insere dados fictícios para facilitar os testes e as demonstrações do sistema. Os registros podem incluir empresas, motoristas/PCUs, carteiras e fretes.

> Os dados de teste são destinados exclusivamente a demonstrações e testes locais.

---

# 🐳 Executando o projeto com Docker

Esta é a forma recomendada de executar o FreteFácil neste repositório. Com o Docker, o PHP, o Apache e o MySQL são executados em containers, reduzindo a necessidade de instalar e configurar esses componentes manualmente no computador.

## 1. Requisitos

Antes de começar, tenha instalado:

- [Docker Desktop](https://www.docker.com/products/docker-desktop/);
- [Git](https://git-scm.com/);
- Um navegador web.

O Docker Desktop precisa estar aberto e em execução.

## 2. Clonar o repositório

Abra o terminal e execute:

```bash
git clone https://github.com/gustavoamomm/fretefacil.git
cd fretefacil
```
GitHub. Se você já possui o projeto no computador, entre diretamente na pasta do repositório.

## 3. Configurar o arquivo `.env`

Na raiz do projeto existe o arquivo `.env.example`, que contém o modelo das variáveis de ambiente.

No PowerShell, crie uma cópia:

```powershell
Copy-Item .env.example .env
```

Se o arquivo `.env` já estiver configurado, não é necessário criá-lo novamente.

Abra o `.env` e configure as variáveis. Exemplo:

```dotenv
# Configurações utilizadas pelo PHP
DB_HOST=db
DB_PORT=3306
DB_NAME=fretefacil_db
DB_USER=fretefacil
DB_PASS=troque_esta_senha
DB_ROOT_PASS=troque_esta_senha_root

# Configurações utilizadas pelo container MySQL
MYSQL_DATABASE=fretefacil_db
MYSQL_USER=fretefacil
MYSQL_PASSWORD=troque_esta_senha
MYSQL_ROOT_PASSWORD=troque_esta_senha_root
```

Substitua os valores de exemplo por senhas definidas para o ambiente local.

**Importante:**

- `DB_HOST=db` é o endereço do banco de dados dentro da rede Docker do projeto.
- `DB_PORT=3306` é a porta interna do MySQL.
- `DB_PASS` deve corresponder a `MYSQL_PASSWORD`.
- `MYSQL_DATABASE` define o banco utilizado pela aplicação.
- `MYSQL_ROOT_PASSWORD` define a senha do usuário administrativo do MySQL.
- Não envie seu arquivo `.env` com credenciais reais ao GitHub.

## 4. Iniciar o ambiente

Com o Docker Desktop aberto, execute na raiz do projeto:

```powershell
.\start.bat
```

O script verifica o Docker e o arquivo `.env`, cria a rede e o volume quando necessário, constrói as imagens que ainda não existem, inicia o banco de dados, aguarda o MySQL ficar pronto e inicia a aplicação PHP com Apache.

Ao final, acesse:

**Aplicação web:** http://localhost:8080

O script também tenta abrir o endereço automaticamente no navegador.

### Containers utilizados

| Container | Função |
|---|---|
| `fretefacil_app` | Executa a aplicação PHP com Apache |
| `fretefacil_db` | Executa o banco de dados MySQL |

### Imagens Docker

| Imagem | Função |
|---|---|
| `fretefacil-php-apache:1.0` | Ambiente PHP e Apache |
| `fretefacil-mysql:8.0` | Ambiente MySQL personalizado com os scripts de inicialização |

### Rede e volume

- **Rede:** `fretefacil_net`, utilizada para a comunicação entre os containers.
- **Volume:** `fretefacil_db_data`, utilizado para preservar os dados do banco.

O volume permite que os dados continuem disponíveis mesmo quando os containers são parados ou removidos.

## 5. Acessar o banco pelo MySQL Workbench

A porta padrão utilizada para acessar o MySQL pelo computador é **3306**, mapeada para a porta `3306` do container.

Utilize os seguintes dados de conexão:

| Campo | Valor |
|---|---|
| Hostname | `127.0.0.1` |
| Port | `3306` |
| Username | `fretefacil` |
| Password | O valor de `MYSQL_PASSWORD` no `.env` |
| Schema | `fretefacil_db` |

**Possível conflito de porta:** se outro serviço MySQL já estiver utilizando a porta `3306` no computador, o container poderá não iniciar com esse mapeamento. Nesse caso, encerre o serviço que está ocupando a porta ou altere a porta publicada no comando `docker run` do `start.bat`. A porta interna do MySQL continua sendo `3306`.

## 6. Parar o ambiente

Para interromper os containers sem excluir as imagens, os containers ou os dados do banco, execute:

```powershell
.\parar-ambiente.bat
```

Para iniciar novamente, execute:

```powershell
.\start.bat
```

## 7. Remover o ambiente

Para remover os containers, as imagens do projeto e a rede Docker, execute:

```powershell
.\remover-ambiente.bat
```

O script solicita confirmação antes de continuar.

**Atenção:** esse procedimento preserva o volume `fretefacil_db_data`. Portanto, os dados do banco permanecem armazenados e podem ser reutilizados quando o ambiente for iniciado novamente.

O script `start.bat` reconstrói as imagens que estiverem ausentes e recria os containers quando necessário.

## 8. Verificar os containers

Para verificar os containers que estão em execução:

```bash
docker ps
```

Para consultar os logs do banco:

```bash
docker logs fretefacil_db
```

Para consultar os logs da aplicação:

```bash
docker logs fretefacil_app
```

---

# 🧪 Dados para teste

O script `database/02_dados_teste.sql` disponibiliza usuários fictícios para facilitar a demonstração do sistema.

### Empresa

```text
CNPJ: 12345678000199
Senha: 12345678
```

### Motorista / PCU

```text
CPF: 12345678901
Senha: 12345678
```

Essas credenciais são destinadas exclusivamente aos testes locais e dependem de os dados correspondentes terem sido inseridos no banco.

---

# 💻 Executando sem Docker (alternativa)

Também é possível executar o projeto utilizando PHP e MySQL instalados diretamente no computador. Essa opção exige configurar manualmente o ambiente e o banco de dados.

## 1. Requisitos

- PHP 8.2 ou superior;
- MySQL ou MariaDB;
- Git;
- Navegador;
- Extensão `pdo_mysql` habilitada no PHP.

Para verificar a instalação do PHP:

```bash
php -v
```

Para verificar as extensões disponíveis:

```bash
php -m
```

Procure por `PDO` e `pdo_mysql`.

## 2. Criar o banco de dados

Inicie o MySQL ou MariaDB e abra o MySQL Workbench, phpMyAdmin ou outro cliente compatível.

Execute primeiro:

```text
database/01_estrutura.sql
```

Depois execute:

```text
database/02_dados_teste.sql
```

Esses scripts criam a estrutura do banco e inserem os dados de teste.

## 3. Configurar o `.env`

Para a execução direta no computador, configure o arquivo `.env` de acordo com a instalação local do MySQL. Por exemplo:

```dotenv
DB_HOST=localhost
DB_PORT=3306
DB_NAME=fretefacil_db
DB_USER=root
DB_PASS=sua_senha
```

Se o usuário `root` não possuir senha, o campo `DB_PASS` poderá ficar vazio, desde que isso corresponda à configuração do seu MySQL.

**Não utilize essas configurações locais no ambiente Docker.** No Docker, o host da aplicação deve continuar como `db`.

## 4. Executar o projeto

Na pasta do projeto, execute:

```bash
php -S localhost:8000
```

Acesse http://localhost:8000.

O funcionamento dessa alternativa depende de o servidor PHP e o banco de dados local estarem configurados corretamente.

---

# 🔄 Fluxo do sistema

O funcionamento geral do FreteFácil segue a comunicação entre as interfaces, os Controllers, os DAOs e o banco de dados.

Por exemplo, ao publicar um frete:

```text
Empresa
   ↓
Tela de publicação
   ↓
FreteController
   ↓
FreteDAO
   ↓
Banco de dados
```

No processamento de um pagamento, as informações podem ser utilizadas para atualizar o registro do pagamento, o saldo da carteira e as movimentações financeiras.

## 💳 Pagamentos e carteira

O sistema possui funcionalidades relacionadas a pagamentos e à carteira do motorista. A carteira permite acompanhar o saldo, os ganhos e as movimentações registradas na plataforma.

---

# 🔒 Segurança

As credenciais de conexão com o banco devem ser configuradas por meio de variáveis de ambiente.

- `.env`: contém as configurações locais e possíveis credenciais;
- `.env.example`: apresenta um modelo das variáveis necessárias;
- `.gitignore`: impede que o arquivo `.env` seja versionado.

**O arquivo `.env` não deve ser enviado ao GitHub.** O arquivo `.env.example` deve utilizar valores de exemplo, sem senhas reais.

Os scripts SQL também não devem conter credenciais reais. As contas de teste devem ser utilizadas somente em ambientes de desenvolvimento e demonstração.

---

# 🧰 Problemas comuns

## Docker Desktop não está em execução

Abra o Docker Desktop e aguarde até que o mecanismo Docker esteja pronto. Depois execute novamente:

```powershell
.\start.bat
```

## Arquivo `.env` não encontrado

Na raiz do projeto, copie o arquivo de exemplo:

```powershell
Copy-Item .env.example .env
```

Depois configure as variáveis necessárias.

## Porta 8080 já está em uso

Outro programa pode estar utilizando a porta publicada para a aplicação. Nesse caso, libere a porta ou ajuste o mapeamento da aplicação no comando `docker run` do `start.bat`.

## Porta 3306 já está em uso

Verifique se outro serviço está utilizando essa porta. Se possível, encerre o serviço conflitante. Como alternativa, altere a porta publicada no comando `docker run` do container MySQL e utilize a nova porta no MySQL Workbench. A porta interna do MySQL permanece `3306`.

## Erro de conexão com o banco

Verifique:

- Se o container `fretefacil_db` está em execução;
- Se o container da aplicação está na rede `fretefacil_net`;
- Se `DB_HOST=db` está configurado no `.env` para o Docker;
- Se o banco, o usuário e as senhas estão configurados corretamente;
- Se `DB_PASS` corresponde a `MYSQL_PASSWORD`.

**Atenção:** depois que o MySQL inicializa um volume vazio pela primeira vez, alterar as variáveis de senha no `.env` não altera automaticamente as credenciais já existentes no banco.

## Erro `could not find driver`

Esse erro pode indicar que o PHP não possui o driver PDO adequado para o banco de dados. No ambiente Docker deste projeto, verifique se a imagem PHP foi construída com a extensão `pdo_mysql`.

## Erro `Unknown database`

Verifique se o banco `fretefacil_db` foi criado e se o nome configurado no `.env` corresponde ao banco utilizado pela aplicação.

No Docker, confira os logs do container MySQL e a execução dos scripts de inicialização.

## Erro `Table doesn't exist`

Verifique se as tabelas foram criadas corretamente.

Os scripts SQL de inicialização são executados automaticamente quando o MySQL inicializa um volume vazio. Se o volume já contém um banco inicializado, os scripts não são executados novamente apenas porque o container foi recriado.

## Página `Not Found`

Verifique:

- Se o container `fretefacil_app` está em execução;
- Se o endereço acessado está correto;
- Se a porta publicada corresponde à configuração do container;
- Se os arquivos da aplicação estão presentes na imagem.

---

# 🧪 Checklist de verificação

Após iniciar o ambiente, confira:

- [ ] O Docker Desktop está em execução;
- [ ] O arquivo `.env` está configurado;
- [ ] Os containers foram iniciados;
- [ ] A aplicação abre em `http://localhost:8080`;
- [ ] O banco `fretefacil_db` está acessível;
- [ ] As tabelas foram criadas;
- [ ] Os dados de teste estão disponíveis;
- [ ] O login funciona;
- [ ] O cadastro funciona;
- [ ] Os dashboards carregam corretamente;
- [ ] Os fretes podem ser consultados;
- [ ] Os pagamentos e a carteira podem ser testados;
- [ ] Os dados continuam disponíveis após parar e iniciar os containers.

---

# 👨‍💻 Desenvolvimento

O FreteFácil foi desenvolvido utilizando PHP e uma arquitetura organizada em camadas.

A separação entre Models, DAOs, Controllers e Views facilita a manutenção, a organização do código, a reutilização, o trabalho em equipe e a implementação de novas funcionalidades.

O uso do Docker ajuda a padronizar o ambiente de desenvolvimento entre diferentes computadores.

# 🎓 Projeto acadêmico

Projeto desenvolvido para fins acadêmicos no curso de **Desenvolvimento de Sistemas — SENAI**.

## 📄 Licença

Projeto desenvolvido para fins educacionais e acadêmicos.
