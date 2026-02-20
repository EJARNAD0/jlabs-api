
This repository contains the backend API built with **Laravel 12** for the JLabs technical assessment.

The API handles authentication and IP search history storage used by the React web application.

---

## Tech Stack

- Laravel 12
- PHP 8.2+
- MySQL
- Laravel Sanctum (Token Authentication)

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
Email: ej@jlabs.test
Password: password123

---

## Requirements

Make sure the following are installed:

- PHP >= 8.2
- Composer
- MySQL
- Git

---

## Installation

Clone the repository:
```bash
git clone https://github.com/EJARNAD0/jlabs-api.git
cd jlabs-api
Install PHP dependencies:

bash
composer install
Copy environment file:

bash
cp .env.example .env
Generate application key:

bash
php artisan key:generate
Database Setup
Create a MySQL database:

sql
CREATE DATABASE jlabs_exam;
Update your .env file:

env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jlabs_exam
DB_USERNAME=root
DB_PASSWORD=
Run migrations and seed the default user:

bash
php artisan migrate --seed
Running the Application
Start the development server:

bash
php artisan serve
API will be available at:

text
http://localhost:8000
API Endpoints
Authentication
Method	Endpoint	Description	Request Body
POST	/api/login	Authenticate user and return token	email, password
POST	/api/logout	Invalidate current token	Requires Bearer token
GET	/api/user	Get authenticated user info	Requires Bearer token
IP Search History
Method	Endpoint	Description	Request Body
GET	/api/history	Get user's search history	Requires Bearer token
POST	/api/history	Save IP address search	ip_address
DELETE	/api/history	Delete multiple history records	ids (array of history IDs)
API Usage Examples
Login
bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"ej@jlabs.test","password":"password123"}'
Response:

json
{
  "token": "1|abcdefghijklmnopqrstuvwxyz123456",
  "user": {
    "id": 1,
    "email": "ej@jlabs.test",
    "created_at": "2024-01-01T00:00:00.000000Z"
  }
}
Get User Info
bash
curl -X GET http://localhost:8000/api/user \
  -H "Authorization: Bearer {your-token}"
Save IP Search
bash
curl -X POST http://localhost:8000/api/history \
  -H "Authorization: Bearer {your-token}" \
  -H "Content-Type: application/json" \
  -d '{"ip_address":"8.8.8.8"}'
Get Search History
bash
curl -X GET http://localhost:8000/api/history \
  -H "Authorization: Bearer {your-token}"
Delete Multiple History Records
bash
curl -X DELETE http://localhost:8000/api/history \
  -H "Authorization: Bearer {your-token}" \
  -H "Content-Type: application/json" \
  -d '{"ids":[1,2,3]}'
Logout
bash
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer {your-token}"
Project Structure
text
jlabs-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   └── HistoryController.php
│   │   └── Middleware/
│   ├── Models/
│   │   ├── User.php
│   │   └── History.php
│   └── Providers/
├── database/
│   ├── migrations/
│   └── seeders/
│       └── DatabaseSeeder.php
├── routes/
│   └── api.php
└── .env
Error Handling
The API returns appropriate HTTP status codes:

200 - Success

201 - Resource created

400 - Bad request

401 - Unauthorized

404 - Not found

422 - Validation error

500 - Server error

Frontend Repository
The React frontend application that consumes this API can be found at:
https://github.com/EJARNAD0/jlabs-web

