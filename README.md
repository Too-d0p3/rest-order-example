# Projekt API pro Správu Objednávek Partnerů

Toto je ukázkový REST API projekt vytvořený v PHP 8.2 a Symfony frameworku. API umožňuje vytváření a správu objednávek od partnerů, včetně validace, doménové logiky a strukturovaného oddělení vrstev.

## Obsah

1. [Použité Technologie](#použité-technologie)
2. [Architektura Projektu](#architektura-projektu)
3. [Požadavky](#požadavky)
4. [Instalace a Konfigurace](#instalace-a-konfigurace)
5. [Spuštění Aplikace](#spuštění-aplikace)
6. [Spuštění Testů](#spuštění-testů)
7. [Popis Funkcionality](#popis-funkcionality)
8. [API Endpoints](#api-endpoints)
9. [Autor](#autor)

## Použité Technologie

- **PHP 8.2**
- **Symfony 6.x**
- **Doctrine ORM**
- **SQLite** (lokálně, možno vyměnit)
- **PHPUnit**
- **Docker (volitelně)**

## Architektura Projektu

Projekt je navržen dle principů DDD a clean architecture:

- **Presentation (`src/Presentation`)**  
  Obsahuje kontrolery, které přijímají HTTP požadavky a delegují je do aplikační logiky.

- **Domain (`src/Domain/Order`)**  
  Obsahuje entity, doménové DTO, repozitáře, příkazy (handlery) a výjimky. Tato vrstva je framework-agnostická.

- **Shared (`src/Shared`)**  
  Obsahuje validátory, obecné DTO rozhraní, společné výjimky a helpery.

Komunikace mezi vrstvami probíhá přes datové objekty (DTO). Validace vstupních dat je centralizovaná a rozšířitelná.

## Požadavky

- PHP 8.2+
- Composer
- Git
- SQLite nebo PostgreSQL
- Symfony CLI (doporučeno)

## Instalace a Konfigurace

```bash
git clone <REPO_URL>
cd <NAZEV_PROJEKTU>
composer install
cp .env.example .env
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

## Spuštění Aplikace

```bash
symfony server:start
# nebo
php -S localhost:8000 -t public
```

## Spuštění Testů

```bash
php bin/phpunit
```

## Popis Funkcionality

- Vytváření nové objednávky pomocí kombinace `partnerId + externalOrderId` jako unikátní identifikátor.
- Validace formátu a obsahu objednávky pomocí Symfony validatoru.
- Možnost aktualizace `deliveryDate` pro již existující objednávku.
- Správa produktů jako embedded hodnotový objekt `OrderProduct`.
- Zpracování chyb pomocí RFC 7807 (`application/problem+json`).
- Pokrytí funkcionality unit testy pro handlery a funkčními testy pro endpointy.

## API Endpoints

### 1. Vytvoření objednávky

- **POST /orders**
- **Tělo požadavku:**

```json
{
  "partnerId": "abc123",
  "externalOrderId": "ORD-2024-0001",
  "deliveryDate": "2024-07-01",
  "totalValue": 1999.99,
  "products": [
    {
      "productId": "sku-123",
      "name": "Produkt A",
      "price": 999.99,
      "quantity": 2
    }
  ]
}
```

- **Možné odpovědi:**
    - `201 Created` – úspěšné vytvoření
    - `422 Unprocessable Entity` – validační chyba
    - `409 Conflict` – objednávka již existuje
    - `400 Bad Request` – špatný formát data

---

### 2. Aktualizace data doručení

- **PUT /orders/delivery-date**
- **Tělo požadavku:**

```json
{
  "partnerId": "abc123",
  "externalOrderId": "ORD-2024-0001",
  "deliveryDate": "2024-07-10"
}
```

- **Možné odpovědi:**
    - `200 OK` – datum úspěšně změněno
    - `404 Not Found` – objednávka nenalezena
    - `400 Bad Request` – nevalidní datum

## Autor

**Ondřej Nevřela**  
🌐 [ondrejnevrela.cz](https://ondrejnevrela.cz)  
💼 [LinkedIn](https://www.linkedin.com/in/ondrej-nevrela/)
