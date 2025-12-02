<?php
require 'config.php';
if (!is_logged()) { header('Location: login.php'); exit; }
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item = trim($_POST['item_name']);
    $desc = trim($_POST['description']);
    $type = 'lost';
    if (!$item) $errors[] = "Item name required.";

    $imgPath = null;
    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg','jpeg','png','gif'];
        if (!in_array(strtolower($ext), $allowed)) $errors[] = "Image must be jpg/png/gif.";
        else {
            $fn = 'post_'.time().'_'.rand(1000,9999).'.'.$ext;
            $dest = __DIR__ . '/uploads/' . $fn;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) $imgPath = 'uploads/' . $fn;
            else $errors[] = "Image upload failed.";
        }
    }
    if (!$errors) {
        $stmt = $mysqli->prepare("INSERT INTO posts (user_id,item_name,description,image,type) VALUES (?,?,?,?,?)");
        $uid = $_SESSION['user_id'];
        $stmt->bind_param('issss', $uid, $item, $desc, $imgPath, $type);
        if ($stmt->execute()) {
            header('Location: user_home.php'); exit;
        } else {
            $errors[] = "DB error: " . $mysqli->error;
        }
    }
}
?>
<?php include 'header.php'; ?>
<div class="container py-4">
  <div class="card mx-auto shadow" style="max-width:720px;">
    <div class="card-body">
      <h4>Report Lost Item</h4>
      <?php if($errors): ?><div class="alert alert-danger"><?=implode('<br>', array_map('esc',$errors))?></div><?php endif; ?>
      <form method="post" enctype="multipart/form-data">
        <div class="mb-2"><label class="form-label">Item name</label><input name="item_name" class="form-control" required></div>
        <div class="mb-2"><label class="form-label">Description</label><textarea name="description" class="form-control"></textarea></div>
        <div class="mb-2"><label class="form-label">Image (optional)</label><input type="file" name="image" class="form-control"></div>
        <div class="d-flex justify-content-between">
          <a href="user_home.php" class="btn btn-outline-secondary">Back</a>
          <button class="btn btn-primary">Post Lost</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>
