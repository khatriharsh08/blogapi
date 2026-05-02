# 🚀 Laravel 13 RESTful Blog API

A production-ready RESTful Blog API built using **Laravel 13.7**. This project demonstrates enterprise-level API design, utilizing robust service-repository patterns, strict request validation, and secure Sanctum token-based authentication.

---

## 🌟 Key Features

* **Strict REST Architecture:** Properly scoped actions with explicit HTTP verbs (`GET`, `POST`, `PUT`, `DELETE`).
* **Authentication:** Token-based authentication utilizing **Laravel Sanctum**.
* **Service Pattern Logic:** Controllers are "thin" and focus only on requests/responses. All DB queries and complex rules live in `AuthService` and `PostService`.
* **Form Requests:** Strict payload validation mapped directly to constraints (e.g., merging `body` into `content`).
* **Ownership Authorization:** Users can only modify or delete the posts they explicitly own, returning `403 Forbidden` otherwise.

---

## 📦 Requirements

* **PHP** ^8.2+
* **Composer**
* **MySQL** or **SQLite**

---

## 🛠️ Installation & Setup

1. **Clone the repository:**
   ```bash
   git clone <your-repository-url>
   cd blogapi
   ```

2. **Install dependencies:**
   ```bash
   composer install
   ```

3. **Environment Setup:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Make sure to configure your DB credentials inside the `.env` file.*

4. **Run Database Migrations:**
   ```bash
   php artisan migrate
   ```

5. **Start the local server:**
   ```bash
   php artisan serve
   ```

---

## 📡 API Endpoints Overview

All endpoints start with the `/api` prefix.

| Method | Endpoint             | Access        | Description                           |
| :---   | :---                 | :---          | :---                                  |
| `POST` | `/api/register`      | 🟢 Public     | Register a new user and receive token |
| `POST` | `/api/login`         | 🟢 Public     | Authenticate user and receive token   |
| `GET`  | `/api/posts`         | 🟢 Public     | Retrieve all blog posts               |
| `GET`  | `/api/posts/{id}`    | 🟢 Public     | Retrieve a specific blog post         |
| `GET`  | `/api/user`          | 🔒 Protected  | Get authenticated user profile        |
| `POST` | `/api/posts`         | 🔒 Protected  | Create a new blog post                |
| `PUT`  | `/api/posts/{id}`    | 🔒 Protected  | Update an existing owned blog post    |
| `DELETE`| `/api/posts/{id}`   | 🔒 Protected  | Delete an owned blog post             |

> **Note on Protected Routes:**
> You must pass the generated token as a **Bearer Token** in the authorization header:  
> `Authorization: Bearer 1|your_token_string_here...`

---

## 🧪 Testing

You can use the built-in Postman collection provided in `/postman` or run Laravel's feature test suite to ensure all endpoints pass reliably:

```bash
php artisan test
```

---

## 📚 Architect's Note
This API is structurally designed to handle scale. Logic is intentionally decoupled into `Services`, preventing monolithic, hard-to-maintain controllers. The `N+1` database query problems have been strictly avoided by ensuring Eloquent models implement eager loading (`with()`).
