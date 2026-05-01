Blog API Postman Tests

Import the collection `postman/blogapi.postman_collection.json` into Postman and the environment `postman/blogapi.postman_environment.json`.

Quick steps:

1. Import the collection and environment in Postman.
2. Select the `Blog API Environment` and ensure `baseUrl` points to your running app, e.g. `http://localhost:8000`.
3. Run the requests in order: `Register`, `Login`, `Get Posts`, `Create Post`, `Get Post by ID`.

Notes:
- `Register` will generate a unique email for each run.
- `Login` saves the returned token into the environment variable `token` used by `Create Post`.
- If your app runs on a different port or host, update `baseUrl` in the environment.

Commands to start your API locally (if using built-in server):

```bash
php artisan serve --host=127.0.0.1 --port=8000
```
