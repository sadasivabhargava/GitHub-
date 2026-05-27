<?php
declare(strict_types=1);

$database = "blog";
$connections = [
    ["localhost", "root", "", 3306],
    ["127.0.0.1", "root", "", 3306],
    ["localhost", "root", "root", 8889],
    ["127.0.0.1", "root", "root", 8889],
];

$conn = false;

foreach ($connections as [$host, $username, $password, $port]) {
    mysqli_report(MYSQLI_REPORT_OFF);
    $conn = mysqli_connect($host, $username, $password, "", $port);

    if ($conn) {
        break;
    }
}

if (!$conn) {
    die("MySQL connection failed. Start MySQL, then refresh this page.");
}

mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
mysqli_select_db($conn, $database);

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Complete | Blog App</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="auth-page">
        <section class="panel auth-panel">
            <h1>Database Ready</h1>
            <p class="message">The blog database and required tables are ready.</p>
            <a class="button" href="register.php">Register User</a>
        </section>
    </main>
</body>
</html>
