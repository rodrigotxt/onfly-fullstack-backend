# Projeto de Pedido de Viagem

Este projeto é uma aplicação web completa para gerenciamento de pedidos de viagem, composta por um backend robusto em **Laravel** e um frontend interativo construído com **Vue.js (Quasar Framework)**. O objetivo é fornecer uma solução limpa, escalável e segura para que usuários autenticados possam criar, visualizar e gerenciar suas próprias ordens de viagem.

## Sumário

1. [Requisitos Gerais](#requisitos-gerais)

2. [Como Iniciar o Projeto](#como-iniciar-o-projeto)

   * [Pré-requisitos](#pré-requisitos)

   * [Configuração Inicial](#configuração-inicial)

   * [Acessando a Aplicação](#acessando-a-aplicação)

3. [Estrutura do Projeto](#estrutura-do-projeto)

4. [Backend (Laravel)](#backend-laravel)

   * [Autenticação (JWT)](#autenticação-jwt)

   * [Relacionamento e Autorização](#relacionamento-e-autorização)

   * [Configuração do Banco de Dados](#configuração-do-banco-de-dados)

   * [Testes](#testes)

5. [Frontend (Vue.js / Quasar)](#frontend-vuejs--quasar)

6. [Comandos Úteis do `make`](#comandos-úteis-do-make)

7. [Informações Relevantes / Troubleshooting](#informações-relevantes--troubleshooting)

## Requisitos Gerais

* **Docker**: Utilização de Docker e Docker Compose para orquestração do ambiente de desenvolvimento.

* **Estrutura Limpa**: Boas práticas de organização de código tanto no Laravel quanto no Vue.js.

* **Testes Unitários**: Testes para as funcionalidades críticas do backend.

* **Autenticação Simples**: Implementação de autenticação via tokens (ex: JWT) para a API.

* **Autorização por Usuário**: Relacionamento `User <-> Travel Order`, garantindo que cada usuário possa gerenciar apenas suas próprias ordens.

* **Documentação**: Este `README.md` detalhado.

## Como Iniciar o Projeto

Siga os passos abaixo para configurar e executar o projeto em seu ambiente local.

### Pré-requisitos

Certifique-se de ter os seguintes softwares instalados em sua máquina:

* **Docker**: Versão mais recente.

* **Docker Compose**: Geralmente vem junto com o Docker Desktop.

* **make**: Ferramenta de automação de tarefas (disponível em sistemas Unix-like; no Windows, recomenda-se usar Git Bash ou WSL).

### Configuração Inicial

1. **Clone o repositório:**
2. **Inicialize o ambiente com Docker Compose:**
Este comando irá copiar o arquivo de ambiente de exemplo, construir as imagens Docker (se necessário), instalar as dependências do Laravel e do Vue.js, e levantar os containers.
*Se você já rodou `make init` uma vez e apenas parou os containers, pode usar `make up` para levantá-los novamente.*

### Acessando a Aplicação

Após a execução bem-sucedida do comando `make init` (ou `make up`):

* **Frontend (Aplicação Web)**: Acessível em `http://localhost:9000`

* **Backend API (API REST)**: Acessível em `http://localhost/api` ou `http://backend.local/api` (dependendo da sua configuração de hosts locais, se existir).

## Estrutura do Projeto

O projeto é modularizado em dois diretórios principais:

* `backend/`: Contém a aplicação Laravel (API REST).

* `frontend/`: Contém a aplicação Vue.js (Quasar Framework).

Cada diretório possui sua própria estrutura e dependências, gerenciadas por seus respectivos gerenciadores de pacotes (Composer para Laravel, npm/yarn para Vue.js) dentro dos containers Docker.

## Backend (Laravel)

O backend é uma API RESTful desenvolvida com Laravel, responsável pela lógica de negócios, persistência de dados e autenticação.

### Autenticação (JWT)

A API utiliza autenticação baseada em tokens JWT (JSON Web Tokens). Os usuários podem se registrar e logar para obter um token, que deve ser incluído em todas as requisições subsequentes para acessar rotas protegidas.

**Endpoints de Autenticação (Exemplos):**

* `POST /api/register`

* `POST /api/login`

* `POST /api/logout` (para invalidar o token)

### Relacionamento e Autorização

* **Relacionamento `User <-> Travel Order`**: Cada pedido de viagem (`TravelOrder`) é associado a um usuário (`User`) através de um relacionamento `belongsTo` / `hasMany`.

* **Autorização**: Uma política de autorização (Laravel Policies) garante que:

* Um usuário só pode criar novas ordens de viagem.

* Um usuário só pode visualizar suas próprias ordens de viagem.

* Um usuário só pode editar suas próprias ordens de viagem.

* Um usuário não pode visualizar ou editar ordens de outros usuários.

### Configuração do Banco de Dados

O banco de dados (geralmente MySQL ou PostgreSQL via Docker) é configurado através do arquivo `.env` no diretório `backend/`. O comando `make init` copia um `.env.example` inicial para `.env`, que você pode personalizar.

Certifique-se de que as variáveis de ambiente do Docker Compose (`docker-compose.yml`) e do Laravel (`backend/.env`) estejam alinhadas para a conexão com o banco de dados.

### Testes

Testes unitários e de funcionalidade são implementados para garantir a integridade das principais lógicas de negócio do backend.

Para rodar os testes do backend (execute dentro da pasta principal):
## Frontend (Vue.js / Quasar)

O frontend é uma aplicação Single Page Application (SPA) desenvolvida com Vue.js, utilizando o Quasar Framework para componentes UI e responsividade. Ele consome a API do backend para gerenciar os pedidos de viagem.

* **Acesso**: `http://localhost:9000`

* **Interação com API**: As requisições à API do Laravel são feitas a partir do frontend, incluindo o token de autenticação para rotas protegidas.

## Comandos Úteis do `make`

Este projeto utiliza `make` para simplificar a execução de tarefas comuns:

* `make init`: **Primeira inicialização.** Copia `.env.example`, instala dependências do backend e frontend, levanta containers e executa migrações do banco de dados.
* `make up`: Inicia os containers do Docker em modo *detached* (background). Use após a configuração inicial ou se os containers foram parados.
* `make down`: Para e remove os containers, redes e volumes associados ao projeto definidos no `docker-compose.yml`.
* `make test-backend`: Executa os testes unitários e de funcionalidade do backend Laravel.

## Informações Relevantes / Troubleshooting

* **Arquivos `.env`**: Lembre-se que cada subdiretório (`backend/` e `frontend/`) pode ter seu próprio arquivo `.env` para configurações específicas. O `make init` lida com o `.env` principal.

* **Limpeza de Cache**: Em caso de problemas no Laravel, você pode precisar limpar o cache de configuração ou de rotas dentro do container do backend:
* **Dependências**: Se você adicionar novas dependências ao `composer.json` (backend) ou `package.json` (frontend), pode ser necessário reconstruir os containers ou instalar as dependências manualmente dentro deles:

* Para o backend: `docker exec <nome_do_container_backend> composer install`

* Para o frontend: `docker exec <nome_do_container_frontend> npm install` ou `yarn install`

* **Portas Ocupadas**: Se as portas 9000 ou 80 já estiverem em uso no seu sistema, o Docker não conseguirá iniciar os containers. Verifique os processos que estão usando essas portas ou ajuste o mapeamento de portas no `docker-compose.yml`.
