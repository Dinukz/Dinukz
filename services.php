<?php
require_once __DIR__ . '/includes/header.php';
$category = trim($_GET['category'] ?? '');
$stmt = $category === ''
    ? $pdo->query('SELECT * FROM services ORDER BY category, name')
    : (function () use ($pdo, $category) {
        $stmt = $pdo->prepare('SELECT * FROM services WHERE category LIKE ? ORDER BY name');
        $stmt->execute(["%$category%"]);
        return $stmt;
    })();
$services = $stmt->fetchAll();
?>
<section class="card">
  <h2>Healthcare Service Listings</h2>
  <form method="get">
    <input class="input" name="category" value="<?= htmlspecialchars($category) ?>" placeholder="Search category (e.g., Cardiology)">
    <button type="submit">Filter Services</button>
  </form>
</section>
<div class="grid">
<?php foreach ($services as $service): ?>
  <article class="card">
    <h3><?= htmlspecialchars($service['name']) ?></h3>
    <p><span class="badge"><?= htmlspecialchars($service['category']) ?></span></p>
    <p><?= htmlspecialchars($service['description']) ?></p>
  </article>
<?php endforeach; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
