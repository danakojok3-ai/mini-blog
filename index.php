<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

start_session();

// READ: list posts, newest first, with simple pagination.
$perPage = max(1, (int)(app_config()['per_page'] ?? 5));
$total   = count_posts();
$pages   = max(1, (int)ceil($total / $perPage));
$page    = min(max(1, read_id($_GET['page'] ?? 1) ?: 1), $pages);
$posts   = get_posts($perPage, ($page - 1) * $perPage);

$pageTitle = 'All posts';
require __DIR__ . '/includes/header.php';
?>
<h1>All posts</h1>

<?php if (!$posts): ?>
    <div class="card empty">
        <p>No posts yet.</p>
        <a class="btn" href="create.php">Write the first post</a>
    </div>
<?php endif; ?>

<?php foreach ($posts as $post): ?>
    <article class="card post">
        <h2><a href="view.php?id=<?= (int)$post['id'] ?>"><?= e($post['title']) ?></a></h2>
        <p class="date"><?= e(format_date($post['created_at'])) ?></p>
        <p><?= e(excerpt($post['body'])) ?></p>
        <a class="more" href="view.php?id=<?= (int)$post['id'] ?>">Read more →</a>
    </article>
<?php endforeach; ?>

<?php if ($pages > 1): ?>
    <nav class="pager" aria-label="Pagination">
        <?php if ($page > 1): ?>
            <a class="btn btn-ghost btn-small" href="index.php?page=<?= $page - 1 ?>">← Newer</a>
        <?php endif; ?>
        <span>Page <?= $page ?> of <?= $pages ?></span>
        <?php if ($page < $pages): ?>
            <a class="btn btn-ghost btn-small" href="index.php?page=<?= $page + 1 ?>">Older →</a>
        <?php endif; ?>
    </nav>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
