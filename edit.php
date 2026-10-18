<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

start_session();

// The id comes from the URL on first load and the form action stays the same.
$id   = read_id($_GET['id'] ?? null);
$post = $id > 0 ? get_post($id) : null;
if ($post === null) {
    not_found();
}

$values = ['title' => $post['title'], 'body' => $post['body']];
$errors = [];

// UPDATE: handle the submitted form.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid($_POST['csrf'] ?? null)) {
        http_response_code(400);
        flash('error', 'Your session expired. Please try again.');
        redirect('edit.php?id=' . $id);
    }

    [$values, $errors] = validate_post($_POST);

    if (!$errors) {
        update_post($id, $values['title'], $values['body']);
        flash('success', 'Post updated.');
        redirect('view.php?id=' . $id);
    }
}

$pageTitle = 'Edit post';
require __DIR__ . '/includes/header.php';
?>
<div class="card">
    <h1>Edit post</h1>
    <?php
    $action      = 'edit.php?id=' . $id;
    $submitLabel = 'Save changes';
    $cancelUrl   = 'view.php?id=' . $id;
    require __DIR__ . '/includes/post_form.php';
    ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
