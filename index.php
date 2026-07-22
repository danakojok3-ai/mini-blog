<?php
require 'config.php';

// READ — fetch all posts, newest first
$stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mini Blog</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
</head>
<body>
  <main class="wrap">

    <?php if (isset($_GET['status']) && $_GET['status'] === 'deleted'): ?>
      <div class="success-banner">Post deleted.</div>
    <?php elseif (isset($_GET['status']) && $_GET['status'] === 'saved'): ?>
      <div class="success-banner">Post saved.</div>
    <?php endif; ?>

    <div class="page-header">
      <div>
        <div class="eyebrow">Mini Blog</div>
        <h1>All posts</h1>
      </div>
      <a href="form.php" class="btn btn-primary">+ New post</a>
    </div>

    <div class="post-list">
      <?php if (empty($posts)): ?>
        <div class="empty-state">No posts yet — create your first one.</div>
      <?php else: ?>
        <?php foreach ($posts as $post): ?>
          <article class="post-card">
            <h2 class="post-title"><?= htmlspecialchars($post['title']) ?></h2>
            <div class="post-meta">
              Posted <?= date('M j, Y \a\t g:i A', strtotime($post['created_at'])) ?>
              <?php if ($post['updated_at'] !== $post['created_at']): ?>
                · edited <?= date('M j, Y \a\t g:i A', strtotime($post['updated_at'])) ?>
              <?php endif; ?>
            </div>
            <div class="post-body"><?= nl2br(htmlspecialchars($post['body'])) ?></div>
            <div class="post-actions">
              <a href="form.php?id=<?= $post['id'] ?>" class="btn btn-ghost">Edit</a>
              <form action="delete.php" method="POST" onsubmit="return confirm('Delete this post? This can\'t be undone.');">
                <input type="hidden" name="id" value="<?= $post['id'] ?>">
                <button type="submit" class="btn btn-danger">Delete</button>
              </form>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </main>
</body>
</html>
