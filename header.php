<?php
/** Shared page header. Set $pageTitle before including this file. */
$flash = take_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(($pageTitle ?? 'Mini Blog') . ' — Mini Blog') ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="site-header">
    <div class="wrap bar">
        <a class="logo" href="index.php">mini<span>.</span>blog</a>
        <a class="btn btn-small" href="create.php">+ New post</a>
    </div>
</header>
<main class="wrap">
<?php if ($flash): ?>
    <div class="alert alert-<?= e($flash['type']) ?>" role="status"><?= e($flash['text']) ?></div>
<?php endif; ?>
