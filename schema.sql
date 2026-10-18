-- Mini Blog database schema (MySQL / MariaDB)
-- Import this file in phpMyAdmin (Import tab) or run:
--   mysql -u root -p < schema.sql

CREATE DATABASE IF NOT EXISTS mini_blog
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE mini_blog;

CREATE TABLE IF NOT EXISTS posts (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    title      VARCHAR(120) NOT NULL,
    body       TEXT         NOT NULL,
    created_at DATETIME     NOT NULL,
    updated_at DATETIME     NULL,
    PRIMARY KEY (id),
    KEY idx_posts_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional sample data so the home page is not empty.
INSERT INTO posts (title, body, created_at) VALUES
    ('Welcome to my mini blog',
     'This blog is a small PHP and MySQL project. You can create, edit and delete posts, and every save is validated on the server.',
     NOW()),
    ('What I learned about prepared statements',
     'Never put user input directly into an SQL string. Prepared statements keep data and SQL separate, which blocks SQL injection.',
     NOW());
