<?php
// tables/user-logs-table.php (USER AUDIT TRAIL TABLE ONLY)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../authentication/user-class.php';

$user = new USER();

// 1. Read POST values safely
$page    = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$query   = isset($_POST['query']) ? trim($_POST['query']) : '';
$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;

// If user_id is missing, stop
if ($user_id === 0) {
    echo '<p>User not identified.</p>';
    exit;
}

// 2. Pagination setup
$limit  = 10;
$offset = ($page - 1) * $limit;

// 3. Base SQL: logs of this user only
$sql      = "SELECT * FROM logs WHERE user_id = :uid";
$sqlCount = "SELECT COUNT(*) AS total FROM logs WHERE user_id = :uid";

$params = [':uid' => $user_id];

// 4. Optional search by activity or date text
if ($query !== '') {
    $sql      .= " AND (activity LIKE :search OR created_at LIKE :search)";
    $sqlCount .= " AND (activity LIKE :search OR created_at LIKE :search)";
    $params[':search'] = '%' . $query . '%';
}

$sql .= " ORDER BY created_at DESC LIMIT :offset, :limit";

// 5. Prepare and execute main query
$stmt = $user->runQuery($sql);
$stmt->bindValue(':uid', $user_id, PDO::PARAM_INT);
if ($query !== '') {
    $stmt->bindValue(':search', '%' . $query . '%', PDO::PARAM_STR);
}
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);

$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 6. Get total rows for pagination
$countStmt = $user->runQuery($sqlCount);
$countStmt->execute($params);
$totalRows  = (int)$countStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = $totalRows > 0 ? (int)ceil($totalRows / $limit) : 1;

// 7. Render HTML table
if ($totalRows > 0) {
    echo '<table class="table table-bordered table-hover">';
    echo '<thead><tr><th>Date & Time</th><th>Activity</th></tr></thead><tbody>';

    foreach ($rows as $row) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['created_at']) . '</td>';
        echo '<td>' . htmlspecialchars($row['activity']) . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';

    // 8. Pagination links (used by .page-link handler in JS)
    echo '<div align="center"><ul class="pagination">';
    for ($i = 1; $i <= $totalPages; $i++) {
        $active = ($i === $page) ? ' active' : '';
        echo '<li class="page-item' . $active . '">';
        echo '<a class="page-link" href="javascript:void(0)" data-page_number="' . $i . '">' . $i . '</a>';
        echo '</li>';
    }
    echo '</ul></div>';
} else {
    echo '<p>No audit records found for this user.</p>';
}
?>
