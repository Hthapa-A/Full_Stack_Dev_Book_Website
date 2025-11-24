BookStore Web Application - README

Author:
-------
Hari Thapa/2415267

Overview:
---------
BookStore is a dynamic web application for managing and browsing books. 
It is built using PHP, MySQL, AJAX, and Bootstrap for a modern, responsive interface. 
The platform allows users to view, add, edit, delete, and search for books efficiently. 
It also includes interactive components like modals and dropdowns powered by AJAX for a seamless user experience.

Features:
---------
1. Homepage:
   - Lists all books with cover images fetched via Google Books API.
   - Responsive table layout showing Title, Author, Genre, Year, and Actions.

2. Book Management:
   - Add new books with title, author, genre, and published year.
   - Edit existing books and update details.
   - Delete books with confirmation prompts.
   - Full audit logging for add, edit, and delete actions.

3. Search:
   - Real-time search by title, author, or genre using AJAX.
   - Instant suggestions with links to detailed pages.

4. Modals and Dropdowns:
   - Bootstrap AJAX-powered modal to display book lists.
   - Dropdown menu dynamically populated from database via AJAX.

5. Book Details Page:
   - Detailed view of individual books.
   - Cover images fetched dynamically from Open Library API using HTTPS connections.
   - Clean, user-friendly layout.

6. Contact Us:
   - Displays support email.
   - Collapsible section showing website overview.

7. Technology Stack:
   - Backend: PHP & MySQL
   - Frontend: Bootstrap 5, HTML, CSS, JavaScript
   - AJAX for dynamic updates without page reloads.

8. Security & Usability:
   - Prepared statements to prevent SQL injection.
   - Input validation for forms.
   - Confirmation prompts for destructive actions.

Contact:
--------
Email: support@wlvbookstore.edu
