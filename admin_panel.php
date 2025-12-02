<?php
require 'config.php';
if (!is_admin()) { header('Location: user_home.php'); exit; }

$users = $mysqli->query("SELECT * FROM users ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
$posts = $mysqli->query("SELECT p.*, u.name as poster_name, u.email FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>
<?php include 'header.php'; ?>
<div class="container py-4">
  <h3>Admin Panel</h3>
  <div class="row">
    <div class="col-md-6">
      <h5>Users</h5>
      <div class="list-group">
        <?php foreach($users as $u): ?>
          <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <strong><?=esc($u['name'])?></strong><br>
              <small><?=esc($u['email'])?> · <?=esc($u['phone'])?><br><?=esc($u['address'])?></small>
            </div>
            <div>
              <?php if($u['avatar']): ?><img src="<?=esc($u['avatar'])?>" alt="avatar" style="width:48px;height:48px;object-fit:cover;border-radius:6px;margin-right:8px;"><?php endif;?>
              <?php if ($u['is_admin']): ?>
                <span class="badge bg-success">Admin</span>
              <?php else: ?>
                <form method="post" action="post_action.php" style="display:inline;">
                  <input type="hidden" name="action" value="delete_user">
                  <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                  <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this user and all posts?')">Delete</button>
                </form>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="col-md-6">
      <h5>All Posts</h5>
      <?php foreach($posts as $p): ?>
        <div class="card mb-2">
          <div class="card-body">
            <h6><?=esc($p['item_name'])?> <small class="text-muted">[<?=esc($p['type'])?>]</small></h6>
            <p class="mb-1"><?=esc($p['description'])?></p>
            <small>By <?=esc($p['poster_name'])?> (<?=esc($p['email'])?>)</small>
            <div class="mt-2">
              <form method="post" action="post_action.php" style="display:inline;">
                <input type="hidden" name="action" value="admin_delete">
                <input type="hidden" name="post_id" value="<?= $p['id'] ?>">
                <button class="btn btn-sm btn-danger" onclick="return confirm('Delete post?')">Delete Post</button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>
