# Hekate: A Configurable API Gateway

Hekate is a Laravel-based API gateway offering dynamic route management, authentication, and authorization. Built with Laravel Octane and Sanctum, it enables efficient, secure, and scalable proxying for microservices.

Features:
- Dynamic Route Management: Configure proxy routes via a database.
- Authentication & Authorization: Secure routes using Laravel Sanctum tokens and custom policies.
- Caching & Performance: Leverages Laravel Octane and Redis for enhanced speed.
- Extensibility: Customizable for advanced routing and admin interfaces.

Installation
	1.	Clone the repository:

```
git clone https://github.com/dreadkopp/hekate.git
cd hekate
```

	2.	Install dependencies:
```
composer install
npm install
```

	3.	Build and start

```
docker compose up -d
```

Configure database and other Laravel settings.

	4.	Run migrations:
```
./art migrate
```


Usage
- Define Routes: Add entries to the routes table:
  - source_path: The API endpoint exposed to clients.
  - target_url: Backend service URL.
- Authentication: Use Sanctum tokens for protected endpoints.

Roadmap
- Advanced authorization rules
- Enhanced caching and routing strategies
- Admin management interface

Contributing

Pull requests are welcome! Please check the contribution guide.

For more details, visit the GitHub repository.

Feel free to expand this with specifics from the source code.
