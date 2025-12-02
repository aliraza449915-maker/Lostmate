<?php
require 'config.php';
if (!is_logged()) { header('Location: login.php'); exit; }
$uid = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';
$post_id = intval($_POST['post_id'] ?? 0);

if (!$post_id && $action !== 'delete_user') { header('Location: user_home.php'); exit; }

switch($action) {
  case 'delete':
    $stmt = $mysqli->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    $stmt->bind_param('ii', $post_id, $uid);
    $stmt->execute();
    break;

  case 'admin_delete':
    if (!is_admin()) { header('Location: user_home.php'); exit; }
    $stmt = $mysqli->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param('i', $post_id);
    $stmt->execute();
    break;

  case 'mark_found':
    $stmt = $mysqli->prepare("UPDATE posts SET status='found' WHERE id=? AND user_id=? AND type='lost'");
    $stmt->bind_param('ii', $post_id, $uid);
    $stmt->execute();
    break;

  case 'mark_returned':
    $stmt = $mysqli->prepare("UPDATE posts SET status='returned' WHERE id=? AND user_id=? AND type='found'");
    $stmt->bind_param('ii', $post_id, $uid);
    $stmt->execute();
    break;

  case 'delete_user':
    if (!is_admin()) break;
    $user_id = intval($_POST['user_id'] ?? 0);
    if ($user_id) {
        $stmt = $mysqli->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
    }
    break;

  default:
    break;
}

header('Location: user_home.php'); exit;
?>