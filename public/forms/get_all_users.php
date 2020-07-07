<?php
  require PRIVATE_PATH . '/connect_database.php';
  $query = "SELECT users.userID, users.firstName, users.lastName, emailAddresses.email, addresses.addressLine1, addresses.postcode, users.admin, users.deleted
  FROM users
  LEFT JOIN emailAddresses ON users.userID = emailAddresses.userID
  LEFT JOIN addresses ON users.userID = addresses.userID";
  $stmt = $connection->prepare($query);
  $stmt->execute();
  $stmt->bind_result($accountID,$accountFirstName,$accountLastName, $accountEmail, $accountAddress1, $accountPostcode, $accountAdmin, $accountRemoved);
  while(!is_null($stmt->fetch())){
    require PRIVATE_PATH . '/templates/user_summary.php';
  }
  $stmt->close();
  mysqli_close($connection);
?>
