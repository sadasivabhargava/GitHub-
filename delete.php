<?php
declare(strict_types=1);
session_start();
include("config.php");
requireLogin();

$id = (int) ($_GET["id"] ?? 0);

if ($id > 0) {
    $select = mysqli_prepare($conn, "SELECT * FROM posts WHERE id = ?");
    mysqli_stmt_bind_param($select, "i", $id);
    mysqli_stmt_execute($select);
    $result = mysqli_stmt_get_result($select);
    $post = mysqli_fetch_assoc($result);

    if ($post && canManagePost($post)) {
        $stmt = mysqli_prepare($conn, "DELETE FROM posts WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
    }
}

header("Location: index.php");
exit;
?>
