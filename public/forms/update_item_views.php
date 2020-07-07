<?php
  require_once '../../private/init.php';
  require PRIVATE_PATH . '/connect_database.php';
  $itemID = $_GET['itemID'];
  $connection->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

  $query = "UPDATE items SET items.views = items.views + 1 WHERE items.itemID = ?";
  $stmt = $connection->prepare($query);
  $stmt->bind_param('s', $itemID);
  $stmt->execute();
  $stmt->close();

  $connection->commit();
  $connection->close();
?>
