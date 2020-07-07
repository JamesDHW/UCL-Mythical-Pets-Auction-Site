<?php
session_start();
require_once '../../private/init.php';
$fname = $_POST['firstName'];
$lname = $_POST['lastName'];
$email = $_POST['email'];
$password = $_POST['password'];
$addl1 = $_POST['addl1'];
$addl2 = $_POST['addl2'];
$city = $_POST['city'];
$country = $_POST['country'];
$postcode = $_POST['postcode'];

#validate form
if (empty($fname) || empty($lname) || empty($email) || empty($password) || empty($addl1)
    || empty($city) || empty($country) || empty($postcode)) {
  $_SESSION['registrationError'] = true;
  header('Location: ../index.php');
} else {
  #connect to DB & validate connection
  $hashPass = password_hash($password, PASSWORD_DEFAULT);
  require PRIVATE_PATH . '/connect_database.php';
  if(!$connection){
    $_SESSION['registrationError'] = true;
    header('Location: ../index.php');
  } else{
    #begin transaction
    $connection->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

    #check if user already exists
    $findUserQuery = "SELECT * FROM emailAddresses WHERE email = ?";
    $stmt = $connection->prepare($findUserQuery);
    $stmt->bind_param('s',$email);
    $stmt->execute();
    $findUserResult = $stmt->get_result();

    if($findUserResult-> num_rows > 0){
      $_SESSION['userExists'] = true;
    } else{
      #add user details to user table
      $insertUserQuery = "INSERT INTO users (passwordHash, firstName, lastName, admin) VALUES (?, ?, ?, FALSE)";
      $stmt = $connection->prepare($insertUserQuery);
      $stmt->bind_param('sss', $hashPass, $fname, $lname);
      $insertUserResult = $stmt->execute();

      #add email details to email table
      $userID = mysqli_insert_id($connection);
      $insertEmailQuery = "INSERT INTO emailAddresses (userID, email) VALUES (?,?)";
      $stmt = $connection->prepare($insertEmailQuery);
      $stmt->bind_param('is', $userID, $email);
      $insertEmailResult = $stmt->execute();

      #add city details into postcode table if does not already exist
      $insertCityQuery = "INSERT IGNORE INTO postcodes (postcode, country, city) VALUES (?,?,?)";
      $stmt = $connection->prepare($insertCityQuery);
      $stmt->bind_param('sss', $postcode, $country, $city);
      $insertCityResult = $stmt->execute();

      #add address to address table
      $insertAddressQuery = "INSERT INTO addresses (userID, addressLine1, addressLine2, postcode) VALUES (?,?,?,?)";
      $stmt = $connection->prepare($insertAddressQuery);
      if(!isset($addl2)){
        $stmt->bind_param('isss', $userID, $addl1, "", $postcode);
      } else{
        $stmt->bind_param('isss', $userID, $addl1, $addl2, $postcode);
      }
      $insertAddressResult = $stmt->execute();

      #check if all queries ran successfully
      if($insertEmailResult && $insertCityResult && $insertAddressResult){
        $_SESSION['registration'] = true;
      } else {
        $_SESSION['registrationError'] = true;
      }
    }
    #commit transaction
    $connection->commit();
  }
  mysqli_close($connection);
  header('Location: ../index.php');
}
?>
