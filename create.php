<?php
declare(strict_types=1);
session_start();
include("config.php");
requireLogin();

$errors = [];
$title = trim($_POST["title"] ?? "");
$content = trim($_POST["content"] ?? "");

if (isset($_POST["save"])) {
    if ($title === "") {
        $errors[] = "Title is required.";
    } elseif (strlen($title) > 255) {
        $errors[] = "Title must be 255 characters or fewer.";
    }

    if ($content === "") {
        $errors[] = "Content is required.";
    } elseif (strlen($content) < 10) {
        $errors[] = "Content must be at least 10 characters.";
    }

    if (!$errors) {
        $userId = currentUserId();
        $stmt = mysqli_prepare($conn, "INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iss", $userId, $title, $content);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php");
            exit;
        }

        $errors[] = "Post could not be saved.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post | Blog App</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="topbar">
        <h1>Create Post</h1>
        <nav>
            <a class="button secondary" href="index.php">Back</a>
        </nav>
    </header>

    <main class="content narrow">
        <section class="panel">
            <?php foreach ($errors as $error): ?>
                <p class="message error"><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>

            <form method="POST">
                <label>
                    Title
                    <input name="title" value="<?php echo htmlspecialchars($title); ?>" placeholder="Post title" maxlength="255" required>
                </label>

                <label>
                    Content
                    <textarea name="content" placeholder="Write your post" minlength="10" required><?php echo htmlspecialchars($content); ?></textarea>
                </label>

                <button name="save">Save</button>
            </form>
        </section>
    </main>
</body>
</html>
