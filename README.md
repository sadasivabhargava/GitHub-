# Blog App

A simple PHP and MySQL web application with CRUD operations and user authentication.

## Features

- Register users
- Login and logout users
- Password hashing with `password_hash`
- Session-based authentication
- Create, read, update, and delete blog posts

## Database

Database name:

```sql
blog
```

Tables:

```sql
users (id, username, password)
posts (id, title, content, created_at)
```

Use `database.sql` to create the database and tables.

## MAMP Settings

Default MAMP MySQL settings used in `config.php`:

```text
Host: localhost
Port: 8889
Username: root
Password: root
Database: blog
```

Open the app:

```text
http://localhost:8888/blog-app
```
