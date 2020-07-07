<?php
  // if(!isset($_SESSION['userID'])){
  //   header("Location: /mythical-pets/public/pages/admin_items_view.php");
  //   exit();
  // };

  require_once '../../private/init.php';
  require PRIVATE_PATH . '/connect_database.php';
  $itemID = $_POST['itemID'];
  $connection->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

  $query1 = "DELETE FROM pictures WHERE pictures.itemID = ?";
  $stmt = $connection->prepare($query1);
  $stmt->bind_param('s', $itemID);
  $stmt->execute();
  $stmt->close();

  $query2 = "DELETE FROM bids WHERE bids.itemID = ?";
  $stmt = $connection->prepare($query2);
  $stmt->bind_param('s', $itemID);
  $stmt->execute();
  $stmt->close();

  $query3 = "DELETE FROM items WHERE items.itemID = ?";
  $stmt = $connection->prepare($query3);
  $stmt->bind_param('s', $itemID);
  $stmt->execute();
  $stmt->close();

  $connection->commit();
  $connection->close();

  # recursive deletion method from https://stackoverflow.com/questions/3349753/delete-directory-with-files-in-it
  $dir = "../images/$itemID";
  $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
  $files = new RecursiveIteratorIterator($it,
      RecursiveIteratorIterator::CHILD_FIRST);
  foreach($files as $file) {
    if ($file->isDir()){
      rmdir($file->getRealPath());
    } else {
      unlink($file->getRealPath());
    }
  }
  rmdir($dir);

  header("Location: /mythical-pets/public/pages/admin_items_view.php");
?>
