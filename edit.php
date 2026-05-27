<?php
declare(strict_types=1);
session_start();
include("config.php");
requireLogin();

$id = (int) ($_GET["id"] ?? 0);

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM posts WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$post = mysqli_fetch_assoc($result);

if (!$post || !canManagePost($post)) {
    http_response_code(403);
    die("Access denied.");
}

$errors = [];
$title = trim($_POST["title"] ?? $post["title"]);
$content = trim($_POST["content"] ?? $post["content"]);

if (isset($_POST["update"])) {
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
        $update = mysqli_prepare($conn, "UPDATE posts SET title = ?, content = ? WHERE id = ?");
        mysqli_stmt_bind_param($update, "ssi", $title, $content, $id);

        if (mysqli_stmt_execute($update)) {
            header("Location: index.php");
            exit;
        }

        $errors[] = "Post could not be updated.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post | Blog App</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="topbar">
        <h1>Edit Post</h1>
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
                    <input name="title" value="<?php echo htmlspecialchars($title); ?>" maxlength="255" required>
                </label>

                <label>
                    Content
                    <textarea name="content" minlength="10" required><?php echo htmlspecialchars($content); ?></textarea>
                </label>

                <button name="update">Update</button>
            </form>
        </section>
    </main>
</body>
</html>
