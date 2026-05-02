## Day 2 – Routing, Controllers, Middleware (Where most people fail)

Today = **request lifecycle mastery**

If you don’t understand this → you cannot debug APIs.

---

# 1. 🧠 Core Concept (Critical)

Flow of every request:

```
Request → Route → Middleware → Controller → Service → Response
```

Break happens mostly in:

* Middleware (auth issues)
* Routing (wrong binding)
* Controller (bad logic)

👉 You must know **where request dies**

---

# 2. 💻 Build Task (REAL FEATURE)

Extend your Blog API:

---

## Add Protected Routes

### Routes:

```id="j8r6zd"
GET    /api/posts        (public)
GET    /api/posts/{id}   (public)

POST   /api/posts        (auth required)
PUT    /api/posts/{id}   (auth + owner)
DELETE /api/posts/{id}   (auth + owner)
```

---

## Requirements

### 1. Use Middleware

* Protect routes using auth (Sanctum or token-based)

---

### 2. Route Model Binding

```php id="skwuj6"
public function show(Post $post)
```

NO manual `find()`

---

### 3. Authorization

* Only owner can update/delete
* Use **Policy**, not if-condition

---

### 4. Controller Rules

Controller must:

* Not contain business logic
* Not query directly (use service)

---

# 3. 🧪 Debug Task (Fix This)

```php id="xdlv40"
Route::middleware('auth')->group(function () {
    Route::post('/posts', [PostController::class, 'store']);
});
```

Problem:
Even after login, API returns:

```
401 Unauthorized
```

---

### Find at least **4 possible causes**

---

# 4. 🔍 Bug Scenario (Real World)

### Issue:

```php id="35f6m7"
public function show(Post $post)
{
    return new PostResource($post);
}
```

API returns:

```
404 Not Found
```

But:

* Post exists in DB

---

### Why?

Give **4 reasons**

---

# 5. ⚙️ Optimization Task

Routes:

```php id="f40t1h"
Route::get('/posts/{id}', [PostController::class, 'show']);
```

Problem:

* This hits DB every time

---

### Improve using:

* Route model binding
* Caching strategy

Explain:

* What to cache
* Where to cache
* Cache invalidation

---

# 6. 🔥 Thinking Challenge

### Scenario:

User sends request:

```
DELETE /api/posts/10
```

User is logged in but:

👉 Post belongs to another user

---

### Question:

Where should request be blocked?

Choose ONE and justify:

* Middleware
* Controller
* Policy
* Service

---

# 7. 📌 Rules of the Day

1. Middleware = request gatekeeper
2. Policy = authorization logic (NOT controller)
3. Routes must be clean and predictable

---

# ✅ YOUR TASK

Reply with:

1. How you implemented protected routes
2. Debug task (401 issue causes)
3. 404 binding issue causes
4. Caching strategy
5. Authorization decision

---

I will evaluate strictly again.

No vague answers.
