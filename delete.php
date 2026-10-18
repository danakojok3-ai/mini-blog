<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

start_session();

// DELETE: POST only. A link (GET) must never delete, because browsers and
// crawlers can follow links without the visitor meaning to.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Method not allowed.');
}

if (!csrf_valid($_POST['csrf'] ?? null)) {
    http_response_code(400);
    flash('error', 'Your session expired. Please try again.');
    redirect('index.php');
}

$id = read_id($_POST['id'] ?? null);
if ($id > 0 && delete_post($id)) {
    flash('success', 'Post deleted.');
} else {
    flash('error', 'That post could not be found.');
}
redirect('index.php');
