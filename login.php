<?php
require_once __DIR__ . '/config/db.php';
$error = $_GET['error'] ?? null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: index.php');
        exit;
    }
    $error = 'Invalid email or password.';
}
require_once __DIR__ . '/includes/header.php';
?>
<section class="card" style="max-width:500px;margin:auto;">
  <h2>Login</h2>
  <?php if ($error): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <form method="post">
    <input class="input" type="email" name="email" placeholder="Email" required>
    <input class="input" type="password" name="password" placeholder="Password" required>
    <button type="submit">Sign In</button>
  </form>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
