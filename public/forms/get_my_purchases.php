<?php
  require PRIVATE_PATH . '/connect_database.php';
  if(!$connection){
    require TEMPLATE_PATH . '/404item.php';
  } else{
    if($_SESSION['admin']==1){
      $query = "SELECT t.*, b2.timestamp as bought_ts, b2.userID FROM
                  (SELECT i.name, m.title as mythology, a.title as animalClass, i.description, i.itemID, MAX(b.bidvalue) AS bought_price
                    FROM  items i
                    INNER JOIN bids b USING(itemID)
                    INNER JOIN mythologies m, animalClasses a
                    WHERE i.endTime < NOW() AND m.mythologyID = i.mythology AND a.animalClassID = i.animalClass
                    GROUP BY i.name, i.mythology, i.animalClass, i.description, i.itemID) t
                  INNER JOIN bids b2 WHERE b2.bidValue = t.bought_price AND b2.itemID = t.itemID
                ORDER BY userID ASC, bought_ts DESC";
    }else{
      $query = "SELECT t.*, b2.timestamp as bought_ts, b2.userID FROM
                  (SELECT i.name, m.title as mythology, a.title as animalClass, i.description, i.itemID, MAX(b.bidvalue) AS bought_price
                    FROM  items i
                    INNER JOIN bids b USING(itemID)
                    INNER JOIN mythologies m, animalClasses a
                    WHERE i.endTime < NOW() AND m.mythologyID = i.mythology AND a.animalClassID = i.animalClass AND b.userID = ?
                    GROUP BY i.name, i.mythology, i.animalClass, i.description, i.itemID) t
                  INNER JOIN bids b2 WHERE b2.bidValue = t.bought_price AND b2.itemID = t.itemID
                ORDER BY userID ASC, bought_ts DESC";
    }
      $stmt = $connection->prepare($query);
      if($_SESSION['admin']!=1){$stmt->bind_param('i', $_SESSION['userID']);}
      $stmt->execute();
      $result = $stmt->get_result();
      if ($result -> num_rows > 0){
        $count = 1;
        while ($row = $result -> fetch_assoc()){
          if ($_SESSION['admin']==1) {
            echo "<tr><td class=\"align-middle\">" . $count . "</td><td class=\"align-middle\">" .
            $row["name"] . "</td><td class=\"align-middle\">" . $row["mythology"] . "</td><td class=\"align-middle\">" .
            $row["animalClass"] . "</td><td class=\"align-middle\">" . $row["description"] .
            "</td><td class=\"align-middle\">£" . $row["bought_price"] . "</td><td class=\"align-middle\">" .
            $row["bought_ts"] . "</td><td class=\"align-middle\">" . $row['userID'] .
            "</td><td class=\"align-middle\"><button class='btn btn-primary' onclick='location.href=\"item.php?itemID=".$row["itemID"]."\";' >View Item Details</button></td></tr>";

          } else{
              echo "<tr><td class=\"align-middle\">" . $count . "</td><td class=\"align-middle\">" .
              $row["name"] . "</td><td class=\"align-middle\">" . $row["mythology"] . "</td><td class=\"align-middle\">" .
              $row["animalClass"] . "</td><td class=\"align-middle\">" . $row["description"] .
              "</td><td class=\"align-middle\">£" . $row["bought_price"] . "</td><td class=\"align-middle\">" . $row["bought_ts"] .
              "</td><td class=\"align-middle\"><button class='btn btn-primary' onclick='location.href=\"item.php?itemID=".$row["itemID"]."\";' >View Item Details</button></td></tr>";
          }
          $count = $count + 1;
        }
      } 
  }
  mysqli_close($connection);
?>
