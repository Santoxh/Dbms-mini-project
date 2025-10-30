<?php
require 'db_config.php';

$students = $pdo->query('SELECT id, name, roll_no, attendance FROM students ORDER BY id DESC')->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset='utf-8'>
  <title>Attendance</title>
  <link rel='stylesheet' href='styles.css'>
</head>
<body>
<header><h1>Attendance</h1><a href='index.php' class='back-btn'>Back to Dashboard</a></header>
<main>
  <h2>Student Attendance</h2>
  <?php if(empty($students)): ?>
    <p>No students found.</p>
  <?php else: ?>
    <table>
      <thead><tr><th>Roll No</th><th>Name</th><th>Attendance (%)</th><th>Eligibility</th></tr></thead>
      <tbody>
        <?php foreach($students as $s): ?>
          <?php $att = isset($s['attendance']) ? (int)$s['attendance'] : null; ?>
          <tr>
            <td><?= htmlspecialchars($s['roll_no']) ?></td>
            <td><?= htmlspecialchars($s['name']) ?></td>
            <td><?= $att !== null ? htmlspecialchars($att) : '-' ?></td>
            <td>
              <?php if($att !== null && $att < 50): ?>
                <span class='text-danger'>Not Eligible</span>
              <?php else: ?>
                <span>Eligible</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</main>
</body>
</html>
