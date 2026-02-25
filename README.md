# Wasteland Dashboard (Symfony + Vue)

A lightweight GM “notebook” for managing locations, NPCs, roles, skills, knowledge, and world secrets.
Built to demonstrate Symfony + Doctrine domain modelling (explicit relationships, pivot entities, embeddables) with a small Vue dashboard consuming a JSON API.

## Key ideas

- **Notebook, not an engine**: narrative notes (Knowledge) are separate from canon plot points (WorldSecrets).
- **Explicit Doctrine modelling**: `NpcSkill` is a first-class pivot entity (skill + level), and `SpecialStats` is an embedded value object.
- **Backend-first UI**: Symfony Forms + Twig for CRUD pages; Vue is used for the dashboard list/filter UX.

## Prerequisites

- Docker + Docker Compose

## Tech Stack

- Symfony, Doctrine ORM, Twig, Symfony Forms, PHPUnit
- Vue 3 for dashboard
- Docker Compose
- MySQL

## Running with Docker

1. Build and start containers:

```bash
docker compose up -d --build
```

2. Install PHP dependencies (first run):

```bash
docker compose exec npc-store-runtime composer install
```

3. Run database migrations:

```bash
docker compose exec npc-store-runtime php bin/console doctrine:migrations:migrate --no-interaction
```

4. (Optional) Load fixtures:

```bash
docker compose exec npc-store-runtime php bin/console doctrine:fixtures:load --no-interaction
```

5. Open the app:

- Frontend dashboard: `http://localhost:8080/`

### How to reset

```bash
docker compose down -v
```

## Frontend

- Main dashboard: `/`
- Entity detail pages:
  - `/locations/{id}`
  - `/npcs/{id}`
  - `/roles/{id}`
  - `/worldsecrets/{id}`
- Create/update forms:
  - `/locations/new`, `/locations/{id}/update`
  - `/npcs/new`, `/npcs/{id}/update`
  - `/roles/new`, `/roles/{id}/update`
  - `/worldsecrets/new`, `/worldsecrets/{id}/update`

## Core Entities and Relationships

- `Location`
  - Fields: `name`, `defence`, `food`, `morale`, `standing`
  - Relationships:
    - One `Location` has many `Npc`

- `Role`
  - Fields: `name`, `description`
  - Relationships:
    - One `Role` has many `Npc`

- `Npc`
  - Fields: `name`, `notes`, `createdAt`, `updatedAt`
  - Embedded value object:
    - `SpecialStats` (`strength`, `perception`, `endurance`, `charisma`, `intelligence`, `agility`, `luck`)
  - Relationships:
    - Many `Npc` belong to one `Role` (optional)
    - Many `Npc` belong to one `Location` (optional)
    - One `Npc` has many `NpcSkill` entities
    - One `Npc` has many `Knowledge` entities

- `Skill`
  - Fields: `name`, `description`
  - Relationships:
    - One `Skill` has many `NpcSkill` entities

- `NpcSkill` (pivot/catalog assignment)
  - Fields: `level`
  - Relationships:
    - Many `NpcSkill` belong to one `Npc`
    - Many `NpcSkill` belong to one `Skill`

- `Knowledge`
  - Fields: `title`, `description`, `category`
  - Relationships:
    - Many `Knowledge` belong to one `Npc`
    - Many `Knowledge` belong to one `WorldSecret` (optional)

- `WorldSecret`
  - Fields: `title`, `description`, `category`
  - Relationships:
    - One `WorldSecret` has many `Knowledge` entities

## API Endpoints (JSON)

- `/api/locations`
- `/api/npcs`
- `/api/roles`
- `/api/worldsecrets`

Each supports list/get/create/update/delete in the relevant controller.

## Tests

Run controller tests:

```bash
docker compose exec npc-store-runtime php bin/phpunit tests/Controller
```

