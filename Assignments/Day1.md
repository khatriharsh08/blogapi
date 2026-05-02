## Day 1 – Project Setup + Fundamentals

We are building: **Blog API (production mindset, not tutorial garbage)**

---

## 1. 🧠 Core Concept (Practical Only)

Laravel app is NOT controllers + routes.

Real structure:

* Controllers → thin (only request/response)
* Business logic → Services
* DB logic → Models / Queries
* Validation → Form Requests

If you put logic in controllers → **you fail interviews**

---

## 2. 💻 Build Task (REAL FEATURE)

### Setup Project

You must create:

* Laravel project
* MySQL DB
* Basic Blog API with:

**Entities:**

* Users
* Posts

---

### Requirements

#### Users Table

* id
* name
* email (unique)
* password
* timestamps

#### Posts Table

* id
* user_id (FK)
* title
* content
* timestamps

---

### API Endpoints

Implement:

```
POST   /api/register
POST   /api/login
GET    /api/posts
POST   /api/posts
GET    /api/posts/{id}
```

---

### Rules

* Use migrations
* Use Eloquent relationships
* Return JSON only
* Proper status codes (201, 200, 404, 401)

---

## 3. 🧪 Debug Task (Fix This Code)

This is bad code. Fix it.

```php
public function store(Request $request)
{
    $post = new Post();
    $post->title = $request->title;
    $post->content = $request->content;
    $post->user_id = auth()->id();
    $post->save();

    return response($post);
}
```

Find **at least 5 issues**.

---

## 4. 🔍 Bug Scenario (Think Like Engineer)

**Problem:**
API `/api/posts` is returning empty array.

But DB has data.

What are possible causes?

Give **at least 4**.

---

## 5. ⚙️ Optimization Task

You load posts with users:

```php
$posts = Post::all();
```

Problem: N+1 query.

Fix it properly.

Also explain:

* What query Laravel is running now
* What changes after fix

---

## 6. 🔥 Thinking Challenge

If this API gets **1 million users**, what breaks first?

Choose top 3:

* DB?
* Auth?
* Code structure?
* File system?

Explain WHY.

---

## 7. 📌 Rules of the Day

1. Controllers must stay thin
2. Never trust request data (validate everything)
3. Always think in DB queries, not just code

---

## ✅ YOUR TASK

Reply with:


---

## ?? FINAL TASK IMPLEMENTATION (How it was added)

- **Pagination:** Modified the PostService's getAll() method to call paginate(10) instead of get(). This limits payload sizes automatically.
- **Form Validation:** Relied on Laravel Form Requests (StorePostRequest, UpdatePostRequest) which were previously injected into the controller.
- **API Resource:** Created the resource using php artisan make:resource PostResource. This file was modified to explicitly shape the JSON return (id, 	itle, content, uthor_name, and formatted dates), then applied inside the Controller using 
ew PostResource() or PostResource::collection().
- **Indexes:** Altered database/migrations/*_create_posts_table.php to explicitly chain ->index() onto the oreignId('user_id') column definition.
- **Error Handling:** Wrapped all contents of PostController methods (index, show, store, etc.) inside a 	ry { ... } catch (\Exception $e) { ... } block. We catch errors, utilize the Log facade (Log::error($e->getMessage())) to monitor them silently in production, and return a safe 500 Internal Server Error to the end user.
1. How you structured project (folders + logic separation)
2. Fix for debug code (list issues)
3. Bug scenario answers
4. N+1 fix explanation
5. Scaling answer

---

I will evaluate like a hiring manager.

No vague answers. Be precise.


---------------------------------------------------------------------------------------------




🔧 Refactor Suggestions

If you want to reach job-ready level, do this:

1. Your Controller should evolve to:
public function store(StorePostRequest $request)
{
    try {
        $post = $this->postService->create(
            $request->validated(),
            auth()->id()
        );

        return new PostResource($post);

    } catch (\Exception $e) {
        Log::error($e);
        return response()->json(['error' => 'Something went wrong'], 500);
    }
}
2. Your Service Layer MUST exist

If not → you fail real interviews.

3. Add Pagination ALWAYS
Post::with('user')->latest()->paginate(10);
4. Think DB FIRST, not Laravel

Ask:

Is column indexed?
Is query optimized?
What happens at scale?
🧠 Your Current Level
Area	Level
Laravel Basics	✅ Strong
Structure	✅ Good
Debugging Depth	⚠️ Average
Production Thinking	❌ Weak
Scaling Awareness	⚠️ Partial
🚨 Reality Check

If I interview you today:

👉 You might pass junior-mid
👉 You will fail strong backend roles

🔥 What You Did Well
Clean separation understanding
Correct N+1 fix
Good service layer mindset
Structured answers (rare)
📌 FINAL TASK (MANDATORY BEFORE DAY 2)

Do NOT skip.

Improve your API:

Add:

Pagination to /posts
Proper validation (Form Request)
API Resource for Post
Index on user_id
Error handling (global or try-catch)

Reply with:


---

## ?? FINAL TASK IMPLEMENTATION (How it was added)

- **Pagination:** Modified the PostService's getAll() method to call paginate(10) instead of get(). This limits payload sizes automatically.
- **Form Validation:** Relied on Laravel Form Requests (StorePostRequest, UpdatePostRequest) which were previously injected into the controller.
- **API Resource:** Created the resource using php artisan make:resource PostResource. This file was modified to explicitly shape the JSON return (id, 	itle, content, uthor_name, and formatted dates), then applied inside the Controller using 
ew PostResource() or PostResource::collection().
- **Indexes:** Altered database/migrations/*_create_posts_table.php to explicitly chain ->index() onto the oreignId('user_id') column definition.
- **Error Handling:** Wrapped all contents of PostController methods (index, show, store, etc.) inside a 	ry { ... } catch (\Exception $e) { ... } block. We catch errors, utilize the Log facade (Log::error($e->getMessage())) to monitor them silently in production, and return a safe 500 Internal Server Error to the end user.
👉 “DONE” + what you implemented
