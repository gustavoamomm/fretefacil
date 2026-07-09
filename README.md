# 🚚 Frete Fácil

Plataforma web que conecta **empresas** que precisam enviar cargas a **motoristas/proprietários de utilitários** disponíveis para realizar o transporte, com postagem de fretes, aceite em tempo real, carteira digital, pagamentos e cálculo de rota.

## Índice

- [Sobre o projeto](#sobre-o-projeto)
- [Funcionalidades](#funcionalidades)
- [Tecnologias](#tecnologias)
- [Arquitetura](#arquitetura)
- [Estrutura de pastas](#estrutura-de-pastas)
- [Banco de dados](#banco-de-dados)
- [Como rodar o projeto](#como-rodar-o-projeto)
- [Configuração de APIs externas](#configuração-de-apis-externas)
- [Segurança — pontos de atenção](#segurança--pontos-de-atenção)
- [Documentação](#documentação)

## Sobre o projeto

O Frete Fácil é um sistema de intermediação de fretes com dois perfis de usuário:

- **Empresa**: cadastra-se com CNPJ, publica fretes (origem, destino, peso, data/hora de coleta, valor, tipo de veículo necessário) e realiza o pagamento quando o frete é concluído.
- **Motorista**: cadastra-se com CPF, CNH, tipo de veículo e placa, visualiza os fretes disponíveis, aceita os que desejar e recebe o valor em uma carteira digital, podendo solicitar saque via PIX.

## Funcionalidades

- Cadastro e login separados por tipo de usuário (empresa / motorista), com senha protegida via `password_hash`/`password_verify`.
- Recuperação de senha.
- Publicação, edição e exclusão de fretes pela empresa.
- Listagem de fretes disponíveis e aceite pelo motorista.
- Cálculo de rota e geocodificação de endereços via [OpenRouteService](https://openrouteservice.org/), exibidos em mapa interativo com [Leaflet](https://leafletjs.com/).
- Simulação/registro de pagamento do frete (dashboard da empresa).
- Carteira digital do motorista (saldo) e solicitação de saque via chave PIX.
- Dashboards distintos para empresa (`dashboard-emp.php`) e motorista (`dashboard-moto.php`), com seções carregadas via AJAX.

## Tecnologias

- **Back-end**: PHP puro (sem framework), PDO para acesso ao MySQL/MariaDB.
- **Front-end**: HTML, CSS e JavaScript vanilla (`static/style.css`, `static/script.js`, `static/pagamento-simulacao.js`).
- **Banco de dados**: MySQL/MariaDB.
- **APIs externas**: OpenRouteService (geocodificação e cálculo de rotas) e Leaflet (renderização de mapas via CDN).
- **Padrão de projeto**: MVC simplificado, com camada de Model/DAO separada dos Controllers e das Views.

## Arquitetura

O projeto segue uma organização inspirada em MVC:

- **`models/`** — Entidades (`Frete`, `Empresa`, `Motorista`, `Pagamento`, `Carteira`) e suas classes DAO (`FreteDAO`, `EmpresaDAO`, `MotoristaDAO`, `PagamentoDAO`, `CarteiraDAO`), responsáveis pelo acesso ao banco via PDO.
- **`controllers/`** — Recebem requisições (POST/GET), validam dados e orquestram as chamadas aos Models. Também concentra a rota de API (`api.php`) usada para geocodificação e cálculo de rota.
- **`views/`** — Páginas exibidas ao usuário (login, cadastro, dashboards) e subpastas `ajax/` com fragmentos carregados dinamicamente pelos dashboards.
- **`components/`** — Cabeçalho e rodapé reutilizados pelas páginas.
- **`config/`** — Classe `Config` (Singleton) que fornece a conexão PDO com o banco.

## Estrutura de pastas

```
fretefacil/
├── index.php                      # Landing page
├── logout.php
├── components/
│   ├── header.php
│   └── footer.php
├── config/
│   └── config.php                 # Conexão PDO (Singleton)
├── controllers/
│   ├── api.php                    # Rota de geocodificação/rota (OpenRouteService)
│   ├── CadastroController.php     # Roteia cadastro para Empresa ou Motorista
│   ├── EmpresaController.php
│   ├── MotoristaController.php
│   ├── LoginController.php
│   ├── RecuperarSenhaController.php
│   ├── FreteController.php
│   ├── PagamentoController.php
│   ├── CarteiraController.php
│   ├── SaqueController.php
│   └── ssl/cacert.pem             # Bundle de CA para validação SSL do cURL
├── models/
│   ├── Empresa.php / EmpresaDAO.php
│   ├── Motorista.php / MotoristaDAO.php
│   ├── Frete.php / FreteDAO.php
│   ├── Pagamento.php / PagamentoDAO.php
│   └── Carteira.php / CarteiraDAO.php
├── views/
│   ├── login.php
│   ├── cadastro.php
│   ├── esqueci-senha.php
│   ├── dashboard-emp.php
│   ├── dashboard-moto.php
│   └── ajax/
│       ├── fretes-empresa.php
│       ├── fretes-disponiveis.php
│       ├── fretes-aceitos.php
│       ├── pagamentos.php
│       └── carteira-motorista.php
├── static/
│   ├── style.css
│   ├── script.js
│   └── pagamento-simulacao.js
└── docs/
    ├── Documentação de Software - Plataforma Frete Fácil.pdf
    ├── Testes - Frete Fácil.pdf
    └── apresentação.txt
```

## Banco de dados

O sistema espera um banco MySQL/MariaDB com (ao menos) as seguintes tabelas, inferidas do código das DAOs:

| Tabela | Descrição |
|---|---|
| `tb_usuario_empresa` | Dados da empresa (nome, cnpj, senha com hash) |
| `tb_usuario_pcu` | Dados do motorista/proprietário de utilitário (nome, dt_nasc, cpf, cnh, tipo_veiculo, placa, senha com hash) |
| `tb_frete` | Fretes publicados (origem, uf_origem, destino, uf_destino, peso, valor, data_coleta, hora_coleta, descricao, empresa_id, carro_necessario, status, pcu_id, data_pagamento) |
| `tb_carteira` | Saldo do motorista (saldo, pcu_id) |
| `tb_pagamento` | Pagamentos vinculados a um frete (frete_id, empresa_id, usuario_pcu_id, valor, metodo, status, data) |

> Não há um arquivo `.sql` de schema no projeto — as tabelas precisam ser criadas manualmente com base nos campos utilizados nas classes `models/*DAO.php`.

## Como rodar o projeto

**Pré-requisitos**: PHP 7.4+ (recomendado 8+, há uso de tipagem de propriedade `private PDO $conn`), extensões `pdo_mysql` e `curl` habilitadas, servidor MySQL/MariaDB, Apache/Nginx ou o servidor embutido do PHP.

1. Clone o repositório e coloque a pasta em seu servidor local (ex.: `htdocs` do XAMPP) ou rode com o servidor embutido:
   ```bash
   php -S localhost:8000
   ```
2. Crie o banco de dados e as tabelas listadas em [Banco de dados](#banco-de-dados).
3. Edite `config/config.php` com as credenciais do seu banco local (host, porta, nome do banco, usuário e senha).
4. Acesse `index.php` pelo navegador, crie uma conta de empresa e uma de motorista para testar o fluxo completo (cadastro → login → publicar frete → aceitar frete → pagamento → carteira/saque).

## Configuração de APIs externas

O arquivo `controllers/api.php` consome a API do **OpenRouteService** para:
- `GET ?acao=geocode` — converter cidade/UF em coordenadas.
- `POST { acao: "rota" }` — calcular a rota entre origem e destino.

Para uso em produção, gere sua própria chave gratuita em [openrouteservice.org](https://openrouteservice.org/) e substitua a chave definida na variável `$ORS_KEY` em `controllers/api.php`.

## Segurança — pontos de atenção

Alguns pontos que valem revisão antes de colocar o projeto em produção:

- **Credenciais em código**: a senha do banco (`config/config.php`) e a chave da API OpenRouteService (`controllers/api.php`) estão hardcoded no código-fonte. O ideal é movê-las para variáveis de ambiente (ex.: arquivo `.env` fora do controle de versão) e adicionar esse arquivo ao `.gitignore`.
- **Detalhe de erro exposto**: em `api.php`, o `register_shutdown_function` inclui o campo `"detalhe"` com a mensagem de erro do PHP na resposta JSON — há um comentário no próprio código indicando que isso deve ser removido em produção.
- **Senhas**: cadastro e login já usam `password_hash`/`password_verify`, o que é o correto.

## Documentação

O projeto inclui documentação adicional na pasta `docs/`:
- Documento de especificação de software (PDF).
- Documento de testes (PDF).
- Link para a apresentação do projeto (Canva), em `apresentacao.txt`.