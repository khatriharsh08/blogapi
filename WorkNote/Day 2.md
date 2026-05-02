# Day 2 – Routing, Controllers, Middleware, and Debugging Mastery
## Today's Core Theme: The Request Lifecycle
**Flow of every request:**
`Request → Route → Middleware → Controller → Service → Response`

If an API breaks, it usually fails at:
- **Middleware:** Authentication or CORS issues (`401 Unauthorized`).
- **Routing:** Wrong Route Model Binding (`404 Not Found`).
- **Controller:** Bad logic or improper delegation.

---

## 1. Implementing Protected Routes
We extended the Blog API to protect sensitive actions while leaving read operations public.
- **Public Routes:** `GET /posts`, `GET /posts/{post}`
- **Protected Routes:** `POST /posts`, `PUT /posts/{post}`, `DELETE /posts/{post}`

**Implementation:**
We grouped the protected routes inside `routes/api.php` under the `auth:sanctum` middleware. This acts as a request gatekeeper, ensuring only authorized, token-holding users can create, update, or delete posts.

---

## 2. Route Model Binding
Instead of querying the database manually (`$post = Post::find($id)`), we refactored the routes to use Laravel's Implicit Route Model Binding.

**Before:** `Route::get('/posts/{id}')` -> Controller gets `$id` as integer.
**After:** `Route::get('/posts/{post}')` -> Controller gets the instantiated `Post $post` model.

**Benefits:**
- **Removes Boilerplate:** No more `if (!$post) return 404;` in the controller.
- **Automatic 404:** Laravel automatically queries the database and immediately aborts with a `404 Not Found` if the model does not exist.

---

## 3. Authorization (Policies over If-Conditions)
**Requirement:** Only the owner of a post should be able to update or delete it.
Instead of adding `if ($post->user_id !== auth()->id())` in the controller, we used **Policies**.

**Implementation:**
- Inside `app/Policies/PostPolicy.php`, we added the logic to `update` and `delete` methods: `return $user->id === $post->user_id;`
- Inside `PostController.php`, we intercept unauthorized actions gracefully natively via Laravel's Gate: `Gate::authorize('update', $post);`
- **Why?** Authorization is a business rule specifically regarding user permissions, which belongs in a Policy. The Controller should strictly remain a lean HTTP router.

---

## 4. Controller & Service Rules Refactored
The `PostController` was refactored strictly under these rules:
- **No business logic:** Controller strictly handles input validation (`FormRequests`) and output formatting (`API Resources`).
- **No direct database queries:** Queries belong in the `PostService`.
We updated `PostService` methods to accept the already-populated `Post $post` object (derived from Route Model Binding), streamlining it entirely for data manipulation.

---

## 5. API Debugging Scenarios

### Scenario A: 401 Unauthorized (Even After Login)
If an API returns 401 despite an active login session, the causes are:
1. **Missing or Expired Token:** The client didn't send the Bearer token in the `Authorization` header, or it has expired.
2. **Missing `Accept: application/json`:** Without it, Laravel tries to seamlessly redirect to a generic web login page instead of returning 401 JSON.
3. **Frontend CORS / Cookie Misconfiguration:** For SPAs, cookies weren't sent (`withCredentials: true`), or `SANCTUM_STATEFUL_DOMAINS` isn't assigned.
4. **Session/Guard Mixed Contexts:** Incorrect application of stateful vs stateless guards.

### Scenario B: 404 Not Found (But Post Exists)
If Route Model Binding returns 404 for an existing row:
1. **Route Parameter Mismatch:** Route defined as `{id}` but controller asks for `$post` -> implicitly fails to map.
2. **Soft Deletes:** The post has a `deleted_at` timestamp. Laravel ignores these by default (use `withTrashed()`).
3. **Global Scopes:** A Global Scope is applied (e.g., `published = true`) filtering out the specific record during automatic lookup.
4. **Primary Key Mismatch:** DB uses UUIDs but the model isn't configured for string keys (`$keyType = 'string'`, `$incrementing = false`).

---

## 6. Performance & Optimization

### A. Slow API (`GET /api/posts` taking 3 seconds)
**Step-by-Step Debugging:**
1. **Identify Bottleneck:** Use Laravel Telescope, Debugbar, or `DB::listen` to measure Database vs Application execution time.
2. **Database Checks:**
    - Is there an **N+1 query problem**? Eager load relationships natively: `Post::with('user')->paginate(...)`.
    - Run `EXPLAIN ANALYZE <sql_query>` to detect missing SQL indexes or full table scans.
3. **Laravel-Level Checks:**
    - Check if hydrating massive unsorted collections without `paginate()` is exhausting PHP memory.
    - Look for poorly coded API Resources triggering lazy database hits for every loop iteration.

### B. Caching Strategy
If the API repeatedly requests the same heavy objects:
- **What to cache:** Heavy DB results or relationships that rarely change.
- **Where to cache:** Redis or Memcached using `Cache::remember('post_'.$post->id, $ttl, function() {...})`.
- **Cache Invalidation:** Use Laravel Model Events (`updated`, `deleted`) or Observers to trigger `Cache::forget()` when a record is altered, keeping data fresh.