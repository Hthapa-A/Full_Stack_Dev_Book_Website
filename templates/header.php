<?php
// Start session once
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>BookStore</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">

  <style>
    .navbar {
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    }
    .navbar-brand {
        font-weight: 700;
        font-size: 1.6rem;
        letter-spacing: 1px;
    }
    .nav-link {
        font-weight: 500;
        margin-right: 8px;
        transition: 0.2s;
    }
    .nav-link:hover {
        color: #ffc107 !important;
        transform: translateY(-1px);
    }
    .welcome-text {
        display: inline-block;
        white-space: nowrap;
        font-size: 0.95rem;
        font-weight: 500;
        animation: scrollMsg 15s linear infinite;
        color: #ffffff;
        padding-left: 100%;
    }
    @keyframes scrollMsg {
        0% { transform: translateX(0); }
        100% { transform: translateX(-100%); }
    }
    .welcome-container {
        overflow: hidden;
        width: 100%;
        background: #212529;
        padding: 4px 0;
    }
    .welcome-user {
      color: #fff;
      margin-left: 12px;
      font-weight: 600;
      font-size: 0.95rem;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container-fluid">

    <!-- Brand -->
    <a class="navbar-brand" href="index.php">BookStore</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">

        <?php if (!isset($_SESSION['user_id'])): ?>

            <!-- BEFORE LOGIN -->
            <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
            <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>

        <?php else: ?>

            <!-- AFTER LOGIN -->
            <li class="nav-item"><a class="nav-link" href="list_books.php">All Books</a></li>
            <li class="nav-item"><a class="nav-link" href="add_form.php">Add</a></li>
            <li class="nav-item"><a class="nav-link" href="search.php">Search</a></li>
            <li class="nav-item"><a class="nav-link" href="bootstrap-ajax-modal.html">Modal Demo</a></li>
            <li class="nav-item"><a class="nav-link" href="bootstrap-ajax-dropdown.html">Dropdown Demo</a></li>
            <li class="nav-item"><a class="nav-link" href="contact_us.php">Contact</a></li>
            <li class="nav-item"><a class="nav-link text-warning" href="logout.php">Logout</a></li>

        <?php endif; ?>

      </ul>
    </div>

    <!-- Welcome User -->
    <?php if (isset($_SESSION['username'])): ?>
      <span class="welcome-user d-none d-lg-inline-block">
        👋 Welcome, <?= htmlspecialchars($_SESSION['username']) ?>
      </span>
    <?php endif; ?>

  </div>
</nav>

<!-- Scrolling Banner -->
<div class="welcome-container">
  <span class="welcome-text">
    📚 Welcome to BookStore — Add, Edit, Search & Explore Books Smoothly with a Modern Experience! ✨
  </span>
</div>
