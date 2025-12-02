<?php
require 'config.php';
if (!is_logged()) { header('Location: login.php'); exit; }

$uid = $_SESSION['user_id'];
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$typeFilter = isset($_GET['type']) && in_array($_GET['type'], ['lost','found']) ? $_GET['type'] : '';

$whereClause = " WHERE 1=1 ";
$params = [];
$types = '';
if ($search !== '') {
    $whereClause .= " AND (item_name LIKE ? OR description LIKE ?)";
    $like = "%{$search}%";
    $params[] = $like; $params[] = $like;
    $types .= 'ss';
}
if ($typeFilter) {
    $whereClause .= " AND type = ?";
    $params[] = $typeFilter;
    $types .= 's';
}

$sql = "SELECT p.*, u.name, u.avatar FROM posts p JOIN users u ON p.user_id = u.id $whereClause ORDER BY p.created_at DESC";
$stmt = $mysqli->prepare($sql);
if ($params) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$res = $stmt->get_result();
$posts = $res->fetch_all(MYSQLI_ASSOC);
?>
<?php include 'header.php'; ?>
<main class="container py-4">
  <div class="d-flex justify-content-between mb-3">
    <form class="d-flex" method="get" action="user_home.php">
      <input class="form-control me-2" name="q" placeholder="Search items" value="<?=esc($search)?>">
      <select name="type" class="form-select me-2">
        <option value="">All</option>
        <option value="lost" <?= $typeFilter==='lost'?'selected':'' ?>>Lost</option>
        <option value="found" <?= $typeFilter==='found'?'selected':'' ?>>Found</option>
      </select>
      <button class="btn btn-outline-primary">Search</button>
    </form>
    <div>
      <a href="report_lost.php" class="btn btn-primary btn-sm">Report Lost</a>
      <a href="report_found.php" class="btn btn-success btn-sm">Report Found</a>
    </div>
  </div>

  <div class="row gy-3">
    <?php foreach($posts as $p): ?>
      <div class="col-md-6">
        <div class="card h-100 shadow-sm">
          <?php if ($p['image']): ?>
            <img src="<?=esc($p['image'])?>" class="card-img-top" style="height:220px;object-fit:cover;">
          <?php endif; ?>
          <div class="card-body">
            <h5 class="card-title"><?=esc($p['item_name'])?> <small class="text-muted">[<?=esc($p['type'])?>]</small></h5>
            <p class="card-text"><?=nl2br(esc(substr($p['description'],0,200)))?><?=(strlen($p['description'])>200)?'...':''?></p>
            <p class="mb-1"><small>Posted by: <?=esc($p['name'])?> | Status: <strong><?=esc($p['status'])?></strong></small></p>
            <div class="mt-2 d-flex gap-2">
              <?php if ($p['user_id'] == $uid): ?>
                <a class="btn btn-outline-secondary btn-sm" href="edit_post.php?id=<?= $p['id'] ?>">Edit</a>
                <form method="post" action="post_action.php" style="display:inline;">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="post_id" value="<?= $p['id'] ?>">
                  <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this post?')">Delete</button>
                </form>

                <?php if($p['type']=='lost' && $p['status']=='active'): ?>
                  <form method="post" action="post_action.php" style="display:inline;">
                    <input type="hidden" name="action" value="mark_found">
                    <input type="hidden" name="post_id" value="<?= $p['id'] ?>">
                    <button class="btn btn-success btn-sm">Mark as Found</button>
                  </form>
                <?php endif; ?>

                <?php if($p['type']=='found' && $p['status']=='active'): ?>
                  <form method="post" action="post_action.php" style="display:inline;">
                    <input type="hidden" name="action" value="mark_returned">
                    <input type="hidden" name="post_id" value="<?= $p['id'] ?>">
                    <button class="btn btn-primary btn-sm">Mark as Returned</button>
                  </form>
                <?php endif; ?>

              <?php endif; ?>

              <a class="btn btn-outline-primary btn-sm" href="view_post.php?id=<?= $p['id'] ?>">View Details</a>

              <?php if (is_admin()): ?>
                <form method="post" action="post_action.php" style="display:inline;">
                  <input type="hidden" name="action" value="admin_delete">
                  <input type="hidden" name="post_id" value="<?= $p['id'] ?>">
                  <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Admin: delete this post?')">Admin Delete</button>
                </form>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
    <?php if (count($posts) === 0): ?>
      <div class="col-12">
        <div class="alert alert-info">No posts found.</div>
      </div>
    <?php endif; ?>
  </div>
</main>
<?php include 'footer.php'; ?>
