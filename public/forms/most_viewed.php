<?php
  require PRIVATE_PATH . '/connect_database.php';
  $query = "SELECT
      i.userID, i.itemID, i.startingPrice, i.name,
      i.description, i.buyNowPrice,
      c.title,
      m.title,
      b.bidValue,
      p.pictureName
    FROM items i
    LEFT JOIN ( SELECT itemID, MAX(bidValue) bidValue FROM bids GROUP BY itemID )
    b ON i.itemID = b.itemID
    JOIN (SELECT animalClassID, title FROM animalClasses) c ON i.animalClass = c.animalClassID
    JOIN (SELECT mythologyID, title FROM mythologies) m ON i.mythology = m.mythologyID
    LEFT JOIN (
      SELECT pics.itemID, pics.pictureID, pictureName
      FROM (SELECT itemID, MIN(pictureID) pictureID FROM pictures GROUP BY itemID) upics
      JOIN pictures pics ON upics.itemID = pics.itemID AND upics.pictureID = pics.pictureID )
    p ON p.itemID = i.itemID
    ORDER BY i.views DESC LIMIT 6";

  $stmt = $connection->prepare($query);
  $stmt->execute();
  $stmt->bind_result($profileID, $itemID, $itemStart, $itemName, $itemDesc, $itemBuyNow, $itemClass, $itemMythology, $bidValue, $picName);

  while ($stmt->fetch()){
    require TEMPLATE_PATH . "/search_result.php";

  }
  $stmt->close();
  mysqli_close($connection);
?>
