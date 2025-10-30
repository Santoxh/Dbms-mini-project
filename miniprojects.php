<?php
require 'db_config.php';

$rows = [];
try {
  $stmt = $pdo->query("SELECT st.name AS student_name,
                              s.mini_topic,
                              s.mini_date,
                              s.mini_marks,
                              s.grade
                        FROM submissions s
                        JOIN students st ON s.student_id = st.id
                        WHERE (s.mini_topic IS NOT NULL AND s.mini_topic <> '')
                           OR s.mini_date IS NOT NULL
                           OR s.mini_marks IS NOT NULL
                        ORDER BY COALESCE(s.mini_date, '1970-01-01') DESC, s.mini_topic ASC");
  $rows = $stmt->fetchAll();
} catch (Exception $e) {
  // Columns not present yet
  $rows = [];
}
?>
<!doctype html>
<html>
<head>
  <meta charset='utf-8'>
  <title>Mini Projects</title>
  <link rel='stylesheet' href='styles.css'>
</head>
<body>
<header><h1>Mini Projects</h1><a href='index.php' class='back-btn'>Back to Dashboard</a></header>
<main>
  <h2>Mini Project Records</h2>
  <?php if(empty($rows)): ?>
    <p>No mini project data found.</p>
  <?php else: ?>
    <table>
      <thead><tr><th>Student</th><th>Mini Project Topic</th><th>Date</th><th>Marks</th><th>Grade</th></tr></thead>
      <tbody>
        <?php foreach($rows as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['student_name']) ?></td>
            <td><?= htmlspecialchars($r['mini_topic']) ?></td>
            <td><?= htmlspecialchars($r['mini_date']) ?></td>
            <td><?= htmlspecialchars($r['mini_marks']) ?></td>
            <td><?= htmlspecialchars($r['grade']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</main>
</body>
</html>
