<?php
require_once __DIR__ . '/includes/auth.php';
$user = requireRole(['admin']);
$stats = [
    'Doctors' => (int)$pdo->query('SELECT COUNT(*) FROM users WHERE role="doctor"')->fetchColumn(),
    'Patients' => (int)$pdo->query('SELECT COUNT(*) FROM users WHERE role="patient"')->fetchColumn(),
    'Appointments' => (int)$pdo->query('SELECT COUNT(*) FROM appointments')->fetchColumn(),
    'Reports' => (int)$pdo->query('SELECT COUNT(*) FROM medical_reports')->fetchColumn(),
    'Messages' => (int)$pdo->query('SELECT COUNT(*) FROM messages')->fetchColumn()
];
require_once __DIR__ . '/includes/header.php';
?>
<section class="card">
  <h2>Admin Dashboard</h2>
  <p>Role-based administration overview of hospital operations.</p>
  <div class="grid">
    <?php foreach ($stats as $label => $value): ?>
      <div class="card"><h3><?= htmlspecialchars($label) ?></h3><p style="font-size:2rem;margin:0;"><?= $value ?></p></div>
    <?php endforeach; ?>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
