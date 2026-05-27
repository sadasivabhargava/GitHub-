<?php
declare(strict_types=1);
session_start();
include("config.php");

$message = "";
$errors = [];
$username = trim($_POST["username"] ?? "");

if (isset($_POST["submit"])) {
    $password = $_POST["password"] ?? "";

    if ($username === "") {
        $errors[] = "Username is required.";
    } elseif (!preg_match("/^[A-Za-z0-9_]{3,30}$/", $username)) {
        $errors[] = "Username must be 3-30 characters and use only letters, numbers, or underscores.";
    }

    if ($password === "") {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if (!$errors) {
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($check, "s", $username);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $errors[] = "Username already exists.";
        } else {
            $countResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users");
            $userCount = (int) mysqli_fetch_assoc($countResult)["total"];
            $role = $userCount === 0 ? "admin" : "editor";
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $username, $hashedPassword, $role);

            if (mysqli_stmt_execute($stmt)) {
                $message = "Registered successfully as {$role}. You can now login.";
                $username = "";
            } else {
                $errors[] = "Registration failed.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Blog App</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="auth-page">
        <section class="panel auth-panel">
            <h1>Create Account</h1>

            <?php foreach ($errors as $error): ?>
                <p class="message error"><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>

            <?php if ($message !== ""): ?>
                <p class="message"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <form method="POST">
                <label>
                    Username
                    <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" placeholder="Username" minlength="3" maxlength="30" pattern="[A-Za-z0-9_]+" required>
                </label>

                <label>
                    Password
                    <input type="password" name="password" placeholder="Password" minlength="6" required>
                </label>

                <button name="submit">Register</button>
            </form>

            <p class="small-link">Already registered? <a href="login.php">Login</a></p>
        </section>
    </main>
</body>
</html>
