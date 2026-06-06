<?php
require 'db.php';
require 'functions.php';
require 'auth.php';
requireLogin();
$userId = currentUserId();


if (isset($_GET['move'], $_GET['to'])) {
    moveTask($pdo, (int)$_GET['move'], $_GET['to']);
    header('Location: index.php'); exit;
}

if (isset($_GET['delete'])) {
    deleteTask($pdo, (int)$_GET['delete']);
    header('Location: index.php'); exit;
}

$columns = ['backlog'=>'Backlog','active'=>'Active','resolved'=>'Resolved'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Project.Flux — Kanban (PHP)</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="page-body board-page">
<header class="topbar">
    <div class="topbar-left">
        <strong>KANBAN</strong>
        <a href="dashboard.php">Dashboard</a>
    </div>
    <div class="topbar-right">
        <span class="user-chip"><?= htmlspecialchars(currentUsername()) ?></span>
        <a href="logout.php" class="btn-ghost">Logout</a>
    </div>
</header>
<header class="topbar">
  <h1>PROJECT.FLUX <span>/ KANBAN</span></h1>
  <nav>
    <a href="index.php" class="active">Board</a>
    <a href="dashboard.php">Dashboard</a>
    <a href="form.php">+ New Task</a>
  </nav>
</header>

<main class="board">
<?php foreach ($columns as $key => $label): ?>
  <section class="col" data-col="<?= $key ?>"
           ondragover="event.preventDefault()"
           ondrop="drop(event,'<?= $key ?>')">
    <div class="col-head <?= $key ?>">
      <h2><?= $label ?></h2>
      <span><?= count(getTasks($pdo, $key)) ?></span>
    </div>

    <?php foreach (getTasks($pdo, $key) as $t): ?>
      <?php if ($key === 'resolved'): ?>
        <article class="card solved"
                 draggable="true"
                 ondragstart="drag(event,<?= $t['id'] ?>)">
          <div class="solved-display">
            <h3><?= e($t['title']) ?></h3>
            <span class="solved-text">✓ SOLVED</span>
          </div>
          <div class="actions">
            <a href="form.php?id=<?= $t['id'] ?>">Edit</a>
            <a href="?delete=<?= $t['id'] ?>" onclick="return confirm('Delete?')">Delete</a>
          </div>
        </article>
      <?php else: ?>
        <article class="card <?= $t['urgent'] ? 'urgent' : '' ?> <?= isOverdue($t['due']) ? 'overdue' : '' ?>"
                 draggable="true"
                 ondragstart="drag(event,<?= $t['id'] ?>)">
          <div class="card-head">
            <span class="code"><?= e($t['code']) ?></span>
            <?php if (isOverdue($t['due'])): ?><span class="badge overdue-badge">OVERDUE</span><?php elseif ($t['urgent']): ?><span class="badge">URGENT</span><?php endif; ?>
          </div>
          <h3><?= e($t['title']) ?></h3>
          <div class="meta">
            <span><?= e($t['assignee']) ?></span>
            <span><?= e($t['due']) ?></span>
          </div>
          <div class="actions">
            <a href="form.php?id=<?= $t['id'] ?>">Edit</a>
            <a href="?delete=<?= $t['id'] ?>" onclick="return confirm('Delete?')">Delete</a>
          </div>
        </article>
      <?php endif; ?>
    <?php endforeach; ?>
  </section>
<?php endforeach; ?>
</main>

<script>
function drag(e, id) { e.dataTransfer.setData('id', id); }
function drop(e, col) {
  const id = e.dataTransfer.getData('id');
  window.location = '?move=' + id + '&to=' + col;
}
</script>
</body>
</html>
