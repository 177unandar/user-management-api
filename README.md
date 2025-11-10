# User Management API

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com/)
[![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)

A simple User Management API built with Laravel 11, featuring authentication, user management, and role-based
access control.

## Features

- JWT Authentication
- User Management (CRUD operations)
- Role-based Access Control (Admin, Manager, User)
- Email Verification
- API Documentation with Swagger/OpenAPI
- Comprehensive Test Coverage
- Queue-based Email Notifications
- Search and Filter Users
- RESTful API Design

## Getting Started

### Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL 5.7+ or MariaDB 10.3+

### Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/177unandar/user-management-api.git
   cd user-management-api
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Copy the environment file and generate application key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure your database in `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=user_management
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. Run database migrations and seeders:
   ```bash
   php artisan migrate --seed
   ```

6. Generate JWT secret key:
   ```bash
   php artisan jwt:secret
   ```

7. Start the development server:
   ```bash
   php artisan serve
   ```

8. Access the API at: `http://localhost:8000/api`

## API Documentation

API documentation is available using Swagger/OpenAPI. After starting the development server, you can access the
interactive API documentation at:

- Swagger UI: `http://localhost:8000/api/documentation`
- API JSON: `http://localhost:8000/api-docs.json`

## Authentication

The API uses JWT (JSON Web Tokens) for authentication. Include the token in the `Authorization` header for protected
routes:

```
Authorization: Bearer your_jwt_token_here
```

### Authentication Endpoints

- `POST /api/login` - Authenticate user and get JWT token
- `GET /api/email/verify/{id}/{hash}` - Verify user's email address

## User Management Endpoints

### Public Endpoints

- `POST /api/users` - Create a new user

### Protected Endpoints (Requires Authentication)

- `GET /api/users` - List all users (paginated with search and filtering)


