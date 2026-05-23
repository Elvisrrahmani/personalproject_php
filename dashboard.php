<?php
require 'db.php';
require 'auth.php';
requireLogin();
$userId = currentUserId();
require 'functions.php';
$s = dashboardStats($pdo);
$pct = fn($n) => $s['total'] ? round($n*100/$s['total']) : 0;
?>
<!doctype html><html><head>
<meta charset="utf-8"><title>Dashboard</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="page-body dashboard-page">
<header class="topbar">
  <h1>PROJECT.FLUX <span>/ DASHBOARD</span></h1>
  <nav><a href="index.php">Board</a><a href="dashboard.php" class="active">Dashboard</a></nav>
</header>

<main class="dash">
  <div class="stat"><h3>Total</h3><p><?= $s['total'] ?></p></div>
  <div class="stat"><h3>Backlog</h3><p><?= $s['backlog'] ?></p><span><?= $pct($s['backlog']) ?>%</span></div>
  <div class="stat active"><h3>Active</h3><p><?= $s['active'] ?></p><span><?= $pct($s['active']) ?>%</span></div>
  <div class="stat"><h3>Resolved</h3><p><?= $s['resolved'] ?></p><span><?= $pct($s['resolved']) ?>%</span></div>
  <div class="stat urgent"><h3>Urgent</h3><p><?= $s['urgent'] ?></p></div>

  <section class="bars">
    <h2>Distribution</h2>
    <?php foreach (['backlog','active','resolved'] as $k): ?>
      <div class="bar-row">
        <span><?= ucfirst($k) ?></span>
        <div class="bar"><div style="width:<?= $pct($s[$k]) ?>%"></div></div>
        <span><?= $s[$k] ?></span>
      </div>
    <?php endforeach; ?>
  </section>
</main>
</body></html>
