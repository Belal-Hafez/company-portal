<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>PROJECT</title>
  <link rel="stylesheet" href="css/css.css">
</head>

<body>
  <div class="navbar">
    <div>
      <a href="index.php">Home</a>
      <a href="export_workers.php">Export Workers</a>
      <a href="edit_workers.php">Edit Workers</a>
      <a href="scheduling_generation.php">Scheduling Generation</a>
      <a href="send_announcement.php">Send Announcement</a>
      <a href="announcement.php">Announcement</a>
      <a href="chat.php">Chat</a>
    </div>
    <?php if (isset($_SESSION['user_id'])) { ?>
      <a href="logout.php" class="logout">Logout</a>
    <?php } else { ?>
      <a href="login.php">Login</a>
    <?php } ?>
  </div>