<?php
require 'config.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);

    if (!$name || !$email || !$password) {
        $errors[] = "Name, email and password are required.";
    } else {
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param('s',$email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Email already registered.";
        } else {
            $avatarPath = null;
            if (!empty($_FILES['avatar']['name'])) {
                $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
                $allowed = ['jpg','jpeg','png','gif'];
                if (!in_array(strtolower($ext), $allowed)) {
                    $errors[] = "Avatar must be image jpg/png/gif.";
                } else {
                    $fn = 'avatar_'.time().'_'.rand(1000,9999).'.'.$ext;
                    $dest = __DIR__ . '/uploads/' . $fn;
                    if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $dest)) {
                        $errors[] = "Failed to upload avatar.";
                    } else {
                        $avatarPath = 'uploads/' . $fn;
                    }
                }
            }

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt2 = $mysqli->prepare("INSERT INTO users (name,email,password,address,phone,avatar) VALUES (?,?,?,?,?,?)");
            $stmt2->bind_param('ssssss', $name, $email, $hash, $address, $phone, $avatarPath);
            if ($stmt2->execute()) {
                header('Location: login.php?registered=1'); exit;
            } else {
                $errors[] = "Register failed: " . $mysqli->error;
            }
        }
    }
}
?>
<?php include 'header.php'; ?>
<div class="container py-5">
  <div class="card mx-auto shadow" style="max-width:720px;">
    <div class="card-body">
      <h3 class="card-title mb-3">Create account — LostMate</h3>
      <?php if(!empty($errors)): ?>
        <div class="alert alert-danger"><?=implode('<br>', array_map('esc',$errors))?></div>
      <?php endif; ?>
      <form method="post" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Full name</label>
          <input name="name" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Email</label>
          <input name="email" type="email" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Password</label>
          <input name="password" type="password" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Phone</label>
          <input name="phone" class="form-control">
        </div>
        <div class="col-12">
          <label class="form-label">Address</label>
          <input name="address" class="form-control">
        </div>
        <div class="col-12">
          <label class="form-label">Avatar (optional)</label>
          <input name="avatar" type="file" class="form-control">
        </div>
        <div class="col-12 d-flex justify-content-between align-items-center">
          <a href="login.php">Already have an account?</a>
          <button class="btn btn-primary">Create account</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>
