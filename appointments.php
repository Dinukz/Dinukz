<?php
require_once __DIR__ . '/includes/auth.php';
$user = requireLogin();
$success = null; $error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user['role'] === 'patient') {
    $doctorId = (int)($_POST['doctor_id'] ?? 0);
    $date = $_POST['appointment_date'] ?? '';
    $time = $_POST['appointment_time'] ?? '';
    $notes = trim($_POST['notes'] ?? '');
    if ($doctorId && $date && $time) {
        $stmt = $pdo->prepare('INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, notes) VALUES (?,?,?,?,?)');
        $stmt->execute([$user['id'], $doctorId, $date, $time, $notes]);
        $success = 'Appointment booked successfully.';
    } else {
        $error = 'Please fill all required appointment fields.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status_update']) && in_array($user['role'], ['admin', 'doctor'], true)) {
    $appointmentId = (int)$_POST['appointment_id'];
    $status = $_POST['status'];
    $allowed = ['Pending', 'Confirmed', 'Completed', 'Cancelled'];
    if (in_array($status, $allowed, true)) {
        $stmt = $pdo->prepare('UPDATE appointments SET status = ? WHERE id = ?');
        $stmt->execute([$status, $appointmentId]);
        $success = 'Appointment status updated.';
    }
}

$doctorStmt = $pdo->query('SELECT id, full_name, specialization, availability FROM users WHERE role = "doctor" ORDER BY full_name');
$doctors = $doctorStmt->fetchAll();

$query = 'SELECT a.*, p.full_name AS patient_name, d.full_name AS doctor_name FROM appointments a
          JOIN users p ON a.patient_id = p.id
          JOIN users d ON a.doctor_id = d.id';
$params = [];
if ($user['role'] === 'patient') { $query .= ' WHERE a.patient_id = ?'; $params[] = $user['id']; }
if ($user['role'] === 'doctor') { $query .= ' WHERE a.doctor_id = ?'; $params[] = $user['id']; }
$query .= ' ORDER BY a.appointment_date DESC, a.appointment_time DESC';
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$appointments = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>
<section class="card">
  <h2>Appointment Booking System</h2>
  <?php if ($error): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="alert success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
  <?php if ($user['role'] === 'patient'): ?>
  <form method="post" class="grid">
    <select name="doctor_id" required>
      <option value="">Select Doctor</option>
      <?php foreach ($doctors as $doctor): ?>
      <option value="<?= (int)$doctor['id'] ?>"><?= htmlspecialchars($doctor['full_name']) ?> - <?= htmlspecialchars($doctor['specialization']) ?> (<?= htmlspecialchars($doctor['availability']) ?>)</option>
      <?php endforeach; ?>
    </select>
    <input class="input" type="date" name="appointment_date" required>
    <input class="input" type="time" name="appointment_time" required>
    <textarea name="notes" placeholder="Symptoms/notes"></textarea>
    <button type="submit">Book Appointment</button>
  </form>
  <?php endif; ?>
</section>
<section class="card">
  <h3>Appointment History</h3>
  <table class="table">
    <tr><th>Patient</th><th>Doctor</th><th>Date</th><th>Time</th><th>Status</th><th>Action</th></tr>
    <?php foreach ($appointments as $appointment): ?>
      <tr>
        <td><?= htmlspecialchars($appointment['patient_name']) ?></td>
        <td><?= htmlspecialchars($appointment['doctor_name']) ?></td>
        <td><?= htmlspecialchars($appointment['appointment_date']) ?></td>
        <td><?= htmlspecialchars($appointment['appointment_time']) ?></td>
        <td><?= htmlspecialchars($appointment['status']) ?></td>
        <td>
          <?php if (in_array($user['role'], ['admin', 'doctor'], true)): ?>
            <form method="post" style="display:flex;gap:.4rem;align-items:center;">
              <input type="hidden" name="appointment_id" value="<?= (int)$appointment['id'] ?>">
              <select name="status">
                <?php foreach (['Pending', 'Confirmed', 'Completed', 'Cancelled'] as $status): ?>
                  <option value="<?= $status ?>" <?= $appointment['status'] === $status ? 'selected' : '' ?>><?= $status ?></option>
                <?php endforeach; ?>
              </select>
              <button name="status_update" value="1">Save</button>
            </form>
          <?php else: ?>
            -
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
