# Full_Stack_Dev_Book_Website
Books Management/BookStore Website
========================

Overview:
---------
This is a PHP/MySQL-based website to manage a library of books. It allows you to:
- List all books
- Add new books
- Edit existing books
- Delete books
- View book details
- Auto-display book covers using Google Books API (optional)
- Search books in real-time
- Explore books using Bootstrap AJAX dropdown and modal demos

The website includes Nepali popular books as well as international books across genres such as Sci-Fi, Classic, Love, Fantasy, Contemporary, and Nepali literature.

Requirements:
-------------
- PHP 7.x or higher
- MySQL 5.x or higher
- Web server (e.g., Apache, Nginx)
- Internet connection for optional cover image fetching
- Bootstrap 5.3 for styling and modals

Installation:
-------------
1. Create a MySQL database (e.g., `db2415267`).
2. Import the `books` table structure and initial book data.
3. Update `db_connect.php` with your MySQL credentials:
   - Host
   - Username
   - Password
   - Database name
4. Upload all PHP files and the `templates` folder to your web server.
5. Open `index.php` in your browser to access the homepage.

File Structure:
---------------
- db_connect.php               -> Database connection
- index.php                     -> Homepage showing welcome and links
- list_books.php               -> Main page listing all books
- add_form.php / add.php        -> Add new books
- edit.php                      -> Edit existing books
- delete.php                    -> Delete books
- details.php                   -> View individual book details
- search.php                    -> Real-time search functionality
- bootstrap-ajax-dropdown.html -> Demo dropdown listing books via AJAX
- bootstrap-ajax-modal.html    -> Demo modal showing books via AJAX
- audit_log.php                 -> Logs all add/edit/delete actions
- templates/                    -> Contains header.php and footer.php
- style.css                     -> Custom styling
- README.txt                    -> This file

Usage:
------
1. Open `index.php` as the homepage. Navigate to:
   - All Books (`list_books.php`)
   - Add Book (`add_form.php`)
   - Search (`search.php`)
   - AJAX Modal & Dropdown demos
2. Use the table in `list_books.php` to view, edit, or delete books.
3. Covers are automatically fetched from Google Books API if available.
4. AJAX dropdown and modal pages (`bootstrap-ajax-dropdown.html` and `bootstrap-ajax-modal.html`) dynamically fetch book data without page reloads.
5. All changes (add/edit/delete) are logged in `audit_log` for admin tracking.

Notes:
------
- Book IDs are auto-incremented.
- Cover images require an internet connection.
- Audit log ensures traceability of actions.
- The website uses Bootstrap 5.3 for responsive design.
- Nepali books and international books are mixed across genres.

Author:
-------
Hari Thapa
