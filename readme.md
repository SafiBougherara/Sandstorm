# 🌪️ Sandstorm Marketplace

A modern, feature-rich marketplace platform built with PHP 8.2+ and MySQL. Sandstorm allows users to buy and sell items in various categories with a beautiful, responsive interface.

Une plateforme de marketplace moderne construite avec PHP 8.2+ et MySQL. Sandstorm permet aux utilisateurs d'acheter et de vendre des articles dans diverses catégories avec une interface belle et responsive.

![Sandstorm Screenshot](docs/screenshot.png)
![Capture d'écran Sandstorm](docs/screenshot.png)

## ✨ Features

- 🛍️ **Rich Marketplace Features**
  - Browse items by category
  - Advanced search with filters
  - Real-time messaging between users
  - Secure payment integration
  - User ratings and reviews

- 🛍️ **Fonctionnalités Marketplace Complètes**
  - Navigation par catégories
  - Recherche avancée avec filtres
  - Messagerie en temps réel
  - Paiements sécurisés
  - Système d'évaluation et avis

- 👤 **User Management**
  - Secure authentication
  - User profiles
  - Seller dashboards
  - Favorites/watchlist

- 👤 **Gestion des Utilisateurs**
  - Authentification sécurisée
  - Profils utilisateurs
  - Tableau de bord vendeur
  - Liste de favoris

- 📱 **Modern UI/UX**
  - Responsive Bootstrap 5 design
  - Clean and intuitive interface
  - Mobile-first approach
  - Bootstrap Icons integration

- 📱 **Interface Moderne**
  - Design responsive avec Bootstrap 5
  - Interface claire et intuitive
  - Approche "mobile-first"
  - Intégration Bootstrap Icons

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

   ```bash
   # Importer le schéma
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

   ```php
   # Mettre à jour les identifiants dans database/Database.php
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

   ```bash
   # Avec le serveur PHP intégré
   php -S localhost:8000
   
   # Ou configurer avec Apache/Nginx
   # Pointer vers le répertoire racine du projet
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

Sandstorm suit le pattern MVC avec une architecture modulaire :

```
Sandstorm/
├── controllers/    # Logique métier
├── models/        # Opérations base de données
├── views/         # Templates Twig
├── database/     # Schéma & migrations
└── public/       # Ressources statiques
```

For detailed architecture documentation, see [Architecture Guide](docs/architecture.md)

Pour une documentation détaillée, voir le [Guide d'Architecture](docs/architecture.md)

## 💡 Key Technologies

- **Backend**: PHP 8.2+
- **Database**: MySQL 8.0+
- **Routing**: AltoRouter
- **Templates**: Twig
- **Frontend**: Bootstrap 5
- **Icons**: Bootstrap Icons
- **Dependencies**: Composer

- **Backend** : PHP 8.2+
- **Base de données** : MySQL 8.0+
- **Routage** : AltoRouter
- **Templates** : Twig
- **Frontend** : Bootstrap 5
- **Icônes** : Bootstrap Icons
- **Dépendances** : Composer

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

### Tests
```bash
composer test
```

### Style de Code
```bash
composer cs-fix
```

### Ajouter des Fonctionnalités
1. Créer le modèle dans `models/`
2. Ajouter le contrôleur dans `controllers/`
3. Créer les templates Twig dans `views/`
4. Définir les routes dans `index.php`

## 📝 Documentation

- [Architecture Guide](docs/architecture.md)
- [API Documentation](docs/api.md)
- [Contributing Guide](CONTRIBUTING.md)
- [Security Policy](SECURITY.md)

- [Guide d'Architecture](docs/architecture.md)
- [Documentation API](docs/api.md)
- [Guide de Contribution](CONTRIBUTING.md)
- [Politique de Sécurité](SECURITY.md)

## 🤝 Contributing

Contributions are welcome! Please read our [Contributing Guide](CONTRIBUTING.md) for details.

Les contributions sont les bienvenues ! Veuillez lire notre [Guide de Contribution](CONTRIBUTING.md) pour plus de détails.

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

Ce projet est sous licence MIT - voir le fichier [LICENSE](LICENSE) pour plus de détails.

## 🙏 Acknowledgments

- Bootstrap team for the amazing UI framework
- Twig team for the templating engine
- AltoRouter for the routing system
- All our contributors and users!

- L'équipe Bootstrap pour leur superbe framework UI
- L'équipe Twig pour le moteur de templates
- AltoRouter pour le système de routage
- Tous nos contributeurs et utilisateurs !
