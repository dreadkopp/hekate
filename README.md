# Hekate API Gateway

Hekate is a Laravel-based API Gateway designed for dynamic route management, authentication, and authorization. It is tailored for microservice architectures, providing seamless integration and high performance through Laravel Octane and Redis.

## Features

- **Dynamic Routing**: Configure routes via the database, allowing for real-time updates without redeployment.
- **Authentication & Authorization**: Powered by Laravel Sanctum, enabling secure token-based authentication and fine-grained access control.
- **High Performance**: Built with Laravel Octane and Redis for efficient request handling and low latency.
- **Containerized Deployment**: Includes Docker support for consistent and straightforward deployment.
- **Extensibility**: Easily customizable to fit specific project needs.

## Installation

### Prerequisites

- PHP >= 8.1
- Composer
- Docker & Docker Compose (optional, for containerized deployment)
- Redis

### Steps

1. Clone the repository:
   ```bash
   git clone https://github.com/dreadkopp/hekate.git
   cd hekate
   ```

2. Install dependencies:
   ```bash
   composer install
   npm install
   ```

3. Configure environment variables:
   Create a new `.env` file in the project root and configure it as needed. Refer to the `config` directory or Laravel documentation for required settings.
   ```bash
   touch .env
   ```

4. Generate application key:
   ```bash
   php artisan key:generate
   ```

5. Run migrations:
   ```bash
   php artisan migrate
   ```

6. Start the application:
   - Locally:
     ```bash
     php artisan serve
     ```
   - With Docker:
     ```bash
     docker-compose up -d
     ```

## Usage

### Dynamic Routes
Routes are managed in the database. Use the following command to show routes:
```bash
php artisan route:list
```
Extend the rounting using Tinker
```bash
php artisan tinker
```
Inside Tinker:
```php
$routing = new App\Models\Routing();
$routing->path = '/foo';
$routing->endpoint = 'https://api.foo.example';
$rourting->save();
```

### Authentication
The API gateway uses Laravel Sanctum for authentication. To issue tokens:
```bash
php artisan tinker
```
Inside Tinker:
```php
$user = App\Models\User::find(1);
$token = $user->createToken('API Token', ['/foo/*'])->plainTextToken;
```
Use the generated token in API requests:
```http
Authorization: Bearer <token>
```

### Testing
Run the test suite with:
```bash
php artisan test
```

## Project Structure

- `app`: Core application logic (controllers, models, middleware, etc.).
- `routes`: Route definitions (e.g., API routes).
- `config`: Application configuration files.
- `public`: Public-facing assets and entry points.
- `tests`: Automated test cases.
- `Dockerfile` & `docker-compose.yml`: Configuration for containerized deployment.

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository.
2. Create a new branch:
   ```bash
   git checkout -b feature/your-feature
   ```
3. Commit your changes:
   ```bash
   git commit -m "Add your feature"
   ```
4. Push to the branch:
   ```bash
   git push origin feature/your-feature
   ```
5. Open a pull request.

## License

Hekate is open-source software licensed under the [MIT license](LICENSE).

## Contact

For any questions or issues, please open an issue on the [GitHub repository](https://github.com/dreadkopp/hekate/issues).

