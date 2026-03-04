<?php
require_once __DIR__ . '/config/db.php';
$success = null; $error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $location = trim($_POST['location'] ?? '');
    if (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO users (full_name,email,password,role,location) VALUES (?,?,?,?,?)');
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), 'patient', $location]);
            $success = 'Registration successful. Please login.';
        } catch (PDOException $e) {
            $error = 'Registration failed. Email may already exist.';
        }
    }
}
require_once __DIR__ . '/includes/header.php';
?>
<section class="card" style="max-width:560px;margin:auto;">
  <h2>Patient Registration</h2>
  <?php if ($error): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="alert success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
  <form method="post">
    <input class="input" name="full_name" placeholder="Full Name" required>
    <input class="input" type="email" name="email" placeholder="Email" required>
    <input class="input" type="password" name="password" placeholder="Password" required>
    <input class="input" name="location" placeholder="Location" required>
    <button type="submit">Create Account</button>
  </form>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
