# Student & Subject API (Laravel + Sail)

This project is a Laravel-based REST API that manages two main entities:

- **Students**
- **Subjects**

The API exposes full CRUD endpoints for both entities and includes a complete authentication flow with password reset.
The stack runs fully inside **Laravel Sail** (Docker) and provides automated tests using **PHPUnit**.

> **Note:** This is a test-only/demo environment.  
> Default credentials and connection settings are intentionally provided in `.env.example` to simplify setup.  
> **Do not reuse these credentials in any production environment.**

---

## 1. Project Structure Overview

- **Routes**  
  Defined in `routes/api.php` and versioned under `/api/v1`.

- **Controllers**  
  Located in `App\Http\Controllers\Api\V1\...`.

- **Validation**  
  Implemented via Form Request classes in `App\Http\Requests\...`.

- **Services**  
  Business logic handled by service classes such as `StudentService`, `SubjectService`, and `AuthServiceInterface`.  
  Controllers depend on interfaces rather than concrete classes (SOLID principles applied).

- **Models**  
  Eloquent models for `Student` and `Subject`.

- **Infrastructure**  
  Fully containerized using Laravel Sail.  
  MySQL database via Docker Compose.  
  Authentication and session handling via Laravel Sanctum.

---

## 2. Requirements

To run this project, you need:

- **Docker & Docker Compose**
- **Make**
- **Git**

You do **not** need PHP or Composer installed locally — Sail provides the runtime inside containers.

---

## 3. Project Initialization (`make init`)

The `init` process is fully automated and prepares the entire environment without requiring PHP or Composer on the host machine.

### What happens during `make init`

1. **Creates `.env`** (if not present)  
   Based on `.env.example`.

2. **Builds the `vendor/` directory using a temporary Docker container**  
   A Composer image is used only for dependency installation:
   - Runs `composer install --ignore-platform-reqs`
   - Does **not** require Composer on the host

3. **Deletes the temporary Composer image**  
   To avoid polluting the user's Docker installation:
   ```
   docker rmi -f laravelsail/php84-composer:latest
   ```

4. **Starts Laravel Sail containers**
   ```
   ./vendor/bin/sail up -d
   ```

5. **Generates APP_KEY** inside the container

6. **Runs all migrations and seeds**

This ensures the project can be initialized reliably in any environment.

---

## 4. Postman Collection

🔗 **Postman Workspace:**  
https://www.postman.com/iurruu/workspace/rox

### 🔐 Automatic Token Handling

The included Postman scripts:

- Capture the authentication token on **login**
- Capture the token after **registration**
- Remove it on **logout**
- Automatically send it on protected routes

---

## 5. Password Reset Flow (Mailpit)

The password reset implementation uses **Mailpit** for capturing outgoing emails during development.

- Mailpit runs automatically through Docker
- Access it at:  
  👉 **http://localhost:8025**
- All outgoing reset-password emails appear there
- The email contains a **token**
- This token is used to complete the password reset flow

No real emails are sent — everything stays inside Mailpit for safe testing.

---

## 6. Make Commands

| Command | Description |
|--------|-------------|
| `make init` | Initializes environment (vendor, containers, APP_KEY, migrations) |
| `make up` | Starts containers |
| `make down` | Stops containers |
| `make restart` | Restarts containers |
| `make logs` | Shows logs |
| `make test` | Runs PHPUnit tests |
| `make artisan cmd=...` | Runs arbitrary Artisan command |
| `make migrate` | Runs migrations + seeders |
| `make fresh` | Drops everything and reruns migrations |

---

## 7. Mailpit UI

Access Mailpit at:

👉 **http://localhost:8025**

All outgoing emails (password reset, verification, etc.) appear there for testing.
