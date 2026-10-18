<?php
/**
 * Database connection and post queries.
 * Every query uses prepared statements, so user input is never pasted into SQL.
 */

declare(strict_types=1);

/** Load settings from config.php (or stop with a clear message). */
function app_config(): array
{
    static $config = null;
    if ($config === null) {
        $file = __DIR__ . '/../config.php';
        if (!is_file($file)) {
            http_response_code(500);
            exit('Missing config.php. Copy config.example.php to config.php and fill in your database details.');
        }
        $config = require $file;
    }
    return $config;
}

/** One shared PDO connection for the whole request. */
function db(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $c = app_config();

    // Tests can supply a full DSN (e.g. SQLite); normal use builds a MySQL one.
    $dsn = $c['dsn'] ?? sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        $c['db_host'],
        $c['db_name'],
        $c['db_charset'] ?? 'utf8mb4'
    );

    try {
        $pdo = new PDO($dsn, $c['db_user'] ?? null, $c['db_pass'] ?? null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $ex) {
        // Do not show the real error (it can contain host and user names).
        error_log('DB connection failed: ' . $ex->getMessage());
        http_response_code(500);
        exit('Could not connect to the database. Please check config.php.');
    }

    return $pdo;
}

/** Total number of posts (for pagination). */
function count_posts(): int
{
    return (int)db()->query('SELECT COUNT(*) FROM posts')->fetchColumn();
}

/** One page of posts, newest first. */
function get_posts(int $limit, int $offset): array
{
    $stmt = db()->prepare(
        'SELECT id, title, body, created_at, updated_at
         FROM posts
         ORDER BY created_at DESC, id DESC
         LIMIT :limit OFFSET :offset'
    );
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

/** A single post, or null if it does not exist. */
function get_post(int $id): ?array
{
    $stmt = db()->prepare(
        'SELECT id, title, body, created_at, updated_at FROM posts WHERE id = :id'
    );
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    return $row === false ? null : $row;
}

/** Insert a new post and return its id. */
function create_post(string $title, string $body): int
{
    $stmt = db()->prepare(
        'INSERT INTO posts (title, body, created_at) VALUES (:title, :body, :created)'
    );
    $stmt->execute([
        ':title'   => $title,
        ':body'    => $body,
        ':created' => date('Y-m-d H:i:s'),
    ]);
    return (int)db()->lastInsertId();
}

/** Update a post. Returns true if the post exists. */
function update_post(int $id, string $title, string $body): bool
{
    if (get_post($id) === null) {
        return false;
    }
    $stmt = db()->prepare(
        'UPDATE posts SET title = :title, body = :body, updated_at = :updated WHERE id = :id'
    );
    $stmt->execute([
        ':title'   => $title,
        ':body'    => $body,
        ':updated' => date('Y-m-d H:i:s'),
        ':id'      => $id,
    ]);
    return true;
}

/** Delete a post. Returns true if a row was removed. */
function delete_post(int $id): bool
{
    $stmt = db()->prepare('DELETE FROM posts WHERE id = :id');
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount() > 0;
}
