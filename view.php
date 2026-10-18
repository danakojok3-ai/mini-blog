<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

start_session();

$id   = read_id($_GET['id'] ?? null);
$post = $id > 0 ? get_post($id) : null;
if ($post === null) {
    not_found();
}

$pageTitle = $post['title'];
require __DIR__ . '/includes/header.php';
?>
<article class="card single">
    <h1><?= e($post['title']) ?></h1>
    <p class="date">
        Published <?= e(format_date($post['created_at'])) ?>
        <?php if ($post['updated_at']): ?>
            · Edited <?= e(format_date($post['updated_at'])) ?>
        <?php endif; ?>
    </p>

    <!-- nl2br after escaping keeps the author's line breaks, safely. -->
    <div class="body"><?= nl2br(e($post['body'])) ?></div>

    <div class="actions">
        <a class="btn" href="edit.php?id=<?= (int)$post['id'] ?>">Edit</a>

        <!-- Deleting changes data, so it is a POST form with a CSRF token. -->
        <form action="delete.php" method="post"
              onsubmit="return confirm('Delete this post? This cannot be undone.');">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int)$post['id'] ?>">
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>

        <a class="btn btn-ghost" href="index.php">Back</a>
    </div>
</article>
<?php require __DIR__ . '/includes/footer.php'; ?>
