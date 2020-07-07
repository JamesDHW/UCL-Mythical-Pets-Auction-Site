<?php
  require PRIVATE_PATH . '/connect_database.php';
  if(!$connection){
    require TEMPLATE_PATH . '/404item.php';
  } else{

    $query = "SELECT t.*, (SELECT CASE WHEN b_max.itemID = t.itemID AND
              b_max.bidValue = t.bidValue THEN 1 ELSE 0 END) AS bid_Max
                FROM (SELECT i.name, m.title as mythology, a.title as animalClass, i.description,
                  i.endTime, b.bidValue, b.timeStamp, b.userID, b.bidID, i.itemID
                    FROM items i
                      INNER JOIN bids b USING (itemID)
                      INNER JOIN mythologies m, animalClasses a WHERE m.mythologyID = i.mythology AND a.animalClassID = i.animalClass) t
                        LEFT JOIN (SELECT b1.itemID, MAX(b1.bidValue) as bidValue
                          FROM bids b1
                          GROUP BY b1.itemID) b_max
                          ON b_max.itemID = t.itemID AND b_max.bidValue = t.bidValue
                            ORDER BY t.itemID ASC, t.timeStamp DESC";

    $stmt = $connection->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result -> num_rows > 0){
      $countBid = 1;
      $countItem = 1;
      $currentItem = 1;
      $previousItem = 1;
      while($row = $result -> fetch_assoc()){
        $previousItem = $currentItem;
        $currentItem = $row["itemID"];
        if($currentItem != $previousItem)
        {
          $countItem = $countItem+1;
        }
        if($_SESSION['admin']==1){
          echo "<tr " .($row["bid_Max"]==1 ? "class=\"table-success\"" : "") . "><td class=\"align-middle\">" . $countBid . "</td><td class=\"align-middle\">" .
          $countItem . "</td><td class=\"align-middle\">" . $row["name"] . "</td><td class=\"align-middle\">" .
          $row["mythology"] . "</td><td class=\"align-middle\">" . $row["animalClass"] .
          "</td><td class=\"align-middle\">" . $row["description"] . "</td><td class=\"align-middle\">£" .
          $row["bidValue"] . "</td><td class=\"align-middle\">" . $row["timeStamp"] .
          "</td><td class=\"align-middle\">" . $row["userID"] .
          "</td><td class=\"align-middle\"><button class='btn btn-primary' onclick='location.href=\"item.php?itemID=".$row["itemID"]."\";' >View Item Details</button></td></tr>";
          $countBid = $countBid + 1;
        } elseif($_SESSION['userID'] == $row["userID"]){
          echo "<tr " .($row["bid_Max"]==1 ? "class=\"table-success\"" : "") . "><td class=\"align-middle\">" . $countBid . "</td><td class=\"align-middle\">" .
          $countItem . "</td><td class=\"align-middle\">" . $row["name"] . "</td><td class=\"align-middle\">" .
          $row["mythology"] . "</td><td class=\"align-middle\">" . $row["animalClass"] .
          "</td><td class=\"align-middle\">" . $row["description"] . "</td><td class=\"align-middle\">£" .
          $row["bidValue"] . "</td><td class=\"align-middle\">" . $row["timeStamp"] .
          "</td><td class=\"align-middle\"><button class='btn btn-primary' onclick='location.href=\"item.php?itemID=".$row["itemID"]."\";' >View Item Details</button></td></tr>";
          $countBid = $countBid + 1;
        }
      }
    } 
  }
?>
