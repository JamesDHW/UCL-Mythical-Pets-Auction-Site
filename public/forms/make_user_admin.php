<?php
  //CHECK FOR ADMIN
  // if(!isset($_SESSION['userID']) or $_SESSION['admin'] == '0'){
  //   header("Location: /mythical-pets/public/pages/admin_users_view.php");
  //   exit();
  // }
  require_once '../../private/init.php';
  require PRIVATE_PATH . '/connect_database.php';
  $profileID = $_POST['profileID'];
  $accountAdmin = $_POST['admin'];
  $connection->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

  //Invert the current admin rights
  if($accountAdmin){$accountAdmin=0;}else{$accountAdmin=1;}

  $query = "UPDATE users SET users.admin = ? WHERE users.userID = ?";
  $stmt = $connection->prepare($query);
  $stmt->bind_param('ss', $accountAdmin, $profileID);
  $stmt->execute();
  $stmt->close();

  $connection->commit();
  $connection->close();
  header("Location: /mythical-pets/public/pages/admin_users_view.php");
?>
