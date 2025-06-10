# Projeto de Pedido de Viagem (Backend)

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
