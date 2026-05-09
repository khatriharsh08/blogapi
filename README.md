# Laravel 13 Blog API

A production-ready RESTful Blog API built with **Laravel 13**, featuring Sanctum token authentication, policy-based authorization, and clean Service/DTO architecture. The API supports user authentication, posts, comments, filtering, and pagination with consistent JSON responses.

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Requirements](#requirements)
- [Quick Start](#quick-start)
- [Configuration](#configuration)
- [API Documentation](#api-documentation)
- [Development Scripts](#development-scripts)
- [Testing](#testing)
- [Linting](#linting)
- [Build Assets](#build-assets)
- [Project Structure](#project-structure)

## Features

- Token-based authentication via Laravel Sanctum
- Policy/Gate authorization for post and comment ownership
- CRUD endpoints for posts and comments
- Filtering, sorting, and pagination for posts
- Centralized validation via Form Requests
- Consistent JSON response envelope and error format
- Rate limiting on auth and write endpoints

## Tech Stack

- **Laravel 13** / PHP 8.3+
- **Sanctum** for API tokens
- **MySQL** or **SQLite**
- **Vite** + **Tailwind CSS** (frontend build assets)

## Requirements

- PHP 8.3+
- Composer
- Node.js 18+
- MySQL or SQLite

## Quick Start

```bash
git clone https://github.com/khatriharsh08/blogapi.git
cd blogapi
composer install
npm install
cp .env.example .env
php artisan key:generate
```

### Database (SQLite example)

```bash
touch database/database.sqlite
php artisan migrate --seed
```

### Run the API

```bash
php artisan serve
```

The API is available at `http://localhost:8000/api/v1`.

### Seeded Demo User

After seeding, you can log in with:

- **Email:** `admin@demo.com`
- **Password:** `password`

## Configuration

Update `.env` for your environment:

- `DB_CONNECTION` (`sqlite` or `mysql`)
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `APP_URL` (base URL for your environment)

## API Documentation

Detailed request/response documentation lives in [API_DOCUMENTATION.txt](./API_DOCUMENTATION.txt).

### Base URL

`/api/v1`

### Auth Notes

Send the token as a **Bearer** token:

```
Authorization: Bearer <token>
```

## Development Scripts

```bash
composer setup   # install deps, prepare env, migrate, and build assets
composer dev     # run API server, queue, logs, and Vite in parallel
```

## Testing

```bash
composer test
```

## Linting

```bash
vendor/bin/pint --test
```

## Build Assets

```bash
npm run build
```

## Project Structure

```
app/
  DTOs/        # Data transfer objects
  Filters/     # Query filtering logic
  Services/    # Business logic layer
  Policies/    # Authorization policies
  Http/        # Controllers, requests, middleware
routes/
  api.php      # API routes
```
