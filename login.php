<?php
require 'config.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $stmt = $mysqli->prepare("SELECT id,name,password,is_admin FROM users WHERE email = ?");
    $stmt->bind_param('s',$email);
    $stmt->execute();
    $stmt->bind_result($id,$name,$hash,$is_admin);
    if ($stmt->fetch()) {
        // allow if stored password equals plain (for legacy admin) OR password_verify matches
        if ($hash === $password || password_verify($password, $hash)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            $_SESSION['is_admin'] = $is_admin;
            header('Location: user_home.php'); exit;
        } else $err = "Invalid credentials.";
    } else {
        $err = "Invalid credentials.";
    }
}
$registered = isset($_GET['registered']);
?>
<?php include 'header.php'; ?>
<div class="container py-5">
  <div class="card mx-auto shadow" style="max-width:540px;">
    <div class="card-body">
      <h3 class="card-title">Sign in — LostMate</h3>
      <?php if($registered): ?>
        <div class="alert alert-success">Registration successful. You can log in now.</div>
      <?php endif; ?>
      <?php if($err): ?>
        <div class="alert alert-danger"><?=esc($err)?></div>
      <?php endif; ?>
      <form method="post">
        <div class="mb-2">
          <input name="email" type="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="mb-3">
          <input name="password" type="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="d-flex justify-content-between">
          <a href="register.php">Create account</a>
          <button class="btn btn-primary">Sign in</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>
