<?php
require 'config.php';
if(!isset($_GET['id'])) { header('Location: user_home.php'); exit; }
$id = intval($_GET['id']);
$stmt = $mysqli->prepare("SELECT p.*, u.name as poster_name, u.email as poster_email, u.phone as poster_phone, u.address as poster_address FROM posts p JOIN users u ON p.user_id=u.id WHERE p.id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) { echo 'Post not found'; exit; }
$p = $res->fetch_assoc();
?>
<?php include 'header.php'; ?>
<div class="container py-4">
  <div class="card mx-auto shadow" style="max-width:300px;">
    <?php if ($p['image']): ?>
      <img src="<?=esc($p['image'])?>" class="card-img-top" style="height:420px;object-fit:cover;">
    <?php endif; ?>
    <div class="card-body">
      <h2 class="card-title"><?=esc($p['item_name'])?> <small class="text-muted">[<?=esc($p['type'])?>]</small></h2>
      <p><strong>Description:</strong><br><?=nl2br(esc($p['description']))?></p>
      <p><strong>Status:</strong> <?=esc($p['status'])?> | <strong>Posted:</strong> <?=esc($p['created_at'])?></p>
      <hr>
      <h5>Posted by: <?=esc($p['poster_name'])?></h5>
      <p>Email: <?=esc($p['poster_email'])?><br>Phone: <?=esc($p['poster_phone'])?><br>Address: <?=esc($p['poster_address'])?></p>

      <?php if (is_logged() && $_SESSION['user_id'] == $p['user_id']): ?>
        <?php if($p['type']=='lost' && $p['status']=='active'): ?>
          <form method="post" action="post_action.php" style="display:inline;">
            <input type="hidden" name="action" value="mark_found">
            <input type="hidden" name="post_id" value="<?= $p['id'] ?>">
            <button class="btn btn-success">Mark as Found</button>
          </form>
        <?php endif; ?>
        <?php if($p['type']=='found' && $p['status']=='active'): ?>
          <form method="post" action="post_action.php" style="display:inline;">
            <input type="hidden" name="action" value="mark_returned">
            <input type="hidden" name="post_id" value="<?= $p['id'] ?>">
            <button class="btn btn-primary">Mark as Returned</button>
          </form>
        <?php endif; ?>

        <a href="edit_post.php?id=<?= $p['id'] ?>" class="btn btn-outline-secondary">Edit</a>
        <form method="post" action="post_action.php" style="display:inline;">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="post_id" value="<?= $p['id'] ?>">
          <button class="btn btn-danger" onclick="return confirm('Delete this post?')">Delete</button>
        </form>
      <?php endif; ?>

      <?php if (is_admin()): ?>
        <form method="post" action="post_action.php" style="display:inline;">
          <input type="hidden" name="action" value="admin_delete">
          <input type="hidden" name="post_id" value="<?= $p['id'] ?>">
          <button class="btn btn-danger">Admin Delete</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>
