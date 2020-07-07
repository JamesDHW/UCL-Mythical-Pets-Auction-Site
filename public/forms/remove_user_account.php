<?php
  //CHECK FOR ADMIN
  // if(!isset($_SESSION['userID']) or $_SESSION['admin'] == '0'){
  //   header("Location: /mythical-pets/public/index.php");
  //   exit();
  // }

  require_once '../../private/init.php';
  require PRIVATE_PATH . '/connect_database.php';
  $profileID = $_POST['profileID'];
  $accountRemoved = $_POST['removed'];
  $connection->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

  // Invert account status
  if($accountRemoved){$accountRemoved=0;}else{$accountRemoved=1;}

  $query = "UPDATE users SET users.deleted = ? WHERE users.userID = ?";
  $stmt = $connection->prepare($query);
  $stmt->bind_param('ss', $accountRemoved, $profileID);
  $stmt->execute();
  $stmt->close();

  $connection->commit();
  $connection->close();
  header("Location: /mythical-pets/public/pages/admin_users_view.php");
?>
