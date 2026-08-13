# 🚚 FreteFácil

O **FreteFácil** é uma plataforma web desenvolvida para conectar empresas que precisam realizar entregas a proprietários de veículos utilitários (PCUs).

A plataforma permite que empresas publiquem fretes e acompanhem seus pagamentos, enquanto os motoristas podem encontrar oportunidades, gerenciar seus fretes e acompanhar seus ganhos através da carteira.

---

# 📋 Funcionalidades

## 🏢 Empresa

* Cadastro de empresa;
* Login;
* Publicação de fretes;
* Visualização dos fretes publicados;
* Acompanhamento do status dos fretes;
* Gerenciamento dos fretes;
* Realização de pagamentos.

## 🚚 Motorista / PCU

* Cadastro de motorista;
* Login;
* Visualização de fretes disponíveis;
* Aceitação de fretes;
* Acompanhamento dos fretes;
* Carteira digital;
* Visualização de saldo;
* Histórico de movimentações;
* Acompanhamento de pagamentos.

---

# 🛠️ Tecnologias utilizadas

* **PHP 8.2+**
* **MySQL / MariaDB**
* **PDO**
* **HTML5**
* **CSS3**
* **JavaScript**
* **Bootstrap**
* **Git / GitHub**

---

# 🏗️ Estrutura do projeto

O projeto utiliza uma arquitetura baseada em **MVC**, com a utilização de **DAOs (Data Access Objects)** para separar o acesso ao banco de dados.

```text
fretefacil/
│
├── config/
│   └── Config.php
│
├── controllers/
│   ├── EmpresaController.php
│   ├── FreteController.php
│   ├── PagamentoController.php
│   └── ...
│
├── models/
│   ├── Empresa.php
│   ├── EmpresaDAO.php
│   ├── Frete.php
│   ├── FreteDAO.php
│   ├── Carteira.php
│   ├── CarteiraDAO.php
│   └── ...
│
├── views/
│   ├── dashboard-empresa.php
│   ├── dashboard-moto.php
│   ├── login.php
│   └── ...
│
├── assets/
│   ├── css/
│   ├── js/
│   └── imagens/
│
├── database/
│   ├── 01_estrutura.sql
│   ├── 02_dados_teste.sql
│   └── README.md
│
├── .env.example
├── .gitignore
├── index.php
└── README.md
```

> O arquivo `.env` é criado localmente e não é enviado para o GitHub, pois contém as configurações de acesso ao banco de dados.

---

# 🧩 Arquitetura

## Models

Representam as entidades utilizadas pelo sistema, como:

* Empresa;
* PCU;
* Frete;
* Pagamento;
* Carteira;
* Usuário.

---

## DAOs

DAO significa **Data Access Object**.

Essa camada é responsável pelas operações de acesso ao banco de dados, concentrando os comandos SQL.

Por exemplo:

```text
FreteDAO
```

é responsável pelas operações relacionadas aos fretes no banco de dados.

---

## Controllers

Os Controllers controlam o fluxo da aplicação e fazem a comunicação entre as Views e os DAOs.

Fluxo básico:

```text
View
  ↓
Controller
  ↓
DAO
  ↓
Banco de Dados
```

---

## Views

São as interfaces utilizadas pelos usuários.

Elas são responsáveis pela apresentação das informações e pela interação com o sistema.

---

# 🗄️ Banco de dados

O FreteFácil utiliza **MySQL/MariaDB**.

O projeto possui scripts SQL dentro da pasta:

```text
database/
```

Esses scripts permitem que o banco de dados seja reproduzido localmente, sem depender do banco online utilizado durante o desenvolvimento.

A pasta contém:

```text
database/
├── 01_estrutura.sql
├── 02_dados_teste.sql
└── README.md
```

### `01_estrutura.sql`

Cria o banco de dados, tabelas, relacionamentos, chaves estrangeiras e demais estruturas necessárias para o funcionamento do sistema.

### `02_dados_teste.sql`

Insere dados fictícios para facilitar os testes do sistema.

Os dados de teste incluem:

* Empresa;
* Motorista/PCU;
* Carteira;
* Frete disponível.

> Os dados presentes nesse arquivo são fictícios e destinados apenas a testes e demonstrações.

---

# 💻 Executando o projeto localmente

A execução local permite que qualquer pessoa baixe o projeto e utilize seu próprio banco de dados MySQL/MariaDB.

Dessa forma, **não é necessário possuir a senha do banco online do projeto**.

O funcionamento será:

```text
Projeto no GitHub
       ↓
Clone do projeto
       ↓
Scripts SQL
       ↓
MySQL/MariaDB local
       ↓
Arquivo .env
       ↓
FreteFácil
```

---

# 📦 1. Clonar o projeto

No terminal:

```bash
git clone URL_DO_REPOSITORIO
```

Entre na pasta:

```bash
cd fretefacil
```

---

# ⚙️ 2. Instalar os requisitos

Para executar o projeto localmente, é necessário possuir:

* PHP 8.2 ou superior;
* MySQL ou MariaDB;
* Git;
* Navegador;
* Extensão `pdo_mysql` habilitada no PHP.

Para verificar o PHP:

```bash
php -v
```

Para verificar o PDO MySQL:

```bash
php -m
```

Procure por:

```text
PDO
pdo_mysql
```

---

# 🗄️ 3. Criar o banco de dados local

Instale e inicie o **MySQL ou MariaDB**.

Depois, abra o MySQL Workbench, phpMyAdmin ou outro cliente MySQL.

Execute primeiro o arquivo:

```text
database/01_estrutura.sql
```

Esse arquivo irá criar o banco:

```text
fretefacil_db
```

e suas respectivas tabelas.

Depois execute:

```text
database/02_dados_teste.sql
```

Esse segundo script irá inserir dados fictícios para testes.

---

# 🔑 4. Configurar o `.env`

Na raiz do projeto existe o arquivo:

```text
.env.example
```

Faça uma cópia dele e renomeie para:

```text
.env
```

A estrutura ficará:

```text
.env
.env.example
```

O `.env` deve apontar para o banco **local**:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=fretefacil_db
DB_USER=root
DB_PASS=
```

### Caso o MySQL possua senha

Se o usuário `root` possuir uma senha, informe-a:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=fretefacil_db
DB_USER=root
DB_PASS=sua_senha
```

> As configurações do `.env` dependem da instalação local do MySQL/MariaDB.

---

# ▶️ 5. Executar o projeto

Abra o terminal dentro da pasta do projeto:

```bash
cd fretefacil
```

Execute:

```bash
php -S localhost:8000
```

O servidor será iniciado.

Abra o navegador e acesse:

```text
http://localhost:8000
```

O FreteFácil estará sendo executado localmente.

---

# 🧪 6. Dados para teste

O script `02_dados_teste.sql` cria usuários fictícios para facilitar a demonstração.

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

> Esses dados são exclusivamente para testes locais.

---

# 🌐 Banco online

Durante o desenvolvimento, o FreteFácil também pode utilizar um banco MySQL/MariaDB hospedado online.

Nesse caso, o `.env` deve conter as credenciais do banco online:

```env
DB_HOST=seu_host
DB_PORT=3306
DB_NAME=seu_banco
DB_USER=seu_usuario
DB_PASS=sua_senha
```

O banco online **não é necessário para executar o projeto localmente**, pois os scripts da pasta `database/` permitem criar uma cópia local do banco.

Essa abordagem também evita a necessidade de compartilhar as credenciais do banco online com outras pessoas.

---

# 🌐 Utilizando XAMPP

Também é possível executar o projeto utilizando o Apache do XAMPP.

Copie a pasta do projeto para:

```text
C:\xampp\htdocs\
```

Por exemplo:

```text
C:\xampp\htdocs\fretefacil
```

Inicie o **Apache** e o **MySQL** no XAMPP.

Como a execução local utiliza o banco criado pelos scripts SQL, nesse caso o **MySQL do XAMPP deve estar iniciado**.

Configure o `.env`:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=fretefacil_db
DB_USER=root
DB_PASS=
```

Depois acesse:

```text
http://localhost/fretefacil/
```

---

# 🔄 Fluxo do sistema

O funcionamento geral segue:

```text
Usuário
   ↓
View
   ↓
Controller
   ↓
DAO
   ↓
Banco de Dados
```

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
tb_frete
```

No pagamento:

```text
Empresa
   ↓
PagamentoController
   ↓
PagamentoDAO
   ↓
tb_pagamento
   ↓
CarteiraDAO
   ↓
tb_carteira
```

---

# 💳 Pagamentos e carteira

O sistema possui um mecanismo de pagamentos integrado à carteira do motorista.

Após o processamento do pagamento, as informações podem ser utilizadas para atualizar:

* Registro do pagamento;
* Saldo da carteira;
* Movimentações financeiras.

A carteira permite ao motorista acompanhar seus ganhos e movimentações realizadas dentro da plataforma.

---

# 🔒 Segurança

As credenciais do banco são armazenadas através de variáveis de ambiente.

O projeto utiliza:

```text
.env
.env.example
.gitignore
```

O arquivo `.env` contém informações sensíveis e **não deve ser enviado ao GitHub**.

O arquivo `.env.example` contém apenas o modelo das variáveis necessárias:

```env
DB_HOST=seu_host
DB_PORT=3306
DB_NAME=seu_banco
DB_USER=seu_usuario
DB_PASS=sua_senha
```

O `.gitignore` impede que o `.env` seja versionado.

Os scripts SQL disponíveis no repositório também **não devem conter senhas ou credenciais reais**.

---

# 🧪 Verificando a instalação

Após iniciar o sistema, verifique:

* [ ] O projeto abre no navegador;
* [ ] O MySQL/MariaDB está em execução;
* [ ] O banco `fretefacil_db` foi criado;
* [ ] As tabelas foram criadas;
* [ ] Os dados de teste foram inseridos;
* [ ] O arquivo `.env` está configurado;
* [ ] A conexão com o banco funciona;
* [ ] O login funciona;
* [ ] O cadastro funciona;
* [ ] Os dashboards carregam corretamente;
* [ ] Os fretes podem ser consultados;
* [ ] Os pagamentos funcionam;
* [ ] A carteira consegue consultar os dados.

---

# ❗ Problemas comuns

## `could not find driver`

Verifique se a extensão:

```text
pdo_mysql
```

está habilitada.

Execute:

```bash
php -m
```

Procure por:

```text
PDO
pdo_mysql
```

Caso não apareça, habilite a extensão `pdo_mysql` no `php.ini`.

---

## `Access denied for user`

Verifique as informações do `.env`:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=fretefacil_db
DB_USER=root
DB_PASS=
```

Confirme também se o MySQL/MariaDB está em execução.

---

## `Unknown database`

Significa que o banco ainda não foi criado.

Execute:

```text
database/01_estrutura.sql
```

---

## `Table doesn't exist`

Execute novamente:

```text
database/01_estrutura.sql
```

e depois:

```text
database/02_dados_teste.sql
```

---

## `Not Found`

Verifique:

* se o servidor PHP está executando;
* se você está na pasta correta do projeto;
* se a URL utilizada corresponde à porta configurada.

---

## Erro de conexão com o banco

Verifique:

* se o MySQL/MariaDB está iniciado;
* host;
* porta;
* nome do banco;
* usuário;
* senha;
* arquivo `.env`;
* extensão `pdo_mysql`.

---

# 👨‍💻 Desenvolvimento

O FreteFácil foi desenvolvido utilizando PHP e uma arquitetura organizada em camadas.

A separação entre **Models, DAOs, Controllers e Views** facilita:

* manutenção;
* organização do código;
* reutilização;
* trabalho em equipe;
* identificação de erros;
* implementação de novas funcionalidades.

---

# 🎓 Projeto acadêmico

Projeto desenvolvido para fins acadêmicos no curso de **Desenvolvimento de Sistemas – SENAI**.

---

## 📄 Licença

Projeto desenvolvido para fins educacionais e acadêmicos.
