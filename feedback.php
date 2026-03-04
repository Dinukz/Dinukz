<?php
require_once __DIR__ . '/includes/auth.php';
$user = requireRole(['patient', 'admin']);
$success = null; $error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctorId = (int)($_POST['doctor_id'] ?? 0);
    $rating = (int)($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');
    if ($doctorId && $rating >= 1 && $rating <= 5) {
        $stmt = $pdo->prepare('INSERT INTO feedback (patient_id, doctor_id, rating, comment) VALUES (?,?,?,?)');
        $stmt->execute([$user['id'], $doctorId, $rating, $comment]);
        $success = 'Thank you for your feedback.';
    } else {
        $error = 'Please provide valid feedback details.';
    }
}
$doctors = $pdo->query('SELECT id, full_name, specialization FROM users WHERE role = "doctor" ORDER BY full_name')->fetchAll();
$list = $pdo->query('SELECT f.*, p.full_name patient_name, d.full_name doctor_name FROM feedback f JOIN users p ON f.patient_id=p.id JOIN users d ON f.doctor_id=d.id ORDER BY f.created_at DESC')->fetchAll();
require_once __DIR__ . '/includes/header.php';
?>
<section class="card">
  <h2>Feedback and Ratings</h2>
  <?php if ($error): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="alert success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
  <form method="post" class="grid">
    <select name="doctor_id" required><option value="">Select Doctor</option><?php foreach ($doctors as $doctor): ?><option value="<?= (int)$doctor['id'] ?>"><?= htmlspecialchars($doctor['full_name']) ?> - <?= htmlspecialchars($doctor['specialization']) ?></option><?php endforeach; ?></select>
    <select name="rating" required><option value="">Rating</option><?php for ($i=1;$i<=5;$i++): ?><option value="<?= $i ?>"><?= $i ?></option><?php endfor; ?></select>
    <textarea name="comment" placeholder="Share your experience"></textarea>
    <button type="submit">Submit Feedback</button>
  </form>
</section>
<section class="card">
  <table class="table">
    <tr><th>Patient</th><th>Doctor</th><th>Rating</th><th>Comment</th><th>Date</th></tr>
    <?php foreach ($list as $row): ?>
    <tr>
      <td><?= htmlspecialchars($row['patient_name']) ?></td>
      <td><?= htmlspecialchars($row['doctor_name']) ?></td>
      <td><?= (int)$row['rating'] ?>/5</td>
      <td><?= htmlspecialchars($row['comment']) ?></td>
      <td><?= htmlspecialchars($row['created_at']) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
