# Day 2 – Routing, Controllers, Middleware - Solution

## 1. How Protected Routes Were Implemented
- **Middleware:** Wrapped the `POST`, `PUT`, and `DELETE` routes in `routes/api.php` inside an `auth:sanctum` middleware group to ensure only authenticated users can access them.
- **Route Model Binding:** Changed the route parameters from `{id}` to `{post}` (`Route::get('/posts/{post}')`) and injected the `Post $post` model directly into the `PostController` methods. This automatically fetches the post, removing `find()` logic from the controller.
- **Authorization:** Used `Gate::authorize('update', $post)` and `Gate::authorize('delete', $post)` inside the controller methods, which delegates the check to `PostPolicy`. The policy verifies if `$user->id === $post->user_id`.
- **Service Pattern:** Passed the validated data and the bound `Post` model to `PostService` to handle database operations, keeping the controller strictly for HTTP request/response flow.

## 2. Debug Task: 401 Unauthorized Issue Causes
1. **Missing or Expired Token:** The client didn't send a token, or the provided token has expired or been revoked.
2. **Missing `Accept: application/json` Header:** Without this header, Laravel might attempt to seamlessly redirect to a web login route instead of returning a proper 401 JSON response, causing authentication to fail.
3. **Missing `Authorization: Bearer <token>` Header:** The client failed to include the authorization header entirely.
4. **CORS / Sanctum Stateful Misconfiguration:** For SPA authentication, the frontend must pass credentials (cookies) with the request, and the `SANCTUM_STATEFUL_DOMAINS` and `config/cors.php` must be properly configured.

## 3. Bug Scenario: 404 Route Binding Issue Causes (Post exists in DB)
1. **Route Parameter Name Mismatch:** The route is defined as `Route::get('/posts/{id}')` instead of `Route::get('/posts/{post}')`. Laravel's Implicit Binding requires the route parameter name to exactly match the injected controller variable name (`$post`).
2. **Soft Deletes:** The post has a `deleted_at` timestamp. Route model binding excludes soft-deleted records by default unless `->withTrashed()` is chained to the route definition.
3. **Global Scopes:** A Global Scope is applied to the `Post` model (e.g., `published = true`) that actively filters out this specific record when the implicit retrieval query runs.
4. **Primary Key / UUID Type Mismatch:** The database uses UUIDs or non-integer primary keys, but the model doesn't specify `$keyType = 'string'` and `public $incrementing = false`, causing the binding query to fail type casting.

## 4. Caching Strategy
- **What to Cache:** Complex queries, heavy API responses, or the `Post` resource along with its relationships (like user data or comments) that are requested frequently but updated rarely.
- **Where to Cache:** An intermediate fast memory store like Redis or Memcached, accessed via Laravel's Cache Facade (`Cache::remember('post_'.$post->id, $ttl, function() {...})`).
- **Cache Invalidation:** The cache must be purged whenever the post is updated or deleted. This should be handled neatly via Laravel Model Events (using `updated` and `deleted` events) or an Observer, automatically triggering `Cache::forget('post_'.$post->id)`.

## 5. Authorization Decision: Where should the request be blocked?
**Policy**

*Justification:* Authorization is a business rule specifically regarding individual resources and user permissions. Applying rules in a **Policy** cleanly ties permissions natively to the respective model. This ensures the Controller remains a lean HTTP router (no business checking), and the Service remains purely dedicated to data manipulation devoid of HTTP context. The Policy cleanly intercepts the request and automatically returns a `403 Forbidden` response natively through Laravel's built-in Gate.



---------------------------------------------------------------------------------------------



# Day 2 – Debugging Scenario: Slow API (`GET /api/posts`)

## Step-by-Step Debugging Approach

### 1. Identify the Bottleneck (The Tools)
To definitively locate why `GET /api/posts` takes 3 seconds, I will measure instead of guessing.
- **Laravel Telescope / Clockwork:** Install and check the "Requests" tab to see the exact breakdown of time spent (Database vs. Application execution vs. Network).
- **Laravel Debugbar:** If working locally, API requests will log data into the `.json` storage. I would inspect the timing and query counts.
- **`DB::listen()` (Quick Check):** Temporarily add `DB::listen` in `AppServiceProvider` to log all queries and their execution times to `storage/logs/laravel.log`.
```php
DB::listen(function ($query) {
    Log::info($query->sql, $query->bindings, $query->time);
});
```

### 2. Database-Level Checks (The Most Common Culprit)
If Telescope/logs show that DB queries are consuming the 3 seconds:
- **N+1 Problem Check:** Check if fetching 10 posts triggers 11 queries (1 for posts, 10 for users). This happens if relationships aren't eager loaded (e.g., calling `$post->user->name` inside a resource without `with('user')`).
- **Slow Query Check:** If it's just one query taking 2.5 seconds, extract the raw SQL from the logs.
- **`EXPLAIN ANALYZE`:** Run `EXPLAIN ANALYZE <sql_query>` straight in MySQL/PostgreSQL. It will explicitly show if it's doing a "Full Table Scan".
- **Missing Indexes:** Check if the columns being filtered, sorted, or joined on (like `user_id`, `created_at`) have indexes. `EXPLAIN` will highlight missing indexes.

### 3. Laravel-Level Checks (Application/Memory Issues)
If the database queries are fast (e.g., 50ms total), the bottleneck is in PHP/Laravel:
- **Memory/Data Hydration Hook:** Are we fetching 50,000 rows at once instead of using `paginate()` or `cursor()`? Loading out massive collections exhausts RAM and CPU during model hydration.
- **N+1 in API Resources:** Are we calling accessors or relationships lazily inside `PostResource`? (e.g., `Comment::where('post_id', $this->id)->count()` inside the resource).
- **Third-Party API calls:** Is the controller synchronously hitting an external API or sending an email before returning the response?
- **Middleware Overhead:** Is a custom middleware performing a heavy check (like an external user validation) synchronously on every request?

### 4. The Fix Strategy (Action Plan)
1. **Fix N+1 Iterations:** Use Eager Loading directly on the initial query: `Post::with(['user', 'comments'])->paginate(15);`
2. **Paginate Output:** Ensure `->paginate()` or `->simplePaginate()` is used rather than `->get()`.
3. **Add Database Indexes:** Add standard indexes via a migration on heavily queried columns:
   ```php
   $table->index('user_id');
   $table->index('created_at');
   ```
4. **Select Specific Columns:** Don't pull huge `TEXT` columns if the list view only needs summaries: `Post::select('id', 'title', 'user_id')->with('user:id,name')...`
5. **Cache the Response:** If data is read-heavy and globally structured, cache the paginated result:
   ```php
   $posts = Cache::remember('posts_page_' . request('page', 1), 3600, function () {
       return Post::with('user')->latest()->paginate(15);
   });
   ```
6. **Move synchronous work to Jobs:** Dispatch emails or external network calls to Background Queues.