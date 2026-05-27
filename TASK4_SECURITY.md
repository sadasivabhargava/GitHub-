# Task 4: Security, Validation, and Roles

## Security Measures Implemented

- All user input used in SQL is handled with MySQLi prepared statements.
- Passwords are stored with `password_hash()` and checked with `password_verify()`.
- Login regenerates the session ID with `session_regenerate_id(true)`.
- Output is escaped with `htmlspecialchars()` to reduce XSS risk.
- Create, edit, and delete pages require login.
- Edit and delete actions check post ownership or admin role.
- The database schema now supports user roles and post ownership.

## Form Validation

- Registration validates username and password on the server.
- Login validates required fields on the server.
- Create and edit post forms validate title and content on the server.
- HTML validation attributes were added for a better client-side experience:
  - `required`
  - `minlength`
  - `maxlength`
  - `pattern`

## Roles and Permissions

- The first registered user becomes `admin`.
- Later registered users become `editor`.
- Admin users can edit and delete any post.
- Editor users can create posts and edit/delete only their own posts.
- Guests can view and search posts but cannot create, edit, or delete.

## Updated Files

- `config.php`
  - Creates/updates `users.role`.
  - Creates/updates `posts.user_id`.
  - Adds helper functions for login and permissions.

- `register.php`
  - Adds role assignment.
  - Adds validation.
  - Uses prepared statements.

- `login.php`
  - Stores role in session.
  - Uses prepared statements.
  - Regenerates session ID on login.

- `index.php`
  - Shows role in the header.
  - Shows post author.
  - Hides edit/delete links unless the user has permission.
  - Keeps search and pagination with prepared statements.

- `create.php`, `edit.php`, `delete.php`
  - Require login.
  - Validate inputs.
  - Enforce ownership/admin permissions.
  - Use prepared statements.

## Demo Steps

1. Register a first user and show that the first account becomes admin.
2. Register another user and show that the account becomes editor.
3. Login as editor and create a post.
4. Confirm the editor can edit/delete their own post.
5. Login as another editor and confirm they cannot edit/delete another user's post.
6. Login as admin and confirm admin can manage all posts.
7. Show search and pagination still working.
