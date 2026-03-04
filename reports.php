<?php
require_once __DIR__ . '/includes/auth.php';
$user = requireRole(['patient', 'doctor', 'admin']);
$success = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($user['role'], ['doctor', 'admin'], true)) {
    $stmt = $pdo->prepare('INSERT INTO medical_reports (patient_id, doctor_id, report_title, report_type, summary, file_link) VALUES (?,?,?,?,?,?)');
    $stmt->execute([
        (int)$_POST['patient_id'],
        $user['id'],
        trim($_POST['report_title']),
        trim($_POST['report_type']),
        trim($_POST['summary']),
        trim($_POST['file_link'])
    ]);
    $success = 'Medical report created.';
}

$patientList = $pdo->query('SELECT id, full_name FROM users WHERE role = "patient" ORDER BY full_name')->fetchAll();
$query = 'SELECT r.*, p.full_name patient_name, d.full_name doctor_name FROM medical_reports r
          JOIN users p ON r.patient_id = p.id JOIN users d ON r.doctor_id = d.id';
$params = [];
if ($user['role'] === 'patient') { $query .= ' WHERE r.patient_id = ?'; $params[] = $user['id']; }
$query .= ' ORDER BY r.created_at DESC';
$stmt = $pdo->prepare($query); $stmt->execute($params); $reports = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>
<section class="card">
  <h2>Medical Reports Access</h2>
  <?php if ($success): ?><div class="alert success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
  <?php if (in_array($user['role'], ['doctor', 'admin'], true)): ?>
  <form method="post" class="grid">
    <select name="patient_id" required>
      <option value="">Select Patient</option>
      <?php foreach ($patientList as $patient): ?><option value="<?= (int)$patient['id'] ?>"><?= htmlspecialchars($patient['full_name']) ?></option><?php endforeach; ?>
    </select>
    <input class="input" name="report_title" placeholder="Report Title" required>
    <input class="input" name="report_type" placeholder="Type (Lab/Prescription/Summary)" required>
    <input class="input" name="file_link" placeholder="File URL or path for download">
    <textarea name="summary" placeholder="Visit summary / findings" required></textarea>
    <button type="submit">Publish Report</button>
  </form>
  <?php endif; ?>
</section>
<section class="card">
  <table class="table">
    <tr><th>Patient</th><th>Doctor</th><th>Title</th><th>Type</th><th>Summary</th><th>Download</th></tr>
    <?php foreach ($reports as $report): ?>
    <tr>
      <td><?= htmlspecialchars($report['patient_name']) ?></td>
      <td><?= htmlspecialchars($report['doctor_name']) ?></td>
      <td><?= htmlspecialchars($report['report_title']) ?></td>
      <td><?= htmlspecialchars($report['report_type']) ?></td>
      <td><?= htmlspecialchars($report['summary']) ?></td>
      <td><?= $report['file_link'] ? '<a href="' . htmlspecialchars($report['file_link']) . '">Open</a>' : '-' ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
