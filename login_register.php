<?php
session_start();
require_once 'config.php';

if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Validate inputs
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $_SESSION['register_error'] = 'All fields are required!';
        $_SESSION['active_form'] = 'register';
        header("Location: index.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['register_error'] = 'Invalid email format!';
        $_SESSION['active_form'] = 'register';
        header("Location: index.php");
        exit();
    }

    // Check if email exists
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $_SESSION['register_error'] = 'Email is already registered!';
        $_SESSION['active_form'] = 'register';
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insert = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $insert->bind_param("ssss", $name, $email, $hashed_password, $role);
        
        if ($insert->execute()) {
            $_SESSION['register_success'] = 'Registration successful! Please login.';
            $_SESSION['active_form'] = 'login';
        } else {
            $_SESSION['register_error'] = 'Registration failed! Please try again.';
            $_SESSION['active_form'] = 'register';
        }
        $insert->close();
    }
    $stmt->close();
    header("Location: index.php");
    exit();
}

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['name'] = htmlspecialchars($user['name']);
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'admin') {
                header('Location: admin_page.php');
            } else {
                header('Location: user_page.php');
            }
            exit();
        }
    }
    
    $_SESSION['login_error'] = 'Incorrect email or password';
    $_SESSION['active_form'] = 'login';
    header('Location: index.php');
    exit();
}
?>