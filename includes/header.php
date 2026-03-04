<?php
require_once __DIR__ . '/auth.php';
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MediCare Plus</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="bg-grid"></div>
<nav class="navbar glass">
  <a href="index.php" class="logo">MediCare <span>Plus</span></a>
  <div class="nav-links">
    <a href="doctors.php">Doctors</a>
    <a href="services.php">Services</a>
    <?php if ($user): ?>
      <a href="appointments.php">Appointments</a>
      <a href="reports.php">Reports</a>
      <a href="messages.php">Messages</a>
      <a href="feedback.php">Feedback</a>
      <?php if ($user['role'] === 'admin'): ?><a href="admin.php">Admin</a><?php endif; ?>
      <a href="logout.php">Logout</a>
    <?php else: ?>
      <a href="login.php">Login</a>
      <a href="register.php">Register</a>
    <?php endif; ?>
  </div>
</nav>
<main class="container">
