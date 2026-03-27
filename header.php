<?php
session_start();
$user_role = $_SESSION['user_role'] ?? 'guest';
$user_name = $_SESSION['user_name'] ?? '';
?>
<header>
  <nav class="main-navbar">
    <div class="logo">Classroom Booking</div>
    <ul class="nav-links">
      <li><a href="index.php">Home</a></li>
      <?php if ($user_role === 'faculty'): ?>
        <li><a href="faculty-dashboard.php">Dashboard</a></li>
      <?php elseif ($user_role === 'admin'): ?>
        <li><a href="admin-panel.php">Admin Panel</a></li>
      <?php endif; ?>
      <?php if ($user_role === 'guest'): ?>
        <li><a href="login.php" class="btn login-btn">Login</a></li>
      <?php else: ?>
        <li><span class="welcome">Hi, <?= htmlspecialchars($user_name) ?></span></li>
        <li><a href="logout.php" class="btn logout-btn">Logout</a></li>
      <?php endif; ?>
    </ul>
  </nav>
</header>
