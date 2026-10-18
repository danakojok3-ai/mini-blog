<?php
/**
 * Small helpers: escaping, CSRF, flash messages, validation, redirects.
 */

declare(strict_types=1);

const TITLE_MIN = 3;
const TITLE_MAX = 120;
const BODY_MIN  = 10;
const BODY_MAX  = 5000;

/** Escape a value for safe output inside HTML. */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function start_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
        session_start();
    }
}

/** Redirect and stop. 303 makes the browser use GET for the next request. */
function redirect(string $url): never
{
    header('Location: ' . $url, true, 303);
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_valid(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], $token);
}

/** Hidden input to put inside every form that changes data. */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

/** Store a one-time message for the next page. */
function flash(string $type, string $text): void
{
    $_SESSION['flash'] = ['type' => $type, 'text' => $text];
}

/** Read and clear the stored message. */
function take_flash(): ?array
{
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function text_length(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}

/**
 * Server-side validation for a post (used by create and edit).
 *
 * @return array{0: array{title:string, body:string}, 1: array<string,string>}
 */
function validate_post(array $input): array
{
    $title = trim((string)($input['title'] ?? ''));
    // A title is one line: flatten any line breaks.
    $title = trim(str_replace(["\r", "\n"], ' ', $title));
    $body  = trim((string)($input['body'] ?? ''));
    $errors = [];

    $tl = text_length($title);
    if ($title === '') {
        $errors['title'] = 'Please enter a title.';
    } elseif ($tl < TITLE_MIN) {
        $errors['title'] = 'Title must be at least ' . TITLE_MIN . ' characters.';
    } elseif ($tl > TITLE_MAX) {
        $errors['title'] = 'Title must be ' . TITLE_MAX . ' characters or fewer.';
    }

    $bl = text_length($body);
    if ($body === '') {
        $errors['body'] = 'Please write something in the post.';
    } elseif ($bl < BODY_MIN) {
        $errors['body'] = 'Post must be at least ' . BODY_MIN . ' characters.';
    } elseif ($bl > BODY_MAX) {
        $errors['body'] = 'Post must be ' . BODY_MAX . ' characters or fewer.';
    }

    return [['title' => $title, 'body' => $body], $errors];
}

/** Read a positive integer id from the query string or POST data. */
function read_id(mixed $value): int
{
    $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $id === false ? 0 : $id;
}

/** Short preview of a post body for the list page. */
function excerpt(string $text, int $max = 180): string
{
    $text = trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
    if (text_length($text) <= $max) {
        return $text;
    }
    $cut = function_exists('mb_substr') ? mb_substr($text, 0, $max, 'UTF-8') : substr($text, 0, $max);
    return rtrim($cut) . '…';
}

/** Show a stored date as "18 Oct 2026". */
function format_date(?string $date): string
{
    if (!$date) {
        return '';
    }
    $ts = strtotime($date);
    return $ts ? date('j M Y', $ts) : '';
}

/** Send a 404 page and stop. */
function not_found(): never
{
    http_response_code(404);
    $pageTitle = 'Post not found';
    require __DIR__ . '/header.php';
    echo '<div class="card"><h1>Post not found</h1><p>That post does not exist or was deleted.</p>'
       . '<p><a class="btn" href="index.php">Back to all posts</a></p></div>';
    require __DIR__ . '/footer.php';
    exit;
}
