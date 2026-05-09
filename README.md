# 🚀 Laravel 13 Enterprise RESTful Blog API

A production-ready, industry-standard RESTful Blog API built utilizing **Laravel 13**. This project demonstrates enterprise-level API design, leveraging Service-DTO architectural patterns, strict request validation, Gate authorization, and secure Sanctum token-based authentication.

---

## 🌟 Key Features

* **Enterprise Architecture:** Action-oriented Thin Controllers with business logic abstracted into decoupled Services and Data Transfer Objects (DTOs).
* **Strict Type Safety:** Strongly enforced declare(strict_types=1); and PHP 8.3 features across all classes.
* **Advanced Security:** Route throttling (rate limiting), explicit Idempotency middleware, and centralized JSON exception handling.
* **Authentication & Authorization:** Token-based authentication using **Laravel Sanctum**. Ownership restrictions natively enforced via Eloquent Policies and Gates.
* **Form Requests:** Strict incoming payload validation mapped directly to robust HTTP Form request classes.

---

## 📦 Requirements

* **PHP** ^8.3+
* **Composer**
* **MySQL / SQLite**

---

## 🛠️ Installation & Setup

1. **Clone the repository:**
   `ash
   git clone <your-repository-url>
   cd blogapi
   `

2. **Install dependencies:**
   `ash
   composer install
   `

3. **Environment Setup:**
   `ash
   cp .env.example .env
   php artisan key:generate
   `

4. **Run Database Migrations & Seeders:**
   `ash
   php artisan migrate --seed
   `

5. **Start the local server:**
   `ash
   php artisan serve
   `

---

## 📖 API Documentation (Endpoints)

For a complete deep-dive into request/response shapes, headers, and payloads, please see the [API_DOCUMENTATION.txt](./API_DOCUMENTATION.txt) included in this repository.

**Quick Overview (Base URL: /api/v1)**

| Method | Endpoint | Access | Description |
| :--- | :--- | :--- | :--- |
| POST | /register | 🟢 Public | Register user & receive token |
| POST | /login | 🟢 Public | Authenticate user & receive token |
| POST | /logout | 🔒 Protected | Invalidate current token |
| GET  | /me | 🔒 Protected | Fetch authenticated profile |
| GET  | /posts | 🟢 Public | Retrieve paginated posts (w/ filters) |
| POST | /posts | 🔒 Protected | Create a new blog post |
| GET  | /posts/{id} | 🟢 Public | Fetch a specific post |
| PUT  | /posts/{id} | 🔒 Protected | Update an owned post |
| DELETE | /posts/{id} | 🔒 Protected | Delete an owned post |
| GET  | /posts/{id}/comments | 🟢 Public | Retrieve comments for a post |
| POST | /posts/{id}/comments | 🔒 Protected | Add a comment to a post |
| DELETE | /comments/{id} | 🔒 Protected | Delete an owned comment |

> **Note on Protected Routes:**
> You must pass the generated token as a **Bearer Token** in the authorization header:  
> Authorization: Bearer 1|your_token_string_here...

---

## 🧪 Architecture & Testing

This API is structurally designed to handle high concurrency and scale. 
* **N+1 Avoidance:** Eloquent models leverage eager loading implicitly mapped.
* **Clean Code:** Standardized formatting enforced via Laravel Pint to PSR-12 / Laravel standards.
* **Testing:** Postman collections and native Laravel feature tests are ready at your disposal (php artisan test).
