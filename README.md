# JLabs API

This repository contains the backend API built with **Laravel 12** for the JLabs technical assessment.

The API handles authentication and IP search history storage used by the React web application.

---

## Tech Stack

- Laravel 12
- PHP 8.2+
- MySQL
- Laravel Sanctum (Token Authentication)
- Node.js & NPM

---

## Features

- User authentication using email and password
- Token-based API authentication
- Retrieve authenticated user information
- Save searched IP addresses
- Display user search history
- Delete multiple history records

---

## Default Login Credentials

These credentials are created using a database seeder.

- **Email:** ej@jlabs.test
- **Password:** password123

---

## Requirements

Make sure the following are installed:

- PHP >= 8.2
- Composer
- MySQL
- Node.js & NPM
- Git

---

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/EJARNAD0/jlabs-api.git
   cd jlabs-api
   ```

2. Run the automated setup script:
   ```bash
   composer run setup
   ```
   *This command installs PHP dependencies, sets up the `.env` file, generates the application key, runs migrations, installs NPM dependencies, and builds assets.*

### Manual Installation (Alternative)

If you prefer to install manually:

```bash
composer install
cp .env.example .env
php artisan key:generate
# Configure database in .env
php artisan migrate --seed
npm install
npm run build
```

---

## Running the Application

Start the development server (runs Laravel server, queue worker, and Vite):

```bash
composer run dev
```

The API will be available at: `http://localhost:8000`

---

## API Endpoints

### Authentication

| Method | Endpoint | Description | Request Body |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/login` | Authenticate user and return token | `email`, `password` |
| `POST` | `/api/logout` | Invalidate current token | *Requires Bearer token* |
| `GET` | `/api/me` | Get authenticated user info | *Requires Bearer token* |

### IP Search History

| Method | Endpoint | Description | Request Body |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/histories` | Get user's search history | *Requires Bearer token* |
| `POST` | `/api/histories` | Save IP address search | `ip` (required), `payload` (optional array) |
| `DELETE` | `/api/histories` | Delete multiple history records | `ids` (array of history IDs) |

---

## API Usage Examples

### Login

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"ej@jlabs.test","password":"password123"}'
```

**Response:**
```json
{
  "token": "1|abcdefghijklmnopqrstuvwxyz123456",
  "user": {
    "id": 1,
    "email": "ej@jlabs.test",
    "name": "Test User"
  }
}
```

### Get User Info

```bash
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer {your-token}"
```

### Save IP Search

```bash
curl -X POST http://localhost:8000/api/histories \
  -H "Authorization: Bearer {your-token}" \
  -H "Content-Type: application/json" \
  -d '{"ip":"8.8.8.8", "payload": {"city": "Mountain View"}}'
```

### Get Search History

```bash
curl -X GET http://localhost:8000/api/histories \
  -H "Authorization: Bearer {your-token}"
```

### Delete Multiple History Records

```bash
curl -X DELETE http://localhost:8000/api/histories \
  -H "Authorization: Bearer {your-token}" \
  -H "Content-Type: application/json" \
  -d '{"ids":[1,2,3]}'
```

### Logout

```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer {your-token}"
```

---

## Project Structure

```
jlabs-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   └── HistoryController.php
│   │   └── Middleware/
│   ├── Models/
│   │   ├── User.php
│   │   └── IpHistory.php
│   └── Providers/
├── database/
│   ├── migrations/
│   └── seeders/
│       └── DatabaseSeeder.php
├── routes/
│   └── api.php
└── .env
```

---

## Error Handling

The API returns appropriate HTTP status codes:

- **200** - Success
- **201** - Resource created
- **400** - Bad request
- **401** - Unauthorized
- **404** - Not found
- **422** - Validation error
- **500** - Server error

---

## Frontend Repository

The React frontend application that consumes this API can be found at:
[https://github.com/EJARNAD0/jlabs-web](https://github.com/EJARNAD0/jlabs-web)
