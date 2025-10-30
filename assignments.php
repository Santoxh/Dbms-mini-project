<?php
require 'db_config.php';
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if($_SERVER['REQUEST_METHOD'] === 'POST') {
  $subject = trim($_POST['subject'] ?? '');
  $title = $_POST['title'] ?? '';
  $desc = $_POST['description'] ?? null;
  $due = $_POST['due_date'] ?? null;
  $edit_id = $_POST['id'] ?? null;

  // Resolve subject -> course_id (find or create by title)
  $course_id = null;
  if($subject !== '') {
    $stmt = $pdo->prepare('SELECT id FROM courses WHERE title = ? LIMIT 1');
    $stmt->execute([$subject]);
    $course = $stmt->fetch();
    if($course) {
      $course_id = (int)$course['id'];
    } else {
      $stmt = $pdo->prepare('INSERT INTO courses (title) VALUES (?)');
      $stmt->execute([$subject]);
      $course_id = (int)$pdo->lastInsertId();
    }
  }

  if($edit_id) {
    $stmt = $pdo->prepare('UPDATE assignments SET course_id=?, title=?, description=?, due_date=? WHERE id=?');
    $stmt->execute([$course_id,$title,$desc,$due,$edit_id]);
  } else {
    $stmt = $pdo->prepare('INSERT INTO assignments (course_id,title,description,due_date) VALUES (?,?,?,?)');
    $stmt->execute([$course_id,$title,$desc,$due]);
  }
  header('Location: assignments.php');
  exit;
}

if($action === 'delete' && $id) {
  $pdo->prepare('DELETE FROM assignments WHERE id=?')->execute([$id]);
  header('Location: assignments.php');
  exit;
}

if($action === 'edit' && $id) {
  $stmt = $pdo->prepare('SELECT a.*, c.title AS subject_title FROM assignments a LEFT JOIN courses c ON a.course_id=c.id WHERE a.id=?');
  $stmt->execute([$id]);
  $assignment = $stmt->fetch();
}

$courses = $pdo->query('SELECT * FROM courses')->fetchAll();
$list = $pdo->query('SELECT a.*, c.title AS course_title FROM assignments a JOIN courses c ON a.course_id=c.id ORDER BY a.id DESC')->fetchAll();
?>
<!doctype html>
<html><head><meta charset='utf-8'><title>Assignments</title><link rel='stylesheet' href='styles.css'></head><body>
<header><h1>Assignments</h1><a href='index.php' class='back-btn'>Back to Dashboard</a></header>
<main>
  <h2><?= isset($assignment) ? 'Edit Assignment' : 'Add Assignment' ?></h2>
  <form method='post'>
    <input type='hidden' name='id' value='<?= $assignment['id'] ?? '' ?>'>
    <label>Subject<br>
      <input name='subject' required value='<?= htmlspecialchars($assignment['subject_title'] ?? '') ?>'>
    </label><br>
    <label>Title<br><input name='title' required value='<?= htmlspecialchars($assignment['title'] ?? '') ?>'></label><br>
    <label>Description<br><textarea name='description'><?= htmlspecialchars($assignment['description'] ?? '') ?></textarea></label><br>
    <label>Due Date<br><input type='date' name='due_date' value='<?= htmlspecialchars($assignment['due_date'] ?? '') ?>'></label><br>
    <button type='submit'>Save</button>
  </form>

  <h2>All Assignments</h2>
  <table>
    <thead><tr><th>Subject</th><th>Title</th><th>Due</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach($list as $r): ?>
      <tr>
        <td><?= htmlspecialchars($r['course_title']) ?></td>
        <td><?= htmlspecialchars($r['title']) ?></td>
        <td><?= htmlspecialchars($r['due_date']) ?></td>
        <td>
          <a href='assignments.php?action=edit&id=<?= $r['id'] ?>'>Edit</a> |
          <a href='assignments.php?action=delete&id=<?= $r['id'] ?>' onclick='return confirm("Delete?")'>Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</main></body></html>
