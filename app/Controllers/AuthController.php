<?php
namespace App\Controllers;

use App\Models\User;

class AuthController {
    
    public function showLogin() {
        require_once __DIR__ . '/../Views/auth/login.php';
    }

    public function showRegister() {
        require_once __DIR__ . '/../Views/auth/register.php';
    }

    public function login() {
        session_start();
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // Server-side validation
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = "All fields are required!";
            header('Location: /login');
            exit;
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['first_name'];
            header('Location: /dashboard');
        } else {
            $_SESSION['error'] = "Invalid email or password!";
            header('Location: /login');
        }
    }

    public function register() {
        session_start();
        $firstName = $_POST['first_name'] ?? '';
        $lastName = $_POST['last_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // Basic validation
        if (strlen($password) < 6) {
            $_SESSION['error'] = "Password must be at least 6 characters!";
            header('Location: /register');
            exit;
        }

        $userModel = new User();
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $result = $userModel->create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password_hash' => $hashedPassword,
            'role_id' => 3, // Default to Staff
            'status' => 1
        ]);

        if ($result) {
            header('Location: /login');
        } else {
            $_SESSION['error'] = "Registration failed!";
            header('Location: /register');
        }
    }
}