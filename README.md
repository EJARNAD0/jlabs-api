# Laravel API Application

A RESTful API built with the Laravel framework, featuring user authentication via Laravel Sanctum and history management capabilities.

## Features

- **Authentication**: Secure user registration and login using Laravel Sanctum.
- **History Management**: Track and manage history records (API endpoints available).
- **API Documentation**: Clearly defined API endpoints.

## Prerequisites

Ensure you have the following installed on your local machine:

- [PHP](https://www.php.net/) >= 8.2
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) & NPM

## Installation

1.  **Clone the repository:**

    ```bash
    git clone <repository-url>
    cd <project-directory>
    ```

2.  **Install PHP dependencies:**

    ```bash
    composer install
    ```

3.  **Install JavaScript dependencies:**

    ```bash
    npm install
    ```

4.  **Set up environment variables:**

    Copy the example environment file and configure your database settings.

    ```bash
    cp .env.example .env
    ```

    Update the `.env` file with your database credentials:

    ```ini
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database_name
    DB_USERNAME=your_database_user
    DB_PASSWORD=your_database_password
    ```

5.  **Generate application key:**

    ```bash
    php artisan key:generate
    ```

6.  **Run migrations:**

    Create the necessary database tables.

    ```bash
    php artisan migrate
    ```

7.  **Build frontend assets (optional):**

    ```bash
    npm run build
    ```

## Usage

Start the development server:

```bash
php artisan serve
```

The application will be accessible at `http://localhost:8000`.

## API Documentation

The API routes are prefixed with `/api`.

### Authentication

| Method | Endpoint | Description | Auth Required |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/login` | Authenticate user and receive an API token. | No |
| `GET` | `/api/me` | Retrieve the authenticated user's details. | Yes |
| `POST` | `/api/logout` | Invalidate the current API token. | Yes |

### History Management

| Method | Endpoint | Description | Auth Required |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/histories` | Retrieve a list of history records. | Yes |
| `POST` | `/api/histories` | Create a new history record. | Yes |
| `DELETE` | `/api/histories` | Bulk delete history records. | Yes |

## Testing

Run the test suite using Artisan:

```bash
php artisan test
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
