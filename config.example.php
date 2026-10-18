<?php
/**
 * Database settings.
 *
 * 1. Copy this file to config.php
 * 2. Fill in your own values
 *
 * config.php is listed in .gitignore, so your real password never reaches GitHub.
 */

declare(strict_types=1);

return [
    // On most local setups the host is "localhost" or "127.0.0.1".
    // On free hosts it is usually a separate name shown in the control panel
    // (for example "sql123.example.com").
    'db_host'    => 'localhost',
    'db_name'    => 'mini_blog',
    'db_user'    => 'root',
    'db_pass'    => '',
    'db_charset' => 'utf8mb4',

    // Posts shown per page on the home page.
    'per_page'   => 5,
];
