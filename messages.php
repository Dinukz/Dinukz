<?php
require_once __DIR__ . '/includes/auth.php';
$user = requireLogin();
$success = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('INSERT INTO messages (sender_id, receiver_id, subject, message) VALUES (?,?,?,?)');
    $stmt->execute([$user['id'], (int)$_POST['receiver_id'], trim($_POST['subject']), trim($_POST['message'])]);
    $success = 'Message sent successfully.';
}
$contactsStmt = $pdo->prepare('SELECT id, full_name, role FROM users WHERE id != ? AND role IN ("doctor", "patient", "admin") ORDER BY role, full_name');
$contactsStmt->execute([$user['id']]);
$contacts = $contactsStmt->fetchAll();
$inboxStmt = $pdo->prepare('SELECT m.*, s.full_name sender_name, r.full_name receiver_name FROM messages m JOIN users s ON m.sender_id=s.id JOIN users r ON m.receiver_id=r.id WHERE m.sender_id = ? OR m.receiver_id = ? ORDER BY m.created_at DESC');
$inboxStmt->execute([$user['id'], $user['id']]);
$messages = $inboxStmt->fetchAll();
require_once __DIR__ . '/includes/header.php';
?>
<section class="card">
  <h2>Secure Messaging</h2>
  <?php if ($success): ?><div class="alert success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
  <form method="post" class="grid">
    <select name="receiver_id" required><option value="">Select Recipient</option><?php foreach ($contacts as $contact): ?><option value="<?= (int)$contact['id'] ?>"><?= htmlspecialchars($contact['full_name']) ?> (<?= htmlspecialchars($contact['role']) ?>)</option><?php endforeach; ?></select>
    <input class="input" name="subject" placeholder="Subject" required>
    <textarea name="message" placeholder="Type your follow-up question or advice" required></textarea>
    <button type="submit">Send Message</button>
  </form>
</section>
<section class="card">
  <h3>Conversation History</h3>
  <table class="table">
    <tr><th>From</th><th>To</th><th>Subject</th><th>Message</th><th>When</th></tr>
    <?php foreach ($messages as $m): ?>
    <tr>
      <td><?= htmlspecialchars($m['sender_name']) ?></td>
      <td><?= htmlspecialchars($m['receiver_name']) ?></td>
      <td><?= htmlspecialchars($m['subject']) ?></td>
      <td><?= htmlspecialchars($m['message']) ?></td>
      <td><?= htmlspecialchars($m['created_at']) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
