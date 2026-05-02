# Laravel 13.7 Artisan Commands Cheat Sheet

This document is a professionally formatted reference guide for the available Artisan commands in Laravel 13.7.0.

---

## 🛠️ Usage

```bash
php artisan [command] [options] [arguments]
```

### Options

| Option | Shorthand | Description |
| :--- | :--- | :--- |
| `--help` | `-h` | Display help for the given command. |
| `--silent` | | Do not output any message. |
| `--quiet` | `-q` | Only errors are displayed. All other output is suppressed. |
| `--version` | `-V` | Display this application version. |
| `--ansi` / `--no-ansi`| | Force (or disable) ANSI output. |
| `--no-interaction` | `-n` | Do not ask any interactive question. |
| `--env[=ENV]` | | The environment the command should run under. |
| `--verbose` | `-v` | Increase output verbosity. Append `v` for more (`-vv`, `-vvv`). |

---

## 🚀 Available Commands

### Global / Root Commands
| Command | Description |
| :--- | :--- |
| `about` | Display basic information about your application |
| `clear-compiled` | Remove the compiled class file |
| `completion` | Dump the shell completion script |
| `db` | Start a new database CLI session |
| `docs` | Access the Laravel documentation |
| `down` | Put the application into maintenance / demo mode |
| `env` | Display the current framework environment |
| `help` | Display help for a command |
| `inspire` | Display an inspiring quote |
| `list` | List commands |
| `migrate` | Run the database migrations |
| `optimize` | Cache framework bootstrap, configuration, and metadata |
| `pail` | Tails the application logs |
| `reload` | Reload running services |
| `serve` | Serve the application on the PHP development server |
| `test` | Run the application tests |
| `tinker` | Interact with your application |
| `up` | Bring the application out of maintenance mode |

### 🔐 Auth
| Command | Description |
| :--- | :--- |
| `auth:clear-resets` | Flush expired password reset tokens |

### 📦 Cache
| Command | Description |
| :--- | :--- |
| `cache:clear` | Flush the application cache |
| `cache:forget` | Remove an item from the cache |
| `cache:prune-stale-tags` | Prune stale cache tags from the cache (Redis only) |

### ⚙️ Config
| Command | Description |
| :--- | :--- |
| `config:cache` | Create a cache file for faster configuration loading |
| `config:clear` | Remove the configuration cache file |
| `config:publish` | Publish configuration files to your application |
| `config:show` | Display all of the values for a given configuration file or key |

### 🗄️ Database (`db`)
| Command | Description |
| :--- | :--- |
| `db:monitor` | Monitor the number of connections on the specified database |
| `db:seed` | Seed the database with records |
| `db:show` | Display information about the given database |
| `db:table` | Display information about the given database table |
| `db:wipe` | Drop all tables, views, and types |

### 🌍 Environment (`env`)
| Command | Description |
| :--- | :--- |
| `env:decrypt` | Decrypt an environment file |
| `env:encrypt` | Encrypt an environment file |

### 🔌 Install
| Command | Description |
| :--- | :--- |
| `install:api` | Create an API routes file and install Laravel Sanctum or Passport |
| `install:broadcasting` | Create a broadcasting channel routes file |

### 🏗️ Make (Generators)
| Command | Description |
| :--- | :--- |
| `make:cast` | Create a new custom Eloquent cast class |
| `make:channel` | Create a new channel class |
| `make:class` | Create a new class |
| `make:command` | Create a new Artisan command |
| `make:component` | Create a new view component class |
| `make:config` | Create a new configuration file |
| `make:controller` | Create a new controller class |
| `make:enum` | Create a new enum |
| `make:event` | Create a new event class |
| `make:exception` | Create a new custom exception class |
| `make:factory` | Create a new model factory |
| `make:interface` | Create a new interface |
| `make:job` | Create a new job class |
| `make:listener` | Create a new event listener class |
| `make:mail` | Create a new email class |
| `make:middleware` | Create a new HTTP middleware class |
| `make:migration` | Create a new migration file |
| `make:model` | Create a new Eloquent model class |
| `make:notification` | Create a new notification class |
| `make:observer` | Create a new observer class |
| `make:policy` | Create a new policy class |
| `make:provider` | Create a new service provider class |
| `make:request` | Create a new form request class |
| `make:resource` | Create a new resource |
| `make:rule` | Create a new validation rule |
| `make:scope` | Create a new scope class |
| `make:seeder` | Create a new seeder class |
| `make:test` | Create a new test class |
| `make:trait` | Create a new trait |
| `make:view` | Create a new view |

### 🔀 Migrate
| Command | Description |
| :--- | :--- |
| `migrate:fresh` | Drop all tables and re-run all migrations |
| `migrate:install` | Create the migration repository |
| `migrate:refresh` | Reset and re-run all migrations |
| `migrate:reset` | Rollback all database migrations |
| `migrate:rollback` | Rollback the last database migration |
| `migrate:status` | Show the status of each migration |

### 🛣️ Route
| Command | Description |
| :--- | :--- |
| `route:cache` | Create a route cache file for faster route registration |
| `route:clear` | Remove the route cache file |
| `route:list` | List all registered routes |

### ⏱️ Schedule
| Command | Description |
| :--- | :--- |
| `schedule:clear-cache` | Delete the cached mutex files created by scheduler |
| `schedule:interrupt` | Interrupt the current schedule run |
| `schedule:list` | List all scheduled tasks |
| `schedule:pause` / `resume`| Pause or Resume the scheduler |
| `schedule:run` | Run the scheduled commands |
| `schedule:work` | Start the schedule worker |

### 📦 Other Categories (Summary)
*   **`channel`**: List broadcast channels.
*   **`event`**: Cache, clear, and list events and listeners.
*   **`key`**: Generate application keys (`key:generate`).
*   **`lang`**: Publish language files customization.
*   **`model`**: Prune unused models or display model info.
*   **`optimize`**: Clear cached bootstrap files (`optimize:clear`).
*   **`package`**: Discover rebuilt package manifest.
*   **`queue`**: Monitor, failed, flush, restart, work, listen, etc.
*   **`schema`**: Dump schema definition.
*   **`storage`**: Create or delete symbolic links.
*   **`stub` / `vendor`**: Publish customizable assets and packages.
*   **`view`**: Cache and clear compiled Blade template files.