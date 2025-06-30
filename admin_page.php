<?php
session_start();
require_once 'config.php';

if(!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin'){
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="box">
        <h1>Welcome, <span><?= htmlspecialchars($_SESSION['name']); ?></span></h1>
        <p>This is an <span>Admin</span> page</p>
        <button onclick="window.location.href='logout.php'">Logout</button>
    </div>
</body>
</html>