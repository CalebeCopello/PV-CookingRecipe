# API - Cooking Recipes

Esta API permite o registro de usuários, autenticação via token, criação e exibição de receitas, avaliações e comentários.

## Tecnologias
- Laravel 12
- Sanctum para autenticação

## Instalação 

1. Clone o repositório do GitHub
```shell
git clone https://github.com/CalebeCopello/PV-CookingRecipe.git
```

2. Acesse o diretório do repositório
```shell
cd PV-CookingRecipe/
```

3. Instale as dependências do `composer`
```shell
composer install
```

4. Faça um `.env` a partir do exemplo do arquivo `.env.example`
```shell
cp .env.example .env
```

5. Configure o arquivo `.env` com as informações do banco de dados
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=PVCR
DB_USERNAME=calebe
DB_PASSWORD=123456
```

6. Rode o caching das configurações
```shell
php artisan optimize
```

7. Rode os scripts de migration
```shell
php artisan migrate
```

**Opcional**
8. Rode os testes automatizados
```shell
	php artisan migrate:refresh && php artisan test
```

**Opcional**
9. Rode as Factories para dar seed no banco de dados
```shell
php artisan migrate:refresh && artisan db:seed
```

10. Rode a API
```
php artisan serve
```
## Rotas

|Método|Endpoint|Autenticado|Descrição|
|---|---|---|---|
|POST|`/api/register`|❌|Registrar usuário|
|POST|`/api/login`|❌|Fazer login|
|DELETE|`/api/logout`|✅|Logout|
|GET|`/recipes`|✅|Listar receitas do usuário|
|GET|`/recipes/{id}`|✅|Ver receita específica do usuário|
|POST|`/recipes`|✅|Criar receita|
|PUT|`/recipes/{id}`|✅|Atualizar receita|
|DELETE|`/recipes/{id}`|✅|Deletar receita|
|POST|`/recipes/{id}/ratings`|❌|Avaliar receita|
|POST|`/recipes/{id}/comments`|❌|Comentar receita|
|GET|`/recipe/{id}`|❌|Exibir receita pública|

### Autenticação
#### POST /api/register

Rota utilizada para registro de usuário. 

**Requisitos**: `json` contendo valores `name` `email` `password`
```json
{
	"name": "name",
	"email": "a@b.com",
	"password": "123456"
}
```

**Resposta de sucesso (201)**: um `json` contando informações sobre o registro do usuário, assim como um token de autenticação
```json
{
    "user": {
        "name": "name",
        "email": "a@b.com",
        "updated_at": "2025-05-10T07:25:28.000000Z",
        "created_at": "2025-05-10T07:25:28.000000Z",
        "id": 1
    },
    "token": {
        "accessToken": {
            "name": "name",
            "abilities": [
                "*"
            ],
            "expires_at": null,
            "tokenable_id": 1,
            "tokenable_type": "App\\Models\\User",
            "updated_at": "2025-05-10T07:25:28.000000Z",
            "created_at": "2025-05-10T07:25:28.000000Z",
            "id": 1
        },
        "plainTextToken": "1|FjckgKL6azbgeE2qY2O8HT5yAAUwf5H1wC83UAF6587bfdae"
    }
}
```

**Resposta de erro (422)**: Caso algum campo não seja preenchido:

```json
{
    "message": "The name field is required. (and 1 more error)",
    "errors": {
        "name": [
            "The name field is required."
        ],
        "email": [
            "The email field is required."
        ]
    }
}
```

**Resposta de erro (409)**: Caso nome de usuário ou e-mail já esteja sendo utilizado:

```json
{
    "message": "The Name is already taken. Please choose another one"
}
```
#### POST /api/login

Rota utilizada para logar usuário. Requisitos: `json` contendo valores `email` `password`
```json
{
	"email": "a@b.com",
	"password": "123456"
}
```

**Resposta de sucesso (200)**: um `json` contando informações sobre o registro do usuário, assim como um token de autenticação
```json
{
    "user": {
        "name": "name",
        "email": "a@b.com",
        "updated_at": "2025-05-10T07:25:28.000000Z",
        "created_at": "2025-05-10T07:25:28.000000Z",
        "id": 1
    },
    "token": {
        "accessToken": {
            "name": "name",
            "abilities": [
                "*"
            ],
            "expires_at": null,
            "tokenable_id": 1,
            "tokenable_type": "App\\Models\\User",
            "updated_at": "2025-05-10T07:25:28.000000Z",
            "created_at": "2025-05-10T07:25:28.000000Z",
            "id": 1
        },
        "plainTextToken": "1|FjckgKL6azbgeE2qY2O8HT5yAAUwf5H1wC83UAF6587bfdae"
    }
}
```

**Resposta de erro (422)**: Caso algum campo não seja preenchido:

```json
{
    "message": "The email field is required.",
    "errors": {
        "email": [
            "The email field is required."
        ]
    }
}
```

**Resposta de erro (401)**: Caso email ou senha incorretos:

```json
{
    "message": "Invalid Email or Password."
}
```

#### DELETE /api/logout
Rota protegida para deslogar usuário. 

**Requisitos**: o HTTP request deve ser feito utilizando um header de `Authorization: Bearer` contando um token de autenticação

**Resposta de sucesso (200)**: um `json` resposta
```json
{
	"message": "You have logged out."
}
```


**Resposta de erro (401)**: 
```json
{
    "message": "Unauthenticated."
}
```

### Endpoints autenticados

#### GET /recipes

**Rota protegida** para fetch receitas do usuário. 

**Requisitos**: o HTTP request deve ser feito utilizando um header de `Authorization: Bearer` contando um token de autenticação

**Opcional**: `json` contendo valor `order` com opção: `asc` para ordem ascendente ou `desc` para ordem  descendente (escolha padrão)

```json
{
    "order": "asc"
}
```

**Resposta de sucesso (200)**: um array contendo informações sobre as receitas enviadas pelo usuário

```json
[
    {
        "id": 75,
        "user_id": 1,
        "title": "Bolo de Cenoura",
        "description": "Versão sem cobertura",
        "ingredients": "cenoura, ovos, farinha, açúcar",
        "instructions": "Misture e asse por 35 minutos.",
        "deleted_at": null,
        "created_at": "2025-04-05T00:00:00.000000Z",
        "updated_at": "2025-05-06T00:00:00.000000Z"
    },
]
```

**Resposta de erro (404)**: Caso o usuário não tenha nenhum receita registrada:
```json
{
    "message": "No recipes found"
}
```

#### GET /recipes/{id}

**Rota protegida** para fetch receita do usuário. 

**Requisitos**: o HTTP request deve ser feito utilizando um header de `Authorization: Bearer` contando um token de autenticação

**Resposta de sucesso (200)**: um `json` contendo informações sobre a receita enviada pelo usuário

```json
{
        "id": 75,
        "user_id": 1,
        "title": "Bolo de Cenoura",
        "description": "Versão sem cobertura",
        "ingredients": "cenoura, ovos, farinha, açúcar",
        "instructions": "Misture e asse por 35 minutos.",
        "deleted_at": null,
        "created_at": "2025-04-05T00:00:00.000000Z",
        "updated_at": "2025-05-06T00:00:00.000000Z"
},
```

**Resposta de erro (404)**: Caso o usuário não tenha a receita registrada:
```json
{
    "message": "Recipe not found"
}
```

#### POST /recipes

**Rota protegida**

**Requisitos**: o HTTP request deve ser feito utilizando um header de `Authorization: Bearer` contando um token de autenticação. Um `json` com as informações `title` `description` `ingredients` `instructions`

```json
{
    "title": "Bolo de Cenoura",
    "description": "Versão sem cobertura",
    "ingredients": "cenoura, ovos, farinha, açúcar",
    "instructions": "Misture e asse por 35 minutos."
}
```

**Resposta de sucesso (201)**: um `json` contendo informações sobre a receita enviada pelo usuário

```json
{
    "message": "Recipe created successfully.",
    "recipe": {
        "title": "Bolo de Cenoura",
        "description": "Versão sem cobertura",
        "ingredients": "cenoura, ovos, farinha, açúcar",
        "instructions": "Misture e asse por 35 minutos.",
        "user_id": 1,
        "updated_at": "2025-05-10T22:31:28.000000Z",
        "created_at": "2025-05-10T22:31:28.000000Z",
        "id": 105
    }
}
```

**Resposta de erro (422)**:  Caso algum campo não seja preenchido:
```json
{
    "message": "The ingredients field is required. (and 1 more error)",
    "errors": {
        "ingredients": [
            "The ingredients field is required."
        ],
        "instructions": [
            "The instructions field is required."
        ]
    }
}
```

#### PUT /recipes/{id}

**Rota protegida**

**Requisitos**: o HTTP request deve ser feito utilizando um header de `Authorization: Bearer` contando um token de autenticação. Um `json` com as informações `title` `description` `ingredients` `instructions`

**Resposta de sucesso (201)**: um `json` contendo informações sobre a receita enviada pelo usuário

```json
{
    "message": "Recipe updated successfully.",
    "recipe": {
        "title": "Bolo de Cenoura",
        "description": "Versão sem cobertura",
        "ingredients": "cenoura, ovos, farinha, açúcar",
        "instructions": "Misture e asse por 35 minutos.",
        "user_id": 1,
        "updated_at": "2025-05-10T22:31:28.000000Z",
        "created_at": "2025-05-10T22:31:28.000000Z",
        "id": 105
    }
}
```

**Resposta de erro (404)**: Caso o usuário não tenha a receita registrada:
```json
{
    "message": "Recipe not found"
}
```

**Resposta de erro (422)**:  Caso algum campo não seja preenchido:
```json
{
    "message": "The ingredients field is required. (and 1 more error)",
    "errors": {
        "ingredients": [
            "The ingredients field is required."
        ],
        "instructions": [
            "The instructions field is required."
        ]
    }
}
```

#### DELETE /recipes/{id}

**Rota protegida** para fetch receita do usuário. 

**Requisitos**: o HTTP request deve ser feito utilizando um header de `Authorization: Bearer` contando um token de autenticação

**Resposta de sucesso (200)**: um `json` contendo mensagem sobre a receita deletada pelo usuário
```json
{
    "message": "Recipe deleted successfully."
}
```

**Resposta de erro (404)**: Caso o usuário não tenha a receita registrada:
```json
{
    "message": "Recipe not found"
}
```


### Endpoints públicos

#### POST /recipes/{id}/ratings

Rota usada para registrar avaliações. 

**Requisitos**: `json` contendo valor `rating` de 0 ate 5.

```json
{
    "rating": 1
}
```

**Resposta de sucesso (200)**: caso registrado com sucesso, será retornado um `json` de confirmação

```json
{
    "message": "Rating stored successfully."
}
```

**Resposta de erro (422)**: Caso o seja maior que 5 ou menor que 1:
```json
{
    "message": "The rating field must not be greater than 5.",
    "errors": {
        "rating": [
            "The rating field must not be greater than 5."
        ]
    }
}
```

**Resposta de erro (404)**:  caso o id da receita não exista.

#### POST /recipes/{id}/comments

Rota utilizada para registo comentário de usuário. 

**Requisitos**: `json` contendo os valores `author` e `content`.

```json
{
    "author": "teste",
    "content": "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla ut nunc hendrerit, elementum mauris ut, vehicula est. Mauris maximus lectus nibh, nec euismod ex scelerisque vel. Sed ultricies mattis quam, nec lacinia ante varius at. Phasellus vehicula congue sapien eget elementum. Sed ut sem elit. Vivamus luctus quis felis et luctus. Vestibulum tempor accumsan nunc, eu fringilla leo condimentum ut. "
}
```

**Resposta de sucesso (200)**: caso o valor seja registrado com sucesso ha um `json` de retorno

```json
{
    "message": "Comment created successfully."
}
```

**Resposta de erro (422)**: Caso o valor de `author` seja maior de 150 caracteres ou `content` seja maior de 700 caracteres:
```json
{
    "message": "The content field must not be greater than 700 characters.",
    "errors": {
        "content": [
            "The content field must not be greater than 700 characters."
        ]
    }
}
```

**Resposta de erro (404)**:  caso o id da receita não exista.

### Exibição de receitas

#### GET /recipe/{id}

Rota utilizada para exibir informações sobre uma receita.

**Resposta de sucesso (200)**: caso exista a receita com determinada id ha um `array` de retorno

```json
[
    {
        "id": 9,
        "title": "A quaerat labore error a.",
        "description": "Occaecati soluta magni quos.",
        "average_rating": "2.9",
        "ratings": [
            {
                "id": 47,
                "rating": 4
            },
        ],
        "comments": [
            {
                "id": 108,
                "author": "Chasity Mills",
                "comment": "Natus cum ab aut totam est aspernatur iste sed.",
                "created_at": "2025-05-11 01:59:50"
            },
        ]
    }
]
```

**Resposta de erro (404)**:  caso o id da receita não exista.

```json
{
    "message": "Recipe not found."
}
```