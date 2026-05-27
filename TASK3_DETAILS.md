# Task 3: Search, Pagination, and UI Improvements

## Completed Features

- Added a search form on the home page.
- Users can search posts by title or content.
- Search queries use prepared statements for safer SQL.
- Added pagination on the posts listing page.
- The page displays 5 posts per page.
- Pagination links keep the search keyword when browsing search results.
- Improved the user interface with cleaner layout, cards, form styling, hover states, and responsive design.

## Updated Files

- `index.php`
  - Reads `search` and `page` from the URL.
  - Counts matching posts.
  - Displays only 5 posts per page.
  - Shows Previous, numbered page links, and Next.
  - Displays search results by matching title or content.

- `style.css`
  - Improved page background, header, cards, forms, buttons, search form, post cards, and pagination.
  - Added responsive layout for mobile screens.

## Demo Steps

1. Start MAMP.
2. Start Apache and MySQL.
3. Open `http://127.0.0.1:8888/blog-app/register.php`.
4. Register or login.
5. Create several posts.
6. Use the search box on the home page.
7. Search by a word in the title.
8. Search by a word in the content.
9. Create more than 5 posts to show pagination.
10. Click page numbers, Previous, and Next.
11. Show the improved UI.

## Final URLs

- Register: `http://127.0.0.1:8888/blog-app/register.php`
- Login: `http://127.0.0.1:8888/blog-app/login.php`
- Home/Search/Pagination: `http://127.0.0.1:8888/blog-app/index.php`
