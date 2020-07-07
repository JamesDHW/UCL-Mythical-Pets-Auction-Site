<?php
  require_once '../../private/init.php';

  if (isset($_GET['itemSearch'])){
    $searchResult = $_GET['itemSearch'];
    $sortQuery = " "; $mythQuery = " "; $classQuery = " ";
    $searchSort = "0"; $searchMythology = "0"; $searchClass = "0";
    if(isset($_GET['sortBy'])){
      $searchSort = $_GET['sortBy'];
      if($searchSort=="1"){ $sortQuery = " ORDER BY i.endTime"; }
      elseif($searchSort=="2"){ $sortQuery = " ORDER BY i.startTime DESC"; }
      elseif($searchSort=="3"){ $sortQuery = " ORDER BY i.buyNowPrice"; }
      elseif($searchSort=="4"){ $sortQuery = " ORDER BY i.buyNowPrice DESC"; }
    }
    if(isset($_GET['mythology'])){
      $searchMythology = $_GET['mythology'];
      if($searchMythology!="0"){ $mythQuery = " AND i.mythology = ? "; }
    }
    if(isset($_GET['animalClass'])){
      $searchClass = $_GET['animalClass'];
      if($searchClass!="0"){ $classQuery = " AND i.animalClass = ? "; }
    }
    require PRIVATE_PATH . '/connect_database.php';
    $query =
    "SELECT
      i.userID, i.itemID,i.startingPrice,
      i.name, i.description, i.buyNowPrice,
      c.title,
      m.title,
      b.bidValue,
      p.pictureName
    FROM items i
    LEFT JOIN (SELECT itemID, MAX(bidValue) bidValue FROM bids GROUP BY itemID) b ON i.itemID = b.itemID
    JOIN (SELECT animalClassID, title FROM animalClasses) c ON i.animalClass = c.animalClassID
    JOIN (SELECT mythologyID, title FROM mythologies) m ON i.mythology = m.mythologyID
    LEFT JOIN (
      SELECT
        pics.itemID,
        pics.pictureID,
        pictureName
      FROM (SELECT itemID, MIN(pictureID) pictureID FROM pictures GROUP BY itemID) upics
      JOIN pictures pics ON
        upics.itemID = pics.itemID
        AND upics.pictureID = pics.pictureID) p ON p.itemID = i.itemID
    WHERE i.name LIKE ?
    AND i.startTime < CURRENT_TIMESTAMP
    AND CURRENT_TIMESTAMP < i.endTime";
    # Concatonate main body of
    $query = $query . $classQuery . $mythQuery . $sortQuery;

    $stmt = $connection->prepare($query);
    $param = "%$searchResult%";

    if($searchMythology == "0" && $searchClass == "0"){ $stmt->bind_param('s', $param);
    } elseif($searchMythology != "0" && $searchClass == "0"){$stmt->bind_param('ss', $param, $searchMythology);
    } elseif($searchMythology == "0" && $searchClass != "0"){$stmt->bind_param('ss', $param, $searchClass);
    } else{$stmt->bind_param('sss', $param, $searchClass, $searchMythology);}

    $stmt->execute();
    $stmt->bind_result($profileID, $itemID, $itemStart, $itemName, $itemDesc,
    $itemBuyNow, $itemClass, $itemMythology, $bidValue, $picName);

    while ($stmt->fetch()){
      require TEMPLATE_PATH . "/search_result.php";
    }
    $stmt->close();
    mysqli_close($connection);
  }
?>
