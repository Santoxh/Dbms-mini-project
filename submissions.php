<?php
require 'db_config.php';
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if($_SERVER['REQUEST_METHOD'] === 'POST') {
  $assignment_id = isset($_POST['assignment_id']) ? (int)$_POST['assignment_id'] : null;
  $student_id = (int)($_POST['student_id'] ?? 0);
  $grade = $_POST['grade'] ?? null;
  $remarks = $_POST['remarks'] ?? null;
  $mini_topic_raw = trim($_POST['mini_topic'] ?? '');
  $mini_topic = ($mini_topic_raw === '') ? null : $mini_topic_raw;
  $mini_date = $_POST['mini_date'] ?? null;
  $mini_marks = $_POST['mini_marks'] !== '' ? (int)$_POST['mini_marks'] : null;
  $edit_id = $_POST['id'] ?? null;

  if($edit_id) {
    try {
      $stmt = $pdo->prepare('UPDATE submissions SET grade=?, remarks=?, mini_topic=?, mini_date=?, mini_marks=? WHERE id=?');
      $stmt->execute([$grade,$remarks,$mini_topic,$mini_date,$mini_marks,$edit_id]);
    } catch (Exception $e) {
      // Fallback if mini_* columns not present yet
      $stmt = $pdo->prepare('UPDATE submissions SET grade=?, remarks=? WHERE id=?');
      $stmt->execute([$grade,$remarks,$edit_id]);
    }
  } else {
    try {
      $sql = 'INSERT INTO submissions (assignment_id, student_id, grade, remarks, mini_topic, mini_date, mini_marks)
              VALUES (?,?,?,?,?,?,?)
              ON DUPLICATE KEY UPDATE
                grade=VALUES(grade),
                remarks=VALUES(remarks),
                mini_topic=VALUES(mini_topic),
                mini_date=VALUES(mini_date),
                mini_marks=VALUES(mini_marks)';
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$assignment_id,$student_id,$grade,$remarks,$mini_topic,$mini_date,$mini_marks]);
    } catch (Exception $e) {
      // Fallback if mini_* columns not present yet
      $sql = 'INSERT INTO submissions (assignment_id, student_id, grade, remarks)
              VALUES (?,?,?,?)
              ON DUPLICATE KEY UPDATE
                grade=VALUES(grade),
                remarks=VALUES(remarks)';
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$assignment_id,$student_id,$grade,$remarks]);
    }
  }
  header('Location: submissions.php');
  exit;
}

if($action === 'delete' && $id) {
  $pdo->prepare('DELETE FROM submissions WHERE id=?')->execute([$id]);
  header('Location: submissions.php');
  exit;
}

// Fetch lists
$assignments = $pdo->query('SELECT a.id, a.title, c.title AS course_title FROM assignments a JOIN courses c ON a.course_id=c.id ORDER BY c.title, a.title')->fetchAll();
$students = $pdo->query('SELECT id, name, roll_no FROM students')->fetchAll();
$list = $pdo->query("SELECT s.*, st.name AS student_name, st.roll_no, a.title AS assignment_title, c.title AS course_title
                      FROM submissions s
                      JOIN students st ON s.student_id = st.id
                      JOIN assignments a ON s.assignment_id = a.id
                      JOIN courses c ON a.course_id = c.id
                      ORDER BY s.submitted_at DESC")->fetchAll();
?>
<!doctype html>
<html><head><meta charset='utf-8'><title>Submissions</title><link rel='stylesheet' href='styles.css'></head><body>
<header><h1>Submissions</h1><a href='index.php' class='back-btn'>Back to Dashboard</a></header>
<main>
  <h2>New Submission</h2>
  <form method='post'>
    <label>Student<br>
      <select name='student_id' required>
        <?php foreach($students as $s): ?>
          <option value='<?= $s['id'] ?>'><?= htmlspecialchars($s['roll_no'] . ' - ' . $s['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </label><br>
    <label>Assignment (Subject - Title)<br>
      <select name='assignment_id' required>
        <?php foreach($assignments as $a): ?>
          <option value='<?= $a['id'] ?>'><?= htmlspecialchars($a['course_title'] . ' - ' . $a['title']) ?></option>
        <?php endforeach; ?>
      </select>
    </label><br>
    <label>Grade<br>
      <select name='grade'>
        <option value=''>-</option>
        <option value='A'>A</option>
        <option value='B'>B</option>
        <option value='C'>C</option>
        <option value='Fail'>Fail</option>
      </select>
    </label><br>
    <label>Remarks<br><textarea name='remarks'></textarea></label><br>

    <h2>Mini Project</h2>
    <label>Mini Project Topic<br>
      <input name='mini_topic' placeholder='e.g., Library Management using PHP'>
    </label><br>
    <label>Date<br>
      <input type='date' name='mini_date'>
    </label><br>
    <label>Marks<br>
      <input type='number' name='mini_marks' min='0' max='100' placeholder='e.g., 85'>
    </label><br>

    <button type='submit'>Submit</button>
  </form>

  <h2>All Submissions</h2>
  <table>
    <thead><tr><th>Student</th><th>Assignment</th><th>Subject</th><th>Submitted On</th><th>Grade</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach($list as $r): ?>
      <tr>
        <td><?= htmlspecialchars($r['roll_no'] . ' - ' . $r['student_name']) ?></td>
        <td><?= htmlspecialchars($r['assignment_title']) ?></td>
        <td><?= htmlspecialchars($r['course_title']) ?></td>
        <td><?= htmlspecialchars($r['submitted_at']) ?></td>
        <td><?= htmlspecialchars($r['grade']) ?></td>
        <td>
          <a href='submissions.php?action=delete&id=<?= $r['id'] ?>' onclick='return confirm("Delete?")'>Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</main></body></html>
