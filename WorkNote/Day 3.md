# Day 3: Database + Eloquent (Deep + Optimization)

## Overview
Day 3 focused on transforming a basic Laravel API into an enterprise-grade, highly scalable backend. The core tasks involved implementing a highly optimized Comment system, enforcing strict database constraints, resolving N+1 query problems, and testing edge cases using deep database scaling strategies.

## 1. Database & Migrations Architecture
- **Soft Deletes:** Implemented `SoftDeletes` natively across all major models. This ensures records are marked with `deleted_at` instead of being permanently erased.
- **Strict Foreign Key Constraints:** Enforced cascading deletes at the database level.
- **Migration Consolidation:** Resolved partial migration overlaps (SQLite constraint conflicts) by physical consolidation, deleting duplicate files, and executing a clean `php artisan migrate:fresh`.

## 2. Models & Relationships
- **Strict Typing:** Configured models with `declare(strict_types=1)`.
- **Eager Loading:** Added advanced Eloquent relationship mapping to eradicate **N+1 query memory blooms**.

## 3. Controllers & Routing
- **Grouped Routing:** Completely refactored `routes/api.php` utilizing `Route::controller()` groups for maximum readability.
- **Unified Middleware:** Safely locked mutation endpoints behind Sanctum (`auth:sanctum`).
- **Memory-Safe Pagination:** Replaced bulk `get()` calls on relationships with paginated chunks.

## 4. Authorization (Sanctum + Policies)
- **Granular Policies:** Created `CommentPolicy` restricted tightly to token-validated authors.
- **Token Verification:** Tested and confirmed token revocation.

## 5. API Resources
- **JSON Transformations:** Adjusted timestamp serialization using the PHP 8 null-safe operator (`?->toIso8601String()`).

## 6. Testing & Quality Assurance
- **Factories & Seeders:** Configured robust `CommentFactory`, `PostFactory`, and `DatabaseSeeder`. 
- **Endpoint Sweep:** Executed comprehensive local server testing against `GET /api/posts`, `DELETE /api/comments/{id}`, and `POST /api/logout`.

## 7. Deep Scalability Concepts Covered
- **Cursor vs. Offset Pagination:** Analyzed stable B-Tree indexing vs offset drift.
- **Composite Indexes:** Using multi-column indices for frequent relational lookups.
- **Hot Partitions:** Strategies for database sharding across massive concurrent write loads.

---

## 8. Files Modified & How They Were Changed

### A. Routes (`routes/api.php`)
- **How:** Grouped routing using `Route::controller()` and applied `'auth:sanctum'` selectively. We wrapped POST/DELETE methods in the auth middleware group to protect mutations while keeping GET methods open.

### B. Migrations (`database/migrations/`)
- **Files:** `...create_posts_table.php`, `...create_comments_table.php`.
- **How:** Added `$table->foreignIdFor(...)` with `->constrained()->cascadeOnDelete();`. Implemented `$table->softDeletes();` to allow recovering deleted records and keep the database immutable. Deleted duplicated/failed migrations to allow `migrate:fresh` to run cleanly.

### C. Models (`app/Models/Comment.php`, `app/Models/Post.php`)
- **How:** Added `use SoftDeletes, HasFactory;`. Mapped relations such as `public function comments(): HasMany` and `public function user(): BelongsTo`. Declared strict types (`strict_types=1`).

### D. Policies (`app/Policies/CommentPolicy.php`)
- **How:** Scaffolded a policy to check if the current authenticated user owns the comment: `return $user->id === $comment->user_id;`.

### E. Controllers (`app/Http/Controllers/PostController.php`, `app/Http/Controllers/CommentController.php`)
- **How (PostController):** In `show()`, mitigated N+1 issues by dynamically loading data: `$post->loadMissing('user')` and paginating sub-relationships `$post->comments()->with('user')->paginate()`.
- **How (CommentController):** In `destroy()`, linked the policy via `Gate::authorize('delete', $comment);` and executed `$comment->delete();` knowing it triggers a soft delete behind the scenes.

### F. API Resources (`app/Http/Resources/PostResource.php`, `app/Http/Resources/CommentResource.php`)
- **How:** Fixed deep null-pointer exceptions in date parsing by introducing the PHP 8 null-safe operator: `'created_at' => $this->created_at?->toIso8601String()`. Structured nested relations safely using `$this->whenLoaded('user')`.

### G. Database Seeder & Factories (`database/seeders/DatabaseSeeder.php`, `database/factories/*`)
- **How:** Set up `DatabaseSeeder` to generate test users, attach posts to them, and attach comments. Created an admin user (`admin@demo.com`) for persistent local token testing.
