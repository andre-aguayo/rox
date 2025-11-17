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
  - Defined in `routes/api.php` and versioned under `/api/v1`.

- **Controllers**
  - Located in `App\Http\Controllers\Api\V1\...`.

- **Validation**
  - Implemented via Form Request classes in `App\Http\Requests\...`.

- **Services**
  - Business logic handled by service classes such as `StudentService`, `SubjectService`, and `AuthServiceInterface`.
  - Controllers depend on interfaces rather than concrete classes (SOLID principles applied).

- **Models**
  - Eloquent models for `Student` and `Subject`.

- **Infrastructure**
  - Fully containerized using Laravel Sail.
  - MySQL database via Docker Compose.
  - Authentication and session handling via Laravel Sanctum.

---

## 2. Requirements

To run this project, you need:

- **Docker & Docker Compose**
- **Make**
- **Git**

You do **not** need PHP or Composer installed locally — Sail provides the runtime inside containers.

---

## 3. Postman Collection

A complete Postman workspace with all API endpoints is available at:

🔗 **Postman Workspace:**  
https://www.postman.com/iurruu/workspace/rox

### 🔐 Automatic Authentication Handling (Login / Register / Logout)

The Postman collection includes scripts that:

- Automatically extract and store the `auth_token` after successful **login**
- Automatically extract and store the `auth_token` after successful **registration**
- Automatically clear the token after **logout**
- Automatically send the token on all protected routes using:

