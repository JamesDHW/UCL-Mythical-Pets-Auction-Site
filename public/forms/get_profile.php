<?php
  $profileID = $_GET['profileID'];
  require PRIVATE_PATH . '/connect_database.php';
  if(!$connection){
    require TEMPLATE_PATH . '/404item.php';
  } else{
    $query = "SELECT u.firstName, u.lastName, u.deleted, e.email, a.addressLine1, a.addressLine2, p.postcode, p.country, p.city
              FROM users u
              LEFT JOIN emailAddresses e ON u.userID = e.userID
              LEFT JOIN addresses a ON a.userID = e.userID
              LEFT JOIN postcodes p ON p.postcode = a.postcode
              WHERE u.userID = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('s', $profileID);
    $stmt->execute();
    $stmt->bind_result($fname, $lname, $userDeleted, $userEmail, $userAddL1, $userAddL2, $userPostcode, $userCountry, $userCity);
    $stmt->fetch();
    $stmt->close();
    mysqli_close($connection);
  }
?>
