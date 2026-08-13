# 🚚 FreteFácil

O **FreteFácil** é uma plataforma web desenvolvida para conectar empresas que precisam realizar entregas a proprietários de veículos utilitários (PCUs).

A plataforma permite que empresas publiquem fretes e acompanhem seus pagamentos, enquanto os motoristas podem encontrar oportunidades, gerenciar seus fretes e acompanhar seus ganhos através da carteira.

---

## 📋 Funcionalidades

### 🏢 Empresa

* Cadastro de empresa;
* Login;
* Publicação de fretes;
* Visualização dos fretes publicados;
* Acompanhamento do status dos fretes;
* Gerenciamento dos fretes;
* Realização de pagamentos.

### 🚚 Motorista / PCU

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
├── .env
├── .gitignore
├── index.php
└── README.md
```

---

# 🧩 Arquitetura

### Models

Representam as entidades utilizadas pelo sistema, como:

* Empresa;
* PCU;
* Frete;
* Pagamento;
* Carteira;
* Usuário.

---

### DAOs

DAO significa **Data Access Object**.

Essa camada é responsável pelas operações de acesso ao banco de dados, concentrando os comandos SQL.

Por exemplo:

```text
FreteDAO
```

é responsável pelas operações relacionadas aos fretes no banco de dados.

---

### Controllers

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

### Views

São as interfaces utilizadas pelos usuários.

Elas são responsáveis pela apresentação das informações e interação com o sistema.

---

# 🗄️ Banco de dados

O FreteFácil utiliza um banco de dados **MySQL/MariaDB hospedado online**.

Portanto, **não é necessário criar ou importar o banco de dados localmente** para executar o projeto.

O sistema utiliza as tabelas do banco online através da conexão configurada no projeto.

---

# 🔐 Configuração das variáveis de ambiente

As informações de conexão com o banco de dados são armazenadas em um arquivo `.env`.

Exemplo:

```env
DB_HOST=seu_host
DB_NAME=seu_banco
DB_USER=seu_usuario
DB_PASSWORD=sua_senha
DB_PORT=3306
```

> Os valores acima são apenas exemplos. Utilize as credenciais fornecidas para o banco de dados do projeto.

O arquivo `.env` contém informações sensíveis e, por isso, **não deve ser enviado ao GitHub**.

O arquivo `.env` está incluído no `.gitignore`.

Para configurar o projeto pela primeira vez, crie um arquivo `.env` na raiz do projeto e informe as credenciais corretas do banco online.

---

# 📦 Como baixar o projeto

## 1. Clonar o repositório

No terminal:

```bash
git clone URL_DO_REPOSITORIO
```

Entre na pasta do projeto:

```bash
cd fretefacil
```

---

# ⚙️ Configurar o ambiente

Antes de executar o sistema, é necessário ter instalado:

* PHP;
* Git;
* Acesso à internet para conexão com o banco de dados online.

Também é necessário possuir a extensão **PDO MySQL** habilitada no PHP.

Para verificar:

```bash
php -m
```

Procure por:

```text
PDO
pdo_mysql
```

---

# 🔑 Configurar o `.env`

Após baixar o projeto, crie o arquivo:

```text
.env
```

na raiz do projeto.

Exemplo:

```env
DB_HOST=seu_host
DB_NAME=seu_banco
DB_USER=seu_usuario
DB_PASSWORD=sua_senha
DB_PORT=3306
```

As informações devem corresponder ao banco de dados online utilizado pelo projeto.

---

# ▶️ Executando o sistema

Com o PHP instalado e o `.env` configurado, abra o terminal na pasta do projeto e execute:

```bash
php -S localhost:8000
```

O servidor PHP será iniciado localmente.

Depois, abra o navegador e acesse:

```text
http://localhost:8000
```

---

# 🌐 Utilizando XAMPP

Também é possível executar o projeto utilizando o Apache do XAMPP.

Coloque a pasta do projeto dentro de:

```text
C:\xampp\htdocs\
```

Por exemplo:

```text
C:\xampp\htdocs\fretefacil
```

Inicie o **Apache** no XAMPP.

Como o banco de dados utilizado pelo sistema é online, **não é necessário iniciar o MySQL do XAMPP** para acessar o banco do projeto.

Depois acesse:

```text
http://localhost/fretefacil/
```

Caso o Apache esteja configurado para utilizar outra porta:

```text
http://localhost:8080/fretefacil/
```

---

# 🔄 Fluxo do sistema

O funcionamento geral segue a estrutura:

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

Informações sensíveis, como credenciais do banco de dados, são armazenadas através de variáveis de ambiente.

O projeto utiliza:

```text
.env
.gitignore
```

O `.env` não deve ser enviado ao repositório.

O `.gitignore` é utilizado para impedir que arquivos sensíveis ou arquivos desnecessários sejam versionados.

---

# 🧪 Verificando a instalação

Após iniciar o sistema, verifique:

* [ ] O projeto abre no navegador;
* [ ] A conexão com o banco online funciona;
* [ ] O login funciona;
* [ ] O cadastro funciona;
* [ ] Os dashboards carregam corretamente;
* [ ] Os fretes podem ser consultados;
* [ ] Os pagamentos funcionam;
* [ ] A carteira consegue consultar os dados.

---

# ❗ Problemas comuns

### `could not find driver`

Verifique se o PHP possui o driver:

```text
pdo_mysql
```

Execute:

```bash
php -m
```

Caso não apareça, habilite a extensão `pdo_mysql` no `php.ini`.

---

### `Access denied for user`

Verifique as credenciais presentes no `.env`:

```env
DB_HOST=
DB_NAME=
DB_USER=
DB_PASSWORD=
DB_PORT=
```

---

### `Not Found`

Verifique se:

* o servidor PHP está executando;
* você está na pasta correta do projeto;
* a URL utilizada corresponde à porta do servidor.

---

### Erro de conexão com o banco

Verifique:

* conexão com a internet;
* disponibilidade do banco online;
* credenciais do `.env`;
* host e porta do banco;
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
