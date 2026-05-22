# Controle de Gastos Pessoais — Fintech Challenge

MVP de controle financeiro pessoal com **Laravel 12**, **Vue.js 3**, **Tailwind CSS** e autenticação via **Laravel Sanctum**.

## Funcionalidades

- Registro, login e logout com token Sanctum
- CRUD de categorias de gasto (nome único por usuário)
- CRUD de despesas com validações de negócio
- Dashboard com total do mês, últimas 5 despesas e resumo por categoria

## Pré-requisitos

- Docker 29+
- Node.js 23+
- NPM 10+

## Como rodar localmente (Docker)

```bash
git clone https://github.com/rafPH1998/fintech-gastos-challenge.git
cd fintech-gastos-challenge
cp .env.example .env
```

Configure o `.env` para MySQL (Docker):

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=fintech
DB_USERNAME=root
DB_PASSWORD=root
```

Suba os containers:

```bash
docker compose up -d
```

Entre no container da aplicação:

```bash
docker compose exec app bash
```

Dentro do container:

```bash
composer install
php artisan key:generate
php artisan migrate:fresh --seed
```

Na máquina host, instale dependências front e gere o build:

```bash
npm install
npm run build
```

Para desenvolvimento com hot reload:

```bash
npm run dev
```

Acesse: [http://localhost:8001/login](http://localhost:8001/login)

## Credenciais de teste (seed)

| Campo  | Valor                 |
|--------|-----------------------|
| E-mail | `user1@teste.local` |
| Senha  | `senha123`            |

## API (rotas principais)

| Método | Rota | Descrição |
|--------|------|-----------|
| POST | `/api/registrar` | Cadastro |
| POST | `/api/entrar` | Login |
| POST | `/api/sair` | Logout (autenticado) |
| GET | `/api/dashboard` | Resumo do mês |
| CRUD | `/api/categorias` | Categorias |
| CRUD | `/api/despesas` | Despesas |

Envie o header `Authorization: Bearer {token}` nas rotas protegidas.

## Testes automatizados

```bash
docker compose exec app php artisan test
```

Cobertura das regras de negócio:

- Valor da despesa deve ser maior que zero
- Data não pode ser futura em mais de 1 dia
- Categoria deve pertencer ao usuário autenticado
- Nome da categoria único por usuário

## Estrutura do projeto

- **Backend:** `app/Http/Controllers/`, `app/Http/Requests/`, `app/Models/`
- **Frontend:** `resources/js/pages/`, `resources/js/servicos/api.js`
- **Rotas API:** `routes/api.php`
- **Rotas SPA:** `routes/web.php`

## Deploy

_(Em breve — será configurado na etapa final do desafio.)_
