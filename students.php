<?php
require 'db_config.php';

// Basic router via `action` and `id`
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Create or update
  $roll = $_POST['roll_no'] ?? '';
  $name = $_POST['name'] ?? '';
  $division = $_POST['division'] ?? null;
  $attendance = isset($_POST['attendance']) && $_POST['attendance'] !== '' ? (int)$_POST['attendance'] : null;
  if($attendance !== null) { $attendance = max(0, min(100, $attendance)); }
  $edit_id = $_POST['id'] ?? null;

  if($edit_id) {
    $stmt = $pdo->prepare('UPDATE students SET roll_no=?, name=?, division=?, attendance=? WHERE id=?');
    $stmt->execute([$roll,$name,$division,$attendance,$edit_id]);
  } else {
    $stmt = $pdo->prepare('INSERT INTO students (roll_no,name,division,attendance) VALUES (?,?,?,?)');
    $stmt->execute([$roll,$name,$division,$attendance]);
  }
  header('Location: students.php');
  exit;
}

if($action === 'delete' && $id) {
  $stmt = $pdo->prepare('DELETE FROM students WHERE id=?');
  $stmt->execute([$id]);
  header('Location: students.php');
  exit;
}

if($action === 'edit' && $id) {
  $stmt = $pdo->prepare('SELECT * FROM students WHERE id=?');
  $stmt->execute([$id]);
  $student = $stmt->fetch();
}

// list
$list = $pdo->query('SELECT * FROM students ORDER BY id DESC')->fetchAll();
?>
<!doctype html>
<html><head><meta charset='utf-8'><title>Students</title><link rel='stylesheet' href='styles.css'></head><body>
<header><h1>Students</h1><a href='index.php' class='back-btn'>Back to Dashboard</a></header>
<main>
  <h2><?= isset($student) ? 'Edit Student' : 'Add Student' ?></h2>
  <form method='post'>
    <input type='hidden' name='id' value='<?= $student['id'] ?? '' ?>'>
    <label>Name<br><input name='name' required value='<?= htmlspecialchars($student['name'] ?? '') ?>'></label><br>
    <label>Roll No<br><input name='roll_no' required value='<?= htmlspecialchars($student['roll_no'] ?? '') ?>'></label><br>
    <label>Division<br><input name='division' value='<?= htmlspecialchars($student['division'] ?? '') ?>'></label><br>
    <label>Attendance (%)<br><input type='number' name='attendance' min='0' max='100' value='<?= htmlspecialchars($student['attendance'] ?? '') ?>' placeholder='e.g., 75'></label><br>
    <button type='submit'>Save</button>
  </form>

  <h2>All Students</h2>
  <table>
    <thead><tr><th>Name</th><th>Roll</th><th>Division</th><th>Attendance (%)</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach($list as $r): ?>
      <tr>
        <td><?= htmlspecialchars($r['name']) ?></td>
        <td><?= htmlspecialchars($r['roll_no']) ?></td>
        <td><?= htmlspecialchars($r['division']) ?></td>
        <td><?= htmlspecialchars($r['attendance']) ?></td>
        <td>
          <a href='students.php?action=edit&id=<?= $r['id'] ?>'>Edit</a> |
          <a href='students.php?action=delete&id=<?= $r['id'] ?>' onclick='return confirm("Delete?")'>Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</main>
</body></html>
