# Sandstorm Marketplace Architecture Documentation

## Overview
Sandstorm is a modern marketplace application built with PHP 8.2+ using a custom MVC framework (LightMVC). This document explains the architecture, components, and how to extend the application.

## Project Structure
```
Sandstorm/
├── controllers/         # Controllers handle business logic
├── models/             # Models manage database interactions
├── views/              # Twig templates for the UI
├── database/          # Database schema and migrations
├── middlewares/       # Request middleware (auth, etc.)
├── public/            # Public assets (CSS, JS, images)
├── uploads/           # User-uploaded files
├── vendor/           # Composer dependencies
├── .htaccess         # Apache URL rewriting rules
├── composer.json     # Project dependencies
└── index.php         # Application entry point
```

## Core Components

### 1. Routing System (AltoRouter)
- All routes are defined in `index.php`
- Format: `$router->map(METHOD, PATH, CALLBACK)`
- Example:
```php
$router->map('GET', '/category/[*:slug]', function($slug) {
    $categoryController = new CategoryController(Database::getInstance());
    $categoryController->view($slug);
});
```

### 2. Database Layer
- Located in `database/Database.php`
- Uses PDO for database connections
- Singleton pattern for connection management
- Example usage:
```php
$db = Database::getInstance();
$stmt = $db->prepare("SELECT * FROM users");
```

### 3. Models
All models extend the base `Model` class and handle database operations:
```php
class UserModel extends Model {
    public function __construct($db) {
        parent::__construct($db, 'users');
    }
}
```

Key Models:
- `UserModel`: Authentication and user management
- `CategoryModel`: Category operations
- `ListingModel`: Marketplace listings
- `MessageModel`: User messaging system

### 4. Controllers
Controllers extend the base `Controller` class:
```php
class HomeController extends Controller {
    public function index() {
        $data = [...];
        $this->render("home.html.twig", $data);
    }
}
```

Key Controllers:
- `HomeController`: Main pages
- `UserController`: Auth & profile
- `ListingController`: CRUD for listings
- `CategoryController`: Category views

### 5. Views (Twig)
- Located in `views/`
- Use Twig templating engine
- Base template: `base.html.twig`
- Example:
```twig
{% extends "base.html.twig" %}
{% block content %}
    <h1>{{ title }}</h1>
{% endblock %}
```

### 6. Authentication
- Handled by `AuthMiddleware`
- Session-based authentication
- Protected route example:
```php
AuthMiddleware::auth(); // Redirects to login if not authenticated
```

## Database Schema

### Key Tables:
1. `users`
   - `id`: Primary key
   - `username`, `mail`, `pass`: User details
   - `created_at`, `updated_at`: Timestamps

2. `categories`
   - `id`: Primary key
   - `name`, `slug`, `icon`: Category details
   - `parent_id`: For hierarchical categories

3. `listings`
   - `id`: Primary key
   - `title`, `description`, `price`
   - `user_id`, `category_id`: Foreign keys
   - `status`: enum('active','sold','expired','draft')

4. `listing_images`
   - `id`: Primary key
   - `listing_id`: Foreign key
   - `image_path`, `is_primary`

## Adding New Features

### 1. Creating a New Model
```php
namespace Models;

class NewModel extends Model {
    public function __construct($db) {
        parent::__construct($db, 'table_name');
    }
    
    public function customMethod() {
        // Your logic here
    }
}
```

### 2. Creating a New Controller
```php
namespace Controllers;

class NewController extends Controller {
    private NewModel $model;
    
    public function __construct($db) {
        parent::__construct($db);
        $this->model = new NewModel($db);
    }
    
    public function index() {
        $this->render("new/index.html.twig", [
            "title" => "New Feature"
        ]);
    }
}
```

### 3. Adding New Routes
In `index.php`:
```php
$router->map('GET', '/new-feature', function() {
    $db = Database::getInstance();
    $controller = new NewController($db);
    $controller->index();
});
```

### 4. Creating Views
Create a new Twig template in `views/`:
```twig
{% extends "base.html.twig" %}
{% block content %}
    <!-- Your HTML here -->
{% endblock %}
```

## Common Tasks

### Adding Authentication to a Route
```php
$router->map('GET', '/protected-route', function() {
    AuthMiddleware::auth(); // Require authentication
    $controller = new Controller(Database::getInstance());
    $controller->method();
});
```

### Working with Files
Upload directory: `uploads/`
```php
// In a controller
$uploadDir = 'uploads/listings/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}
move_uploaded_file($tmpName, $uploadDir . $filename);
```

### Adding Categories
Use the SQL in `database/base.sql`:
```sql
INSERT INTO categories (name, slug, icon, description) 
VALUES ('New Category', 'new-category', 'bi-icon', 'Description');
```

## Security Considerations

1. **SQL Injection Prevention**
   - Always use prepared statements
   - Never concatenate SQL strings

2. **XSS Prevention**
   - Twig auto-escapes by default
   - Use `{{ var|raw }}` only when necessary

3. **CSRF Protection**
   - Implement CSRF tokens in forms
   - Validate tokens in POST requests

4. **File Upload Security**
   - Validate file types
   - Use secure file names
   - Set proper permissions

## Deployment

1. Configure your web server (Apache/Nginx)
2. Set up the database using `database/base.sql`
3. Update database credentials
4. Ensure proper file permissions
5. Configure error reporting for production

## Troubleshooting

### Common Issues:

1. **404 Errors**
   - Check .htaccess configuration
   - Verify route definitions
   - Check file permissions

2. **Database Connection Issues**
   - Verify credentials
   - Check database server status
   - Review error logs

3. **Upload Issues**
   - Check directory permissions
   - Verify PHP upload settings
   - Check file size limits
