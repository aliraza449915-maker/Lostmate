<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>LostMate</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-white bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="user_home.php"><strong>LostMate</strong></a>
    <div class="d-flex align-items-center">
      <?php if(is_logged()): ?>
        <span class="me-3">Hello, <?=esc($_SESSION['user_name'])?></span>
        <?php if (is_admin()): ?><a class="btn btn-sm btn-outline-dark me-2" href="admin_panel.php">Admin</a><?php endif; ?>
        <a class="btn btn-sm btn-outline-secondary" href="logout.php">Logout</a>
      <?php else: ?>
        <a class="btn btn-sm btn-primary me-2" href="login.php">Login</a>
        <a class="btn btn-sm btn-outline-primary" href="register.php">Register</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
