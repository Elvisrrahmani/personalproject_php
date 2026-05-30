<?php
function e($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

function getTasks(PDO $pdo, string $status = null): array {
    if ($status) {
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE status = ? ORDER BY priority DESC, id DESC");
        $stmt->execute([$status]);
    } else {
        $stmt = $pdo->query("SELECT * FROM tasks ORDER BY priority DESC, id DESC");
    }
    return $stmt->fetchAll();
}

function getTask(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function createTask(PDO $pdo, array $d): void {
    $stmt = $pdo->prepare("INSERT INTO tasks (code,title,assignee,due,urgent,priority,status)
                           VALUES (?,?,?,?,?,?,?)");
    $stmt->execute([
        $d['code'], $d['title'], $d['assignee'],
        $d['due'] ?: null,
        !empty($d['urgent']) ? 1 : 0,
        (int)$d['priority'],
        $d['status'],
    ]);
}

function updateTask(PDO $pdo, int $id, array $d): void {
    $stmt = $pdo->prepare("UPDATE tasks SET code=?,title=?,assignee=?,due=?,urgent=?,priority=?,status=?
                           WHERE id=?");
    $stmt->execute([
        $d['code'], $d['title'], $d['assignee'],
        $d['due'] ?: null,
        !empty($d['urgent']) ? 1 : 0,
        (int)$d['priority'],
        $d['status'],
        $id,
    ]);
}

function deleteTask(PDO $pdo, int $id): void {
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->execute([$id]);
}

function moveTask(PDO $pdo, int $id, string $status): void {
    $stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
}

function isOverdue(string $dueDate = null): bool {
    if (!$dueDate) return false;
    return strtotime($dueDate) < strtotime('today');
}


function dashboardStats(PDO $pdo): array {
    $stmt = $pdo->query("SELECT status, COUNT(*) c FROM tasks GROUP BY status");
    $counts = ['backlog'=>0,'active'=>0,'resolved'=>0];
    foreach ($stmt as $r) $counts[$r['status']] = (int)$r['c'];
    $counts['total'] = array_sum($counts);
    $counts['urgent'] = (int)$pdo->query("SELECT COUNT(*) FROM tasks WHERE urgent=1")->fetchColumn();
    return $counts;
}
