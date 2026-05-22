
## Requisitos

- Docker version 29.3.0
- Node v23.6.1
- Npm 10.9.2

## Instalação e passo a passo para rodar o projeto

Clone o repositório:

```bash
git clone https://github.com/rafPH1998/fintech-gastos-challenge.git
```

Entre na pasta do projeto:

```bash
cd fintech-gastos-challenge
```

Acesse o projeto:

```bash
code .
```

Crie o Arquivo .env:

```bash
cp .env.example .env
```

Ajuste o **`.env`** OBS: (Por ser um projeto de teste, abaixo esta as credencias do env):

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=fintech
DB_USERNAME=root
DB_PASSWORD=root
```

Suba os containers do projeto:

```bash
docker compose up -d
```

Caso gere um erro de permissao, é importante que o ID seja o mesmo que está definido no Dockerfile:

![alt text](image.png)


Acessar o container:

```bash
docker compose exec app bash
```

Instale as dependências PHP:

```bash
composer install
```

Configure o ambiente:

```bash
php artisan key:generate
```

Rode as migrations com o seed de demonstração:

```bash
php artisan migrate:fresh --seed
```

O seed cria usuário demo

## Acessar projeto

```bash
http://localhost:8001/login
```

### Login de demonstração

| Campo   | Valor                 |
|---------|-----------------------|
| E-mail  | `user1@teste.local` |
| Senha   | `senha123`            |


## Estrutura principal

- **Rotas:** `routes/web.php`
- **Controllers:** `app/Http/Controllers/`
- **Services (regras de negócio):** `app/Services/` — classes `*Service`
- **Models:** `app/Models/`
- **Views:** `resources/pages/`
