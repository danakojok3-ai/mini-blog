<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

start_session();

$values = ['title' => '', 'body' => ''];
$errors = [];

// CREATE: handle the submitted form.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid($_POST['csrf'] ?? null)) {
        http_response_code(400);
        flash('error', 'Your session expired. Please try again.');
        redirect('create.php');
    }

    [$values, $errors] = validate_post($_POST);

    if (!$errors) {
        $newId = create_post($values['title'], $values['body']);
        flash('success', 'Post published.');
        redirect('view.php?id=' . $newId);
    }
}

$pageTitle = 'New post';
require __DIR__ . '/includes/header.php';
?>
<div class="card">
    <h1>New post</h1>
    <?php
    $action      = 'create.php';
    $submitLabel = 'Publish';
    $cancelUrl   = 'index.php';
    require __DIR__ . '/includes/post_form.php';
    ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
