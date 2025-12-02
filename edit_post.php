<?php
require 'config.php';
if (!is_logged()) { header('Location: login.php'); exit; }
$uid = $_SESSION['user_id'];
$id = isset($_GET['id'])?intval($_GET['id']):0;
$stmt = $mysqli->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
$stmt->bind_param('ii',$id,$uid);
$stmt->execute();
$res = $stmt->get_result();
$post = $res->fetch_assoc();
if (!$post) { header('Location: user_home.php'); exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item = trim($_POST['item_name']);
    $desc = trim($_POST['description']);
    if (!$item) $errors[] = "Item name required.";

    $imgPath = $post['image'];
    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg','jpeg','png','gif'];
        if (!in_array(strtolower($ext), $allowed)) $errors[] = "Image must be jpg/png/gif.";
        else {
            $fn = 'post_'.time().'_'.rand(1000,9999).'.'.$ext;
            $dest = __DIR__ . '/uploads/' . $fn;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                $imgPath = 'uploads/' . $fn;
            } else $errors[] = "Image upload failed.";
        }
    }
    if (!$errors) {
        $stmt2 = $mysqli->prepare("UPDATE posts SET item_name=?, description=?, image=? WHERE id=? AND user_id=?");
        $stmt2->bind_param('sssii', $item, $desc, $imgPath, $id, $uid);
        if ($stmt2->execute()) {
            header('Location: user_home.php'); exit;
        } else $errors[] = "Update failed.";
    }
}
?>
<?php include 'header.php'; ?>
<div class="container py-4">
  <div class="card mx-auto" style="max-width:720px;">
    <div class="card-body">
      <h4>Edit Post</h4>
      <?php if($errors): ?><div class="alert alert-danger"><?=implode('<br>', array_map('esc',$errors))?></div><?php endif; ?>
      <form method="post" enctype="multipart/form-data">
        <div class="mb-2"><label>Item name</label><input name="item_name" value="<?=esc($post['item_name'])?>" class="form-control"></div>
        <div class="mb-2"><label>Description</label><textarea name="description" class="form-control"><?=esc($post['description'])?></textarea></div>
        <div class="mb-2"><label>Image (replace)</label><input type="file" name="image" class="form-control"></div>
        <div class="d-flex justify-content-between">
          <a href="user_home.php" class="btn btn-outline-secondary">Cancel</a>
          <button class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>
