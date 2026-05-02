# Day 1: Comprehensive Project Setup and API Documentation

This document serves as a detailed technical log for Day 1 of the Blog API development project. It covers the initial architecture, design decisions, and implementation details for a robust Laravel 11 RESTful API using Sanctum authentication.

---

## 1. Project Initialization & Architecture

### **Setup Process**
The application was bootstrapped using Composer to ensure a clean, standardized Laravel structure:
```bash
composer create-project laravel/laravel blogapi
```
Once installed, the local development server was initiated via `php artisan serve`, exposing the application base URL (typically `http://127.0.0.1:8000`).

### **Design Pattern: Service Layer Repository**
To adhere to the Single Responsibility Principle (SRP) and keep our HTTP Controllers extremely "thin", we introduced a **Service Pattern**. 
Instead of placing database queries and complex logic directly inside the controllers, controllers delegate this work to dedicated service classes (e.g., `AuthService`, `PostService`). This makes our code more modular, easier to test, and highly reusable.

---

## 2. Database Schema & Eloquent Models

### **Migrations**
We generated two core migrations using `php artisan make:migration`:
1. `create_users_table`: Handles user credential storage (`name`, `email`, `password`).
2. `create_posts_table`: Handles the blog content storage. It contains a `user_id` foreign key that cascades on delete, a `title` column, and a `content` column.

### **Eloquent Relationships & Sanctum setup**
- **User Model (`App\Models\User`)**: 
  - Represents the authenticated entity in our system.
  - **Crucial Update**: Included the `Laravel\Sanctum\HasApiTokens` trait. This trait provides the necessary internal mechanisms to issue and revoke personal access tokens (`createToken('token-name')->plainTextToken`).
  - Added a `posts()` method returning `$this->hasMany(Post::class)` to represent the One-to-Many relationship.
  
- **Post Model (`App\Models\Post`)**: 
  - Represents individual blog entries created by users.
  - Configured the `$fillable` array (`['title', 'content', 'user_id']`) to protect against mass-assignment vulnerabilities.
  - Contains a `user()` method returning `$this->belongsTo(User::class)`.

---

## 3. API Authentication (Laravel Sanctum)

To secure our API endpoints, we implemented Laravel Sanctum.
```bash
php artisan install:api
```

### **Form Requests (Validation)**
To cleanly handle validation before requests ever hit the controller, we generated:
- `RegisterRequest`: Enforces that `name`, `email` (must be unique), and `password` (minimum 8 chars + confirmation) are provided.
- `LoginRequest`: Asserts that `email` and `password` exist.

### **The Authentication Flow**
- **AuthService**: Contains the heavy lifting.
  - `register(array $data)`: Hashes the raw password utilizing the `Hash::make()` facade and persists the new user to the database.
  - `login(array $data)`: Queries for the user by email, securely verifies the password using `Hash::check()`, and generates the Sanctum API token if valid. Returns `null` on failure.
- **AuthController**: 
  - `login()` and `register()` endpoints receive authorized/validated input from our Form Requests. 
  - They inject the `AuthService`, execute the operation, and return clean JSON responses (e.g., `201 Created` with user details, or `200 OK` with the Bearer token).

---

## 4. Post Management (CRUD Implementation)

We implemented a standard RESTful convention for post management via the `PostController`.

### **Handling Request Payloads**
We created `StorePostRequest` and `UpdatePostRequest`. 
**Implementation Detail**: The database expects a `content` property, but API design specifications (and our Postman collection) dictate that clients submit a `body` key. Instead of handling this manually in the controller, we leveraged the `prepareForValidation` method within our Form Requests:
```php
protected function prepareForValidation()
{
    if ($this->has('body')) {
        $this->merge(['content' => $this->body]);
    }
}
```
This safely normalizes the developer payload before validation or mapping.

### **CRUD Operations & Authorization**
The `PostController` methods inject the `PostService`.
- `index()`: Returns all posts (eager loading the associated user to prevent N+1 query problems).
- `show($id)`: Fetches a single post. Returns a `404 Not Found` if it doesn't exist.
- `store()`: Uses `auth()->id()` behind the scenes in the `PostService` to automatically associate the newly created post with the User who sent the request.
- `update($id)` & `destroy($id)`:
  - **Security Check**: We specifically built in an ownership verification layer. Before allowing an update or deletion, the system verifies `$post->user_id === auth()->id()`. 
  - If a user tries to modify a post they do not own, the system safely aborts and returns a `403 Forbidden` response.

---

## 5. API Routing (`routes/api.php`)

Our routing architecture is strictly split between public accessibility and token-protected endpoints:

```php
// --- PUBLIC ROUTES (No Token Required) ---
Route::post('/register',[AuthController::class, 'register']);
Route::post('/login',[AuthController::class, 'login'])->name('login');
Route::get('/posts',[PostController::class, 'index']);
Route::get('/posts/{id}',[PostController::class, 'show']);

// --- PROTECTED ROUTES (Requires Bearer Token) ---
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) { return $request->user(); });
    
    // Post creation & mutation
    Route::post('/posts',[PostController::class, 'store']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
});
```

---

## 6. Testing Strategy

The complete build has been verified systematically using **Postman**:
1. Run the `Register` route to generate the unique simulated email/password.
2. Verified `Login` to ensure we receive a `token`. 
3. Automatically captured the `token` in Postman environments as a Bearer Token.
4. Used the Bearer token to perform protected `POST /posts` actions successfully.
5. Successfully fetched data continuously without errors, establishing that automated test scripts in Postman resolve with passing 2xx statuses. Local `php artisan test` suits are fully green.

