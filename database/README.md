# Banco de dados - FreteFácil

Esta pasta contém os scripts necessários para reproduzir o banco do FreteFácil localmente.

## Arquivos

- `01_estrutura.sql` — cria o banco e as tabelas.
- `02_dados_teste.sql` — insere dados fictícios para testes.

## Uso local

1. Instale MySQL ou MariaDB.
2. Execute `01_estrutura.sql`.
3. Execute `02_dados_teste.sql`.
4. Crie o arquivo `.env` na raiz do projeto.
5. Para banco local, use por exemplo:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=fretefacil_db
DB_USER=root
DB_PASS=
```

Se o seu MySQL local possuir senha para o usuário `root`, informe-a em `DB_PASS`.

## Usuários de teste

O script de dados cria:

- Empresa: CNPJ `12345678000199`
- Motorista: CPF `12345678901`
- Senha de ambos: `12345678`

Os dados são fictícios e servem apenas para demonstração.

## Observação

Os scripts foram montados de acordo com as tabelas e consultas utilizadas pelo código atual do projeto. Antes de usar em produção, valide a estrutura contra o banco online oficial do projeto.
