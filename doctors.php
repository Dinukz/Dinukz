<?php
require_once __DIR__ . '/includes/header.php';
$specialization = trim($_GET['specialization'] ?? '');
$location = trim($_GET['location'] ?? '');
$query = 'SELECT u.*, COALESCE(AVG(f.rating),0) AS avg_rating, COUNT(f.id) AS review_count
          FROM users u LEFT JOIN feedback f ON u.id = f.doctor_id
          WHERE u.role = "doctor"';
$params = [];
if ($specialization !== '') { $query .= ' AND u.specialization LIKE ?'; $params[] = "%$specialization%"; }
if ($location !== '') { $query .= ' AND u.location LIKE ?'; $params[] = "%$location%"; }
$query .= ' GROUP BY u.id ORDER BY avg_rating DESC';
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$doctors = $stmt->fetchAll();
?>
<section class="card">
  <h2>Doctor Directory</h2>
  <form method="get" class="grid">
    <input class="input" name="specialization" placeholder="Filter by specialization" value="<?= htmlspecialchars($specialization) ?>">
    <input class="input" name="location" placeholder="Filter by location" value="<?= htmlspecialchars($location) ?>">
    <button type="submit">Search</button>
  </form>
</section>
<div class="grid">
<?php foreach ($doctors as $doctor): ?>
  <article class="card">
    <h3><?= htmlspecialchars($doctor['full_name']) ?></h3>
    <p><span class="badge"><?= htmlspecialchars($doctor['specialization']) ?></span></p>
    <p>Experience: <?= (int)$doctor['experience_years'] ?> years</p>
    <p>Qualifications: <?= htmlspecialchars($doctor['qualifications']) ?></p>
    <p>Availability: <?= htmlspecialchars($doctor['availability']) ?></p>
    <p>Consultation: LKR <?= number_format((float)$doctor['consultation_charge'], 2) ?></p>
    <p>Rating: <?= number_format((float)$doctor['avg_rating'], 1) ?>/5 (<?= (int)$doctor['review_count'] ?> reviews)</p>
  </article>
<?php endforeach; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
