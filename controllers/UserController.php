<?php
namespace Controllers;
use Models\UserModel;
use Models\ListingModel;

class UserController extends Controller
{
    private UserModel $userModel;
    private ListingModel $listingModel;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->userModel = new UserModel($db);
        $this->listingModel = new ListingModel($db);
    }

    /**
     * Display login page and handle login
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (!empty($email) && !empty($password)) {
                $userId = $this->userModel->authenticate($email, $password);

                if ($userId) {
                    $user = $this->userModel->getUserById($userId);
                    $_SESSION['user_id'] = $user->id;
                    $_SESSION['username'] = $user->username;
                    $_SESSION['email'] = $user->email;
                    
                    header('Location: /Sandstorm/');
                    exit;
                } else {
                    $error = 'Email ou mot de passe invalide';
                }
            } else {
                $error = 'Veuillez remplir tous les champs';
            }
        }

        $data = [
            "title" => "Connexion",
            "error" => $error ?? null
        ];

        $this->render("login.html.twig", $data);
    }

    /**
     * Display registration page and handle registration
     */
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';

            if (!empty($username) && !empty($email) && !empty($password) && !empty($passwordConfirm)) {
                if ($password === $passwordConfirm) {
                    if (strlen($password) >= 8) {
                        try {
                            if ($this->userModel->getUserByEmail($email)) {
                                $error = 'Email déjà enregistré';
                            } else {
                                $userId = $this->userModel->register($username, $email, $password);

                                if ($userId) {
                                    $user = $this->userModel->getUserById($userId);
                                    $_SESSION['user_id'] = $user->id;
                                    $_SESSION['username'] = $user->username;
                                    $_SESSION['email'] = $user->email;
                                    
                                    header('Location: /Sandstorm/');
                                    exit;
                                } else {
                                    $error = 'Échec de l\'inscription';
                                }
                            }
                        } catch (\PDOException $e) {
                            $error = 'Erreur d\'inscription: ' . $e->getMessage();
                        }
                    } else {
                        $error = 'Le mot de passe doit contenir au moins 8 caractères';
                    }
                } else {
                    $error = 'Les mots de passe ne correspondent pas';
                }
            } else {
                $error = 'Veuillez remplir tous les champs';
            }
        }

        $data = [
            "title" => "Inscription",
            "error" => $error ?? null
        ];

        $this->render("register.html.twig", $data);
    }

    /**
     * Handle user logout
     */
    public function logout()
    {
        session_destroy();
        header('Location: /Sandstorm/');
        exit;
    }

    /**
     * Display admin panel
     */
    public function admin()
    {
        // Only accessible if user is logged in (handled by AuthMiddleware)
        $data = [
            "title" => "Admin Panel",
            "users" => $this->userModel->getAllUsers()
        ];

        $this->render("admin.html.twig", $data);
    }

    /**
     * Display user dashboard
     */
    public function dashboard()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /Sandstorm/login');
            exit;
        }

        $user = $this->userModel->getUserById($_SESSION['user_id']);
        $listings = $this->listingModel->getUserListings($_SESSION['user_id']);

        $data = [
            "title" => "Dashboard",
            "user" => $user,
            "listings" => $listings
        ];

        $this->render("user/dashboard.html.twig", $data);
    }

    /**
     * Display user profile
     */
    public function profile()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /Sandstorm/login');
            exit;
        }

        $user = $this->userModel->getUserById($_SESSION['user_id']);

        $data = [
            "title" => "Profile",
            "user" => $user
        ];

        $this->render("user/profile.html.twig", $data);
    }

    /**
     * Display user's listings
     */
    public function myListings()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /Sandstorm/login');
            exit;
        }

        $listings = $this->listingModel->getUserListings($_SESSION['user_id']);

        $data = [
            "title" => "My Listings",
            "listings" => $listings
        ];

        $this->render("user/my-listings.html.twig", $data);
    }
}
