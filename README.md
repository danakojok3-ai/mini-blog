# Mini Blog (CRUD)

A small blog built with PHP and MySQL. You can create, read, edit and delete posts. Every save is validated on the server, and all database access uses prepared statements.

## Features

- **Create, read, update, delete (CRUD)** for blog posts, with a paginated home page.
- **Server-side validation** on every save (title 3-120 characters, post 10-5000 characters), with errors shown next to the field and the visitor's text kept in the form.
- **SQL injection protection.** Every query is a PDO prepared statement; user input is never pasted into SQL.
- **XSS protection.** All output is escaped with `htmlspecialchars`. Line breaks in posts are kept safely by escaping first, then converting them.
- **CSRF protection.** Create, edit and delete forms carry a session token. Deleting is POST-only, never a plain link.
- **Redirect after POST** with one-time flash messages ("Post published", "Post deleted").
- **Credentials kept out of Git.** Database settings live in `config.php`, which is ignored by `.gitignore`.

## Project structure

```
mini-blog/
├── index.php            List of posts (paginated)
├── view.php             One post, with Edit and Delete
├── create.php           New post form + handler
├── edit.php             Edit form + handler
├── delete.php           Delete handler (POST only)
├── config.example.php   Copy to config.php and add your DB details
├── schema.sql           Creates the database, table and sample posts
├── includes/
│   ├── db.php           PDO connection and all queries
│   ├── helpers.php      Escaping, CSRF, flash messages, validation
│   ├── header.php
│   ├── footer.php
│   └── post_form.php    Form shared by create and edit
└── assets/style.css
```

## Run it locally

You need PHP 8.1+ and MySQL or MariaDB (XAMPP, MAMP or Laragon all include both).

1. **Create the database.** In phpMyAdmin open the *Import* tab and import `schema.sql`, or run:
   ```bash
   mysql -u root -p < schema.sql
   ```
2. **Add your settings.**
   ```bash
   cp config.example.php config.php
   ```
   Edit `config.php` with your database host, name, user and password.
3. **Start the server.**
   ```bash
   cd projects/mini-blog
   php -S localhost:8000
   ```
   Open <http://localhost:8000>.

## Deploy to a PHP host

1. Create a MySQL database in the host's control panel and note the host name, database name, user and password (the host is usually *not* `localhost` on free hosts).
2. Import `schema.sql` through phpMyAdmin. If the host already created the database for you, remove the first two lines (`CREATE DATABASE` and `USE`) and select your database before importing.
3. Upload the project files and create `config.php` on the server with those details.

> GitHub Pages cannot run PHP or MySQL, so this project needs a PHP host or a local server.

## What I practised

Relational data with MySQL, PDO prepared statements, the full CRUD cycle, server-side validation, session-based CSRF tokens, pagination, and keeping secrets out of version control.
