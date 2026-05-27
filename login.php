<?php
declare(strict_types=1);
session_start();
include("config.php");

if (isset($_SESSION["user"])) {
    header("Location: index.php");
    exit;
}

$message = "";
$username = trim($_POST["username"] ?? "");

if (isset($_POST["login"])) {
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $message = "Username and password are required.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, username, password, role FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user["password"])) {
            session_regenerate_id(true);
            $_SESSION["user"] = $user["username"];
            $_SESSION["user_id"] = (int) $user["id"];
            $_SESSION["role"] = $user["role"];
            header("Location: index.php");
            exit;
        }

        $message = "Wrong username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Blog App</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="auth-page">
        <section class="panel auth-panel">
            <h1>Login</h1>

            <?php if ($message !== ""): ?>
                <p class="message error"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <form method="POST">
                <label>
                    Username
                    <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" minlength="3" maxlength="30" required>
                </label>

                <label>
                    Password
                    <input type="password" name="password" minlength="6" required>
                </label>

                <button name="login">Login</button>
            </form>

            <p class="small-link">Need an account? <a href="register.php">Register</a></p>
        </section>
    </main>
</body>
</html>
