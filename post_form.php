<?php
/**
 * Shared form for creating and editing a post.
 * Expects: $values (title, body), $errors, $action (URL), $submitLabel, $cancelUrl
 */
?>
<form action="<?= e($action) ?>" method="post" novalidate>
    <?= csrf_field() ?>

    <div class="field">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" maxlength="<?= TITLE_MAX ?>"
               value="<?= e($values['title']) ?>" required
               class="<?= isset($errors['title']) ? 'invalid' : '' ?>">
        <?php if (isset($errors['title'])): ?>
            <p class="error"><?= e($errors['title']) ?></p>
        <?php endif; ?>
    </div>

    <div class="field">
        <label for="body">Post</label>
        <textarea id="body" name="body" rows="10" maxlength="<?= BODY_MAX ?>" required
                  class="<?= isset($errors['body']) ? 'invalid' : '' ?>"><?= e($values['body']) ?></textarea>
        <?php if (isset($errors['body'])): ?>
            <p class="error"><?= e($errors['body']) ?></p>
        <?php endif; ?>
    </div>

    <div class="actions">
        <button type="submit" class="btn"><?= e($submitLabel) ?></button>
        <a class="btn btn-ghost" href="<?= e($cancelUrl) ?>">Cancel</a>
    </div>
</form>
