<?php
require 'db.php';
require 'auth.php';
requireLogin();
$userId = currentUserId();
require 'functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$task = $id ? getTask($pdo, $id) : null;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'code'     => trim($_POST['code'] ?? ''),
        'title'    => trim($_POST['title'] ?? ''),
        'assignee' => trim($_POST['assignee'] ?? ''),
        'due'      => $_POST['due'] ?? '',
        'urgent'   => isset($_POST['urgent']),
        'priority' => (int)($_POST['priority'] ?? 3),
        'status'   => $_POST['status'] ?? 'backlog',
    ];

    if ($data['title'] === '') $errors[] = 'Title required';
    if ($data['code'] === '')  $errors[] = 'Code required';
    if (!in_array($data['status'], ['backlog','active','resolved'])) $errors[] = 'Bad status';

    if (!$errors) {
        if ($id) updateTask($pdo, $id, $data);
        else     createTask($pdo, $data);
        header('Location: index.php'); exit;
    }
    $task = $data;
}
?>
<!doctype html>
<html><head>
<meta charset="utf-8">
<title><?= $id ? 'Edit' : 'New' ?> Task</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="page-body form-page">
<header class="topbar">
  <h1>PROJECT.FLUX <span>/ <?= $id ? 'EDIT' : 'NEW' ?></span></h1>
  <nav><a href="index.php">← Board</a></nav>
</header>

<form method="post" class="form">
  <?php if ($errors): ?>
    <div class="errors"><?php foreach ($errors as $er) echo '<p>'.e($er).'</p>'; ?></div>
  <?php endif; ?>

  <label>Code <input name="code" value="<?= e($task['code'] ?? 'TASK-'.rand(100,999)) ?>" required></label>
  <label>Title <input name="title" value="<?= e($task['title'] ?? '') ?>" required></label>
  <label>Assignee <input name="assignee" value="<?= e($task['assignee'] ?? '') ?>"></label>
  <label>Due <input type="date" name="due" value="<?= e($task['due'] ?? '') ?>"></label>

  <label>Status
    <select name="status">
      <?php foreach (['backlog','active','resolved'] as $s): ?>
        <option value="<?= $s ?>" <?= ($task['status'] ?? '')===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
      <?php endforeach; ?>
    </select>
  </label>

  <label>Priority: <output id="pv"><?= e($task['priority'] ?? 3) ?></output>
    <input type="range" name="priority" min="1" max="5"
           value="<?= e($task['priority'] ?? 3) ?>"
           oninput="document.getElementById('pv').value=this.value">
  </label>

  <label class="check">
    <input type="checkbox" name="urgent" <?= !empty($task['urgent']) ? 'checked' : '' ?>>
    Mark Urgent
  </label>

  <button type="submit"><?= $id ? 'Save' : 'Create' ?></button>
</form>
</body></html>
