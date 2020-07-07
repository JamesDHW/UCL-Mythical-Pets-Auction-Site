<?php
  $profileID = $_SESSION['userID'];
  require PRIVATE_PATH . '/connect_database.php';
  $query = "SELECT
    i.userID, i.itemID, i.name, m.title, c.title, i.description,
    i.startingPrice, i.startTime, i.endTime,
    b.bidValue
  FROM items i
  LEFT JOIN (
    SELECT itemID, MAX(bidValue) bidValue FROM bids GROUP BY itemID
  ) b ON i.itemID = b.itemID
  JOIN (SELECT animalClassID, title FROM animalClasses) c ON i.animalClass = c.animalClassID
  JOIN (SELECT mythologyID, title FROM mythologies) m ON i.mythology = m.mythologyID
  WHERE i.userID = ?";
  $stmt = $connection->prepare($query);
  $stmt->bind_param('s', $profileID);
  $stmt->execute();
  $stmt->bind_result($profileID, $itemID, $itemName, $itemMythology, $itemClass, $itemDesc, $itemStartPrice, $itemStartTime, $itemEndTime, $itemBid);
  while(!is_null($stmt->fetch())){
    require PRIVATE_PATH . '/templates/item_summary.php';
  }
  $stmt->close();
  mysqli_close($connection);
?>
