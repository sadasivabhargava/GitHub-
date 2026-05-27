<?php
declare(strict_types=1);

mysqli_report(MYSQLI_REPORT_OFF);

$database = "blog";
$connections = [
    ["127.0.0.1", "root", "root", 8889],
    ["localhost", "root", "root", 8889],
    ["localhost", "root", "", 3306],
    ["127.0.0.1", "root", "", 3306],
];

$conn = false;

foreach ($connections as [$host, $user, $password, $port]) {
    $conn = mysqli_connect($host, $user, $password, "", $port);

    if ($conn) {
        break;
    }
}

if (!$conn) {
    die("Connection failed. Start MySQL in MAMP, then refresh this page.");
}

mysqli_set_charset($conn, "utf8mb4");

mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

if (!mysqli_select_db($conn, $database)) {
    die("Connection failed. Could not select the blog database.");
}

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'editor') NOT NULL DEFAULT 'editor'
)");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (user_id)
)");

ensureColumn($conn, "users", "role", "ALTER TABLE users ADD role ENUM('admin', 'editor') NOT NULL DEFAULT 'editor'");
ensureColumn($conn, "posts", "user_id", "ALTER TABLE posts ADD user_id INT NULL AFTER id");
ensureAdminUser($conn);
backfillPostOwners($conn);

function ensureColumn(mysqli $conn, string $table, string $column, string $sql): void
{
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
    mysqli_stmt_bind_param($stmt, "ss", $table, $column);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if ((int) $row["total"] === 0) {
        mysqli_query($conn, $sql);
    }
}

function ensureAdminUser(mysqli $conn): void
{
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'admin'");
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $adminCount = (int) mysqli_fetch_assoc($result)["total"];

    if ($adminCount === 0) {
        $update = mysqli_prepare($conn, "UPDATE users SET role = 'admin' WHERE id = (SELECT id FROM (SELECT id FROM users ORDER BY id ASC LIMIT 1) AS first_user)");
        mysqli_stmt_execute($update);
    }
}

function backfillPostOwners(mysqli $conn): void
{
    $stmt = mysqli_prepare($conn, "UPDATE posts SET user_id = (SELECT id FROM (SELECT id FROM users ORDER BY id ASC LIMIT 1) AS first_user) WHERE user_id IS NULL");
    mysqli_stmt_execute($stmt);
}

function currentUserId(): int
{
    return (int) ($_SESSION["user_id"] ?? 0);
}

function currentUserRole(): string
{
    return $_SESSION["role"] ?? "guest";
}

function isAdmin(): bool
{
    return currentUserRole() === "admin";
}

function requireLogin(): void
{
    if (!isset($_SESSION["user_id"])) {
        header("Location: login.php");
        exit;
    }
}

function canManagePost(array $post): bool
{
    return isAdmin() || ((int) ($post["user_id"] ?? 0) === currentUserId());
}
?>
