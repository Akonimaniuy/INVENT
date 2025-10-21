<?php
require_once 'models/User.php';
require_once 'config/Database.php';

class UserController {
    private $db;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $full_name = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            $errors = [];

            if (empty($full_name)) {
                $errors[] = 'Full Name is required.';
            }
            if (empty($email)) {
                $errors[] = 'Email is required.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format.';
            }
            if (empty($password)) {
                $errors[] = 'Password is required.';
            } elseif (strlen($password) < 6) { // Example: minimum password length
                $errors[] = 'Password must be at least 6 characters long.';
            }
            if ($password !== $confirm_password) {
                $errors[] = 'Passwords do not match.';
            }

            if (empty($errors)) {
                // You might want to add a check here if the email already exists before attempting to register
                if ($this->user->register($full_name, $email, $password)) {
                    $_SESSION['message'] = 'Registration successful! Please log in.';
                    header('Location: index.php?action=login');
                    exit;
                } else {
                    $errors[] = 'Registration failed. The email might already be in use.';
                }
            }
            // If there are errors, store them in session and redirect back to landing
            $_SESSION['form_errors'] = ['type' => 'register', 'errors' => $errors, 'post' => $_POST];
            header('Location: index.php?action=landing');
            exit;
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $this->user->login($_POST['email'], $_POST['password']);
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email']; // Store email in session
                $_SESSION['user_full_name'] = $user['full_name']; // Store full name in session
                header('Location: index.php?action=index'); // Redirect to product list
                exit;
            } else {
                // If login fails, store error in session and redirect back to landing
                $_SESSION['form_errors'] = ['type' => 'login', 'error' => 'Invalid credentials'];
                header('Location: index.php?action=landing');
                exit;
            }
        }
    }

    public function logout() {
        session_destroy();
        header('Location: index.php?action=login');
        exit;
    }

    public function landing() {
        // Check for form errors from a failed login/register attempt
        $form_errors = $_SESSION['form_errors'] ?? null;
        if ($form_errors) {
            unset($_SESSION['form_errors']);
        }
        // This will make $form_errors available in the landing.php view
        require_once 'views/landing.php';
    }
}
?>