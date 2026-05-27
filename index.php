<?php
declare(strict_types=1);
session_start();

ini_set("display_errors", "1");
error_reporting(E_ALL);

include("config.php");

$isLoggedIn = isset($_SESSION["user"]);

$search = trim($_GET["search"] ?? "");
$page = max(1, (int) ($_GET["page"] ?? 1));
$limit = 5;
$offset = ($page - 1) * $limit;
$searchTerm = "%" . $search . "%";

if ($search !== "") {
    $countStmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM posts WHERE title LIKE ? OR content LIKE ?");
    mysqli_stmt_bind_param($countStmt, "ss", $searchTerm, $searchTerm);
    mysqli_stmt_execute($countStmt);
    $countResult = mysqli_stmt_get_result($countStmt);

    $stmt = mysqli_prepare($conn, "SELECT posts.*, users.username AS author FROM posts LEFT JOIN users ON posts.user_id = users.id WHERE posts.title LIKE ? OR posts.content LIKE ? ORDER BY posts.created_at DESC LIMIT ? OFFSET ?");
    mysqli_stmt_bind_param($stmt, "ssii", $searchTerm, $searchTerm, $limit, $offset);
} else {
    $countStmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM posts");
    mysqli_stmt_execute($countStmt);
    $countResult = mysqli_stmt_get_result($countStmt);

    $stmt = mysqli_prepare($conn, "SELECT posts.*, users.username AS author FROM posts LEFT JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC LIMIT ? OFFSET ?");
    mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
}

$totalPosts = (int) mysqli_fetch_assoc($countResult)["total"];
$totalPages = max(1, (int) ceil($totalPosts / $limit));

if ($page > $totalPages) {
    $params = $search !== "" ? "?search=" . urlencode($search) . "&page=" . $totalPages : "?page=" . $totalPages;
    header("Location: index.php" . $params);
    exit;
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog App</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="topbar">
        <div>
            <h1>Blog App</h1>
            <p>
                <?php if ($isLoggedIn): ?>
                    Welcome <?php echo htmlspecialchars($_SESSION["user"]); ?> · <?php echo htmlspecialchars(currentUserRole()); ?>
                <?php else: ?>
                    Browse posts or login to manage your blog.
                <?php endif; ?>
            </p>
        </div>
        <nav>
            <?php if ($isLoggedIn): ?>
                <a class="button" href="create.php">Create Post</a>
                <a class="button secondary" href="logout.php">Logout</a>
            <?php else: ?>
                <a class="button" href="login.php">Login</a>
                <a class="button secondary" href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="content">
        <section class="dashboard-head">
            <div>
                <p class="eyebrow">Posts</p>
                <h2><?php echo $search !== "" ? "Search results" : "Latest posts"; ?></h2>
                <p>
                    <?php echo $totalPosts; ?>
                    <?php echo $totalPosts === 1 ? "post" : "posts"; ?>
                    <?php if ($search !== ""): ?>
                        found for "<?php echo htmlspecialchars($search); ?>"
                    <?php endif; ?>
                </p>
            </div>

            <form class="search-form" method="GET">
                <input
                    type="search"
                    name="search"
                    value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Search title or content">
                <button type="submit">Search</button>
                <?php if ($search !== ""): ?>
                    <a class="button secondary" href="index.php">Clear</a>
                <?php endif; ?>
            </form>
        </section>

        <?php if (mysqli_num_rows($result) === 0): ?>
            <section class="panel empty-state">
                <h2>No matching posts</h2>
                <p><?php echo $search !== "" ? "Try a different search keyword." : "Create your first post to test the CRUD flow."; ?></p>
                <a class="button" href="create.php">Create Post</a>
            </section>
        <?php endif; ?>

        <section class="post-list">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <article class="panel post">
                    <div class="post-header">
                        <div>
                            <h2><?php echo htmlspecialchars($row["title"]); ?></h2>
                            <time><?php echo htmlspecialchars($row["created_at"]); ?></time>
                            <span class="author">By <?php echo htmlspecialchars($row["author"] ?? "Unknown"); ?></span>
                        </div>
                        <?php if ($isLoggedIn && canManagePost($row)): ?>
                            <div class="actions">
                                <a href="edit.php?id=<?php echo (int) $row["id"]; ?>">Edit</a>
                                <a class="danger" href="delete.php?id=<?php echo (int) $row["id"]; ?>" onclick="return confirm('Delete this post?');">Delete</a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <p><?php echo nl2br(htmlspecialchars($row["content"])); ?></p>
                </article>
            <?php endwhile; ?>
        </section>

        <?php if ($totalPages > 1): ?>
            <nav class="pagination" aria-label="Post pages">
                <?php
                    $queryPrefix = $search !== "" ? "search=" . urlencode($search) . "&" : "";
                    $previousPage = max(1, $page - 1);
                    $nextPage = min($totalPages, $page + 1);
                ?>

                <a class="<?php echo $page === 1 ? "disabled" : ""; ?>" href="index.php?<?php echo $queryPrefix; ?>page=<?php echo $previousPage; ?>">Previous</a>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a class="<?php echo $i === $page ? "active" : ""; ?>" href="index.php?<?php echo $queryPrefix; ?>page=<?php echo $i; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>

                <a class="<?php echo $page === $totalPages ? "disabled" : ""; ?>" href="index.php?<?php echo $queryPrefix; ?>page=<?php echo $nextPage; ?>">Next</a>
            </nav>
        <?php endif; ?>
    </main>
</body>
</html>
