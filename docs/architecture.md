# Documentation de l'Architecture Sandstorm

## Vue d'ensemble
Sandstorm est une application de marketplace moderne construite avec PHP 8.2+ utilisant un framework MVC personnalisé (LightMVC). Ce document explique l'architecture, les composants et comment étendre l'application.

## Structure du Projet
```
Sandstorm/
├── controllers/         # Contrôleurs pour la logique métier
├── models/             # Modèles pour la gestion de la base de données
├── views/              # Templates Twig pour l'interface
├── database/          # Schéma et migrations de la base de données
├── middlewares/       # Middlewares (auth, etc.)
├── public/            # Ressources publiques (CSS, JS, images)
├── uploads/           # Fichiers uploadés par les utilisateurs
├── vendor/           # Dépendances Composer
├── .htaccess         # Règles de réécriture Apache
├── composer.json     # Dépendances du projet
└── index.php         # Point d'entrée de l'application
```

## Composants Principaux

### 1. Système de Routage (AltoRouter)
- Toutes les routes sont définies dans `index.php`
- Format : `$router->map(METHODE, CHEMIN, CALLBACK)`
- Exemple :
```php
$router->map('GET', '/category/[*:slug]', function($slug) {
    $categoryController = new CategoryController(Database::getInstance());
    $categoryController->view($slug);
});
```

### 2. Couche Base de Données
- Située dans `database/Database.php`
- Utilise PDO pour les connexions
- Pattern Singleton pour la gestion des connexions
- Exemple d'utilisation :
```php
$db = Database::getInstance();
$stmt = $db->prepare("SELECT * FROM users");
```

### 3. Modèles
Tous les modèles étendent la classe `Model` et gèrent les opérations de base de données :
```php
class UserModel extends Model {
    public function __construct($db) {
        parent::__construct($db, 'users');
    }
}
```

Modèles Principaux :
- `UserModel` : Authentification et gestion des utilisateurs
- `CategoryModel` : Opérations sur les catégories
- `ListingModel` : Annonces du marketplace
- `MessageModel` : Système de messagerie

### 4. Contrôleurs
Les contrôleurs étendent la classe `Controller` :
```php
class HomeController extends Controller {
    public function index() {
        $data = [...];
        $this->render("home.html.twig", $data);
    }
}
```

Contrôleurs Principaux :
- `HomeController` : Pages principales
- `UserController` : Auth & profil
- `ListingController` : CRUD des annonces
- `CategoryController` : Vues des catégories

### 5. Vues (Twig)
- Situées dans `views/`
- Utilise le moteur de template Twig
- Template de base : `base.html.twig`
- Exemple :
```twig
{% extends "base.html.twig" %}
{% block content %}
    <h1>{{ title }}</h1>
{% endblock %}
```

### 6. Authentification
- Gérée par `AuthMiddleware`
- Authentification basée sur les sessions
- Exemple de route protégée :
```php
AuthMiddleware::auth(); // Redirige vers login si non authentifié
```

## Schéma de Base de Données

### Tables Principales :
1. `users`
   - `id` : Clé primaire
   - `username`, `mail`, `pass` : Détails utilisateur
   - `created_at`, `updated_at` : Horodatages

2. `categories`
   - `id` : Clé primaire
   - `name`, `slug`, `icon` : Détails catégorie
   - `parent_id` : Pour catégories hiérarchiques

3. `listings`
   - `id` : Clé primaire
   - `title`, `description`, `price`
   - `user_id`, `category_id` : Clés étrangères
   - `status` : enum('active','sold','expired','draft')

4. `listing_images`
   - `id` : Clé primaire
   - `listing_id` : Clé étrangère
   - `image_path`, `is_primary`

## Ajouter de Nouvelles Fonctionnalités

### 1. Créer un Nouveau Modèle
```php
namespace Models;

class NewModel extends Model {
    public function __construct($db) {
        parent::__construct($db, 'table_name');
    }
    
    public function customMethod() {
        // Votre logique ici
    }
}
```

### 2. Créer un Nouveau Contrôleur
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
            "title" => "Nouvelle Fonctionnalité"
        ]);
    }
}
```

### 3. Ajouter de Nouvelles Routes
Dans `index.php` :
```php
$router->map('GET', '/nouvelle-fonction', function() {
    $db = Database::getInstance();
    $controller = new NewController($db);
    $controller->index();
});
```

### 4. Créer des Vues
Créer un nouveau template Twig dans `views/` :
```twig
{% extends "base.html.twig" %}
{% block content %}
    <!-- Votre HTML ici -->
{% endblock %}
```

## Tâches Courantes

### Ajouter l'Authentification à une Route
```php
$router->map('GET', '/route-protegee', function() {
    AuthMiddleware::auth(); // Requiert l'authentification
    $controller = new Controller(Database::getInstance());
    $controller->method();
});
```

### Travailler avec les Fichiers
Répertoire d'upload : `uploads/`
```php
// Dans un contrôleur
$uploadDir = 'uploads/listings/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}
move_uploaded_file($tmpName, $uploadDir . $filename);
```

### Ajouter des Catégories
Utiliser le SQL dans `database/base.sql` :
```sql
INSERT INTO categories (name, slug, icon, description) 
VALUES ('Nouvelle Catégorie', 'nouvelle-categorie', 'bi-icon', 'Description');
```

## Considérations de Sécurité

1. **Prévention des Injections SQL**
   - Toujours utiliser des requêtes préparées
   - Ne jamais concaténer les chaînes SQL

2. **Prévention XSS**
   - Twig échappe automatiquement
   - Utiliser `{{ var|raw }}` uniquement si nécessaire

3. **Protection CSRF**
   - Implémenter des tokens CSRF dans les formulaires
   - Valider les tokens dans les requêtes POST

4. **Sécurité des Uploads**
   - Valider les types de fichiers
   - Utiliser des noms de fichiers sécurisés
   - Définir les bonnes permissions

## Déploiement

1. Configurer votre serveur web (Apache/Nginx)
2. Configurer la base de données avec `database/base.sql`
3. Mettre à jour les identifiants de base de données
4. Assurer les bonnes permissions de fichiers
5. Configurer le reporting d'erreurs pour la production

## Dépannage

### Problèmes Courants :

1. **Erreurs 404**
   - Vérifier la configuration .htaccess
   - Vérifier les définitions de routes
   - Vérifier les permissions de fichiers

2. **Problèmes de Connexion Base de Données**
   - Vérifier les identifiants
   - Vérifier le statut du serveur de base de données
   - Consulter les logs d'erreurs

3. **Problèmes d'Upload**
   - Vérifier les permissions des répertoires
   - Vérifier les paramètres PHP d'upload
   - Vérifier les limites de taille de fichiers
