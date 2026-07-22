<?php
require 'config.php';

$editing = false;
$title = '';
$body = '';
$id = null;
$errors = [];

// If an id is present, we're editing an existing post — load it
if (isset($_GET['id'])) {
    $editing = true;
    $id = (int) $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$id]);
    $post = $stmt->fetch();

    if (!$post) {
        header("Location: index.php");
        exit;
    }
    $title = $post['title'];
    $body = $post['body'];
}

// Handle form submission (both create and update use this same block)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $body  = trim($_POST['body'] ?? '');
    $id    = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;
    $editing = $id !== null;

    if (strlen($title) < 3) {
        $errors[] = 'Title must be at least 3 characters.';
    }
    if (strlen($body) < 10) {
        $errors[] = 'Post body must be at least 10 characters.';
    }

    if (empty($errors)) {
        if ($editing) {
            // UPDATE
            $stmt = $pdo->prepare("UPDATE posts SET title = ?, body = ? WHERE id = ?");
            $stmt->execute([$title, $body, $id]);
        } else {
            // CREATE
            $stmt = $pdo->prepare("INSERT INTO posts (title, body) VALUES (?, ?)");
            $stmt->execute([$title, $body]);
        }
        header("Location: index.php?status=saved");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $editing ? 'Edit Post' : 'New Post' ?> — Mini Blog</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
</head>
<body>
  <main class="wrap">
    <div class="eyebrow">Mini Blog</div>
    <h1><?= $editing ? 'Edit post' : 'New post' ?></h1>
    <p class="lede"><?= $editing ? 'Update the title or body below.' : 'Write something and publish it to the blog.' ?></p>

    <form method="POST" class="card" style="margin-top: 28px;">
      <?php if (!empty($errors)): ?>
        <div class="error-banner"><?= implode(' ', array_map('htmlspecialchars', $errors)) ?></div>
      <?php endif; ?>

      <?php if ($editing): ?>
        <input type="hidden" name="id" value="<?= $id ?>">
      <?php endif; ?>

      <div class="field">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" required>
      </div>

      <div class="field">
        <label for="body">Body</label>
        <textarea id="body" name="body" required><?= htmlspecialchars($body) ?></textarea>
      </div>

      <div style="display:flex; gap:12px;">
        <button type="submit" class="btn btn-primary"><?= $editing ? 'Save changes' : 'Publish post' ?></button>
        <a href="index.php" class="btn btn-ghost">Cancel</a>
      </div>
    </form>
  </main>
</body>
</html>
