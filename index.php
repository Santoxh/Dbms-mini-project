<?php
require 'db_config.php';

// Fetch recent submissions with joins
try {
  $stmt = $pdo->query("SELECT s.id, st.name AS student_name, st.roll_no, st.attendance, a.title AS assignment_title, c.title AS course_title, s.submitted_at, s.grade
                       FROM submissions s
                       JOIN students st ON s.student_id = st.id
                       JOIN assignments a ON s.assignment_id = a.id
                       JOIN courses c ON a.course_id = c.id
                       ORDER BY s.submitted_at DESC
                       LIMIT 50");
} catch (Exception $e) {
  // Fallback if 'attendance' column doesn't exist yet
  $stmt = $pdo->query("SELECT s.id, st.name AS student_name, st.roll_no, a.title AS assignment_title, c.title AS course_title, s.submitted_at, s.grade
                       FROM submissions s
                       JOIN students st ON s.student_id = st.id
                       JOIN assignments a ON s.assignment_id = a.id
                       JOIN courses c ON a.course_id = c.id
                       ORDER BY s.submitted_at DESC
                       LIMIT 50");
}
$submissions = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset='utf-8'>
  <title>Submission Tracker - Dashboard</title>
  <link rel='stylesheet' href='styles.css'>
</head>
<body>
  <header><h1>Submission Tracker</h1></header>
  <nav>
    <a href='students.php'>Students</a> |
    <a href='assignments.php'>Assignments</a> |
    <a href='submissions.php'>Submissions</a> |
    <a href='attendance.php'>Attendance</a> |
    <a href='miniprojects.php'>Mini Project</a>
  </nav>

  <main>
    <h2>Recent Submissions</h2>
    <?php if(empty($submissions)): ?>
      <p>No submissions yet.</p>
    <?php else: ?>
      <table>
        <thead><tr><th>Roll No</th><th>Student</th><th>Subject</th><th>Assignment</th><th>Submitted On</th><th>Grade</th></tr></thead>
        <tbody>
        <?php foreach($submissions as $row): ?>
          <?php $att = isset($row['attendance']) ? (int)$row['attendance'] : null; ?>
          <tr>
            <td><?=htmlspecialchars($row['roll_no'])?></td>
            <td><?=htmlspecialchars($row['student_name'])?></td>
            <td><?=htmlspecialchars($row['course_title'])?></td>
            <td><?=htmlspecialchars($row['assignment_title'])?></td>
            <td><?=htmlspecialchars($row['submitted_at'])?></td>
            <td><?=htmlspecialchars($row['grade'])?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </main>
  <script src='scripts.js'></script>
</body>
</html>
