# 🌪️ Sandstorm Marketplace

A modern, feature-rich marketplace platform built with PHP 8.2+ and MySQL. Sandstorm allows users to buy and sell items in various categories with a beautiful, responsive interface.

![Sandstorm Screenshot](docs/screenshot.png)

## ✨ Features

- 🛍️ **Rich Marketplace Features**
  - Browse items by category
  - Advanced search with filters
  - Real-time messaging between users
  - Secure payment integration
  - User ratings and reviews

- 👤 **User Management**
  - Secure authentication
  - User profiles
  - Seller dashboards
  - Favorites/watchlist

- 📱 **Modern UI/UX**
  - Responsive Bootstrap 5 design
  - Clean and intuitive interface
  - Mobile-first approach
  - Bootstrap Icons integration

## 🚀 Quick Start

1. **Prerequisites**
   ```bash
   PHP 8.2+
   MySQL 8.0+
   Composer
   ```

2. **Clone & Install**
   ```bash
   git clone https://github.com/yourusername/Sandstorm.git
   cd Sandstorm
   composer install
   ```

3. **Database Setup**
   ```bash
   # Import the database schema
   mysql -u root < database/base.sql
   ```

4. **Configuration**
   ```php
   # Update database credentials in database/Database.php
   'host' => 'localhost',
   'dbname' => 'sandstorm',
   'user' => 'root',
   'pass' => ''
   ```

5. **Run the Application**
   ```bash
   # Using PHP's built-in server
   php -S localhost:8000
   
   # Or configure with Apache/Nginx
   # Point to the project root directory
   ```

## 🏗️ Architecture

Sandstorm follows the MVC pattern with a clean, modular architecture:

```
Sandstorm/
├── controllers/    # Business logic
├── models/        # Database operations
├── views/         # Twig templates
├── database/     # Schema & migrations
└── public/       # Static assets
```

For detailed architecture documentation, see [Architecture Guide](docs/architecture.md)

## 💡 Key Technologies

- **Backend**: PHP 8.2+
- **Database**: MySQL 8.0+
- **Routing**: AltoRouter
- **Templates**: Twig
- **Frontend**: Bootstrap 5
- **Icons**: Bootstrap Icons
- **Dependencies**: Composer

## 🛠️ Development

### Running Tests
```bash
composer test
```

### Code Style
```bash
composer cs-fix
```

### Adding Features
1. Create relevant model in `models/`
2. Add controller in `controllers/`
3. Create Twig templates in `views/`
4. Define routes in `index.php`

## 📝 Documentation

- [Architecture Guide](docs/architecture.md)
- [API Documentation](docs/api.md)
- [Contributing Guide](CONTRIBUTING.md)
- [Security Policy](SECURITY.md)

## 🤝 Contributing

Contributions are welcome! Please read our [Contributing Guide](CONTRIBUTING.md) for details.

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Bootstrap team for the amazing UI framework
- Twig team for the templating engine
- AltoRouter for the routing system
- All our contributors and users!
