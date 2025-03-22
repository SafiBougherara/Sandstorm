<?php
namespace Controllers;
use Models\UserModel;

class UserController extends Controller
{
    private UserModel $userModel;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->userModel = new UserModel($db);
    }

    /**
     * Display login page and handle login
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['mail'] ?? '';
            $password = $_POST['pass'] ?? '';

            if (!empty($email) && !empty($password)) {
                $userId = $this->userModel->authenticate($email, $password);

                if ($userId) {
                    $user = $this->userModel->getUserById($userId);
                    $_SESSION['user_id'] = $user->id;
                    $_SESSION['username'] = $user->username;
                    $_SESSION['email'] = $user->mail;
                    
                    header('Location: /Sandstorm/');
                    exit;
                } else {
                    $error = 'Invalid email or password';
                }
            } else {
                $error = 'Please fill in all fields';
            }
        }

        $data = [
            "title" => "Login",
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
            $email = $_POST['mail'] ?? '';
            $password = $_POST['pass'] ?? '';
            $passwordConfirm = $_POST['pass_confirm'] ?? '';

            if (!empty($username) && !empty($email) && !empty($password) && !empty($passwordConfirm)) {
                if ($password === $passwordConfirm) {
                    if (strlen($password) >= 8) {
                        try {
                            // Check if email already exists
                            if ($this->userModel->getUserByEmail($email)) {
                                $error = 'Email already registered';
                            } else {
                                $userId = $this->userModel->register($username, $email, $password);

                                if ($userId) {
                                    $user = $this->userModel->getUserById($userId);
                                    $_SESSION['user_id'] = $user->id;
                                    $_SESSION['username'] = $user->username;
                                    $_SESSION['email'] = $user->mail;
                                    
                                    header('Location: /Sandstorm/');
                                    exit;
                                } else {
                                    $error = 'Registration failed';
                                }
                            }
                        } catch (\PDOException $e) {
                            $error = 'Registration error: ' . $e->getMessage();
                        }
                    } else {
                        $error = 'Password must be at least 8 characters long';
                    }
                } else {
                    $error = 'Passwords do not match';
                }
            } else {
                $error = 'Please fill in all fields';
            }
        }

        $data = [
            "title" => "Register",
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
}
